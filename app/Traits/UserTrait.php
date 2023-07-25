<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

trait UserTrait
{
    /**
     * Update the User.
     *
     * @param Request $request
     * @param User $user
     * @return User
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function userUpdate(Request $request, User $user)
    {
        $user->name = $request->input('name');
        $user->timezone = $request->input('timezone');
        $user->tfa = $request->boolean('tfa');

        if ($user->email != $request->input('email')) {
            // If email registration site setting is enabled and the request is not from the Admin Panel
            if (config('settings.registration_verification') && !$request->is('admin/*')) {
                // Send send email validation notification
                $user->newEmail($request->input('email'));
            } else {
                $user->email = $request->input('email');
            }
        }

        if ($request->is('admin/*') && $request->user()->role == 1) {
            $user->role = $request->input('role');

            // Update the password
            if (!empty($request->input('password'))) {
                $user->password = Hash::make($request->input('password'));
            }

            // Update the email verified status
            if ($request->input('email_verified_at')) {
                $user->markEmailAsVerified();
            } else {
                $user->email_verified_at = null;
            }

            // Update the plan
            if ($request->input('plan_id')) {
                $planEndsAt = null;
                // If the plan ends at is set, and the plan is not the default one
                if ($request->input('plan_ends_at') && $request->input('plan_id') != 1) {
                    $planEndsAt = Carbon::createFromFormat('Y-m-d', $request->input('plan_ends_at'), $user->timezone ?? config('app.timezone'))->tz(config('app.timezone'));
                }

                // If the plan has changed
                // or if the plan end date is indefinitely but the date has changed
                // or if the plan has an end date but the end date has changed
                if ($user->plan->id != $request->input('plan_id') || ($user->plan_ends_at == null && $user->plan_ends_at != $planEndsAt) || ($user->plan_ends_at && $planEndsAt && !$user->plan_ends_at->isSameDay($planEndsAt))) {
                    $now = Carbon::now();

                    // If the user previously had a subscription, attempt to cancel it
                    if ($user->plan_subscription_id) {
                        $user->planSubscriptionCancel();
                    }

                    $user->plan_id = $request->input('plan_id');
                    $user->plan_interval = null;
                    $user->plan_currency = null;
                    $user->plan_amount = null;
                    $user->plan_payment_processor = null;
                    $user->plan_subscription_id = null;
                    $user->plan_subscription_status = null;
                    $user->plan_created_at = $now;
                    $user->plan_recurring_at = null;
                    $user->plan_trial_ends_at = $user->plan_trial_ends_at ? $now : null;
                    $user->plan_ends_at = $planEndsAt;
                }
            }
        }

        $user->save();

        return $user;
    }
}
