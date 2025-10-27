<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1 class="mt-4">Registered Users</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">User Management</li>
    </ol>

    <div class="card mb-4">
        <!-- Header: Title + Filter + Print -->
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-users me-1"></i> Registered Users</span>
            <div class="d-flex align-items-center">
                <!-- Filter by Purok -->
                <form method="get" action="<?= site_url('admin/registeredUsers') ?>" class="d-flex me-2">
                    <select name="purok" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Puroks</option>
                        <?php foreach ($puroks as $p): ?>
                            <option value="<?= $p ?>" <?= ($selectedPurok == $p) ? 'selected' : '' ?>>
                                Purok <?= esc($p) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>

                <!-- Print Button -->
                <button class="btn btn-primary btn-sm" onclick="printTable()">🖨️ Print</button>
            </div>
        </div>

        <!-- User Table -->
        <div class="card-body table-responsive">
            <table id="userTable" class="table table-bordered table-hover text-center align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Purok</th>
                        <th>Barangay</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= esc($user['id']) ?></td>
                                <td><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></td>
                                <td><?= esc($user['purok']) ?></td>
                                <td><?= esc($user['barangay']) ?></td>
                                <td><?= esc($user['email']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5">No users found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
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
</script>

<?= $this->endSection() ?>
