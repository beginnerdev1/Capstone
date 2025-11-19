<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin Account Retired</title>
</head>
<body>
    <p>Hello <?= esc($targetName ?? '') ?>,</p>

    <p>This is to inform you that your admin account has been retired and archived.</p>

    <ul>
        <li><strong>Retired by:</strong> <?= esc($actorName ?? '-') ?></li>
        <li><strong>Username:</strong> <?= esc($username ?? '-') ?></li>
        <li><strong>Email:</strong> <?= esc($targetEmail ?? '-') ?></li>
        <li><strong>Time:</strong> <?= esc($time ?? date('Y-m-d H:i:s')) ?></li>
    </ul>

    <p>If you believe this is an error, please contact the system administrators.</p>

    <p>--<br/>System Notifications</p>
</body>
</html>