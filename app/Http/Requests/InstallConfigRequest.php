<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InstallConfigRequest extends FormRequest
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
            'database_hostname' => ['required', 'string', 'max:50'],
            'database_port' => ['required', 'numeric'],
            'database_name' => ['required', 'string', 'max:50'],
            'database_username' => ['required', 'string', 'max:50'],
            'database_password' => ['nullable', 'string', 'max:50'],
        ];
    }
}
