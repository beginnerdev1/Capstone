<br>
<br>
<div class="container-fluid px-4">
  <div class="row">
    <div class="col">
      <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
          <h3 class="mb-0">Admin Accounts</h3>
          <button class="btn btn-light" id="btnNewAdmin"><i class="fas fa-plus me-1"></i>Create Admin</button>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover align-middle" id="adminsTable">
              <thead class="table-light">
                <tr>
                  <th>#</th>
                  <th>Full Name</th>
                  <th>Username</th>
                  <th>Email</th>
                  <th>Position</th>
                  <th>Status</th>
                  <th>Created</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
          <div id="adminsAlerts" class="mt-2"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- New Admin Modal -->
<div class="modal fade" id="newAdminModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Create Admin Account</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="newAdminForm" enctype="multipart/form-data">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">First Name</label>
              <input type="text" name="first_name" class="form-control" required pattern="[A-Za-z\s'\-]+" maxlength="100" autocomplete="given-name" oninput="this.value=this.value.replace(/[^A-Za-z\s'\-]/g,'')">
            </div>
            <div class="col-md-4">
              <label class="form-label">Middle Name</label>
              <input type="text" name="middle_name" class="form-control" required pattern="[A-Za-z\s'\-]*" maxlength="100" autocomplete="additional-name" oninput="this.value=this.value.replace(/[^A-Za-z\s'\-]/g,'')">
            </div>
            <div class="col-md-4">
              <label class="form-label">Last Name</label>
              <input type="text" name="last_name" class="form-control" required pattern="[A-Za-z\s'\-]+" maxlength="100" autocomplete="family-name" oninput="this.value=this.value.replace(/[^A-Za-z\s'\-]/g,'')">
            </div>
            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Username</label>
              <input type="text" name="username" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Position</label>
              <select name="position" class="form-select" required>
                <option value="President">President</option>
                <option value="Vice President">Vice President</option>
                <option value="Secretary">Secretary</option>
                <option value="Treasurer">Treasurer</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Profile Picture (optional)</label>
              <input type="file" name="profile_picture" class="form-control" accept="image/*">
            </div>
            <div class="col-12">
              <div class="alert alert-info small mb-0">A default password of <strong>123456</strong> will be assigned. Ask the new admin to change their password after first login.</div>
            </div>
          </div>
        </form>
        <div id="newAdminAlerts" class="mt-3"></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" id="saveAdminBtn"><i class="fas fa-save me-1"></i>Save</button>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
  function buildFullName(r){
    return [r.first_name, r.middle_name, r.last_name].filter(Boolean).join(' ');
  }

  function loadAdmins(){
    $.getJSON('<?= site_url('superadmin/getUsers') ?>').done(function(rows){
      const $tb = $('#adminsTable tbody');
      $tb.empty();
      if(!rows || rows.length === 0){
        $tb.append('<tr><td colspan="8" class="text-center text-muted py-4">No admins found</td></tr>');
        return;
      }
      rows.forEach((r, idx) => {
        const status = r.is_verified ? '<span class="badge bg-success">Verified</span>' : '<span class="badge bg-secondary">Unverified</span>';
        const actions = `<button class=\"btn btn-sm btn-outline-danger retire-btn\" data-id=\"${r.id}\"><i class=\"fas fa-user-slash me-1\"></i>Retire</button>`;
        $tb.append(`
          <tr>
            <td>${idx+1}</td>
            <td>${buildFullName(r)}</td>
            <td>${r.username || ''}</td>
            <td>${r.email || ''}</td>
            <td>${r.position || ''}</td>
            <td>${status}</td>
            <td>${r.created_at || ''}</td>
            <td>${actions}</td>
          </tr>`);
      });
    });
  }

  // Open create modal
  $(document).on('click', '#btnNewAdmin', function(){
    $('#newAdminForm')[0].reset();
    $('#newAdminAlerts').empty();
    new bootstrap.Modal(document.getElementById('newAdminModal')).show();
  });

  // Save new admin
  $(document).on('click', '#saveAdminBtn', function(){
    const btn = $(this);
    $('#newAdminAlerts').empty();
    const form = document.getElementById('newAdminForm');
    const fd = new FormData(form);
    // Passwords are assigned server-side (default: 123456)
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Saving...');
    $.ajax({
      url: '<?= site_url('superadmin/createUser') ?>',
      method: 'POST',
      data: fd,
      processData: false,
      contentType: false,
      success: function(res){
        if(res && res.status === 'success'){
          $('#newAdminAlerts').html('<div class="alert alert-success">'+res.message+'</div>');
          form.reset();
          loadAdmins();
          setTimeout(()=>{ bootstrap.Modal.getInstance(document.getElementById('newAdminModal')).hide(); }, 900);
        } else {
          const msg = (res && res.message) ? res.message : 'Failed to create admin';
          $('#newAdminAlerts').html('<div class="alert alert-danger">'+msg+'</div>');
        }
      },
      error: function(){
        $('#newAdminAlerts').html('<div class="alert alert-danger">Request failed.</div>');
      },
      complete: function(){
        btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Save');
      }
    });
  });

  // Retire button click
  $(document).on('click', '.retire-btn', function(){
    const id = $(this).data('id');
    $('#retireAdminId').val(id);
    $('#retireConfirmText').text('Are you sure you want to retire this admin? This action archives the account and frees the position.');
    // Clear any previously-entered admin code to avoid accidental reuse
    $('#retireAdminCode').val('');
    new bootstrap.Modal(document.getElementById('retireModal')).show();
  });

  // Confirm retire
  $(document).on('click', '#confirmRetireBtn', function(){
    const btn = $(this);
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Processing...');
    const id = $('#retireAdminId').val();
    const code = $('#retireAdminCode').val() || '';
    $.post('<?= site_url('superadmin/retireUser') ?>', {id, admin_code: code}).done(function(res){
      if(res && res.status === 'success'){
        $('#retireAlerts').html('<div class="alert alert-success">'+res.message+'</div>');
        loadAdmins();
        setTimeout(()=>{ bootstrap.Modal.getInstance(document.getElementById('retireModal')).hide(); $('#retireAlerts').empty(); }, 900);
      } else {
        const msg = (res && res.message) ? res.message : 'Failed to retire admin';
        $('#retireAlerts').html('<div class="alert alert-danger">'+msg+'</div>');
      }
    }).fail(function(){
      $('#retireAlerts').html('<div class="alert alert-danger">Request failed.</div>');
    }).always(function(){
      btn.prop('disabled', false).html('<i class="fas fa-user-slash me-1"></i>Confirm Retire');
    });
  });

  loadAdmins();
})();
</script>

<!-- Retire Confirmation Modal -->
<div class="modal fade" id="retireModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Retire Admin</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p id="retireConfirmText" class="mb-3"></p>
        <input type="hidden" id="retireAdminId" value="">
        <div class="mb-3">
          <label class="form-label">Confirm Super Admin Code</label>
          <input type="text" id="retireAdminCode" class="form-control" placeholder="Enter your super admin code to confirm" required autocomplete="off" autocapitalize="off" spellcheck="false" inputmode="text">
          <div class="form-text">Enter your super admin admin_code to confirm this action.</div>
        </div>
        <div id="retireAlerts"></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-danger" id="confirmRetireBtn"><i class="fas fa-user-slash me-1"></i>Confirm Retire</button>
      </div>
    </div>
  </div>
</div>
