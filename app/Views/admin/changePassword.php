<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Change Password</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

  <!-- Your main admin CSS -->
  <link rel="stylesheet" href="<?= base_url('assets/admin/css/style.css') ?>">

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

    .signup-card {
      position: relative;
      width: 100%;
      max-width: 420px;
      background: #fff;
      border-radius: 25px;
      padding: 3.5rem 2.5rem 2rem;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
      text-align: center;
      z-index: 1;
      animation: fadeIn 0.6s ease-out;
    }

    .title {
      font-weight: 700;
      font-size: 1.6rem;
      color: #004aad;
      margin-bottom: 0.3rem;
      line-height: 1.2;
    }

    .subtitle {
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

    .btn-primary {
      background-color: #004aad;
      color: white;
      border-radius: 12px;
      padding: 0.75rem;
      font-weight: 600;
      font-size: 1.1rem;
      border: none;
      margin-top: 1rem;
      transition: 0.3s;
    }

    .btn-primary:hover {
      background-color: #003b8f;
      transform: translateY(-2px);
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(15px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>

  <div class="signup-card">
    <h4 class="title">Change Password</h4>
    <p class="subtitle">For your security, please update your default password before continuing.</p>

    <form action="<?= base_url('admin/setPassword') ?>" method="post" id="changePasswordForm">
      <div class="mb-3 text-start">
        <label for="password" class="form-label fw-semibold">New Password</label>
        <input type="password" name="password" id="password" class="form-control" required>
        <div class="password-hint mt-2" id="passwordRules">
          <div id="rule-length" class="invalid"> At least 6 characters</div>
          <div id="rule-uppercase" class="invalid"> At least one uppercase letter</div>
          <div id="rule-number" class="invalid"> At least one number</div>
          <div id="rule-special" class="invalid"> At least one special character</div>
        </div>
      </div>

      <div class="mb-3 text-start">
        <label for="confirmPassword" class="form-label fw-semibold">Confirm Password</label>
        <input type="password" name="confirm_password" id="confirmPassword" class="form-control" required>
        <div class="password-hint mt-2" id="matchRules">
          <div id="rule-match" class="invalid"> Must match the new password</div>
        </div>
      </div>

      <button type="submit" class="btn btn-primary w-100">Update Password</button>
    </form>
  </div>

  <script>
    const passwordInput = document.querySelector('#password');
    const confirmInput = document.querySelector('#confirmPassword');
    const rules = {
      length: document.querySelector('#rule-length'),
      uppercase: document.querySelector('#rule-uppercase'),
      number: document.querySelector('#rule-number'),
      special: document.querySelector('#rule-special')
    };
    const matchRule = document.querySelector('#rule-match');

    for (const el of [...Object.values(rules), matchRule]) {
      el.dataset.base = el.textContent.replace(/^[✅❌]\\s*/, '');
    }

    function toggleRule(el, condition) {
      el.classList.toggle('valid', condition);
      el.classList.toggle('invalid', !condition);
      el.textContent = (condition ? '✅ ' : '❌ ') + el.dataset.base;
    }

    function validatePassword() {
      const val = passwordInput.value;
      toggleRule(rules.length, val.length >= 6);
      toggleRule(rules.uppercase, /[A-Z]/.test(val));
      toggleRule(rules.number, /\d/.test(val));
      toggleRule(rules.special, /[\W_]/.test(val));
      toggleRule(matchRule, confirmInput.value === val && confirmInput.value.length > 0);
    }

    passwordInput.addEventListener('input', validatePassword);
    confirmInput.addEventListener('input', validatePassword);

    document.querySelector('#changePasswordForm').addEventListener('submit', e => {
      const pass = passwordInput.value;
      const confirm = confirmInput.value;
      const regex = /^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/;

      if (pass !== confirm) {
        e.preventDefault();
        alert('Passwords do not match.');
      } else if (!regex.test(pass)) {
        e.preventDefault();
        alert('Password does not meet security requirements.');
      }
    });
  </script>
</body>
</html>
