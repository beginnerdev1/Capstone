<DOCTYPE html>
<ul class="nav flex-column">
<!--This is the index.php -->
  <li class="nav-item">
    <a class="nav-link active" href="<?= base_url('users/') ?>">
      <i class="bi bi-speedometer2 me-2"></i> Dashboard
    </a>
  </li>
<!--This is the payments.php -->
  <li class="nav-item">
    <a class="nav-link" href="<?= base_url('users/payments') ?>">
      <i class="bi bi-receipt me-2"></i> My Bills
    </a>
  </li>
<!--This is the report.php -->
  <li class="nav-item">
    <a class="nav-link" href="<?= base_url('users/report') ?>">
      <i class="bi bi-exclamation-triangle me-2"></i> Report Issue
    </a>
  </li>
<!--This is the billing.php -->
  <li class="nav-item">
    <a class="nav-link" href="<?= base_url('users/billing') ?>">
      <i class="bi bi-clock-history me-2"></i> Billing History
    </a>
  </li>
<!--This is the pressure.php -->
  <li class="nav-item">
    <a class="nav-link" href="<?= base_url('users/pressure') ?>">
      <i class="bi bi-droplet me-2"></i> My Water Pressure
    </a>
  </li>
<!--This is the profile.php -->
  <li class="nav-item">
    <a class="nav-link" href="<?= base_url('users/profile') ?>">
      <i class="bi bi-gear me-2"></i> Profile
    </a>
  </li>
</ul>