<?php
namespace App\Models;

use CodeIgniter\Model;

class SuperAdminModel extends Model
{
    protected $table      = 'super_admin'; // must match migration exactly
    protected $primaryKey = 'id';               // matches your migration's primary key
    protected $allowedFields = ['admin_code', 'email', 'password', 'role', 'created_at', 'updated_at']; // fields that can be set during insert/update
    
    public function generateAdminCode()
    {
    // Get the last inserted admin_code
        $lastAdmin = $this->orderBy('id', 'DESC')->first();

        if ($lastAdmin && isset($lastAdmin['admin_code'])) {
            // Extract the number part (after SUP-A-)
            $lastNumber = (int) substr($lastAdmin['admin_code'], 6);
            $newNumber = $lastNumber + 1;
        } else {
            // If no admin exists yet, start at 1
            $newNumber = 1;
        }

        // Format with leading zeros (e.g., 0001, 0002)
        return 'SUP-A-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}

?>