
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap');

:root{
    --bg: #f6f8fb;
    --card: #ffffff;
    --muted: #6b7280;
    --primary: #2563eb;
    --primary-600: #1e40af;
    --success: #10b981;
    --danger: #ef4444;
    --border: #e6eef8;
    --shadow: 0 10px 30px rgba(23,42,77,0.06);
    --radius: 12px;
    --font-sans: 'Poppins', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial;
    --max-width: 1100px;
}

* { box-sizing: border-box; }
html, body { margin:0; font-family: var(--font-sans); background: linear-gradient(180deg,#f7fbff 0%, #f2f6fb 100%); color:#111827; -webkit-font-smoothing:antialiased; -moz-osx-font-smoothing:grayscale; }
.container-fluid { max-width: var(--max-width); margin: 28px auto; padding: 20px; }

/* Header */
.header { display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:16px; }
.header .title { font-weight:700; font-size:1.1rem; color:#0f172a; }
.header .subtitle { color:var(--muted); font-size:0.9rem; }

/* Controls */
.controls { display:flex; gap:10px; align-items:center; }
.search {
    background: #f3f6fb; padding:8px 10px; border-radius:10px; display:flex; gap:8px; align-items:center; border:1px solid transparent;
}
.search input { border:none; outline:none; background:transparent; width:220px; font-size:0.95rem; color:#0f172a; font-family:var(--font-sans); }
.count-pill { background: linear-gradient(90deg,#eef2ff 0%, #eefcfa 100%); padding:6px 10px; border-radius:999px; font-weight:600; color:var(--primary-600); font-size:0.85rem; border:1px solid rgba(37,99,235,0.08); }

/* Card */
.card { background: var(--card); border-radius: var(--radius); box-shadow: var(--shadow); overflow:hidden; border:1px solid var(--border); }
.card .card-body { padding:16px; }

/* Form styles */
.form-row { display:flex; gap:10px; flex-wrap:wrap; align-items:center; }
.form-row .form-control, .form-row input, .form-row select { border:1px solid var(--border); padding:8px 10px; border-radius:8px; background: #fff; font-family:var(--font-sans); }
.form-actions { display:flex; gap:8px; align-items:center; }

/* Table */
.table-wrap { overflow:auto; margin-top:8px; }
.table { width:100%; border-collapse:collapse; min-width:760px; font-size:0.95rem; }
.table thead th { text-align:left; padding:12px 14px; font-weight:700; font-size:0.8rem; color:var(--muted); text-transform:uppercase; letter-spacing:0.6px; background:transparent; position:sticky; top:0; z-index:1; }
.table tbody td { padding:12px 14px; border-top:1px solid var(--border); vertical-align:middle; color:#111827; }
.table tbody tr:hover { background: linear-gradient(90deg, rgba(37,99,235,0.03), rgba(37,99,235,0.02)); }

/* Buttons */
.btn { display:inline-flex; align-items:center; gap:8px; padding:8px 12px; border-radius:10px; font-weight:600; cursor:pointer; border:none; transition:all .12s ease; font-size:0.9rem; font-family:var(--font-sans); }
.btn-primary { background: linear-gradient(90deg,var(--primary) 0%, var(--primary-600) 100%); color:white; box-shadow: 0 6px 18px rgba(37,99,235,0.14); }
.btn-outline { background: transparent; color:var(--primary-600); border:1px solid rgba(37,99,235,0.08); border-radius:10px; padding:8px 10px; }
.btn-sm { padding:6px 10px; border-radius:8px; font-size:0.85rem; }

/* Modal tweaks */
.modal .modal-content { border-radius:12px; }

/* Responsive */
@media (max-width:860px){
    .search input { width:120px; }
    .table { min-width:640px; }
    .form-row { flex-direction:column; align-items:stretch; }
}
</style>

<div class="container-fluid" aria-live="polite">
    <div class="header" role="region" aria-label="Inactive users header">
        <div>
            <div class="title">Inactive Users</div>
            <div class="subtitle">View archived and reactivate inactive accounts</div>
        </div>

        <div class="controls" role="toolbar" aria-label="Inactive users controls">
            <div class="search" role="search" aria-label="Search inactive users">
                <svg class="icon" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M21 21l-4.35-4.35" stroke="#6b7280" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="11" cy="11" r="6" stroke="#6b7280" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <input id="quickInactiveSearch" placeholder="Search name or email" aria-label="Quick search inactive users" />
            </div>

            <div class="count-pill" id="inactiveCount">—</div>

            <button id="refreshInactive" class="btn btn-outline btn-sm" title="Refresh list">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M21 12a9 9 0 10-2.59 6.01L21 21" stroke="#1e40af" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Refresh
            </button>
        </div>
    </div>

    <div class="card" role="region" aria-labelledby="inactiveFiltersHeader" style="margin-bottom:12px;">
        <div class="card-body">
            <form id="inactiveFilters" class="g-2" aria-label="Inactive users filters">
                <div class="form-row" style="align-items:center;">
                    <input type="text" name="search" class="form-control" placeholder="Search name or email" aria-label="Search name or email" style="min-width:200px;" />

                    <input type="number" name="purok" class="form-control" placeholder="Purok" min="1" aria-label="Filter by purok" style="width:120px;" />

                    <input type="date" name="from" class="form-control" aria-label="From date" style="width:160px;" />
                    <input type="date" name="to" class="form-control" aria-label="To date" style="width:160px;" />

                    <div class="form-actions" style="margin-left:auto;">
                        <button class="btn btn-primary btn-sm" id="applyInactiveFilters" type="submit">Apply</button>
                        <button class="btn btn-outline btn-sm" id="resetInactiveFilters" type="button">Reset</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card" role="region" aria-labelledby="inactiveTableHeader">
        <div class="card-body" style="padding:8px 16px;">
            <div class="table-wrap">
                <table class="table" id="inactiveUsersTable" aria-describedby="inactiveTableHeader">
                    <thead>
                        <tr>
                            <th style="width:6%;">#</th>
                            <th style="width:24%;">Name</th>
                            <th style="width:20%;">Email</th>
                            <th style="width:12%;">Phone</th>
                            <th style="width:8%;">Purok</th>
                            <th style="width:16%;">Inactivated</th>
                            <th style="width:14%; text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody><!-- Populated by AJAX --></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Archived Bills Modal -->
<div class="modal fade" id="archivedBillsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header" style="background: linear-gradient(90deg,var(--primary), var(--primary-600)); color:white;">
        <h5 class="modal-title">Archived Bills</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="table-wrap">
          <table class="table table-sm table-striped" id="archivedBillsTable">
            <thead>
              <tr>
                <th>Bill #</th>
                <th>Billing Month</th>
                <th>Due Date</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Paid Date</th>
                <th>Archived At</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline btn-sm" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Reactivate Modal -->
<div class="modal fade" id="reactivateModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="background: linear-gradient(90deg,var(--primary), var(--primary-600)); color:white;">
        <h5 class="modal-title">Reactivate User</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to reactivate this user?</p>
        <input type="hidden" id="reactivateUserId" />
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline btn-sm" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary btn-sm" id="confirmReactivate">Yes, Reactivate</button>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
  const INACTIVE_API = "<?= site_url('admin/getInactiveUsers') ?>";
  const ARCHIVED_API_BASE = "<?= site_url('admin/archivedBills') ?>";
  const REACTIVATE_API_BASE = "<?= site_url('admin/reactivateUser') ?>";
  const $tableBody = $('#inactiveUsersTable tbody');

  function buildQuery() {
    const params = new URLSearchParams(new FormData(document.getElementById('inactiveFilters')));
    return params.toString();
  }

  function setCount(count){
    const el = document.getElementById('inactiveCount');
    if(!el) return;
    el.textContent = (count > 0 ? count + ' Inactive' : 'No Inactive');
  }

  function loadInactive() {
    const qs = buildQuery();
    const url = `${INACTIVE_API}${qs ? '?' + qs : ''}`;
    $.getJSON(url).done(function(rows){
      $tableBody.empty();
      if(!rows || rows.length === 0){
        $tableBody.append('<tr><td colspan="7" class="text-center text-muted py-4">No inactive users found</td></tr>');
        setCount(0);
        return;
      }
      rows.forEach((r, idx) => {
        const name = `${r.first_name || ''} ${r.last_name || ''}`.trim();
        const tr = $(
          `<tr>
            <td>${idx+1}</td>
            <td>${name}</td>
            <td>${r.email || ''}</td>
            <td>${r.phone || ''}</td>
            <td>${r.purok || ''}</td>
            <td>${r.inactivated_at || ''}</td>
            <td style="text-align:right;">
              <button class="btn btn-sm btn-outline btn-sm view-archived me-1" data-id="${r.inactive_id}">Archived Bills</button>
              <button class="btn btn-sm btn-primary reactivate" data-user-id="${r.user_id}">Reactivate</button>
            </td>
          </tr>`);
        $tableBody.append(tr);
      });
      setCount(rows.length);
    });
  }

  $(document).on('submit', '#inactiveFilters', function(e){ e.preventDefault(); loadInactive(); });
  $(document).on('click', '#resetInactiveFilters', function(){
    $('#inactiveFilters')[0].reset();
    loadInactive();
  });
  $(document).on('click', '#refreshInactive', function(){ loadInactive(); });

  // Archived bills modal
  $(document).on('click', '.view-archived', function(){
    const id = $(this).data('id');
    const url = `${ARCHIVED_API_BASE}/${id}`;
    $.getJSON(url).done(function(bills){
      const $tb = $('#archivedBillsTable tbody');
      $tb.empty();
      if(!bills || bills.length === 0){
        $tb.append('<tr><td colspan="7" class="text-center text-muted">No archived bills</td></tr>');
      } else {
        bills.forEach(b => {
          $tb.append(`
            <tr>
              <td>${b.bill_no || ''}</td>
              <td>${b.billing_month || ''}</td>
              <td>${b.due_date || ''}</td>
              <td>${(b.amount_due ?? 0).toFixed(2)}</td>
              <td>${b.status || ''}</td>
              <td>${b.paid_date || ''}</td>
              <td>${b.archived_at || ''}</td>
            </tr>`);
        });
      }
      const modal = new bootstrap.Modal(document.getElementById('archivedBillsModal'));
      modal.show();
    });
  });

  // Reactivate
  $(document).on('click', '.reactivate', function(){
    const userId = $(this).data('user-id');
    $('#reactivateUserId').val(userId);
    const modal = new bootstrap.Modal(document.getElementById('reactivateModal'));
    modal.show();
  });
  $(document).on('click', '#confirmReactivate', function(){
    const userId = $('#reactivateUserId').val();
    const url = `${REACTIVATE_API_BASE}/${userId}`;
    $.post(url, {}).done(function(res){
      if(res && res.success){
        loadInactive();
        bootstrap.Modal.getInstance(document.getElementById('reactivateModal')).hide();
      } else {
        alert(res && res.message ? res.message : 'Failed to reactivate');
      }
    }).fail(function(){ alert('Request failed'); });
  });

  // Quick search ties to filters search input
  document.getElementById('quickInactiveSearch').addEventListener('input', function(e){
    const v = this.value || '';
    const input = document.querySelector('#inactiveFilters [name="search"]');
    if(input){
      input.value = v;
    }
    // debounce small
    clearTimeout(window.__inactiveSearchDeb);
    window.__inactiveSearchDeb = setTimeout(loadInactive, 400);
  });

  // Initial load
  loadInactive();
})();
</script>
