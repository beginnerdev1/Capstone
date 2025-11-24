<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class BackfillAdminChats extends BaseCommand
{
    protected $group = 'db';
    protected $name = 'backfill:adminchats';
    protected $description = 'Backfill admin_internal messages from chat_messages into admin_chats (non-destructive)';

    public function run(array $params = [])
    {
        $db = \Config\Database::connect();
        $chatTable = 'chat_messages';
        $targetTable = 'admin_chats';

        // Ensure target exists
        if (! $db->tableExists($targetTable)) {
            CLI::error("Target table {$targetTable} does not exist. Run migrations first.");
            return;
        }

        // Fetch rows that look like admin->admin messages (targeted)
        $sql = "SELECT * FROM {$chatTable} WHERE sender = 'admin_internal' AND admin_recipient_id IS NOT NULL";
        $rows = $db->query($sql)->getResultArray();

        $count = 0;
        foreach ($rows as $r) {
            // map fields
            $data = [
                'external_id' => $r['external_id'] ?? null,
                'sender_admin_id' => (int)($r['admin_id'] ?? 0),
                'recipient_admin_id' => (int)($r['admin_recipient_id'] ?? 0),
                'message' => $r['message'] ?? '',
                'is_read' => (int)($r['is_read'] ?? 1),
                'is_broadcast' => 0,
                'created_at' => $r['created_at'] ?? date('Y-m-d H:i:s'),
            ];

            try {
                $db->table($targetTable)->insert($data);
                $count++;
            } catch (\Exception $e) {
                CLI::error('Failed to insert row id=' . ($r['id'] ?? '?') . ': ' . $e->getMessage());
            }
        }

        CLI::write("Backfill complete. Inserted {$count} rows into {$targetTable}.");
        CLI::write('Note: legacy rows are not deleted; review results before any cleanup.');
    }
}
