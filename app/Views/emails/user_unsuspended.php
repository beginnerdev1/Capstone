<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Account Restored</title>
</head>
<body style="margin:0;padding:0;background:#f5f7fa;font-family:Arial, Helvetica, sans-serif;">
<div style="max-width:600px;margin:20px auto;padding:20px;border-radius:10px;background:#fff;box-shadow:0 6px 20px rgba(16,24,40,0.06);overflow:hidden;">
    <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color:#fff; padding:18px; border-radius:8px; text-align:center;">
        <h2 style="margin:0;font-size:20px;">✅ Account Restored</h2>
        <p style="margin:6px 0 0;opacity:0.95;">Your account access has been reinstated</p>
    </div>

    <div style="padding:18px;background:#f8fafc;border-radius:8px;margin-top:14px;">
        <p style="margin:0 0 12px;font-size:15px;color:#374151;"><strong>Hi <?= esc($recipientName ?? 'there') ?>,</strong></p>
        <p style="margin:0 0 12px;color:#4b5563;line-height:1.6;">Good news — your account was unsuspended on <?= esc($unsuspendedAt ?? date('Y-m-d H:i:s')) ?> and full access has been restored.</p>
        <p style="margin:0 0 12px;color:#4b5563;line-height:1.6;">If you have any questions about your account or need assistance, feel free to contact our support team.</p>
    </div>

    <div style="background:#fff;padding:18px;border-radius:8px;border:1px solid #e5e7eb;margin-top:14px;">
        <h3 style="margin:0 0 12px 0;color:#1f2937;font-size:16px;border-bottom:2px solid #10b981;padding-bottom:8px;">Account Summary</h3>
        <table style="width:100%;border-collapse:collapse;">
            <tr>
                <td style="padding:8px 0;font-weight:600;color:#374151;">Email</td>
                <td style="padding:8px 0;color:#6b7280;"><?= esc($email ?? '-') ?></td>
            </tr>
            <tr>
                <td style="padding:8px 0;font-weight:600;color:#374151;">Unsuspended At</td>
                <td style="padding:8px 0;color:#6b7280;"><?= esc($unsuspendedAt ?? date('Y-m-d H:i:s')) ?></td>
            </tr>
            <?php if (!empty($note)): ?>
            <tr>
                <td style="padding:8px 0;font-weight:600;color:#374151;">Note</td>
                <td style="padding:8px 0;color:#6b7280;"><?= esc($note) ?></td>
            </tr>
            <?php endif; ?>
        </table>
    </div>

    <div style="background:#ecfeff;padding:16px;border-radius:8px;border-left:4px solid #06b6d4;margin-top:14px;">
        <h4 style="margin:0 0 8px 0;color:#064e3b;">Next steps</h4>
        <p style="margin:0;color:#065f46;font-size:14px;">You can now sign in and continue using all features. If anything looks incorrect, please contact support.</p>
        <p style="margin:12px 0 0 0;text-align:center;">
            <a href="<?= esc(site_url('users')) ?>" style="display:inline-block;padding:10px 18px;background:#059669;color:#ffffff;border-radius:6px;text-decoration:none;font-weight:600;">Go to Dashboard</a>
        </p>
    </div>

    <div style="text-align:center;padding:16px;background:#f1f5f9;border-radius:8px;margin-top:14px;color:#6b7280;font-size:13px;">
        <div style="margin-bottom:6px;">Best regards,</div>
        <div style="font-weight:700;color:#111827;"><?= esc($fromName ?? 'Support') ?></div>
        <div style="margin-top:8px;font-size:11px;color:#9ca3af;">This is an automated message. Please do not reply to this email.</div>
    </div>

    <div style="text-align:center;margin-top:12px;color:#9ca3af;font-size:12px;">
        &copy; <?= esc($siteUrl ?? '') ?> <?= date('Y') ?>
    </div>
</div>
</body>
</html>
