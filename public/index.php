<?php

use Illuminate\Contracts\Http\Kernel;
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

/*
|--------------------------------------------------------------------------
| Locate The Laravel Application
|--------------------------------------------------------------------------
|
| Estructura local / estándar:  <proyecto>/public/index.php  -> '..'
| Estructura Hostinger:         <dominio>/public_html/index.php y el proyecto
|                               en <dominio>/laravel/             -> '../laravel'
|
| Solo se usan rutas relativas a este archivo, sin rutas absolutas.
|
*/

$basePath = is_file(__DIR__.'/../vendor/autoload.php')
    ? __DIR__.'/..'
    : __DIR__.'/../laravel';

if (file_exists($maintenance = $basePath.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| this application. We just need to utilize it! We'll simply require it
| into the script here so we don't need to manually load our classes.
|
*/

require $basePath.'/vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request using
| the application's HTTP kernel. Then, we will send the response back
| to this client's browser, allowing them to enjoy our application.
|
*/

$app = require_once $basePath.'/bootstrap/app.php';

// El directorio público es el de este index.php (public/ en local, public_html en
// Hostinger). Necesario para que Vite encuentre build/manifest.json.
$app->instance('path.public', __DIR__);

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
