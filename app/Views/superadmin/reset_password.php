<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Reset SuperAdmin Password</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>body{background:#f5f7fb}</style>
</head>
<body class="d-flex align-items-center justify-content-center" style="min-height:100vh;">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-5">
        <?php if(session()->getFlashdata('error')): ?>
          <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>
        <?php if(session()->getFlashdata('success')): ?>
          <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>

        <div class="card shadow">
          <div class="card-header text-center">
            <strong>Reset SuperAdmin Password</strong>
          </div>
          <div class="card-body">
            <form action="<?= base_url('superadmin/reset-password') ?>" method="post">
              <?= csrf_field() ?>
              <div class="mb-2">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
              </div>
              <div class="mb-2">
                <label class="form-label">Reset Code</label>
                <input type="text" name="code" class="form-control" placeholder="6-digit code" required>
              </div>
              <div class="mb-2">
                <label class="form-label">New Password</label>
                <input type="password" name="password" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" required>
              </div>
              <div class="d-grid">
                <button class="btn btn-primary">Set New Password</button>
              </div>
            </form>
            <div class="mt-3 text-center small text-muted">Return to <a href="<?= base_url('superadmin/login') ?>">login</a></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
