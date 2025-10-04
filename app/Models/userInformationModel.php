<?php

namespace App\Models;

use CodeIgniter\Model;

class UserInformationModel extends Model
{
    protected $table      = 'user_information'; // must match migration exactly
    protected $primaryKey = 'user_id';               // matches your migration's primary key
    protected $allowedFields = ['user_id', 'phone', 'email', 'street', 'address', 'created_at', 'updated_at']; // include user_id for linking
}
