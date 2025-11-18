<?php

namespace App\Models;

use CodeIgniter\Model;

class SuperAdminActionModel extends Model
{
    protected $table = 'superadmin_actions';
    protected $primaryKey = 'id';
    protected $allowedFields = ['action_type','payload','proposer_id','approver_id','status','created_at','updated_at','approved_at'];
    protected $useTimestamps = true;

    public function getPending()
    {
        return $this->where('status', 'pending')->orderBy('created_at','ASC')->findAll();
    }

    public function createAction(string $type, array $payload, int $proposerId)
    {
        $data = [
            'action_type' => $type,
            'payload' => json_encode($payload),
            'proposer_id' => $proposerId,
            'status' => 'pending'
        ];
        return $this->insert($data);
    }

    public function markApproved(int $id, int $approverId)
    {
        return $this->update($id, ['status'=>'approved','approver_id'=>$approverId,'approved_at'=>date('Y-m-d H:i:s')]);
    }

    public function markRejected(int $id, int $approverId)
    {
        return $this->update($id, ['status'=>'rejected','approver_id'=>$approverId,'approved_at'=>date('Y-m-d H:i:s')]);
    }
}
