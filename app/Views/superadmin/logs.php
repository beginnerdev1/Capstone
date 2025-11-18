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
              <th>Details</th>
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
  function fmt(dt){
    if(!dt) return '';
    // Normalize and parse safely. Expecting 'YYYY-MM-DD HH:MM:SS' or ISO variants.
    const s = dt.replace(' ', 'T');
    const d = new Date(s);
    if (isNaN(d.getTime())) return '';
    const pad = (n) => String(n).padStart(2, '0');
    const mm = pad(d.getMonth() + 1);
    const dd = pad(d.getDate());
    const yyyy = d.getFullYear();
    const hh = pad(d.getHours());
    const mi = pad(d.getMinutes());
    const ss = pad(d.getSeconds());
    return `${mm}/${dd}/${yyyy} ${hh}:${mi}:${ss}`;
  }
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
        if(!rows || !rows.length){ $tb.append('<tr><td colspan="10" class="text-center text-muted">No logs</td></tr>'); return; }

        // Collect unique actor pairs to resolve names
        const actors = {};
        rows.forEach(r=>{ actors[r.actor_type+'#'+r.actor_id] = {type:r.actor_type,id:r.actor_id}; });

        // Request all actor display names in a single batch
        const actorKeys = Object.keys(actors);
        const actorPayload = actorKeys.map(k => ({ type: actors[k].type, id: actors[k].id }));

        const actorMapPromise = $.ajax({
          url: '<?= site_url('superadmin/getActorDisplays') ?>',
          method: 'POST',
          contentType: 'application/json',
          data: JSON.stringify({ actors: actorPayload }),
        }).then(function(resp){
          return (resp && resp.status === 'success' && resp.map) ? resp.map : {};
        }).catch(function(){ return {}; });

        // Render rows now; we'll replace actor cells when map resolves
        rows.forEach(r=>{
          const key = r.actor_type+'#'+r.actor_id;
          const detailsSnippet = (r.details||'') ? $('<div/>').text((r.details||'')).html().slice(0,80) + ((r.details||'').length>80?'…':'') : '';
          $tb.append(`
            <tr data-log='${$('<div/>').text(JSON.stringify(r)).html()}'>
              <td>${fmt(r.created_at)}</td>
              <td class="actor-cell">${r.actor_type}#${r.actor_id}</td>
              <td>${r.action}</td>
              <td><code>${r.route||''}</code></td>
              <td>${r.resource||''}</td>
              <td>${r.ip_address||''}</td>
              <td title="${r.user_agent||''}">${(r.user_agent||'').slice(0,40)}${(r.user_agent||'').length>40?'…':''}</td>
              <td><button class="btn btn-sm btn-outline-secondary view-details">View</button></td>
              <td>${fmt(r.logged_out_at)}</td>
            </tr>`);
        });

        // When actor map resolves, update actor cells
        actorMapPromise.then(function(map){
          $('#logsTable tbody tr').each(function(){
            const $tr = $(this);
            const r = JSON.parse($tr.attr('data-log'));
            const key = r.actor_type+'#'+r.actor_id;
            const display = map[key] || key;
            $tr.find('.actor-cell').text(display);
          });
        });

        // attach details handler
        $('.view-details').off('click').on('click', function(){
          const r = JSON.parse($(this).closest('tr').attr('data-log'));
          const html = `
            <dl class="row">
              <dt class="col-sm-3">Time</dt><dd class="col-sm-9">${fmt(r.created_at)}</dd>
              <dt class="col-sm-3">Actor</dt><dd class="col-sm-9">${r.actor_type}#${r.actor_id}</dd>
              <dt class="col-sm-3">Action</dt><dd class="col-sm-9">${r.action}</dd>
              <dt class="col-sm-3">Route</dt><dd class="col-sm-9"><code>${r.route||''}</code></dd>
              <dt class="col-sm-3">Resource</dt><dd class="col-sm-9">${r.resource||''}</dd>
              <dt class="col-sm-3">IP</dt><dd class="col-sm-9">${r.ip_address||''}</dd>
              <dt class="col-sm-3">User Agent</dt><dd class="col-sm-9">${$('<div/>').text(r.user_agent||'').html()}</dd>
              <dt class="col-sm-3">Details</dt><dd class="col-sm-9"><pre style="white-space:pre-wrap">${$('<div/>').text(r.details||'').html()}</pre></dd>
              <dt class="col-sm-3">Logged Out</dt><dd class="col-sm-9">${fmt(r.logged_out_at)}</dd>
            </dl>`;
          $('#logDetailsBody').html(html);
          new bootstrap.Modal(document.getElementById('logDetailsModal')).show();
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
<!-- Log Details Modal -->
<div class="modal fade" id="logDetailsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Log Details</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body" id="logDetailsBody"></div>
      <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Close</button></div>
    </div>
  </div>
</div>
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