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
<div class="card shadow rounded-4 border-0 mb-5">
    <div class="card-header bg-light fw-semibold fs-5">
        Income per Month (₱)
    </div>
    <div class="card-body">
        <canvas id="incomeChart" height="120"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('incomeChart').getContext('2d');
const incomeChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: <?= json_encode($months ?? ['Jan', 'Feb', 'Mar', 'Apr']); ?>,
    datasets: [{
      label: 'Total Income (₱)',
      data: <?= json_encode($incomeData ?? [1200, 900, 1500, 2000]); ?>,
      backgroundColor: 'rgba(54, 162, 235, 0.6)',
      borderColor: 'rgba(54, 162, 235, 1)',
      borderWidth: 1,
      borderRadius: 8,
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    scales: {
      y: {
        beginAtZero: true,
        grid: { color: 'rgba(0,0,0,0.05)' },
        ticks: { font: { size: 13 } }
      },
      x: {
        ticks: { font: { size: 13 } }
      }
    },
    plugins: {
      legend: {
        labels: { font: { size: 14 } }
      }
    }
  }
});
</script>

<?= $this->endSection() ?>
