<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
error_reporting ( E_ALL & ~E_NOTICE & ~E_DEPRECATED);


// Autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Load Config
$config = require_once __DIR__ . '/../config/config.php';

// Services
require_once  __DIR__ . '/../config/services.php';

// Router
$router = require_once  __DIR__ . '/../routes/router.php';

// Run application through router:
try {
    $router->run();
} catch (\App\Plugins\Http\ApiException $e) {
    // Send the API exception to the client:
    $e->send();
} catch (Exception $e) {
    // For debugging purposes, throw the initial exception:
    throw $e;
}
