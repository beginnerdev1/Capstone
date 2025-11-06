<?= $this->extend('admin/layouts/main.php') ?>
<?= $this->section('content') ?>

<div class="container mt-5">
    <h3 class="mb-4"><?= esc($title) ?></h3>

    <form>
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">First Name</label>
                <input type="text" class="form-control" value="<?= esc($user['first_name']) ?>" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label">Last Name</label>
                <input type="text" class="form-control" value="<?= esc($user['last_name']) ?>" readonly>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="text" class="form-control" value="<?= esc($user['email']) ?>" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label">Barangay</label>
                <input type="text" class="form-control" value="<?= esc($user['barangay']) ?>" readonly>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Municipality</label>
                <input type="text" class="form-control" value="<?= esc($user['municipality']) ?>" readonly>
            </div>
            <div class="col-md-4">
                <label class="form-label">Purok</label>
                <input type="text" class="form-control" value="<?= esc($user['purok']) ?>" readonly>
            </div>
            <div class="col-md-4">
                <label class="form-label">Province</label>
                <input type="text" class="form-control" value="<?= esc($user['province']) ?>" readonly>
            </div>
        </div>

        <?php
        // Map numeric statuses to human-readable labels
        $statusLabels = [
            2  => 'Active',
            1  => 'Inactive',
            0  => 'Pending',
            -1 => 'Suspended'
        ];
        ?>

        <div class="row mb-4">
            <div class="col-md-6">
                <label>Status</label>
                <input type="text" class="form-control"
                       value="<?= $statusLabels[$user['active']] ?? 'Unknown' ?>" readonly>
            </div>
        </div>

        <!-- Action buttons based on status -->
        <div class="d-flex justify-content-between">
            <a href="<?= base_url('admin/registeredUsers') ?>" class="btn btn-secondary">Back</a>

            <?php if ($user['active'] == 2): // Active ?>
                <a href="<?= base_url('admin/deactivateUser/'.$user['id']) ?>"
                   class="btn btn-danger"
                   onclick="return confirm('Are you sure you want to deactivate this user?');">Deactivate</a>

                <a href="<?= base_url('admin/suspendUser/'.$user['id']) ?>"
                   class="btn btn-warning"
                   onclick="return confirm('Are you sure you want to suspend this user?');">Suspend</a>

            <?php elseif ($user['active'] == 1): // Inactive ?>
                <a href="<?= base_url('admin/activateUser/'.$user['id']) ?>"
                   class="btn btn-success"
                   onclick="return confirm('Are you sure you want to activate this user?');">Activate</a>

            <?php elseif ($user['active'] == -1): // Suspended ?>
                <a href="<?= base_url('admin/activateUser/'.$user['id']) ?>"
                   class="btn btn-success"
                   onclick="return confirm('Are you sure you want to activate this suspended user?');">Activate</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
