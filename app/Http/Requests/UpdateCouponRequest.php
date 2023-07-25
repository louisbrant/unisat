<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCouponRequest extends FormRequest
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
            'code' => ['required', 'alpha_dash', 'max:128', 'unique:coupons,code,'.$this->route('id')],
            'days' => ['required_if:type,1', 'nullable', 'integer', 'min:-1', 'max:3650'],
            'quantity' => ['required', 'integer'],
        ];
    }
}
