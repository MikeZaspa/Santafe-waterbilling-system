<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class PlumberAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Debug: Log session data
        Log::info('PlumberAuth Middleware - Session all:', Session::all());
        Log::info('PlumberAuth Middleware - plumber_auth value:', ['plumber_auth' => Session::get('plumber_auth')]);
        
        // Check if the plumber is authenticated by verifying the session
        if (!Session::has('plumber_auth') || !Session::get('plumber_auth')) {
            Log::warning('PlumberAuth Middleware - Access denied');
            abort(403, 'Unauthorized action. Please log in to access the Plumber Portal.');
        }

        Log::info('PlumberAuth Middleware - Access granted');
        return $next($request);
    }
}