<div class="container-fluid px-4">
  <div class="row">
    <div class="col">
      <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
          <h3 class="mb-0">Super Admin Accounts</h3>
          <button class="btn btn-light" id="btnNewSuper"><i class="fas fa-plus me-1"></i>Create Super Admin</button>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover align-middle" id="superTable">
              <thead class="table-light">
                <tr>
                  <th>#</th>
                  <th>Email</th>
                  <th>First name</th>
                  <th>Primary</th>
                  <th>Created</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
          <div id="superAlerts" class="mt-2"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- New SuperAdmin Modal -->
<div class="modal fade" id="newSuperModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Create Super Admin</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="newSuperForm">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">First name</label>
              <input type="text" name="first_name" class="form-control" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Last name</label>
              <input type="text" name="last_name" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Password (min 8)</label>
              <input type="password" name="password" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Confirm Password</label>
              <input type="password" name="confirm_password" class="form-control" required>
            </div>
          </div>
        </form>
        <div id="newSuperAlerts" class="mt-3"></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" id="saveSuperBtn"><i class="fas fa-save me-1"></i>Save</button>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
    function loadSupers(){
    $.getJSON('<?= site_url('superadmin/getSuperAdmins') ?>').done(function(rows){
      console.log('getSuperAdmins response:', rows);
      const $tb = $('#superTable tbody');
      $tb.empty();
      if(!rows || rows.length === 0){
        $tb.append('<tr><td colspan="6" class="text-center text-muted py-4">No super admins found</td></tr>');
        return;
      }
      rows.forEach((r, idx) => {
        // Coerce is_primary to boolean (handles '0'/'1' or 0/1)
        const isPrimary = (r.is_primary !== undefined) ? (parseInt(r.is_primary, 10) === 1) : false;
        const primary = isPrimary ? '<span class="badge bg-success">Primary</span>' : '';
        // Show Retire and Delete for non-primary accounts
        const actions = isPrimary ? '' : `
          <button class="btn btn-sm btn-outline-danger retire-super me-1" data-id="${r.id}"><i class="fas fa-user-slash me-1"></i>Retire</button>
          <button class="btn btn-sm btn-outline-secondary delete-super" data-id="${r.id}"><i class="fas fa-trash-alt me-1"></i>Delete</button>
        `;
        $tb.append(`
          <tr>
            <td>${idx+1}</td>
            <td>${r.email || ''}</td>
            <td>${r.first_name || ''}</td>
            <td>${primary}</td>
            <td>${r.created_at || ''}</td>
            <td>${actions}</td>
          </tr>`);
      });
    }).fail(function(jqxhr, status, err){
      console.error('Failed to load super admins:', status, err, jqxhr.responseText);
      $('#superTable tbody').html('<tr><td colspan="6" class="text-center text-danger py-4">Failed to load super admins. Are you logged in?</td></tr>');
      $('#superAlerts').html('<div class="alert alert-warning">Failed to load super admins. Check your session or open DevTools network tab for details.</div>');
    });
  }

  $(document).on('click', '#btnNewSuper', function(){
    $('#newSuperForm')[0].reset();
    $('#newSuperAlerts').empty();
    new bootstrap.Modal(document.getElementById('newSuperModal')).show();
  });

  $(document).on('click', '#saveSuperBtn', function(){
    const btn = $(this);
    $('#newSuperAlerts').empty();
    const form = document.getElementById('newSuperForm');
    const fd = new FormData(form);
    const pwd = fd.get('password');
    const cpw = fd.get('confirm_password');
    if(pwd.length < 8){
      $('#newSuperAlerts').html('<div class="alert alert-danger">Password must be at least 8 characters.</div>');
      return;
    }
    if(pwd !== cpw){
      $('#newSuperAlerts').html('<div class="alert alert-danger">Passwords do not match.</div>');
      return;
    }
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Saving...');
    $.ajax({
      url: '<?= site_url('superadmin/createSuperAdmin') ?>',
      method: 'POST',
      data: fd,
      processData: false,
      contentType: false,
      success: function(res){
        if(res && res.status === 'success'){
          // Do not display admin code in UI for security. Admin code should be handled securely (email/clipboard) instead.
          $('#newSuperAlerts').html('<div class="alert alert-success">'+res.message+'</div>');
          form.reset();
          loadSupers();
          setTimeout(()=>{ bootstrap.Modal.getInstance(document.getElementById('newSuperModal')).hide(); }, 900);
        } else {
          const msg = (res && res.message) ? res.message : 'Failed to create super admin';
          $('#newSuperAlerts').html('<div class="alert alert-danger">'+msg+'</div>');
        }
      },
      error: function(){
        $('#newSuperAlerts').html('<div class="alert alert-danger">Request failed.</div>');
      },
      complete: function(){
        btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Save');
      }
    });
  });

  $(document).on('click', '.retire-super', function(){
    const id = $(this).data('id');
    if (!confirm('Retire this super admin? This creates a pending deletion that must be approved.')) return;
    $.post('<?= site_url('superadmin/retireSuperAdmin') ?>', {id}).done(function(res){
      if(res && (res.status === 'pending' || res.status === 'success')){
        alert(res.message);
        loadSupers();
      } else {
        alert((res && res.message) ? res.message : 'Failed');
      }
    }).fail(function(){ alert('Request failed'); });
  });

  // Permanent delete handler
  $(document).on('click', '.delete-super', function(){
    const id = $(this).data('id');
    if (!confirm('Permanently delete this super admin? This cannot be undone.')) return;
    $.post('<?= site_url('superadmin/deleteSuperAdmin') ?>', {id}).done(function(res){
      if(res && res.status === 'success'){
        alert(res.message || 'Deleted');
        loadSupers();
      } else {
        alert((res && res.message) ? res.message : 'Failed to delete');
      }
    }).fail(function(){ alert('Request failed'); });
  });

  loadSupers();
})();
</script>
