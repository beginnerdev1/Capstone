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

        // Only redirect if admin is logged in AND password change is required
        if ($session->get('is_admin_logged_in') === true && $session->get('force_password_change') === true) {
            $currentPath = uri_string();
            $excluded = ['admin/change-password', 'admin/setPassword', 'admin/logout'];

            if (!in_array($currentPath, $excluded)) {
                log_message('debug', '🧩 ForcePasswordChange triggered for ' . $currentPath);

                return redirect()->to(base_url('admin/change-password'))
                                 ->with('info', 'Please change your default password first.');
            }
        }

        // Optional: always log current session state
        log_message('debug', '🧩 ForcePasswordChange session: ' . json_encode($session->get()));
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No post-processing needed
    }
}
