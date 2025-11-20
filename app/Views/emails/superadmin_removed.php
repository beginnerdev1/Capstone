<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Super Admin Account Removed</title>
</head>
<body>
    <p>Hello <?= esc($targetName ?? '') ?>,</p>

    <p>This is to inform you that your Super Admin account has been removed following an approved retire request.</p>

    <ul>
        <li><strong>Removed by:</strong> <?= esc($actorName ?? '-') ?></li>
        <li><strong>Email:</strong> <?= esc($targetEmail ?? '-') ?></li>
        <li><strong>Time:</strong> <?= esc($time ?? date('Y-m-d H:i:s')) ?></li>
    </ul>

    <p>If you have concerns about this action, please contact the system administrators.</p>

    <p>--<br/>System Notifications</p>
</body>
</html>