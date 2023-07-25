<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProcessCheckoutRequest;
use App\Models\Coupon;
use App\Models\Plan;
use App\Models\Setting;
use App\Models\TaxRate;
use App\Traits\PaymentTrait;
use Carbon\Carbon;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutController
{
    use PaymentTrait;

    /**
     * The default plan interval.
     *
     * @var string
     */
    private $defaultInterval = 'month';

    /**
     * Plan intervals.
     *
     * @var string[]
     */
    private $intervals = ['month', 'year'];

    /**
     * Display the checkout form.
     *
     * @param Request $request
     * @param $id
     * @return Factory|RedirectResponse|View
     */
    public function index(Request $request, $id)
    {
        $request->session()->forget(['plan_redirect']);
        
        // If no interval is set
        if (!in_array($request->input('interval'), $this->intervals)) {
            // Redirect to a default interval
            return redirect()->route('checkout.index', ['id' => $id, 'interval' => $this->defaultInterval]);
        }

        $plan = Plan::where('id', '=', $id)->notDefault()->firstOrFail();
        $address = Setting::where('name', '=', 'brc20_address')->get();

        // If the user is already subscribed to the plan
        if ($request->user()->plan->id == $plan->id) {
            return redirect()->route('pricing');
        }

        $coupon = null;

        // If the plan has coupons assigned
        if ($plan->coupons) {
            // If a coupon was set
            if ($request->old('coupon')) {
                $coupon = Coupon::where('code', '=', $request->old('coupon'))->first() ?? null;

                // If the coupon isn't available on this plan
                if ($coupon && !in_array($coupon->id, $plan->coupons)) {
                    $coupon = null;
                }

                // If the coupon quantity is smaller or equal with the number of redeems, and it is not unlimited
                if ($coupon && $coupon->quantity <= $coupon->redeems && $coupon->quantity != -1) {
                    $coupon = null;
                }
            }
        }

        // Get the tax rates
        $taxRates = TaxRate::whereIn('id', $plan->tax_rates ?? [])->ofRegion(old('country') ?? ($request->user()->billing_information->country ?? null))->orderBy('type')->get();

        // Sum the inclusive tax rates
        $inclTaxRatesPercentage = $taxRates->where('type', '=', 0)->sum('percentage');

        // Sum the exclusive tax rates
        $exclTaxRatesPercentage = $taxRates->where('type', '=', 1)->sum('percentage');

        return view('checkout.index', ['plan' => $plan, 'user' => $request->user(), 'taxRates' => $taxRates, 'coupon' => $coupon, 'inclTaxRatesPercentage' => $inclTaxRatesPercentage, 'exclTaxRatesPercentage' => $exclTaxRatesPercentage,'address' => $address]);
    }

    /**
     * Process the payment request.
     *
     * @param ProcessCheckoutRequest $request
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|Factory|RedirectResponse|\Illuminate\Routing\Redirector|View|void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function process(ProcessCheckoutRequest $request, $id)
    {
        $plan = Plan::where('id', '=', $id)->notDefault()->firstOrFail();

        // If the user is already subscribed to the plan
        if ($request->user()->plan->id == $plan->id) {
            return redirect()->route('pricing');
        }

        // If the user wants to skip trial
        if ($request->input('skip_trial')) {
            $request->user()->plan_trial_ends_at = Carbon::now();
            $request->user()->save();
            return redirect()->back()->withInput();
        }

        // If the user's country has changed, or a coupon was applied
        if ($request->has('country') && !$request->has('submit') || $request->has('coupon') && !$request->has('coupon_set')) {
            return redirect()->back()->withInput();
        }

        // Update the user's billing information
        $request->user()->billing_information = [
            'city' => $request->input('city'),
            'country' => $request->input('country'),
            'postal_code' => $request->input('postal_code'),
            'state' => $request->input('state'),
            'address' => $request->input('address'),
            'name' => $request->input('name'),
            'phone' => $request->input('phone')
        ];

        $request->user()->save();

        // Get the coupon
        $coupon = $plan->coupons && $request->input('coupon') ? Coupon::where('code', '=', $request->input('coupon'))->firstOrFail() : null;

        // Get the tax rates
        $taxRates = TaxRate::whereIn('id', $plan->tax_rates ?? [])->ofRegion($request->user()->billing_information->country ?? null)->orderBy('type')->get();

        // Sum the inclusive tax rates
        $inclTaxRatesPercentage = $taxRates->where('type', '=', 0)->sum('percentage');

        // Sum the exclusive tax rates
        $exclTaxRatesPercentage = $taxRates->where('type', '=', 1)->sum('percentage');

        // Get the total amount to be charged
        $amount = formatMoney(checkoutTotal(($request->input('interval') == 'year' ? $plan->amount_year : $plan->amount_month), $coupon->percentage ?? null, $exclTaxRatesPercentage, $inclTaxRatesPercentage), $plan->currency, false, false);

        $taxRates = TaxRate::whereIn('id', $plan->tax_rates ?? [])->ofRegion($request->user()->billing_information->country ?? null)->orderBy('type')->get();

        // If a trial is available
        if ($plan->trial_days > 0 && !$request->user()->plan_trial_ends_at) {
            return $this->tryPlan($request, $plan);
        }
        // If a redeemable coupon was used
        elseif ($coupon && $coupon->type == 1) {
            return $this->redeemPlan($request, $plan, $coupon);
        } elseif ($request->input('payment_processor') == 'paypal') {
            return $this->initPayPal($request, $plan, $coupon, $taxRates, $amount);
        } elseif ($request->input('payment_processor') == 'stripe') {
            return $this->initStripe($request, $plan, $coupon, $taxRates, $amount);
        } elseif ($request->input('payment_processor') == 'razorpay') {
            return $this->initRazorpay($request, $plan, $coupon, $taxRates, $amount);
        } elseif ($request->input('payment_processor') == 'paystack') {
            return $this->initPaystack($request, $plan, $coupon, $taxRates, $amount);
        } elseif ($request->input('payment_processor') == 'coinbase') {
            return $this->initCoinbase($request, $plan, $coupon, $taxRates, $amount);
        } elseif ($request->input('payment_processor') == 'cryptocom') {
            return $this->initCryptocom($request, $plan, $coupon, $taxRates, $amount);
        } elseif ($request->input('payment_processor') == 'bank') {
            return $this->initBank($request, $plan, $coupon, $taxRates, $amount);
        } elseif ($request->input('payment_processor') == 'brc20') {
            return $this->initBrc20($request, $plan, $coupon, $taxRates, $amount);
        }
    }

    /**
     * Redeem a plan.
     *
     * @param Request $request
     * @param Plan $plan
     * @param Coupon $coupon
     * @return RedirectResponse
     */
    private function redeemPlan(Request $request, Plan $plan, Coupon $coupon)
    {
        // Cancel the current plan
        $request->user()->planSubscriptionCancel();

        // Store the new plan
        $request->user()->plan_id = $plan->id;
        $request->user()->plan_amount = null;
        $request->user()->plan_currency = null;
        $request->user()->plan_interval = null;
        $request->user()->plan_payment_processor = null;
        $request->user()->plan_subscription_id = null;
        $request->user()->plan_subscription_status = null;
        $request->user()->plan_recurring_at = null;
        $request->user()->plan_ends_at = $coupon->days < 0 ? null : Carbon::now()->addDays($coupon->days);
        $request->user()->save();

        // Increase the coupon usage
        $coupon->increment('redeems', 1);

        return redirect()->route('checkout.complete');
    }

    /**
     * Try a plan.
     *
     * @param Request $request
     * @param Plan $plan
     * @return RedirectResponse
     */
    private function tryPlan(Request $request, Plan $plan)
    {
        $now = Carbon::now();

        // Cancel the current plan
        $request->user()->planSubscriptionCancel();

        // Store the new plan
        $request->user()->plan_id = $plan->id;
        $request->user()->plan_amount = null;
        $request->user()->plan_currency = null;
        $request->user()->plan_interval = null;
        $request->user()->plan_payment_processor = null;
        $request->user()->plan_subscription_id = null;
        $request->user()->plan_subscription_status = null;
        $request->user()->plan_created_at = $now;
        $request->user()->plan_recurring_at = null;
        $request->user()->plan_trial_ends_at = (clone $now)->addDays($plan->trial_days);
        $request->user()->plan_ends_at = (clone $now)->addDays($plan->trial_days);
        $request->user()->save();

        return redirect()->route('checkout.complete');
    }

    /**
     * Initialize the PayPal payment.
     *
     * @param Request $request
     * @param Plan $plan
     * @param $coupon
     * @param $taxRates
     * @param $amount
     * @return \Illuminate\Contracts\Foundation\Application|RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function initPayPal(Request $request, Plan $plan, $coupon, $taxRates, $amount) {
        $httpClient = new HttpClient();

        $httpBaseUrl = 'https://'.(config('settings.paypal_mode') == 'sandbox' ? 'api-m.sandbox' : 'api-m').'.paypal.com/';

        // Attempt to retrieve the auth token
        try {
            $payPalAuthRequest = $httpClient->request('POST', $httpBaseUrl . 'v1/oauth2/token', [
                    'auth' => [config('settings.paypal_client_id'), config('settings.paypal_secret')],
                    'form_params' => [
                        'grant_type' => 'client_credentials'
                    ]
                ]
            );

            $payPalAuth = json_decode($payPalAuthRequest->getBody()->getContents());
        } catch (BadResponseException $e) {
            return back()->with('error', $e->getResponse()->getBody()->getContents());
        }

        $payPalProduct = 'product_' . $plan->id;

        // Attempt to retrieve the product
        try {
            $payPalProductRequest = $httpClient->request('GET', $httpBaseUrl . 'v1/catalogs/products/' . $payPalProduct, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $payPalAuth->access_token,
                        'Content-Type' => 'application/json'
                    ]
                ]
            );

            $payPalProduct = json_decode($payPalProductRequest->getBody()->getContents());
        } catch (\Exception $e) {
            // Attempt to create the product
            try {
                $payPalProductRequest = $httpClient->request('POST', $httpBaseUrl . 'v1/catalogs/products', [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $payPalAuth->access_token,
                            'Content-Type' => 'application/json'
                        ],
                        'body' => json_encode([
                            'id' => $payPalProduct,
                            'name' => $plan->name,
                            'description' => $plan->description,
                            'type' => 'SERVICE'
                        ])
                    ]
                );

                $payPalProduct = json_decode($payPalProductRequest->getBody()->getContents());
            } catch (BadResponseException $e) {
                return back()->with('error', $e->getResponse()->getBody()->getContents());
            }
        }

        $payPalAmount = $amount;

        $payPalPlan = 'plan_' . $plan->id . '_' .$request->input('interval') . '_' . $payPalAmount . '_' . $plan->currency;

        // Attempt to create the plan
        try {
            $payPalPlanRequest = $httpClient->request('POST', $httpBaseUrl . 'v1/billing/plans', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $payPalAuth->access_token,
                        'Content-Type' => 'application/json'
                    ],
                    'body' => json_encode([
                        'product_id' => $payPalProduct->id,
                        'name' => $payPalPlan,
                        'status' => 'ACTIVE',
                        'billing_cycles' => [
                            [
                                'frequency' => [
                                    'interval_unit' => strtoupper($request->input('interval')),
                                    'interval_count' => 1,
                                ],
                                'tenure_type' => 'REGULAR',
                                'sequence' => 1,
                                'total_cycles' => 0,
                                'pricing_scheme' => [
                                    'fixed_price' => [
                                        'value' => $payPalAmount,
                                        'currency_code' => $plan->currency,
                                    ],
                                ]
                            ]
                        ],
                        'payment_preferences' => [
                            'auto_bill_outstanding' => true,
                            'payment_failure_threshold' => 0,
                        ],
                    ])
                ]
            );

            $payPalPlan = json_decode($payPalPlanRequest->getBody()->getContents());
        } catch (BadResponseException $e) {
            return back()->with('error', $e->getResponse()->getBody()->getContents());
        }

        // Attempt to create the subscription
        try {
            $payPalSubscriptionRequest = $httpClient->request('POST', $httpBaseUrl . 'v1/billing/subscriptions', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $payPalAuth->access_token,
                        'Content-Type' => 'application/json'
                    ],
                    'body' => json_encode([
                        'plan_id' => $payPalPlan->id,
                        'application_context' => [
                            'brand_name' => config('settings.title'),
                            'locale' => 'en-US',
                            'shipping_preference' => 'SET_PROVIDED_ADDRESS',
                            'user_action' => 'SUBSCRIBE_NOW',
                            'payment_method' => [
                                'payer_selected' => 'PAYPAL',
                                'payee_preferred' => 'IMMEDIATE_PAYMENT_REQUIRED',
                            ],
                            'return_url' => route('checkout.complete'),
                            'cancel_url' => route('checkout.cancelled')
                        ],
                        'custom_id' => http_build_query([
                            'user' => $request->user()->id,
                            'plan' => $plan->id,
                            'plan_amount' => $request->input('interval') == 'year' ? $plan->amount_year : $plan->amount_month,
                            'amount' => $amount,
                            'currency' => $plan->currency,
                            'interval' => $request->input('interval'),
                            'coupon' => $coupon->id ?? null,
                            'tax_rates' => $taxRates->pluck('id')->implode('_'),
                        ])
                    ])
                ]
            );

            $payPalSubscription = json_decode($payPalSubscriptionRequest->getBody()->getContents());
        } catch (BadResponseException $e) {
            return back()->with('error', $e->getResponse()->getBody()->getContents());
        }

        return redirect($payPalSubscription->links[0]->href);
    }

    /**
     * Initialize the Stripe payment.
     *
     * @param Request $request
     * @param Plan $plan
     * @param $coupon
     * @param $taxRates
     * @param $amount
     * @return \Illuminate\Contracts\Foundation\Application|Factory|RedirectResponse|View
     */
    private function initStripe(Request $request, Plan $plan, $coupon, $taxRates, $amount)
    {
        $stripe = new \Stripe\StripeClient(
            config('settings.stripe_secret')
        );

        // Attempt to retrieve the product
        try {
            $stripeProduct = $stripe->products->retrieve($plan->id);

            // Check if the plan's name has changed
            if ($plan->name != $stripeProduct->name) {

                // Attempt to update the product
                try {
                    $stripeProduct = $stripe->products->update($stripeProduct->id, [
                        'name' => $plan->name
                    ]);
                } catch (\Exception $e) {
                    return back()->with('error', $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            // Attempt to create the product
            try {
                $stripeProduct = $stripe->products->create([
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'description' => $plan->description
                ]);
            } catch (\Exception $e) {
                return back()->with('error', $e->getMessage());
            }
        }

        $stripeAmount = in_array($plan->currency, config('currencies.zero_decimals')) ? $amount : ($amount * 100);

        $stripePlan = $plan->id . '_' .$request->input('interval') . '_' . $stripeAmount . '_' . $plan->currency;

        // Attempt to retrieve the plan
        try {
            $stripePlan = $stripe->plans->retrieve($stripePlan);
        } catch (\Exception $e) {
            // Attempt to create the plan
            try {
                $stripePlan = $stripe->plans->create([
                    'amount' => $stripeAmount,
                    'currency' => $plan->currency,
                    'interval' => $request->input('interval'),
                    'product' => $stripeProduct->id,
                    'id' => $stripePlan,
                ]);
            } catch (\Exception $e) {
                return back()->with('error', $e->getMessage());
            }
        }

        // Attempt to create the checkout session
        try {
            $stripeSession = $stripe->checkout->sessions->create([
                'success_url' => route('checkout.complete'),
                'cancel_url' => route('checkout.cancelled'),
                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        'price' => $stripePlan->id,
                        'quantity' => 1,
                    ],
                ],
                'mode' => 'subscription',
                'subscription_data' => [
                    'metadata' => [
                        'user' => $request->user()->id,
                        'plan' => $plan->id,
                        'plan_amount' => $request->input('interval') == 'year' ? $plan->amount_year : $plan->amount_month,
                        'amount' => $amount,
                        'currency' => $plan->currency,
                        'interval' => $request->input('interval'),
                        'coupon' => $coupon->id ?? null,
                        'tax_rates' => $taxRates->pluck('id')->implode('_')
                    ],
                ]
            ]);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return view('checkout.processors.stripe', ['stripeSession' => $stripeSession]);
    }

    /**
     * Initialize the Razorpay payment.
     *
     * @param Request $request
     * @param Plan $plan
     * @param $coupon
     * @param $taxRates
     * @param $amount
     * @return \Illuminate\Contracts\Foundation\Application|Factory|RedirectResponse|View
     */
    private function initRazorpay(Request $request, Plan $plan, $coupon, $taxRates, $amount)
    {
        $razorpay = new \Razorpay\Api\Api(config('settings.razorpay_key'), config('settings.razorpay_secret'));

        $razorpayAmount = in_array($plan->currency, config('currencies.zero_decimals')) ? $amount : ($amount * 100);

        $razorpayPlan = $plan->id . '_' .$request->input('interval') . '_' . $razorpayAmount . '_' . $plan->currency;

        try {
            $razorpayPlanRequest = $razorpay->plan->create([
                'period' => $request->input('interval') == 'month' ? 'monthly' : 'yearly',
                'interval' => 1,
                'item' => [
                    'name' => $razorpayPlan,
                    'description' => $plan->description,
                    'amount' => $razorpayAmount,
                    'currency' => $plan->currency,
                ],
            ]);
        } catch (\Exception $e) {
            return back()->with('error', $e->getCode() . ' - ' . $e->getMessage());
        }

        try {
            $razorpaySubscriptionRequest = $razorpay->subscription->create([
                'plan_id' => $razorpayPlanRequest->id,
                'total_count' => $request->input('interval') == 'month' ? 36 : 3,
                'notes' => [
                    'user' => $request->user()->id,
                    'plan' => $plan->id,
                    'plan_amount' => $request->input('interval') == 'year' ? $plan->amount_year : $plan->amount_month,
                    'amount' => $amount,
                    'currency' => $plan->currency,
                    'interval' => $request->input('interval'),
                    'coupon' => $coupon->id ?? null,
                    'tax_rates' => $taxRates->pluck('id')->implode('_')
                ]
            ]);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect($razorpaySubscriptionRequest->short_url);
    }

    /**
     * Initialize the Paystack payment.
     *
     * @param Request $request
     * @param Plan $plan
     * @param $coupon
     * @param $taxRates
     * @param $amount
     * @return \Illuminate\Contracts\Foundation\Application|Factory|RedirectResponse|View
     */
    private function initPaystack(Request $request, Plan $plan, $coupon, $taxRates, $amount)
    {
        $httpClient = new HttpClient();

        $paystackAmount = in_array($plan->currency, config('currencies.zero_decimals')) ? $amount : ($amount * 100);

        // Attempt to create the plan
        try {
            $paystackPlanRequest = $httpClient->request('POST', 'https://api.paystack.co/plan', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . config('settings.paystack_secret'),
                        'Content-Type' => 'application/json',
                        'Cache-Control' => 'no-cache'
                    ],
                    'body' => json_encode([
                        'name' => $plan->name,
                        'interval' => 'monthly',
                        'amount' => $paystackAmount,
                        'currency' => $plan->currency,
                        'description' => http_build_query([
                            'user' => $request->user()->id,
                            'plan' => $plan->id,
                            'plan_amount' => $request->input('interval') == 'year' ? $plan->amount_year : $plan->amount_month,
                            'amount' => $amount,
                            'currency' => $plan->currency,
                            'interval' => $request->input('interval'),
                            'coupon' => $coupon->id ?? null,
                            'tax_rates' => $taxRates->pluck('id')->implode('_'),
                        ])
                    ])
                ]
            );

            $paystackPlan = json_decode($paystackPlanRequest->getBody()->getContents());
        } catch (\Exception $e) {
            return back()->with('error',  $e->getMessage());
        }

        // Attempt to create the subscription
        try {
            $paystackSubscriptionRequest = $httpClient->request('POST', 'https://api.paystack.co/transaction/initialize', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . config('settings.paystack_secret'),
                        'Content-Type' => 'application/json',
                        'Cache-Control' => 'no-cache'
                    ],
                    'body' => json_encode([
                        'email' => $request->user()->email,
                        'amount' => $paystackAmount,
                        'currency' => $plan->currency,
                        'callback_url' => route('checkout.complete'),
                        'plan' => $paystackPlan->data->plan_code
                    ])
                ]
            );

            $paystackSubscription = json_decode($paystackSubscriptionRequest->getBody()->getContents());
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect($paystackSubscription->data->authorization_url);
    }

    /**
     * Initialize the Coinbase payment.
     *
     * @param Request $request
     * @param Plan $plan
     * @param $coupon
     * @param $taxRates
     * @param $amount
     * @return \Illuminate\Contracts\Foundation\Application|RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function initCoinbase(Request $request, Plan $plan, $coupon, $taxRates, $amount) {
        $httpClient = new HttpClient();

        // Attempt to retrieve the auth token
        try {
            $coinbaseCheckoutRequest = $httpClient->request('POST', 'https://api.commerce.coinbase.com/charges', [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'X-CC-Api-Key' => config('settings.coinbase_key'),
                        'X-CC-Version' => '2018-03-22',
                    ],
                    'body' => json_encode(array_merge_recursive([
                        'name' => $plan->name,
                        'description' => $plan->description,
                        'local_price' => [
                            'amount' => $amount,
                            'currency' => $plan->currency,
                        ],
                        'pricing_type' => 'fixed_price',
                        'metadata' => [
                            'user' => $request->user()->id,
                            'plan' => $plan->id,
                            'plan_amount' => $request->input('interval') == 'year' ? $plan->amount_year : $plan->amount_month,
                            'amount' => $amount,
                            'currency' => $plan->currency,
                            'interval' => $request->input('interval'),
                            'coupon' => $coupon->id ?? null,
                            'tax_rates' => $taxRates->pluck('id')->implode('_')
                        ],
                        'redirect_url' => route('checkout.complete'),
                        'cancel_url' => route('checkout.cancelled'),
                    ]))
                ]
            );

            $coinbase = json_decode($coinbaseCheckoutRequest->getBody()->getContents());
        } catch (BadResponseException $e) {
            return back()->with('error', $e->getResponse()->getBody()->getContents());
        }

        return redirect($coinbase->data->hosted_url);
    }

    /**
     * Initialize the Crypto.com payment.
     *
     * @param Request $request
     * @param Plan $plan
     * @param $coupon
     * @param $taxRates
     * @param $amount
     * @return \Illuminate\Contracts\Foundation\Application|RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function initCryptocom(Request $request, Plan $plan, $coupon, $taxRates, $amount) {
        $httpClient = new HttpClient();

        $cryptocomAmount = in_array($plan->currency, config('currencies.zero_decimals')) ? $amount : ($amount * 100);

        // Attempt to retrieve the auth token
        try {
            $cryptocomCheckoutRequest = $httpClient->request('POST', 'https://pay.crypto.com/api/payments', [
                    'auth' => [config('settings.cryptocom_secret'), ''],
                    'form_params' => [
                        'description' => $plan->description,
                        'amount' => $cryptocomAmount,
                        'currency' => $plan->currency,
                        'metadata' => [
                            'user' => $request->user()->id,
                            'plan' => $plan->id,
                            'plan_amount' => $request->input('interval') == 'year' ? $plan->amount_year : $plan->amount_month,
                            'amount' => $amount,
                            'currency' => $plan->currency,
                            'interval' => $request->input('interval'),
                            'coupon' => $coupon->id ?? null,
                            'tax_rates' => $taxRates->pluck('id')->implode('_')
                        ],
                        'return_url' => route('checkout.complete'),
                        'cancel_url' => route('checkout.cancelled')
                    ]
                ]
            );

            $cryptocom = json_decode($cryptocomCheckoutRequest->getBody()->getContents());
        } catch (BadResponseException $e) {
            return back()->with('error', $e->getResponse()->getBody()->getContents());
        }

        return redirect($cryptocom->payment_url);
    }
    private function initBrc20(Request $request, Plan $plan, $coupon, $taxRates, $amount) {
        $address = Setting::where('name', '=', 'brc20_address')->get();
        if(!$address)
            return back()->with('error', "The main wallet address is not set in the admin page.");
        else{
            $payval = $request->input('interval') == 'year' ? $plan->amount_year : $plan->amount_month;
            $request->validate([
                'brc20' => 'required|numeric|min:10'
            ]);
            return;
        }
    }
    
    /**
     * Initialize the Bank payment.
     *
     * @param Request $request
     * @param Plan $plan
     * @param $taxRates
     * @param $amount
     * @return \Illuminate\Contracts\Foundation\Application|RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function initBank(Request $request, Plan $plan, $coupon, $taxRates, $amount) {
        $this->paymentStore([
            'user_id' => $request->user()->id,
            'plan_id' => $plan->id,
            'payment_id' => $request->payment_id,
            'processor' => 'bank',
            'amount' => $amount,
            'currency' => $plan->currency,
            'interval' => $request->input('interval'),
            'status' => 'pending',
            'coupon' => $coupon->id ?? null,
            'tax_rates' => $taxRates->pluck('id')->implode('_'),
            'customer' => $request->user()->billing_information,
        ]);

        return redirect()->route('checkout.pending');
    }

    /**
     * Display the Payment complete page.
     *
     * @return Factory|View
     */
    public function complete()
    {
        return view('checkout.complete');
    }

    public function completebrc20()
    {
        return view('checkout.completebrc20');
    }

    public function faildbrc20()
    {
        return view('checkout.faildbrc20');
    }

    /**
     * Display the Payment pending page.
     *
     * @return Factory|View
     */
    public function pending()
    {
        return view('checkout.pending');
    }

    /**
     * Display the Payment cancelled page.
     *
     * @return Factory|View
     */
    public function cancelled()
    {
        return view('checkout.cancelled');
    }
}
