<?php

namespace App\Models;

use CodeIgniter\Model;

class BillingModel extends Model
{
    protected $table = 'billings';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'user_id',
        'payment_id',
        'bill_no',
        'amount_due',
        'status',
        'billing_month',
        'due_date',
        'paid_date',
        'proof_of_payment',
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
                users.email,
                payments.amount as payment_amount,
                payments.status as payment_status,
                payments.method as payment_method
            ')
            ->join('users', 'users.id = billings.user_id', 'left')
            ->join('user_information', 'user_information.user_id = users.id', 'left')
            ->join('payments', 'payments.id = billings.payment_id', 'left')
            ->orderBy('billings.billing_month', 'DESC')
            ->findAll();
    }

    /**
     * Get bills for a specific user
     */
    public function getBillsByUser($userId)
    {
        return $this->select('
                billings.*,
                payments.amount as payment_amount,
                payments.status as payment_status,
                payments.method as payment_method
            ')
            ->join('payments', 'payments.id = billings.payment_id', 'left')
            ->where('billings.user_id', $userId)
            ->orderBy('billings.billing_month', 'DESC')
            ->findAll();
    }

    /**
     * Update billing status when payment is confirmed
     */
    public function updateBillingStatus($paymentId, $status = 'Paid')
    {
        return $this->where('payment_id', $paymentId)
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
}