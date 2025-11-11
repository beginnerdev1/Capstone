<style>
    :root {
        --primary: #667eea;
        --primary-dark: #5568d3;
        --secondary: #764ba2;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --light: #f3f4f6;
        --border: #e5e7eb;
        --dark: #1f2937;
        --muted: #6b7280;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        background: linear-gradient(135deg, #f0f4f8 0%, #d9e2ec 100%);
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    }

    .container-fluid {
        padding: 2rem 1rem;
    }

    /* Header Section */
    .header-wrapper {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: flex-start;
        gap: 2rem;
        margin-bottom: 2.5rem;
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 1.25rem;
    }

    .header-icon-box {
        width: 56px;
        height: 56px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 16px rgba(102, 126, 234, 0.3);
        flex-shrink: 0;
    }

    .header-icon-box i {
        color: white;
        font-size: 1.75rem;
    }

    .header-content h1 {
        font-size: 1.875rem;
        font-weight: 700;
        color: var(--dark);
        margin: 0 0 0.5rem 0;
    }

    .breadcrumb {
        padding: 0 !important;
        background: transparent !important;
        margin: 0 !important;
    }

    .breadcrumb-item.active {
        color: var(--muted);
        font-size: 0.875rem;
    }

    .header-actions {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    /* Buttons */
    .btn {
        padding: 0.625rem 1.25rem;
        border-radius: 8px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
        text-decoration: none;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .btn-primary:hover {
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        transform: translateY(-2px);
    }

    .btn-outline-secondary {
        background: white;
        color: var(--muted);
        border: 1.5px solid var(--border);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    }

    .btn-outline-secondary:hover {
        border-color: var(--primary);
        color: var(--primary);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    /* Card */
    .card {
        background: white;
        border-radius: 14px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--border);
        overflow: hidden;
    }

    .card-header {
        background: linear-gradient(135deg, var(--light) 0%, #f9fafb 100%);
        border-bottom: 1px solid var(--border);
        padding: 1.5rem;
    }

    .card-header h5 {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--dark);
        margin: 0;
    }

    .card-header i {
        color: var(--primary);
    }

    .card-body {
        padding: 1.5rem;
    }

    .card-body.pb-2 {
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--border);
    }

    /* Form */
    #userFilterForm {
        gap: 1rem !important;
    }

    .col-12, .col-md-6, .col-lg-5, .col-md-4, .col-lg-3 {
        margin-bottom: 0 !important;
    }

    .input-group-text {
        background: var(--light) !important;
        border: 1px solid var(--border) !important;
        color: var(--muted);
    }

    .form-control,
    .form-select {
        border: 1px solid var(--border);
        padding: 0.625rem 0.75rem;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
    }

    .form-control::placeholder {
        color: var(--muted);
    }

    /* Table */
    .table-responsive {
        overflow-x: auto;
    }

    .table {
        margin-bottom: 0;
        font-size: 0.95rem;
    }

    .table thead {
        background: var(--light);
        border-bottom: 2px solid var(--border);
    }

    .table th {
        color: var(--dark);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.3px;
        padding: 1.25rem;
        border: none;
    }

    .table th.ps-4 {
        padding-left: 1.5rem;
    }

    .table tbody tr {
        border-bottom: 1px solid var(--border);
        transition: all 0.2s ease;
    }

    .table tbody tr:hover {
        background-color: rgba(102, 126, 234, 0.05);
    }

    .table td {
        padding: 1.25rem;
        color: var(--dark);
        border: none;
        vertical-align: middle;
    }

    .table td.ps-4 {
        padding-left: 1.5rem;
    }

    /* Badges */
    .badge {
        font-weight: 600;
        padding: 0.5rem 0.875rem;
        border-radius: 20px;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        border: 1px solid;
    }

    .bg-success {
        background-color: rgba(16, 185, 129, 0.1) !important;
        color: var(--success) !important;
        border-color: rgba(16, 185, 129, 0.2) !important;
    }

    .bg-warning {
        background-color: rgba(245, 158, 11, 0.1) !important;
        color: #92400e !important;
        border-color: rgba(245, 158, 11, 0.2) !important;
    }

    .bg-danger {
        background-color: rgba(239, 68, 68, 0.1) !important;
        color: var(--danger) !important;
        border-color: rgba(239, 68, 68, 0.2) !important;
    }

    .bg-secondary {
        background-color: rgba(107, 114, 128, 0.1) !important;
        color: var(--muted) !important;
        border-color: rgba(107, 114, 128, 0.2) !important;
    }

    /* Action Buttons */
    .btn-sm {
        padding: 0.5rem 0.75rem;
        font-size: 0.85rem;
        height: 36px;
        width: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0;
    }

    .btn-outline-primary {
        background: rgba(102, 126, 234, 0.1);
        color: var(--primary);
        border: 1px solid rgba(102, 126, 234, 0.2);
    }

    .btn-outline-primary:hover {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    .btn-outline-danger {
        background: rgba(239, 68, 68, 0.1);
        color: var(--danger);
        border: 1px solid rgba(239, 68, 68, 0.2);
    }

    .btn-outline-danger:hover {
        background: var(--danger);
        color: white;
        border-color: var(--danger);
    }

    /* Card Footer */
    .card-footer {
        background: var(--light) !important;
        border-top: 1px solid var(--border) !important;
        padding: 1rem 1.5rem !important;
        color: var(--muted) !important;
        font-size: 0.9rem;
    }

    .card-footer i {
        color: var(--primary);
    }

    /* Empty State */
    .text-center.py-4 {
        padding: 3rem 1rem !important;
    }

    .text-muted {
        color: var(--muted) !important;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .header-wrapper {
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start;
        }

        .header-content h1 {
            font-size: 1.5rem;
        }

        .header-actions {
            width: 100%;
        }

        .btn {
            flex: 1;
            justify-content: center;
        }

        .table {
            font-size: 0.85rem;
        }

        .table th,
        .table td {
            padding: 0.875rem 0.5rem;
        }

        .table th.ps-4,
        .table td.ps-4 {
            padding-left: 0.5rem;
        }
    }

    @media print {
        body {
            background: white;
        }

        .container-fluid {
            padding: 0;
        }

        .header-wrapper,
        .card-body.pb-2,
        .header-actions,
        .card-footer {
            display: none;
        }

        .card {
            box-shadow: none;
            border: none;
        }

        .card-header {
            page-break-inside: avoid;
        }
    }
</style>

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 header-wrapper">
        <div>
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                <div class="header-icon-box">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="header-content">
                    <h1 style="margin: 0;">
                        User Management
                    </h1>
                </div>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb small text-muted mt-1 mb-0">
                    <li class="breadcrumb-item active" aria-current="page">All Registered Users</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 header-actions">
            <a href="<?= site_url('admin/addUser') ?>" 
               class="btn btn-primary d-flex align-items-center">
                <i class="bi bi-person-plus-fill"></i> Add User
            </a>
            <button class="btn btn-outline-secondary d-flex align-items-center" onclick="printTable()">
                <i class="bi bi-printer"></i> Print
            </button>
        </div>
    </div>

    <!-- Card -->
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-white border-bottom">
            <h5 class="fw-bold mb-0 text-dark">
                <i class="bi bi-list-check me-2"></i> Registered Users
            </h5>
        </div>

        <!-- Filters -->
        <div class="card-body pb-2">
            <form id="userFilterForm" class="row g-3">
                <!-- Search -->
                <div class="col-12 col-md-6 col-lg-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" id="searchInput"
                               placeholder="Search name, email, or ID..."
                               class="form-control">
                    </div>
                </div>
                <!-- Purok Filter -->
                <div class="col-12 col-md-4 col-lg-3">
                    <select name="purok" id="purokSelect" class="form-select form-select-sm">
                        <option value="">All Puroks</option>
                        <?php foreach ($puroks as $p): ?>
                            <option value="<?= $p ?>">
                                <?= esc($purokLabel ?? 'Purok') ?> <?= esc($p) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table id="userTable" class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4 text-start">ID</th>
                        <th class="text-start">Full Name</th>
                        <th>Location</th>
                        <th class="d-none d-lg-table-cell">Email</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="userTableBody">
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td class="ps-4"><?= esc($user['id']) ?></td>
                        <td><?= esc($user['name']) ?></td>
                        <td><?= esc($user['location']) ?></td>
                        <td class="d-none d-lg-table-cell"><?= esc($user['email']) ?></td>
                        <td>
                            <?php if ($user['status'] === 'Active'): ?>
                                <span class="badge bg-success">Active</span>
                            <?php elseif ($user['status'] === 'Pending'): ?>
                                <span class="badge bg-warning text-dark">Pending</span>
                            <?php elseif ($user['status'] === 'Suspended'): ?>
                                <span class="badge bg-danger">Suspended</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Unknown</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= site_url('admin/editUser/'.$user['id']) ?>" 
                               class="btn btn-sm btn-outline-primary">
                               <i class="bi bi-pencil-square"></i>
                            </a>
                            <a href="<?= site_url('admin/deleteUser/'.$user['id']) ?>" 
                               class="btn btn-sm btn-outline-danger">
                               <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                    <!-- Fallback if no users -->
                    <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox me-2"></i> No users found...
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="card-footer bg-light border-top small text-muted">
            <i class="bi bi-info-circle me-1"></i> Data refreshes automatically when filters change.
        </div>
    </div>
</div>

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
</script>