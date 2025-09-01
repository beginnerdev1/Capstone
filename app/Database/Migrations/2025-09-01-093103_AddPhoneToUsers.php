<?php
// nag add ako ne phone number sa users
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPhoneToUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'phone_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '15',
                'unique'     => false,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'phone_number');
    }
}
