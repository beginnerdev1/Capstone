<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>

<h1 class="mb-4">Manage Accounts</h1>

<!-- Flash messages -->
<?php if (session()->getFlashdata('success')): ?>
<div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
<div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<!-- QR Code Section -->
<div class="card mb-4 shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="m-0">Payment QR Code</h5>
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#qrModal">
            Change QR
        </button>
    </div>
    <div class="card-body text-center">
        <?php if (!empty($payment['qr_image'])): ?>
            <img src="<?= base_url($payment['qr_image']) ?>" alt="QR Code" class="img-fluid" style="max-width: 200px;">
        <?php else: ?>
            <p>No QR code uploaded yet.</p>
        <?php endif; ?>
        <p class="mt-2">
            <strong><?= esc($payment['payment_method'] ?? '') ?></strong><br>
            <?= esc($payment['account_name'] ?? '') ?><br>
            <?= esc($payment['account_number'] ?? '') ?>
        </p>
    </div>
</div>

<!-- QR Upload Modal -->
<div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" action="<?= site_url('admin/updateQR') ?>" enctype="multipart/form-data" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="qrModalLabel">Upload New QR</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label>Payment Method</label>
          <input type="text" name="payment_method" class="form-control" value="<?= esc($payment['payment_method'] ?? '') ?>">
        </div>
        <div class="mb-3">
          <label>Account Name</label>
          <input type="text" name="account_name" class="form-control" value="<?= esc($payment['account_name'] ?? '') ?>">
        </div>
        <div class="mb-3">
          <label>Account Number</label>
          <input type="text" name="account_number" class="form-control" value="<?= esc($payment['account_number'] ?? '') ?>">
        </div>
        <div class="mb-3">
          <label>Upload QR Image</label>
          <input type="file" name="qr_image" class="form-control" accept="image/*" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success">Save</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </form>
  </div>
</div>

<!-- Tabs for filtering -->
<ul class="nav nav-tabs mb-3">
    <?php 
    $tabs = ['All' => '', 'Pending' => 'Pending', 'Paid' => 'Paid', 'Over the Counter' => 'Over the Counter'];
    foreach ($tabs as $label => $value): 
        $active = ($selectedStatus == $value) ? 'active' : '';
    ?>
        <li class="nav-item">
            <a class="nav-link <?= $active ?>" 
               href="<?= site_url('admin/manageAccounts?status=' . urlencode($value) . '&search=' . urlencode($search ?? '')) ?>">
                <?= esc($label) ?>
            </a>
        </li>
    <?php endforeach; ?>
</ul>

<!-- Search bar -->
<form method="get" action="<?= site_url('admin/manageAccounts') ?>" class="d-flex mb-4">
    <input type="hidden" name="status" value="<?= esc($selectedStatus) ?>">
    <input type="text" name="search" value="<?= esc($search) ?>" class="form-control me-2" placeholder="Search by name...">
    <button class="btn btn-primary">Search</button>
</form>

<!-- No users found message -->
<?php if (empty($users)): ?>
    <div class="alert alert-warning text-center mt-4">
        No users found 
        <?php if (!empty($search)): ?>
            matching "<strong><?= esc($search) ?></strong>"
        <?php endif; ?>
        <?php if (!empty($selectedStatus) && strtolower($selectedStatus) !== 'all'): ?>
            with status "<strong><?= esc($selectedStatus) ?></strong>"
        <?php endif; ?>.
    </div>
<?php else: ?>

    <!-- ✅ User Cards with Bills -->
    <?php foreach ($users as $user): ?>
    <div class="card mb-3 shadow-sm">
        <div class="card-header bg-primary text-white">
            <strong><?= esc($user['last_name'] . ', ' . $user['first_name']) ?></strong>
            <span class="float-end">
                <?= $user['is_verified'] ? '<span class="badge bg-success">Verified</span>' : '<span class="badge bg-secondary">Unverified</span>' ?>
            </span>
        </div>
        <div class="card-body">
            <p><b>Email:</b> <?= esc($user['email']) ?></p>
            <p><b>Contact:</b> <?= esc($user['phone']) ?></p>
            <p><b>Address:</b> <?= esc($user['barangay']) ?>, Purok <?= esc($user['purok']) ?></p>

            <h6 class="mt-3">Unpaid Bills:</h6>
            <?php if (!empty($user['unpaid_bills'])): ?>
                <table class="table table-sm table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Amount</th>
                            <th>Due Date</th>
                            <th>Proof</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($user['unpaid_bills'] as $bill): ?>
                        <tr>
                            <td><?= esc($bill['id']) ?></td>
                            <td>₱<?= number_format($bill['amount_due'], 2) ?></td>
                            <td><?= esc($bill['due_date']) ?></td>
                            <td>
                                <?php if (!empty($bill['payment_proof'])): ?>
                                    <a href="<?= base_url($bill['payment_proof']) ?>" target="_blank">View Proof</a>
                                <?php else: ?>
                                    <span class="text-muted">None</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= base_url('admin/markAsPaid/' . $bill['id']) ?>" 
                                   class="btn btn-success btn-sm"
                                   onclick="return confirm('Mark this bill as paid?');">
                                    Mark as Paid
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-muted">No unpaid bills.</p>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
<?php endif; ?>

<?= $this->endSection() ?>
