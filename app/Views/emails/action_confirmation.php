<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Action Confirmation</title>
</head>
<body>
    <p>Hello <?= esc($recipientName ?? '') ?>,</p>

    <?php if (!empty($phase) && $phase === 'proposed'): ?>
        <p>This is a confirmation that you proposed the following action:</p>
    <?php elseif (!empty($phase) && $phase === 'approved'): ?>
        <p>This is a confirmation that you approved and executed the following action:</p>
    <?php else: ?>
        <p>This is a confirmation regarding the following administrative action:</p>
    <?php endif; ?>

    <ul>
        <li><strong>Action:</strong> <?= esc($actionLabel ?? '-') ?></li>
        <li><strong>Target:</strong> <?= esc($targetName ?? '-') ?> (<?= esc($targetEmail ?? '') ?>)</li>
        <li><strong>Time:</strong> <?= esc($time ?? date('Y-m-d H:i:s')) ?></li>
        <?php if (!empty($actionId)): ?><li><strong>Action ID:</strong> <?= esc($actionId) ?></li><?php endif; ?>
    </ul>

    <p>If you did not perform this action or believe this message was sent in error, contact your system administrator immediately.</p>

    <p>--<br/>System Notifications</p>
</body>
</html>