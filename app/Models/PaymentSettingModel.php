<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentSettingsModel extends Model
{
    protected $table = 'payment_settings';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'payment_method',
        'account_name',
        'account_number',
        'qr_image',
        'updated_at',
    ];

    protected $useTimestamps = false;
    protected $returnType = 'array';
}
?>