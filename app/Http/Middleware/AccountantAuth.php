<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class AccountantAuth
{
    /**
     * Ensure only authenticated accountants can access protected routes.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isGuardAuthenticated = Auth::guard('accountant')->check();
        $isAdminAuthenticated = Auth::guard('admin')->check();
        $hasAccountantSession = Session::has('accountant_id')
            && (Session::get('accountant_auth') || Session::has('accountant_name'));

        if (!$isGuardAuthenticated && !$hasAccountantSession && !$isAdminAuthenticated) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Please log in as accountant.',
                    'redirect' => route('accountant.login'),
                ], 401);
            }

            return redirect()->route('accountant.login')
                ->with('error', 'Please log in to access this page.');
        }

        return $next($request);
    }
}
