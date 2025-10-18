<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
    // 🔹 Login
    $routes->get('/login', 'Auth::login', ['as' => 'login', 'filter' => 'guest']); 
    $routes->post('/login', 'Auth::attemptLogin', ['as' => 'attemptLogin', 'filter' => 'guest']);
        // 🔹 Register
    $routes->get('/register', 'Auth::registerForm', ['filter' => 'guest']);  
    $routes->post('/register', 'Auth::register', ['filter' => 'guest']);

    // 🔹 Verify (OTP)
    $routes->get('/verify', 'Auth::verify', ['filter' => 'guest']);         
    $routes->post('/verifyOtp', 'Auth::verifyOtp', ['filter' => 'guest']);  
    $routes->get('/resendOtp', 'Auth::resendOtp', ['filter' => 'guest']);   

    // 🔹 Logout (always accessible if logged in)
    $routes->post('/logout', 'Auth::logout', ['as' => 'logout', 'filter' => 'userauth']);

    //forgot password
    $routes->get('/forgot-password', 'Auth::forgotPasswordForm', ['filter' => 'guest']);
    $routes->post('/forgot-password', 'Auth::sendResetLink', ['filter' => 'guest']);

    

    //handle reset form submission(mula sa controller natin)
    $routes->post('/reset-password', 'Auth::processResetPassword', ['filter' => 'guest']);

    // Admin login (accessible to guests only)
    $routes->get('admin/login', 'AdminAuth::adminLogin', ['as' => 'adminLoginForm', 'filter' => 'guest']);
    $routes->post('admin/login', 'AdminAuth::login', ['as' => 'adminLoginPost', 'filter' => 'guest']);
    $routes->get('admin/verify-otp', 'AdminAuth::showOtpForm');   // Show form
    $routes->post('admin/verify-otp', 'AdminAuth::verifyOtp'); // Handle OTP
    $routes->post('admin/resend-otp', 'AdminAuth::resendOtp');     // Optional resend


   // SuperAdmin login & check-code (guest only)
$routes->get('superadmin/login', 'SuperAdminAuth::loginForm', ['filter' => 'superadminguest']);
$routes->post('superadmin/login', 'SuperAdminAuth::login', ['filter' => 'superadminguest']);
$routes->get('superadmin/check-code', 'SuperAdminAuth::checkCodeForm', ['filter' => 'superadminguest']);
$routes->post('superadmin/check-code', 'SuperAdminAuth::checkCode', ['filter' => 'superadminguest']);

// 🔹 Users routes (protected by filter)
$routes->group('users', ['filter' => 'userauth'], function($routes) {
    $routes->get('/'                , 'Users::index'              );
    $routes->get('getBillingsAjax'  , 'Users::getBillingsAjax'    );
    $routes->get('history'          , 'Users::history'            );
    $routes->get('payments'         , 'Users::payments'           );
    $routes->get('report'           , 'Users::report'             );
    $routes->get('profile'          , 'Users::profile'            );
    $routes->get('changepassword'   , 'Users::changePassword'     );
    $routes->get('editprofile'      , 'Users::editProfile'        );     
    $routes->get('users/profile'    , 'Users::profile'            ); // Alias for profile
    // Profile AJAX
    $routes->get('getProfileInfo', 'Users::getProfileInfo');
    $routes->post('updateProfile', 'Users::updateProfile');

    // Payment AJAX
    $routes->post('createCheckout', 'Users::createCheckout');
    



});

$routes->post('webhook', 'WebhookController::webhook');



// Admin routes grouped under 'admin'
$routes->group('admin', ['filter' => 'adminauth'], function($routes) {
    $routes->get('/'                , 'Admin::index'                );           // Admin dashboard
    $routes->get('layoutstatic'     , 'Admin::layoutStatic'         );
    $routes->get('logout'           , 'AdminAuth::logout'           );
    $routes->get('charts'           , 'Admin::charts'               );
    $routes->get('tables'           , 'Admin::tables'               );
    $routes->get('404'              , 'Admin::page404'              );
    $routes->get('401'              , 'Admin::page401'              );
    $routes->get('500'              , 'Admin::page500'              );
    $routes->get('registeredUsers' , 'Admin::registeredUsers'      );
    $routes->get('getUserInfo'      , 'Admin::getUserInfo'          ); // Registered users-Ajax
    $routes->get('billings'         , 'Admin::billings'             );               // Billings
    $routes->get('getBillings'      , 'admin::getBillings'          ); //Billings Ajax
    $routes->get('paidBills'        , 'Admin::paidBills'            );             // Paid bills
    $routes->get('reports'          , 'Admin::reports'              );                 // User reports dashboard
    $routes->get('test-email'       , 'Admin::testEmail'            );           // Test email functionality yeah d pa to functional and idk when this was added...
    $routes->post('createBilling'   , 'Admin::createBilling'        );           // Form to create a new bill Ajax
   $routes->get('admin/change-password', 'Admin::changePasswordView');
    $routes->post('admin/setPassword', 'Admin::setPassword');
});

// SuperAdmin protected routes
$routes->group('superadmin', ['filter' => 'superadminauth'], function($routes) {
    $routes->get('/', 'SuperAdmin::index');
    $routes->get('dashboard', 'SuperAdmin::dashboard');
    $routes->get('users', 'SuperAdmin::users');
    $routes->post('createUser', 'SuperAdmin::createUser'); // Handle user creation
   $routes->get('getUsers', 'SuperAdmin::getUsers'); // Fetch users with pagination
    $routes->get('logout', 'SuperAdminAuth::logout'); // Logout route

    

}); 

// You can also add more user routes here, for example:
$routes->get('about', 'Home::about'); // About page for users
