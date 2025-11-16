<?php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use App\Models\AdminActivityLogModel;

class ActivityLogger implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // No-op before
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        try {
            $method = strtoupper($request->getMethod());
            $path   = $request->getUri()->getPath();

            // Skip GETs to reduce noise; login/logout handled in controllers
            $lowerPathForSkip = strtolower($path);
            if ($method === 'GET' || str_contains($lowerPathForSkip, '/login') || str_contains($lowerPathForSkip, '/logout') || str_contains($lowerPathForSkip, 'verify-otp')) {
                return;
            }

            $session = session();
            $actorType = null; $actorId = null;
            if ($session->get('is_admin_logged_in')) {
                $actorType = 'admin';
                $actorId = $session->get('admin_id');
            } elseif ($session->get('is_superadmin_logged_in')) {
                $actorType = 'superadmin';
                $actorId = $session->get('superadmin_id');
            }

            if (!$actorType || !$actorId) {
                return; // unknown actor
            }

            $lowerPath = strtolower($path);
            $action = match (true) {
                str_contains($lowerPath, 'delete') || str_contains($lowerPath, 'remove') => 'delete',
                str_contains($lowerPath, 'deactivate') || str_contains($lowerPath, 'suspend') => 'deactivate',
                str_contains($lowerPath, 'activate') || str_contains($lowerPath, 'reactivate') => 'activate',
                str_contains($lowerPath, 'update') || str_contains($lowerPath, 'edit') => 'edit',
                str_contains($lowerPath, 'create') || str_contains($lowerPath, 'add') => 'create',
                default => strtolower($method),
            };

            $resource = $request->getVar('file') ?? $request->getVar('path') ?? basename($path) ?: null;
            $details  = null;
            $vars     = $request->getVar();
            if (!empty($vars)) {
                // Trim large payloads; mask passwords
                if (isset($vars['password'])) $vars['password'] = '***';
                if (isset($vars['confirm_password'])) $vars['confirm_password'] = '***';
                $encoded = json_encode($vars);
                $details = strlen($encoded) > 2000 ? substr($encoded, 0, 2000) . '…' : $encoded;
            }

            $log = new AdminActivityLogModel();
            $log->insert([
                'actor_type' => $actorType,
                'actor_id'   => $actorId,
                'action'     => $action,
                'route'      => '/' . ltrim($path, '/'),
                'method'     => $method,
                'resource'   => $resource,
                'details'    => $details,
                'ip_address' => $request->getIPAddress(),
                'user_agent' => substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255),
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'ActivityLogger error: ' . $e->getMessage());
        }
    }
}
