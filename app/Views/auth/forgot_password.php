<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password</title>
  <link href="<?= base_url('assets/css/loginstyle.css') ?>" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="box-area d-flex shadow">
      <!-- Left side (image) -->
      <div class="left-box col-md-6 p-0">
        <img src="<?= base_url('assets/images/forgot.jpg') ?>" alt="Forgot Password" class="img-centered">
      </div>
      
      <!-- Right side (form) -->
      <div class="right-box col-md-6">
        <div class="header-text mb-4">
          <h2>Forgot Password</h2>
          <p>Enter your email and we’ll send you a reset link.</p>
        </div>

        <form action="<?= base_url('forgot-password') ?>" method="post">
          <div class="input-group mb-3">
            <input type="email" name="email" class="form-control" placeholder="Email address" required>
          </div>

          <div class="d-grid">
            <button type="submit" class="btn btn-primary">Send Reset Link</button>
          </div>
        </form>

        <div class="mt-3">
          <a href="<?= base_url('login') ?>" class="btn btn-light w-100">Back to Login</a>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
