<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Reset Password</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: radial-gradient(circle at bottom left, #0072ff, #004aad);
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      overflow: hidden;
      position: relative;
      padding: 1rem;
      font-size: 16px;
    }

    body::before, body::after {
      content: "";
      position: absolute;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.05);
      z-index: 0;
    }

    body::before { 
      width: 600px; 
      height: 600px; 
      bottom: -200px; 
      left: -200px; 
    }
    body::after { 
      width: 500px; 
      height: 500px; 
      top: -100px; 
      right: -150px; 
    }

    .card-reset {
      position: relative;
      width: 100%;
      max-width: 420px;
      background: #fff;
      border-radius: 25px;
      padding: 3.5rem 2.5rem 2rem;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
      text-align: center;
      z-index: 1;
    }

    .card-reset .title {
      font-weight: 700;
      font-size: 1.6rem;
      color: #004aad;
      margin-bottom: 0.3rem;
      line-height: 1.2;
    }

    .card-reset .subtitle {
      font-size: 0.9rem;
      color: #6c757d;
      margin-bottom: 1.8rem;
      line-height: 1.4;
    }

    .form-control {
      border-radius: 12px;
      padding: 0.9rem 1rem;
      font-size: 0.95rem;
      border: 1px solid #dee2e6;
      line-height: 1.5;
    }

    .form-control:focus {
      border-color: #0072ff;
      box-shadow: 0 0 0 0.2rem rgba(0, 114, 255, 0.15);
    }

    .show-btn {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      border: none;
      background: transparent;
      color: #004aad;
      font-weight: 600;
      cursor: pointer;
      font-size: 0.85rem;
      z-index: 2;
    }

    .password-hint {
      background: #f8f9fa;
      border-radius: 12px;
      text-align: left;
      font-size: 0.85rem;
      color: #555;
      padding: 0.8rem 1rem;
      margin-top: 1rem;
      line-height: 1.5;
    }

    .password-hint span.invalid {
      color: #dc3545;
      font-weight: 500;
    }

    .password-hint span.valid {
      color: #198754;
      font-weight: 600;
    }

    .btn-reset {
      background-color: #004aad;
      color: white;
      border-radius: 12px;
      padding: 0.75rem;
      font-weight: 600;
      font-size: 1.1rem;
      border: none;
      margin-top: 1rem;
      transition: 0.3s;
      line-height: 1.2;
    }

    .btn-reset:hover {
      background-color: #003b8f;
      transform: translateY(-2px);
    }

    .hint {
      font-size: 0.9rem;
      color: #6c757d;
      margin-top: 1rem;
      line-height: 1.4;
    }

    .login-link {
      font-size: 0.9rem;
      margin-top: 1rem;
      line-height: 1.4;
      color: #004aad;
      font-weight: 600;
      text-decoration: none;
      display: inline-block;
    }

    .login-link:hover {
      text-decoration: underline;
    }

    /* Large screens (laptops and desktops) */
    @media (min-width: 1200px) {
      .card-reset .title {
        font-size: 1.8rem;
      }
      .card-reset .subtitle {
        font-size: 1rem;
      }
      .form-control {
        font-size: 1rem;
        padding: 1rem 1.1rem;
      }
      .password-hint {
        font-size: 0.9rem;
        padding: 0.9rem 1.1rem;
      }
      .btn-reset {
        font-size: 1.2rem;
        padding: 0.8rem;
      }
      .hint, .login-link {
        font-size: 1rem;
      }
    }

    /* Tablet and smaller desktop adjustments */
    @media (max-width: 992px) {
      body::before {
        width: 400px;
        height: 400px;
        bottom: -150px;
        left: -150px;
      }
      body::after {
        width: 350px;
        height: 350px;
        top: -75px;
        right: -100px;
      }
      .card-reset .title {
        font-size: 1.5rem;
      }
      .card-reset .subtitle {
        font-size: 0.9rem;
      }
      .form-control {
        font-size: 0.95rem;
      }
      .password-hint {
        font-size: 0.85rem;
      }
      .btn-reset {
        font-size: 1.1rem;
      }
      .hint, .login-link {
        font-size: 0.9rem;
      }
    }

    /* Mobile adjustments */
    @media (max-width: 768px) {
      .card-reset { 
        padding: 4rem 2rem 2rem; 
        max-width: 90%; 
        margin: 0 auto;
      }
      .card-reset .title {
        font-size: 1.4rem;
      }
      .card-reset .subtitle {
        font-size: 0.85rem;
        margin-bottom: 1.5rem;
      }
      .form-control {
        font-size: 0.9rem;
        padding: 0.8rem 1rem;
      }
      .password-hint {
        font-size: 0.8rem;
        padding: 0.7rem 0.9rem;
      }
      .btn-reset {
        font-size: 1rem;
        padding: 0.7rem;
      }
      .hint, .login-link {
        font-size: 0.85rem;
      }
    }

    /* Very small screens (phones in portrait) */
    @media (max-width: 480px) {
      body {
        padding: 0.5rem;
      }
      body::before, body::after {
        display: none;
      }
      .card-reset {
        padding: 3.5rem 1.5rem 1.5rem;
        border-radius: 20px;
      }
      .card-reset .title {
        font-size: 1.3rem;
      }
      .card-reset .subtitle {
        font-size: 0.8rem;
        margin-bottom: 1.2rem;
      }
      .form-control {
        font-size: 0.85rem;
        padding: 0.75rem 0.9rem;
      }
      .show-btn {
        font-size: 0.8rem;
        right: 12px;
      }
      .password-hint {
        font-size: 0.75rem;
        padding: 0.6rem 0.8rem;
      }
      .btn-reset {
        font-size: 0.95rem;
        padding: 0.65rem;
      }
      .hint, .login-link {
        font-size: 0.8rem;
      }
    }
  </style>
