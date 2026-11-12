<?php

namespace App\Shared\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureEmailIsVerified
{
    public function handle(Request $request, Closure $next)
    {
        if (! config('app.email_verification_enabled')) {
            return $next($request);
        }

        $user = $request->user();

        if (! $user || $user->hasVerifiedEmail()) {
            return $next($request);
        }

        return redirect()->guest(route('verification.notice'));
    }
}
