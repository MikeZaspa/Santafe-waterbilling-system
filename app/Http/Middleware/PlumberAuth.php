<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PlumberAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!Session::get('plumber_logged_in')) {
            return redirect('/plumber-login')->with('error', 'Please login first.');
        }
        return $next($request);
    }
}