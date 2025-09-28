<?php

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/../SANTAFEWATERBILLINGSYSTEM/storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../SANTAFEWATERBILLINGSYSTEM/vendor/autoload.php';

$app = require_once __DIR__.'/../SANTAFEWATERBILLINGSYSTEM/bootstrap/app.php';

/** @var Kernel $kernel */
$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
