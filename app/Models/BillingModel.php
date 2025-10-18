<?php

namespace App\Models;

use CodeIgniter\Model;

class BillingModel extends Model
{
    protected $table = 'billings';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'user_id',
        'amount',
        'billing_date',
        'due_date',
        'status',
        'paid_at',
        'reading',
        'previous_reading',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;

    // 💡 Join with users table to get full name and purok
    public function getAllWithUsers()
    {
        return $this->select('billings.*, users.Firstname, users.Surname, users.Purok')
                    ->join('users', 'users.id = billings.user_id', 'left')
                    ->orderBy('billing_date', 'DESC')
                    ->findAll();
    }

    // 🧾 Get bills for a specific user
    public function getBillsByUser($userId)
    {
        return $this->where('user_id', $userId)
                    ->orderBy('billing_date', 'DESC')
                    ->findAll();
    }

    // 📊 Count total bills by status
    public function countByStatus($status)
    {
        return $this->where('status', $status)->countAllResults();
    }

    // 💸 Calculate total amount collected
    public function getTotalCollected()
    {
        return $this->selectSum('amount')
                    ->where('status', 'paid')
                    ->get()->getRow()->amount ?? 0;
    }
}