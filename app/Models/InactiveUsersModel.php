<?php
namespace App\Models;

use CodeIgniter\Model;

class InactiveUsersModel extends Model
{
    protected $table = 'inactive_users';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'user_id', 'email', 'first_name', 'last_name', 'phone', 'purok', 'barangay', 'municipality', 'province', 'zipcode', 'inactivated_at', 'inactivated_by', 'reason', 'created_at', 'updated_at'
    ];

    public function getByUserId($userId)
    {
        return $this->where('user_id', $userId)->first();
    }

    public function getFullName($userId)
    {
        $row = $this->getByUserId($userId);
        if (!$row) return null;
        $fn = trim((string)($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''));
        return $fn ?: null;
    }
}
