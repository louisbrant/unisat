<?php

namespace App\Http\Requests;

use App\Rules\ValidateUserPasswordRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserSecurityRequest extends FormRequest
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
            'tfa' => ['sometimes', 'integer', 'between:0,1'],
            'current_password' => ['nullable', 'required_with:password', 'string', 'min:6', 'max:128', new ValidateUserPasswordRule($this->user()->password)],
            'password' => ['nullable', 'required_with:current_password', 'string', 'min:6', 'max:128', 'confirmed']
        ];
    }
}
