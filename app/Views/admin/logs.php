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
        <div class="col-6 col-md-2">
          <label for="start" class="form-label small mb-1">From: </label>
          <input type="date" id="start" class="form-control form-control-sm" />
        </div>
        <div class="col-6 col-md-2">
          <label for="end" class="form-label small mb-1">Until: </label>
          <input type="date" id="end" class="form-control form-control-sm" />
        </div>
        <div class="col-6 col-md-2">
          <label for="action" class="form-label small mb-1">Activity</label>
          <select id="action" class="form-select form-select-sm">
            <option value="">All activities</option>
            <option value="login">Signed in</option>
            <option value="create">Added / Created</option>
            <option value="edit">Edited / Updated</option>
            <option value="delete">Removed / Deleted</option>
            <option value="activate">Activated / Re-enabled</option>
            <option value="deactivate">Deactivated / Suspended</option>
          </select>
        </div>
        <div class="col-6 col-md-2">
          <label for="method" class="form-label small mb-1">Request type</label>
          <select id="method" class="form-select form-select-sm">
            <option value="">Any request type</option>
            <option value="POST">Submit (POST)</option>
            <option value="PUT">Replace (PUT)</option>
            <option value="PATCH">Modify (PATCH)</option>
            <option value="DELETE">Remove (DELETE)</option>
          </select>
        </div>
        <div class="col-12 col-md-3">
          <label for="q" class="form-label small mb-1">Admin (who performed action)</label>
          <input type="text" id="q" class="form-control form-control-sm" placeholder="Type admin first or last name" />
        </div>
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
        const adminName = r.actor_name ? r.actor_name : (r.actor_type === 'admin' ? r.actor_type : r.actor_type);
        $tb.append(`
          <tr>
            <td>${fmt(r.created_at)}</td>
            <td>${$('<div/>').text(adminName).html()}</td>
            <td>
              <button class="btn btn-sm btn-outline-primary view-details" data-idx="${idx}">View Details (${actions.length})</button>
              <select class="form-select form-select-sm export-format-row ms-2" data-idx="${idx}" style="width:auto;display:inline-block;vertical-align:middle">
                <option value="">Use global</option>
                <option value="csv">CSV</option>
                <option value="pdf">PDF</option>
                <option value="xlsx">XLSX</option>
              </select>
              <button class="btn btn-sm btn-outline-secondary ms-2 export-log-btn" data-id="${r.id}" data-idx="${idx}"><i class="fas fa-file-csv me-1"></i>Export</button>
            </td>
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

  // Query server for available export formats and disable unsupported options
  async function loadExportAvailability(){
    try {
      const res = await fetch('<?= site_url('admin/exportAvailability') ?>', { credentials: 'same-origin' });
      if (!res.ok) return;
      const avail = await res.json();
      if (!avail.pdf) {
        $('#exportFormat option[value="pdf"]').prop('disabled', true).text('PDF (server - unavailable)');
        $('.export-format-row option[value="pdf"]').prop('disabled', true).text('PDF (unavailable)');
      }
      if (!avail.xlsx) {
        $('#exportFormat option[value="xlsx"]').prop('disabled', true).text('XLSX (server - unavailable)');
        $('.export-format-row option[value="xlsx"]').prop('disabled', true).text('XLSX (unavailable)');
      }
    } catch (e) {
      // ignore
    }
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
    const modalAdminName = row.actor_name ? row.actor_name : row.actor_type;
    $('#detailsModalAdmin').text(modalAdminName);
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
    // include format in summary
    const fmt = $('#exportFormat').val() || 'csv';
    parts.push(`<strong>Format:</strong> ${fmt.toUpperCase()}`);
    $('#exportSummary').html(parts.join(' &nbsp;|&nbsp; '));
    new bootstrap.Modal(document.getElementById('exportConfirmModal')).show();
  });

  // Confirm export — fetch CSV with credentials and trigger download to avoid permission issues
  $(document).on('click', '#confirmExportBtn', async function(){
    const p = buildParams();
    const fmt = $('#exportFormat').val() || 'csv';
    p.format = fmt;
    const qs = new URLSearchParams(p).toString();
    const url = '<?= site_url('admin/exportLogs') ?>' + '?' + qs;
    try {
      const res = await fetch(url, { credentials: 'same-origin' });
      if (!res.ok) {
        const txt = await res.text();
        $('#logsAlerts').html('<div class="alert alert-danger">Export failed: ' + (res.statusText || 'Server error') + '</div>');
        console.error('Export failed', res.status, txt);
        return;
      }
      const ctype = (res.headers.get('content-type') || '').toLowerCase();
      // Validate server returned the requested format
      if (fmt === 'pdf' && ctype.indexOf('application/pdf') === -1) {
        const txt = await res.text();
        $('#logsAlerts').html('<div class="alert alert-danger">Server did not return a PDF. ' + (txt || 'Missing Dompdf on server?') + '</div>');
        console.error('Export returned non-PDF:', ctype, txt);
        return;
      }
      if (fmt === 'xlsx' && ctype.indexOf('sheet') === -1 && ctype.indexOf('excel') === -1) {
        const txt = await res.text();
        $('#logsAlerts').html('<div class="alert alert-danger">Server did not return an XLSX file. ' + (txt || 'Missing PhpSpreadsheet on server?') + '</div>');
        console.error('Export returned non-XLSX:', ctype, txt);
        return;
      }
      const blob = await res.blob();
      const ext = (fmt === 'xlsx' ? '.xlsx' : (fmt === 'pdf' ? '.pdf' : '.csv'));
      const filename = 'admin-activity-logs-' + new Date().toISOString().slice(0,19).replace(/[:T]/g,'-') + ext;
      const link = document.createElement('a');
      link.href = URL.createObjectURL(blob);
      link.download = filename;
      document.body.appendChild(link);
      link.click();
      link.remove();
      URL.revokeObjectURL(link.href);
      // Show success alert
      $('#logsAlerts').html('<div class="alert alert-success">' + fmt.toUpperCase() + ' downloaded</div>');
      setTimeout(()=>$('#logsAlerts').fadeOut(400,function(){ $(this).html('').show(); }), 3000);
    } catch (err) {
      console.error('Export error', err);
      $('#logsAlerts').html('<div class="alert alert-danger">Failed to download CSV. See console for details.</div>');
    } finally {
      const m = bootstrap.Modal.getInstance(document.getElementById('exportConfirmModal'));
      if (m) m.hide();
    }
  });

  // Export single log (per-row)
  $(document).on('click', '.export-log-btn', async function(){
    const id = $(this).data('id');
    const idx = $(this).data('idx');
    if (!id) return;
    // prefer per-row selection, otherwise use global
    const rowFmt = $(`.export-format-row[data-idx="${idx}"]`).val();
    const fmt = rowFmt || $('#exportFormat').val() || 'csv';
    const url = '<?= site_url('admin/exportLogs') ?>' + '?log_id=' + encodeURIComponent(id) + '&format=' + encodeURIComponent(fmt);
    try {
      const res = await fetch(url, { credentials: 'same-origin' });
      if (!res.ok) {
        const txt = await res.text();
        $('#logsAlerts').html('<div class="alert alert-danger">Export failed: ' + (res.statusText || 'Server error') + '</div>');
        console.error('Export failed', res.status, txt);
        return;
      }
      const ctype = (res.headers.get('content-type') || '').toLowerCase();
      if (fmt === 'pdf' && ctype.indexOf('application/pdf') === -1) {
        const txt = await res.text();
        $('#logsAlerts').html('<div class="alert alert-danger">Server did not return a PDF. ' + (txt || 'Missing Dompdf on server?') + '</div>');
        console.error('Export returned non-PDF:', ctype, txt);
        return;
      }
      if (fmt === 'xlsx' && ctype.indexOf('sheet') === -1 && ctype.indexOf('excel') === -1) {
        const txt = await res.text();
        $('#logsAlerts').html('<div class="alert alert-danger">Server did not return an XLSX file. ' + (txt || 'Missing PhpSpreadsheet on server?') + '</div>');
        console.error('Export returned non-XLSX:', ctype, txt);
        return;
      }
      const blob = await res.blob();
      let performer = '';
      try { if (window._adminLogsRows && typeof idx !== 'undefined') performer = window._adminLogsRows[idx] && (window._adminLogsRows[idx].actor_name || '') } catch(e){}
      performer = performer ? performer.replace(/[^a-z0-9]+/ig, '_') : 'unknown';
      const ext = (fmt === 'xlsx' ? '.xlsx' : (fmt === 'pdf' ? '.pdf' : '.csv'));
      const filename = 'performed-by_' + performer + '_log-' + id + '-' + new Date().toISOString().slice(0,19).replace(/[:T]/g,'-') + ext;
      const link = document.createElement('a');
      link.href = URL.createObjectURL(blob);
      link.download = filename;
      document.body.appendChild(link);
      link.click();
      link.remove();
      URL.revokeObjectURL(link.href);
      // Show success alert
      $('#logsAlerts').html('<div class="alert alert-success">' + (fmt||'CSV').toUpperCase() + ' downloaded</div>');
      setTimeout(()=>$('#logsAlerts').fadeOut(400,function(){ $(this).html('').show(); }), 3000);
    } catch (err) {
      console.error('Export error', err);
      $('#logsAlerts').html('<div class="alert alert-danger">Failed to download CSV. See console for details.</div>');
    }
  });
  loadLogs();
  loadExportAvailability();
})();
</script>
  <div class="position-sticky bottom-0 p-2 d-flex gap-2 align-items-center">
    <select id="exportFormat" class="form-select form-select-sm" style="width:auto">
      <option value="csv">CSV</option>
      <option value="pdf">PDF (server)</option>
      <option value="xlsx">XLSX</option>
    </select>
    <button class="btn btn-outline-secondary btn-sm" id="exportCsv"><i class="fas fa-file-csv me-1"></i>Export All</button>
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
        <p class="mb-2">Export the current logs with applied filters in the selected format. This is read-only and does not modify any data.</p>
        <div id="exportSummary" class="small text-muted"></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" id="confirmExportBtn"><i class="fas fa-file-export me-1"></i>Confirm Export</button>
      </div>
    </div>
  </div>
</div>