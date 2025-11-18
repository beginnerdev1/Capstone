<div class="container-fluid">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h4 class="mb-0">Dashboard</h4>
  </div>
  <div class="row g-3">
    <div class="col-md-4">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="fw-semibold text-primary mb-1">Primary Role</div>
          <div>Create and manage Admin accounts.</div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="fw-semibold text-success mb-1">Status</div>
          <div>Super Admin is active. Use the sidebar to manage Admins.</div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="fw-semibold text-info mb-1">Tips</div>
          <div>Keep admin usernames unique and verify emails.</div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="row g-3 mt-3">
    <div class="col-md-6">
      <div class="card border-0 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
          <div class="fw-semibold mb-0">Recent Activity</div>
          <small class="text-muted">Last 10</small>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-sm mb-0" id="recentActivityTable">
              <thead class="table-light">
                <tr><th>Time</th><th>Actor</th><th>Action</th><th>Resource</th></tr>
              </thead>
              <tbody>
                <tr><td colspan="4" class="text-center py-4 text-muted">Loading...</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="fw-semibold mb-2">Quick Links & Actions</div>
          <div class="d-grid gap-2">
            <a href="<?= site_url('superadmin/create_superadmin') ?>" class="btn btn-outline-primary">Create Super Admin</a>
            <a href="<?= site_url('superadmin/logs') ?>" class="btn btn-outline-secondary">View Logs</a>
            <a href="<?= site_url('superadmin/pendingActions') ?>" class="btn btn-outline-warning">Pending Actions</a>
            <a href="<?= site_url('superadmin/profile') ?>" class="btn btn-outline-success">My Profile</a>
            <a href="<?= site_url('superadmin/settings') ?>" class="btn btn-outline-info">Settings</a>
          </div>
          <div class="mt-3 text-muted small">Use these quick links for common Super Admin tasks.</div>
        </div>
      </div>
    </div>
  </div>

  <script>
  (function(){
    function loadRecentActivity(){
      $.getJSON('<?= site_url('superadmin/getLogs') ?>?limit=10').done(function(rows){
        const $tb = $('#recentActivityTable tbody');
        $tb.empty();
        if(!rows || rows.length === 0){
          $tb.append('<tr><td colspan="4" class="text-center text-muted py-3">No recent activity</td></tr>');
          return;
        }
        rows.forEach(function(r){
          const time = r.created_at ? new Date(r.created_at).toLocaleString() : '';
          const actor = (r.actor_type && r.actor_id) ? (r.actor_type+"#"+r.actor_id) : '';
          const action = r.action || '';
          const resource = r.resource || '';
          $tb.append('<tr><td>'+time+'</td><td>'+actor+'</td><td>'+action+'</td><td>'+resource+'</td></tr>');
        });
      }).fail(function(){
        $('#recentActivityTable tbody').html('<tr><td colspan="4" class="text-center text-danger py-3">Failed to load activity (login required)</td></tr>');
      });
    }
    loadRecentActivity();
  })();
  </script>
</div>
