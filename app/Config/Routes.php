<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// =====================================================
// 🧑 USER AUTH ROUTES (For regular users)
// =====================================================
$routes->get('/login', 'Auth::login', ['filter' => 'guest']);
$routes->post('/login', 'Auth::attemptLogin', ['filter' => 'guest']);

$routes->get('/register', 'Auth::registerForm', ['filter' => 'guest']);
$routes->post('/register', 'Auth::register', ['filter' => 'guest']);

$routes->get('/verify', 'Auth::verify', ['filter' => 'guest']);
$routes->post('/verifyOtp', 'Auth::verifyOtp', ['filter' => 'guest']);
$routes->get('/resendOtp', 'Auth::resendOtp', ['filter' => 'guest']);

$routes->get('/forgot-password', 'Auth::forgotPasswordForm', ['filter' => 'guest']);
$routes->post('/forgot-password', 'Auth::sendResetLink', ['filter' => 'guest']);
$routes->post('/reset-password', 'Auth::processResetPassword', ['filter' => 'guest']);

$routes->post('/logout', 'Auth::logout', ['filter' => 'userauth']);


// =====================================================
// 👥 USER DASHBOARD & PROFILE (Protected by userauth)
// =====================================================
$routes->group('users', ['filter' => 'userauth'], function ($routes) {
    // Page routes
    $routes->get('/', 'Users::index');
    $routes->get('history', 'Users::history');  // Bill History page
    $routes->get('payments', 'Users::payments');
    $routes->get('profile', 'Users::profile');
    $routes->get('edit-profile', 'Users::editProfile');

    // AJAX routes
    $routes->get('getBillingsAjax', 'Users::getBillingsAjax');
    $routes->get('getProfileInfo', 'Users::getProfileInfo');
    $routes->post('updateProfile', 'Users::updateProfile');
    $routes->get('getProfilePicture/(:any)', 'Users::getProfilePicture/$1');
    $routes->post('createCheckout', 'Users::createCheckout');

    // Payment results
    $routes->get('payment-success', 'Users::paymentSuccess');
    $routes->get('payment-failed', 'Users::paymentFailed');
});


// ======================================================
// 💳 PAYMENT WEBHOOKS
// ======================================================
$routes->post('webhook', 'WebhookController::webhook');


// =====================================================
// 🔒 ADMIN ROUTES (Controllers in App\Controllers\Admin\)
// =====================================================
$routes->group('admin', ['namespace' => 'App\Controllers\Admin'], function ($routes) {

    // Auth routes (Controller: AdminAuth)
    $routes->get('login', 'AdminAuth::adminLogin', ['filter' => 'guest']);
    $routes->post('login', 'AdminAuth::login', ['filter' => 'guest']);
    $routes->get('verify-otp', 'AdminAuth::showOtpForm', ['filter' => 'guest']);
    $routes->post('verify-otp', 'AdminAuth::verifyOtp', ['filter' => 'guest']);
    $routes->post('resend-otp', 'AdminAuth::resendOtp', ['filter' => 'guest']);

    // Protected admin routes (Controller: Admin, Billing)
    $routes->group('', ['filter' => 'adminauth'], function ($routes) {
        
        // 🔹 Admin Controller Routes
        $routes->get('/', 'Admin::index');
        $routes->get('logout', 'AdminAuth::logout');
        $routes->get('change-password', 'Admin::changePasswordView');
        $routes->post('set-password', 'AdminAuth::setPassword');
        $routes->get('registeredUsers', 'Admin::registeredUsers');
        $routes->get('announcements', 'Admin::announcements');
        $routes->get('manageAccounts', 'Admin::manageAccounts');
<<<<<<< HEAD
=======
        $routes->post('markAsPaid/(:num)', 'Admin::markAsPaid/$1');
>>>>>>> 21c635f71d13b33fa8a1eba396defabb00f89499
        $routes->get('userInfo', 'Admin::getUserInfo');
        $routes->get('reports', 'Admin::reports');
        $routes->get('charts', 'Admin::charts');
        $routes->get('tables', 'Admin::tables');
        $routes->get('404', 'Admin::page404');
        $routes->get('401', 'Admin::page401');
        $routes->get('500', 'Admin::page500');

        // ✅ Billing Controller Routes
        $routes->get('view/(:num)', 'Billing::view/$1');
        $routes->post('create', 'Billing::create');
        $routes->get('paidBills', 'Billing::paidBills');
        $routes->get('(:segment)', 'Billing::show/$1');
        $routes->post('update-status/(:num)', 'Billing::updateStatus/$1');
        $routes->get('delete/(:num)', 'Billing::delete/$1');
    });
});


// ======================================================
// 👑 SUPER ADMIN ROUTES
// ======================================================
$routes->group('superadmin', ['namespace' => 'App\Controllers\SuperAdmin'], function ($routes) {
    $routes->get('login', 'SuperAdminAuth::loginForm', ['filter' => 'superadminguest']);
    $routes->post('login', 'SuperAdminAuth::login', ['filter' => 'superadminguest']);
    $routes->get('check-code', 'SuperAdminAuth::checkCodeForm', ['filter' => 'superadminguest']);
    $routes->post('check-code', 'SuperAdminAuth::checkCode', ['filter' => 'superadminguest']);
    
    $routes->group('', ['filter' => 'superadminauth'], function ($routes) {
        $routes->get('/', 'SuperAdmin::index');
        $routes->get('dashboard', 'SuperAdmin::dashboard');
        $routes->get('users', 'SuperAdmin::users');
        $routes->get('getUsers', 'SuperAdmin::getUsers');
        $routes->post('createUser', 'SuperAdmin::createUser');
        $routes->get('logout', 'SuperAdminAuth::logout');
    });
});
