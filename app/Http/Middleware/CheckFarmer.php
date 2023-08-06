<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Contracts\Role;

class CheckFarmer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {

        $user = Auth::guard('api')->user();
        if (!$user->hasRole("farmer")) {
            return response()->json(['message' => 'Tu usuario no cuenta con rol de agricultor'], 403);
        }
        return $next($request);
    }
}
