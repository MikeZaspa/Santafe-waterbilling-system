<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    // ... other properties like $middleware

    /**
     * The application's middleware aliases.
     *
     * Aliases may be used instead of class names to conveniently assign middleware to routes and groups.
     *
     * @var array<string, class-string|string>
     */
    protected $middlewareAliases = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        // ... other default aliases
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

        // YOUR CUSTOM MIDDLEWARE SHOULD BE HERE
        'admin.auth' => \App\Http\Middleware\AdminAuth::class,
        'accountant.auth' => \App\Http\Middleware\AccountantAuth::class,
        'plumber.auth' => \App\Http\Middleware\PlumberAuth::class, // <-- ENSURE THIS LINE IS HERE
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'auth:plumber' => \Illuminate\Auth\Middleware\Authenticate::class,
    ];
}