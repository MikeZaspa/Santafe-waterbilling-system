<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class PlumberAuth
{
    /**
     * Ensure only authenticated plumbers can access protected routes.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isGuardAuthenticated = Auth::guard('plumber')->check();
        $hasPlumberSession = Session::has('plumber_id')
            && (Session::get('plumber_logged_in') || Session::get('plumber_auth'));

        if (!$isGuardAuthenticated && !$hasPlumberSession) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Please log in as plumber.',
                    'redirect' => route('plumber.login'),
                ], 401);
            }

            return redirect()->route('plumber.login')
                ->with('error', 'Please log in to access this page.');
        }

        return $next($request);
    }
}
