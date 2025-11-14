<?php

namespace App\Models;

use CodeIgniter\Model;

class BillingModel extends Model
{
    protected $table = 'billings';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'user_id',
        'bill_no',
        'amount_due',
        'status',
        'billing_month',
        'due_date',
        'paid_date',
        'remarks',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;

    /**
     * Get all bills with user info
     */
    public function getAllWithUsers()
    {
        return $this->select('
                billings.*,
                user_information.first_name,
                user_information.last_name,
                users.email
            ')
            ->join('users', 'users.id = billings.user_id', 'left')
            ->join('user_information', 'user_information.user_id = users.id', 'left')
            ->orderBy('billings.billing_month', 'DESC')
            ->findAll();
    }

    /**
     * Get bills for a specific user
     */
    public function getBillsByUser($userId)
    {
        return $this->select('billings.*')
            ->where('billings.user_id', $userId)
            ->orderBy('billings.billing_month', 'DESC')
            ->findAll();
    }

    /**
     * Update billing status
     */
    public function updateBillingStatus($billingId, $status = 'Paid')
    {
        return $this->where('id', $billingId)
            ->set([
                'status' => $status,
                'paid_date' => date('Y-m-d')
            ])
            ->update();
    }

    /**
     * Count total bills by status
     */
    public function countByStatus($status)
    {
        return $this->where('status', $status)->countAllResults();
    }

    /**
     * Get total amount collected (Paid only)
     */
    public function getTotalCollected()
    {
        $result = $this->selectSum('amount_due')
            ->where('status', 'Paid')
            ->get()
            ->getRow();

        return $result->amount_due ?? 0;
    }

    /**
     * Get pending billings for a specific user and month
     */
    public function getPendingBillingsByUserAndMonth($userId, $month = null)
    {
        $builder = $this->db->table('billings');
        $builder->where('user_id', $userId);
        $builder->where('status', 'pending');

        if ($month) {
            // billing_month should be in 'YYYY-MM' format
            $builder->where('billing_month', $month);
        }

        return $builder->get()->getResultArray();
    }
}
