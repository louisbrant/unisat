<?php

namespace App\Http\Middleware;

use Closure;

class LicenseMiddleware
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
        // Check if a license is present
        // if ((config('settings.license_key') === null || config('settings.license_type') === null) && $request->url() != route('admin.settings', 'license')) {
        //     return redirect()->route('admin.settings', 'license');
        // }

        return $next($request);
    }
}
