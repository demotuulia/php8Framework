<?php

/** @var Bramus\Router\Router $router */

$router->mount('/api', function () use ($router) {
// Define routes here
    $router->get('/test', App\Controllers\IndexController::class . '@test');
    $router->get('/', App\Controllers\IndexController::class . '@test');


    $router->get('/admin/users', App\Controllers\AdminUsersController::class . '@index');
    $router->get('/admin/users/(\d+)', App\Controllers\AdminUsersController::class . '@show');
    $router->post('/admin/users/login', App\Controllers\AdminUsersController::class . '@login');
    $router->post('/admin/users/logout/(\d+)', App\Controllers\AdminUsersController::class . '@logout');
    $router->post('/admin/users', App\Controllers\AdminUsersController::class . '@store');
    $router->put('/admin/users', App\Controllers\AdminUsersController::class . '@update');
    $router->delete('/admin/users/(\d+)', App\Controllers\AdminUsersController::class . '@destroy');
    $router->get('/admin/dashboard', App\Controllers\AdminDashboardController::class . '@index');


    $router->get('/content/texts', App\Controllers\ContentTextsController::class . '@index');
    $router->put('/content/texts', App\Controllers\ContentTextsController::class . '@update');
    $router->post('/content/texts', App\Controllers\ContentTextsController::class . '@store');
    $router->get('/content/texts/show', App\Controllers\ContentTextsController::class . '@show');

    $router->get('/matches', App\Controllers\MatchesController::class . '@index');
    $router->post('/matches', App\Controllers\MatchesController::class . '@store');
    $router->put('/matches', App\Controllers\MatchesController::class . '@update');
    $router->get('/matches/(\d+)', App\Controllers\MatchesController::class . '@show');
    $router->delete('/matches/(\d+)', App\Controllers\MatchesController::class . '@destroy');

    $router->get('/matchesprofiles/(\d+)', App\Controllers\MatchesProfilesController::class . '@index');
    $router->post('/matchesprofiles', App\Controllers\MatchesProfilesController::class . '@store');
    $router->put('/matchesprofiles', App\Controllers\MatchesProfilesController::class . '@update');
    $router->get('/matchesprofiles/show/(\d+)', App\Controllers\MatchesProfilesController::class . '@show');
    $router->delete('/matchesprofiles/(\d+)', App\Controllers\MatchesProfilesController::class . '@destroy');

    $router->get('/matchesprofilesstatuses', App\Controllers\MatchesProfileStatusController::class . '@index');
    $router->post('/matchesprofilesstatuses', App\Controllers\MatchesProfileStatusController::class . '@store');
    $router->put('/matchesprofilesstatuses', App\Controllers\MatchesProfileStatusController::class . '@update');
    $router->get('/matchesprofilesstatuses/(\d+)', App\Controllers\MatchesProfileStatusController::class . '@show');
    $router->delete('/matchesprofilesstatuses/(\d+)', App\Controllers\MatchesProfileStatusController::class . '@destroy');

    $router->get('/matchesform', App\Controllers\MatchesFormController::class . '@index');
    $router->post('/matchesform', App\Controllers\MatchesFormController::class . '@store');
    $router->get('/matchesform/(\d+)', App\Controllers\MatchesFormController::class . '@show');
    $router->delete('/matchesform/(\d+)', App\Controllers\MatchesFormController::class . '@destroy');
    $router->put('/matchesform/(\d+)', App\Controllers\MatchesFormController::class . '@update');
    $router->post('/matchesform/(\d+)', App\Controllers\MatchesFormController::class . '@update');

    $router->get('/searchmatches', App\Controllers\SearchController::class . '@search');
    $router->get('/searchmatches/byform', App\Controllers\SearchController::class . '@searchByForm');
    $router->get('/searchmatches/(\d+)', App\Controllers\SearchController::class . '@searchProfiles');
    $router->get('/createdemo', App\Controllers\DemoController::class . '@create');

});