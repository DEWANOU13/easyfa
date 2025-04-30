<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class EnsureSmsIsVerified
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && !Auth::user()->sms_verified_at) {
            return redirect()->route('sms.send');
        }

        return $next($request);
    }
}
