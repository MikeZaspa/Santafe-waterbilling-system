<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PlumberAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!Session::get('plumber_auth')) {
            return redirect('/plumber-login');
        }

        return $next($request);
    }
}