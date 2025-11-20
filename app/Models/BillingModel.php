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
        'balance',
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
                'status' => $status, // ✅ Already correct - 'Paid' matches ENUM
                'paid_date' => date('Y-m-d')
            ])
            ->update();
    }

    /**
     * Count total bills by status
     */
    public function countByStatus($status)
    {
        // ✅ This method is fine as it accepts the status parameter
        return $this->where('status', $status)->countAllResults();
    }

    /**
     * Get total amount collected (Paid only)
     */
    public function getTotalCollected()
    {
        $result = $this->selectSum('amount_due')
            ->where('status', 'Paid') // ✅ Already correct
            ->get()
            ->getRow();

        return $result->amount_due ?? 0;
    }

    /**
     * Get pending billings for a specific user and month
     */
public function getPendingBillingsByUserAndMonth($userId, $month = null)
{
    try {
        if (empty($userId) || !is_numeric($userId)) {
            log_message('error', "Invalid user ID provided: " . var_export($userId, true));
            return [];
        }

        $builder = $this->db->table('billings');
        $builder->where('user_id', (int)$userId);
        $builder->where('status', 'Pending');
        $builder->where('paid_date IS NULL'); // Only unpaid bills

        if ($month && preg_match('/^\d{4}-\d{2}$/', $month)) {
            // Show bills for the selected month and all previous months
            $builder->where('DATE_FORMAT(billing_month, "%Y-%m") <=', $month);
        }

        $builder->orderBy('billing_month', 'ASC');
        $result = $builder->get()->getResultArray();

        log_message('info', "getPendingBillings - UserID: {$userId}, Month: {$month}, Found: " . count($result) . " billings");
        return $result;

    } catch (\Exception $e) {
        log_message('error', "BillingModel getPendingBillingsByUserAndMonth error: " . $e->getMessage());
        return [];
    }
}
}
