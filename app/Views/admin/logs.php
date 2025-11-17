<div class="container-fluid px-4">
  <div class="card shadow-sm">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Activity Logs</h5>
      <div>
        <select id="limit" class="form-select form-select-sm d-inline-block" style="width:auto">
          <option value="100">Last 100</option>
          <option value="200" selected>Last 200</option>
          <option value="500">Last 500</option>
        </select>
        <button class="btn btn-light btn-sm ms-2" id="refreshLogs"><i class="fas fa-rotate"></i></button>
      </div>
    </div>
    <div class="card-body">
      <div class="row g-2 mb-3">
        <div class="col-6 col-md-2"><input type="date" id="start" class="form-control form-control-sm" /></div>
        <div class="col-6 col-md-2"><input type="date" id="end" class="form-control form-control-sm" /></div>
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
              <th>Session Start</th>
              <th>Admin</th>
              <th>Actions (click for details)</th>
              <th>Logout Time</th>
              <th>Location/IP</th>
              <th>Device</th>
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
      action: $('#action').val(),
      method: $('#method').val(),
      q: $('#q').val()
    };
  }
  function loadLogs(){
    $.getJSON('<?= site_url('admin/getLogs') ?>', buildParams()).done(function(rows){
      const $tb = $('#logsTable tbody');
      $tb.empty();
      if(!rows || !rows.length){ $tb.append('<tr><td colspan="6" class="text-center text-muted">No logs found</td></tr>'); return; }
      rows.forEach(function(r, idx){
        // Parse actions timeline
        let actions = [];
        try { actions = r.details ? JSON.parse(r.details) : []; } catch(e) { actions = []; }
        let actionSummary = actions.length ? actions.map(a => a.action).join(', ') : (r.action||'login');
        let readable = '';
        if(actions.length) {
          readable = '<ul class="list-unstyled mb-0">';
          actions.forEach(function(a, i){
            readable += `<li><span class="fw-semibold">${fmt(a.time)}</span>: <span>${friendlyAction(a)}</span></li>`;
          });
          readable += '</ul>';
        } else {
          readable = '<em>No actions recorded</em>';
        }
        $tb.append(`
          <tr>
            <td>${fmt(r.created_at)}</td>
            <td>${r.actor_type === 'admin' ? 'Admin #' + r.actor_id : r.actor_type}</td>
            <td><button class="btn btn-sm btn-outline-primary view-details" data-idx="${idx}">View Details (${actions.length})</button></td>
            <td>${fmt(r.logged_out_at)}</td>
            <td title="IP address">${r.ip_address||''}</td>
            <td title="Device info">${(r.user_agent||'').slice(0,40)}${(r.user_agent||'').length>40?'…':''}</td>
          </tr>`);
        r._readable = readable;
      });
      // Store for modal
      window._adminLogsRows = rows;
    }).fail(()=>$('#logsAlerts').html('<div class="alert alert-danger">Failed to load logs</div>'));
  }

  // Friendly action text
  function friendlyAction(a){
    if(!a || !a.action) return '';
    let what = '';
    if (a.resource) {
      what = ` <span class="text-info">${a.resource}</span>`;
    }
    let extra = '';
    if (a.details) {
      try {
        const d = typeof a.details === 'string' ? JSON.parse(a.details) : a.details;
        console.log('Log details for action:', a.action, d);
        if (d && typeof d === 'object') {
          // Prefer combined user_name if present (set by ActivityLogger), fall back to first_name/last_name or id
          if (d.user_name) {
            extra += ` (User: <span class="text-primary">${d.user_name}</span>)`;
          } else if (d.first_name || d.last_name) {
            extra += ` (User: <span class="text-primary">${(d.first_name||'') + (d.last_name ? ' ' + d.last_name : '')}</span>)`;
          } else if (d.id) {
            extra += ` (User ID: <span class="text-muted">${d.id}</span>)`;
          }
          if (d.bill_id) extra += ` (Bill: <span class="text-primary">${d.bill_id}</span>)`;
          if (d.receipt_id) extra += ` (Receipt: <span class="text-primary">${d.receipt_id}</span>)`;
        }
      } catch(e) {}
    }
    switch(a.action){
      case 'login': return 'Logged in';
      case 'logout': return 'Logged out';
      case 'create': return `Created${what}${extra}`;
      case 'edit': return `Edited${what}${extra}`;
      case 'delete': return `Deleted${what}${extra}`;
      case 'activate': return `Activated${what}${extra}`;
      case 'deactivate': return `Deactivated${what}${extra}`;
      default: return a.action.charAt(0).toUpperCase() + a.action.slice(1) + what + extra;
    }
  }

  // Details modal
  $(document).on('click', '.view-details', function(){
    const idx = $(this).data('idx');
    const row = window._adminLogsRows && window._adminLogsRows[idx];
    if(!row) return;
    $('#detailsModalBody').html(row._readable || '<em>No details</em>');
    $('#detailsModalTime').text(fmt(row.created_at));
    $('#detailsModalAdmin').text(row.actor_type === 'admin' ? 'Admin #' + row.actor_id : row.actor_type);
    $('#detailsModalLogout').text(fmt(row.logged_out_at));
    new bootstrap.Modal(document.getElementById('detailsModal')).show();
  });
  $('#refreshLogs, #limit, #applyFilters').on('click change', loadLogs);
  $('#q').on('keypress', function(e){ if(e.which===13){ loadLogs(); }});

  // Export CSV
  $(document).on('click', '#exportCsv', function(){
    const p = buildParams();
    const parts = [];
    const datePart = (p.start || p.end) ? `${p.start || '...'} to ${p.end || '...'}` : 'All dates';
    parts.push(`<strong>Date:</strong> ${datePart}`);
    parts.push(`<strong>Limit:</strong> ${p.limit || '200'}`);
    if (p.action) parts.push(`<strong>Action:</strong> ${p.action}`);
    if (p.method) parts.push(`<strong>Method:</strong> ${p.method}`);
    if (p.q) parts.push(`<strong>Search:</strong> ${$('<div/>').text(p.q).html()}`);
    $('#exportSummary').html(parts.join(' &nbsp;|&nbsp; '));
    new bootstrap.Modal(document.getElementById('exportConfirmModal')).show();
  });

  // Confirm export
  $(document).on('click', '#confirmExportBtn', function(){
    const p = buildParams();
    const qs = new URLSearchParams(p).toString();
    window.open('<?= site_url('admin/exportLogs') ?>' + '?' + qs, '_blank');
    const m = bootstrap.Modal.getInstance(document.getElementById('exportConfirmModal'));
    if (m) m.hide();
  });
  loadLogs();
})();
</script>
<div class="position-sticky bottom-0 p-2">
  <button class="btn btn-outline-secondary btn-sm" id="exportCsv"><i class="fas fa-file-csv me-1"></i>Export CSV</button>
</div>

<!-- Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailsModalLabel">Session Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-2"><strong>Session Start:</strong> <span id="detailsModalTime"></span></div>
        <div class="mb-2"><strong>Admin:</strong> <span id="detailsModalAdmin"></span></div>
        <div class="mb-2"><strong>Logout Time:</strong> <span id="detailsModalLogout"></span></div>
        <hr>
        <div id="detailsModalBody"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Export Confirmation Modal -->
<div class="modal fade" id="exportConfirmModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Export Activity Logs</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="mb-2">Export the current logs with applied filters as CSV? This is read-only and does not modify any data.</p>
        <div id="exportSummary" class="small text-muted"></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" id="confirmExportBtn"><i class="fas fa-file-export me-1"></i>Confirm Export</button>
      </div>
    </div>
  </div>
</div>