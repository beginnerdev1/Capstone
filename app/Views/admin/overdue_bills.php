<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');
:root { --primary: #667eea; --muted: #6b7280; --border: #e5e7eb; --light: #f9fafb; --dark:#1f2937; }
body { font-family: 'Poppins', system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; }
.payments-wrapper { max-width: 1200px; margin: 20px auto; padding: 16px; }
.card { background: #fff; border-radius: 12px; border: 1px solid var(--border); box-shadow: 0 8px 24px rgba(15,23,42,0.04); }
.card-body { padding: 14px; }
.filters { display:flex; gap:8px; align-items:center; flex-wrap:wrap; }
.filters .form-control { min-width:160px; }
.table-container { margin-top:12px; overflow:auto; }
table { width:100%; border-collapse:collapse; }
th, td { padding:12px 10px; border-bottom:1px solid var(--border); text-align:left; }
th { font-weight:700; background:var(--light); text-transform:uppercase; font-size:12px; }
@media(max-width:820px){ thead { display:none; } tbody tr { display:block; margin-bottom:12px; border:1px solid var(--border); border-radius:8px; padding:10px; } td { display:flex; justify-content:space-between; padding:8px; } td:before { content: attr(data-label); color:var(--muted); font-weight:700; margin-right:8px; }
}
.actions { text-align:right; }
.small-muted { color:var(--muted); font-size:0.9rem; }
.pager { display:flex; align-items:center; gap:8px; margin-top:12px; }
.btn-sm { padding:6px 10px; font-size:13px; }
</style>

<div class="payments-wrapper">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h4 class="mb-0">Overdue Bills</h4>
      <div class="small-muted">Transactions with status = "overdue"</div>
    </div>
    <div class="d-flex gap-2 align-items-center">
      <input id="overdueQuickSearch" placeholder="Search bill no, name or email" class="form-control form-control-sm" style="width:260px;" />
      <button id="refreshOverdue" class="btn btn-outline btn-sm">Refresh</button>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <form id="overdueFilters" class="filters" onsubmit="return false;">
        <input type="text" name="search" class="form-control" placeholder="Search" />
        <input type="date" name="from" class="form-control" />
        <input type="date" name="to" class="form-control" />
        <input type="number" name="min_days" class="form-control" placeholder="Min days overdue" />
        <select name="per_page" id="perPage" class="form-control">
          <option value="10">10 / page</option>
          <option value="20" selected>20 / page</option>
          <option value="50">50 / page</option>
        </select>
        <div style="margin-left:auto;">
          <button id="applyOverdueFilters" class="btn btn-primary btn-sm">Apply</button>
          <button id="resetOverdueFilters" class="btn btn-outline btn-sm">Reset</button>
        </div>
      </form>
    </div>
  </div>

  <div class="card table-container mt-3">
    <div class="card-body">
      <div class="table-responsive">
        <table id="overdueTable">
          <thead>
            <tr>
              <th style="width:36px;"><input type="checkbox" id="selectAll" aria-label="Select all" /></th>
              <th>#</th>
              <th>Bill #</th>
              <th>User</th>
              <th>Billing</th>
              <th>Due</th>
              <th>Amount</th>
              <th>Days Overdue</th>
              <th class="actions">Actions</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>

      <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="small-muted">Showing <span id="showingCount">0</span> of <span id="totalCount">0</span></div>
        <div style="display:flex; gap:12px; align-items:center;">
          <div id="bulkToolbar" style="display:none; align-items:center; gap:8px;">
            <button id="bulkMarkPaid" class="btn btn-sm btn-primary" disabled>Mark Selected Paid</button>
            <span class="small-muted" id="selectedCount">0 selected</span>
          </div>
          <div class="pager" id="billsPager">
            <button class="btn btn-sm" id="prevPage">Prev</button>
            <div id="pageLinks" style="display:flex; gap:6px; align-items:center;"></div>
            <button class="btn btn-sm" id="nextPage">Next</button>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>

<!-- Bill details modal removed: view action omitted (most data shown in table) -->

<script>
(() => {
  const API = '<?= site_url('admin/getOverdueBills') ?>';
  const MARK_PAID = '<?= site_url('admin/markBillPaid') ?>';
  const billTbody = document.querySelector('#overdueTable tbody');
  const perPageEl = document.getElementById('perPage');
  let page = 1, perPage = parseInt(perPageEl.value || 20, 10);

  function qs(params){ return Object.keys(params).map(k=>encodeURIComponent(k)+'='+encodeURIComponent(params[k])).join('&'); }

  function formatCurrency(v){ return Number(v||0).toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2}); }

  function renderRows(list, meta){
    // clear tbody
    while (billTbody.firstChild) billTbody.removeChild(billTbody.firstChild);
    if(!Array.isArray(list) || list.length===0){
      const tr = document.createElement('tr');
      const td = document.createElement('td');
      td.setAttribute('colspan','9');
      td.className = 'text-center small-muted';
      td.style.padding = '28px';
      td.textContent = 'No overdue bills';
      tr.appendChild(td);
      billTbody.appendChild(tr);
      const showingEl = document.getElementById('showingCount'); if(showingEl) showingEl.textContent = 0;
      const totalEl = document.getElementById('totalCount'); if(totalEl) totalEl.textContent = (meta && meta.total) || 0;
      return;
    }

    list.forEach((r, idx) => {
      const user = ((r.first_name||'') + ' ' + (r.last_name||'')).trim() || (r.email||'');
      const tr = document.createElement('tr');

      const makeTd = (label, text) => {
        const td = document.createElement('td');
        td.setAttribute('data-label', label);
        td.textContent = text;
        return td;
      };

      // select checkbox
      const tdSelect = document.createElement('td');
      tdSelect.setAttribute('data-label','Select');
      const cb = document.createElement('input');
      cb.type = 'checkbox'; cb.className = 'row-select'; cb.setAttribute('data-id', String(r.id));
      tdSelect.appendChild(cb);
      tr.appendChild(tdSelect);

      const indexNum = ((meta && meta.page?((meta.page-1)*meta.per_page):((page-1)*perPage))+idx+1);
      tr.appendChild(makeTd('#', indexNum));
      tr.appendChild(makeTd('Bill #', r.bill_no||''));
      tr.appendChild(makeTd('User', user));
      tr.appendChild(makeTd('Billing', r.billing_month||''));
      tr.appendChild(makeTd('Due', r.due_date||''));
      tr.appendChild(makeTd('Amount', '₱' + formatCurrency(r.amount_due||r.amount||0)));
      tr.appendChild(makeTd('Days Overdue', String(r.days_overdue||0)));

      // actions (no "View" button — most info is in the row)
      const tdActions = document.createElement('td');
      tdActions.setAttribute('data-label','Actions'); tdActions.className = 'actions';
      const markBtn = document.createElement('button'); markBtn.className = 'btn btn-sm btn-primary ms-1 mark-paid'; markBtn.setAttribute('data-id', String(r.id)); markBtn.textContent = 'Mark Paid';
      tdActions.appendChild(markBtn);
      tr.appendChild(tdActions);

      billTbody.appendChild(tr);
    });

    const showingEl = document.getElementById('showingCount'); if(showingEl) showingEl.textContent = String(list.length);
    const totalEl = document.getElementById('totalCount'); if(totalEl) totalEl.textContent = String((meta && meta.total) || list.length);
    const currentPageEl = document.getElementById('currentPage'); if(currentPageEl) currentPageEl.textContent = String((meta && meta.page) || page);
  }

  // render numbered pagination links
  function renderPagination(meta){
    const pageLinks = document.getElementById('pageLinks');
    while (pageLinks.firstChild) pageLinks.removeChild(pageLinks.firstChild);
    if(!meta || !meta.total) return;
    const total = parseInt(meta.total,10) || 0;
    const per = parseInt(meta.per_page || meta.perPage || perPage,10) || perPage;
    const current = parseInt(meta.page || page,10) || page;
    const totalPages = Math.max(1, Math.ceil(total / per));

    // determine window of page links (max 7)
    const maxLinks = 7;
    let start = Math.max(1, current - Math.floor(maxLinks/2));
    let end = start + maxLinks - 1;
    if(end > totalPages){ end = totalPages; start = Math.max(1, end - maxLinks + 1); }

    for(let p = start; p <= end; p++){
      const btn = document.createElement('button');
      btn.className = 'btn btn-sm';
      if(p === current) btn.classList.add('active');
      btn.textContent = p;
      btn.setAttribute('data-page', p);
      btn.addEventListener('click', () => { page = p; loadOverdue(); });
      pageLinks.appendChild(btn);
    }
    // enable/disable prev/next
    document.getElementById('prevPage').disabled = (current <= 1);
    document.getElementById('nextPage').disabled = (current >= totalPages);
  }

  async function loadOverdue(){
    perPage = parseInt(perPageEl.value||20,10);
    const form = document.getElementById('overdueFilters');
    const params = new FormData(form);
    params.append('page', page);
    params.append('limit', perPage);
    // enforce only overdue status
    params.append('status', 'overdue');

    const query = qs(Object.fromEntries(params.entries()));
    try{
      const res = await fetch(API + '?' + query, { headers: { 'X-Requested-With':'XMLHttpRequest' } });
      const json = await res.json();
      // support both {data,meta} and plain array
      const rows = Array.isArray(json) ? json : (json.data || []);
      const meta = json.meta || (json.length? { total: json.length, page: page, per_page: perPage } : {});
      renderRows(rows, meta);
      renderPagination(meta);
    }catch(err){
      console.error('Failed to load overdue bills', err);
      // show failure row
      while (billTbody.firstChild) billTbody.removeChild(billTbody.firstChild);
      const tr = document.createElement('tr');
      const td = document.createElement('td'); td.setAttribute('colspan','9'); td.className='text-center text-danger'; td.textContent = 'Failed to load data';
      tr.appendChild(td); billTbody.appendChild(tr);
    }
  }

  // selection / bulk handlers
  const selectedIds = new Set();
  const selectAllEl = document.getElementById('selectAll');
  const bulkToolbar = document.getElementById('bulkToolbar');
  const bulkMarkBtn = document.getElementById('bulkMarkPaid');
  const selectedCountEl = document.getElementById('selectedCount');

  function updateBulkToolbar(){
    const n = selectedIds.size;
    selectedCountEl.textContent = n + ' selected';
    bulkMarkBtn.disabled = (n === 0);
    bulkToolbar.style.display = (n>0)?'flex':'none';
  }

  selectAllEl.addEventListener('change', function(){
    const checks = document.querySelectorAll('#overdueTable tbody input.row-select');
    if(this.checked){
      checks.forEach(cb => { cb.checked = true; selectedIds.add(cb.getAttribute('data-id')); });
    } else {
      checks.forEach(cb => { cb.checked = false; selectedIds.delete(cb.getAttribute('data-id')); });
    }
    updateBulkToolbar();
  });

  // handlers
  document.addEventListener('click', async (e) => {
    // row checkbox clicks (delegate)
    if(e.target.matches('#overdueTable tbody input.row-select')){
      const id = e.target.getAttribute('data-id');
      if(e.target.checked) selectedIds.add(id); else selectedIds.delete(id);
      // sync header checkbox
      const allChecks = document.querySelectorAll('#overdueTable tbody input.row-select');
      const checked = document.querySelectorAll('#overdueTable tbody input.row-select:checked');
      selectAllEl.checked = (allChecks.length>0 && checked.length === allChecks.length);
      updateBulkToolbar();
      return;
    }
    if(e.target.matches('.view-bill')){
      const id = e.target.getAttribute('data-id');
      try{
        const resp = await fetch('<?= site_url('users/getBillDetails') ?>?bill_id=' + encodeURIComponent(id), { headers: { 'Accept': 'application/json', 'X-Requested-With':'XMLHttpRequest' } });
        const contentType = (resp.headers.get('content-type') || '').toLowerCase();
        let data = null;
        if(!resp.ok) {
          const contentEl = document.getElementById('billDetailsContent');
          while(contentEl.firstChild) contentEl.removeChild(contentEl.firstChild);
          // If server returned JSON (e.g., {error: ...}), parse and show message
          if (contentType.includes('application/json') || contentType.includes('+json')) {
            try {
              const errJson = await resp.json();
              const errDiv = document.createElement('div'); errDiv.className='text-danger'; errDiv.textContent = errJson.error || errJson.message || ('Server error: ' + resp.status);
              contentEl.appendChild(errDiv);
            } catch(e) {
              const txt = await resp.text().catch(()=>'<no body>');
              const errDiv = document.createElement('div'); errDiv.className='text-danger'; errDiv.textContent = 'Server error: ' + resp.status;
              const pre = document.createElement('pre'); pre.textContent = txt;
              contentEl.appendChild(errDiv); contentEl.appendChild(pre);
            }
          } else {
            const txt = await resp.text().catch(()=>'<no body>');
            const errDiv = document.createElement('div'); errDiv.className='text-danger'; errDiv.textContent = 'Server error: ' + resp.status + ' — check console for details.';
            const pre = document.createElement('pre'); pre.textContent = txt;
            contentEl.appendChild(errDiv); contentEl.appendChild(pre);
          }
          document.getElementById('billDetailsModal').style.display = 'block';
          return;
        }

        if (contentType.includes('application/json') || contentType.includes('+json')) {
          try { data = await resp.json(); } catch(err) { data = null; }
        } else {
          // not JSON — get text and attempt to extract JSON block for debugging
          const txt = await resp.text().catch(()=>'');
          // try to find a JSON object in the text
          const firstBrace = txt.indexOf('{');
          const lastBrace = txt.lastIndexOf('}');
          if (firstBrace !== -1 && lastBrace !== -1 && lastBrace > firstBrace) {
            const maybe = txt.substring(firstBrace, lastBrace + 1);
            try { data = JSON.parse(maybe); }
            catch(e) { data = null; }
          }
          // show raw server response to help debugging
          const contentEl = document.getElementById('billDetailsContent');
          while(contentEl.firstChild) contentEl.removeChild(contentEl.firstChild);
          const hint = document.createElement('div'); hint.className = 'small-muted'; hint.textContent = 'Server returned non-JSON content. Showing raw response for debugging:';
          const pre = document.createElement('pre'); pre.textContent = txt;
          contentEl.appendChild(hint); contentEl.appendChild(pre);
          document.getElementById('billDetailsModal').style.display = 'block';
          if (data === null) return;
        }

        const contentEl = document.getElementById('billDetailsContent');
        while(contentEl.firstChild) contentEl.removeChild(contentEl.firstChild);
        if(!data || (Object.keys(data).length===0 && Array.isArray(data)===false)){
          const d = document.createElement('div'); d.className='small-muted'; d.textContent = 'No details found for this bill.'; contentEl.appendChild(d);
        } else {
          const pre = document.createElement('pre'); pre.textContent = JSON.stringify(data, null, 2); contentEl.appendChild(pre);
        }
        document.getElementById('billDetailsModal').style.display = 'block';
      }catch(err){
        console.error('Error fetching bill details', err);
        alert('Failed to load bill details (network or parse error). See console.');
      }
    }

    if(e.target.matches('.mark-paid')){
      const id = e.target.getAttribute('data-id');
      if(!confirm('Mark this bill as paid?')) return;
      try{
        const resp = await fetch(MARK_PAID + '/' + encodeURIComponent(id), { method:'POST', headers:{ 'Content-Type':'application/x-www-form-urlencoded' }, body: 'csrf_test_name=' + encodeURIComponent(window.csrfHash || '') });
        if(resp.ok){ loadOverdue(); } else { alert('Failed to mark paid'); }
      }catch(err){ alert('Failed to mark paid'); }
    }
  });

  // bulk action flow
  document.getElementById('bulkMarkPaid').addEventListener('click', function(){
    const ids = Array.from(selectedIds);
    if(ids.length===0) return;
    // show confirm modal
    const modalHtml = `Are you sure you want to mark <strong>${ids.length}</strong> bill(s) as paid?`;
    if(!confirm(modalHtml)) return;
    performBulkMarkPaid(ids);
  });

  async function performBulkMarkPaid(ids){
    if(!ids || !ids.length) return;
    // try bulk endpoint first
    try{
      const bulkUrl = '<?= site_url('admin/markBillsPaidBulk') ?>';
      const body = JSON.stringify({ ids: ids });
      const resp = await fetch(bulkUrl, { method:'POST', headers:{ 'Content-Type':'application/json' , 'X-Requested-With':'XMLHttpRequest' }, body: body });
      if(resp.ok){
        // success
        selectedIds.clear(); selectAllEl.checked = false; updateBulkToolbar(); loadOverdue();
        return;
      }
      // if bulk endpoint not found (404) or not implemented, fallback to sequential calls
    }catch(err){ /* continue to fallback */ }

    // fallback: mark each individually
    for(const id of ids){
      try{
        await fetch(MARK_PAID + '/' + encodeURIComponent(id), { method:'POST', headers:{ 'Content-Type':'application/x-www-form-urlencoded' }, body: 'csrf_test_name=' + encodeURIComponent(window.csrfHash || '') });
      }catch(err){ console.error('Failed mark id', id, err); }
    }
    selectedIds.clear(); selectAllEl.checked = false; updateBulkToolbar(); loadOverdue();
  }

  // modal close
  window.closeBillModal = function(){ document.getElementById('billDetailsModal').style.display = 'none'; };

  function escapeHtml(str){
    if(!str) return '';
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
  }

  // pagination controls
  document.getElementById('prevPage').addEventListener('click', () => { if(page>1){ page--; loadOverdue(); } });
  document.getElementById('nextPage').addEventListener('click', () => { page++; loadOverdue(); });
  document.getElementById('perPage').addEventListener('change', () => { page=1; loadOverdue(); });

  // filters
  document.getElementById('applyOverdueFilters').addEventListener('click', () => { page=1; loadOverdue(); });
  document.getElementById('resetOverdueFilters').addEventListener('click', () => { document.getElementById('overdueFilters').reset(); page=1; loadOverdue(); });
  document.getElementById('refreshOverdue').addEventListener('click', loadOverdue);
  document.getElementById('overdueQuickSearch').addEventListener('input', function(){ document.querySelector('#overdueFilters [name="search"]').value = this.value; clearTimeout(window.__overdueDeb); window.__overdueDeb = setTimeout(()=>{ page=1; loadOverdue(); }, 400); });

  // initial load
  loadOverdue();
})();
