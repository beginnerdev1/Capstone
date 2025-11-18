<!-- registeredUsers.php -->
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

/* Header */
.header {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:1rem;
    margin-bottom: 16px;
}
.header h1 { margin:0; font-size:1.2rem; font-weight:700; color:#0f172a; }
.header .sub { color:var(--muted); font-size:0.9rem; }

/* Controls */
.controls { display:flex; gap:12px; align-items:center; flex-wrap:wrap; }

/* Search and simple count pill */
.search {
    background: #f3f6fb;
    padding:8px 10px;
    border-radius: 10px;
    display:flex;
    gap:8px;
    align-items:center;
    border: 1px solid transparent;
}
.search input { border: none; outline:none; background:transparent; width: 220px; font-size:0.95rem; color:#0f172a; font-family: var(--font-sans); }
.count-pill {
    background: linear-gradient(90deg,#eef2ff 0%, #eefcfa 100%);
    padding:6px 10px;
    border-radius:999px;
    font-weight:600;
    color:var(--primary-600);
    font-size:0.85rem;
    border:1px solid rgba(37,99,235,0.08);
    white-space: nowrap;
}

/* Buttons */
.btn { display:inline-flex; align-items:center; gap:8px; padding:8px 12px; border-radius: 10px; font-weight:600; cursor:pointer; border:none; transition:all .12s ease; font-size:0.9rem; font-family: var(--font-sans); white-space: nowrap; }
.btn:active { transform:translateY(1px); }
.btn-primary { background: linear-gradient(90deg,var(--primary) 0%, var(--primary-600) 100%); color:white; box-shadow: 0 6px 18px rgba(37,99,235,0.14); }
.btn-ghost { background: transparent; color:var(--primary-600); border: 1px solid rgba(37,99,235,0.08); }
.btn-sm { padding:6px 10px; border-radius:8px; font-size:0.85rem; }

/* Card */
.card { background: var(--card); border-radius: var(--radius); box-shadow: var(--shadow); overflow:hidden; border: 1px solid var(--border); }
.card-header {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:1rem;
    padding: 14px 18px;
    border-bottom: 1px solid var(--border);
    background: linear-gradient(180deg, rgba(255,255,255,0.5), var(--card));
    flex-wrap: wrap;
}
.card-title { font-weight:700; color:#0f172a; margin:0; }
.card-body { padding: 14px 18px; }

/* Filters row */
.filters { display:flex; gap:12px; align-items:center; flex-wrap:wrap; }

/* Table */
.table-wrap { overflow:auto; }
.table { width:100%; border-collapse: collapse; font-size:0.95rem; min-width:720px; font-family: var(--font-sans); }
.table thead th { text-align:left; padding:12px 16px; font-weight:700; font-size:0.8rem; color:var(--muted); text-transform:uppercase; letter-spacing:0.6px; background:transparent; position:sticky; top:0; z-index:1; }
.table tbody td { padding:14px 16px; border-top:1px solid var(--border); vertical-align:middle; color:#111827; }
.table tbody tr:hover { background: linear-gradient(90deg, rgba(37,99,235,0.03), rgba(37,99,235,0.02)); }

/* Badges */
.badge { display:inline-block; padding:6px 10px; border-radius:8px; font-weight:700; font-size:0.8rem; color:#fff; }
.bg-success { background: var(--success); }
.bg-warning { background: #f59e0b; }
.bg-danger { background: var(--danger); }
.bg-secondary { background: #6b7280; }
.bg-info { background: #0ea5e9; }

/* Action cell */
.actions { display:flex; gap:8px; justify-content:flex-end; flex-wrap:wrap; }

/* Modals use Bootstrap classes; keep their look consistent with a subtle radius */
.modal .modal-content { border-radius: 12px; }

/* ----------------------------
   ENHANCED RESPONSIVE RULES
   ---------------------------- */

/* Large tablets and below */
@media (max-width: 1024px) {
    .container-fluid { padding: 16px; margin: 20px auto; }
    .header { flex-direction: column; align-items: flex-start; gap: 12px; }
    .controls { width: 100%; justify-content: space-between; }
    .search input { width: 180px; }
}

/* Tablet */
@media (max-width: 768px) {
    .container-fluid { padding: 12px; margin: 12px auto; }
    .header h1 { font-size: 1.1rem; }
    .header .sub { font-size: 0.85rem; }
    
    /* Stack controls */
    .controls { flex-direction: column; align-items: stretch; gap: 8px; }
    .search { width: 100%; }
    .search input { width: 100%; flex: 1; }
    .count-pill { text-align: center; }
    .btn { width: 100%; justify-content: center; }
    
    /* Card header stack */
    .card-header { flex-direction: column; align-items: flex-start; }
    .card-header .text-muted { font-size: 0.85rem; }
    
    /* Filters stack */
    .filters > div { width: 100%; }
    .filters select { width: 100%; }
    .filters > div[style*="margin-left"] { margin-left: 0 !important; }
    
    /* Convert table to cards */
    .table { min-width: 0; border: none; }
    .table thead { display:none; }
    .table tbody, .table tr, .table td { display:block; width:100%; }
    .table tbody tr { 
        margin: 0 0 12px; 
        background: var(--card); 
        border-radius:10px; 
        padding: 12px; 
        box-shadow: var(--shadow); 
        border:1px solid var(--border); 
    }
    .table tbody td { 
        padding: 10px 12px; 
        border-top: none; 
        display:flex; 
        justify-content:space-between; 
        align-items:center; 
        gap:8px; 
        flex-wrap:wrap; 
    }
    .table tbody td[data-label]::before {
        content: attr(data-label);
        display:block;
        font-weight:700;
        color:var(--muted);
        margin-right:8px;
        flex: 0 0 38%;
        text-align:left;
        font-size:0.85rem;
    }
    .table tbody td .badge { margin-left: 6px; }
    .actions { justify-content:flex-end; gap:8px; margin-left:8px; width: 100%; }
    .actions .btn { flex: 1; min-width: 0; }
    .card-body .table-wrap { padding:8px; }
}

/* Mobile */
@media (max-width: 480px) {
    .container-fluid { padding: 8px; margin: 8px auto; }
    .header h1 { font-size: 1rem; }
    .header .sub { font-size: 0.8rem; }
    
    .search { padding: 6px 8px; }
    .search input { font-size: 0.9rem; }
    .count-pill { font-size: 0.8rem; padding: 5px 8px; }
    .btn-sm { padding: 5px 8px; font-size: 0.8rem; }
    
    .card-header { padding: 12px; }
    .card-body { padding: 12px; }
    
    .table tbody tr { padding: 10px; margin-bottom: 10px; }
    .table tbody td { padding: 8px 10px; font-size: 0.9rem; }
    .table tbody td[data-label]::before { 
        flex: 0 0 40%; 
        font-size:0.82rem; 
    }
    
    .actions { flex-direction: column; }
    .actions .btn { width: 100%; }
    
    /* Modal adjustments */
    .modal-dialog { margin: 0.5rem; }
    .modal-body { padding: 1rem; }
    .row.mb-3 { margin-bottom: 0.75rem !important; }
}

/* Extra small devices */
@media (max-width: 360px) {
    .header h1 { font-size: 0.95rem; }
    .btn { font-size: 0.8rem; padding: 5px 8px; }
    .table tbody td { font-size: 0.85rem; }
}

/* Force-responsive fallback */
.container-fluid.force-responsive .table { min-width: 0 !important; border: none !important; }
.container-fluid.force-responsive .table thead { display: none !important; }
.container-fluid.force-responsive .table tbody,
.container-fluid.force-responsive .table tr,
.container-fluid.force-responsive .table td { display: block !important; width: 100% !important; }
.container-fluid.force-responsive .table tbody tr {
  margin: 0 0 12px;
  background: var(--card);
  border-radius: 10px;
  padding: 12px;
  box-shadow: var(--shadow);
  border: 1px solid var(--border);
}
.container-fluid.force-responsive .table tbody td {
  padding: 8px 10px;
  border-top: none;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap:8px;
}
.container-fluid.force-responsive .table tbody td[data-label]::before {
  content: attr(data-label);
  display: block;
  font-weight: 700;
  color: var(--muted);
  margin-right: 8px;
  flex: 0 0 35%;
  text-align: left;
  font-size: 0.85rem;
}
</style>

<div class="container-fluid">
    <div class="header">
        <div>
            <h1>Registered Users</h1>
            <div class="sub">Manage users — view, add, activate, or deactivate accounts</div>
        </div>

        <div class="controls" role="toolbar" aria-label="Registered users actions">
            <div class="search" role="search" aria-label="Search registered users">
                <svg class="icon" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M21 21l-4.35-4.35" stroke="#6b7280" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="11" cy="11" r="6" stroke="#6b7280" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <input id="filterInput" placeholder="Search name or email" aria-label="Search by name or email" />
            </div>

            <div class="count-pill" id="usersCount">— Users</div>

            <button id="printUsersBtn" class="btn btn-ghost btn-sm" title="Print users list">
                <svg class="icon" width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M6 9h12v6H6z" stroke="#1e40af" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Print
            </button>

            <button id="addUserBtn" class="btn btn-primary btn-sm" title="Add new user">
                <svg class="icon" width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M12 5v14M5 12h14" stroke="white" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Add User
            </button>
        </div>
    </div>

    <div class="card" role="region" aria-labelledby="usersCardHeader">
        <div class="card-header" id="usersCardHeader">
            <div class="card-title">Users Directory</div>
            <div class="text-muted" style="font-size:0.9rem;">Live list — updates when filters change</div>
        </div>

        <div class="card-body">
            <div class="filters" style="margin-bottom:12px;">
                <div style="min-width:160px;">
                    <select id="filterPurok" class="form-select" aria-label="Filter by purok" style="border-radius:8px; padding:8px;">
                        <option value="">All Puroks</option>
                    </select>
                </div>

                <div style="min-width:160px;">
                    <select id="filterStatus" class="form-select" aria-label="Filter by status" style="border-radius:8px; padding:8px;">
                        <option value="">All Status</option>
                        <option value="approved">Approved</option>
                        <option value="pending">Pending</option>
                        <option value="suspended">Suspended</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>

                <div style="margin-left:auto;">
                    <button id="resetFilters" class="btn btn-ghost btn-sm">Reset</button>
                </div>
            </div>

            <div class="table-wrap">
                <table class="table" id="usersTable" aria-describedby="usersCardHeader">
                    <thead>
                        <tr>
                            <th style="width:6%;">#</th>
                            <th style="width:28%;">Name</th>
                            <th style="width:28%;">Email</th>
                            <th style="width:10%;">Purok</th>
                            <th style="width:12%;">Status</th>
                            <th style="width:16%; text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody><!-- AJAX will populate rows --></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- View User Modal -->
<div class="modal fade" id="viewUserModal" tabindex="-1" aria-labelledby="viewUserLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(90deg,var(--primary), var(--primary-600)); color:white;">
                <h5 class="modal-title" id="viewUserLabel"><i class="fas fa-user me-2"></i>User Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="userDetailsContent" class="p-3 text-secondary">
                    Loading user info...
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(90deg,var(--primary), var(--primary-600)); color:white;">
                <h5 class="modal-title" id="addUserLabel"><i class="fas fa-user-plus me-2"></i>Add User Account</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addUserForm">
                    <?= csrf_field() ?>
                    <div class="alert alert-info" role="alert">
                        <i class="fas fa-info-circle me-1"></i>
                        <strong>Note:</strong> Please fill in all required fields. Password must be at least 6 characters.
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="first_name" minlength="2" maxlength="50" required>
                            <div class="invalid-feedback">Please enter a valid first name (2-50 characters)</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="last_name" minlength="2" maxlength="50" required>
                            <div class="invalid-feedback">Please enter a valid last name (2-50 characters)</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email" required>
                        <div class="invalid-feedback">Please enter a valid email address</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control" name="password" id="addUserPassword" minlength="6" required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="form-text">Minimum 6 characters</div>
                        <div class="invalid-feedback">Password must be at least 6 characters</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Contact Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="phone" maxlength="20" required>
                            <div class="invalid-feedback">Please enter a contact number</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Gender <span class="text-danger">*</span></label>
                            <select class="form-select" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                            <div class="invalid-feedback">Please select a gender</div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Age <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="age" min="1" max="120" required>
                            <div class="invalid-feedback">Please enter a valid age (1-120)</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Family Members <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="family_number" min="1" max="20" required>
                            <div class="invalid-feedback">Please enter a valid number (1-20)</div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Purok <span class="text-danger">*</span></label>
                            <select class="form-select" name="purok" id="addUserPurok" required>
                                <option value="">Select Purok</option>
                            </select>
                            <div class="invalid-feedback">Please select a purok</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Initial Status <span class="text-danger">*</span></label>
                            <select class="form-select" name="status" required>
                                <option value="pending">Pending</option>
                                <option value="approved" selected>Approved</option>
                                <option value="suspended">Suspended</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Barangay</label>
                            <input type="text" class="form-control" value="Borlongan" readonly disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Municipality</label>
                            <input type="text" class="form-control" value="Dipaculao" readonly disabled>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Province</label>
                            <input type="text" class="form-control" value="Aurora" readonly disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Zip Code</label>
                            <input type="text" class="form-control" value="3203" readonly disabled>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Add User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript Logic -->
<script>
function initRegisteredUsersPage() {
    const baseUrl = "<?= base_url() ?>";
    const maxPurok = 7;

    console.log("✅ Registered Users page initialized!");

    // populate add-user purok select
    let addSelect = document.getElementById('addUserPurok');
    if (addSelect) {
        addSelect.innerHTML = '<option value="">Select Purok</option>';
        for(let i=1; i<=maxPurok; i++){
            let opt = document.createElement('option');
            opt.value = i;
            opt.text = 'Purok '+i;
            addSelect.appendChild(opt);
        }
    }

    // populate filter purok select
    let filterSelect = document.getElementById('filterPurok');
    if (filterSelect) {
        filterSelect.innerHTML = '<option value="">All</option>';
        for (let i = 1; i <= maxPurok; i++) {
            let opt = document.createElement('option');
            opt.value = i;
            opt.text = 'Purok ' + i;
            filterSelect.appendChild(opt);
        }
    }

    // Update users count pill when table loads
    function setUsersCount(count){
        const el = document.getElementById('usersCount');
        if(el) el.textContent = (count > 0 ? count + ' Users' : 'No Users');
    }

    // Load users
    window.loadUsers = function(search='', purok='', status=''){
        console.log("🔍 Sending AJAX request with:", {search,purok,status});
        $.ajax({
            url: baseUrl+'/admin/filterUsers',
            type:'GET',
            data:{search,purok,status},
            dataType:'json',
            success:function(users){
                console.log("✅ Loaded users:", users);
                const tbody = $('#usersTable tbody'); tbody.empty();
                if(!users || users.length===0){
                    tbody.html(`<tr><td colspan="6" class="text-center">No users found</td></tr>`);
                    setUsersCount(0);
                    return;
                }
                users.forEach((user,index)=>{
                    const statusLower = (user.status||'').toLowerCase();
                    let badgeClass='secondary';
                    if(statusLower==='approved') badgeClass='success';
                    else if(statusLower==='pending') badgeClass='warning';
                    else if(statusLower==='rejected') badgeClass='danger';
                    else if(statusLower==='suspended') badgeClass='secondary';
                    const fullName = user.name || '-';
                    const pendingBills = parseInt(user.pending_bills || 0, 10);
                    const canDeactivate = pendingBills === 0 && statusLower === 'approved';
                    const deactivateTitle = !canDeactivate
                        ? (pendingBills > 0 ? 'Cannot deactivate: pending bill(s) exist' : 'Only approved users can be deactivated')
                        : 'Deactivate user';
                    const deactivateBtn = `
                        <button class="btn btn-sm btn-outline-danger deactivateUserBtn ${canDeactivate ? '' : 'opacity-50'}" data-id="${user.id}" data-name="${fullName}" ${canDeactivate ? '' : 'data-disabled="1"'} title="${deactivateTitle}">
                            <i class="fas fa-user-slash"></i> Deactivate
                        </button>`;
                    const row = `
                        <tr>
                            <td data-label="#">${index+1}</td>
                            <td data-label="Name">${fullName}</td>
                            <td data-label="Email">${user.email || '-'}</td>
                            <td data-label="Purok">${user.purok || '-'}</td>
                            <td data-label="Status"><span class="badge bg-${badgeClass}">${user.status||'-'}</span></td>
                            <td data-label="Actions" class="actions">
                                <button class="btn btn-sm btn-primary viewUserBtn" data-id="${user.id}" title="View user details">
                                    <i class="fas fa-eye"></i> View
                                </button>
                                ${deactivateBtn}
                            </td>
                        </tr>
                    `;
                    tbody.append(row);
                });
                setUsersCount(users.length);
            },
            error:function(xhr,status,error){
                console.error("❌ AJAX Error:", status,error,xhr.responseText);
            }
        });
    }

    loadUsers();

    // Debounce for search input
    let searchTimeout;
    $('#filterInput').on('input', function(){
        clearTimeout(searchTimeout);
        const searchVal = $(this).val();
        const purokVal = $('#filterPurok').val();
        const statusVal = $('#filterStatus').val();
        searchTimeout = setTimeout(function(){
            loadUsers(searchVal, purokVal, statusVal);
        }, 500);
    });

    // Filters
    $('#filterPurok').on('change', function(){
        loadUsers($('#filterInput').val(), $(this).val(), $('#filterStatus').val());
    });

    $('#filterStatus').on('change', function(){
        loadUsers($('#filterInput').val(), $('#filterPurok').val(), $(this).val());
    });

    // Reset filters
    $('#resetFilters').on('click', function(){
        $('#filterInput').val('');
        $('#filterPurok').val('');
        $('#filterStatus').val('');
        loadUsers();
    });

    // View user
    $(document).on('click','.viewUserBtn',function(){
        const userId=$(this).data('id');
        $.ajax({
            url: baseUrl+'/admin/getUser/'+userId,
            type:'GET',
            dataType:'json',
            success:function(user){
                if(!user.id){ $("#userDetailsContent").html(`<p class="text-danger">User not found.