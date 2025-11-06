<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>

<h1 class="mb-4">Manage Accounts</h1>

<!-- Flash messages -->
<?php if (session()->getFlashdata('success')): ?>
<div class="alert alert-success alert-dismissible fade show">
    <?= session()->getFlashdata('success') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php elseif (session()->getFlashdata('error')): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <?= session()->getFlashdata('error') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
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

<!-- Tabs -->
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

<!-- Search -->
<form method="get" action="<?= site_url('admin/manageAccounts') ?>" class="d-flex mb-4">
    <input type="hidden" name="status" value="<?= esc($selectedStatus) ?>">
    <input type="text" name="search" value="<?= esc($search) ?>" class="form-control me-2" placeholder="Search by name...">
    <button class="btn btn-primary">Search</button>
</form>

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
    <?php foreach ($users as $user): ?>
        <div class="card mb-3 shadow-sm">
            <div class="card-header bg-primary text-white">
                <strong><?= esc($user['last_name'] . ', ' . $user['first_name']) ?></strong>
                <span class="float-end">
                    <?= $user['is_verified'] ? '<span class="badge bg-success">Verified</span>' : '<span class="badge bg-secondary">Unverified</span>' ?>
                </span>
            </div>

            <div class="card-body table-responsive">
                <p><b>Email:</b> <?= esc($user['email']) ?></p>
                <p><b>Contact:</b> <?= esc($user['phone']) ?></p>
                <p><b>Address:</b> <?= esc($user['barangay']) ?>, Purok <?= esc($user['purok']) ?></p>

                <h6 class="mt-3">Bills:</h6>
                <?php if (!empty($user['unpaid_bills'])): ?>
                    <table class="table table-sm table-bordered table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Amount</th>
                                <th>Due Date</th>
                                <th>Paid Date</th>
                                <th>Status</th>
                                <th>Created At</th>
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
                                <td><?= esc($bill['paid_date'] ?? 'N/A') ?></td>
                                <td><?= esc(ucwords($bill['status'])) ?></td>
                                <td><?= date('Y-m-d', strtotime($bill['created_at'])) ?></td>
                                <td>
                                    <?php if (!empty($bill['payment_proof'])): ?>
                                        <a href="<?= base_url($bill['payment_proof']) ?>" target="_blank">View Proof</a>
                                    <?php else: ?>
                                        <span class="text-muted">None</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                <?php if (in_array($bill['status'], ['Rejected', 'Pending']) ): ?>
                                <div class="d-flex">
                                    <!-- Status Update Form -->
                                    <form action="<?= site_url('admin/update-status/' . $bill['id']) ?>" method="post" class="d-flex me-2">
                                        <?= csrf_field() ?>
                                    
                                        <select name="status" class="form-select form-select-sm me-2" required>
                                            <option value="" disabled selected>Choose...</option>
                                            <option value="Paid">Paid</option>
                                            <option value="Over the Counter">Over the Counter</option>
                                        </select>
                                        
                                        <button type="submit" class="btn btn-sm btn-primary">Update</button>
                                    </form>

                                    <!-- Edit Button -->
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editBillModal<?= $bill['id'] ?>">
                                        Edit
                                    </button>
                                </div>

                                <!-- Edit Bill Modal -->
                                <div class="modal fade" id="editBillModal<?= $bill['id'] ?>" tabindex="-1" aria-labelledby="editBillLabel<?= $bill['id'] ?>" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="<?= site_url('admin/editBill/' . $bill['id']) ?>" method="post">
                                                <?= csrf_field() ?>
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editBillLabel<?= $bill['id'] ?>">Edit Bill #<?= $bill['id'] ?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Amount</label>
                                                        <input type="number" step="0.01" class="form-control" name="amount" value="<?= esc($bill['amount_due']) ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Due Date</label>
                                                        <input type="date" class="form-control" name="due_date" value="<?= esc($bill['due_date']) ?>" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-success">Save Changes</button>
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-muted">No bills found.</p>
                <?php endif; ?>
                
                <?php if ($user['status'] !== 'pending'): ?>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addBillModal<?= $user['id'] ?>">
                        + Add Bill
                    </button>
                <?php else: ?>
                    <button class="btn btn-sm btn-secondary" disabled>Cannot add bill (Pending)</button>
                <?php endif; ?>

                <!-- Add Bill Modal -->
                <div class="modal fade" id="addBillModal<?= $user['id'] ?>" tabindex="-1" aria-labelledby="addBillLabel<?= $user['id'] ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="<?= base_url('admin/addBill/' . $user['id']) ?>" method="post">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addBillLabel<?= $user['id'] ?>">Add New Bill for <?= esc($user['first_name'] . ' ' . $user['last_name']) ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Amount</label>
                                        <input type="number" step="0.01" class="form-control" name="amount" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Due Date</label>
                                        <input type="date" class="form-control" name="due_date" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-success">Add Bill</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?= $this->endSection() ?>
