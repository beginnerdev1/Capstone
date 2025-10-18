<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<h1 class="mt-4">Registered Users</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item active">User Management</li>
</ol>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-users me-1"></i> Registered Users</span>
        <form method="get" action="<?= site_url('admin/registeredUsers') ?>" class="d-flex">
            <select name="purok" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All Puroks</option>
                <?php foreach ($puroks as $p): ?>
                    <option value="<?= $p ?>" <?= ($selectedPurok == $p) ? 'selected' : '' ?>>
                        Purok <?= esc($p) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <div class="card-body">
        <table class="table table-bordered table-hover text-center align-middle">
            <thead class="table-primary">
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Username</th>
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
                            <td><?= esc($user['Firstname'] . ' ' . $user['Surname']) ?></td>
                            <td><?= esc($user['username']) ?></td>
                            <td><?= esc($user['Purok']) ?></td>
                            <td><?= esc($user['Barangay']) ?></td>
                            <td><?= esc($user['email']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6">No users found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
