<?php

namespace App\Traits;

use App\Models\Coupon;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\TaxRate;

trait PaymentTrait
{
    /**
     * Store the Payment.
     *
     * @param $params
     * @return Payment
     */
    private function paymentStore($params)
    {
        $payment = new Payment();
        $payment->user_id = $params['user_id'];
        $payment->plan_id = $params['plan_id'];
        $payment->payment_id = $params['payment_id'];
        $payment->processor = $params['processor'];
        $payment->amount = $params['amount'];
        $payment->currency = $params['currency'];
        $payment->interval = $params['interval'];
        $payment->status = $params['status'];
        $payment->product = Plan::select('id', 'name', 'currency', 'amount_' . $params['interval'])->where('id', '=', $params['plan_id'])->withTrashed()->first();
        $payment->coupon = $params['coupon'] ? Coupon::select('id', 'name', 'code', 'type', 'percentage')->where('id', '=', $params['coupon'])->withTrashed()->first() : null;
        $payment->tax_rates = $params['tax_rates'] ? TaxRate::select('id', 'name', 'type', 'percentage')->whereIn('id', explode('_', $params['tax_rates']))->withTrashed()->get() : null;
        $payment->customer = $params['customer'];
        $payment->seller = collect([
            'title' => config('settings.title'),
            'vendor' => config('settings.billing_vendor'),
            'address' => config('settings.billing_address'),
            'city' => config('settings.billing_city'),
            'state' => config('settings.billing_state'),
            'postal_code' => config('settings.billing_postal_code'),
            'country' => config('settings.billing_country'),
            'phone' => config('settings.billing_phone'),
            'vat_number' => config('settings.billing_vat_number')
        ]);
        $payment->save();

        // Store the invoice ID
        $payment->invoice_id = config('settings.billing_invoice_prefix') . $payment->id;
        $payment->save();

        return $payment;
    }
}
