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
              <th style="width:48px">#</th>
              <th style="width:160px">Time</th>
              <th style="width:220px">Actor</th>
              <th>Action</th>
              <th>Route</th>
              <th style="width:120px">Resource</th>
              <th style="width:110px">IP</th>
              <th style="width:220px">User Agent</th>
              <th style="width:80px">Details</th>
              <th style="width:140px">Logout</th>
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
    const s = String(dt).replace(' ', 'T');
    const d = new Date(s);
    if (isNaN(d.getTime())) return '';
    const pad = (n) => String(n).padStart(2, '0');
    return `${pad(d.getMonth()+1)}/${pad(d.getDate())}/${d.getFullYear()} ${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())}`;
  }

  function friendlyAction(a){
    if(!a) return '';
    const act = (a.action || '').toLowerCase();
    let what = act.charAt(0).toUpperCase() + act.slice(1);
    if (a.resource) what += ' — ' + a.resource;
    // include key details if present
    try{
      const d = typeof a.details === 'string' ? JSON.parse(a.details || '{}') : (a.details || {});
      if (d && typeof d === 'object'){
        if (d.user_name) what += ` (User: ${d.user_name})`;
        else if (d.first_name || d.last_name) what += ` (User: ${((d.first_name||'') + ' ' + (d.last_name||'')).trim()})`;
        if (d.bill_id) what += ` (Bill:${d.bill_id})`;
      }
    }catch(e){}
    return what;
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

        // Render rows now with index. Prefer server-provided readable fields if present.
        rows.forEach(function(r, idx){
          const actorDisplay = r.actor_display || (r.actor_type + '#' + r.actor_id);
          const actionLabel = r.human_action || friendlyAction(r);
          const detailsLabel = r.human_details || '';
          const noteLabel = r.human_note || '';
          $tb.append(`
            <tr data-log='${$('<div/>').text(JSON.stringify(r)).html()}'>
              <td>${idx+1}</td>
              <td>${fmt(r.created_at)}</td>
              <td class="actor-cell">${$('<div/>').text(actorDisplay).html()}</td>
              <td>${$('<div/>').text(actionLabel).html()}</td>
              <td><code>${$('<div/>').text(r.route||'').html()}</code></td>
              <td>${$('<div/>').text(r.resource||'').html()}</td>
              <td>${$('<div/>').text(r.ip_address||'').html()}</td>
              <td title="${$('<div/>').text(r.user_agent||'').html()}">${$('<div/>').text((r.user_agent||'').slice(0,40)).html()}${(r.user_agent||'').length>40?'…':''}</td>
              <td>
                <button class="btn btn-sm btn-outline-secondary view-details">View</button>
                <div class="small text-muted mt-1">${$('<div/>').text(detailsLabel).html()}</div>
              </td>
              <td>${fmt(r.logged_out_at)}${noteLabel?('<div class="small text-warning">'+$('<div/>').text(noteLabel).html()+'</div>'):''}</td>
            </tr>`);
        });

        // attach details handler
        $('.view-details').off('click').on('click', function(){
          const r = JSON.parse($(this).closest('tr').attr('data-log'));
          const key = r.actor_type + '#' + r.actor_id;
          const actorDisplay = (window._actorMap && window._actorMap[key]) ? window._actorMap[key] : key;
          // Try to format JSON details into a readable key/value layout for non-technical users
          let detailsHtml = '';
          try{
            const parsed = JSON.parse(r.details || '{}');
            function prettyKey(k){ return String(k).replace(/_/g,' ').replace(/\b\w/g, function(m){ return m.toUpperCase(); }); }
            function maskKey(k, v){
              const lk = String(k).toLowerCase();
              if (lk.includes('csrf') || lk.includes('token')) return '[hidden]';
              if (lk.includes('code') && typeof v === 'string' && v.length > 4) return '•••' + v.slice(-3);
              return v === null || v === undefined ? '' : v;
            }

            if (parsed && typeof parsed === 'object' && !Array.isArray(parsed)){
              // render as definition list of key: value
              let parts = '';
              for (const key in parsed){
                if (!Object.prototype.hasOwnProperty.call(parsed, key)) continue;
                const raw = parsed[key];
                const val = maskKey(key, raw);
                parts += `<dt class="col-sm-4">${$('<div/>').text(prettyKey(key)).html()}</dt><dd class="col-sm-8">${$('<div/>').text(String(val)).html()}</dd>`;
              }
              detailsHtml = `<dl class="row">${parts}</dl>`;
            } else if (Array.isArray(parsed)){
              if (parsed.length > 0 && typeof parsed[0] === 'object'){
                // table for array of objects
                const allKeys = Array.from(parsed.reduce((s,o)=>{ Object.keys(o||{}).forEach(k=>s.add(k)); return s; }, new Set()));
                const thead = '<tr>' + allKeys.map(k=>`<th>${$('<div/>').text(prettyKey(k)).html()}</th>`).join('') + '</tr>';
                const rowsHtml = parsed.map(rowObj=>{
                  return '<tr>' + allKeys.map(k=>`<td>${$('<div/>').text(String(maskKey(k, rowObj[k]===undefined?'':rowObj[k]))).html()}</td>`).join('') + '</tr>';
                }).join('');
                detailsHtml = `<div class="table-responsive"><table class="table table-sm table-bordered mb-0"><thead>${thead}</thead><tbody>${rowsHtml}</tbody></table></div>`;
              } else {
                detailsHtml = '<div>' + $('<div/>').text(parsed.join(', ')).html() + '</div>';
              }
            } else {
              detailsHtml = '<div>' + $('<div/>').text(String(parsed)).html() + '</div>';
            }
          }catch(e){
            detailsHtml = '<pre style="white-space:pre-wrap">' + $('<div/>').text(r.details || '').html() + '</pre>';
          }
          const html = `
            <dl class="row">
              <dt class="col-sm-3">Time</dt><dd class="col-sm-9">${fmt(r.created_at)}</dd>
              <dt class="col-sm-3">Actor</dt><dd class="col-sm-9">${$('<div/>').text(actorDisplay).html()}</dd>
              <dt class="col-sm-3">Action</dt><dd class="col-sm-9">${$('<div/>').text(friendlyAction(r)).html()}</dd>
              <dt class="col-sm-3">Route</dt><dd class="col-sm-9"><code>${$('<div/>').text(r.route||'').html()}</code></dd>
              <dt class="col-sm-3">Resource</dt><dd class="col-sm-9">${$('<div/>').text(r.resource||'').html()}</dd>
              <dt class="col-sm-3">IP</dt><dd class="col-sm-9">${$('<div/>').text(r.ip_address||'').html()}</dd>
              <dt class="col-sm-3">User Agent</dt><dd class="col-sm-9">${$('<div/>').text(r.user_agent||'').html()}</dd>
              <dt class="col-sm-3">Details</dt><dd class="col-sm-9">${detailsHtml}</dd>
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