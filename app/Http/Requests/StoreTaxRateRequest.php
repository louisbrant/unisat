<?php

namespace App\Http\Requests;

use App\Rules\ValidateExtendedLicenseRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTaxRateRequest extends FormRequest
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
            'name' => ['required', 'max:128', new ValidateExtendedLicenseRule()],
            'type' => ['required', 'min:0', 'max:1'],
            'percentage' => ['required', 'numeric', 'min:1', 'max:100'],
            'regions' => ['sometimes', 'nullable']
        ];
    }
}
