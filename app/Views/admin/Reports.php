<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>

<div class="container mt-4">
    <h2 class="text-center fw-bold mb-4">Reports</h2>

    <!-- Top Summary Cards -->
    <div class="row g-4 mb-4 justify-content-center">
        <div class="col-md-3">
            <div class="card border-success shadow-sm text-center p-3">
                <h6 class="text-success fw-bold">Total Collected</h6>
                <h3 class="fw-bold">₱<?= number_format($totalCollected ?? 0, 2) ?></h3>
                <small>Paid + Over the Counter</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-danger shadow-sm text-center p-3">
                <h6 class="text-danger fw-bold">Total Unpaid Bills</h6>
                <h3 class="fw-bold"><?= $unpaidCount ?? 0 ?></h3>
                <small>Status: Pending</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-primary shadow-sm text-center p-3">
                <h6 class="text-primary fw-bold">Active Users</h6>
                <h3 class="fw-bold"><?= $activeUsers ?? 0 ?></h3>
                <small>Registered & Verified</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning shadow-sm text-center p-3">
                <h6 class="text-warning fw-bold">This Month’s Collection</h6>
                <h3 class="fw-bold">₱<?= number_format($monthlyTotal ?? 0, 2) ?></h3>
                <small><?= date('F Y') ?></small>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row g-4 mt-4">
        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-header fw-semibold">Total Paid Bills (Monthly)</div>
                <div class="card-body">
                    <canvas id="paidBillsChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-header fw-semibold">User Distribution</div>
                <div class="card-body">
                    <canvas id="userChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Bar chart data
    const months = <?= $months ?? '[]' ?>;
    const totals = <?= $totals ?? '[]' ?>;

    new Chart(document.getElementById('paidBillsChart'), {
        type: 'bar',
        data: {
            labels: months,
            datasets: [{
                label: '₱ Collected',
                data: totals,
                backgroundColor: 'rgba(75, 192, 192, 0.7)',
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: true } },
            scales: { y: { beginAtZero: true } }
        }
    });

    // Pie chart data
    const userChart = document.getElementById('userChart');
    new Chart(userChart, {
        type: 'pie',
        data: {
            labels: ['Active', 'Pending', 'Inactive'],
            datasets: [{
                data: [<?= $active ?? 0 ?>, <?= $pending ?? 0 ?>, <?= $inactive ?? 0 ?>],
                backgroundColor: ['#36A2EB', '#FFCE56', '#FF6384']
            }]
        },
        options: {
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>

<?= $this->endSection() ?>
