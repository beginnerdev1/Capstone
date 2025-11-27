<?php
// tools/fix_failed_payments.php
// CLI helper to mark gateway awaiting payments as rejected for a given user/date.

if (php_sapi_name() !== 'cli') {
    echo "This script must be run from the command line.\n";
    exit(1);
}

function prompt($msg, $default = null) {
    if ($default !== null) {
        echo "$msg [$default]: ";
    } else {
        echo "$msg: ";
    }
    $line = rtrim(fgets(STDIN), "\r\n");
    if ($line === '' && $default !== null) return $default;
    return $line;
}

echo "Fix Failed Payments — mark gateway awaiting payments as rejected\n";
echo "Run this on your local/dev DB.\n\n";

$host = prompt('DB Host', '127.0.0.1');
$port = prompt('DB Port', '3306');
$user = prompt('DB Username', 'root');
$pass = prompt('DB Password (will be hidden if empty, press Enter to skip)');
$db   = prompt('DB Name');

if (!$db) {
    echo "Database name is required. Exiting.\n";
    exit(1);
}

// user id and date inputs
$userId = prompt('Target user_id (numeric)');
if (!is_numeric($userId)) {
    echo "Invalid user_id. Exiting.\n";
    exit(1);
}
$userId = (int)$userId;

$date = prompt('Date to match (YYYY-MM-DD)', date('Y-m-d'));
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    echo "Invalid date format. Use YYYY-MM-DD. Exiting.\n";
    exit(1);
}

$method = prompt('Payment method to target', 'gateway');

$start = $date . ' 00:00:00';
$end   = $date . ' 23:59:59';

// Connect
$mysqli = new mysqli($host, $user, $pass, $db, (int)$port);
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: ({$mysqli->connect_errno}) {$mysqli->connect_error}\n";
    exit(1);
}

$mysqli->set_charset('utf8mb4');

// Show matching rows first
$selectSql = "SELECT id, user_id, billing_id, method, status, admin_reference, amount, created_at, updated_at, paid_at FROM payments WHERE user_id = ? AND method = ? AND (status IS NULL OR status = '' OR LOWER(status) = 'awaiting_payment') AND created_at BETWEEN ? AND ? ORDER BY created_at ASC";
if (!($stmt = $mysqli->prepare($selectSql))) {
    echo "Prepare failed: ({$mysqli->errno}) {$mysqli->error}\n";
    exit(1);
}
$stmt->bind_param('isss', $userId, $method, $start, $end);
$stmt->execute();
$res = $stmt->get_result();
$rows = $res->fetch_all(MYSQLI_ASSOC);

if (count($rows) === 0) {
    echo "No matching gateway awaiting payments found for user_id={$userId} on {$date}.\n";
    exit(0);
}

echo "Found " . count($rows) . " matching row(s):\n";
foreach ($rows as $r) {
    echo "- id={$r['id']} billing_id={$r['billing_id']} method={$r['method']} status={$r['status']} admin_ref=" . ($r['admin_reference'] ?? '-') . " amount={$r['amount']} created_at={$r['created_at']}\n";
}

$confirm = prompt('Proceed to mark these rows as rejected? Type YES to continue', 'NO');
if (strtoupper($confirm) !== 'YES') {
    echo "Aborted by user. No changes made.\n";
    exit(0);
}

// Perform update
$updateSql = "UPDATE payments SET status = 'rejected', admin_reference = 'superseded_by_manual_payment', updated_at = NOW() WHERE user_id = ? AND method = ? AND (status IS NULL OR status = '' OR LOWER(status) = 'awaiting_payment') AND created_at BETWEEN ? AND ?";
if (!($ustmt = $mysqli->prepare($updateSql))) {
    echo "Prepare failed: ({$mysqli->errno}) {$mysqli->error}\n";
    exit(1);
}
$ustmt->bind_param('isss', $userId, $method, $start, $end);
$ustmt->execute();
$affected = $ustmt->affected_rows;

echo "Update complete. Affected rows: {$affected}\n";

// Show updated rows
$stmt->execute();
$res2 = $stmt->get_result();
$after = $res2->fetch_all(MYSQLI_ASSOC);
if (count($after) === 0) {
    echo "No rows remaining that match the pre-update filter.\n";
} else {
    echo "Rows remaining (still matching pre-update filter):\n";
    foreach ($after as $r) {
        echo "- id={$r['id']} status={$r['status']} admin_ref=" . ($r['admin_reference'] ?? '-') . " created_at={$r['created_at']}\n";
    }
}

echo "Done. Refresh the admin Failed Transactions page to verify results.\n";

$ustmt->close();
$stmt->close();
$mysqli->close();

exit(0);
