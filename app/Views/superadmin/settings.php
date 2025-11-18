<div class="container-fluid">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h4 class="mb-0">Settings</h4>
  </div>

  <div class="row g-3">
    <div class="col-md-6">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="fw-semibold text-primary mb-2">General</div>
          <div class="text-muted">This is a placeholder for Super Admin settings.</div>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="fw-semibold text-success mb-2">Security</div>
          <div class="text-muted">Future: MFA, audit logs, and more.</div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3 mt-3">
    <div class="col-md-12">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="fw-semibold mb-2">Quick Actions</div>
          <div class="mb-2 text-muted">Actions for administrators — use with care.</div>
          <div class="d-flex gap-2 flex-wrap">
            <a href="<?= site_url('superadmin/create_superadmin') ?>" class="btn btn-sm btn-outline-primary">Create SuperAdmin</a>
            <a href="<?= site_url('superadmin/logs') ?>" class="btn btn-sm btn-outline-secondary">View Logs</a>
            <a href="<?= site_url('superadmin/pendingActions') ?>" class="btn btn-sm btn-outline-warning">Pending Actions</a>
            <a href="<?= site_url('superadmin/getSuperAdmins') ?>" class="btn btn-sm btn-outline-info">API: List SuperAdmins</a>
          </div>

          <hr>
          <div class="text-muted small">Promote primary: currently handled manually. If you want an in-app promote action, I can add a safe endpoint that atomically swaps the `is_primary` flag and logs the change.</div>
        </div>
      </div>
    </div>
  </div>
</div>
