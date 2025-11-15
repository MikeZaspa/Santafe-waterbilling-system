<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // This is where you can specify different redirects for different guards
        if ($request->is('admin*') || $request->is('plumber*')) {
            return route('plumber.login'); // Make sure you have a named route for your plumber login
        }

        return $request->expectsJson() ? null : route('login');
    }
}