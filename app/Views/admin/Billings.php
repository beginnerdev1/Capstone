<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1 class="mt-4">Billing Management</h1>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-file-invoice"></i> Create Billing</span>
        </div>
        <div class="card-body">
            <form id="createBillForm" method="post" action="<?= site_url('admin/billings/create') ?>">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>User</label>
                        <select name="user_id" class="form-select" required>
                            <option value="">-- Select User --</option>
                            <?php foreach ($users as $u): ?>
                                <option value="<?= esc($u['id']) ?>">
                                    <?= esc($u['Firstname'] . ' ' . $u['Surname']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label>Billing Month</label>
                        <input type="month" name="billing_month" class="form-control" required>
                    </div>

                    <div class="col-md-3">
                        <label>Amount</label>
                        <input type="number" name="amount" step="0.01" class="form-control" required>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Create</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Status Filter -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-filter"></i> Filter by Status</span>
            <form method="get" class="d-flex">
                <select name="status" class="form-select me-2">
                    <?php foreach ($statuses as $status): ?>
                        <option value="<?= esc($status) ?>" <?= ($selectedStatus == $status) ? 'selected' : '' ?>>
                            <?= esc($status) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-outline-primary">Filter</button>
            </form>
        </div>

        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Month</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($bills)): ?>
                        <?php foreach ($bills as $i => $bill): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= esc($bill['user_name']) ?></td>
                                <td><?= esc($bill['email']) ?></td>
                                <td>₱<?= number_format($bill['amount'], 2) ?></td>
                                <td><?= esc($bill['status']) ?></td>
                                <td><?= esc($bill['billing_month']) ?></td>
                                <td><?= esc($bill['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center">No records found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
