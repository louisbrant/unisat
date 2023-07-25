<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocaleController extends Controller
{
    /**
     * Update the Locale preference.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateLocale(Request $request)
    {
        // If the locale exists
        if (array_key_exists($request->input('locale'), config('app.locales'))) {
            // Update the user's locale
            if(Auth::check()) {
                $request->user()->locale = $request->input('locale');
                $request->user()->save();
            }
        }

        return redirect()->back()->withCookie('locale', $request->input('locale'), (60 * 24 * 365 * 10));
    }
}
