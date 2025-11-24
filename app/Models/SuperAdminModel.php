<?php
namespace App\Models;

use CodeIgniter\Model;

class SuperAdminModel extends Model
{
    protected $table      = 'super_admin';   // must match migration
    protected $primaryKey = 'id';            // migration primary key
    
    // fields allowed for insert/update
    protected $allowedFields = ['admin_code', 'email', 'password', 'first_name', 'middle_name', 'last_name', 'is_primary', 'otp_hash', 'otp_expires', 'otp_failed_attempts', 'otp_locked_until', 'created_at', 'updated_at'];

    // auto timestamps
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Generate new admin code
    public function generateAdminCode(int $length = 12): string
    {
        // Generate a secure random alphanumeric string (mixed letters and numbers)
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789'; // avoid ambiguous chars
        $max = strlen($chars) - 1;
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $chars[random_int(0, $max)];
        }
        return $code;
    }

    /**
     * Generate a cryptographically secure numeric OTP for login (6 digits)
     * Caller must hash/store this value securely (stored in `otp_hash`).
     */
    public function generateLoginOtp(): string
    {
        // Generate a 6-digit code using random_int
        $min = 100000; $max = 999999;
        return (string) random_int($min, $max);
    }
}
