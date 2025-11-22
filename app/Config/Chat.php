<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Chat extends BaseConfig
{
    /**
     * When true, admin<->admin reads come from the dedicated `admin_chats` table.
     * Writes are already duplicated into `admin_chats` by the controllers.
     */
    public bool $useAdminChats = true;
}
