<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

class EncryptCookies extends Middleware
{
    /**
     * The names of the cookies that should not be encrypted.
     *
     * @var array
     */
    protected $except = [
        'dark_mode',
        'cookie_law'
    ];

    /**
     * Determine whether encryption has been disabled for the given cookie.
     *
     * @param  string $name
     * @return bool
     */
    public function isDisabled($name)
    {
        // Do not encrypt the announcement_id cookies
        if (preg_match('/announcement_\w+/', $name)) {
            return true;
        }

        return in_array($name, $this->except);
    }
}
