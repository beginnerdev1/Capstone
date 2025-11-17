<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUniqueIndexToInactiveUsers extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        try {
            $res = $db->query("SHOW INDEX FROM `inactive_users` WHERE Key_name = 'unq_inactive_users_user_id'")->getResultArray();
            if (empty($res)) {
                $db->query("CREATE UNIQUE INDEX `unq_inactive_users_user_id` ON `inactive_users` (`user_id`)");
            }
        } catch (\Throwable $e) {
            // If the table doesn't exist or index creation fails, log and continue
            log_message('error', 'AddUniqueIndexToInactiveUsers migration up error: ' . $e->getMessage());
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();
        try {
            $res = $db->query("SHOW INDEX FROM `inactive_users` WHERE Key_name = 'unq_inactive_users_user_id'")->getResultArray();
            if (!empty($res)) {
                $db->query("DROP INDEX `unq_inactive_users_user_id` ON `inactive_users`");
            }
        } catch (\Throwable $e) {
            log_message('error', 'AddUniqueIndexToInactiveUsers migration down error: ' . $e->getMessage());
        }
    }
}
