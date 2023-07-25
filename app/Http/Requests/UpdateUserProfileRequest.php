<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->route('id') && $this->user()->role == 0) {
            return false;
        }

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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.($this->route('id') ?? $this->user()->id)],
            'role'  => ['sometimes', 'integer', 'between:0,1'],
            'email_verified_at' => ['nullable', 'integer', 'between:0,1'],
            'tfa' => ['nullable', 'integer', 'between:0,1'],
            'password'  => ['nullable', 'string', 'min:6', 'confirmed'],
            'timezone' => ['required'],
            'plan_id' => ['sometimes'],
            'plan_ends_at' => ['sometimes', 'nullable', 'date_format:Y-m-d']
        ];
    }
}
