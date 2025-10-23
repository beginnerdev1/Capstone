<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<h1 class="mt-4">Paid Bills</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item active">Billing History</li>
</ol>

<div class="card">
    <div class="card-body">
        <table class="table table-bordered table-hover text-center align-middle">
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
                            <td>₱<?= esc($bill['amount_due']) ?></td>
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
<?= $this->endSection() ?>
