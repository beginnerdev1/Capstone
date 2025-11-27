<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentsModel extends Model
{
    protected $table = 'payments';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'user_id',
        'billing_id',
        'payment_intent_id',
        'payment_method_id',
        'method',
        'reference_number',
        'admin_reference',
        'receipt_image',
        'amount',
        'currency',
        'status',
        'expires_at',        // NEW
        'attempt_number',    // NEW
        'paid_at',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    protected $useSoftDeletes = true;

    public function getMonthlyPayments($filters = [])
    {
        $builder = $this->db->table('payments p');
        $builder->select('
            p.id,
            p.amount,
            p.method,
            p.status,
            p.reference_number,
            p.payment_intent_id,
            p.admin_reference,
            p.receipt_image,
            p.created_at,
            p.paid_at,
            p.expires_at,
            u.id as user_id,
            ui.first_name,
            ui.last_name,
            u.email,
            CONCAT(ui.first_name, " ", ui.last_name) as user_name,
            CONCAT(SUBSTRING(ui.first_name, 1, 1), SUBSTRING(ui.last_name, 1, 1)) as avatar
        ');
        $builder->join('users u', 'u.id = p.user_id', 'left');
        $builder->join('user_information ui', 'ui.user_id = u.id', 'left');

        // Exclude soft-deleted records
        $builder->where('p.deleted_at', null);

        if (!empty($filters['month'])) {
            $builder->where('DATE_FORMAT(p.created_at, "%Y-%m")', $filters['month']);
        }

        if (!empty($filters['method'])) {
            $builder->where('p.method', $filters['method']);
        }

        if (!empty($filters['search'])) {
            $builder->groupStart();
            $builder->like('ui.first_name', $filters['search']);
            $builder->orLike('ui.last_name', $filters['search']);
            $builder->orLike('u.email', $filters['search']);
            $builder->orLike('p.reference_number', $filters['search']);
            $builder->orLike('p.admin_reference', $filters['search']);
            $builder->groupEnd();
        }

        // Only apply limit/offset if set in filters
        if (isset($filters['limit']) && isset($filters['offset'])) {
            $limit = (int)$filters['limit'];
            $offset = (int)$filters['offset'];
            $builder->limit($limit, $offset);
        }

        $builder->orderBy('p.created_at', 'DESC');

        return $builder->get()->getResultArray();
    }

    public function getMonthlyStats($filters = [])
    {
        $builder = $this->db->table('payments p');
        $builder->select('
            COUNT(DISTINCT p.user_id) as total_users,
            SUM(CASE WHEN p.status = "paid" THEN p.amount ELSE 0 END) as total_amount,
            SUM(CASE WHEN p.method = "gateway" THEN 1 ELSE 0 END) as gateway,
            SUM(CASE WHEN p.method = "manual" THEN 1 ELSE 0 END) as gcash,
            SUM(CASE WHEN p.method = "offline" THEN 1 ELSE 0 END) as counter,
            SUM(CASE WHEN p.status = "paid" THEN 1 ELSE 0 END) as paid_count
        ');

        // Exclude soft-deleted records
        $builder->where('p.deleted_at', null);

        if (!empty($filters['month'])) {
            $builder->where('DATE_FORMAT(p.created_at, "%Y-%m")', $filters['month']);
        }

        if (!empty($filters['method'])) {
            $builder->where('p.method', $filters['method']);
        }

        if (!empty($filters['search'])) {
            $builder->join('users u', 'u.id = p.user_id', 'left');
            $builder->join('user_information ui', 'ui.user_id = u.id', 'left');
            $builder->groupStart();
            $builder->like('ui.first_name', $filters['search']);
            $builder->orLike('ui.last_name', $filters['search']);
            $builder->orLike('u.email', $filters['search']);
            $builder->orLike('p.reference_number', $filters['search']);
            $builder->orLike('p.admin_reference', $filters['search']);
            $builder->groupEnd();
        }

        $result = $builder->get()->getRowArray();

        $totalUsersQuery = $this->db->table('users')
            ->where('status', 'approved')
            ->countAllResults();

        $collectionRate = 0;
        if ($totalUsersQuery > 0 && isset($result['paid_count'])) {
            $collectionRate = round(($result['paid_count'] / $totalUsersQuery) * 100);
        }

        return [
            'total_users' => $result['total_users'] ?? 0,
            'total_amount' => $result['total_amount'] ?? 0,
            'gateway' => $result['gateway'] ?? 0,
            'gcash' => $result['gcash'] ?? 0,
            'counter' => $result['counter'] ?? 0,
            'collection_rate' => $collectionRate
        ];
    }

    public function confirmGCashPayment($paymentId, $adminReference = null)
    {
        $data = [
            'status' => 'paid',
            'paid_at' => date('Y-m-d H:i:s')
        ];

        if ($adminReference) {
            $data['admin_reference'] = $adminReference;
        }

        $ok = $this->update($paymentId, $data);

        // After confirming a manual/GCash payment, mark any same-day gateway awaiting payments
        // for the same user as rejected so they appear in failed transactions.
        try {
            $payment = $this->find($paymentId);
            if ($payment && in_array(strtolower($payment['method'] ?? ''), ['manual', 'offline', 'gcash'])) {
                $userId = $payment['user_id'] ?? null;
                $createdAt = isset($payment['created_at']) ? date('Y-m-d', strtotime($payment['created_at'])) : date('Y-m-d');
                if ($userId) {
                    // Use a safe created_at range for same-day matching instead of DATE() function
                    $startOfDay = $createdAt . ' 00:00:00';
                    $endOfDay = $createdAt . ' 23:59:59';

                    $this->where('user_id', $userId)
                        ->where('method', 'gateway')
                        ->where('status', 'awaiting_payment')
                        ->where('created_at >=', $startOfDay)
                        ->where('created_at <=', $endOfDay)
                        ->set(['status' => 'rejected', 'admin_reference' => 'superseded_by_manual_payment', 'updated_at' => date('Y-m-d H:i:s')])
                        ->update();

                    log_message('info', "Marked gateway awaiting payments as rejected for user {$userId} between {$startOfDay} and {$endOfDay}");
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'Error while marking gateway awaiting payments as rejected: ' . $e->getMessage());
        }

        return $ok;
    }

    /**
     * Reject a GCash/manual payment (mark as rejected)
     * @param int $paymentId
     * @param string|null $adminReference
     * @return bool
     */
    public function rejectGCashPayment($paymentId, $adminReference = null)
    {
        $data = [
            'status' => 'rejected',
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($adminReference) {
            $data['admin_reference'] = $adminReference;
        }

        return $this->update($paymentId, $data);
    }

    // NEW: Helper methods for payment tracking (added without altering existing functionality)
    
    /**
     * Get payment status counts for dashboard/reports
     */
    public function getPaymentStatusCounts($userId = null)
    {
        $builder = $this->db->table('payments');
        $builder->select('status, COUNT(*) as count');
        
        if ($userId) {
            $builder->where('user_id', $userId);
        }
        
        $builder->where('deleted_at', null);
        $builder->groupBy('status');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Get expired payments that need cleanup
     */
    public function getExpiredPayments($minutesOld = 30)
    {
        return $this->where('status', 'awaiting_payment')
                    ->where('created_at <', date('Y-m-d H:i:s', strtotime("-{$minutesOld} minutes")))
                    ->findAll();
    }
}