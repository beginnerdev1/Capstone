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

$routes->get('/forgotPasswordForm', 'Auth::forgotPasswordForm', ['filter' => 'guest']);
$routes->post('/forgot-password', 'Auth::sendResetLink', ['filter' => 'guest']);
$routes->post('/reset-password', 'Auth::processResetPassword', ['filter' => 'guest']);

// Friendly forgot-password GET (use this)
$routes->get('/forgot-password', 'Auth::forgotPasswordForm', ['filter' => 'guest']);
// Keep old alias for backward compatibility
$routes->get('/forgotPasswordForm', 'Auth::forgotPasswordForm', ['filter' => 'guest']);

// Reset link opens form (GET /reset?email=...&token=...)
$routes->get('/reset', 'Auth::resetForm', ['filter' => 'guest']);

$routes->post('/forgot-password', 'Auth::sendResetLink', ['filter' => 'guest']);
$routes->post('/reset-password', 'Auth::processResetPassword', ['filter' => 'guest']);

$routes->post('/logout', 'Auth::logout', ['filter' => 'userauth']);



// ======================================================
// 👑 USER ROUTES
// ======================================================
$routes->group('users', ['filter' => 'userauth'], function ($routes) {

    // User chat
    $routes->get('chat', 'Chat::index');
    $routes->get('chat/getMessages', 'Chat::getMessages');
    $routes->post('chat/postMessage', 'Chat::postMessage');


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
        $routes->get('manualTransaction', 'Users::ManualTransaction');
        $routes->get('getBillingsAjax', 'Users::getBillingsAjax');
        $routes->post('createCheckout', 'Users::createCheckout');
        $routes->get('payment-success', 'Users::paymentSuccess');
        $routes->get('payment-failed', 'Users::paymentFailed');
          $routes->get('payment-cancel', 'Users::paymentCancel');          // Handle payment cancellation
    $routes->get('cleanupPayments', 'Users::cleanupPayments');       // Manual cleanup of expired payments
$routes->get('getGcashSettings', 'Users::getGcashSettings'); // Fetch GCash settings for payment page
$routes->get('getBillDetails', 'Users::getBillDetails'); // Fetch bill details for payment confirmation
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
    $routes->get('forgot-password', 'AdminAuth::forgotPasswordForm', ['filter' => 'guest']);
    $routes->post('forgot-password', 'AdminAuth::sendResetLink', ['filter' => 'guest']);
    $routes->get('verify-otp', 'AdminAuth::showOtpForm', ['filter' => 'guest']);
    $routes->post('verify-otp', 'AdminAuth::verifyOtp', ['filter' => 'guest']);
    $routes->post('resend-otp', 'AdminAuth::resendOtp', ['filter' => 'guest']);

    // 🧠 CHANGE PASSWORD (accessible even when forcePasswordChange = true)
    $routes->get('change-password', 'AdminAuth::changePassword', ['filter' => 'adminauth']);
    $routes->post('setPassword', 'AdminAuth::setPassword', ['filter' => 'adminauth']);

    // ✅ Protected admin routes (requires both adminauth + no force password change)
    $routes->group('', ['filter' => ['adminauth', 'forcepasswordchange']], function ($routes) {
        $routes->get('/', 'Admin::index');
        $routes->get('dashboard', 'Admin::index');
        $routes->get('dashboard-content', 'Admin::content');
        $routes->get('content', 'Admin::content'); // Alternative route for AJAX
        $routes->get('api/dashboard-stats', 'Admin::getDashboardStats'); // Optimized JSON API endpoint
        $routes->get('logout', 'AdminAuth::logout');
        // Allow POST logout (form submission) in addition to GET
        $routes->post('logout', 'AdminAuth::logout');
        $routes->get('registeredUsers', 'Admin::registeredUsers');
        $routes->get('inactiveUsers', 'Admin::inactiveUsers');
        $routes->get('getInactiveUsers', 'Admin::getInactiveUsers');
        $routes->get('archivedBills/(:num)', 'Admin::getArchivedBills/$1');
        $routes->post('reactivateUser/(:num)', 'Admin::reactivateUser/$1');
        $routes->get('getUserDetails/(:num)', 'Admin::getUserDetails/$1'); // Fetch user details for admin view
        $routes->get('announcements', 'Admin::announcements');
        $routes->get('manageAccounts', 'Admin::manageAccounts');
        $routes->get('userInfo', 'Admin::getUserInfo');
        $routes->get('pendingAccounts', 'Admin::pendingAccounts');
$routes->get('getUser/(:num)', 'Admin::getUser/$1');//get user info for verify user
$routes->get('filterUsers', 'Admin::filterUsers'); //filter users in User Management (registeredUsers.php) by name/purok
$routes->post('addUser', 'Admin::addUser'); // Add new user account (AJAX)
$routes->get('gcash-settings', 'Admin::gcashsettings');// GCash Settings Page
$routes->post('saveGcashSettings', 'Admin::saveGcashSettings'); // Save GCash Settings
$routes->get('transactionRecords', 'Admin::transactionRecords');// Transaction Records Page
$routes->get('edit-profile', 'Admin::editProfile'); // Edit Profile Page
$routes->get('getProfileInfo', 'Admin::getProfileInfo'); // Get Admin Profile Info (AJAX)
$routes->post('updateProfile', 'Admin::updateProfile'); // Update Admin Profile (AJAX)
$routes->get('reports', 'Admin::reports'); // Reports Page
$routes->get('monthlyPayments', 'Admin::monthlyPayments'); // Monthly Payments Page
$routes->get('getPaymentsData', 'Admin::getPaymentsData');// Get Payments Data for Monthly Payments Page
$routes->post('confirmGCashPayment', 'Admin::confirmGCashPayment');// Confirm GCash Payment
$routes->get('exportPayments', 'Admin::exportPayments');// Export Payments Data
$routes->get('exportReports', 'Admin::exportReports');// Export Reports (CSV/Excel)
    $routes->get('exportLogs', 'Admin::exportLogs'); // Export activity logs (admin-level)
$routes->get('getUsersByPurok/(:any)', 'Admin::getUsersByPurok/$1'); // Get Users by Purok for Counter Payment
$routes->get('getUsersByPurokForManualBilling/(:any)', 'Admin::getUsersByPurokForManualBilling/$1'); // Get Users by Purok for Manual Billing
$routes->get('getPendingBillings/(:num)', 'Admin::getPendingBillings/$1');// Get Pending Billings for Counter Payment
$routes->post('addCounterPayment', 'Admin::addCounterPayment'); // Add Counter Payment
$routes->get('billingManagement', 'Admin::billingManagement'); // Billing Management Page
$routes->post('synchronizeBillings', 'Admin::synchronizeBillings'); // Synchronize Billings
$routes->get('getAllBillings', 'Admin::getAllBillings'); // Get All Billings (AJAX for Billing Management Page)
$routes->get('getBillingStatistics', 'Admin::getBillingStatistics'); // Get Billing Statistics
$routes->post('createManualBilling', 'Admin::createManualBilling'); // Create Manual Billing
$routes->post('rejectGCashPayment', 'Admin::rejectGCashPayment'); // Reject GCash Payment   
$routes->get('failedTransactions', 'Admin::failedTransactions'); // Failed Transactions Page
$routes->get('getFailedPaymentsData', 'Admin::getFailedPaymentsData');// Get Failed Payments Data for Failed Transactions Page
$routes->get('overduePayments', 'Admin::overduePayments'); // Overdue Payments Page
$routes->get('getOverduePaymentsData', 'Admin::getOverduePaymentsData');// Get Overdue Payments Data for Overdue Payments Page


        $routes->post('approve/(:num)', 'Admin::approve/$1');
        $routes->post('reject/(:num)', 'Admin::reject/$1');
        $routes->get('activateUser/(:num)', 'Admin::activateUser/$1');
        $routes->get('deactivateUser/(:num)', 'Admin::deactivateUser/$1');
        $routes->post('deactivateUser/(:num)', 'Admin::deactivateUser/$1');
        $routes->get('suspendUser/(:num)', 'Admin::suspendUser/$1');
        // Removed duplicate/stale routes to ensure correct controller + data
        // $routes->get('reports', 'Reports::index');
        // $routes->get('charts', 'Admin::charts');
        // $routes->get('tables', 'Admin::tables');
        $routes->get('404', 'Admin::page404');
        $routes->get('401', 'Admin::page401');
        $routes->get('500', 'Admin::page500');
        $routes->get('profile', 'Admin::profile');
        // Activity logs moved to superadmin area; admin endpoints removed
        // Admin chat
        $routes->get('chat', 'Chat::index');
        $routes->get('chat/getAdmins', 'Chat::getAdmins');
        $routes->get('chat/getMessages', 'Chat::getMessages');
        $routes->get('chat/getConversations', 'Chat::getConversations');
        $routes->get('chat/getBroadcasts', 'Chat::getBroadcasts');
        $routes->get('chat/getMessages/(:num)', 'Chat::getMessagesFor/$1');
        $routes->post('chat/markRead/(:num)', 'Chat::markRead/$1');
        $routes->post('chat/postMessage', 'Chat::postMessage');
        // Admin-to-admin internal messaging
        $routes->get('chat/getMessagesForAdmin/(:num)', 'Chat::getMessagesForAdmin/$1');
        $routes->post('chat/postAdminMessage', 'Chat::postAdminMessage');
        $routes->get('chat/unreadCount', 'Chat::unreadCount');
        $routes->post('chat/importJson', 'Chat::importJson');
        $routes->post('updateProfile', 'Admin::updateProfile');
        // Secure password change with OTP
        $routes->post('requestPasswordOtp', 'Admin::requestPasswordOtp');
        $routes->post('changePassword', 'Admin::changePassword');

       /*  // View single user details
        $routes->get('viewUser/(:num)', 'Admin::viewUser/$1');

        // Deactivate / activate user
        $routes->get('toggleUserStatus/(:num)', 'Admin::toggleUserStatus/$1'); */

        // Billing Controller Routes
        $routes->get('view/(:num)', 'Billing::view/$1');
        $routes->post('addBill/(:num)', 'Billing::addBill/$1');
        $routes->get('paidBills', 'Billing::paidBills');
        // Suspended users & overdue bills (placed before billing catch-all to avoid route collision)
        $routes->get('suspendedUsers', 'Admin::suspendedUsers');
        $routes->get('getSuspendedUsers', 'Admin::getSuspendedUsers');
        $routes->post('markBillPaid/(:num)', 'Admin::markBillPaid/$1');
        $routes->get('(:segment)', 'Billing::show/$1');
        $routes->post('editBill/(:num)', 'Billing::editBill/$1');
       $routes->post('update-status/(:num)', 'Billing::updateStatus/$1');
        $routes->get('delete/(:num)', 'Billing::delete/$1');
    });
});


