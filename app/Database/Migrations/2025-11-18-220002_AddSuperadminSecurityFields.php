<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSuperadminSecurityFields extends Migration
{
    public function up()
    {
        $fields = [
            'otp_failed_attempts' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'otp_locked_until' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ];

        $this->forge->addColumn('super_admin', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('super_admin', ['otp_failed_attempts','otp_locked_until']);
    }
}
