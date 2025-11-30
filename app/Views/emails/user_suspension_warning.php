<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    body{font-family:Arial,Helvetica,sans-serif;color:#111;margin:0;padding:24px;background:#f5f7fa}
    .card{max-width:600px;margin:0 auto;background:#fff;border-radius:8px;padding:20px;box-shadow:0 6px 18px rgba(16,24,40,0.08)}
    .muted{color:#6b7280;font-size:13px}
    .btn{display:inline-block;padding:10px 16px;background:#2563eb;color:#fff;border-radius:6px;text-decoration:none}
  </style>
</head>
<body>
  <div class="card">
    <h2 style="margin-top:0">Account Suspension Warning</h2>
    <p>Hi <?= esc($recipientName) ?>,</p>
    <p class="muted">This is an automated message to let you know that your account is at risk of being suspended due to unpaid/overdue bills.</p>

    <?php if ($mode === 'months'): ?>
      <p>You have one or more bill(s) that are more than two months past due. If payment is not made or you do not contact support, your account may be suspended.</p>
    <?php else: ?>
      <p>We noticed multiple consecutive unpaid bills for your account. If these remain unpaid, your account may be suspended.</p>
    <?php endif; ?>

    <?php if (! empty($unpaidBills)): ?>
      <h4 style="margin-top:16px">Unpaid / Outstanding Bills</h4>
      <table width="100%" cellpadding="6" cellspacing="0" style="border-collapse:collapse;border:1px solid #e5e7eb;margin-bottom:12px">
        <thead>
          <tr style="background:#f9fafb;color:#111;text-align:left">
            <th style="border-bottom:1px solid #e5e7eb">Billing Month</th>
            <th style="border-bottom:1px solid #e5e7eb">Due Date</th>
            <th style="border-bottom:1px solid #e5e7eb">Status</th>
            <th style="border-bottom:1px solid #e5e7eb">Balance</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($unpaidBills as $b): ?>
            <tr>
              <td style="border-top:1px solid #f3f4f6"><?= esc($b['billing_month']) ?></td>
              <td style="border-top:1px solid #f3f4f6"><?= esc($b['due_date']) ?></td>
              <td style="border-top:1px solid #f3f4f6"><?= esc($b['status'] ?: 'Unpaid') ?></td>
              <td style="border-top:1px solid #f3f4f6">PHP <?= number_format($b['balance'] + 0, 2) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>

    <p style="margin-top:12px">If you believe this is an error or need assistance, please contact our support team:</p>
    <p><a class="btn" href="<?= esc($supportUrl) ?>">Contact Support</a></p>

    <p class="muted" style="margin-top:18px">This message was generated on <?= esc($detectedAt) ?>. If you've already paid, please disregard this warning.</p>
    <hr />
    <p class="muted">Support: <?= esc($supportUrl) ?></p>
  </div>
</body>
</html>
