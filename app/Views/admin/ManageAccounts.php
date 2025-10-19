<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<h1 class="mt-4">Manage Accounts</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item active">Control and oversee user accounts</li>
</ol>

<div class="card mb-4 shadow-sm">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <span><i class="fas fa-users-cog me-2"></i> User Accounts</span>
        <form class="d-flex" method="get" action="<?= site_url('admin/manage-accounts') ?>">
            <input class="form-control form-control-sm me-2" type="search" name="search" placeholder="Search user..." aria-label="Search">
            <button class="btn btn-light btn-sm" type="submit"><i class="fas fa-search"></i></button>
        </form>
    </div>

    <div class="card-body table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-primary">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Barangay</th>
                    <th>Purok</th>
                    <th>Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $index => $u): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= esc($u['first_name'] . ' ' . $u['last_name']) ?></td>
                            <td><?= esc($u['username']) ?></td>
                            <td><?= esc($u['email']) ?></td>
                            <td><?= esc($u['phone']) ?></td>
                            <td><?= esc($u['barangay']) ?></td>
                            <td><?= esc($u['purok']) ?></td>
                            <td><?= $user['is_verified'] ? 'Verified' : 'Not Verified' ?></td>
                            <td class="text-center">
                                <a href="#" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                <a href="#" class="btn btn-sm btn-outline-warning"><i class="fas fa-user-slash"></i></a>
                                <a href="#" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="9" class="text-center text-muted">No users found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
