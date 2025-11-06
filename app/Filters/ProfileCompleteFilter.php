<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class ProfileCompleteFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $userId = $session->get('user_id');

        // Redirect if user not logged in
        if (!$userId) {
            return redirect()->to(base_url('login'));
        }

        $userModel = new \App\Models\UsersModel();
        $user = $userModel->find($userId);

        if (!$user) {
            $session->setFlashdata('error', 'User not found.');
            return redirect()->to(base_url('login'));
        }

        // If profile incomplete or not approved, set flashdata
        if ($user['profile_complete'] == 0 || $user['status'] != 'approved') {
            $session->setFlashdata('profile_alert', true);

            // Redirect restricted pages to profile
            $currentPath = $request->getUri()->getPath();
            if ($currentPath !== 'users/profile' && $currentPath !== 'users') {
                return redirect()->to(base_url('users/profile'));
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action needed after request
    }
}
