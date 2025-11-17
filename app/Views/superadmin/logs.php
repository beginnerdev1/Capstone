<div class="container-fluid px-4">
  <div class="card shadow-sm">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
      <h5 class="mb-0">System Activity Logs</h5>
      <div>
        <select id="limit" class="form-select form-select-sm d-inline-block" style="width:auto">
          <option value="200">Last 200</option>
          <option value="500" selected>Last 500</option>
          <option value="1000">Last 1000</option>
        </select>
        <button class="btn btn-light btn-sm ms-2" id="refreshLogs"><i class="fas fa-rotate"></i></button>
      </div>
    </div>
    <div class="card-body">
      <div class="row g-2 mb-3">
        <div class="col-6 col-md-2"><input type="date" id="start" class="form-control form-control-sm" /></div>
        <div class="col-6 col-md-2"><input type="date" id="end" class="form-control form-control-sm" /></div>
        <div class="col-6 col-md-2">
          <select id="actor_type" class="form-select form-select-sm">
            <option value="">Any Actor</option>
            <option value="admin">Admin</option>
            <option value="superadmin">SuperAdmin</option>
          </select>
        </div>
        <div class="col-6 col-md-2">
          <select id="action" class="form-select form-select-sm">
            <option value="">Any Action</option>
            <option>login</option>
            <option>create</option>
            <option>edit</option>
            <option>delete</option>
            <option>activate</option>
            <option>deactivate</option>
          </select>
        </div>
        <div class="col-6 col-md-2">
          <select id="method" class="form-select form-select-sm">
            <option value="">Any Method</option>
            <option>POST</option>
            <option>PUT</option>
            <option>PATCH</option>
            <option>DELETE</option>
          </select>
        </div>
        <div class="col-12 col-md-3"><input type="text" id="q" class="form-control form-control-sm" placeholder="Search route/resource/details" /></div>
        <div class="col-12 col-md-1 d-grid d-md-block">
          <button class="btn btn-sm btn-primary w-100" id="applyFilters">Apply</button>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-striped table-hover align-middle" id="logsTable">
          <thead class="table-light">
            <tr>
              <th>Time</th>
              <th>Actor</th>
              <th>Action</th>
              <th>Route</th>
              <th>Resource</th>
              <th>IP</th>
              <th>User Agent</th>
              <th>Logout</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
      <div id="logsAlerts" class="mt-2"></div>
    </div>
  </div>
</div>
<script>
(function(){
  function fmt(dt){ return dt ? new Date(dt.replace(' ','T')).toLocaleString() : ''; }
  function buildParams(){
    return {
      limit: $('#limit').val(),
      start: $('#start').val(),
      end: $('#end').val(),
      actor_type: $('#actor_type').val(),
      action: $('#action').val(),
      method: $('#method').val(),
      q: $('#q').val()
    };
  }
  function loadLogs(){
    $.getJSON('<?= site_url('superadmin/getLogs') ?>', buildParams()).done(function(rows){
      const $tb = $('#logsTable tbody');
      $tb.empty();
      if(!rows || !rows.length){ $tb.append('<tr><td colspan="8" class="text-center text-muted">No logs</td></tr>'); return; }
      rows.forEach(r=>{
        $tb.append(`
          <tr>
            <td>${fmt(r.created_at)}</td>
            <td>${r.actor_type}#${r.actor_id}</td>
            <td>${r.action}</td>
            <td><code>${r.route||''}</code></td>
            <td>${r.resource||''}</td>
            <td>${r.ip_address||''}</td>
            <td title="${r.user_agent||''}">${(r.user_agent||'').slice(0,40)}${(r.user_agent||'').length>40?'…':''}</td>
            <td>${fmt(r.logged_out_at)}</td>
          </tr>`);
      });
    }).fail(()=>$('#logsAlerts').html('<div class="alert alert-danger">Failed to load logs</div>'));
  }
  $('#refreshLogs, #limit, #applyFilters').on('click change', loadLogs);
  $('#q').on('keypress', function(e){ if(e.which===13){ loadLogs(); }});

  // Export CSV
  $(document).on('click', '#exportCsv', function(){
    const p = buildParams();
    const parts = [];
    const datePart = (p.start || p.end) ? `${p.start || '...'} to ${p.end || '...'}` : 'All dates';
    parts.push(`<strong>Date:</strong> ${datePart}`);
    parts.push(`<strong>Limit:</strong> ${p.limit || '500'}`);
    if (p.actor_type) parts.push(`<strong>Actor:</strong> ${p.actor_type}`);
    if (p.action) parts.push(`<strong>Action:</strong> ${p.action}`);
    if (p.method) parts.push(`<strong>Method:</strong> ${p.method}`);
    if (p.q) parts.push(`<strong>Search:</strong> ${$('<div/>').text(p.q).html()}`);
    $('#exportSummary').html(parts.join(' &nbsp;|&nbsp; '));
    new bootstrap.Modal(document.getElementById('exportConfirmModal')).show();
  });
  $(document).on('click', '#confirmExportBtn', function(){
    const p = buildParams();
    const qs = new URLSearchParams(p).toString();
    window.open('<?= site_url('superadmin/exportLogs') ?>' + '?' + qs, '_blank');
    const m = bootstrap.Modal.getInstance(document.getElementById('exportConfirmModal'));
    if (m) m.hide();
  });
  loadLogs();
})();
</script>
<div class="position-sticky bottom-0 p-2">
  <button class="btn btn-outline-secondary btn-sm" id="exportCsv"><i class="fas fa-file-csv me-1"></i>Export CSV</button>
</div>

<!-- Export Confirmation Modal -->
<div class="modal fade" id="exportConfirmModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Export System Activity Logs</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="mb-2">Export the current logs with applied filters as CSV? This operation is read-only and will not change any data.</p>
        <div id="exportSummary" class="small text-muted"></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" id="confirmExportBtn"><i class="fas fa-file-export me-1"></i>Confirm Export</button>
      </div>
    </div>
  </div>
</div>