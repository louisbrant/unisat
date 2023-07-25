<?php

namespace App\Http\Requests;

use App\Rules\ChatsLimitGateRule;
use App\Rules\ValidateBadWordsRule;
use App\Rules\ValidateChatOwnershipRule;
use App\Rules\WordsLimitGateRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
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
            'chat_id' => ['required', 'integer', new ValidateChatOwnershipRule($this->user()), new WordsLimitGateRule($this->user())],
            'role' => ['required', 'string', 'in:user,assistant'],
            'message' => ['required', 'string', 'min:1', 'max:1024', new ValidateBadWordsRule()]
        ];
    }
}
