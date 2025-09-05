<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveDuplicateColumnFromUsers extends Migration
{
    public function up()
    {
        $this->forge->dropColumn('users', 'otp_code'); 
    }

    public function down()
    {
        // If you want to restore it when rolling back
        $this->forge->addColumn('users', [
            'otp_code' => [
                'type'       => 'VARCHAR',
                'constraint' => '6',
                'null'       => true,
            ],
        ]);
    }
}
