<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4">
  <h1 class="mt-4 text-center fw-bold">Profile</h1>

  <div class="row justify-content-center">
    <div class="col-lg-6">
      <div class="card shadow-sm border-0 profile-card glass-card">
        <div class="card-body text-center">

          <!-- Profile Picture -->
          <div class="profile-image-wrapper mb-3">
            <img src="<?= base_url('uploads/profile/' . ($admin['profile_picture'] ?? 'default.png')) ?>"
                 alt="Profile Picture"
                 class="rounded-circle profile-image shadow">
          </div>

          <h4 class="fw-bold mb-1">
            <?= esc(($admin['first_name'] ?? '') . ' ' . ($admin['middle_name'] ?? '') . ' ' . ($admin['last_name'] ?? '')) ?>
          </h4>
          <p class="text-muted mb-3"><?= esc($admin['email'] ?? '') ?></p>

          <!-- Profile Form -->
          <form action="<?= base_url('admin/updateProfile') ?>" method="post" enctype="multipart/form-data">
            
            <div class="row g-3 text-start">
              <div class="col-md-4">
                <label for="first_name" class="form-label fw-semibold">First Name</label>
                <input type="text" class="form-control" name="first_name" id="first_name"
                       value="<?= esc($admin['first_name'] ?? '') ?>" required>
              </div>

              <div class="col-md-4">
                <label for="middle_name" class="form-label fw-semibold">Middle Name</label>
                <input type="text" class="form-control" name="middle_name" id="middle_name"
                       value="<?= esc($admin['middle_name'] ?? '') ?>" required>
              </div>

              <div class="col-md-4">
                <label for="last_name" class="form-label fw-semibold">Last Name</label>
                <input type="text" class="form-control" name="last_name" id="last_name"
                       value="<?= esc($admin['last_name'] ?? '') ?>" required>
              </div>
            </div>

            <div class="mt-3 text-start">
              <label for="email" class="form-label fw-semibold">Email</label>
              <input type="email" class="form-control" name="email" id="email"
                     value="<?= esc($admin['email'] ?? '') ?>" required>
            </div>

            <div class="mt-3 text-start">
              <label for="profile_picture" class="form-label fw-semibold">Profile Picture</label>
              <input type="file" class="form-control" name="profile_picture" id="profile_picture" accept="image/*">
            </div>

            <button type="submit" class="btn btn-primary w-100 fw-semibold mt-4">Update Profile</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
