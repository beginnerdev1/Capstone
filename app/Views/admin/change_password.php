<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Change Password</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
  <div class="card shadow mx-auto" style="max-width: 480px;">
    <div class="card-header bg-primary text-white text-center">
      <h4>Change Password</h4>
    </div>
    <div class="card-body">
      <form action="<?= base_url('admin/setPassword') ?>" method="post" id="changePasswordForm">
        <div class="mb-3">
          <label for="password" class="form-label">New Password</label>
          <input type="password" name="password" id="password" class="form-control" required>
          <div id="passwordHelp" class="form-text">
            At least 6 characters, 1 uppercase, 1 number, 1 special character.
          </div>
        </div>
        <div class="mb-3">
          <label for="confirmPassword" class="form-label">Confirm Password</label>
          <input type="password" id="confirmPassword" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success w-100">Update Password</button>
      </form>
    </div>
  </div>
</div>

<script>
document.querySelector('#changePasswordForm').addEventListener('submit', e => {
    const pass = document.querySelector('#password').value;
    const confirm = document.querySelector('#confirmPassword').value;
    const regex = /^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/;

    if (pass !== confirm) {
        e.preventDefault();
        alert('Passwords do not match.');
    } else if (!regex.test(pass)) {
        e.preventDefault();
        alert('Password must have at least 6 characters, one uppercase, one number, and one special character.');
    }
});
</script>

</body>
</html>