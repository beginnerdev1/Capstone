<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class ForcePasswordChange implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        // Only redirect if password change is required for admin
        $currentPath = uri_string();

        // Admin enforcement: when force_password_change is set, block access to
        // all admin pages except the profile edit/update and logout endpoints.
        if ($session->get('is_admin_logged_in') === true && $session->get('force_password_change') === true) {
            $excluded = [
                'admin/edit-profile',       // allow updating profile (GET)
                'admin/updateProfile',      // allow POST that saves profile
                'admin/requestPasswordOtp', // allow AJAX OTP request from edit-profile
                'admin/changePassword',     // allow AJAX password change endpoint
                'admin/logout',
            ];
            if (! in_array($currentPath, $excluded)) {
                log_message('info', '🧩 ForcePasswordChange (admin) triggered for ' . $currentPath);
                return redirect()->to(base_url('admin/edit-profile'))
                                 ->with('info', 'Please complete your profile and change your default password before continuing.');
            }
        }

        // Optional: always log current session state
        log_message('info', '🧩 ForcePasswordChange session: ' . json_encode($session->get()));
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No post-processing needed
    }
}
