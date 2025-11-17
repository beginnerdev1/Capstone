<!-- registeredUsers.php -->
<style>
:root {
    --primary: #667eea;
    --secondary: #764ba2;
    --success: #10b981;
    --danger: #ef4444;
    --warning: #f59e0b;
    --light: #f3f4f6;
    --border: #e5e7eb;
    --dark: #1f2937;
    --muted: #6b7280;
}
body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    background: linear-gradient(135deg, #f0f4f8 0%, #d9e2ec 100%);
}
.header-wrapper { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: flex-start; gap: 2rem; margin-bottom: 2.5rem; }
.header-icon-box { width: 56px; height: 56px; background: linear-gradient(135deg, var(--primary), var(--secondary)); border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 16px rgba(102,126,234,0.3); }
.header-icon-box i { color: white; font-size: 1.75rem; }
.header-content h1 { font-size: 1.875rem; font-weight: 700; color: var(--dark); margin: 0; }
.header-actions { display: flex; gap: 0.75rem; flex-wrap: wrap; }
.btn { padding: 0.5rem 0.9rem; border-radius: 6px; font-weight: 600; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.9rem; transition: all 0.3s ease; text-decoration: none; }
.btn-primary { background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white; box-shadow: 0 4px 12px rgba(102,126,234,0.3); }
.btn-primary:hover { box-shadow: 0 8px 20px rgba(102,126,234,0.4); transform: translateY(-2px); }
.btn-outline-secondary { background: white; color: var(--muted); border: 1.5px solid var(--border); box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
.btn-outline-secondary:hover { border-color: var(--primary); color: var(--primary); }
.btn-sm { padding: 0.375rem 0.75rem; font-size: 0.85rem; }
.badge { padding: 0.35rem 0.75rem; border-radius: 6px; font-weight: 600; font-size: 0.8rem; }
.badge-success { background-color: var(--success); color: white; }
.badge-warning { background-color: var(--warning); color: white; }
.badge-danger { background-color: var(--danger); color: white; }
.badge-secondary { background-color: var(--muted); color: white; }
.table-responsive { overflow-x: auto; }
.table { margin-bottom: 0; font-size: 0.95rem; width: 100%; }
.table th, .table td { padding: 1rem; text-align: left; vertical-align: middle; }
.table th { background: var(--light); font-weight: 600; text-transform: uppercase; font-size: 0.8rem; border-bottom: 2px solid var(--border); color: var(--muted); }
.table tbody tr { border-bottom: 1px solid var(--border); transition: all 0.2s ease; }
.table tbody tr:hover { background-color: rgba(102,126,234,0.05); }
.table td { white-space: nowrap; }
.card { background: white; border-radius: 14px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); border: 1px solid var(--border); overflow: hidden; position: relative; }
.card-header { background: var(--light); border-bottom: 1px solid var(--border); padding: 1.5rem; }
.card-header h5 { margin: 0; font-weight: 700; color: var(--dark); }
.card-body { padding: 1.5rem; }
.filters-row { display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap; }
.input-group { display: flex; align-items: stretch; }
.input-group-text { background: var(--light); border: 1px solid var(--border); border-right: none; padding: 0.5rem 0.75rem; border-radius: 6px 0 0 6px; color: var(--muted); }
.form-control, .form-select { border: 1px solid var(--border); padding: 0.5rem 0.75rem; border-radius: 6px; font-size: 0.95rem; transition: all 0.2s ease; }
.input-group .form-control { border-radius: 0 6px 6px 0; }
.form-control:focus, .form-select:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(102,126,234,0.1); }
.loading-overlay { position: absolute; top:0; left:0; right:0; bottom:0; background: rgba(255,255,255,0.8); display: none; align-items:center; justify-content:center; z-index:10; }
.loading-overlay.show { display:flex; }
.spinner { width:40px; height:40px; border:4px solid var(--border); border-top-color: var(--primary); border-radius:50%; animation: spin 0.8s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }
.results-info { padding: 1rem 1.5rem; background: var(--light); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); color: var(--muted); font-size:0.9rem; }
@media(max-width:768px){
    .header-wrapper{flex-direction:column;gap:1rem;align-items:flex-start;}
    .header-content h1{font-size:1.5rem;}
    .header-actions{width:100%;}
    .btn{flex:1;justify-content:center;}
    .filters-row{flex-direction:column;}
}
</style>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-primary"><i class="fas fa-users me-2"></i>Registered Users</h4>
        <div>
            <button id="printUsersBtn" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-print me-1"></i> Print
            </button>
            <button id="addUserBtn" class="btn btn-primary btn-sm">
                <i class="fas fa-user-plus me-1"></i> Add User
            </button>
        </div>
    </div>

    <!-- Filter section -->
    <div class="row mb-3">
        <div class="col-md-4">
            <input type="text" id="filterInput" class="form-control" placeholder="Search by name or email...">
        </div>
        <div class="col-md-3">
            <select id="filterPurok" class="form-select">
                <option value="">All Puroks</option>
            </select>
        </div>
        <div class="col-md-3">
            <select id="filterStatus" class="form-select">
                <option value="">All Status</option>
                <option value="approved">Approved</option>
                <option value="pending">Pending</option>
                <option value="suspended">Suspended</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>
        <div class="col-md-2">
            <button id="resetFilters" class="btn btn-outline-secondary w-100">
                <i class="fas fa-redo me-1"></i> Reset
            </button>
        </div>
    </div>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table table-bordered align-middle" id="usersTable">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Purok</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody><!-- AJAX will load rows here --></tbody>
        </table>
    </div>
</div>

<!-- View User Modal -->
<div class="modal fade" id="viewUserModal" tabindex="-1" aria-labelledby="viewUserLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
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

<!-- Add User / Add Account Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
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
                    <!-- Fixed Location (read-only for consistency) -->
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
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
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
                        <button class="btn btn-sm btn-outline-danger deactivateUserBtn" data-id="${user.id}" data-name="${fullName}" ${canDeactivate ? '' : 'disabled'} title="${deactivateTitle}">
                            <i class="fas fa-user-slash"></i> Deactivate
                        </button>`;
                    tbody.append(`
                        <tr>
                            <td>${index+1}</td>
                            <td>${fullName}</td>
                            <td>${user.email}</td>
                            <td>${user.purok||'-'}</td>
                            <td><span class="badge bg-${badgeClass}">${user.status||'-'}</span></td>
                            <td>
                                <button class="btn btn-sm btn-primary viewUserBtn" data-id="${user.id}" title="View user details">
                                    <i class="fas fa-eye"></i> View
                                </button>
                                ${deactivateBtn}
                            </td>
                        </tr>
                    `);
                });
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