// ======================================================
// 👑 SUPER ADMIN ROUTES
// ======================================================
$routes->group('superadmin', ['namespace' => 'App\Controllers'], function ($routes) {
    $routes->get('login', 'SuperAdminAuth::loginForm', ['filter' => 'superadminguest']);
    $routes->post('login', 'SuperAdminAuth::login', ['filter' => 'superadminguest']);
    // Change password for superadmin (allow even when force_password_change is set)
    $routes->get('change-password', 'SuperAdminAuth::changePassword', ['filter' => 'superadminauth']);
    $routes->post('setPassword', 'SuperAdminAuth::setPassword', ['filter' => 'superadminauth']);
    $routes->get('check-code', 'SuperAdminAuth::checkCodeForm', ['filter' => 'superadminguest']);
    $routes->post('check-code', 'SuperAdminAuth::checkCode', ['filter' => 'superadminguest']);
    
    $routes->group('', ['filter' => ['superadminauth','forcepasswordchange']], function ($routes) {
        $routes->get('/', 'SuperAdmin::index');
        $routes->get('dashboard', 'SuperAdmin::index');
        $routes->get('dashboard-content', 'SuperAdmin::content');
        $routes->get('content', 'SuperAdmin::content');
        $routes->get('dashboardMetrics', 'SuperAdmin::dashboardMetrics');
        $routes->get('systemInfo', 'SuperAdmin::systemInfo');
        $routes->get('downloadBackup', 'SuperAdmin::downloadBackup');
        $routes->get('users', 'SuperAdmin::users');
        $routes->get('settings', 'SuperAdmin::settings');
        $routes->get('backup', 'SuperAdmin::backup');
        $routes->post('backup', 'SuperAdmin::backup');
        $routes->get('logs', 'SuperAdmin::logs');
        // Reference pages for admin/debugging
        $routes->get('logs-admin-ref', 'SuperAdmin::logsAdminRef');
        $routes->get('logs-ref', 'SuperAdmin::logsFunctionsRef');
        $routes->get('getLogs', 'SuperAdmin::getLogs');
        $routes->get('exportLogs', 'SuperAdmin::exportLogs');
        $routes->get('getUsers', 'SuperAdmin::getUsers');
        $routes->get('getSuperAdmins', 'SuperAdmin::getSuperAdmins');
        $routes->get('create_superadmin', 'SuperAdmin::create_superadmin');
        $routes->post('createSuperAdmin', 'SuperAdmin::createSuperAdmin');
        $routes->get('pendingActions', 'SuperAdmin::pendingActions');
        $routes->post('approveAction', 'SuperAdmin::approveAction');
        $routes->post('rejectAction', 'SuperAdmin::rejectAction');
        $routes->get('profile', 'SuperAdmin::profile');
        $routes->post('updateProfile', 'SuperAdmin::updateProfile');
        $routes->get('getActorDisplay', 'SuperAdmin::getActorDisplay');
        $routes->post('getActorDisplays', 'SuperAdmin::getActorDisplays');
        $routes->post('createUser', 'SuperAdmin::createUser');
        $routes->post('retireUser', 'SuperAdmin::retireUser');
        $routes->get('logout', 'SuperAdminAuth::logout');
        // Superadmin chat (wraps Admin chat implementation)
        $routes->get('chat', 'Superadmin\Chat::index');
        $routes->get('chat/getAdmins', 'Superadmin\Chat::getAdmins');
        // Expose admin-only chat endpoints for superadmin: list admins and private admin<->admin messages
        $routes->get('chat/getMessages', 'Superadmin\Chat::getMessages');
        // Do NOT expose user conversations to superadmin. Instead provide admin-admin conversation endpoints:
        $routes->get('chat/getMessagesForAdmin/(:num)', 'Superadmin\Chat::getMessagesForAdmin/$1');
        $routes->post('chat/postAdminMessage', 'Superadmin\Chat::postAdminMessage');
        $routes->get('chat/getBroadcasts', 'Superadmin\Chat::getBroadcasts');
        $routes->post('chat/markRead/(:num)', 'Superadmin\Chat::markRead/$1');
        $routes->get('chat/unreadCount', 'Superadmin\Chat::unreadCount');
        $routes->post('chat/importJson', 'Superadmin\Chat::importJson');
    });
});
