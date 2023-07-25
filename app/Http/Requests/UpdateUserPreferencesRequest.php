<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserPreferencesRequest extends FormRequest
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
            'default_language' => ['required', 'string', 'max:64'],
            'default_creativity' => ['required', 'numeric', 'between:0,1'],
            'default_variations' => ['required', 'integer', 'in:' . implode(',', config('completions.variations'))]
        ];
    }
}
