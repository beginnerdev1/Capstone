<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-primary fw-bold mb-1">
                <i class="fas fa-users me-2"></i> User Management
            </h1>
            <ol class="breadcrumb small mb-0">
                <li class="breadcrumb-item active">All Users</li>
            </ol>
        </div>
        <button class="btn btn-outline-secondary btn-sm" onclick="printTable()">
            <i class="fas fa-print me-1"></i> Print
        </button>
    </div>

    <!-- Card -->
    <div class="card shadow-sm border-0">
        <!-- Header: Search & Filter -->
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <span class="fw-semibold text-dark">
                <i class="fas fa-list me-1"></i> Registered Users
            </span>
            <form id="userFilterForm" class="d-flex align-items-center">
                <input type="text" name="search" id="searchInput"
                       placeholder="Search name or email"
                       class="form-control form-control-sm me-2" style="max-width:200px;">
                <select name="purok" id="purokSelect" class="form-select form-select-sm me-2">
                    <option value="">All Puroks</option>
                    <?php foreach ($puroks as $p): ?>
                        <option value="<?= $p ?>"><?= esc($purokLabel ?? 'Purok') ?> <?= esc($p) ?></option>
                    <?php endforeach; ?>
                </select>
                <button class="btn btn-sm btn-primary" type="submit">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
            </form>
        </div>

        <!-- Table -->
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="userTable" class="table table-hover table-striped table-bordered mb-0 align-middle text-center">
                    <thead class="table-primary text-dark">
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Purok</th>
                            <th>Barangay</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Actions</th>  
                        </tr>
                    </thead>
                    <tbody id="userTableBody">
                        <tr><td colspan="7" class="text-muted">Loading users...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Print Script -->
<script>
function printTable() {
    const printContents = document.getElementById('userTable').outerHTML;
    const originalContents = document.body.innerHTML;
    document.body.innerHTML = `
        <div style="text-align:center; margin-bottom:20px;">
            <h2>Registered Users</h2>
            <small>Generated on: ${new Date().toLocaleString()}</small>
        </div>
        ${printContents}
    `;
    window.print();
    document.body.innerHTML = originalContents;
}

// Load users via AJAX
function loadUsers(params = {}) {
    $.ajax({
        url: "<?= site_url('admin/registeredUsers') ?>",
        type: "GET",
        data: params,
        dataType: "json",
        success: function(users) {
            const tbody = $("#userTableBody");
            tbody.empty();

            if (users.length === 0) {
                tbody.append('<tr><td colspan="7" class="text-muted">No users found.</td></tr>');
                return;
            }

            users.forEach(user => {
                const statusBadge = user.status === 'approved'
                    ? '<span class="badge bg-success">Approved</span>'
                    : user.status === 'rejected'
                        ? '<span class="badge bg-danger">Rejected</span>'
                        : '<span class="badge bg-warning text-dark">Pending</span>';

                tbody.append(`
                    <tr>
                        <td>${user.id}</td>
                        <td class="fw-semibold">${user.first_name} ${user.last_name}</td>
                        <td>${user.purok}</td>
                        <td>${user.barangay}</td>
                        <td class="text-muted">${user.email}</td>
                        <td>${statusBadge}</td>
                        <td>
                            <a href="<?= base_url('admin/viewUser/') ?>${user.id}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                `);
            });
        },
        error: function() {
            $("#userTableBody").html('<tr><td colspan="7" class="text-danger">Failed to load users.</td></tr>');
        }
    });
}

// On page load
$(document).ready(function() {
    loadUsers();

    // Filter form submit
    $("#userFilterForm").on("submit", function(e) {
        e.preventDefault();
        loadUsers({
            search: $("#searchInput").val(),
            purok: $("#purokSelect").val()
        });
    });

    // Reload when purok changes directly
    $("#purokSelect").on("change", function() {
        loadUsers({
            search: $("#searchInput").val(),
            purok: $(this).val()
        });
    });
});
</script>

<?= $this->endSection() ?>
