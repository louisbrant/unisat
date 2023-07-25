<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'product' => 'object',
        'tax_rates' => 'object',
        'coupon' => 'object',
        'customer' => 'object',
        'seller' => 'object'
    ];

    /**
     * Get the user that owns the payment.
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

    /**
     * Get the plan of the payment.
     */
    public function plan()
    {
        return $this->belongsTo('App\Models\Plan')->withTrashed();
    }

    /**
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeSearchPayment(Builder $query, $value)
    {
        return $query->where('payment_id', 'like', '%' . $value . '%');
    }

    /**
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeSearchInvoice(Builder $query, $value)
    {
        return $query->where([['invoice_id', 'like', '%' . $value . '%'], ['status', '<>', 'pending']]);
    }

    /**
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeOfPlan(Builder $query, $value)
    {
        return $query->where('plan_id', '=', $value);
    }

    /**
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeOfUser(Builder $query, $value)
    {
        return $query->where('user_id', '=', $value);
    }

    /**
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeOfInterval(Builder $query, $value)
    {
        return $query->where('interval', '=', $value);
    }

    /**
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeOfProcessor(Builder $query, $value)
    {
        return $query->where('processor', '=', $value);
    }

    /**
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeOfStatus(Builder $query, $value)
    {
        return $query->where('status', '=', $value);
    }
}
