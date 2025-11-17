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

                // Always add user/customer name for actions concerning users
                $targetUserId = $vars['user_id'] ?? $vars['id'] ?? null;
                $userName = null;
                if (isset($vars['user']) && is_array($vars['user'])) {
                    $userName = trim(($vars['user']['first_name'] ?? '') . ' ' . ($vars['user']['last_name'] ?? '')) ?: null;
                }

                // Try to resolve name from known sources in order
                if (!$userName && $targetUserId) {
                    // 1) user_information table
                    try {
                        $userInfoModel = new \App\Models\UserInformationModel();
                        $userName = $userInfoModel->getFullName($targetUserId);
                    } catch (\Throwable $e) {
                        log_message('debug', '[ActivityLogger] userInformation lookup failed: ' . $e->getMessage());
                        $userName = null;
                    }
                    $vars['user_id'] = $targetUserId;
                }

                if (!$userName) {
                    // 2) inactive_users table
                    try {
                        $inactiveModel = new \App\Models\InactiveUsersModel();
                        $userName = $inactiveModel->getFullName($targetUserId ?? $resource ?? null);
                    } catch (\Throwable $e) {
                        // ignore
                    }
                }

                if (!$userName) {
                    // 3) users table (joined info)
                    try {
                        $usersModel = new \App\Models\UsersModel();
                        $u = null;
                        if ($targetUserId) {
                            $u = $usersModel->getUserWithInfo($targetUserId);
                        } elseif (is_numeric($resource)) {
                            $u = $usersModel->getUserWithInfo($resource);
                            $vars['user_id'] = $resource;
                        }
                        if ($u) {
                            $userName = trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? '')) ?: ($u['email'] ?? null);
                        }
                    } catch (\Throwable $e) {
                        // ignore
                    }
                }

                if ($userName) {
                    $vars['user_name'] = $userName;
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
