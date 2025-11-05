<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>

<h1 class="mb-3 fw-bold">Dashboard</h1>
<p class="text-muted mb-4">Overview</p>

<!-- Top summary cards -->
<div class="row g-4 mb-4">
    <div class="col-12 col-sm-6 col-md-3">
        <div class="card text-white bg-primary shadow h-100 rounded-4">
            <div class="card-body d-flex flex-column justify-content-between">
                <h5 class="card-title mb-3">Registered Users</h5>
                <a href="<?= base_url('admin/registeredUsers') ?>" class="text-white fw-bold text-decoration-none">View Details →</a>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-md-3">
        <div class="card text-white bg-warning shadow h-100 rounded-4">
            <div class="card-body d-flex flex-column justify-content-between">
                <h5 class="card-title mb-3">Accounts</h5>
                <a href="<?= base_url('admin/manageAccounts') ?>" class="text-white fw-bold text-decoration-none">Manage Accounts →</a>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-md-3">
        <div class="card text-white bg-success shadow h-100 rounded-4">
            <div class="card-body d-flex flex-column justify-content-between">
                <h5 class="card-title mb-3">Paid Bills</h5>
                <a href="<?= base_url('admin/paidBills') ?>" class="text-white fw-bold text-decoration-none">View Details →</a>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-md-3">
        <div class="card text-white bg-danger shadow h-100 rounded-4">
            <div class="card-body d-flex flex-column justify-content-between">
                <h5 class="card-title mb-3">Announcements</h5>
                <a href="<?= base_url('admin/reports') ?>" class="text-white fw-bold text-decoration-none">Reports →</a>
            </div>
        </div>
    </div>
</div>

<!-- Income Chart Section -->
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
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script>
    // Bar chart data
    const months = <?= $months ?? '[]' ?>;
    const totals = <?= $incomeData ?? '[]' ?>;

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

    new Chart(userChart, {
    type: 'pie',
    data: {
        labels: ['Active', 'Pending', 'Inactive'],
        datasets: [{
            data: [<?= $active ?? 0 ?>, <?= $pending ?? 0 ?>, <?= $inactive ?? 0 ?>],
            backgroundColor: ['#36A2EB', '#FFCE56', '#FF6384'],
            borderColor: '#fff',
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    font: { size: 14 },
                    padding: 15
                }
            },
            datalabels: {
                color: '#060606ff',
                font: {
                    weight: 'bold',
                    size: 30
                },
                formatter: (value, ctx) => {
                    return value > 0 ? value : ''; // hide zero values
                }
            }
        }
    },
    plugins: [ChartDataLabels] // ✅ Correct placement
});
    
</script>


<?= $this->endSection() ?>
