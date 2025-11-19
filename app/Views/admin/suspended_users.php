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
    --glass: rgba(255,255,255,0.6);
    --shadow: 0 10px 30px rgba(23,42,77,0.06);
    --radius: 12px;
    --max-width: 1100px;
    --font-sans: 'Poppins', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial;
}

* { box-sizing: border-box; }
html,body { height:100%; margin:0; font-family: var(--font-sans); background: linear-gradient(180deg,#f7fbff 0%, #f2f6fb 100%); color:#111827; -webkit-font-smoothing:antialiased; -moz-osx-font-smoothing:grayscale; }
.container-fluid { max-width: var(--max-width); margin: 28px auto; padding: 20px; }

.header { display:flex; align-items:center; justify-content:space-between; gap:1rem; margin-bottom: 16px; }
.header h1 { margin:0; font-size:1.2rem; font-weight:700; color:#0f172a; }
.header .sub { color:var(--muted); font-size:0.9rem; }

.controls { display:flex; gap:12px; align-items:center; flex-wrap:wrap; }
.search { background: #f3f6fb; padding:8px 10px; border-radius: 10px; display:flex; gap:8px; align-items:center; border: 1px solid transparent; }
.search input { border: none; outline:none; background:transparent; width: 220px; font-size:0.95rem; color:#0f172a; }
.count-pill { background: linear-gradient(90deg,#eef2ff 0%, #eefcfa 100%); padding:6px 10px; border-radius:999px; font-weight:600; color:var(--primary-600); font-size:0.85rem; border:1px solid rgba(37,99,235,0.08); white-space: nowrap; }

.btn { display:inline-flex; align-items:center; gap:8px; padding:8px 12px; border-radius: 10px; font-weight:600; cursor:pointer; border:none; transition:all .12s ease; font-size:0.9rem; white-space: nowrap; }
.btn-primary { background: linear-gradient(90deg,var(--primary) 0%, var(--primary-600) 100%); color:white; box-shadow: 0 6px 18px rgba(37,99,235,0.14); }
.btn-ghost { background: transparent; color:var(--primary-600); border: 1px solid rgba(37,99,235,0.08); }
.btn-sm { padding:6px 10px; border-radius:8px; font-size:0.85rem; }

.card { background: var(--card); border-radius: var(--radius); box-shadow: var(--shadow); overflow:hidden; border: 1px solid var(--border); }
.card-header { display:flex; align-items:center; justify-content:space-between; gap:1rem; padding: 14px 18px; border-bottom: 1px solid var(--border); background: linear-gradient(180deg, rgba(255,255,255,0.5), var(--card)); flex-wrap: wrap; }
.card-title { font-weight:700; color:#0f172a; margin:0; }
.card-body { padding: 14px 18px; }

.table-wrap { overflow:auto; }
.table { width:100%; border-collapse: collapse; font-size:0.95rem; min-width:720px; }
.table thead th { text-align:left; padding:12px 16px; font-weight:700; font-size:0.8rem; color:var(--muted); text-transform:uppercase; letter-spacing:0.6px; background:transparent; position:sticky; top:0; z-index:1; }
.table tbody td { padding:14px 16px; border-top:1px solid var(--border); vertical-align:middle; color:#111827; }
.table tbody tr:hover { background: linear-gradient(90deg, rgba(37,99,235,0.03), rgba(37,99,235,0.02)); }

/* Responsive cards for small screens */
@media (max-width: 768px) {
    .container-fluid { padding: 12px; margin: 12px auto; }
    .header h1 { font-size: 1.1rem; }
    .controls { flex-direction: column; align-items: stretch; gap: 8px; }
    .search { width: 100%; }
    .search input { width: 100%; flex: 1; }
    .table { min-width: 0; border: none; }
    .table thead { display:none; }
    .table tbody, .table tr, .table td { display:block; width:100%; }
    .table tbody tr { margin: 0 0 12px; background: var(--card); border-radius:10px; padding: 12px; box-shadow: var(--shadow); border:1px solid var(--border); }
    .table tbody td { padding: 10px 12px; border-top: none; display:flex; justify-content:space-between; align-items:center; gap:8px; flex-wrap:wrap; }
    .table tbody td[data-label]::before { content: attr(data-label); display:block; font-weight:700; color:var(--muted); margin-right:8px; flex: 0 0 38%; text-align:left; font-size:0.85rem; }
}

</style>

<div class="container-fluid">
    <div class="header">
        <div>
            <h1>Suspended Users</h1>
            <div class="sub">Manage users who have been suspended</div>
        </div>

        <div class="controls" role="toolbar" aria-label="Suspended users actions">
            <div class="search" role="search" aria-label="Search suspended users">
                <input id="suspendQuickSearch" placeholder="Search name or email" aria-label="Search by name or email" />
            </div>

            <div class="count-pill" id="suspendedCount">—</div>

            <button id="refreshSuspended" class="btn btn-ghost btn-sm" title="Refresh list">Refresh</button>
        </div>
    </div>

    <div class="card" role="region" aria-labelledby="suspendedCardHeader">
        <div class="card-header" id="suspendedCardHeader">
            <div class="card-title">Suspended Users</div>
            <div class="text-muted" style="font-size:0.9rem;">Filtered list — use search and purok</div>
        </div>

        <div class="card-body">
            <div class="filters" style="margin-bottom:12px; display:flex; gap:12px; align-items:center;">
                <div style="min-width:160px;">
                    <select id="filterPurok" name="purok" class="form-select" aria-label="Filter by purok" style="border-radius:8px; padding:8px;">
                        <option value="">All Puroks</option>
                    </select>
                </div>

                <div style="margin-left:auto;">
                    <button id="resetSuspendedFilters" class="btn btn-ghost btn-sm">Reset</button>
                </div>
            </div>

            <div class="table-wrap">
                <table class="table" id="suspendedUsersTable" aria-describedby="suspendedCardHeader">
                    <thead>
                        <tr>
                            <th style="width:5%">#</th>
                            <th style="width:22%">Name</th>
                            <th style="width:20%">Email</th>
                            <th style="width:12%">Phone</th>
                            <th style="width:12%">Purok</th>
                            <th style="width:14%">Suspended</th>
                            <th style="width:15%; text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function initSuspendedUsersPage(){
    const baseUrl = "<?= base_url() ?>";
    const API = baseUrl + '/admin/getSuspendedUsers';
    const UNSUSPEND_BASE = baseUrl + '/admin/reactivateUser';
    const maxPurok = 7;
    const $tbody = $('#suspendedUsersTable tbody');

    console.log('✅ Suspended Users page initialized');

    // populate filter purok select
    const filterPurok = document.getElementById('filterPurok');
    if(filterPurok){
        filterPurok.innerHTML = '<option value="">All Puroks</option>';
        for(let i=1;i<=maxPurok;i++){ filterPurok.insertAdjacentHTML('beforeend', `<option value="${i}">Purok ${i}</option>`); }
    }

    function setCount(n){ $('#suspendedCount').text(n? (n + ' Suspended') : 'No Suspended'); }

    function renderRows(rows){
        $tbody.empty();
        if(!rows || rows.length===0){
            $tbody.html('<tr><td colspan="7" class="text-center">No suspended users found</td></tr>');
            return;
        }
        rows.forEach((r, idx) => {
            const name = ((r.first_name||'') + ' ' + (r.last_name||'')).trim();
            const tr = `
                <tr>
                    <td data-label="#">${idx+1}</td>
                    <td data-label="Name">${name}</td>
                    <td data-label="Email">${r.email||''}</td>
                    <td data-label="Phone">${r.phone||''}</td>
                    <td data-label="Purok">${r.purok||''}</td>
                    <td data-label="Suspended">${r.updated_at||''}</td>
                    <td data-label="Actions" style="text-align:right;"><button class="btn btn-sm btn-primary unsuspend" data-id="${r.id}">Unsuspend</button></td>
                </tr>`;
            $tbody.append(tr);
        });
    }

    // Load with optional params (search, purok, from, to)
    function loadSuspended(opts={}){
        const params = Object.assign({}, opts);
        // if filters exist on the form, merge them
        const purok = $('#filterPurok').val() || '';
        const search = $('#suspendQuickSearch').val() || '';
        if(purok) params.purok = purok;
        if(search) params.search = search;

        console.log('🔍 Loading suspended users with', params);
        $.ajax({
            url: API,
            method: 'GET',
            data: params,
            dataType: 'json',
        }).done(function(resp){
            const rows = resp && resp.data ? resp.data : resp;
            const meta = resp && resp.meta ? resp.meta : null;
            renderRows(rows);
            setCount(meta && typeof meta.total !== 'undefined' ? meta.total : (rows? rows.length : 0));
        }).fail(function(xhr){
            $tbody.html('<tr><td colspan="7" class="text-center text-danger">Failed to load suspended users</td></tr>');
            setCount(0);
        });
    }

    // Unsuspend
    $(document).on('click', '.unsuspend', function(){
        const id = $(this).data('id');
        if(!confirm('Unsuspend this user?')) return;
        $.post(UNSUSPEND_BASE + '/' + encodeURIComponent(id), {}).done(function(){ loadSuspended(); }).fail(function(){ alert('Failed to unsuspend'); });
    });

    // Events
    $('#refreshSuspended').on('click', function(){ loadSuspended(); });
    $('#resetSuspendedFilters').on('click', function(){ $('#filterPurok').val(''); $('#suspendQuickSearch').val(''); loadSuspended(); });

    // debounce search
    let debounce;
    $('#suspendQuickSearch').on('input', function(){ clearTimeout(debounce); debounce = setTimeout(function(){ loadSuspended(); }, 400); });

    // initial load
    loadSuspended();
}

$(document).ready(function(){ initSuspendedUsersPage(); });
</script>
