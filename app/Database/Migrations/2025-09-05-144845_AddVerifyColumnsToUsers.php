<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVerifyColumnsToUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'is_verified' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'password' // place after password column
            ],
            'otp_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 6,
                'null'       => true,
                'after'      => 'is_verified'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', ['is_verified', 'otp_code']);
    }
}
