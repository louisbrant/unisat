<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePageRequest extends FormRequest
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
            'name' => ['required', 'max:64'],
            'slug' => ['required', 'max:64', 'alpha_dash', 'unique:pages,slug,'.$this->route('id')],
            'visibility' => ['required', 'integer', 'between:0,1'],
            'content' => ['required']
        ];
    }
}
