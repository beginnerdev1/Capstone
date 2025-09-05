<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class UserAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Check kung naka-login ang user
        if (! session()->get('isUserLoggedIn')) {
            return redirect()->to('/login');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Optional: nothing for now
    }
}
