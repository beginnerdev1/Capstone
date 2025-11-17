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
            log_message('debug', '[ActivityLogger] after() called: method=' . $request->getMethod() . ', path=' . $request->getUri()->getPath());
            $method = strtoupper($request->getMethod());
            $path   = $request->getUri()->getPath();

            // Skip GETs to reduce noise; login/logout handled in controllers
            $lowerPathForSkip = strtolower($path);
            if ($method === 'GET' || str_contains($lowerPathForSkip, '/login') || str_contains($lowerPathForSkip, '/logout') || str_contains($lowerPathForSkip, 'verify-otp')) {
                return;
            }

            $session = session();
            $actorType = null; $actorId = null; $logId = null;
            if ($session->get('is_admin_logged_in')) {
                $actorType = 'admin';
                $actorId = $session->get('admin_id');
                $logId = $session->get('admin_activity_log_id');
            } elseif ($session->get('is_superadmin_logged_in')) {
                $actorType = 'superadmin';
                $actorId = $session->get('superadmin_id');
                $logId = $session->get('superadmin_activity_log_id');
            }

            if (!$actorType || !$actorId || !$logId) {
                return; // unknown actor or no active log
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
            $vars     = $request->getVar();
            $details  = null;
            if (!empty($vars)) {
                // Trim large payloads; mask passwords
                if (isset($vars['password'])) $vars['password'] = '***';
                if (isset($vars['confirm_password'])) $vars['confirm_password'] = '***';

                // If user_id or id is present, fetch first_name and last_name for better log context
                $targetUserId = $vars['user_id'] ?? $vars['id'] ?? null;
                // If user object is present, use its name
                if (isset($vars['user']) && is_array($vars['user'])) {
                    $vars['first_name'] = $vars['user']['first_name'] ?? null;
                    $vars['last_name'] = $vars['user']['last_name'] ?? null;
                } else if ($targetUserId) {
                    $userInfoModel = new \App\Models\UserInformationModel();
                    $userInfo = $userInfoModel->getByUserId($targetUserId);
                    if ($userInfo) {
                        $vars['first_name'] = $userInfo['first_name'] ?? null;
                        $vars['last_name'] = $userInfo['last_name'] ?? null;
                    }
                } else if (
                    ($action === 'deactivate' || $action === 'activate') && is_numeric($resource)
                ) {
                    // Try to fetch user info if route/resource looks like a user id
                    $userInfoModel = new \App\Models\UserInformationModel();
                    $userInfo = $userInfoModel->getByUserId($resource);
                    if ($userInfo) {
                        $vars['first_name'] = $userInfo['first_name'] ?? null;
                        $vars['last_name'] = $userInfo['last_name'] ?? null;
                        $vars['id'] = $resource;
                    }
                }

                $encoded = json_encode($vars);
                $details = strlen($encoded) > 2000 ? substr($encoded, 0, 2000) . '…' : $encoded;
            }

            $logModel = new AdminActivityLogModel();
            $existing = $logModel->find($logId);
            if ($existing) {
                // Append action/resource/details to the log
                $actions = isset($existing['details']) && $existing['details'] ? json_decode($existing['details'], true) : [];
                if (!is_array($actions)) $actions = [];
                $actions[] = [
                    'action'   => $action,
                    'route'    => '/' . ltrim($path, '/'),
                    'method'   => $method,
                    'resource' => $resource,
                    'details'  => $details,
                    'time'     => date('Y-m-d H:i:s'),
                ];
                $logModel->update($logId, [
                    'details' => json_encode($actions),
                ]);
            }
        } catch (\Throwable $e) {
            log_message('error', 'ActivityLogger error: ' . $e->getMessage());
        }
    }
}
