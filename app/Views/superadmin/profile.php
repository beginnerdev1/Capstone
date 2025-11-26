<div class="container-fluid px-4">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h4 class="mb-0">Your Profile</h4>
  </div>
  <div class="card shadow-sm">
    <div class="card-body">
      <form id="profileForm">
        <div class="row">
          <div class="col-md-4 mb-3">
            <label class="form-label">First name</label>
            <input type="text" name="first_name" class="form-control" value="<?= esc($row['first_name'] ?? session()->get('superadmin_first_name') ?? '') ?>" required pattern="[A-Za-z\s'\-]+" maxlength="100" oninput="this.value=this.value.replace(/[^A-Za-z\s'\-]/g,'')">
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">Middle name</label>
            <input type="text" name="middle_name" class="form-control" value="<?= esc($row['middle_name'] ?? session()->get('superadmin_middle_name') ?? '') ?>" pattern="[A-Za-z\s'\-]*" maxlength="100" oninput="this.value=this.value.replace(/[^A-Za-z\s'\-]/g,'')">
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">Last name</label>
            <input type="text" name="last_name" class="form-control" value="<?= esc($row['last_name'] ?? session()->get('superadmin_last_name') ?? '') ?>" required pattern="[A-Za-z\s'\-]+" maxlength="100" oninput="this.value=this.value.replace(/[^A-Za-z\s'\-]/g,'')">
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" value="<?= esc($row['email'] ?? session()->get('superadmin_email') ?? '') ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label">New Password (leave blank to keep)</label>
          <div class="input-group">
            <input id="passwordInput" type="password" name="password" class="form-control">
            <button class="btn btn-outline-secondary" type="button" id="togglePassword" aria-pressed="false" title="Show password"><i class="fas fa-eye"></i></button>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label">Confirm New Password</label>
          <div class="input-group">
            <input id="confirmPasswordInput" type="password" name="confirm_password" class="form-control">
            <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword" aria-pressed="false" title="Show password"><i class="fas fa-eye"></i></button>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label">Super Admin Code (required to change password)</label>
          <div class="input-group">
            <input id="adminCodeInput" type="password" name="admin_code" class="form-control" placeholder="Enter your super admin code" autocomplete="off" autocapitalize="off" spellcheck="false" inputmode="text" pattern="[A-Za-z0-9]+" maxlength="128" oninput="this.value=this.value.replace(/[^A-Za-z0-9]/g,'')">
            <button class="btn btn-outline-secondary" type="button" id="toggleAdminCode" aria-pressed="false" title="Show code"><i class="fas fa-eye"></i></button>
          </div>
          <div class="form-text">Enter your super admin code when changing your password. Leave empty if not changing password. Alphanumeric only.</div>
        </div>
        <div id="profileAlerts"></div>
        <div class="d-flex justify-content-end">
          <button class="btn btn-primary" id="saveProfileBtn">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
$(document).on('click', '#saveProfileBtn', function(e){
  e.preventDefault();
  const fd = new FormData(document.getElementById('profileForm'));
  $('#profileAlerts').empty();
  const pwd = fd.get('password');
  const cpw = fd.get('confirm_password');
  const adminCode = fd.get('admin_code');
  if(pwd && pwd.length > 0 && pwd.length < 8){ $('#profileAlerts').html('<div class="alert alert-danger">Password must be at least 8 characters.</div>'); return; }
  if(pwd !== cpw){ $('#profileAlerts').html('<div class="alert alert-danger">Passwords do not match.</div>'); return; }
  // If password is being changed, require admin_code for confirmation
  if(pwd && pwd.length > 0 && (!adminCode || adminCode.trim().length === 0)){
    $('#profileAlerts').html('<div class="alert alert-danger">Changing password requires your super admin code for confirmation.</div>'); return;
  }
  $.ajax({
    url: '<?= site_url('superadmin/updateProfile') ?>',
    method: 'POST',
    data: fd,
    contentType: false,
    processData: false,
    success: function(res){
      if(res && res.status === 'success'){
        $('#profileAlerts').html('<div class="alert alert-success">'+res.message+'</div>');
      } else {
        $('#profileAlerts').html('<div class="alert alert-danger">'+(res.message||'Failed to update')+'</div>');
      }
    },
    error: function(){ $('#profileAlerts').html('<div class="alert alert-danger">Request failed</div>'); }
  });
});

// Toggle visibility of the admin_code input
$(document).on('click', '#toggleAdminCode', function(){
  const $btn = $(this);
  const $input = $('#adminCodeInput');
  if (!$input.length) return;
  const inputEl = $input.get(0);
  if (inputEl.type === 'password') {
    inputEl.type = 'text';
    $btn.find('i').removeClass('fa-eye').addClass('fa-eye-slash');
    $btn.attr('aria-pressed','true').attr('title','Hide code');
  } else {
    inputEl.type = 'password';
    $btn.find('i').removeClass('fa-eye-slash').addClass('fa-eye');
    $btn.attr('aria-pressed','false').attr('title','Show code');
  }
});

// Toggle visibility for password fields
$(document).on('click', '#togglePassword', function(){
  const $btn = $(this);
  const $input = $('#passwordInput');
  if (!$input.length) return;
  const el = $input.get(0);
  if (el.type === 'password') {
    el.type = 'text';
    $btn.find('i').removeClass('fa-eye').addClass('fa-eye-slash');
    $btn.attr('aria-pressed','true').attr('title','Hide password');
  } else {
    el.type = 'password';
    $btn.find('i').removeClass('fa-eye-slash').addClass('fa-eye');
    $btn.attr('aria-pressed','false').attr('title','Show password');
  }
});

$(document).on('click', '#toggleConfirmPassword', function(){
  const $btn = $(this);
  const $input = $('#confirmPasswordInput');
  if (!$input.length) return;
  const el = $input.get(0);
  if (el.type === 'password') {
    el.type = 'text';
    $btn.find('i').removeClass('fa-eye').addClass('fa-eye-slash');
    $btn.attr('aria-pressed','true').attr('title','Hide password');
  } else {
    el.type = 'password';
    $btn.find('i').removeClass('fa-eye-slash').addClass('fa-eye');
    $btn.attr('aria-pressed','false').attr('title','Show password');
  }
});
</script>
