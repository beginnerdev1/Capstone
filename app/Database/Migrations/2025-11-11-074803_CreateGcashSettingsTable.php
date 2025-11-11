<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGcashSettingsTable extends Migration
{
    /**
     * Creates the 'gcash_settings' table.
     */
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            // Field for the GCash phone number (input name: gcash_number)
            'gcash_number' => [
                'type'           => 'VARCHAR',
                'constraint'     => '20', // Maxlength is 20 in the HTML
                'null'           => false,
            ],
            // Field for the path to the uploaded QR code image (input name: gcash_qr)
            'qr_code_path' => [
                'type'           => 'VARCHAR',
                'constraint'     => '255',
                'null'           => false,
                'comment'        => 'File path or URL to the GCash QR image.',
            ],
            // Standard CodeIgniter 4 timestamps
            'created_at' => [
                'type'           => 'DATETIME',
                'null'           => true,
            ],
            'updated_at' => [
                'type'           => 'DATETIME',
                'null'           => true,
            ],
        ]);

        $this->forge->addKey('id', true); // Set 'id' as the Primary Key
        $this->forge->createTable('gcash_settings');
    }

    /**
     * Drops the 'gcash_settings' table.
     */
    public function down()
    {
        $this->forge->dropTable('gcash_settings');
    }
}