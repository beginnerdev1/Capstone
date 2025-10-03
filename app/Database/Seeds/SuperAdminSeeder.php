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
        'password'   => password_hash('Super1', PASSWORD_DEFAULT),
    ];

    $this->db->table('super_admin')->insert($data);
    }
}