</head>
<body>
<?php if (session()->getFlashdata('error')): ?>
    <div class="toast-container position-fixed top-0 start-50 translate-middle-x p-3" style="z-index: 2000;">
        <div id="errorToast" class="toast align-items-center text-white bg-danger border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body text-center">
                    <?= session()->getFlashdata('error') ?>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('message')): ?>
    <div class="toast-container position-fixed top-0 start-50 translate-middle-x p-3" style="z-index: 2000;">
        <div id="successToast" class="toast align-items-center text-white bg-success border-0 show" role="alert" aria-live="polite" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body text-center">
                    <?= session()->getFlashdata('message') ?>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>
<?php endif; ?>

  <div class="card-reset">
    <h2 class="title">Reset Your Password</h2>
    <p class="subtitle">Choose a strong new password for your account. The reset link expires in one hour.</p>

    <form action="<?= base_url('/reset-password') ?>" method="post">
      <?= csrf_field() ?>
      <input type="hidden" name="email" value="<?= esc($email ?? '') ?>">
      <input type="hidden" name="token" value="<?= esc($token ?? '') ?>">

      <div class="mb-3 position-relative">
        <input id="password" type="password" name="password" class="form-control" placeholder="New Password" required autocomplete="new-password" aria-describedby="passwordHelp">
        <button type="button" class="show-btn" onclick="togglePassword()" aria-label="Show password">SHOW</button>
      </div>

      <div class="mb-3 position-relative">
        <input id="confirm_password" type="password" name="confirm_password" class="form-control" placeholder="Confirm New Password" required autocomplete="new-password">
        <button type="button" class="show-btn" onclick="toggleConfirmPassword()" aria-label="Show confirm password">SHOW</button>
      </div>

      <div id="passwordHelp" class="password-hint">
        <strong>Password must include:</strong><br>
        <span id="length" class="invalid">• 8+ characters</span><br>
        <span id="upper"  class="invalid">• 1 uppercase letter</span><br>
        <span id="lower"  class="invalid">• 1 lowercase letter</span><br>
        <span id="number" class="invalid">• 1 number</span>
      </div>

      <button type="submit" class="btn-reset w-100">Reset Password</button>

      <div class="hint">After resetting, you can log in with your new password.</div>
      <a href="<?= base_url('login') ?>" class="login-link">Back to login</a>
    </form>
  </div>

<script>
  function togglePassword() {
    const input = document.getElementById("password");
    if (!input) return;
    input.type = input.type === "password" ? "text" : "password";
  }

  function toggleConfirmPassword() {
    const input = document.getElementById("confirm_password");
    if (!input) return;
    input.type = input.type === "password" ? "text" : "password";
  }

  const password = document.getElementById("password");
  const rules = {
    length: document.getElementById("length"),
    upper:  document.getElementById("upper"),
    lower:  document.getElementById("lower"),
    number: document.getElementById("number")
  };

  if (password) {
    password.addEventListener("input", () => {
      const val = password.value;
      const tests = {
        length: val.length >= 8,
        upper:  /[A-Z]/.test(val),
        lower:  /[a-z]/.test(val),
        number: /\d/.test(val)
      };

      for (let key in tests) {
        if (!rules[key]) continue;
        rules[key].className = tests[key] ? "valid" : "invalid";
      }
    });
  }

  document.addEventListener('DOMContentLoaded', function() {
      var toastEl = document.getElementById('errorToast');
      if (toastEl) {
          var toast = new bootstrap.Toast(toastEl);
          toast.show();
      }
      var successEl = document.getElementById('successToast');
      if (successEl) {
          var toast2 = new bootstrap.Toast(successEl);
          toast2.show();
      }
      var p = document.getElementById('password');
      if (p) p.focus();
  });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>