<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'admin_code' => 'SUP-A-0001',
            'email'      => 'mikevidal689@gmail.com',
            'password'   => password_hash('super123', PASSWORD_DEFAULT),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->db->table('super_admin')->insert($data);
    }
}
