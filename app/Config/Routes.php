<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers\Api\AuthController;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

service('auth')->routes($routes);

$routes->group('api', ['namespace' => 'App\Controllers\Api'], static function ($routes) {

    $routes->get('invalid', [AuthController::class, 'invalidRequest']);
    $routes->post('register', [AuthController::class, 'register']);
    $routes->post('login', [AuthController::class, 'login']);

});

$routes->group('api', ['namespace' => 'App\Controllers\Api', 'filter' => 'checkauth'], static function ($routes) {

    $routes->post('logout', [AuthController::class, 'logout']);
    $routes->post('show', [AuthController::class, 'userProfile']);
});
