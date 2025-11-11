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



// ======================================================
// 👑 USER ROUTES
// ======================================================
$routes->group('users', ['filter' => 'userauth'], function ($routes) {

    // Routes accessible even if profile is incomplete
    $routes->get('/', 'Users::index'); // index is now accessible
    $routes->get('profile', 'Users::profile');
    $routes->get('edit-profile', 'Users::editProfile');
    $routes->get('getProfileInfo', 'Users::getProfileInfo');
    $routes->get('getAccountStatus', 'Users::getAccountStatus');
    $routes->get('getProfilePicture/(:any)', 'Users::getProfilePicture/$1');

    $routes->post('updateProfile', 'Users::updateProfile');
    $routes->post('changeEmail', 'Users::changeEmail');
    $routes->post('changePassword', 'Users::changePassword');
    $routes->post('uploadProof', 'Users::uploadProof');
    $routes->post('checkReference', 'Users::checkReference');

    // Routes that require profile completion and/or admin approval
    $routes->group('', ['filter' => 'profilecomplete'], function ($routes) {
        $routes->get('history', 'Users::history');
        $routes->get('payments', 'Users::payments');
        $routes->get('paymentProof', 'Users::paymentProof');
        $routes->get('getBillingsAjax', 'Users::getBillingsAjax');
        $routes->post('createCheckout', 'Users::createCheckout');
        $routes->get('payment-success', 'Users::paymentSuccess');
        $routes->get('payment-failed', 'Users::paymentFailed');
    });

});



// ======================================================
// 💳 PAYMENT WEBHOOKS
// ======================================================
$routes->post('webhook', 'WebhookController::webhook');


// =====================================================
// 🔒 ADMIN ROUTES (Controllers in App\Controllers\Admin\)
// =====================================================
$routes->group('admin', ['namespace' => 'App\Controllers\Admin'], function ($routes) {

    // 🧩 AUTH
    $routes->get('login', 'AdminAuth::adminLogin', ['filter' => 'guest']);
    $routes->post('login', 'AdminAuth::login', ['filter' => 'guest']);
    $routes->get('verify-otp', 'AdminAuth::showOtpForm', ['filter' => 'guest']);
    $routes->post('verify-otp', 'AdminAuth::verifyOtp', ['filter' => 'guest']);
    $routes->post('resend-otp', 'AdminAuth::resendOtp', ['filter' => 'guest']);

    // 🧠 CHANGE PASSWORD (accessible even when forcePasswordChange = true)
    $routes->get('change-password', 'AdminAuth::changePassword', ['filter' => 'adminauth']);
    $routes->post('setPassword', 'AdminAuth::setPassword', ['filter' => 'adminauth']);

    // ✅ Protected admin routes (requires both adminauth + no force password change)
    $routes->group('', ['filter' => ['adminauth', 'forcepasswordchange']], function ($routes) {
        $routes->get('/', 'Admin::index');
        $routes->get('dashboard', 'Admin::dashboard');
        $routes->get('dashboard-content', 'Admin::content');

        $routes->get('logout', 'AdminAuth::logout');
        $routes->get('registeredUsers', 'Admin::registeredUsers');
        $routes->get('announcements', 'Admin::announcements');
        $routes->get('manageAccounts', 'Admin::manageAccounts');
        $routes->get('userInfo', 'Admin::getUserInfo');
        $routes->get('pendingAccounts', 'Admin::pendingAccounts');
$routes->get('getUser/(:num)', 'Admin::getUser/$1');//get user info for verify user
$routes->get('gcash-settings', 'Admin::gcashsettings');// GCash Settings Page
$routes->post('saveGcashSettings', 'Admin::saveGcashSettings'); // Save GCash Settings
$routes->get('transaction-records', 'Admin::transactionRecords');// Transaction Records Page
$routes->get('edit-profile', 'Admin::editProfile'); // Edit Profile Page
$routes->get('reports', 'Admin::reports'); // Reports Page




        $routes->post('approve/(:num)', 'Admin::approve/$1');
        $routes->post('reject/(:num)', 'Admin::reject/$1');;
        $routes->get('activateUser/(:num)', 'Admin::activateUser/$1');
        $routes->get('deactivateUser/(:num)', 'Admin::deactivateUser/$1');
        $routes->get('suspendUser/(:num)', 'Admin::suspendUser/$1');
        $routes->get('reports', 'Reports::index');
        $routes->get('charts', 'Admin::charts');
        $routes->get('tables', 'Admin::tables');
        $routes->get('404', 'Admin::page404');
        $routes->get('401', 'Admin::page401');
        $routes->get('500', 'Admin::page500');
        $routes->get('profile', 'Admin::profile');
        $routes->post('updateProfile', 'Admin::updateProfile');

        // View single user details
        $routes->get('viewUser/(:num)', 'Admin::viewUser/$1');

        // Deactivate / activate user
        $routes->get('toggleUserStatus/(:num)', 'Admin::toggleUserStatus/$1');

        // Billing Controller Routes
        $routes->get('view/(:num)', 'Billing::view/$1');
        $routes->post('addBill/(:num)', 'Billing::addBill/$1');
        $routes->get('paidBills', 'Billing::paidBills');
        $routes->get('(:segment)', 'Billing::show/$1');
        $routes->post('editBill/(:num)', 'Billing::editBill/$1');
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
