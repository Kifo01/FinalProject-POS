<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->setAutoRoute(false);
$routes->get('/', 'Auth::home');
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attempt');
$routes->post('logout', 'Auth::logout', ['filter' => 'auth']);
$routes->get('health', 'Health::index');
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('terminal', 'Sales::terminal');
    $routes->post('sales', 'Sales::record');
});
$routes->group('admin', ['filter' => 'admin'], static function ($routes) {
    $routes->get('/', 'Dashboard::index');
    foreach (['products'=>'Products', 'customers'=>'Customers', 'staff'=>'Staff'] as $path => $controller) {
        $routes->get($path, "$controller::index");
        $routes->get("$path/new", "$controller::form");
        $routes->post($path, "$controller::save");
        $routes->get("$path/(:num)/edit", "$controller::form/$1");
        $routes->post("$path/(:num)", "$controller::save/$1");
        $routes->post("$path/(:num)/archive", "$controller::archive/$1");
    }
    $routes->get('sales', 'Sales::history');
});
