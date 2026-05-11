<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->setAutoRoute(false);

$routes->get('/', 'AuthController::index');
$routes->get('login', 'AuthController::index');
$routes->post('login', 'AuthController::attempt');
$routes->post('logout', 'AuthController::logout', ['filter' => 'auth']);

$routes->get('dashboard', 'DashboardController::index', ['filter' => 'auth']);

$routes->group('profile', ['filter' => 'auth'], static function (RouteCollection $routes): void {
    $routes->get('/', 'ProfileController::index');
    $routes->post('update', 'ProfileController::update');
});

$routes->group('sales', ['filter' => 'auth'], static function (RouteCollection $routes): void {
    $routes->get('/', 'SalesController::index');
    $routes->get('create', 'SalesController::create');
    $routes->get('template', 'SalesController::template');
    $routes->post('store', 'SalesController::store');
});

$routes->group('admin', ['filter' => 'role:admin'], static function (RouteCollection $routes): void {
    $routes->get('users', 'UsersController::index');
    $routes->get('users/create', 'UsersController::create');
    $routes->post('users/store', 'UsersController::store');
    $routes->get('users/edit/(:num)', 'UsersController::edit/$1');
    $routes->post('users/update/(:num)', 'UsersController::update/$1');
    $routes->post('users/delete/(:num)', 'UsersController::delete/$1');

    $routes->get('products', 'ProductsController::index');
    $routes->get('products/create', 'ProductsController::create');
    $routes->post('products/store', 'ProductsController::store');
    $routes->get('products/edit/(:num)', 'ProductsController::edit/$1');
    $routes->post('products/update/(:num)', 'ProductsController::update/$1');
    $routes->post('products/delete/(:num)', 'ProductsController::delete/$1');

    $routes->get('prices', 'PricesController::index');
    $routes->post('prices/update/(:num)', 'PricesController::update/$1');

    $routes->get('templates', 'QuickTemplatesController::index');
    $routes->get('templates/create', 'QuickTemplatesController::create');
    $routes->post('templates/store', 'QuickTemplatesController::store');
    $routes->get('templates/edit/(:num)', 'QuickTemplatesController::edit/$1');
    $routes->post('templates/update/(:num)', 'QuickTemplatesController::update/$1');
    $routes->post('templates/delete/(:num)', 'QuickTemplatesController::delete/$1');

    $routes->get('sale-limit', 'SaleLimitController::index');
    $routes->post('sale-limit/update', 'SaleLimitController::update');

    $routes->get('reports', 'ReportsController::index');
    $routes->get('charts', 'ChartsController::index');
});
