<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Home::index');  // User home page

// Admin routes grouped under 'admin'
$routes->group('admin', function($routes) {
    $routes->get('/', 'Admin::index');           // Admin dashboard
    $routes->get('layoutstatic', 'Admin::layoutStatic');
    $routes->get('login', 'Admin::login');
    $routes->get('charts', 'Admin::charts');
    $routes->get('404', 'Admin::page404');
    $routes->get('401', 'Admin::page401');
});

// You can also add more user routes here, for example:
$routes->get('about', 'Home::about'); // About page for users
