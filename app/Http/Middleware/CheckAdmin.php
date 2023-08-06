<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Contracts\Role;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {

        $user = Auth::guard('api')->user();
        if (!$user->hasRole("admin")) {
            return response()->json(['message' => 'Tu usuario no cuenta con rol de administrador'], 403);
        }
        return $next($request);
    }
}
