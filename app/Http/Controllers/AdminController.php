<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTemplateRequest;
use App\Http\Requests\UpdateChatRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Http\Requests\UpdateImageRequest;
use App\Http\Requests\StoreCouponRequest;
use App\Http\Requests\StorePageRequest;
use App\Http\Requests\StorePlanRequest;
use App\Http\Requests\StoreTaxRateRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateCouponRequest;
use App\Http\Requests\UpdatePageRequest;
use App\Http\Requests\UpdatePlanRequest;
use App\Http\Requests\UpdateSettingRequest;
use App\Http\Requests\UpdateTaxRateRequest;
use App\Http\Requests\UpdateTemplateRequest;
use App\Http\Requests\UpdateTranscriptionRequest;
use App\Http\Requests\UpdateUserProfileRequest;
use App\Mail\PaymentMail;
use App\Models\Chat;
use App\Models\Coupon;
use App\Models\Document;
use App\Models\Image;
use App\Models\Page;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Setting;
use App\Models\TaxRate;
use App\Models\Template;
use App\Models\Transcription;
use App\Traits\ChatTrait;
use App\Traits\DocumentTrait;
use App\Traits\ImageTrait;
use App\Traits\TemplateTrait;
use App\Traits\UserTrait;
use App\Models\User;
use Carbon\Carbon;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    use UserTrait, TemplateTrait, DocumentTrait, ImageTrait, ChatTrait;

    /**
     * Show the Dashboard.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function dashboard()
    {
        $stats = [
            'users' => User::withTrashed()->count(),
            'plans' => Plan::withTrashed()->count(),
            'payments' => Payment::count(),
            'pages' => Page::count(),
            'documents' => Document::count(),
            'images' => Image::count(),
            'chats' => Chat::count(),
            'transcriptions' => Transcription::count()
        ];

        $users = User::withTrashed()->orderBy('id', 'desc')->limit(5)->get();

        $payments = $documents = [];
        if (paymentProcessors()) {
            $payments = Payment::with('plan')->orderBy('id', 'desc')->limit(5)->get();
        } else {
            $documents = Document::with('template')->orderBy('id', 'desc')->limit(5)->get();
        }

        return view('admin.dashboard.index', ['stats' => $stats, 'users' => $users, 'payments' => $payments, 'documents' => $documents]);
    }

    /**
     * Show the Settings forms.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     */
    public function settings(Request $request, $id)
    {
        if (view()->exists('admin.settings.' . $id)) {
            return view('admin.container', ['view' => 'admin.settings.' . $id]);
        }

        abort(404);
    }

    /**
     * Update the Setting.
     *
     * @param UpdateSettingRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateSetting(UpdateSettingRequest $request, $id)
    {
        foreach ($request->except(['_token', 'submit']) as $key => $value) {
            // If the request is for a file upload
            if($request->hasFile($key)) {
                $value = $request->file($key)->hashName();

                // Check if the file exists
                if (file_exists(public_path('uploads/brand/' . config('settings.' . $key)))) {
                    unlink(public_path('uploads/brand/' . config('settings.' . $key)));
                }
                // Save the file
                $request->file($key)->move(public_path('uploads/brand'), $value);
            }

            if ($id == 'cronjob') {
                $value = Str::random(32);
            } elseif ($id == 'license') {
                $httpClient = new HttpClient(['timeout' => 10, 'verify' => false]);

                try {
                    $response = $httpClient->request('POST', 'https://api.lunatio.com/license',
                        [
                            'form_params' => [
                                'license' => $request->input('license_key'),
                                'product' => config('info.software.name')
                            ]
                        ]
                    );

                    $output = json_decode($response->getBody()->getContents());

                    if ($output->status == 200) {
                        Setting::where('name', '=', 'license_key')->update(['value' => $request->input('license_key')]);
                        Setting::where('name', '=', 'license_type')->update(['value' => $output->type]);
                    } else {
                        return redirect()->back()->with('error', $output->status . ' - ' . $output->message);
                    }

                    return redirect()->route('admin.dashboard');
                } catch (\Exception $e) {
                    return redirect()->back()->with('error', $e->getMessage());
                }
            }

            Setting::where('name', $key)->update(['value' => $value]);
        }

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * List the Users.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function indexUsers(Request $request)
    {
        $search = $request->input('search');
        $searchBy = in_array($request->input('search_by'), ['name', 'email']) ? $request->input('search_by') : 'name';
        $role = $request->input('role');
        $sortBy = in_array($request->input('sort_by'), ['id', 'name', 'email']) ? $request->input('sort_by') : 'id';
        $sort = in_array($request->input('sort'), ['asc', 'desc']) ? $request->input('sort') : 'desc';
        $perPage = in_array($request->input('per_page'), [10, 25, 50, 100]) ? $request->input('per_page') : config('settings.paginate');

        $users = User::withTrashed()
            ->when($search, function ($query) use ($search, $searchBy) {
                if($searchBy == 'email') {
                    return $query->searchEmail($search);
                }
                return $query->searchName($search);
            })
            ->when(isset($role) && is_numeric($role), function ($query) use ($role) {
                return $query->ofRole($role);
            })
            ->orderBy($sortBy, $sort)
            ->paginate($perPage)
            ->appends(['search' => $search, 'search_by' => $searchBy, 'role' => $role, 'sort_by' => $sortBy, 'sort' => $sort, 'per_page' => $perPage]);

        return view('admin.container', ['view' => 'admin.users.list', 'users' => $users]);
    }

    /**
     * Show the create User form.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function createUser()
    {
        return view('admin.container', ['view' => 'admin.users.new']);
    }

    /**
     * Show the edit User form.
     *
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editUser($id)
    {
        $user = User::withTrashed()
            ->where('id', $id)
            ->firstOrFail();

        $stats = [
            'payments' => Payment::where('user_id', $user->id)->count(),
            'templates' => Template::where('user_id', $user->id)->count(),
            'documents' => Document::where('user_id', $user->id)->count(),
            'images' => Image::where('user_id', $user->id)->count(),
            'chats' => Chat::where('user_id', $user->id)->count(),
            'transcriptions' => Transcription::where('user_id', $user->id)->count()
        ];

        $plans = Plan::withTrashed()->get();

        return view('admin.container', ['view' => 'account.profile', 'user' => $user, 'stats' => $stats, 'plans' => $plans]);
    }

    /**
     * Store the User.
     *
     * @param StoreUserRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeUser(StoreUserRequest $request)
    {
        $user = new User;

        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->locale = app()->getLocale();
        $user->timezone = config('settings.timezone');
        $user->api_token = Str::random(64);
        $user->tfa = config('settings.registration_tfa');
        $user->default_language = config('settings.openai_default_language');

        $user->save();

        $user->markEmailAsVerified();

        return redirect()->route('admin.users')->with('success', __(':name has been created.', ['name' => $request->input('name')]));
    }

    /**
     * Update the User.
     *
     * @param UpdateUserProfileRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateUser(UpdateUserProfileRequest $request, $id)
    {
        $user = User::withTrashed()->findOrFail($id);

        if ($request->user()->id == $user->id && $request->input('role') == 0) {
            return redirect()->route('admin.users.edit', $id)->with('error', __('Operation denied.'));
        }

        $this->userUpdate($request, $user);

        return redirect()->route('admin.users.edit', $id)->with('success', __('Settings saved.'));
    }

    /**
     * Delete the User.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyUser(Request $request, $id)
    {
        $user = User::withTrashed()->findOrFail($id);

        if ($request->user()->id == $user->id && $user->role == 1) {
            return redirect()->route('admin.users.edit', $id)->with('error', __('Operation denied.'));
        }

        $user->forceDelete();

        return redirect()->route('admin.users')->with('success', __(':name has been deleted.', ['name' => $user->name]));
    }

    /**
     * Soft delete the User.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function disableUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($request->user()->id == $user->id && $user->role == 1) {
            return redirect()->route('admin.users.edit', $id)->with('error', __('Operation denied.'));
        }

        $user->delete();

        return redirect()->route('admin.users.edit', $id)->with('success', __('Settings saved.'));
    }

    /**
     * Restore the soft deleted User.
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restoreUser($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        return redirect()->route('admin.users.edit', $id)->with('success', __('Settings saved.'));
    }

    /**
     * List the Pages.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function indexPages(Request $request)
    {
        $search = $request->input('search');
        $searchBy = in_array($request->input('search_by'), ['name', 'email']) ? $request->input('search_by') : 'name';
        $sortBy = in_array($request->input('sort_by'), ['id', 'name']) ? $request->input('sort_by') : 'id';
        $sort = in_array($request->input('sort'), ['asc', 'desc']) ? $request->input('sort') : 'desc';
        $perPage = in_array($request->input('per_page'), [10, 25, 50, 100]) ? $request->input('per_page') : config('settings.paginate');

        $pages = Page::when($search, function ($query) use ($search, $searchBy) {
                return $query->searchName($search);
            })
            ->orderBy($sortBy, $sort)
            ->paginate($perPage)
            ->appends(['search' => $search, 'search_by' => $searchBy, 'sort_by' => $sortBy, 'sort' => $sort, 'per_page' => $perPage]);

        return view('admin.container', ['view' => 'admin.pages.list', 'pages' => $pages]);
    }

    /**
     * Show the create Page form.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function createPage()
    {
        return view('admin.container', ['view' => 'admin.pages.new']);
    }

    /**
     * Show the edit Page form.
     *
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editPage($id)
    {
        $page = Page::where('id', $id)->firstOrFail();

        return view('admin.container', ['view' => 'admin.pages.edit', 'page' => $page]);
    }

    /**
     * Store the Page.
     *
     * @param StorePageRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storePage(StorePageRequest $request)
    {
        $page = new Page;

        $page->name = $request->input('name');
        $page->slug = $request->input('slug');
        $page->visibility = $request->input('visibility');
        $page->content = $request->input('content');

        $page->save();

        return redirect()->route('admin.pages')->with('success', __(':name has been created.', ['name' => $request->input('name')]));
    }

    /**
     * Update the Page.
     *
     * @param UpdatePageRequest $request
     * @param $id
     * @return mixed
     */
    public function updatePage(UpdatePageRequest $request, $id)
    {
        $page = Page::findOrFail($id);

        $page->name = $request->input('name');
        $page->slug = $request->input('slug');
        $page->visibility = $request->input('visibility');
        $page->content = $request->input('content');

        $page->save();

        return redirect()->route('admin.pages.edit', $id)->with('success', __('Settings saved.'));
    }

    /**
     * Delete the Page.
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroyPage($id)
    {
        $page = Page::findOrFail($id);
        $page->delete();

        return redirect()->route('admin.pages')->with('success', __(':name has been deleted.', ['name' => $page->name]));
    }

    /**
     * List the Payments.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function indexPayments(Request $request)
    {
        $search = $request->input('search');
        $searchBy = in_array($request->input('search_by'), ['payment_id', 'invoice_id']) ? $request->input('search_by') : 'payment_id';
        $userId = $request->input('user_id');
        $planId = $request->input('plan_id');
        $interval = $request->input('interval');
        $processor = $request->input('processor');
        $status = $request->input('status');
        $sortBy = in_array($request->input('sort_by'), ['id']) ? $request->input('sort_by') : 'id';
        $sort = in_array($request->input('sort'), ['asc', 'desc']) ? $request->input('sort') : 'desc';
        $perPage = in_array($request->input('per_page'), [10, 25, 50, 100]) ? $request->input('per_page') : config('settings.paginate');

        $payments = Payment::with('user')
            ->when(isset($planId) && !empty($planId), function ($query) use ($planId) {
                return $query->ofPlan($planId);
            })
            ->when($userId, function ($query) use ($userId) {
                return $query->ofUser($userId);
            })
            ->when($interval, function ($query) use ($interval) {
                return $query->ofInterval($interval);
            })
            ->when($processor, function ($query) use ($processor) {
                return $query->ofProcessor($processor);
            })
            ->when($status, function ($query) use ($status) {
                return $query->ofStatus($status);
            })
            ->when($search, function ($query) use ($search, $searchBy) {
                if($searchBy == 'invoice_id') {
                    return $query->searchInvoice($search);
                }
                return $query->searchPayment($search);
            })
            ->orderBy($sortBy, $sort)
            ->paginate($perPage)
            ->appends(['search' => $search, 'search_by' => $searchBy, 'interval' => $interval, 'processor' => $processor, 'plan_id' => $planId, 'status' => $status, 'user_id' => $userId, 'sort_by' => $sortBy, 'sort' => $sort, 'per_page' => $perPage]);

        // Get all the plans
        $plans = Plan::where([['amount_month', '>', 0], ['amount_year', '>', 0]])->withTrashed()->get();

        $filters = [];

        if ($userId) {
            $user = User::where('id', '=', $userId)->first();
            if ($user) {
                $filters['user'] = $user->name;
            }
        }

        return view('admin.container', ['view' => 'admin.payments.list', 'payments' => $payments, 'interval' => $interval, 'plans' => $plans, 'filters' => $filters]);
    }

    /**
     * Show the edit Payment form.
     *
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editPayment($id)
    {
        $payment = Payment::where('id', $id)->firstOrFail();

        return view('admin.container', ['view' => 'account.payments.edit', 'payment' => $payment]);
    }

    /**
     * Approve the Payment.
     *
     * @param Request $request
     * @param $id
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function approvePayment(Request $request, $id)
    {
        $payment = Payment::where([['id', '=', $id], ['status', '=', 'pending']])->firstOrFail();

        $user = User::where('id', $payment->user_id)->first();

        $payment->status = 'completed';
        $payment->save();

        // Assign the plan to the user
        if ($user) {
            $now = Carbon::now();

            if ($user->plan_subscription_id) {
                $user->planSubscriptionCancel();
            }

            $user->plan_id = $payment->plan->id;
            $user->plan_interval = $payment->interval;
            $user->plan_currency = $payment->currency;
            $user->plan_amount = $payment->amount;
            $user->plan_payment_processor = $payment->processor;
            $user->plan_subscription_id = null;
            $user->plan_subscription_status = null;
            $user->plan_created_at = $now;
            $user->plan_recurring_at = null;
            $user->plan_trial_ends_at = $user->plan_trial_ends_at ? $now : null;
            $user->plan_ends_at = $payment->interval == 'month' ? (clone $now)->addMonth() : (clone $now)->addYear();
            $user->save();

            // If a coupon was used
            if (isset($payment->coupon->id)) {
                $coupon = Coupon::find($payment->coupon->id);

                // If a coupon was found
                if ($coupon) {
                    // Increase the coupon usage
                    $coupon->increment('redeems', 1);
                }
            }

            // Attempt to send an email notification
            try {
                Mail::to($user->email)->locale($user->locale)->send(new PaymentMail($payment));
            }
            catch (\Exception $e) {}
        }

        return redirect()->route('admin.payments.edit', $id)->with('success', __('Settings saved.'));
    }

    /**
     * Cancel the Payment.
     *
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function cancelPayment(Request $request, $id)
    {
        $payment = Payment::where([['id', '=', $id], ['status', '=', 'pending']])->firstOrFail();
        $payment->status = 'cancelled';
        $payment->save();

        $user = User::where('id', $payment->user_id)->first();

        if ($user) {
            // Attempt to send an email notification
            try {
                Mail::to($user->email)->locale($user->locale)->send(new PaymentMail($payment));
            }
            catch (\Exception $e) {}
        }

        return redirect()->route('admin.payments.edit', $id)->with('success', __('Settings saved.'));
    }

    /**
     * Show the Invoice.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showInvoice(Request $request, $id)
    {
        $payment = Payment::where([['id', '=', $id], ['status', '!=', 'pending']])->firstOrFail();

        // Sum the inclusive tax rates
        $inclTaxRatesPercentage = collect($payment->tax_rates)->where('type', '=', 0)->sum('percentage');

        // Sum the exclusive tax rates
        $exclTaxRatesPercentage = collect($payment->tax_rates)->where('type', '=', 1)->sum('percentage');

        return view('admin.container', ['view' => 'account.payments.invoice', 'payment' => $payment, 'inclTaxRatesPercentage' => $inclTaxRatesPercentage, 'exclTaxRatesPercentage' => $exclTaxRatesPercentage]);
    }

    /**
     * List the plans.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function indexPlans(Request $request)
    {
        $search = $request->input('search');
        $searchBy = in_array($request->input('search_by'), ['name']) ? $request->input('search_by') : 'name';
        $visibility = $request->input('visibility');
        $status = $request->input('status');
        $sortBy = in_array($request->input('sort_by'), ['id', 'name']) ? $request->input('sort_by') : 'id';
        $sort = in_array($request->input('sort'), ['asc', 'desc']) ? $request->input('sort') : 'desc';
        $perPage = in_array($request->input('per_page'), [10, 25, 50, 100]) ? $request->input('per_page') : config('settings.paginate');

        $plans = Plan::withTrashed()
            ->when($search, function ($query) use ($search, $searchBy) {
                return $query->searchName($search);
            })
            ->when(isset($visibility) && is_numeric($visibility), function ($query) use ($visibility) {
                return $query->ofVisibility((int)$visibility);
            })
            ->when(isset($status) && is_numeric($status), function ($query) use ($status) {
                if ($status) {
                    $query->whereNotNull('deleted_at');
                } else {
                    $query->whereNull('deleted_at');
                }
            })
            ->orderBy($sortBy, $sort)
            ->paginate($perPage)
            ->appends(['search' => $search, 'search_by' => $searchBy, 'visibility' => $visibility, 'status' => $status, 'sort_by' => $sortBy, 'sort' => $sort, 'per_page' => $perPage]);

        return view('admin.container', ['view' => 'admin.plans.list', 'plans' => $plans]);
    }

    /**
     * Show the create Plan form.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function createPlan()
    {
        $coupons = Coupon::all();

        $taxRates = TaxRate::all();

        return view('admin.container', ['view' => 'admin.plans.new', 'coupons' => $coupons, 'taxRates' => $taxRates]);
    }

    /**
     * Show the edit Plan form.
     *
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editPlan($id)
    {
        $plan = Plan::withTrashed()->where('id', $id)->firstOrFail();

        $coupons = Coupon::all();

        $taxRates = TaxRate::all();

        return view('admin.container', ['view' => 'admin.plans.edit', 'plan' => $plan, 'coupons' => $coupons, 'taxRates' => $taxRates]);
    }

    /**
     * Store the Plan.
     *
     * @param StorePlanRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storePlan(StorePlanRequest $request)
    {
        $plan = new Plan;
        $features = $request->input('features');
        $plan->name = $request->input('name');
        $plan->description = $request->input('description');
        $plan->amount_month = $request->input('amount_month');
        $plan->amount_year = $request->input('amount_year');
        $plan->currency = $request->input('currency');
        $plan->coupons = $request->input('coupons');
        $plan->tax_rates = $request->input('tax_rates');
        $plan->trial_days = $request->input('trial_days');
        $plan->visibility = $request->input('visibility');
        $plan->position = $request->input('position');
        $plan->token_balance = $request->input('balance');
        $plan->features = $features;
        $plan->save();

        return redirect()->route('admin.plans')->with('success', __(':name has been created.', ['name' => $request->input('name')]));
    }

    /**
     * Update the Plan.
     *
     * @param UpdatePlanRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePlan(UpdatePlanRequest $request, $id)
    {
        $plan = Plan::withTrashed()->findOrFail($id);

        if (!$plan->isDefault()) {
            $plan->amount_month = $request->input('amount_month');
            $plan->amount_year = $request->input('amount_year');
            $plan->currency = $request->input('currency');
            $plan->coupons = $request->input('coupons');
            $plan->tax_rates = $request->input('tax_rates');
            $plan->trial_days = $request->input('trial_days');
        }
        $plan->name = $request->input('name');
        $plan->description = $request->input('description');
        $plan->visibility = $request->input('visibility');
        $plan->position = $request->input('position');
        $plan->token_balance = $request->input('balance');
        $plan->features = $request->input('features');
        $plan->save();

        return redirect()->route('admin.plans.edit', $id)->with('success', __('Settings saved.'));
    }

    /**
     * Soft delete the Plan.
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function disablePlan($id)
    {
        $plan = Plan::findOrFail($id);

        // Do not delete the default plan
        if ($plan->isDefault()) {
            return redirect()->route('admin.plans.edit', $id)->with('error', __('The default plan can\'t be disabled.'));
        }

        $plan->delete();

        return redirect()->route('admin.plans.edit', $id)->with('success', __('Settings saved.'));
    }

    /**
     * Restore the Plan.
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restorePlan($id)
    {
        $plan = Plan::withTrashed()->findOrFail($id);
        $plan->restore();

        return redirect()->route('admin.plans.edit', $id)->with('success', __('Settings saved.'));
    }

    /**
     * List the Coupons.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function indexCoupons(Request $request)
    {
        $search = $request->input('search');
        $searchBy = in_array($request->input('search_by'), ['name', 'code']) ? $request->input('search_by') : 'name';
        $type = $request->input('type');
        $status = $request->input('status');
        $sortBy = in_array($request->input('sort_by'), ['id', 'name', 'code']) ? $request->input('sort_by') : 'id';
        $sort = in_array($request->input('sort'), ['asc', 'desc']) ? $request->input('sort') : 'desc';
        $perPage = in_array($request->input('per_page'), [10, 25, 50, 100]) ? $request->input('per_page') : config('settings.paginate');

        $coupons = Coupon::withTrashed()
            ->when($search, function ($query) use ($search, $searchBy) {
                if ($searchBy == 'code') {
                    return $query->searchCode($search);
                }
                return $query->searchName($search);
            })
            ->when(isset($type) && is_numeric($type), function ($query) use ($type) {
                return $query->ofType($type);
            })
            ->when(isset($status) && is_numeric($status), function ($query) use ($status) {
                if ($status) {
                    $query->whereNotNull('deleted_at');
                } else {
                    $query->whereNull('deleted_at');
                }
            })
            ->orderBy($sortBy, $sort)
            ->paginate($perPage)
            ->appends(['search' => $search, 'search_by' => $searchBy, 'type' => $type, 'status' => $status, 'sort_by' => $sortBy, 'sort' => $sort, 'per_page' => $perPage]);

        return view('admin.container', ['view' => 'admin.coupons.list', 'coupons' => $coupons]);
    }

    /**
     * Show the create Coupon form.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function createCoupon()
    {
        return view('admin.container', ['view' => 'admin.coupons.new']);
    }

    /**
     * Show the edit Coupon form.
     *
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editCoupon($id)
    {
        $coupon = Coupon::where('id', $id)->withTrashed()->firstOrFail();

        return view('admin.container', ['view' => 'admin.coupons.edit', 'coupon' => $coupon]);
    }

    /**
     * Store the Coupon.
     *
     * @param StoreCouponRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeCoupon(StoreCouponRequest $request)
    {
        $coupon = new Coupon;

        $coupon->name = $request->input('name');
        $coupon->code = $request->input('code');
        $coupon->type = $request->input('type');
        $coupon->days = $request->input('days');
        $coupon->percentage = $request->input('type') ? 100 : $request->input('percentage');
        $coupon->quantity = $request->input('quantity');

        $coupon->save();

        return redirect()->route('admin.coupons')->with('success', __(':name has been created.', ['name' => $request->input('name')]));
    }

    /**
     * Update the Coupon.
     *
     * @param UpdateCouponRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateCoupon(UpdateCouponRequest $request, $id)
    {
        $coupon = Coupon::withTrashed()->findOrFail($id);

        $coupon->code = $request->input('code');
        $coupon->days = $request->input('days');
        $coupon->quantity = $request->input('quantity');

        $coupon->save();

        return redirect()->route('admin.coupons.edit', $id)->with('success', __('Settings saved.'));
    }

    /**
     * Soft delete the Coupon.
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function disableCoupon($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();

        return redirect()->route('admin.coupons.edit', $id)->with('success', __('Settings saved.'));
    }

    /**
     * Restore the Coupon.
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restoreCoupon($id)
    {
        $coupon = Coupon::withTrashed()->findOrFail($id);
        $coupon->restore();

        return redirect()->route('admin.coupons.edit', $id)->with('success', __('Settings saved.'));
    }

    /**
     * List the Tax Rates.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function indexTaxRates(Request $request)
    {
        $search = $request->input('search');
        $searchBy = in_array($request->input('search_by'), ['name', 'code']) ? $request->input('search_by') : 'name';
        $type = $request->input('type');
        $status = $request->input('status');
        $sortBy = in_array($request->input('sort_by'), ['id', 'name', 'code']) ? $request->input('sort_by') : 'id';
        $sort = in_array($request->input('sort'), ['asc', 'desc']) ? $request->input('sort') : 'desc';
        $perPage = in_array($request->input('per_page'), [10, 25, 50, 100]) ? $request->input('per_page') : config('settings.paginate');

        $taxRates = TaxRate::withTrashed()
            ->when($search, function ($query) use ($search, $searchBy) {
                return $query->searchName($search);
            })
            ->when(isset($type) && is_numeric($type), function ($query) use ($type) {
                return $query->ofType($type);
            })
            ->when(isset($status) && is_numeric($status), function ($query) use ($status) {
                if ($status) {
                    $query->whereNotNull('deleted_at');
                } else {
                    $query->whereNull('deleted_at');
                }
            })
            ->orderBy($sortBy, $sort)
            ->paginate($perPage)
            ->appends(['search' => $search, 'search_by' => $searchBy, 'type' => $type, 'status' => $status, 'sort_by' => $sortBy, 'sort' => $sort, 'per_page' => $perPage]);

        return view('admin.container', ['view' => 'admin.tax-rates.list', 'taxRates' => $taxRates]);
    }

    /**
     * Show the create Tax Rate form.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function createTaxRate()
    {
        return view('admin.container', ['view' => 'admin.tax-rates.new']);
    }

    /**
     * Show the edit Tax Rate form.
     *
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editTaxRate($id)
    {
        $taxRate = TaxRate::where('id', $id)->withTrashed()->firstOrFail();

        return view('admin.container', ['view' => 'admin.tax-rates.edit', 'taxRate' => $taxRate]);
    }

    /**
     * Store the Tax Rate.
     *
     * @param StoreTaxRateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeTaxRate(StoreTaxRateRequest $request)
    {
        $taxRate = new TaxRate;

        $taxRate->name = $request->input('name');
        $taxRate->type = $request->input('type');
        $taxRate->percentage = $request->input('percentage');
        $taxRate->regions = $request->input('regions');

        $taxRate->save();

        return redirect()->route('admin.tax_rates')->with('success', __(':name has been created.', ['name' => $request->input('name')]));
    }

    /**
     * Update the Tax Rate.
     *
     * @param UpdateTaxRateRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateTaxRate(UpdateTaxRateRequest $request, $id)
    {
        $taxRate = TaxRate::withTrashed()->findOrFail($id);

        $taxRate->regions = $request->input('regions');

        $taxRate->save();

        return redirect()->route('admin.tax_rates.edit', $id)->with('success', __('Settings saved.'));
    }

    /**
     * Soft delete the Tax Rate.
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function disableTaxRate($id)
    {
        $taxRate = TaxRate::findOrFail($id);
        $taxRate->delete();

        return redirect()->route('admin.tax_rates.edit', $id)->with('success', __('Settings saved.'));
    }

    /**
     * Restore the Tax Rate.
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restoreTaxRate($id)
    {
        $taxRate = TaxRate::withTrashed()->findOrFail($id);
        $taxRate->restore();

        return redirect()->route('admin.tax_rates.edit', $id)->with('success', __('Settings saved.'));
    }

    /**
     * List the Templates.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function indexTemplates(Request $request)
    {
        $search = $request->input('search');
        $searchBy = in_array($request->input('search_by'), ['name']) ? $request->input('search_by') : 'name';
        $userId = $request->input('user_id');
        $type = $request->input('type');
        $sortBy = in_array($request->input('sort_by'), ['id', 'name']) ? $request->input('sort_by') : 'id';
        $sort = in_array($request->input('sort'), ['asc', 'desc']) ? $request->input('sort') : 'desc';
        $perPage = in_array($request->input('per_page'), [10, 25, 50, 100]) ? $request->input('per_page') : config('settings.paginate');

        $templates = Template::with('user')
            ->when($search, function ($query) use ($search, $searchBy) {
                return $query->searchName($search);
            })
            ->when($userId, function ($query) use ($userId) {
                return $query->ofUser($userId);
            })
            ->when($type, function ($query) use ($type) {
                if ($type == 1) {
                    return $query->global();
                } else {
                    return $query->private();
                }
            })
            ->custom()
            ->orderBy($sortBy, $sort)
            ->paginate($perPage)
            ->appends(['search' => $search, 'search_by' => $searchBy, 'user_id' => $userId, 'type' => $type, 'sort_by' => $sortBy, 'sort' => $sort, 'per_page' => $perPage]);

        $filters = [];

        if ($userId) {
            $user = User::where('id', '=', $userId)->first();
            if ($user) {
                $filters['user'] = $user->name;
            }
        }

        return view('admin.container', ['view' => 'admin.templates.list', 'templates' => $templates, 'filters' => $filters]);
    }

    /**
     * Show the create Template form.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function createTemplate()
    {
        return view('admin.container', ['view' => 'templates.new']);
    }

    /**
     * Show the edit Template form.
     *
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editTemplate($id)
    {
        $template = Template::where([['id', '=', $id]])->firstOrFail();

        $stats = [
            'documents' => $template->user_id ? Document::where([['user_id', '=', $template->user_id], ['template_id', '=', $template->id]])->count() : Document::where('template_id', '=', $template->id)->count(),
        ];

        return view('admin.container', ['view' => 'templates.edit', 'template' => $template, 'stats' => $stats]);
    }

    /**
     * Store the Template.
     *
     * @param StoreTemplateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeTemplate(StoreTemplateRequest $request)
    {
        $this->templateStore($request);

        return redirect()->route('admin.templates')->with('success', __(':name has been created.', ['name' => $request->input('name')]));
    }

    /**
     * Update the Template.
     *
     * @param UpdateTemplateRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateTemplate(UpdateTemplateRequest $request, $id)
    {
        $template = Template::where([['id', '=', $id]])->firstOrFail();

        $this->templateUpdate($request, $template);

        return redirect()->route('admin.templates.edit', $id)->with('success', __('Settings saved.'));
    }

    /**
     * Delete the Template.
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroyTemplate($id)
    {
        $template = Template::where([['id', '=', $id]])->custom()->firstOrFail();
        $template->delete();

        return redirect()->route('admin.templates')->with('success', __(':name has been deleted.', ['name' => $template->name]));
    }

    /**
     * List the Documents.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function indexDocuments(Request $request)
    {
        $search = $request->input('search');
        $searchBy = in_array($request->input('search_by'), ['name', 'result']) ? $request->input('search_by') : 'name';
        $templateId = $request->input('template_id');
        $userId = $request->input('user_id');
        $sortBy = in_array($request->input('sort_by'), ['id', 'name']) ? $request->input('sort_by') : 'id';
        $sort = in_array($request->input('sort'), ['asc', 'desc']) ? $request->input('sort') : 'desc';
        $perPage = in_array($request->input('per_page'), [10, 25, 50, 100]) ? $request->input('per_page') : config('settings.paginate');

        $documents = Document::with('template', 'user')
            ->when($search, function ($query) use ($search, $searchBy) {
                if ($searchBy == 'result') {
                    return $query->searchResult($search);
                }
                return $query->searchName($search);
            })
            ->when(isset($templateId), function ($query) use ($templateId) {
                return $query->ofTemplate($templateId);
            })
            ->when($userId, function ($query) use ($userId) {
                return $query->ofUser($userId);
            })
            ->orderBy($sortBy, $sort)
            ->paginate($perPage)
            ->appends(['search' => $search, 'user_id' => $userId, 'template_id' => $templateId, 'search_by' => $searchBy, 'sort_by' => $sortBy, 'sort' => $sort, 'per_page' => $perPage]);

        $filters = [];

        if ($userId) {
            $user = User::where('id', '=', $userId)->first();
            if ($user) {
                $filters['user'] = $user->name;
            }
        }

        if ($templateId) {
            $template = Template::where('id', '=', $templateId)->first();
            if ($template) {
                $filters['template'] = $template->name;
            }
        }

        $templates = Template::all();

        return view('admin.container', ['view' => 'admin.documents.list', 'documents' => $documents, 'templates' => $templates, 'filters' => $filters]);
    }

    /**
     * Show the edit Document form.
     *
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editDocument($id)
    {
        $document = Document::where('id', $id)->firstOrFail();

        return view('admin.container', ['view' => 'documents.edit', 'document' => $document]);
    }

    /**
     * Update the Document.
     *
     * @param UpdateDocumentRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateDocument(UpdateDocumentRequest $request, $id)
    {
        $document = Document::where('id', $id)->firstOrFail();

        $this->documentUpdate($request, $document);

        return redirect()->route('admin.documents.edit', $id)->with('success', __('Settings saved.'));
    }

    /**
     * Delete the Document.
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroyDocument($id)
    {
        $document = Document::where('id', $id)->firstOrFail();
        $document->delete();

        return redirect()->route('admin.documents')->with('success', __(':name has been deleted.', ['name' => $document->name]));
    }

    /**
     * List the Images.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function indexImages(Request $request)
    {
        $search = $request->input('search');
        $searchBy = in_array($request->input('search_by'), ['name']) ? $request->input('search_by') : 'name';
        $userId = $request->input('user_id');
        $style = $request->input('style');
        $medium = $request->input('medium');
        $filter = $request->input('filter');
        $resolution = $request->input('resolution');
        $sortBy = in_array($request->input('sort_by'), ['id', 'name']) ? $request->input('sort_by') : 'id';
        $sort = in_array($request->input('sort'), ['asc', 'desc']) ? $request->input('sort') : 'desc';
        $perPage = in_array($request->input('per_page'), [10, 25, 50, 100]) ? $request->input('per_page') : config('settings.paginate');

        $images = Image::with('user')
            ->when($search, function ($query) use ($search, $searchBy) {
                return $query->searchName($search);
            })
            ->when($userId, function ($query) use ($userId) {
                return $query->ofUser($userId);
            })
            ->when($style, function ($query) use ($style) {
                return $query->ofStyle($style);
            })
            ->when($medium, function ($query) use ($medium) {
                return $query->ofMedium($medium);
            })
            ->when($filter, function ($query) use ($filter) {
                return $query->ofFilter($filter);
            })
            ->when($resolution, function ($query) use ($resolution) {
                return $query->ofResolution($resolution);
            })
            ->orderBy($sortBy, $sort)
            ->paginate($perPage)
            ->appends(['search' => $search, 'user_id' => $userId, 'style' => $style, 'medium' => $medium, 'filter' => $filter, 'resolution' => $resolution, 'search_by' => $searchBy, 'sort_by' => $sortBy, 'sort' => $sort, 'per_page' => $perPage]);

        $filters = [];

        if ($userId) {
            $user = User::where('id', '=', $userId)->first();
            if ($user) {
                $filters['user'] = $user->name;
            }
        }

        return view('admin.container', ['view' => 'admin.images.list', 'images' => $images, 'filters' => $filters]);
    }

    /**
     * Show the edit Image form.
     *
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editImage($id)
    {
        $image = Image::where('id', $id)->firstOrFail();

        return view('admin.container', ['view' => 'images.edit', 'image' => $image]);
    }

    /**
     * Update the Image.
     *
     * @param UpdateImageRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateImage(UpdateImageRequest $request, $id)
    {
        $image = Image::where('id', $id)->firstOrFail();

        $this->imageUpdate($request, $image);

        return redirect()->route('admin.images.edit', $id)->with('success', __('Settings saved.'));
    }

    /**
     * Delete the Image.
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroyImage($id)
    {
        $image = Image::where('id', $id)->firstOrFail();
        $image->delete();

        return redirect()->route('admin.images')->with('success', __(':name has been deleted.', ['name' => $image->name]));
    }

    /**
     * List the Chats.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function indexChats(Request $request)
    {
        $search = $request->input('search');
        $searchBy = in_array($request->input('search_by'), ['name']) ? $request->input('search_by') : 'name';
        $userId = $request->input('user_id');
        $sortBy = in_array($request->input('sort_by'), ['id', 'name']) ? $request->input('sort_by') : 'id';
        $sort = in_array($request->input('sort'), ['asc', 'desc']) ? $request->input('sort') : 'desc';
        $perPage = in_array($request->input('per_page'), [10, 25, 50, 100]) ? $request->input('per_page') : config('settings.paginate');

        $chats = Chat::with('user')
            ->when($search, function ($query) use ($search, $searchBy) {
                return $query->searchName($search);
            })
            ->when($userId, function ($query) use ($userId) {
                return $query->ofUser($userId);
            })
            ->orderBy($sortBy, $sort)
            ->paginate($perPage)
            ->appends(['search' => $search, 'search_by' => $searchBy, 'user_id' => $userId, 'sort_by' => $sortBy, 'sort' => $sort, 'per_page' => $perPage]);

        $filters = [];

        if ($userId) {
            $user = User::where('id', '=', $userId)->first();
            if ($user) {
                $filters['user'] = $user->name;
            }
        }

        return view('admin.container', ['view' => 'admin.chats.list', 'chats' => $chats, 'filters' => $filters]);
    }

    /**
     * Show the edit Chat form.
     *
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editChat($id)
    {
        $chat = Chat::where('id', $id)->firstOrFail();

        return view('admin.container', ['view' => 'chats.edit', 'chat' => $chat]);
    }

    /**
     * Update the Chat.
     *
     * @param UpdateChatRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateChat(UpdateChatRequest $request, $id)
    {
        $chat = Chat::where('id', $id)->firstOrFail();

        $this->chatUpdate($request, $chat);

        return redirect()->route('admin.chats.edit', $id)->with('success', __('Settings saved.'));
    }

    /**
     * Delete the Chat.
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroyChat($id)
    {
        $chat = Chat::where('id', $id)->firstOrFail();
        $chat->delete();

        return redirect()->route('admin.chats')->with('success', __(':name has been deleted.', ['name' => $chat->name]));
    }

    /**
     * List the Transcriptions.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function indexTranscriptions(Request $request)
    {
        $search = $request->input('search');
        $searchBy = in_array($request->input('search_by'), ['name']) ? $request->input('search_by') : 'name';
        $userId = $request->input('user_id');
        $sortBy = in_array($request->input('sort_by'), ['id', 'name']) ? $request->input('sort_by') : 'id';
        $sort = in_array($request->input('sort'), ['asc', 'desc']) ? $request->input('sort') : 'desc';
        $perPage = in_array($request->input('per_page'), [10, 25, 50, 100]) ? $request->input('per_page') : config('settings.paginate');

        $transcriptions = Transcription::with('user')
            ->when($search, function ($query) use ($search, $searchBy) {
                return $query->searchName($search);
            })
            ->when($userId, function ($query) use ($userId) {
                return $query->ofUser($userId);
            })
            ->orderBy($sortBy, $sort)
            ->paginate($perPage)
            ->appends(['search' => $search, 'search_by' => $searchBy, 'user_id' => $userId, 'sort_by' => $sortBy, 'sort' => $sort, 'per_page' => $perPage]);

        $filters = [];

        if ($userId) {
            $user = User::where('id', '=', $userId)->first();
            if ($user) {
                $filters['user'] = $user->name;
            }
        }

        return view('admin.container', ['view' => 'admin.transcriptions.list', 'transcriptions' => $transcriptions, 'filters' => $filters]);
    }

    /**
     * Show the edit Transcription form.
     *
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editTranscription($id)
    {
        $transcription = Transcription::where('id', $id)->firstOrFail();

        return view('admin.container', ['view' => 'transcriptions.edit', 'transcription' => $transcription]);
    }

    /**
     * Update the Transcription.
     *
     * @param UpdateTranscriptionRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateTranscription(UpdateTranscriptionRequest $request, $id)
    {
        $transcription = Transcription::where('id', $id)->firstOrFail();

        $this->transcriptionUpdate($request, $transcription);

        return redirect()->route('admin.transcriptions.edit', $id)->with('success', __('Settings saved.'));
    }

    /**
     * Delete the Transcription.
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroyTranscription($id)
    {
        $transcription = Transcription::where('id', $id)->firstOrFail();
        $transcription->delete();

        return redirect()->route('admin.transcriptions')->with('success', __(':name has been deleted.', ['name' => $transcription->name]));
    }
}
