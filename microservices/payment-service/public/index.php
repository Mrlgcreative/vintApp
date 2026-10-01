<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists($main = __DIR__.'/../storage/framework/maintenance.php')) {
    require $main;
}

require __DIR__.'/../vendor/autoload.php';

(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());
