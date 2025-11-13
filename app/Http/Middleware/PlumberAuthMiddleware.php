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
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if plumber is authenticated
        if (!Session::has('plumber_auth') || !Session::get('plumber_auth')) {
            // Redirect to login page with error message
            return redirect('/plumber/login')->with('error', 'Please login to access the plumber dashboard.');
        }
        
        return $next($request);
    }
}