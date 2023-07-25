<?php

namespace App\Http\Requests;

use App\Rules\ChatsLimitGateRule;
use App\Rules\WordsLimitGateRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreChatRequest extends FormRequest
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
            'name' => ['required', 'max:32', new WordsLimitGateRule($this->user()), new ChatsLimitGateRule($this->user())],
            'behavior' => ['nullable', 'string', 'max:128'],
            'favorite' => ['sometimes', 'required', 'integer', 'between:0,1'],
        ];
    }
}
