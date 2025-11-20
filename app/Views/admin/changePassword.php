<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');
:root{ --card:#ffffff; --muted:#6b7280; --primary:#2563eb; --border:#e6eef8; --radius:12px; --shadow:0 10px 30px rgba(23,42,77,0.06); --font-sans:'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial; }
*{box-sizing:border-box}
html,body{height:100%;margin:0;font-family:var(--font-sans);background:linear-gradient(180deg,#f7fbff 0%, #f2f6fb 100%);color:#111827}
.container-center{max-width:800px;margin:36px auto;padding:20px}
.card{background:var(--card);border-radius:var(--radius);box-shadow:var(--shadow);border:1px solid var(--border)}
.card-body{padding:20px}
.card-title{margin:0 0 8px 0;font-weight:700}
.muted{color:var(--muted);margin-bottom:16px}
.form-row{display:flex;gap:12px}
.form-group{margin-bottom:12px}
.btn-primary{background:linear-gradient(90deg,var(--primary) 0%, #1e40af 100%);color:#fff;border:none;padding:8px 14px;border-radius:8px}
</style>
<div class="container-center">
  <div class="card">
    <div class="card-body">
      <h4 class="card-title">Change Password</h4>
      <p class="muted">Please set a new password for your account. Password must be at least 8 characters.</p>

      <form method="post" action="<?= site_url('admin/setPassword') ?>">
        <?= csrf_field() ?>
        <div class="form-group">
          <label for="password" class="form-label">New Password</label>
          <input type="password" class="form-control" id="password" name="password" required minlength="8" style="width:100%;padding:10px;border-radius:8px;border:1px solid var(--border)" />
        </div>
        <div class="form-group">
          <label for="confirm_password" class="form-label">Confirm Password</label>
          <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="8" style="width:100%;padding:10px;border-radius:8px;border:1px solid var(--border)" />
        </div>

        <div style="display:flex;justify-content:space-between;align-items:center;margin-top:18px">
          <small class="muted">We recommend choosing a strong password you haven't used elsewhere.</small>
          <button type="submit" class="btn-primary">Set Password</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
(function(){
  const form = document.querySelector('form');
  form.addEventListener('submit', function(e){
    const pw = document.getElementById('password').value;
    const cpw = document.getElementById('confirm_password').value;
    if (pw !== cpw) {
      e.preventDefault();
      alert('Passwords do not match.');
      return false;
    }
    if (pw.length < 8) {
      e.preventDefault();
      alert('Password must be at least 8 characters long.');
      return false;
    }
  });
})();
</script>