<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

require __DIR__.'/../SANTAFEWATERBILLINGSYSTEM/vendor/autoload.php';
$app = require_once __DIR__.'/../SANTAFEWATERBILLINGSYSTEM/bootstrap/app.php';

$kernel = $app->make(Kernel::class);
$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
