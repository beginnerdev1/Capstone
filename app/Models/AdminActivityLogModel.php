<?php
namespace App\Models;

use CodeIgniter\Model;

class AdminActivityLogModel extends Model
{
    protected $table      = 'admin_activity_logs';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'actor_type', 'actor_id', 'action', 'route', 'method', 'resource', 'details', 'ip_address', 'user_agent', 'created_at', 'logged_out_at'
    ]; // 'details' will be a JSON array of actions for merged logs

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    /**
     * Append a single action object to an existing admin activity log's details array.
     * $detailsArray should be an associative array that will be JSON-encoded into the action's details field.
     */
    public function appendAction(int $logId, string $action, string $route = '', string $method = 'POST', $resource = null, array $detailsArray = [])
    {
        $existing = $this->find($logId);
        if (!$existing) return false;

        $actions = [];
        if (!empty($existing['details'])) {
            $decoded = json_decode($existing['details'], true);
            if (is_array($decoded)) $actions = $decoded;
        }

        $actions[] = [
            'action'   => $action,
            'route'    => $route ?: '',
            'method'   => strtoupper($method),
            'resource' => $resource,
            'details'  => !empty($detailsArray) ? json_encode($detailsArray) : null,
            'time'     => date('Y-m-d H:i:s'),
        ];

        return $this->update($logId, ['details' => json_encode($actions)]);
    }
}
