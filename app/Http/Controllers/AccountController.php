<?php

namespace App\Http\Controllers;

use App\Http\Requests\DestroyUserRequest;
use App\Http\Requests\UpdateUserPreferencesRequest;
use App\Http\Requests\UpdateUserProfileRequest;
use App\Http\Requests\UpdateUserSecurityRequest;
use App\Models\Document;
use App\Models\Image;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Template;
use App\Traits\UserTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AccountController extends Controller
{
    use UserTrait;

    /**
     * Show the Settings index.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request) {
        return view('account.container', ['view' => 'index', 'user' => $request->user()]);
    }

    /**
     * Show the Profile settings form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function profile(Request $request)
    {
        return view('account.container', ['view' => 'profile', 'user' => $request->user()]);
    }

    /**
     * Update the Profile settings.
     *
     * @param UpdateUserProfileRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateProfile(UpdateUserProfileRequest $request)
    {
        $this->userUpdate($request, $request->user());

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Resent the Account Email Confirmation request.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resendAccountEmailConfirmation(Request $request)
    {
        try {
            $request->user()->resendPendingEmailVerificationMail();
        } catch (\Exception $e) {
            return redirect()->route('account.profile')->with('error', $e->getMessage());
        }

        return back()->with('success', __('A new verification link has been sent to your email address.'));
    }

    /**
     * Cancel the Account Email Confirmation request.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancelAccountEmailConfirmation(Request $request)
    {
        $request->user()->clearPendingEmail();

        return back();
    }

    /**
     * Show the Security settings form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function security(Request $request)
    {
        return view('account.container', ['view' => 'security', 'user' => $request->user()]);
    }

    /**
     * Update the Security settings.
     *
     * @param UpdateUserSecurityRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSecurity(UpdateUserSecurityRequest $request)
    {
        if ($request->input('password')) {
            $request->user()->password = Hash::make($request->input('password'));

            Auth::logoutOtherDevices($request->input('password'));
        }

        if ($request->has('tfa')) {
            $request->user()->tfa = $request->boolean('tfa');
        }

        $request->user()->save();

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Show the Preference settings form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function preferences(Request $request)
    {
        return view('account.container', ['view' => 'preferences']);
    }

    /**
     * Update the Preferences settings.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePreferences(UpdateUserPreferencesRequest $request)
    {
        $request->user()->default_language = $request->input('default_language');
        $request->user()->default_creativity = $request->input('default_creativity');
        $request->user()->default_variations = $request->input('default_variations');

        $request->user()->save();

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Show the Plan settings form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function plan(Request $request)
    {
        $templates = Template::global()->premade()->orderBy('name', 'asc')->get();

        $customTemplates = Template::global()->custom()->orderBy('name', 'asc')->get();

        return view('account.container', ['view' => 'plan', 'user' => $request->user(), 'templates' => $templates, 'customTemplates' => $customTemplates]);
    }

    /**
     * Update the Plan settings.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePlan(Request $request)
    {
        $request->user()->planSubscriptionCancel();

        return back()->with('success', __('Settings saved.'));
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
        $plan = $request->input('plan');
        $interval = $request->input('interval');
        $processor = $request->input('processor');
        $status = $request->input('status');
        $sortBy = in_array($request->input('sort_by'), ['id']) ? $request->input('sort_by') : 'id';
        $sort = in_array($request->input('sort'), ['asc', 'desc']) ? $request->input('sort') : 'desc';
        $perPage = in_array($request->input('per_page'), [10, 25, 50, 100]) ? $request->input('per_page') : config('settings.paginate');

        $payments = Payment::where('user_id', '=', $request->user()->id)
            ->when(isset($plan) && !empty($plan), function ($query) use ($plan) {
                return $query->ofPlan($plan);
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
            ->appends(['search' => $search, 'search_by' => $searchBy, 'interval' => $interval, 'processor' => $processor, 'plan' => $plan, 'status' => $status, 'sort_by' => $sortBy, 'sort' => $sort, 'per_page' => $perPage]);

        // Get all the plans
        $plans = Plan::where([['amount_month', '>', 0], ['amount_year', '>', 0]])->withTrashed()->get();

        return view('account.container', ['view' => 'payments.list', 'payments' => $payments, 'plans' => $plans]);
    }

    /**
     * Show the edit Payment form.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editPayment(Request $request, $id)
    {
        $payment = Payment::where([['id', '=', $id], ['user_id', '=', $request->user()->id]])->firstOrFail();

        return view('account.container', ['view' => 'payments.edit', 'payment' => $payment]);
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
        $payment = Payment::where([['id', '=', $id], ['status', '=', 'pending'], ['user_id', '=', $request->user()->id]])->firstOrFail();
        $payment->status = 'cancelled';
        $payment->save();

        return redirect()->route('account.payments.edit', $id)->with('success', __('Settings saved.'));
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
        $payment = Payment::where([['user_id', '=', $request->user()->id], ['id', '=', $id], ['status', '=', 'completed']])->firstOrFail();

        // Sum the inclusive tax rates
        $inclTaxRatesPercentage = collect($payment->tax_rates)->where('type', '=', 0)->sum('percentage');

        // Sum the exclusive tax rates
        $exclTaxRatesPercentage = collect($payment->tax_rates)->where('type', '=', 1)->sum('percentage');

        return view('account.container', ['view' => 'payments.invoice', 'payment' => $payment, 'inclTaxRatesPercentage' => $inclTaxRatesPercentage, 'exclTaxRatesPercentage' => $exclTaxRatesPercentage]);
    }

    /**
     * Show the API settings form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function api(Request $request)
    {
        return view('account.container', ['view' => 'api', 'user' => $request->user()]);
    }

    /**
     * Update the API settings.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateApi(Request $request)
    {
        $request->user()->api_token = Str::random(64);
        $request->user()->save();

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Show the Delete Account form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function delete(Request $request)
    {
        return view('account.container', ['view' => 'delete', 'user' => $request->user()]);
    }

    /**
     * Delete the Account.
     *
     * @param DestroyUserRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyUser(DestroyUserRequest $request)
    {
        $request->user()->forceDelete();

        return redirect()->route('home');
    }
}
