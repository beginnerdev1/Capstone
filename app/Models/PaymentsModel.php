<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentsModel extends Model
{
    protected $table            = 'payments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;

    protected $allowedFields = [
        'payment_intent_id',
        'payment_method_id',
        'method',
        'reference_number',
        'admin_reference',
        'receipt_image',
        'amount',
        'currency',
        'status',
        'paid_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    /**
     * Get all payments for a specific user
     */
    public function forUser(int $userId)
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get all payments for a specific billing
     */
    public function forBilling(int $billingId)
    {
        return $this->where('billing_id', $billingId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Mark payment as paid
     */
    public function markAsPaid(string $intentId)
    {
        return $this->where('payment_intent_id', $intentId)
                    ->set([
                        'status'   => 'Paid',
                        'paid_at'  => date('Y-m-d H:i:s'),
                    ])
                    ->update();
    }

    /**
     * Mark payment as failed
     */
    public function markAsFailed(string $intentId)
    {
        return $this->where('payment_intent_id', $intentId)
                    ->set([
                        'status'     => 'Failed',
                        'updated_at' => date('Y-m-d H:i:s'),
                    ])
                    ->update();
    }
}
