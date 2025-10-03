<?php
namespace App\Models;

use CodeIgniter\Model;

class SuperAdminModel extends Model
{
    protected $table      = 'super_admin';   // must match migration
    protected $primaryKey = 'id';            // migration primary key
    
    // fields allowed for insert/update
    protected $allowedFields = ['admin_code', 'email', 'password', 'created_at', 'updated_at'];

    // auto timestamps
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Generate new admin code
    public function generateAdminCode()
    {
        $lastAdmin = $this->orderBy('id', 'DESC')->first();

        if ($lastAdmin && isset($lastAdmin['admin_code'])) {
            $lastNumber = (int) substr($lastAdmin['admin_code'], 6); // get number after SUP-A-
            $newNumber  = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return 'SUP-A-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
