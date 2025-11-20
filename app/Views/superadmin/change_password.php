<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Change Password - SuperAdmin</title>
  <link href="<?= base_url('assets/admin/css/admin-login.css?v=' . time()) ?>" rel="stylesheet" />
  <style> .container { max-width:520px; margin:4rem auto; }</style>
</head>
<body>
  <div class="container">
    <div class="card">
      <div class="card-body">
        <h4 class="mb-3">Change Password</h4>
        <?php if(session()->getFlashdata('info')): ?>
          <div class="alert alert-info"><?= session()->getFlashdata('info') ?></div>
        <?php endif; ?>
        <form method="post" action="<?= base_url('superadmin/setPassword') ?>">
          <?= csrf_field() ?>
          <div class="mb-3">
            <label class="form-label">New Password</label>
            <input type="password" name="password" class="form-control" required minlength="8">
          </div>
          <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="confirm_password" class="form-control" required minlength="8">
          </div>
          <button class="btn btn-primary">Set Password</button>
        </form>
      </div>
    </div>
  </div>
</body>
</html>
