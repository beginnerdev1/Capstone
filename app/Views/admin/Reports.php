<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<h1 class="mt-4">Service Reports</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item active">Customer Reported Issues</li>
</ol>

<div class="card">
    <div class="card-body">
        <table class="table table-bordered table-hover text-center align-middle">
            <thead class="table-danger">
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Issue Type</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($reports)): ?>
                    <?php foreach ($reports as $report): ?>
                        <tr>
                            <td><?= esc($report['id']) ?></td>
                            <td><?= esc($report['user'] ?? 'Unknown') ?></td>
                            <td><?= esc($report['issue_type']) ?></td>
                            <td><?= esc($report['description']) ?></td>
                            <td><?= esc($report['status']) ?></td>
                            <td><?= esc($report['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6">No reports found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
