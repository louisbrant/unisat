<?php

namespace App\Http\Requests;

use App\Rules\TranscriptionsLimitGateRule;
use App\Rules\ValidateBadWordsRule;
use App\Rules\WordsLimitGateRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class StoreTranscriptionRequest extends FormRequest
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
            'name' => ['required', 'string', 'min:1', 'max:128', new WordsLimitGateRule($this->user()), new TranscriptionsLimitGateRule($this->user()), new ValidateBadWordsRule()],
            'file' => ['required', File::types(config('transcriptions.formats'))->max(config('settings.openai_transcriptions_size') * 1024)],
            'description' => ['nullable', 'string', 'max:2048', new ValidateBadWordsRule()],
            'language' => ['nullable', 'in:' . implode(',', config('completions.languages')), new ValidateBadWordsRule()]
        ];
    }
}
