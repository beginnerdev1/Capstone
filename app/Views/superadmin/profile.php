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
            <input type="text" name="first_name" class="form-control" value="<?= esc($row['first_name'] ?? session()->get('superadmin_first_name') ?? '') ?>" required>
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">Middle name</label>
            <input type="text" name="middle_name" class="form-control" value="<?= esc($row['middle_name'] ?? session()->get('superadmin_middle_name') ?? '') ?>">
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">Last name</label>
            <input type="text" name="last_name" class="form-control" value="<?= esc($row['last_name'] ?? session()->get('superadmin_last_name') ?? '') ?>" required>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" value="<?= esc($row['email'] ?? session()->get('superadmin_email') ?? '') ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label">New Password (leave blank to keep)</label>
          <input type="password" name="password" class="form-control">
        </div>
        <div class="mb-3">
          <label class="form-label">Confirm New Password</label>
          <input type="password" name="confirm_password" class="form-control">
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
  if(pwd && pwd.length > 0 && pwd.length < 8){ $('#profileAlerts').html('<div class="alert alert-danger">Password must be at least 8 characters.</div>'); return; }
  if(pwd !== cpw){ $('#profileAlerts').html('<div class="alert alert-danger">Passwords do not match.</div>'); return; }
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
</script>
