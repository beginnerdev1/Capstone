<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

    $routes->get('/', 'Home::index');
    $routes->get('/login', 'Auth::login');           // show login form
    $routes->post('/login', 'Auth::attemptLogin');   // process login
    $routes->get('/logout', 'Auth::logout');         // logout
    
    $routes->group('users', function($routes) {
    $routes->get('/', 'Users::index');
    $routes->get('billing', 'Users::billing');
    $routes->get('payments', 'Users::payments');
    $routes->get('pressure', 'Users::pressure');
    $routes->get('report', 'Users::report');
    $routes->get('profile', 'Users::profile');
    $routes->get('changepassword', 'Users::changePassword');
    $routes->get('editprofile', 'Users::editprofile');
});


// Admin routes grouped under 'admin'
$routes->group('admin', function($routes) {
    $routes->get('/'                , 'Admin::index'            );           // Admin dashboard
    $routes->get('layoutstatic'     , 'Admin::layoutStatic'     );
    $routes->get('login'            , 'Admin::login'            );
    $routes->get('charts'           , 'Admin::charts'           );
    $routes->get('tables'           , 'Admin::tables'           );
    $routes->get('404'              , 'Admin::page404'          );
    $routes->get('401'              , 'Admin::page401'          );
    $routes->get('500'              , 'Admin::page500'          );
    $routes->get('registeredUsers'  , 'Admin::registeredUsers'  );          // Registered users
    $routes->get('billings'         , 'Admin::billings'         );          // Billings
    $routes->get('paidBills'        , 'Admin::paidBills'        );          // Paid bills
    $routes->get('reports'          , 'Admin::reports'          );          // User reports dashboard
});

// You can also add more user routes here, for example:
$routes->get('about', 'Home::about'); // About page for users
