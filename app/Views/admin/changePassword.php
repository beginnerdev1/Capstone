<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>

<div class="container mt-5 d-flex justify-content-center align-items-center" style="min-height: 80vh;">
  <div class="card shadow change-password-card">
    <div class="card-body">
      <h4 class="card-title text-center mb-3 fw-bold">Change Password</h4>
      <p class="text-center text-muted mb-4">
        For your security, please update your default password before continuing.
      </p>

      <form action="<?= base_url('admin/setPassword') ?>" method="post" id="changePasswordForm">
        <div class="mb-3">
          <label for="password" class="form-label fw-semibold">New Password</label>
          <input type="password" name="password" id="password" class="form-control" required>
          <div class="password-hint mt-2 small text-muted" id="passwordRules">
            <div id="rule-length" class="invalid">At least 6 characters</div>
            <div id="rule-uppercase" class="invalid">At least one uppercase letter</div>
            <div id="rule-number" class="invalid">At least one number</div>
            <div id="rule-special" class="invalid">At least one special character</div>
          </div>
        </div>

        <div class="mb-3">
          <label for="confirmPassword" class="form-label fw-semibold">Confirm Password</label>
          <input type="password" name="confirm_password" id="confirmPassword" class="form-control" required>
          <div class="password-hint mt-2 small text-muted" id="matchRules">
            <div id="rule-match" class="invalid">Must match the new password</div>
          </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 fw-semibold">
          Update Password
        </button>
      </form>
    </div>
  </div>
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
<?= $this->endSection() ?>
