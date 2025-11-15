<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

// CHANGE THE CLASS NAME HERE
class PlumberAuthMiddleware 
{
    public function handle(Request $request, Closure $next)
    {
        if (!Session::get('plumber_logged_in')) {
            return redirect('/plumber-login')->with('error', 'Please login to access this page.');
        }
        return $next($request);
    }
}