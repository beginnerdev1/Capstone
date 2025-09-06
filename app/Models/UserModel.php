<?php 
namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users'; // your users table
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'name', 'username', 'email', 'password', 'is_verified', 'otp_code', 'otp_expires'
    ];
}
?>
