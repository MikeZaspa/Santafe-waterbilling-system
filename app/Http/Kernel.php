<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    // ... other properties

    /**
     * The application's middleware aliases.
     *
     * Aliases may be used instead of class names to conveniently assign middleware to routes and groups.
     *
     * @var array<string, class-string|string>
     */
    protected $middlewareAliases = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        // ... other aliases
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

        // --- ADD THIS LINE (THE CORRECT WAY) ---
        'plumber.auth' => \App\Http\Middleware\PlumberAuth::class,

    ];
}