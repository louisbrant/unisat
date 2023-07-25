<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidateBadWordsRule implements Rule
{
    /**
     * The input attribute
     *
     * @var
     */
    private $attribute;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $this->attribute = $attribute;

        $bannedWords = preg_split('/\n|\r/', config('settings.bad_words'), -1, PREG_SPLIT_NO_EMPTY);

        foreach($bannedWords as $word) {
            // Search for the word in string
            if(strpos(mb_strtolower($value), mb_strtolower($word)) !== false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('The :attribute contains a keyword that is banned.', ['attribute' => $this->attribute]);
    }
}
