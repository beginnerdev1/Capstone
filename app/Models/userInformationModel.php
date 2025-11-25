<?php
namespace App\Models;

use CodeIgniter\Model;

class UserInformationModel extends Model
{
    protected $table      = 'user_information';
    protected $primaryKey = 'info_id';

    protected $allowedFields = [
        'user_id',
        'first_name',
        'last_name',
        'gender',
        'line_number',
        'age',
        'family_number',
        'phone',
        'purok',
        'barangay',
        'municipality',
        'province',
        'zipcode',
        'profile_picture',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $returnType    = 'array';

    protected $validationRules = [
        'first_name'    => 'required|string|max_length[100]',
        'last_name'     => 'required|string|max_length[100]',
        'phone'         => 'required|string|max_length[20]',
        'gender'        => 'required|in_list[Male,Female,Other]',
        'age'           => 'required|integer|greater_than[0]|less_than[120]',
        'family_number' => 'required|integer|greater_than[0]|less_than[21]',
        'purok'         => 'required|integer|greater_than[0]|less_than[8]', // updated for numeric purok
        'line_number'   => 'permit_empty|string|max_length[32]',
    ];

    public function getByUserId($userId)
    {
        return $this->where('user_id', $userId)->first();
    }

    public function hasCompleteProfile($userId)
    {
        $info = $this->getByUserId($userId);
        return $info && !empty($info['first_name']) && !empty($info['last_name']);
    }

    public function saveUserInfo($userId, $data)
    {
        if (!$this->validate($data)) {
            return [
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->errors()
            ];
        }

        try {
            $existingInfo = $this->getByUserId($userId);

            if ($existingInfo) {
                $updated = $this->update($existingInfo['info_id'], $data);
                if (!$updated) {
                    throw new \Exception('Failed to update profile');
                }

                return [
                    'success' => true,
                    'message' => 'Profile updated successfully',
                    'data'    => $this->getByUserId($userId)
                ];

            } else {
                $data['user_id'] = $userId;
                $inserted = $this->insert($data);
                if (!$inserted) {
                    throw new \Exception('Failed to create profile');
                }

                return [
                    'success' => true,
                    'message' => 'Profile created successfully',
                    'data'    => $this->getByUserId($userId)
                ];
            }

        } catch (\Exception $e) {
            log_message('error', 'UserInfo Save Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage(),
                'errors'  => []
            ];
        }
    }

    public function getFullName($userId)
    {
        $info = $this->getByUserId($userId);
        return $info ? trim("{$info['first_name']} {$info['last_name']}") : 'Unknown User';
    }

    public function getAllProfiles()
    {
        return $this->select('user_information.*')
                    ->orderBy('user_information.last_name', 'ASC')
                    ->findAll();
    }
}
