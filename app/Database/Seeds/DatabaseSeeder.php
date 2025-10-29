<?php
//php spark db:seed DatabaseSeeder

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call('SuperAdminSeeder');
        $this->call('AdminSeeder');
        $this->call('UsersSeeder');
        $this->call('UserInformationSeeder');
        $this->call('BillingSeeder');
        $this->call('PaymentsSeeder');
    }
}
?>