<?php
namespace App\Models;

use CodeIgniter\Model;

class ServiceReportModel extends Model
{
    protected $table            = 'service_reports';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'user_id',
        'issue_type',
        'description',
        'status',
        'address',
        'latitude',
        'longitude',
    ];

    // Automatically manage created_at and updated_at
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation rules
    protected $validationRules = [
        'issue_type' => 'required|string|max_length[100]',
        'status'     => 'in_list[pending,in_progress,resolved]',
    ];

    protected $validationMessages = [];
    protected $skipValidation     = false;

    // Custom method
    public function getReportsByUser($userId)
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }
}
?>
