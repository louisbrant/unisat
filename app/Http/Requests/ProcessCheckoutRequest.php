<?php

namespace App\Http\Requests;

use App\Rules\ValidateCouponCodeRule;
use Illuminate\Foundation\Http\FormRequest;

class ProcessCheckoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'payment_processor' => ['required', 'in:' . implode(',', array_keys(paymentProcessors()))],
            'interval' => ['required', 'in:month,year'],
            'name' => ['required'],
            'address' => ['required'],
            'city' => ['required'],
            'postal_code' => ['required'],
            'country' => ['required'],
            'coupon' => ['sometimes', 'min:1', new ValidateCouponCodeRule($this->route('id'))],
            'payment_id' => ['required_if:payment_processor,bank', 'alpha_num', 'min:16', 'max:16', 'unique:payments,payment_id']
        ];
    }
}
