<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;


use Closure;

class AdminMiddleware
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
        // Pre-Middleware Action
        if (Auth::check() && Auth::user()->admin) {
            return $next($request);
        }
        // Post-Middleware Action
        return response()->json(['success' => false, 'error' => 'Unauthorized'], 401);
    }
}
