
<div class="container my-4">
    <h2 class="mb-4">User Profile</h2>
    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <img src="https://ui-avatars.com/api/?name=John+Doe&background=0d6efd&color=fff&size=128" alt="User Avatar" class="rounded-circle mb-3" width="100" height="100">
                    <h5 class="card-title mb-0">John Doe</h5>
                    <div class="text-muted mb-2">johndoe@email.com</div>
                    <span class="badge bg-primary">Active</span>
                </div>
            </div>
        </div>
        <div class="col-md-8 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Profile Details</h5>
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Full Name</dt>
                        <dd class="col-sm-8">John Doe</dd>
                        <dt class="col-sm-4">Email</dt>
                        <dd class="col-sm-8">johndoe@email.com</dd>
                        <dt class="col-sm-4">Contact</dt>
                        <dd class="col-sm-8">0917-123-4567</dd>
                        <dt class="col-sm-4">Address</dt>
                        <dd class="col-sm-8">123 Main St, Barangay Example, City</dd>
                        <dt class="col-sm-4">Account Status</dt>
                        <dd class="col-sm-8"><span class="badge bg-success">Verified</span></dd>
                    </dl>
                    <a href="<?= base_url('users/editprofile') ?>" class="btn btn-outline-primary btn-sm mt-3">Edit Profile</a>
                    <a href="<?= base_url('users/changepassword') ?>" class="btn btn-outline-secondary btn-sm mt-3">Change Password</a>
                </div>
            </div>
        </div>
    </div>
</div>