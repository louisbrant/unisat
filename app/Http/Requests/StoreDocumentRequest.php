<?php

namespace App\Http\Requests;

use App\Rules\DocumentsLimitGateRule;
use App\Rules\ValidateBadWordsRule;
use App\Rules\WordsLimitGateRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
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
            'name' => ['required', 'string', 'min:1', 'max:128', new WordsLimitGateRule($this->user()), new DocumentsLimitGateRule($this->user()), new ValidateBadWordsRule()],
            'prompt' => ['required', 'string', 'max:2048', new ValidateBadWordsRule()],
            'creativity' => ['required', 'numeric', 'between:0,1', new ValidateBadWordsRule()],
            'variations' => ['sometimes', 'required', 'integer', 'in:' . implode(',', config('completions.variations')), new ValidateBadWordsRule()]
        ];
    }
}
