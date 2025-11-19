<?php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface; 
use CodeIgniter\Filters\FilterInterface;

class AdminAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session()->get('is_admin_logged_in')) {
            // Detect AJAX or JSON-accepting requests and return a JSON 401 instead
            $accept = strtolower((string) $request->getHeaderLine('Accept'));
            $isAjax = $request->isAJAX() || strpos($accept, 'application/json') !== false;

            if ($isAjax) {
                return \Config\Services::response()
                    ->setStatusCode(401)
                    ->setJSON(['error' => 'Authentication required.']);
            }

            return redirect()->to('admin/login')->with('error', 'Please log in to access the admin area.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something after the request
    }
}
?>