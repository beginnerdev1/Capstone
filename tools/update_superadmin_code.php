<?php
// Usage: php tools\update_superadmin_code.php <id> <plain_code>
// Example: php tools\update_superadmin_code.php 4 UAMFrpjqMarC

require __DIR__ . '/../vendor/autoload.php';

// Bootstrap CodeIgniter's Config (we only need DB)
\Config\Services::reset();
$db = \Config\Database::connect();

if ($argc < 3) {
    echo "Usage: php tools\\update_superadmin_code.php <id> <plain_code>\n";
    exit(1);
}

$id = (int) $argv[1];
$plain = (string) $argv[2];

if ($id <= 0 || $plain === '') {
    echo "Invalid args.\n";
    exit(1);
}

$hash = password_hash($plain, PASSWORD_DEFAULT);

$builder = $db->table('super_admin');
$ok = $builder->where('id', $id)->update(['admin_code' => $hash]);

if ($ok) {
    echo "Updated super_admin id={$id} with new hashed admin_code.\n";
    echo "New hash length: " . strlen($hash) . "\n";
    exit(0);
} else {
    echo "Failed to update row. Check DB permissions and column type/length.\n";
    exit(2);
}
