<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;

class PlumberAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Session::get('plumber_logged_in')) {
            // Instead of redirecting, return 404 if not logged in
            abort(404, 'Page not found');
        }

        return $next($request);
    }
}