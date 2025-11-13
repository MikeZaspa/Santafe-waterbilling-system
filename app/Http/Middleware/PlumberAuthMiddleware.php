<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PlumberAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if the plumber is authenticated by verifying the session
        if (!Session::has('plumber_auth') || !Session::get('plumber_auth')) {
            // If not authenticated, abort the request with a 403 Forbidden error.
            // This is a clear, secure response that stops all further processing.
            abort(403, 'Unauthorized action. Please log in to access the Plumber Portal.');
        }

        // If authenticated, allow the request to proceed to the controller
        return $next($request);
    }
}