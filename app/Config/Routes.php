<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->group('admin', static function ($routes) {
    $routes->group('', [], static function ($routes) {
        $routes->view('example-page', 'example-page');
    });
    $routes->group('', [], static function ($routes) {
        $routes->view('example-auth', 'example-auth');
    });
});
