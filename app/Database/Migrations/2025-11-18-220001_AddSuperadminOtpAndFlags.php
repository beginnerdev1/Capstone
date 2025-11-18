<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSuperadminOtpAndFlags extends Migration
{
    public function up()
    {
        $fields = [
            'otp_hash' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'otp_expires' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'is_primary' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
        ];

        $this->forge->addColumn('super_admin', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('super_admin', ['otp_hash','otp_expires','is_primary']);
    }
}
