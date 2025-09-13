<?php 
namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users'; // your users table
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'name', 'username', 'email','phone_number', 'password', 'is_verified', 'otp_code', 'otp_expires','created_at', 'updated_at'
    ];

    public function getRegisteredUsers()
    {
        return $this->select('id, username, email, phone_number, created_at, updated_at')
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }
}
?>
