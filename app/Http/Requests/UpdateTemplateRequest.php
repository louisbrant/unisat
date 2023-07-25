<?php

namespace App\Http\Requests;

use App\Models\Template;
use App\Rules\CustomTemplatesGateRule;
use App\Rules\ValidateBadWordsRule;
use App\Rules\ValidateEmojiRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTemplateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // If the request is to edit a space as a specific user
        // And the user is not an admin
        if ($this->has('user_id') && $this->user()->role == 0) {
            return false;
        }

        // Check if the space to be edited exists under that user
        if ($this->has('user_id')) {
            Template::where([['id', '=', $this->route('id')], ['user_id', '=', $this->input('user_id')]])->firstOrFail();
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
            'name' => ['sometimes', 'min:1', 'max:32', 'unique:templates,name,'.$this->route('id').',id,user_id,'.($this->input('user_id') ?? $this->user()->id), new ValidateBadWordsRule(), new CustomTemplatesGateRule($this->user())],
            'icon' => ['sometimes', new ValidateEmojiRule(), 'min:1', 'max:4'],
            'description' => ['sometimes', 'string', 'min:1', 'max:64'],
            'prompt' => ['sometimes', 'string', 'min:1', 'max:2048'],
        ];
    }
}
