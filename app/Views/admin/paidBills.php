<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1 class="mt-4">Paid Bills</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Billing History</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Paid Bills List
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover text-center align-middle text-nowrap">
                <thead class="table-success">
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Amount</th>
                        <th>Date Paid</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($bills)): ?>
                        <?php foreach ($bills as $bill): ?>
                            <tr>
                                <td><?= esc($bill['id']) ?></td>
                                <td><?= esc($bill['user_name'] ?? 'Unknown') ?></td>
                                <td>₱<?= number_format($bill['amount_due'] ?? 0, 2) ?></td> 
                                <td><?= esc($bill['updated_at'] ?? '—') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4">No paid bills found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>