<?php

namespace App\Http\Requests;

use App\Rules\CustomTemplatesGateRule;
use App\Rules\ValidateBadWordsRule;
use App\Rules\ValidateEmojiRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTemplateRequest extends FormRequest
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
            'name' => ['required', 'max:32', 'unique:templates,name,null,id,user_id,'.$this->user()->id, new ValidateBadWordsRule(), new CustomTemplatesGateRule($this->user())],
            'icon' => ['required', new ValidateEmojiRule(), 'min:1', 'max:4'],
            'description' => ['required', 'string', 'min:1', 'max:64'],
            'prompt' => ['required', 'string', 'min:1', 'max:2048']
        ];
    }
}
