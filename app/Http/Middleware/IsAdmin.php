<?php

namespace App\Http\Middleware;

use App\Helpers\ResponseAPI;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::user()->is_admin == 1)
        {
            return $next($request);
        }
            return ResponseAPI::error('forbidden', 403);
    }
}
