<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Plugins;
use App\Plugins\Di\Factory;

$di = Factory::getDi();

$di->setShared('router', function () {
    return new Bramus\Router\Router();
});

