<?php

namespace App\Http\Middleware;

use Closure;
use Laravel\Passport\Exceptions\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class Authenticate
{
    public function handle($request, Closure $next)
    {
        if (Auth::guard('api')->check()) {
            return $next($request);
        }

        throw new AuthenticationException('Unauthenticated.');
    }
}
