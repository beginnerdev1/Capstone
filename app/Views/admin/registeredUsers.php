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
                            <button class="btn btn-info editUserBtn me-2" data-id="${user.id}" title="Edit user">
                                <i class="fas fa-edit me-1"></i> Edit
                            </button>
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
                    <input id="filterLine" class="form-control" type="text" placeholder="Filter by Line #" style="border-radius:8px; padding:8px;">
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
                            <th style="width:22%;">Email</th>
                            <th style="width:10%;">Line #</th>
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

            <!-- Edit User Modal -->
            <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header" style="background: linear-gradient(90deg,var(--primary), var(--primary-600)); color:white;">
                            <h5 class="modal-title" id="editUserLabel"><i class="fas fa-user-edit me-2"></i>Edit User</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form id="editUserForm">
                                <?= csrf_field() ?>
                                <input type="hidden" name="user_id" id="editUserId" value="">

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">First Name</label>
                                        <input type="text" class="form-control" id="editFirstName" name="first_name" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Last Name</label>
                                        <input type="text" class="form-control" id="editLastName" name="last_name" readonly>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="editEmail" name="email" required>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Contact Number</label>
                                        <input type="text" class="form-control" id="editPhone" name="phone" maxlength="20">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Password</label>
                                        <input type="password" class="form-control" id="editPassword" name="password" readonly placeholder="(hidden)">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Gender</label>
                                        <select class="form-select" id="editGender" name="gender">
                                            <option value="">Select</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Age</label>
                                        <input type="number" class="form-control" id="editAge" name="age" min="1" max="120">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Family Members</label>
                                        <input type="number" class="form-control" id="editFamily" name="family_number" min="1" max="20">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                            <label class="form-label">Line Number</label>
                                            <input type="text" class="form-control" id="editLine" name="line_number" maxlength="32">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Purok</label>
                                            <select class="form-select" id="editPurok" name="purok"></select>
                                        </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                            <label class="form-label">Municipality</label>
                                            <input type="text" class="form-control" id="editMunicipality" name="municipality" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Barangay</label>
                                            <input type="text" class="form-control" id="editBarangay" name="barangay" readonly>
                                        </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Province</label>
                                        <input type="text" class="form-control" id="editProvince" name="province" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Zip Code</label>
                                        <input type="text" class="form-control" id="editZip" name="zipcode" readonly>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary" id="saveEditUserBtn">Save Changes</button>
                                </div>
                            </form>
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
                    <input type="hidden" name="status" value="approved">
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
                            <label class="form-label">Water Line Number</label>
                            <input type="text" class="form-control" name="line_number" maxlength="32" placeholder="e.g., 001">
                            <div class="form-text">Optional: specify the water line identifier for this user.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Initial Status</label>
                            <input type="text" class="form-control" value="Approved" readonly>
                            <div class="form-text text-success">Approved (set automatically for admin-created accounts)</div>
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
                            <!-- intentionally left blank to keep columns aligned -->
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
            data:{search,purok,status,line_number: $('#filterLine').length ? $('#filterLine').val() : ''},
            dataType:'json',
            success:function(users){
                console.log("✅ Loaded users:", users);
                const tbody = $('#usersTable tbody'); tbody.empty();
                if(!users || users.length===0){
                    tbody.html(`<tr><td colspan="7" class="text-center">No users found</td></tr>`);
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
                    // Move deactivate action into the View modal (render view button only here)
                    const row = `
                        <tr>
                            <td data-label="#">${index+1}</td>
                            <td data-label="Name">${fullName}</td>
                            <td data-label="Email">${user.email || '-'}</td>
                            <td data-label="Line">${user.line_number || '-'}</td>
                            <td data-label="Purok">${user.purok || '-'}</td>
                            <td data-label="Status"><span class="badge bg-${badgeClass}">${user.status||'-'}</span></td>
                            <td data-label="Actions" class="actions">
                                <button class="btn btn-sm btn-primary viewUserBtn" data-id="${user.id}" title="View user details">
                                    <i class="fas fa-eye"></i> View
                                </button>
                                <!-- Deactivate moved into View modal to avoid accidental clicks -->
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

    // Line filter
    $(document).on('input', '#filterLine', function(){
        loadUsers($('#filterInput').val(), $('#filterPurok').val(), $('#filterStatus').val());
    });

    // Reset filters
    $('#resetFilters').on('click', function(){
        $('#filterInput').val('');
        $('#filterPurok').val('');
        $('#filterLine').val('');
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
                if(!user.id){ $("#userDetailsContent").html(`<p class="text-danger">User not found.</p>`); return; }
                let profileImg = user.profile_picture
                    ? `<img src="${user.profile_picture}" class="rounded-circle mb-3" width="100" height="100">`
                    : `<i class="fas fa-user-circle fa-5x text-muted mb-3"></i>`;

                const pendingBills = parseInt(user.pending_bills || 0, 10);
                const canDeactivate = (user.status||'').toLowerCase() === 'approved' && pendingBills === 0;
                const deactivateTitle = !canDeactivate
                    ? (pendingBills > 0 ? 'Cannot deactivate: pending bill(s) exist' : 'Only approved users can be deactivated')
                    : 'Deactivate user';

                $("#userDetailsContent").html(`
                    <div class="text-center">${profileImg}</div>
                    <h5 class="text-center mb-3">${user.first_name||''} ${user.last_name||''}</h5>
                    <div class="row text-start">
                        <div class="col-md-6">
                            <p><strong>Email:</strong> ${user.email||'-'}</p>
                            <p><strong>Contact Number:</strong> ${user.phone||'-'}</p>
                            <p><strong>Gender:</strong> ${user.gender||'-'}</p>
                            <p><strong>Age:</strong> ${user.age||'-'}</p>
                            <p><strong>Family Members:</strong> ${user.family_number||'-'}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Purok:</strong> ${user.purok||'-'}</p>
                            <p><strong>Line #:</strong> ${user.line_number||user.line||user.lineNumber||user.line_no||user.lineNo||'-'}</p>
                            <p><strong>Barangay:</strong> ${user.barangay||'-'}</p>
                            <p><strong>Municipality:</strong> ${user.municipality||'-'}</p>
                            <p><strong>Province:</strong> ${user.province||'-'}</p>
                            <p><strong>Zip Code:</strong> ${user.zipcode||'-'}</p>
                            <p><strong>Status:</strong> <span class="badge bg-info">${user.status||'-'}</span></p>
                        </div>
                    </div>
                    <div class="text-end mt-3">
                            <button class="btn btn-info editUserBtn me-2" data-id="${user.id}" title="Edit user">
                                <i class="fas fa-edit me-1"></i> Edit
                            </button>
                            <button class="btn btn-warning suspendUserBtn me-2" data-id="${user.id}" ${ (user.status||'').toLowerCase() === 'suspended' ? 'data-disabled="1" title="User already suspended"' : '' }>
                                <i class="fas fa-user-clock me-1"></i> Suspend
                            </button>
                            <button class="btn btn-danger deactivateUserBtn" data-id="${user.id}" data-name="${(user.first_name||'') + ' ' + (user.last_name||'') }" ${canDeactivate ? '' : 'data-disabled="1" title="' + deactivateTitle + '"'}>
                                <i class="fas fa-user-slash me-1"></i> Deactivate
                            </button>
                    </div>
                `);
                $("#viewUserModal").modal("show");
            },
            error:function(){ $("#userDetailsContent").html(`<p class="text-danger">Error fetching user data.</p>`);}
        });
    });

    // Open Edit User modal and populate fields
    $(document).on('click', '.editUserBtn', function(){
        const userId = $(this).data('id');
        if(!userId) return;
        $.ajax({
            url: baseUrl + '/admin/getUser/' + encodeURIComponent(userId),
            type: 'GET',
            dataType: 'json'
        }).done(function(user){
            if(!user || !user.id) {
                const warn = $('<div class="alert alert-warning alert-dismissible fade show" role="alert">' +
                    '<i class="fas fa-exclamation-triangle me-2"></i>User not found.' +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                    '</div>');
                $('.container-fluid').prepend(warn);
                setTimeout(()=>{ warn.fadeOut(400,function(){ $(this).remove(); }); }, 3000);
                return;
            }

            // fill fields
            $('#editUserId').val(user.id || '');
            $('#editFirstName').val(user.first_name || '');
            $('#editLastName').val(user.last_name || '');
            $('#editEmail').val(user.email || '');
            $('#editPhone').val(user.phone || '');
            $('#editGender').val(user.gender || '');
            $('#editAge').val(user.age || '');
            $('#editFamily').val(user.family_number || '');
            // purok options
            const psel = $('#editPurok'); psel.empty(); psel.append('<option value="">Select Purok</option>');
            for(let i=1;i<=maxPurok;i++){ psel.append('<option value="'+i+'">Purok '+i+'</option>'); }
            if(user.purok) psel.val(user.purok);
            // populate line number, try several common keys returned by the API
            const lineVal = user.line_number || user.line || user.lineNumber || user.line_no || user.lineNo || '';
            $('#editLine').val(lineVal);

            // close the view modal before showing the edit modal so focus is correct
            try { $('#viewUserModal').modal('hide'); } catch(e) { /* ignore */ }
            $('#editPassword').val(''); // keep password hidden and readonly

            // readonly location fields
            $('#editBarangay').val(user.barangay || '');
            $('#editMunicipality').val(user.municipality || '');
            $('#editProvince').val(user.province || '');
            $('#editZip').val(user.zipcode || '');

            // show modal
            try { $('#editUserModal').modal('show'); } catch(e){ console.warn('Failed to show edit modal', e); }
        }).fail(function(){
            const err = $('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                '<i class="fas fa-exclamation-triangle me-2"></i>Failed to load user data.' +
                '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                '</div>');
            $('.container-fluid').prepend(err);
            setTimeout(()=>{ err.fadeOut(400,function(){ $(this).remove(); }); }, 4000);
        });
    });

    // Submit Edit User form
    $('#editUserForm').on('submit', function(e){
        e.preventDefault();
        const userId = $('#editUserId').val();
        if(!userId) return;
        // Client-side: ensure line_number contains only letters and digits
        const editLineVal = $('#editLine').val() || '';
        if (!/^[A-Za-z0-9]*$/.test(editLineVal)) {
            const errorAlert = $('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                '<i class="fas fa-exclamation-triangle me-2"></i>Line number may only contain letters and numbers.' +
                '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                '</div>');
            $('#editUserForm').prepend(errorAlert);
            return;
        }
        const btn = $('#saveEditUserBtn');
        btn.prop('disabled', true).text('Saving...');
        const data = $(this).serialize();
        $.ajax({
            url: baseUrl + '/admin/updateUser/' + encodeURIComponent(userId),
            type: 'POST',
            data: data,
            dataType: 'json'
        }).done(function(res){
            if(res && res.success){
                const successAlert = $('<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                    '<i class="fas fa-check-circle me-2"></i>' + (res.message || 'User updated successfully.') +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                    '</div>');
                $('.container-fluid').prepend(successAlert);
                try { $('#editUserModal').modal('hide'); } catch(_) {}
                loadUsers($('#filterInput').val(), $('#filterPurok').val(), $('#filterStatus').val());
            } else {
                const msg = res && res.message ? res.message : 'Failed to update user.';
                const errorAlert = $('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                    '<i class="fas fa-exclamation-triangle me-2"></i>' + msg +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                    '</div>');
                $('#editUserForm').prepend(errorAlert);
            }
        }).fail(function(xhr){
            const errorAlert = $('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                '<i class="fas fa-exclamation-triangle me-2"></i>Error updating user. Please try again.' +
                '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                '</div>');
            $('#editUserForm').prepend(errorAlert);
        }).always(function(){
            btn.prop('disabled', false).html('Save Changes');
        });
    });

    // Print
    $('#printUsersBtn').on('click',function(){
        const printWindow = window.open('','', 'width=900,height=700');
        const tableHTML = document.getElementById('usersTable').outerHTML;
        printWindow.document.write(`
            <html>
                <head>
                    <title>Registered Users</title>
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
                </head>
                <body class="p-4">
                    <h4 class="text-center mb-4">Registered Users</h4>
                    ${tableHTML}
                    <script>window.print();<\/script>
                </body>
            </html>
        `);
        printWindow.document.close();
    });

    // Show Add User Modal
    $('#addUserBtn').on('click',function(){ $('#addUserModal').modal('show'); });

    // Toggle password visibility
    $('#togglePassword').on('click', function(){
        const passwordField = $('#addUserPassword');
        const icon = $(this).find('i');
        if(passwordField.attr('type') === 'password'){
            passwordField.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            passwordField.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // Add User / Account Submission with validation
    $('#addUserForm').on('submit',function(e){
        e.preventDefault();

        // Client-side validation
        const form = this;
        if (!form.checkValidity()) {
            e.stopPropagation();
            $(form).addClass('was-validated');
            return;
        }

        // Client-side: ensure line_number contains only letters and digits
        const addLineVal = $(this).find('[name="line_number"]').val() || '';
        if (!/^[A-Za-z0-9]*$/.test(addLineVal)) {
            const errorAlert = $('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                '<i class="fas fa-exclamation-triangle me-2"></i>Line number may only contain letters and numbers.' +
                '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                '</div>');
            $('#addUserForm').prepend(errorAlert);
            return;
        }

        const formData = $(this).serialize();
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Adding...');

        $.ajax({
            url: baseUrl+'/admin/addUser',
            type:'POST',
            data: formData,
            dataType:'json',
            success:function(res){
                console.log('addUser response', res);
                if(res.success){
                    const successAlert = $('<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                        '<i class="fas fa-check-circle me-2"></i>' + res.message +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                        '</div>');
                    $('.container-fluid').prepend(successAlert);
                    // If server reports email delivery problems, show a helpful warning
                    if (res.email_sent === false) {
                        const warn = $('<div class="alert alert-warning alert-dismissible fade show" role="alert">' +
                            '<i class="fas fa-exclamation-triangle me-2"></i>Email not delivered automatically. Check mail server configuration or logs.' +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                            '</div>');
                        $('.container-fluid').prepend(warn);
                        console.warn('addUser email delivery issue', res.email_driver, res.email_debug);
                    }
                    setTimeout(function(){ successAlert.fadeOut(); }, 5000);
                    $('#addUserForm')[0].reset();
                    $('#addUserForm').removeClass('was-validated');
                    $('#addUserModal').modal('hide');
                    loadUsers();
                } else {
                    let errorMsg = res.message || 'Failed to add user.';
                    if(res.errors){
                        errorMsg += '<ul class="mb-0 mt-2">';
                        for(let field in res.errors){
                            errorMsg += '<li>' + res.errors[field] + '</li>';
                        }
                        errorMsg += '</ul>';
                    }
                    const errorAlert = $('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                        '<i class="fas fa-exclamation-triangle me-2"></i>' + errorMsg +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                        '</div>');
                    $('#addUserForm').prepend(errorAlert);
                }
            },
            error:function(xhr, status, err){
                console.error('AJAX Error:', xhr.responseText);
                const errorAlert = $('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                    '<i class="fas fa-exclamation-triangle me-2"></i>Error adding user. Please try again.' +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                    '</div>');
                $('#addUserForm').prepend(errorAlert);
            },
            complete: function(){
                submitBtn.prop('disabled', false).html('<i class="fas fa-plus me-1"></i>Add User');
            }
        });
    });

    // Deactivate flow
    let currentDeactivateUserId = null;
    $(document).on('click', '.deactivateUserBtn', function(){
        if ($(this).data('disabled')) {
            const msg = $(this).attr('title') || 'This user cannot be deactivated.';
            const warn = $(`<div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>${msg}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>`);
            $('.container-fluid').prepend(warn);
            setTimeout(()=>{ warn.fadeOut(400,function(){ $(this).remove(); }); }, 4000);
            return;
        }
        if (!$('#deactivateUserModal').length) return;
        currentDeactivateUserId = $(this).data('id');
        const name = $(this).data('name') || 'this user';
        $('#deactivateUserName').text(name);
        $('#deactivateReason').val('');
        $('#deactivateUserModal').modal('show');
    });

    $('#confirmDeactivateBtn').on('click', function(){
        if (!$('#deactivateUserModal').length) return;
        if(!currentDeactivateUserId) return;
        let formData = $('#deactivateUserForm').serialize();
        formData += '&user_id=' + encodeURIComponent(currentDeactivateUserId);
        const btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Deactivating...');
        $.ajax({
            url: baseUrl + '/admin/deactivateUser/' + currentDeactivateUserId,
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(res){
                if(res && res.success){
                    const successAlert = $('<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                        '<i class="fas fa-check-circle me-2"></i>' + (res.message || 'User deactivated and archived successfully.') +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                        '</div>');
                    $('.container-fluid').prepend(successAlert);
                    // If server reports email delivery problems, surface a warning and log details
                    if (res.email_sent === false) {
                        const warn = $('<div class="alert alert-warning alert-dismissible fade show" role="alert">' +
                            '<i class="fas fa-exclamation-triangle me-2"></i>Email to user was not delivered automatically. Check mail server configuration or logs.' +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                            '</div>');
                        $('.container-fluid').prepend(warn);
                        console.warn('deactivateUser email delivery issue', res.email_driver, res.email_debug);
                    }
                    $('#deactivateUserModal').modal('hide');
                    loadUsers($('#filterInput').val(), $('#filterPurok').val(), $('#filterStatus').val());
                } else {
                    const msg = res && res.message ? res.message : 'Failed to deactivate user.';
                    const errorAlert = $('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                        '<i class="fas fa-exclamation-triangle me-2"></i>' + msg +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                        '</div>');
                    $('#deactivateUserForm').prepend(errorAlert);
                }
            },
            error: function(xhr){
                const errorAlert = $('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                    '<i class="fas fa-exclamation-triangle me-2"></i>Error deactivating user. Please try again.' +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                    '</div>');
                $('#deactivateUserForm').prepend(errorAlert);
            },
            complete: function(){
                btn.prop('disabled', false).html('<i class="fas fa-user-slash me-1"></i>Deactivate');
            }
        });
    });

    // Suspend user flow
    $(document).on('click', '.suspendUserBtn', function(){
        if ($(this).data('disabled')) {
            const msg = $(this).attr('title') || 'This user is already suspended.';
            const warn = $(`<div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>${msg}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>`);
            $('.container-fluid').prepend(warn);
            setTimeout(()=>{ warn.fadeOut(400,function(){ $(this).remove(); }); }, 4000);
            return;
        }

        const userId = $(this).data('id');
        if(!userId) return;
        const btn = $(this);
        const original = btn.html();
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Suspending...');

        $.ajax({
            url: baseUrl + '/admin/suspendUser/' + encodeURIComponent(userId),
            type: 'POST',
            data: { user_id: userId },
            dataType: 'json',
            success: function(res){
                if(res && res.success){
                    const successAlert = $('<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                        '<i class="fas fa-check-circle me-2"></i>' + (res.message || 'User suspended successfully.') +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                        '</div>');
                    $('.container-fluid').prepend(successAlert);
                    $('#viewUserModal').modal('hide');
                    loadUsers($('#filterInput').val(), $('#filterPurok').val(), $('#filterStatus').val());
                } else {
                    const msg = res && res.message ? res.message : 'Failed to suspend user.';
                    const errorAlert = $('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                        '<i class="fas fa-exclamation-triangle me-2"></i>' + msg +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                        '</div>');
                    $('.container-fluid').prepend(errorAlert);
                }
            },
            error: function(xhr){
                const errorAlert = $('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                    '<i class="fas fa-exclamation-triangle me-2"></i>Error suspending user. Please try again.' +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                    '</div>');
                $('.container-fluid').prepend(errorAlert);
            },
            complete: function(){
                btn.prop('disabled', false).html(original);
            }
        });
    });

    // Activate/reactivate flow
    $(document).on('click', '.activateUserBtn, .reactivateUserBtn', function(){
        const userId = $(this).data('id');
        $.ajax({
            url: baseUrl + '/admin/activateUser/' + userId,
            type: 'POST',
            data: { user_id: userId },
            dataType: 'json',
            success: function(res){
                if(res && res.success){
                    const successAlert = $('<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                        '<i class="fas fa-check-circle me-2"></i>' + (res.message || 'User activated successfully.') +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                        '</div>');
                    $('.container-fluid').prepend(successAlert);
                    loadUsers($('#filterInput').val(), $('#filterPurok').val(), $('#filterStatus').val());
                } else {
                    const msg = res && res.message ? res.message : 'Failed to activate user.';
                    const errorAlert = $('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                        '<i class="fas fa-exclamation-triangle me-2"></i>' + msg +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                        '</div>');
                    $('.container-fluid').prepend(errorAlert);
                }
            },
            error: function(xhr){
                const errorAlert = $('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                    '<i class="fas fa-exclamation-triangle me-2"></i>Error activating user. Please try again.' +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                    '</div>');
                $('.container-fluid').prepend(errorAlert);
            }
        });
    });
}

$(document).ready(function(){ initRegisteredUsersPage(); });
</script>
<script>
// Fallback: inject per-view deactivate modal if missing
(function(){
    if (!document.getElementById('globalDeactivateModal') && !document.getElementById('deactivateUserModal')) {
        const html = `
        <div class="modal fade" id="deactivateUserModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Deactivate <span id="deactivateUserName"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="deactivateUserForm">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <label for="deactivateReason" class="form-label">Reason (optional)</label>
                                <textarea class="form-control" id="deactivateReason" name="reason" rows="3" placeholder="Provide a reason (optional)"></textarea>
                            </div>
                        </form>
                        <div class="alert alert-warning mb-0" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>This will archive the user's last 2 years of billings.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmDeactivateBtn">
                            <i class="fas fa-user-slash me-1"></i>Deactivate
                        </button>
                    </div>
                </div>
            </div>
        </div>`;
        document.body.insertAdjacentHTML('beforeend', html);
    const container = document.querySelector('.container-fluid');
    if (!container) return;
    const THRESHOLD = 992;

    function update() {
        const inner = typeof window.innerWidth === 'number' ? window.innerWidth : 0;
        const screenW = (window.screen && window.screen.width) ? window.screen.width : 0;
        if ((inner && inner <= THRESHOLD) || (screenW && screenW <= THRESHOLD)) {
            container.classList.add('force-responsive');
        } else {
            container.classList.remove('force-responsive');
        }
    }

    window.addEventListener('resize', update);
    window.addEventListener('orientationchange', update);
    document.addEventListener('DOMContentLoaded', update);
    update();
})();
</script>