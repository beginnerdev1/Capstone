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

            // Skip noisy GETs but allow export/download GETs to be logged
            $lowerPathForSkip = strtolower($path);
            $isExportLike = str_contains($lowerPathForSkip, 'export') || str_contains($lowerPathForSkip, 'download') || str_contains($lowerPathForSkip, 'exportpayments') || str_contains($lowerPathForSkip, 'exportreports');
            if ($method === 'GET' && ! $isExportLike) {
                return; // ignore normal GET fetches
            }
            if (str_contains($lowerPathForSkip, '/login') || str_contains($lowerPathForSkip, '/logout') || str_contains($lowerPathForSkip, 'verify-otp')) {
                return;
            }

            $session = session();
            // If a controller has already appended the action and requested to skip, consume the flag and return.
            if ($session->get('skip_activity_logger')) {
                $session->remove('skip_activity_logger');
                return;
            }
            $actorType = null; $actorId = null; $logId = null;
            if ($session->get('is_admin_logged_in')) {
                $actorType = $session->get('position') ?: 'admin';
                $actorId = $session->get('admin_id');
                $logId = $session->get('admin_activity_log_id');
            } elseif ($session->get('is_superadmin_logged_in')) {
                $actorType = 'superadmin';
                $actorId = $session->get('superadmin_id');
                $logId = $session->get('superadmin_activity_log_id');
            }

            // If actor is known but there's no active log id, create a new top-level log entry
            if (($actorType && $actorId) && ! $logId) {
                try {
                    $logModelCreate = new AdminActivityLogModel();
                    $newId = $logModelCreate->insert([
                        'actor_type' => $actorType,
                        'actor_id'   => $actorId,
                        'action'     => 'session',
                        'route'      => '/' . ltrim($path, '/'),
                        'method'     => $method,
                        'resource'   => null,
                        'details'    => json_encode([['action'=>'start_log','time'=>date('Y-m-d H:i:s')]]),
                        'ip_address' => $request->getIPAddress(),
                        'user_agent' => substr((string)($request->getUserAgent() ?? ''), 0, 255),
                    ], true);
                    if ($newId) {
                        // persist into session for subsequent appends
                        if ($session->get('is_admin_logged_in')) $session->set('admin_activity_log_id', $newId);
                        if ($session->get('is_superadmin_logged_in')) $session->set('superadmin_activity_log_id', $newId);
                        $logId = $newId;
                    }
                } catch (\Throwable $e) {
                    // if we can't create a log, continue but avoid crashing
                    log_message('debug', '[ActivityLogger] failed to create initial log: ' . $e->getMessage());
                }
            }

            if (! $actorType || ! $actorId || ! $logId) {
                return; // unknown actor or no active log available
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
                // If the controller didn't include user_id in POST vars, try to extract a numeric id from the route
                $targetUserId = $vars['user_id'] ?? $vars['id'] ?? (is_numeric($resource) ? (int)$resource : null);
                $userName = null;
                if (isset($vars['user']) && is_array($vars['user'])) {
                    $userName = trim(($vars['user']['first_name'] ?? '') . ' ' . ($vars['user']['last_name'] ?? '')) ?: null;
                }

                // Try to resolve name from known sources in order
                if (!$userName && $targetUserId) {
                    // 1) user_information table (prefer exact profile)
                    try {
                        $userInfoModel = new \App\Models\UserInformationModel();
                        $userInfo = $userInfoModel->getByUserId($targetUserId);
                        if ($userInfo) {
                            $userName = trim(($userInfo['first_name'] ?? '') . ' ' . ($userInfo['last_name'] ?? '')) ?: null;
                            if (!empty($userInfo['first_name'])) $vars['first_name'] = $userInfo['first_name'];
                            if (!empty($userInfo['last_name'])) $vars['last_name'] = $userInfo['last_name'];
                        }
                    } catch (\Throwable $e) {
                        log_message('debug', '[ActivityLogger] userInformation lookup failed: ' . $e->getMessage());
                        $userName = null;
                    }
                    $vars['user_id'] = $targetUserId;
                }

                if (!$userName) {
                    // 2) inactive_users table (archived copy)
                    try {
                        $inactiveModel = new \App\Models\InactiveUsersModel();
                        $inactiveRow = $inactiveModel->getByUserId($targetUserId ?? $resource ?? null);
                        if ($inactiveRow) {
                            $userName = trim(($inactiveRow['first_name'] ?? '') . ' ' . ($inactiveRow['last_name'] ?? '')) ?: null;
                            if (!empty($inactiveRow['first_name'])) $vars['first_name'] = $inactiveRow['first_name'];
                            if (!empty($inactiveRow['last_name'])) $vars['last_name'] = $inactiveRow['last_name'];
                            if (!empty($inactiveRow['email'])) $vars['email'] = $inactiveRow['email'];
                            // preserve original referenced user id where possible
                            if (!isset($vars['user_id']) && !empty($inactiveRow['user_id'])) $vars['user_id'] = $inactiveRow['user_id'];
                        }
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
                            if (!empty($u['first_name'])) $vars['first_name'] = $u['first_name'];
                            if (!empty($u['last_name'])) $vars['last_name'] = $u['last_name'];
                            if (!empty($u['email'])) $vars['email'] = $u['email'];
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
