<?php

namespace App\Controllers\Superadmin;

use App\Controllers\Admin\Chat as AdminChat;

/**
 * Superadmin chat wrapper — reuses Admin\Chat behavior while running
 * under the superadmin namespace so sidebar links and routes are
 * consistent for superadmins.
 */
class Chat extends AdminChat
{
    // Intentionally empty — inherits Admin\Chat methods and behavior.
}
