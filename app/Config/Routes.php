<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/login', 'Auth::login', ['as' => 'login']); // show login form
$routes->post('/attemptLogin', 'Auth::attemptLogin', ['as' => 'attemptLogin']); // process login

$routes->get('/register', 'Auth::registerForm');  // show register form
$routes->post('/register', 'Auth::register');    // process register

$routes->get('/verify', 'Auth::verify');         // show verify page
$routes->post('/verifyOtp', 'Auth::verifyOtp');  // process OTP
$routes->get('/resendOtp', 'Auth::resendOtp');   // resend OTP

$routes->get('/logout', 'Auth::logout');         // logout


// 🔹 Users routes (protected by filter)
$routes->group('users', ['filter' => 'userauth'], function($routes) {
    $routes->get('/', 'Users::index');
    $routes->get('billing', 'Users::billing');
    $routes->get('payments', 'Users::payments');
    $routes->get('pressure', 'Users::pressure');
    $routes->get('report', 'Users::report');
    $routes->get('profile', 'Users::profile');
    $routes->get('changepassword', 'Users::changePassword');
    $routes->get('editprofile', 'Users::editProfile');
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

     $routes->get('test-email', 'Admin::testEmail');
});

// You can also add more user routes here, for example:
$routes->get('about', 'Home::about'); // About page for users
