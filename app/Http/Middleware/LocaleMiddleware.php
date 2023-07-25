<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class LocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            // If the user is authed
            if (Auth::check()) {
                // If the user's preferred local exists
                if(array_key_exists($request->user()->locale, config('app.locales'))) {
                    app()->setLocale($request->user()->locale);
                } else {
                    app()->setLocale(config('app.locale'));
                }
            }
            // If a locale has already been saved
            elseif($request->hasCookie('locale')) {
                // If the cookie stored locale exists
                if(array_key_exists($request->cookie('locale'), config('app.locales'))) {
                    app()->setLocale($request->cookie('locale'));
                } else {
                    app()->setLocale(config('app.locale'));
                }
            }
            // If the browser has a preferred locale header set
            elseif($request->server('HTTP_ACCEPT_LANGUAGE')) {
                $locale = explode('-', $request->server('HTTP_ACCEPT_LANGUAGE'))[0] ?? null;

                // If the browser's locale exists
                if(array_key_exists($locale, config('app.locales'))) {
                    app()->setLocale($locale);
                } else {
                    app()->setLocale(config('app.locale'));
                }
            }
            // Set the locale to the default one
            else {
                app()->setLocale(config('app.locale'));
            }
        } catch (\Exception $e) {}

        return $next($request);
    }
}
