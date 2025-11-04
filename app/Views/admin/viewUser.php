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

        <div class="row mb-4">
            <div class="col-md-6">
                <label>Status</label>
                <input type="text" class="form-control" value="<?= $user['active'] ? 'Active' : 'Inactive' ?>" readonly>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <a href="<?= base_url('admin/registeredUsers') ?>" class="btn btn-secondary">Back</a>
            <a href="<?= base_url('admin/deactivateUser/'.$user['id']) ?>" 
               class="btn btn-danger"
               onclick="return confirm('Are you sure you want to deactivate this user?');">
               Deactivate User
            </a>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
