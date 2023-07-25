<?php

namespace App\Http\Controllers;

use App\Mail\PaymentMail;
use App\Models\Coupon;
use App\Models\Payment;
use App\Traits\PaymentTrait;
use App\Models\User;
use App\Models\Setting;
use Carbon\Carbon;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Razorpay\Api\Plan;

class WebhookController extends Controller
{
    use PaymentTrait;

    /**
     * Handle the PayPal webhook.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function paypal(Request $request)
    {
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
            Log::info($e->getResponse()->getBody()->getContents());

            return response()->json([
                'status' => 400
            ], 400);
        }

        // Get the payload's content
        $payload = json_decode($request->getContent());

        // Attempt to validate the webhook signature
        try {
            $payPalWHSignatureRequest = $httpClient->request('POST', $httpBaseUrl . 'v1/notifications/verify-webhook-signature', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $payPalAuth->access_token,
                        'Content-Type' => 'application/json'
                    ],
                    'body' => json_encode([
                        'auth_algo' => $request->header('PAYPAL-AUTH-ALGO'),
                        'cert_url' => $request->header('PAYPAL-CERT-URL'),
                        'transmission_id' => $request->header('PAYPAL-TRANSMISSION-ID'),
                        'transmission_sig' => $request->header('PAYPAL-TRANSMISSION-SIG'),
                        'transmission_time' => $request->header('PAYPAL-TRANSMISSION-TIME'),
                        'webhook_id' => config('settings.paypal_webhook_id'),
                        'webhook_event' => $payload
                    ])
                ]
            );

            $payPalWHSignature = json_decode($payPalWHSignatureRequest->getBody()->getContents());
        } catch (BadResponseException $e) {
            Log::info($e->getResponse()->getBody()->getContents());

            return response()->json([
                'status' => 400
            ], 400);
        }

        // Check if the webhook's signature status is successful
        if ($payPalWHSignature->verification_status != 'SUCCESS') {
            Log::info('PayPal signature validation failed.');

            return response()->json([
                'status' => 400
            ], 400);
        }

        // Parse the custom metadata parameters
        parse_str($payload->resource->custom_id ?? ($payload->resource->custom ?? null), $metadata);

        if ($metadata) {
            $user = User::where('id', '=', $metadata['user'])->first();

            // If a user was found
            if ($user) {
                if ($payload->event_type == 'BILLING.SUBSCRIPTION.CREATED') {
                    // If the user previously had a subscription, attempt to cancel it
                    if ($user->plan_subscription_id) {
                        $user->planSubscriptionCancel();
                    }

                    $user->plan_id = $metadata['plan'];
                    $user->plan_amount = $metadata['amount'];
                    $user->plan_currency = $metadata['currency'];
                    $user->plan_interval = $metadata['interval'];
                    $user->plan_payment_processor = 'paypal';
                    $user->plan_subscription_id = $payload->resource->id;
                    $user->plan_subscription_status = $payload->resource->status;
                    $user->plan_created_at = Carbon::now();
                    $user->plan_recurring_at = null;
                    $user->plan_ends_at = null;
                    $user->save();

                    // If a coupon was used
                    if (isset($metadata['coupon']) && $metadata['coupon']) {
                        $coupon = Coupon::find($metadata['coupon']);

                        // If a coupon was found
                        if ($coupon) {
                            // Increase the coupon usage
                            $coupon->increment('redeems', 1);
                        }
                    }
                } elseif (stripos($payload->event_type, 'BILLING.SUBSCRIPTION.') !== false) {
                    // If the subscription exists
                    if ($user->plan_payment_processor == 'paypal' && $user->plan_subscription_id == $payload->resource->id) {
                        // Update the recurring date
                        if (isset($payload->resource->billing_info->next_billing_time)) {
                            $user->plan_recurring_at = Carbon::create($payload->resource->billing_info->next_billing_time);
                        }

                        // Update the subscription status
                        if (isset($payload->resource->status)) {
                            $user->plan_subscription_status = $payload->resource->status;
                        }

                        // If the subscription has been cancelled
                        if ($payload->event_type == 'BILLING.SUBSCRIPTION.CANCELLED') {
                            // Update the subscription end date and recurring date
                            if (!empty($user->plan_recurring_at)) {
                                $user->plan_ends_at = $user->plan_recurring_at;
                                $user->plan_recurring_at = null;
                            }
                        }

                        $user->save();
                    }
                } elseif ($payload->event_type == 'PAYMENT.SALE.COMPLETED') {
                    // If the payment does not exist
                    if (!Payment::where([['processor', '=', 'paypal'], ['payment_id', '=', $payload->resource->id]])->exists()) {
                        $payment = $this->paymentStore([
                            'user_id' => $user->id,
                            'plan_id' => $metadata['plan'],
                            'payment_id' => $payload->resource->id,
                            'processor' => 'paypal',
                            'amount' => $metadata['amount'],
                            'currency' => $metadata['currency'],
                            'interval' => $metadata['interval'],
                            'status' => 'completed',
                            'coupon' => $metadata['coupon'] ?? null,
                            'tax_rates' => $metadata['tax_rates'] ?? null,
                            'customer' => $user->billing_information,
                        ]);

                        // Attempt to send the payment confirmation email
                        try {
                            Mail::to($user->email)->locale($user->locale)->send(new PaymentMail($payment));
                        }
                        catch (\Exception $e) {}
                    }
                }
            }
        }

        return response()->json([
            'status' => 200
        ], 200);
    }

    /**
     * Handle the Stripe webhook.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function stripe(Request $request)
    {
        // Attempt to validate the Webhook
        try {
            $stripeEvent = \Stripe\Webhook::constructEvent($request->getContent(), $request->server('HTTP_STRIPE_SIGNATURE'), config('settings.stripe_wh_secret'));
        } catch(\UnexpectedValueException $e) {
            // Invalid payload
            Log::info($e->getMessage());

            return response()->json([
                'status' => 400
            ], 400);
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            Log::info($e->getMessage());

            return response()->json([
                'status' => 400
            ], 400);
        }

        // Get the metadata
        $metadata = $stripeEvent->data->object->lines->data[0]->metadata ?? ($stripeEvent->data->object->metadata ?? null);

        if (isset($metadata->user)) {
            if ($stripeEvent->type != 'customer.subscription.created' && stripos($stripeEvent->type, 'customer.subscription.') !== false) {
                // Provide enough time for the subscription created event to be handled
                sleep(3);
            }

            $user = User::where('id', '=', $metadata->user)->first();

            // If a user was found
            if ($user) {
                if ($stripeEvent->type == 'customer.subscription.created') {
                    // If the user previously had a subscription, attempt to cancel it
                    if ($user->plan_subscription_id) {
                        $user->planSubscriptionCancel();
                    }

                    $user->plan_id = $metadata->plan;
                    $user->plan_amount = $metadata->amount;
                    $user->plan_currency = $metadata->currency;
                    $user->plan_interval = $metadata->interval;
                    $user->plan_payment_processor = 'stripe';
                    $user->plan_subscription_id = $stripeEvent->data->object->id;
                    $user->plan_subscription_status = $stripeEvent->data->object->status;
                    $user->plan_created_at = Carbon::now();
                    $user->plan_recurring_at = $stripeEvent->data->object->current_period_end ? Carbon::createFromTimestamp($stripeEvent->data->object->current_period_end) : null;
                    $user->plan_ends_at = null;
                    $user->save();

                    // If a coupon was used
                    if (isset($metadata->coupon) && $metadata->coupon) {
                        $coupon = Coupon::find($metadata->coupon);

                        // If a coupon was found
                        if ($coupon) {
                            // Increase the coupon usage
                            $coupon->increment('redeems', 1);
                        }
                    }
                } elseif (stripos($stripeEvent->type, 'customer.subscription.') !== false) {
                    // If the subscription exists
                    if ($user->plan_payment_processor == 'stripe' && $user->plan_subscription_id == $stripeEvent->data->object->id) {
                        // Update the recurring date
                        if ($stripeEvent->data->object->current_period_end) {
                            $user->plan_recurring_at = Carbon::createFromTimestamp($stripeEvent->data->object->current_period_end);
                        }

                        // Update the subscription status
                        if ($stripeEvent->data->object->status) {
                            $user->plan_subscription_status = $stripeEvent->data->object->status;
                        }

                        // Update the subscription end date
                        if ($stripeEvent->data->object->cancel_at_period_end) {
                            $user->plan_ends_at = Carbon::createFromTimestamp($stripeEvent->data->object->current_period_end);
                        } elseif ($stripeEvent->data->object->cancel_at) {
                            $user->plan_ends_at = Carbon::createFromTimestamp($stripeEvent->data->object->cancel_at);
                        } elseif ($stripeEvent->data->object->canceled_at) {
                            $user->plan_ends_at = Carbon::createFromTimestamp($stripeEvent->data->object->canceled_at);
                        } else {
                            $user->plan_ends_at = null;
                        }

                        // Reset the subscription recurring date
                        if (!empty($user->plan_ends_at)) {
                            $user->plan_recurring_at = null;
                        }

                        $user->save();
                    }
                } elseif ($stripeEvent->type == 'invoice.paid') {
                    // Make sure the invoice contains the payment id
                    if ($stripeEvent->data->object->charge) {
                        // If the payment does not exist
                        if (!Payment::where([['processor', '=', 'stripe'], ['payment_id', '=', $stripeEvent->data->object->charge]])->exists()) {
                            $payment = $this->paymentStore([
                                'user_id' => $user->id,
                                'plan_id' => $metadata->plan,
                                'payment_id' => $stripeEvent->data->object->charge,
                                'processor' => 'stripe',
                                'amount' => $metadata->amount,
                                'currency' => $metadata->currency,
                                'interval' => $metadata->interval,
                                'status' => 'completed',
                                'coupon' => $metadata->coupon ?? null,
                                'tax_rates' => $metadata->tax_rates ?? null,
                                'customer' => $user->billing_information,
                            ]);

                            // Attempt to send the payment confirmation email
                            try {
                                Mail::to($user->email)->locale($user->locale)->send(new PaymentMail($payment));
                            }
                            catch (\Exception $e) {}
                        }
                    } else {
                        return response()->json([
                            'status' => 400
                        ], 400);
                    }
                }
            }
        }

        return response()->json([
            'status' => 200
        ], 200);
    }

    /**
     * Handle the Razorpay webhook.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function razorpay(Request $request)
    {
        $payload = json_decode($request->getContent());

        $signature = $request->header('x-razorpay-signature');

        $computedSignature = hash_hmac('sha256', $request->getContent(), config('settings.razorpay_wh_secret'));

        // Validate the webhook signature
        if (hash_equals($computedSignature, $signature)) {
            // Get the metadata
            $metadata = $payload->payload->subscription->entity->notes ?? null;

            if (isset($metadata->user)) {
                $user = User::where('id', '=', $metadata->user)->first();

                // If a user was found
                if ($user) {
                    if ($payload->event == 'subscription.authenticated') {
                        // If the user previously had a subscription, attempt to cancel it
                        if ($user->plan_subscription_id) {
                            $user->planSubscriptionCancel();
                        }

                        $user->plan_id = $metadata->plan;
                        $user->plan_amount = $metadata->amount;
                        $user->plan_currency = $metadata->currency;
                        $user->plan_interval = $metadata->interval;
                        $user->plan_payment_processor = 'razorpay';
                        $user->plan_subscription_id = $payload->payload->subscription->entity->id;
                        $user->plan_subscription_status = $payload->payload->subscription->entity->status;
                        $user->plan_created_at = Carbon::now();
                        $user->plan_recurring_at = $payload->payload->subscription->entity->charge_at ? Carbon::createFromTimestamp($payload->payload->subscription->entity->charge_at) : null;
                        $user->plan_ends_at = null;
                        $user->save();

                        // If a coupon was used
                        if (isset($metadata->coupon) && $metadata->coupon) {
                            $coupon = Coupon::find($metadata->coupon);

                            // If a coupon was found
                            if ($coupon) {
                                // Increase the coupon usage
                                $coupon->increment('redeems', 1);
                            }
                        }
                    } elseif (stripos($payload->event, 'subscription.') !== false) {
                        // If the subscription exists
                        if ($user->plan_payment_processor == 'razorpay' && $user->plan_subscription_id == $payload->payload->subscription->entity->id) {
                            // Update the recurring date
                            if ($payload->payload->subscription->entity->charge_at) {
                                $user->plan_recurring_at = Carbon::createFromTimestamp($payload->payload->subscription->entity->charge_at);
                            }

                            // Update the subscription status
                            if ($payload->payload->subscription->entity->status) {
                                $user->plan_subscription_status = $payload->payload->subscription->entity->status;
                            }

                            // Update the subscription end date
                            if ($payload->payload->subscription->entity->ended_at) {
                                // Update the subscription end date and recurring date
                                if (!empty($user->plan_recurring_at)) {
                                    $user->plan_ends_at = $user->plan_recurring_at;
                                    $user->plan_recurring_at = null;
                                }
                            } else {
                                $user->plan_ends_at = null;
                            }

                            $user->save();
                        }
                    }

                    if ($payload->event == 'subscription.charged') {
                        // If the payment does not exist
                        if (!Payment::where([['processor', '=', 'razorpay'], ['payment_id', '=', $payload->payload->payment->entity->id]])->exists()) {
                            $payment = $this->paymentStore([
                                'user_id' => $user->id,
                                'plan_id' => $metadata->plan,
                                'payment_id' => $payload->payload->payment->entity->id,
                                'processor' => 'razorpay',
                                'amount' => $metadata->amount,
                                'currency' => $metadata->currency,
                                'interval' => $metadata->interval,
                                'status' => 'completed',
                                'coupon' => $metadata->coupon ?? null,
                                'tax_rates' => $metadata->tax_rates ?? null,
                                'customer' => $user->billing_information,
                            ]);

                            // Attempt to send the payment confirmation email
                            try {
                                Mail::to($user->email)->locale($user->locale)->send(new PaymentMail($payment));
                            }
                            catch (\Exception $e) {}
                        }
                    }
                }
            }
        } else {
            Log::info('Razorpay signature validation failed.');

            return response()->json([
                'status' => 400
            ], 400);
        }

        return response()->json([
            'status' => 200
        ], 200);
    }

    /**
     * Handle the Paystack webhook.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function paystack(Request $request)
    {
        $payload = json_decode($request->getContent());

        $signature = $request->header('x-paystack-signature');

        $computedSignature = hash_hmac('sha512', $request->getContent(), config('settings.paystack_secret'));

        // Validate the webhook signature
        if (hash_equals($computedSignature, $signature)) {
            // Get the metadata
            // Parse the custom metadata parameters
            parse_str($payload->data->plan->description ?? null, $metadata);

            if (isset($metadata['user'])) {
                $user = User::where('id', '=', $metadata['user'])->first();

                // If a user was found
                if ($user) {
                    if ($payload->event == 'subscription.create') {
                        // If the user previously had a subscription, attempt to cancel it
                        if ($user->plan_subscription_id) {
                            $user->planSubscriptionCancel();
                        }

                        $user->plan_id = $metadata['plan'];
                        $user->plan_amount = $metadata['amount'];
                        $user->plan_currency = $metadata['currency'];
                        $user->plan_interval = $metadata['interval'];
                        $user->plan_payment_processor = 'paystack';
                        $user->plan_subscription_id = $payload->data->subscription_code;
                        $user->plan_subscription_status = $payload->data->status;
                        $user->plan_created_at = Carbon::now();
                        $user->plan_recurring_at = $payload->data->next_payment_date ? Carbon::createFromTimeString($payload->data->next_payment_date) : null;
                        $user->plan_ends_at = null;
                        $user->save();

                        // If a coupon was used
                        if (isset($metadata['coupon']) && $metadata['coupon']) {
                            $coupon = Coupon::find($metadata['coupon']);

                            // If a coupon was found
                            if ($coupon) {
                                // Increase the coupon usage
                                $coupon->increment('redeems', 1);
                            }
                        }
                    } elseif (stripos($payload->event, 'subscription.') !== false) {
                        // If the subscription exists
                        if ($user->plan_payment_processor == 'paystack' && $user->plan_subscription_id == $payload->data->subscription_code) {
                            // Update the recurring date
                            if ($payload->data->next_payment_date) {
                                $user->plan_recurring_at = Carbon::createFromTimeString($payload->data->next_payment_date);
                                $user->plan_ends_at = null;
                            } else {
                                // Update the subscription end date and recurring date
                                if (!empty($user->plan_recurring_at)) {
                                    $user->plan_ends_at = $user->plan_recurring_at;
                                    $user->plan_recurring_at = null;
                                }
                            }

                            // Update the subscription status
                            if ($payload->data->status) {
                                $user->plan_subscription_status = $payload->data->status;
                            }

                            $user->save();
                        }
                    }

                    if ($payload->event == 'charge.success') {
                        // If the payment does not exist
                        if (!Payment::where([['processor', '=', 'paystack'], ['payment_id', '=', $payload->data->reference]])->exists()) {
                            $payment = $this->paymentStore([
                                'user_id' => $user->id,
                                'plan_id' => $metadata['plan'],
                                'payment_id' => $payload->data->reference,
                                'processor' => 'paystack',
                                'amount' => $metadata['amount'],
                                'currency' => $metadata['currency'],
                                'interval' => $metadata['interval'],
                                'status' => 'completed',
                                'coupon' => $metadata['coupon'] ?? null,
                                'tax_rates' => $metadata['tax_rates'] ?? null,
                                'customer' => $user->billing_information,
                            ]);

                            // Attempt to send the payment confirmation email
                            try {
                                Mail::to($user->email)->locale($user->locale)->send(new PaymentMail($payment));
                            }
                            catch (\Exception $e) {}
                        }
                    }
                }
            }
        } else {
            Log::info('Paystack signature validation failed.');

            return response()->json([
                'status' => 400
            ], 400);
        }

        return response()->json([
            'status' => 200
        ], 200);
    }

    /**
     * Handle the Coinbase webhook.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function coinbase(Request $request)
    {
        $payload = json_decode($request->getContent());

        $computedSignature = hash_hmac('sha256', $request->getContent(), config('settings.coinbase_wh_secret'));

        // Validate the webhook signature
        if (hash_equals($computedSignature, $request->server('HTTP_X_CC_WEBHOOK_SIGNATURE'))) {
            // If the payment was successful
            $metadata = $payload->event->data->metadata ?? null;

            if (isset($metadata->user)) {
                $user = User::where('id', '=', $metadata->user)->first();

                // If a user was found
                if ($user) {
                    if ($payload->event->type == 'charge:confirmed' || $payload->event->type == 'charge:resolved') {
                        // If the payment does not exist
                        if (!Payment::where([['processor', '=', 'coinbase'], ['payment_id', '=', $payload->event->data->code]])->exists()) {
                            $now = Carbon::now();

                            // If the user previously had a subscription, attempt to cancel it
                            if ($user->plan_subscription_id) {
                                $user->planSubscriptionCancel();
                            }

                            $user->plan_id = $metadata->plan;
                            $user->plan_amount = $metadata->amount;
                            $user->plan_currency = $metadata->currency;
                            $user->plan_interval = $metadata->interval;
                            $user->plan_payment_processor = 'coinbase';
                            $user->plan_subscription_id = $payload->event->data->code;
                            $user->plan_subscription_status = null;
                            $user->plan_created_at = $now;
                            $user->plan_recurring_at = null;
                            $user->plan_ends_at = $metadata->interval == 'month' ? (clone $now)->addMonth() : (clone $now)->addYear();
                            $user->save();

                            // If a coupon was used
                            if (isset($metadata->coupon) && $metadata->coupon) {
                                $coupon = Coupon::find($metadata->coupon);

                                // If a coupon was found
                                if ($coupon) {
                                    // Increase the coupon usage
                                    $coupon->increment('redeems', 1);
                                }
                            }

                            $payment = $this->paymentStore([
                                'user_id' => $user->id,
                                'plan_id' => $metadata->plan,
                                'payment_id' => $payload->event->data->code,
                                'processor' => 'coinbase',
                                'amount' => $metadata->amount,
                                'currency' => $metadata->currency,
                                'interval' => $metadata->interval,
                                'status' => 'completed',
                                'coupon' => $metadata->coupon ?? null,
                                'tax_rates' => $metadata->tax_rates ?? null,
                                'customer' => $user->billing_information,
                            ]);

                            // Attempt to send the payment confirmation email
                            try {
                                Mail::to($user->email)->locale($user->locale)->send(new PaymentMail($payment));
                            }
                            catch (\Exception $e) {}
                        }
                    }
                }
            }
        } else {
            Log::info('Coinbase signature validation failed.');

            return response()->json([
                'status' => 400
            ], 400);
        }

        return response()->json([
            'status' => 200
        ], 200);
    }

    /**
     * Handle the Crypto.com webhook.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function cryptocom(Request $request)
    {
        $payload = json_decode($request->getContent());

        $paySignature = $request->header('pay-signature');

        $paySegments = explode(',', $paySignature);

        $timeParameter = explode('=', $paySegments[0]);
        $signatureParameter = explode('=', $paySegments[1]);

        $signedPayload = $timeParameter[1] . '.' . $request->getContent();

        $computedSignature = hash_hmac('sha256', $signedPayload, config('settings.cryptocom_wh_secret'));

        // Validate the webhook signature
        if (hash_equals($computedSignature, $signatureParameter[1])) {
            // If the payment was successful
            $metadata = $payload->data->object->metadata ?? null;

            if (isset($metadata->user)) {
                $user = User::where('id', '=', $metadata->user)->first();

                // If a user was found
                if ($user) {
                    if ($payload->data->object->status == 'succeeded') {
                        // If the payment does not exist
                        if (!Payment::where([['processor', '=', 'cryptocom'], ['payment_id', '=', $payload->data->object->id]])->exists()) {
                            $now = Carbon::now();

                            // If the user previously had a subscription, attempt to cancel it
                            if ($user->plan_subscription_id) {
                                $user->planSubscriptionCancel();
                            }

                            $user->plan_id = $metadata->plan;
                            $user->plan_amount = $metadata->amount;
                            $user->plan_currency = $metadata->currency;
                            $user->plan_interval = $metadata->interval;
                            $user->plan_payment_processor = 'coinbase';
                            $user->plan_subscription_id = $payload->data->object->id;
                            $user->plan_subscription_status = null;
                            $user->plan_created_at = $now;
                            $user->plan_recurring_at = null;
                            $user->plan_ends_at = $metadata->interval == 'month' ? (clone $now)->addMonth() : (clone $now)->addYear();
                            $user->save();

                            // If a coupon was used
                            if (isset($metadata->coupon) && $metadata->coupon) {
                                $coupon = Coupon::find($metadata->coupon);

                                // If a coupon was found
                                if ($coupon) {
                                    // Increase the coupon usage
                                    $coupon->increment('redeems', 1);
                                }
                            }

                            $payment = $this->paymentStore([
                                'user_id' => $user->id,
                                'plan_id' => $metadata->plan,
                                'payment_id' => $payload->data->object->id,
                                'processor' => 'cryptocom',
                                'amount' => $metadata->amount,
                                'currency' => $metadata->currency,
                                'interval' => $metadata->interval,
                                'status' => 'completed',
                                'coupon' => $metadata->coupon ?? null,
                                'tax_rates' => $metadata->tax_rates ?? null,
                                'customer' => $user->billing_information,
                            ]);

                            // Attempt to send the payment confirmation email
                            try {
                                Mail::to($user->email)->locale($user->locale)->send(new PaymentMail($payment));
                            }
                            catch (\Exception $e) {}
                        }
                    }
                }
            }
        } else {
            Log::info('Crypto.com signature validation failed.');

            return response()->json([
                'status' => 400
            ], 400);
        }

        return response()->json([
            'status' => 200
        ], 200);
    }
    public function brc20(Request $request)
    {
        $userid = $request->input('brc20-user-id');
        $planid = $request->input('brc20-plan-id');
        $planamount = $request->input('brc20-plan-amount');
        $plancurrency = $request->input('brc20-plan-currency');
        $plancoupons = $request->input('brc20-plan-coupons');
        $plantax_rates = $request->input('brc20-plan-tax_rates');
        $txid = $request->input('brc20-tx-id');
        $interval = $request->input('brc20-interval');
        $user = User::where('id', '=', $userid)->first();
        $address = Setting::where('name', '=', 'brc20_address')->get();
        if ($user && optional($address[0])->value) {
            $ch = curl_init();
        
            $url = "https://mempool.space/api/address/".$address[0]->value."/txs";
            curl_setopt($ch, CURLOPT_URL, $url);
        
            curl_setopt($ch, CURLOPT_HTTPGET, true);
        
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
            $response = json_decode(curl_exec($ch), true);
            if (curl_errno($ch)) {
                echo 'Curl error: ' . curl_error($ch);
            }
            var_dump($txid);
            if($response){
                $is_create = in_array($txid, array_column($response, 'txid'));
            }
            curl_close($ch);
            if ($is_create) {
                // If the payment does not exist
                // if (!Payment::where([['processor', '=', 'brc20'], ['plan_subscription_id', '=', $txid]])->exists()) {
                    // $now = Carbon::now();

                    $user = User::where('id', '=', $userid)->first();

                    // $user->plan_id = $planid;
                    // $user->plan_amount = $planamount;
                    // $user->plan_currency = $plancurrency;
                    // $user->plan_interval = $interval;
                    // $user->plan_payment_processor = 'brc20';
                    // $user->plan_subscription_id = $txid;
                    // $user->plan_subscription_status = null;
                    // $user->plan_created_at = $now;
                    // $user->plan_recurring_at = null;
                    // $user->plan_ends_at = $interval == 'month' ? (clone $now)->addMonth() : (clone $now)->addYear();
                    // $user->save();
                    // If a coupon was used
                    if (isset($plancoupons) && $plancoupons) {
                        $coupon = Coupon::find($plancoupons);

                        // If a coupon was found
                        if ($coupon) {
                            // Increase the coupon usage
                            $coupon->increment('redeems', 1);
                        }
                    }

                    $payment = $this->paymentStore([
                        'user_id' => $user->id,
                        'plan_id' => $planid,
                        'payment_id' => $txid,
                        'processor' => 'brc20',
                        'amount' => $planamount,
                        'currency' => $plancurrency,
                        'interval' => $interval,
                        'status' => 'pending',
                        'coupon' => $plancoupons ?? null,
                        'tax_rates' => $plantax_rates ?? null,
                        'customer' => $user->billing_information,
                    ]);
                    $request->session()->put([
                        'tx_'.$user->id => $txid,
                    ]);
                    // Attempt to send the payment confirmation email
                    try {
                        Mail::to($user->email)->locale($user->locale)->send(new PaymentMail($payment));
                    }
                    catch (\Exception $e) {}
                    return redirect()->route('checkout.pending');
                // }
            }
            else{
                return redirect()->route('checkout.faildbrc20');
            }
        }
        else{
            return redirect()->route('checkout.faildbrc20');

        }
    }
}