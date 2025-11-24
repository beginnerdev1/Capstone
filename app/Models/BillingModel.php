<?php

namespace App\Models;

use CodeIgniter\Model;
use Config\Database;

class BillingModel extends Model
{
    protected $table = 'billings';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id', 'bill_no', 'amount_due', 'balance', 'carryover',
        'status', 'billing_month', 'due_date', 'paid_date',
        'remarks', 'created_at', 'updated_at'
    ];

    protected $useTimestamps = false;
    protected $returnType = 'array';

    /**
     * Get billings with user join (for admin listing)
     */
    public function getAllWithUsers(array $filters = [], int $limit = 50, int $offset = 0)
    {
        $builder = $this->db->table($this->table)
            ->select('billings.*, users.first_name, users.last_name, users.email, users.purok')
            ->join('users', 'users.id = billings.user_id', 'left');

        if (!empty($filters['month'])) {
            $month = $filters['month'];
            $builder->where("DATE_FORMAT(billing_month, '%Y-%m')", $month);
        }

        if (!empty($filters['purok'])) {
            $builder->where('users.purok', $filters['purok']);
        }

        if (!empty($filters['search'])) {
            $s = '%' . $filters['search'] . '%';
            $builder->groupStart()
                ->like('users.first_name', $s)
                ->orLike('users.last_name', $s)
                ->orLike('users.email', $s)
                ->orLike('billings.bill_no', $s)
                ->groupEnd();
        }

        return $builder->orderBy('billing_month', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->limit($limit, $offset)
            ->get()
            ->getResultArray();
    }

    /**
     * Return bills for a specific user (latest first)
     */
    public function getBillsByUser(int $userId, int $limit = 10)
    {
        return $this->where('user_id', $userId)
            ->orderBy('billing_month', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->findAll($limit);
    }

    /**
     * Compute monthly summary for a user (helper used by controller)
     * Returns array: ['amount_this_month' => x, 'balance_last_month' => y, 'total_due' => z]
     * Uses carryover + (balance || amount_due) semantics.
     */
    public function getMonthlySummary(int $userId, string $month = null): array
    {
        $db = Database::connect();
        $month = $month ?? date('Y-m'); // default to current month

        $sql = "
            SELECT
                COALESCE(SUM(CASE WHEN DATE_FORMAT(billing_month, '%Y-%m') = ? THEN amount_due ELSE 0 END), 0) AS amount_this_month,
                COALESCE(SUM(CASE WHEN DATE_FORMAT(billing_month, '%Y-%m') < ? AND (status != 'Paid') THEN (COALESCE(carryover,0) + COALESCE(balance, amount_due)) ELSE 0 END), 0) AS balance_last_month,
                COALESCE(SUM(CASE WHEN status != 'Paid' THEN (COALESCE(carryover,0) + COALESCE(balance, amount_due)) ELSE 0 END), 0) AS total_due
            FROM billings
            WHERE user_id = ?
        ";
        $row = $db->query($sql, [$month, $month, $userId])->getRowArray();

        return [
            'amount_this_month' => isset($row['amount_this_month']) ? (float)$row['amount_this_month'] : 0.0,
            'balance_last_month' => isset($row['balance_last_month']) ? (float)$row['balance_last_month'] : 0.0,
            'total_due' => isset($row['total_due']) ? (float)$row['total_due'] : 0.0,
        ];
    }

    /**
     * Return total collected across payments
     */
    public function getTotalCollected(): float
    {
        $db = Database::connect();
        $row = $db->query("SELECT COALESCE(SUM(amount),0) AS total FROM payments WHERE LOWER(status) IN ('paid', 'paid')")->getRowArray();
        return isset($row['total']) ? (float)$row['total'] : 0.0;
    }

    /**
     * Get pending billings for a user for the given month (used for payments)
     */
    public function getPendingBillingsByUserAndMonth(int $userId, string $month = null): array
    {
        $month = $month ?? date('Y-m');
        $builder = $this->where('user_id', $userId)
            ->where('status !=', 'Paid')
            ->where("DATE_FORMAT(billing_month,'%Y-%m') <=", $month)
            ->orderBy('billing_month', 'DESC')
            ->orderBy('created_at', 'ASC');

        return $builder->findAll();
    }

    /**
     * Compute display outstanding for a single bill (carryover + effective balance)
     */
    public function computeBillOutstanding(array $bill): float
    {
        $carryover = isset($bill['carryover']) ? (float)$bill['carryover'] : 0.0;
        $balance = array_key_exists('balance', $bill) && $bill['balance'] !== null
            ? (float)$bill['balance']
            : (isset($bill['amount_due']) ? (float)$bill['amount_due'] : 0.0);

        return round($carryover + $balance, 2);
    }

    /**
     * Get outstanding total for a user (sum of carryover + balance/amount)
     */
    public function getOutstandingForUser(int $userId): float
    {
        $db = Database::connect();
        $sql = "
            SELECT COALESCE(SUM((COALESCE(carryover,0) + COALESCE(balance, amount_due))),0) AS outstanding
            FROM billings
            WHERE user_id = ? AND status != 'Paid'
        ";
        $row = $db->query($sql, [$userId])->getRowArray();
        return isset($row['outstanding']) ? (float)$row['outstanding'] : 0.0;
    }

    /**
     * Apply a payment to a bill (transactional).
     * Policy: apply to carryover first, then to balance/amount_due.
     * Returns array with new balances or false on failure.
     */
    public function applyPaymentToBill(int $billingId, float $amount)
    {
        $db = Database::connect();
        $db->transStart();

        // Lock the row for update to avoid races
        $builder = $this->builder();
        $builder->where('id', $billingId)->forUpdate();
        $query = $builder->get();
        $bill = $query->getRowArray();

        if (!$bill) {
            $db->transComplete();
            return false;
        }

        $carryover = isset($bill['carryover']) ? (float)$bill['carryover'] : 0.0;
        $balance = array_key_exists('balance', $bill) && $bill['balance'] !== null
            ? (float)$bill['balance']
            : (isset($bill['amount_due']) ? (float)$bill['amount_due'] : 0.0);

        $toApply = round($amount, 2);

        // 1) Apply to carryover first
        if ($carryover > 0 && $toApply > 0) {
            $apply = min($carryover, $toApply);
            $carryover = round($carryover - $apply, 2);
            $toApply = round($toApply - $apply, 2);
        }

        // 2) Apply to balance (remaining portion)
        if ($toApply > 0 && $balance > 0) {
            $apply = min($balance, $toApply);
            $balance = round($balance - $apply, 2);
            $toApply = round($toApply - $apply, 2);
        }

        // Determine new status
        $remainingTotal = round($carryover + $balance, 2);
        $newStatus = $remainingTotal <= 0 ? 'Paid' : ($balance < (float)$bill['amount_due'] ? 'Partial' : 'Pending');

        $updateData = [
            'carryover' => number_format($carryover, 2, '.', ''),
            'balance' => $balance,
            'status' => $newStatus,
            'paid_date' => $remainingTotal <= 0 ? date('Y-m-d H:i:s') : $bill['paid_date'],
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $this->update($billingId, $updateData);

        $db->transComplete();
        if ($db->transStatus() === false) {
            return false;
        }

        return [
            'new_balance' => $balance,
            'new_carryover' => $carryover,
            'status' => $newStatus,
        ];
    }

    /**
     * Detect inconsistent balances (balance null vs amount_due mismatches)
     * Returns array of affected rows or empty
     */
    public function detectInconsistentBalances(): array
    {
        $db = Database::connect();
        $sql = "SELECT id, amount_due, balance, carryover FROM billings WHERE balance IS NULL OR balance <> amount_due OR carryover IS NULL";
        return $db->query($sql)->getResultArray();
    }

    /**
     * Fix inconsistent balances by initializing null balances to amount_due and carryover to 0.00
     */
    public function initializeBalances(): int
    {
        $db = Database::connect();
        $sql = "UPDATE billings SET balance = amount_due WHERE balance IS NULL";
        $db->query($sql);
        $sql2 = "UPDATE billings SET carryover = 0.00 WHERE carryover IS NULL";
        $db->query($sql2);
        return $this->db->affectedRows();
    }

    /**
     * Ensure paid bills have zero outstanding
     */
    public function fixInconsistentBalances(): int
    {
        $db = Database::connect();
        $sql = "UPDATE billings SET balance = 0.00, carryover = 0.00 WHERE status = 'Paid'";
        $db->query($sql);
        return $this->db->affectedRows();
    }

    /**
     * Get filtered results for admin pagination (simple wrapper)
     */
    public function getFiltered(array $filters = [], int $limit = 50, int $offset = 0): array
    {
        return $this->getAllWithUsers($filters, $limit, $offset);
    }
}