<?php
// sa tingin ko kailangan natin ng super admin para ma-manage/makaregister tayo ng admins
namespace App\Models;

use CodeIgniter\Model;

class AdminModel extends Model
{
    protected $table      = 'admin';       
    protected $primaryKey = 'id';          

    protected $allowedFields = [
        'username',
        'email',
        'password',
    ];

    
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
?>