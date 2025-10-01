<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Check If The Application Is Under Maintenance
|--------------------------------------------------------------------------
|
| If the application is in maintenance / demo mode via the "down" command
| we will load this file so that any pre-rendered content can be shown
| instead of starting the framework, which could cause an exception.
|
*/

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader
| for this application. We just need to utilize it! We'll simply require
| it into the script here so we don’t need to manually load our classes.
|
*/

require __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Bootstrap The Application
|--------------------------------------------------------------------------
|
| Here we will bootstrap the Laravel application and run it, capturing
| the incoming request and then sending the associated response back
| to the client’s browser for them to enjoy a great experience.
|
*/

/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->withMiddleware(function (Middleware $middleware) {
    //
})->withExceptions(function (Exceptions $exceptions) {
    //
})->handleRequest(Request::capture());
