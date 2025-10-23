<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePaymentSettingsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'payment_method' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'default'    => 'GCash',
            ],
            'account_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'account_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'qr_image' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('payment_settings');
    }

    public function down()
    {
        $this->forge->dropTable('payment_settings');
    }
}
