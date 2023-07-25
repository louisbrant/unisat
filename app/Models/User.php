<?php

namespace App\Models;

use Carbon\Carbon;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Translation\HasLocalePreference;
use ProtoneMedia\LaravelVerifyNewEmail\MustVerifyNewEmail;

/**
 * Class User
 *
 * @mixin Builder
 * @package App
 */
class User extends Authenticatable implements MustVerifyEmail, hasLocalePreference
{
    use MustVerifyNewEmail, Notifiable, SoftDeletes;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'plan_metadata' => 'object',
        'billing_information' => 'object',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'email_verified_at',
        'plan_created_at',
        'plan_recurring_at',
        'plan_ends_at',
        'plan_trial_ends_at',
        'tfa_code_created_at',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeSearchName(Builder $query, $value)
    {
        return $query->where('name', 'like', '%' . $value . '%');
    }

    /**
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeSearchEmail(Builder $query, $value)
    {
        return $query->where('email', 'like', '%' . $value . '%');
    }

    /**
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeOfRole(Builder $query, $value)
    {
        return $query->where('role', '=', $value);
    }

    /**
     * Get the preferred locale of the entity.
     *
     * @return string|null
     */
    public function preferredLocale()
    {
        return $this->locale;
    }

    /**
     * Returns whether the user is an admin or not.
     *
     * @return bool
     */
    public function admin()
    {
        return $this->role == 1;
    }

    /**
     * Get the plan that the user owns.
     *
     * @return mixed
     */
    public function plan()
    {
        // If the current plan is default, or the plan is not active
        if ($this->planIsDefault() || !$this->planIsActive()) {

            // Switch to the default plan
            $this->plan_id = 1;
        }

        return $this->belongsTo('App\Models\Plan')->withTrashed();
    }

    /**
     * Determine if the plan subscription is no longer active.
     *
     * @return bool
     */
    public function planIsCancelled()
    {
        return !is_null($this->plan_ends_at);
    }

    /**
     * Determine if the plan subscription is within its trial period.
     *
     * @return bool
     */
    public function planOnTrial()
    {
        return $this->plan_trial_ends_at && $this->plan_trial_ends_at->isFuture();
    }

    /**
     * Determine if the plan subscription is active.
     *
     * @return bool
     */
    public function planIsActive()
    {
        if ($this->plan_payment_processor == 'paypal') {
            return $this->planOnTrial() || $this->planOnGracePeriod() || $this->plan_subscription_status == 'ACTIVE';
        } elseif ($this->plan_payment_processor == 'stripe') {
            return $this->planOnTrial() || $this->planOnGracePeriod() || $this->plan_subscription_status == 'active';
        } elseif ($this->plan_payment_processor == 'razorpay') {
            return $this->planOnTrial() || $this->planOnGracePeriod() || $this->plan_subscription_status == 'active';
        } elseif ($this->plan_payment_processor == 'paystack') {
            return $this->planOnTrial() || $this->planOnGracePeriod() || $this->plan_subscription_status == 'active';
        } else {
            return !$this->planIsCancelled() || $this->planOnTrial() || $this->planOnGracePeriod();
        }
    }

    /**
     * Determine if the plan subscription is recurring and not on trial.
     *
     * @return bool
     */
    public function planIsRecurring()
    {
        return !$this->planOnTrial() && !$this->planIsCancelled();
    }

    /**
     * Determine if the plan subscription is within its grace period after cancellation.
     *
     * @return bool
     */
    public function planOnGracePeriod()
    {
        return $this->plan_ends_at && $this->plan_ends_at->isFuture();
    }

    /**
     * Determine if the user is subscribed to the default plan.
     *
     * @return bool
     */
    public function planIsDefault()
    {
        return $this->plan_id == 1;
    }

    /**
     * Cancel the current plan.
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function planSubscriptionCancel()
    {
        if ($this->plan_payment_processor == 'paypal') {
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
            } catch (BadResponseException $e) {}

            // Attempt to cancel the subscription
            try {
                $payPalSubscriptionCancelRequest = $httpClient->request('POST', $httpBaseUrl . 'v1/billing/subscriptions/' . $this->plan_subscription_id . '/cancel', [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $payPalAuth->access_token,
                            'Content-Type' => 'application/json'
                        ],
                        'body' => json_encode([
                            'reason' => __('Cancelled')
                        ])
                    ]
                );
            } catch (BadResponseException $e) {}
        } elseif ($this->plan_payment_processor == 'stripe') {
            // Attempt to cancel the current subscription
            try {
                $stripe = new \Stripe\StripeClient(
                    config('settings.stripe_secret')
                );

                $stripe->subscriptions->update(
                    $this->plan_subscription_id,
                    ['cancel_at_period_end' => true]
                );
            } catch (\Exception $e) {}
        } elseif ($this->plan_payment_processor == 'razorpay') {
            // Attempt to cancel the current subscription
            try {
                $razorpay = new \Razorpay\Api\Api(config('settings.razorpay_key'), config('settings.razorpay_secret'));

                $razorpay->subscription->fetch($this->plan_subscription_id)->cancel();
            } catch (\Exception $e) {}
        } elseif ($this->plan_payment_processor == 'paystack') {
            $httpClient = new HttpClient();

            // Attempt to cancel the current subscription
            try {
                $paystackSubscriptionRequest = $httpClient->request('GET', 'https://api.paystack.co/subscription/' . $this->plan_subscription_id, [
                        'headers' => [
                            'Authorization' => 'Bearer ' . config('settings.paystack_secret'),
                            'Content-Type' => 'application/json',
                            'Cache-Control' => 'no-cache'
                        ]
                    ]
                );

                $paystackSubscription = json_decode($paystackSubscriptionRequest->getBody()->getContents());
            } catch (\Exception $e) {}

            if (isset($paystackSubscription->data->email_token)) {
                try {
                    $httpClient->request('POST', 'https://api.paystack.co/subscription/disable', [
                            'headers' => [
                                'Authorization' => 'Bearer ' . config('settings.paystack_secret'),
                                'Content-Type' => 'application/json',
                                'Cache-Control' => 'no-cache'
                            ],
                            'body' => json_encode([
                                'code' => $this->plan_subscription_id,
                                'token' => $paystackSubscription->data->email_token
                            ])
                        ]
                    );
                } catch (\Exception $e) {}
            }
        }

        // Update the subscription end date and recurring date
        if (!empty($this->plan_recurring_at)) {
            $this->plan_ends_at = $this->plan_recurring_at;
            $this->plan_recurring_at = null;
        }
        $this->save();
    }

    /**
     * Get the user's templates.
     */
    public function templates()
    {
        return $this->hasMany('App\Models\Template')->where('user_id', $this->id);
    }

    /**
     * Get the user's documents.
     */
    public function documents()
    {
        return $this->hasMany('App\Models\Document')->where('user_id', $this->id);
    }

    /**
     * Get the user's images.
     */
    public function images()
    {
        return $this->hasMany('App\Models\Image')->where('user_id', $this->id);
    }

    /**
     * Get the user's chats.
     */
    public function chats()
    {
        return $this->hasMany('App\Models\Chat')->where('user_id', $this->id);
    }

    /**
     * Get the user's messages.
     */
    public function messages()
    {
        return $this->hasMany('App\Models\Message')->where('user_id', $this->id);
    }

    /**
     * Get the user's transcriptions.
     */
    public function transcriptions()
    {
        return $this->hasMany('App\Models\Transcription')->where('user_id', $this->id);
    }
}
