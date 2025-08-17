<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Home::index');

// ✅ Add your admin routes here
$routes->get('admin', 'Admin::index');
$routes->get('admin/layoutstatic', 'Admin::layoutStatic');
$routes->get('admin/login', 'Admin::login');
$routes->get('admin/charts', 'Admin::charts');
$routes->get('admin/404', 'Admin::page404');
$routes->get('admin/401', 'Admin::page401');
