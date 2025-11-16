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
            // Validate user ID
            if (empty($userId) || !is_numeric($userId)) {
                log_message('error', "Invalid user ID provided: " . var_export($userId, true));
                return [];
            }

            $builder = $this->db->table('billings');
            $builder->where('user_id', (int)$userId);
            
            // ✅ FIX: Use 'Pending' with capital P to match ENUM values
            $builder->where('status', 'Pending');

            if ($month) {
                // Handle different month formats
                if (preg_match('/^\d{4}-\d{2}$/', $month)) {
                    // YYYY-MM format
                    $builder->where('billing_month', $month);
                } elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $month)) {
                    // YYYY-MM-DD format - extract year-month part
                    $yearMonth = substr($month, 0, 7);
                    $builder->where('billing_month', $yearMonth);
                } else {
                    log_message('warning', "Invalid month format provided: {$month}");
                }
            }

            // Add order by to get most recent first
            $builder->orderBy('created_at', 'DESC');

            $result = $builder->get()->getResultArray();
            
            // Debug logging
            log_message('info', "getPendingBillings - UserID: {$userId}, Month: {$month}, Found: " . count($result) . " billings");
            
            return $result;

        } catch (\Exception $e) {
            log_message('error', "BillingModel getPendingBillingsByUserAndMonth error: " . $e->getMessage());
            return [];
        }
    }
}
