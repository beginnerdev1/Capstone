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

    //show reset form (controller natin)
    $routes->get('/reset-password', 'Auth::resetPasswordForm', ['filter' => 'guest']);

    //handle reset form submission(mula sa controller natin)
    $routes->post('/reset-password', 'Auth::processResetPassword', ['filter' => 'guest']);

    // Admin login (accessible to guests only)
    $routes->get('admin/login', 'Admin::adminLogin', ['as' => 'adminLoginForm', 'filter' => 'guest']);
    $routes->post('admin/login', 'Admin::login', ['as' => 'adminLoginPost', 'filter' => 'guest']);
    $routes->get('admin/loginVerify', 'Admin::showOtpForm');   // Show form
    $routes->post('admin/loginVerify', 'Admin::loginVerify'); // Handle OTP
    $routes->post('admin/resendOtp', 'Admin::resendOtp');     // Optional resend

    //for sup admin
    $routes->get('superadmin/check-code', 'SuperAdmin::checkCodeForm');
    $routes->post('superadmin/check-code', 'SuperAdmin::checkCode');
    $routes->get('superadmin/login', 'SuperAdmin::loginForm', ['filter'=>'superadminCode']);
    $routes->post('superadmin/login', 'SuperAdmin::login');


// 🔹 Users routes (protected by filter)
$routes->group('users', ['filter' => 'userauth'], function($routes) {
    $routes->get('/'                , 'Users::index'              );
    $routes->get('getBillingsAjax'  , 'Users::getBillingsAjax'    );
    $routes->get('history'          , 'Users::history'            );
    $routes->get('payments'         , 'Users::payments'           );
    $routes->get('pressure'         , 'Users::pressure'           );
    $routes->get('report'           , 'Users::report'             );
    $routes->get('profile'          , 'Users::profile'            );
    $routes->get('changepassword'   , 'Users::changePassword'     );
    $routes->get('editprofile'      , 'Users::editProfile'        );     
    $routes->get('users/profile'    , 'Users::profile'            ); // Alias for profile
    // Profile AJAX
    $routes->get('getProfileInfo', 'Users::getProfileInfo');
    $routes->post('updateProfile', 'Users::updateProfile');
});


// Admin routes grouped under 'admin'
$routes->group('admin', ['filter' => 'adminauth'], function($routes) {
    $routes->get('/'                , 'Admin::index'                );           // Admin dashboard
    $routes->get('layoutstatic'     , 'Admin::layoutStatic'         );
    $routes->get('logout'           , 'Admin::logout'               );
    $routes->get('charts'           , 'Admin::charts'               );
    $routes->get('tables'           , 'Admin::tables'               );
    $routes->get('404'              , 'Admin::page404'              );
    $routes->get('401'              , 'Admin::page401'              );
    $routes->get('500'              , 'Admin::page500'              );
    $routes->get('registeredUsers'  , 'Admin::registeredUsers'      ); // Registered users
    $routes->get('billings'         , 'Admin::billings'             );               // Billings
    $routes->get('paidBills'        , 'Admin::paidBills'            );             // Paid bills
    $routes->get('reports'          , 'Admin::reports'              );                 // User reports dashboard
    $routes->get('test-email'       , 'Admin::testEmail'            );           // Test email functionality yeah d pa to functional and idk when this was added...
});

// SuperAdmin routes grouped under 'superadmin'
$routes->group('superadmin', ['filter' => 'superadminauth'], function($routes) {
    $routes->get('/', 'SuperAdmin::index');              // /superadmin
    $routes->get('dashboard', 'SuperAdmin::dashboard');      // /superadmin/dashboard
    $routes->get('users', 'SuperAdmin::users');          // /superadmin/users
    $routes->get('settings', 'SuperAdmin::settings');    // /superadmin/settings
    $routes->get('login', 'SuperAdmin::login');          // /superadmin/login
    $routes->get('logout', 'SuperAdmin::logout');        // /superadmin/logout
    $routes->get('forgot-password', 'SuperAdmin::forgotPassword'); // /superadmin/forgot-password
});


// You can also add more user routes here, for example:
$routes->get('about', 'Home::about'); // About page for users
