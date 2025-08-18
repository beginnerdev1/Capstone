<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Home::index');  // User home page
//user routes grouped under 'users'
$routes->group('users', function($routes) {
    $routes->get('/', 'Users::index'); // User dashboard
    $routes->get('billing', 'Users::billing');     // Water bills
    $routes->get('payments', 'Users::payments');   // Payments
    $routes->get('pressure', 'Users::pressure');   // Water pressure
    $routes->get('report', 'Users::report');       // Report a problem
    $routes->get('profile', 'Users::profile');     // Profile settings
    $routes->get('changepassword', 'Users::changePassword'); // Change password
    $routes->get('settings', 'Users::settings');   // User settings
});
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
