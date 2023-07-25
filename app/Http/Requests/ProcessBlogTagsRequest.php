<?php

namespace App\Http\Requests;

use App\Rules\DocumentsLimitGateRule;
use App\Rules\TemplatesGateRule;
use App\Rules\ValidateBadWordsRule;
use App\Rules\ValidateTemplateOwnershipRule;
use App\Rules\WordsLimitGateRule;
use Illuminate\Foundation\Http\FormRequest;

class ProcessBlogTagsRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'min:1', 'max:128', new WordsLimitGateRule($this->user()), new DocumentsLimitGateRule($this->user()), new TemplatesGateRule($this->user()), new ValidateBadWordsRule()],
            'title' => ['required', 'string', 'max:256', new ValidateBadWordsRule()],
            'content' => ['required', 'string', 'max:2048', new ValidateBadWordsRule()],
            'template_id' => ['required', new ValidateTemplateOwnershipRule($this->user()), new ValidateBadWordsRule()],
            'creativity' => ['required', 'numeric', 'between:0,1', new ValidateBadWordsRule()],
            'variations' => ['required', 'integer', 'in:' . implode(',', config('completions.variations')), new ValidateBadWordsRule()],
            'language' => ['required', 'in:' . implode(',', config('completions.languages')), new ValidateBadWordsRule()]
        ];
    }
}
