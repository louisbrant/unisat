<?php

namespace App\Http\Middleware;

use Closure;

class VerifyPaymentEnabledMiddleware
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
        // Check if any payment processor is enabled
        if (!paymentProcessors()) {
            return redirect()->route('home');
        }

        return $next($request);
    }
}
