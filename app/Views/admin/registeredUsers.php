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
.controls { display:flex; gap:12px; align-items:center; }

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
}

/* Buttons */
.btn { display:inline-flex; align-items:center; gap:8px; padding:8px 12px; border-radius: 10px; font-weight:600; cursor:pointer; border:none; transition:all .12s ease; font-size:0.9rem; font-family: var(--font-sans); }
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
.actions { display:flex; gap:8px; justify-content:flex-end; }

/* Modals use Bootstrap classes; keep their look consistent with a subtle radius */
.modal .modal-content { border-radius: 12px; }

/* Responsive */
@media (max-width:860px){
    .search input { width: 120px; }
    .table { min-width: 640px; }
    .filters { flex-direction:column; align-items:stretch; }
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

<!-- View User Modal (keeps original Bootstrap markup, functionality preserved) -->
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

<!-- Add User Modal (original form preserved) -->
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

                    <!-- Remainder of the form preserved as in original file -->
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

            <!-- Deactivate modal now provided globally in layout; removed here to avoid duplicates -->
</div>

<!-- ✅ JavaScript Logic -->
<script>
function initRegisteredUsersPage() {
    const baseUrl = "<?= base_url() ?>";
    const maxPurok = 7;

    console.log("✅ Registered Users page initialized!");

    // sa pag add naman ito ng user? 
    let addSelect = document.getElementById('addUserPurok');
    addSelect.innerHTML = '<option value="">Select Purok</option>'; // clear first
    for(let i=1; i<=maxPurok; i++){
        let opt = document.createElement('option');
        opt.value = i;
        opt.text = 'Purok '+i;
        addSelect.appendChild(opt);
    }

    //Para sa pag filter ng puroks
    let filterSelect = document.getElementById('filterPurok');
    filterSelect.innerHTML = '<option value="">All</option>'; // reset
    for (let i = 1; i <= maxPurok; i++) {
        let opt = document.createElement('option');
        opt.value = i;
        opt.text = 'Purok ' + i;
        filterSelect.appendChild(opt);
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
                    else if(statusLower==='suspended') badgeClass='dark';
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
                    tbody.append(`
                        <tr>
                            <td>${index+1}</td>
                            <td>${fullName}</td>
                            <td>${user.email}</td>
                            <td>${user.purok||'-'}</td>
                            <td><span class="badge bg-${badgeClass}">${user.status||'-'}</span></td>
                            <td class="actions">
                                <button class="btn btn-sm btn-primary viewUserBtn" data-id="${user.id}" title="View user details">
                                    <i class="fas fa-eye"></i> View
                                </button>
                                ${deactivateBtn}
                            </td>
                        </tr>
                    `);
                });
                setUsersCount(users.length);
            },
            error:function(xhr,status,error){
                console.error("❌ AJAX Error:", status,error,xhr.responseText);
            }
        });
    }

    loadUsers();

    // Debounce for search input (wait 500ms after user stops typing)
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

    // Filters - only run when explicitly changed
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
                if(!user.id){ $("#userDetailsContent").html(`<p class="text-danger">User not found.</p>`); return; }
                let profileImg = user.profile_picture
                    ? `<img src="<?= base_url('uploads/profile_pictures') ?>/${user.profile_picture}" class="rounded-circle mb-3" width="100" height="100">`
                    : `<i class="fas fa-user-circle fa-5x text-muted mb-3"></i>`;
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
                            <p><strong>Barangay:</strong> ${user.barangay||'-'}</p>
                            <p><strong>Municipality:</strong> ${user.municipality||'-'}</p>
                            <p><strong>Province:</strong> ${user.province||'-'}</p>
                            <p><strong>Zip Code:</strong> ${user.zipcode||'-'}</p>
                            <p><strong>Status:</strong> <span class="badge bg-info">${user.status||'-'}</span></p>
                        </div>
                    </div>
                `);
                $("#viewUserModal").modal("show");
            },
            error:function(){ $("#userDetailsContent").html(`<p class="text-danger">Error fetching user data.</p>`);}
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

        const formData = $(this).serialize();
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Adding...');
        
        $.ajax({
            url: baseUrl+'/admin/addUser',
            type:'POST',
            data: formData,
            dataType:'json',
            success:function(res){
                if(res.success){
                    // Show success message
                    const successAlert = $('<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                        '<i class="fas fa-check-circle me-2"></i>' + res.message +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                        '</div>');
                    $('.container-fluid').prepend(successAlert);
                    
                    // Auto-dismiss after 5 seconds
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

    // Deactivate flow (legacy per-view handler). Guard so global modal handles by default.
    let currentDeactivateUserId = null;
    $(document).on('click', '.deactivateUserBtn', function(){
        // If button was rendered as disabled (has data-disabled), show a warning instead of doing nothing
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
        if (!$('#deactivateUserModal').length) return; // global handler will take over
        currentDeactivateUserId = $(this).data('id');
        const name = $(this).data('name') || 'this user';
        $('#deactivateUserName').text(name);
        $('#deactivateReason').val('');
        $('#deactivateUserModal').modal('show');
    });

    $('#confirmDeactivateBtn').on('click', function(){
        if (!$('#deactivateUserModal').length) return; // handled globally
        if(!currentDeactivateUserId) return;
        let formData = $('#deactivateUserForm').serialize();
        // Always append user_id to the form data
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

    // Activate/reactivate flow: always send user_id
    $(document).on('click', '.activateUserBtn, .reactivateUserBtn', function(){
        const userId = $(this).data('id');
        const name = $(this).data('name') || 'this user';
        // If you have a modal for activation/reactivation, show it here
        // Otherwise, send AJAX directly
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
                    // If you have a form, prepend to it; else prepend to container
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
// Fallback: if the page is loaded directly (not via Dashboard wrapper),
// auto-inject a per-view deactivate modal so legacy handlers work.
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
    }
})();
</script>
