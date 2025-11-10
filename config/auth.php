<?php

return [
    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'admin' => [
            'driver' => 'session',
            'provider' => 'admins',
        ],

        'consumer' => [
            'driver' => 'session',
            'provider' => 'consumers',
        ],
        'plumber' => [
            'driver' => 'session',
            'provider' => 'plumbers',
        ],
        'accountant' => [
            'driver' => 'session',
            'provider' => 'accountants',
        ],
        
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],

        'admins' => [
            'driver' => 'eloquent',
            'model' => App\Models\Admin::class,
        ],

        'consumers' => [
            'driver' => 'eloquent',
            'model' => App\Models\ConsumerAccount::class,
        ],
         'plumbers' => [
            'driver' => 'eloquent',
            'model' => App\Models\Plumber::class,
        ],
        'accountants' => [
            'driver' => 'eloquent',
            'model' => App\Models\Accountant::class,
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],

        'admins' => [
            'provider' => 'admins',
            'table' => 'admin_password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],

        'consumers' => [
            'provider' => 'consumers',
            'table' => 'consumer_password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],

        'plumbers' => [ // Add this provider
        'driver' => 'eloquent',
        'model' => App\Models\Plumber::class,
    ],
    ],

    'password_timeout' => 10800,
];