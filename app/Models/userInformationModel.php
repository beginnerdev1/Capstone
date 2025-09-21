<?php

namespace App\Models;

use CodeIgniter\Model;

class UserInformationModel extends Model
{
    protected $table      = 'user_Information'; // table name in DB
    protected $primaryKey = 'user_id';         // primary key column
    protected $allowedFields = ['phone', 'email', 'street', 'address']; // fields you can update
}
?>