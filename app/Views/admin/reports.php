<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fixed-Rate Water Bill Reports</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;900&display=swap" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
  <style>
:root {
  --primary: #3b82f6;
  --primary-dark: #1d4ed8;
  --secondary: #0ea5e9;
  --success: #10b981;
  --warning: #f59e0b;
  --danger: #ef4444;
  --border: #e5e7eb;
  --dark: #1f2937;
  --light: #f9fafb;
  --muted: #6b7280;
}

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(135deg, #f0f4f8 0%, #d9e2ec 100%);
  min-height: 100vh;
}

.reports-wrapper {
  padding: 2rem;
  max-width: 1400px;
  margin: 0 auto;
}

.reports-header {
  background: linear-gradient(135deg, #3b82f6 0%, #0ea5e9 100%);
  color: white;
  padding: 3rem 2rem;
  border-radius: 20px;
  margin-bottom: 2rem;
  box-shadow: 0 15px 40px rgba(59, 130, 246, 0.25);
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 2rem;
}

.header-title {
  font-size: 2.2rem;
  font-weight: 900;
  letter-spacing: -0.5px;
  display: flex;
  align-items: center;
  gap: 1rem;
}

.header-icon {
  font-size: 2.8rem;
  animation: bounce 2s ease-in-out infinite;
}

@keyframes bounce {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-8px); }
}

.header-actions {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
}

.btn {
  padding: 0.85rem 1.5rem;
  border: none;
  border-radius: 10px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s ease;
  display: inline-flex;
  align-items: center;
  gap: 0.6rem;
  font-size: 0.9rem;
}

.btn-primary {
  background: rgba(255, 255, 255, 0.2);
  backdrop-filter: blur(10px);
  border: 2px solid rgba(255, 255, 255, 0.3);
  color: white;
}

.btn-primary:hover {
  background: rgba(255, 255, 255, 0.3);
  border-color: rgba(255, 255, 255, 0.5);
  transform: translateY(-2px);
}

.btn-success {
  background: var(--success);
  color: white;
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.btn-success:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
}

.filters-section {
  background: white;
  padding: 1.75rem;
  border-radius: 16px;
  margin-bottom: 2rem;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
  border: 1px solid var(--border);
}

.filters-title {
  font-weight: 700;
  color: var(--dark);
  margin-bottom: 1.25rem;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-size: 1.1rem;
}

.filters-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
}

.filter-group {
  display: flex;
  flex-direction: column;
}

.filter-label {
  font-size: 0.85rem;
  font-weight: 700;
  color: var(--dark);
  margin-bottom: 0.6rem;
}

.filter-input,
.filter-select {
  padding: 0.85rem;
  border: 2px solid var(--border);
  border-radius: 10px;
  font-size: 0.9rem;
  font-family: 'Poppins', sans-serif;
  background: var(--light);
  transition: all 0.3s ease;
}

.filter-input:focus,
.filter-select:focus {
  outline: none;
  border-color: var(--primary);
  box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
  background: white;
}

.filter-buttons {
  display: flex;
  gap: 0.75rem;
  align-items: flex-end;
}

.btn-filter {
  padding: 0.85rem 1.5rem;
  border: none;
  border-radius: 10px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 0.9rem;
  flex: 1;
}

.btn-apply {
  background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
  color: white;
  box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
}

.btn-apply:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
}

.btn-reset {
  background: white;
  color: var(--dark);
  border: 2px solid var(--border);
}

.btn-reset:hover {
  background: var(--light);
  border-color: var(--primary);
}

.reports-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.report-card {
  background: white;
  border-radius: 16px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
  border: 1px solid var(--border);
  overflow: hidden;
  transition: all 0.3s ease;
}

.report-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
}

.report-header {
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(14, 165, 233, 0.1) 100%);
  padding: 1.5rem;
  border-bottom: 2px solid var(--border);
  display: flex;
  align-items: center;
  gap: 1rem;
}

.report-icon {
  font-size: 2rem;
  width: 50px;
  height: 50px;
  background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
}

.report-info h3 {
  margin: 0;
  color: var(--dark);
  font-weight: 700;
  font-size: 1rem;
}

.report-info p {
  margin: 0.25rem 0 0;
  color: var(--muted);
  font-size: 0.85rem;
}

.report-body {
  padding: 1.5rem;
}

.report-stat {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem 0;
  border-bottom: 1px solid var(--light);
}

.report-stat:last-child {
  border-bottom: none;
}

.report-stat-label {
  color: var(--muted);
  font-size: 0.9rem;
  font-weight: 600;
}

.report-stat-value {
  color: var(--dark);
  font-size: 1.1rem;
  font-weight: 700;
}

.report-footer {
  padding: 1rem 1.5rem;
  background: var(--light);
  border-top: 1px solid var(--border);
  display: flex;
  gap: 0.75rem;
  justify-content: space-between;
}

.btn-small {
  padding: 0.6rem 1.2rem;
  font-size: 0.85rem;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  flex: 1;
  text-align: center;
}

.btn-view {
  background: rgba(59, 130, 246, 0.1);
  color: var(--primary);
  border: 1.5px solid rgba(59, 130, 246, 0.2);
}

.btn-view:hover {
  background: rgba(59, 130, 246, 0.2);
  border-color: var(--primary);
}

.btn-export {
  background: linear-gradient(135deg, var(--success) 0%, #0d9488 100%);
  color: white;
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
}

.btn-export:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 16px rgba(16, 185, 129, 0.3);
}

.chart-container {
  background: white;
  border-radius: 16px;
  padding: 2rem;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
  border: 1px solid var(--border);
  margin-bottom: 2rem;
}

.chart-title {
  font-size: 1.3rem;
  font-weight: 700;
  color: var(--dark);
  margin-bottom: 1.5rem;
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.chart-canvas {
  max-height: 350px;
}

.export-options {
  background: white;
  border-radius: 16px;
  padding: 2rem;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
  border: 1px solid var(--border);
}

.export-title {
  font-size: 1.3rem;
  font-weight: 700;
  color: var(--dark);
  margin-bottom: 1.5rem;
}

.export-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 1rem;
}

.export-btn {
  padding: 1.5rem;
  border: 2px solid var(--border);
  background: white;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.3s ease;
  text-align: center;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.75rem;
  font-weight: 700;
  color: var(--dark);
}

.export-btn:hover {
  border-color: var(--primary);
  background: rgba(59, 130, 246, 0.05);
  transform: translateY(-2px);
}

.export-icon {
  font-size: 1.75rem;
}

@media (max-width: 768px) {
  .reports-wrapper {
    padding: 1rem;
  }

  .reports-header {
    padding: 1.5rem;
  }

  .header-content {
    flex-direction: column;
    gap: 1rem;
  }

  .header-title {
    font-size: 1.5rem;
  }

  .header-actions {
    width: 100%;
  }

  .header-actions .btn {
    flex: 1;
  }

  .filters-grid {
    grid-template-columns: 1fr;
  }

  .filter-buttons {
    flex-direction: column;
  }

  .filter-buttons .btn-filter {
    width: 100%;
  }

  .reports-grid {
    grid-template-columns: 1fr;
  }

  .chart-container,
  .export-options {
    padding: 1.5rem;
  }

  .export-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 480px) {
  .reports-wrapper {
    padding: 0.75rem;
  }

  .reports-header {
    padding: 1.25rem;
  }

  .header-title {
    font-size: 1.25rem;
  }

  .header-icon {
    font-size: 2rem;
  }

  .filters-section {
    padding: 1.25rem;
  }

  .report-card {
    border-radius: 12px;
  }
}
  </style>
</head>
<body>
<div class="reports-wrapper">
  <!-- Header -->
  <div class="reports-header">
    <div class="header-content">
      <div class="header-title">
        <span class="header-icon">📊</span>
        <span>Fixed-Rate Water Bill Reports</span>
      </div>
      <div class="header-actions">
        <button class="btn btn-primary">
          <span>🔄</span>
          <span>Refresh</span>
        </button>
        <button class="btn btn-success">
          <span>📥</span>
          <span>Export All</span>
        </button>
      </div>
    </div>
  </div>

  <!-- Filters -->
  <div class="filters-section">
    <div class="filters-title">🔍 Filter Reports</div>
    <div class="filters-grid">
      <div class="filter-group">
        <label class="filter-label">Start Date</label>
        <input type="date" class="filter-input" id="startDate">
      </div>
      <div class="filter-group">
        <label class="filter-label">End Date</label>
        <input type="date" class="filter-input" id="endDate">
      </div>
      <div class="filter-group">
        <label class="filter-label">Report Type</label>
        <select class="filter-select">
          <option value="">All Reports</option>
          <option value="collection">Collection Status</option>
          <option value="payment">Payment History</option>
          <option value="household">Household Summary</option>
        </select>
      </div>
      <div class="filter-buttons">
        <button class="btn-filter btn-apply">Apply Filters</button>
        <button class="btn-filter btn-reset">Reset</button>
      </div>
    </div>
  </div>

  <!-- Report Cards -->
  <div class="reports-grid">
    <!-- Fixed Bill Summary Report -->
    <div class="report-card">
      <div class="report-header">
        <div class="report-icon">💰</div>
        <div class="report-info">
          <h3>Fixed Bill Summary</h3>
          <p>Year <?= date('Y') ?> Overview</p>
        </div>
      </div>
      <div class="report-body">
        <div class="report-stat">
          <span class="report-stat-label">Normal Rate (<?= $normalCount ?? 0 ?> households)</span>
          <span class="report-stat-value">₱<?= number_format($rateNormal ?? 60, 2) ?></span>
        </div>
        <div class="report-stat">
          <span class="report-stat-label">Senior Citizen Rate (<?= $seniorCount ?? 0 ?> households)</span>
          <span class="report-stat-value">₱<?= number_format($rateSenior ?? 48, 2) ?></span>
        </div>
        <div class="report-stat">
          <span class="report-stat-label">Living Alone Rate (<?= $aloneCount ?? 0 ?> households)</span>
          <span class="report-stat-value">₱<?= number_format($rateAlone ?? 30, 2) ?></span>
        </div>
        <div class="report-stat" style="border-top: 2px solid var(--primary); padding-top: 1rem; margin-top: 0.5rem;">
          <span class="report-stat-label" style="font-weight: 700;">Expected Monthly Collection</span>
          <span class="report-stat-value" style="color: var(--primary); font-size: 1.25rem;">₱<?= number_format($monthlyExpected ?? 0, 2) ?></span>
        </div>
      </div>
      <div class="report-footer">
        <button class="btn-small btn-view">📄 View</button>
        <button class="btn-small btn-export">📥 Export</button>
      </div>
    </div>

    <!-- Payment Collection Report -->
    <div class="report-card">
      <div class="report-header">
        <div class="report-icon">💳</div>
        <div class="report-info">
          <h3>Payment Collection</h3>
          <p>Collection Status</p>
        </div>
      </div>
      <div class="report-body">
        <div class="report-stat">
          <span class="report-stat-label">Collected This Month</span>
          <span class="report-stat-value">₱<?= number_format($currentMonthCollected ?? 0, 2) ?></span>
        </div>
        <div class="report-stat">
          <span class="report-stat-label">Paid Households</span>
          <span class="report-stat-value"><?= $paidHouseholds ?? 0 ?> of <?= $totalHouseholds ?? 0 ?></span>
        </div>
        <div class="report-stat">
          <span class="report-stat-label">Pending Collection</span>
          <span class="report-stat-value">₱<?= number_format($pendingAmount ?? 0, 2) ?></span>
        </div>
        <div class="report-stat">
          <span class="report-stat-label">Collection Rate</span>
          <span class="report-stat-value"><?= $collectionRate ?? 0 ?>%</span>
        </div>
      </div>
      <div class="report-footer">
        <button class="btn-small btn-view">📄 View</button>
        <button class="btn-small btn-export">📥 Export</button>
      </div>
    </div>

    <!-- Rate Breakdown Card -->
    <div class="report-card">
      <div class="report-header">
        <div class="report-icon">📊</div>
        <div class="report-info">
          <h3>Rate Distribution</h3>
          <p>Household Categories</p>
        </div>
      </div>
      <div class="report-body">
        <div class="report-stat">
          <span class="report-stat-label">Normal Households</span>
          <span class="report-stat-value"><?= $normalCount ?? 0 ?> <span style="font-size: 0.85rem; color: var(--muted);">(₱<?= number_format(($normalCount ?? 0) * ($rateNormal ?? 60), 2) ?>)</span></span>
        </div>
        <div class="report-stat">
          <span class="report-stat-label">Senior Citizens</span>
          <span class="report-stat-value"><?= $seniorCount ?? 0 ?> <span style="font-size: 0.85rem; color: var(--muted);">(₱<?= number_format(($seniorCount ?? 0) * ($rateSenior ?? 48), 2) ?>)</span></span>
        </div>
        <div class="report-stat">
          <span class="report-stat-label">Living Alone</span>
          <span class="report-stat-value"><?= $aloneCount ?? 0 ?> <span style="font-size: 0.85rem; color: var(--muted);">(₱<?= number_format(($aloneCount ?? 0) * ($rateAlone ?? 30), 2) ?>)</span></span>
        </div>
        <div class="report-stat" style="border-top: 2px solid var(--primary); padding-top: 1rem; margin-top: 0.5rem;">
          <span class="report-stat-label" style="font-weight: 700;">Total Households</span>
          <span class="report-stat-value" style="color: var(--primary);"><?= $totalHouseholds ?? 0 ?></span>
        </div>
        <div style="margin-top: 1rem; padding: 1rem; background: var(--light); border-radius: 8px;">
          <canvas id="rateDistributionChart" style="max-height: 150px;"></canvas>
        </div>
      </div>
      <div class="report-footer">
        <button class="btn-small btn-view">📄 View</button>
        <button class="btn-small btn-export">📥 Export</button>
      </div>
    </div>

    <!-- Community Statistics -->
    <div class="report-card">
      <div class="report-header">
        <div class="report-icon">🏘️</div>
        <div class="report-info">
          <h3>Community Statistics</h3>
          <p>Household Overview</p>
        </div>
      </div>
      <div class="report-body" style="display: flex; flex-direction: column; gap: 0.5rem;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
          <div class="report-stat" style="flex-direction: column; align-items: flex-start; padding: 0.5rem; border: none;">
            <span class="report-stat-label">Active Households</span>
            <span class="report-stat-value"><?= $totalHouseholds ?? 0 ?></span>
          </div>
          <div class="report-stat" style="flex-direction: column; align-items: flex-start; padding: 0.5rem; border: none;">
            <span class="report-stat-label">Pending Payments</span>
            <span class="report-stat-value"><?= $pendingCount ?? 0 ?></span>
          </div>
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
          <div class="report-stat" style="flex-direction: column; align-items: flex-start; padding: 0.5rem; border: none;">
            <span class="report-stat-label">Payment Compliance</span>
            <span class="report-stat-value"><?= $collectionRate ?? 0 ?>%</span>
          </div>
          <div class="report-stat" style="flex-direction: column; align-items: flex-start; padding: 0.5rem; border: none;">
            <span class="report-stat-label">Late Payments</span>
            <span class="report-stat-value"><?= $latePayments ?? 0 ?></span>
          </div>
        </div>
        <div style="padding-top: 1rem;">
          <canvas id="miniPaymentChart" style="max-height: 180px;"></canvas>
        </div>
      </div>
      <div class="report-footer">
        <button class="btn-small btn-view">📄 View</button>
        <button class="btn-small btn-export">📥 Export</button>
      </div>
    </div>
  </div>

  <!-- Monthly Collection Chart -->
  <div class="chart-container">
    <div class="chart-title">
      <span>💰</span>
      <span>Monthly Collection Rate (2024)</span>
    </div>
    <canvas id="collectionChart" class="chart-canvas"></canvas>
  </div>

  <!-- Payment Status Chart -->
  <div class="chart-container">
    <div class="chart-title">
      <span>📊</span>
      <span>Payment Status Overview</span>
    </div>
    <canvas id="paymentStatusChart" class="chart-canvas"></canvas>
  </div>

  <!-- Export Options -->
  <div class="export-options">
    <div class="export-title">📥 Export All Data</div>
    <div class="export-grid">
      <button class="export-btn">
        <span class="export-icon">📄</span>
        <span>PDF</span>
      </button>
      <button class="export-btn">
        <span class="export-icon">📊</span>
        <span>Excel</span>
      </button>
      <button class="export-btn">
        <span class="export-icon">📋</span>
        <span>CSV</span>
      </button>
      <button class="export-btn">
        <span class="export-icon">🖨️</span>
        <span>Print</span>
      </button>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Set default dates (current year)
const today = new Date();
const startOfYear = new Date(today.getFullYear(), 0, 1);

document.getElementById('startDate').valueAsDate = startOfYear;
document.getElementById('endDate').valueAsDate = today;

// Monthly Collection Rate Chart
const collectionCtx = document.getElementById('collectionChart').getContext('2d');
const collectionChart = new Chart(collectionCtx, {
  type: 'line',
  data: {
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    datasets: [{
      label: 'Collection Rate (%)',
      data: <?= $collectionRates ?? '[0,0,0,0,0,0,0,0,0,0,0,0]' ?>,
      borderColor: '#3b82f6',
      backgroundColor: 'rgba(59, 130, 246, 0.1)',
      borderWidth: 3,
      fill: true,
      tension: 0.4,
      pointBackgroundColor: '#3b82f6',
      pointBorderColor: '#fff',
      pointBorderWidth: 2,
      pointRadius: 5,
      pointHoverRadius: 7
    }, {
      label: 'Amount Collected (₱)',
      data: <?= $collectionAmounts ?? '[0,0,0,0,0,0,0,0,0,0,0,0]' ?>,
      borderColor: '#10b981',
      backgroundColor: 'rgba(16, 185, 129, 0.1)',
      borderWidth: 3,
      fill: true,
      tension: 0.4,
      pointBackgroundColor: '#10b981',
      pointBorderColor: '#fff',
      pointBorderWidth: 2,
      pointRadius: 5,
      pointHoverRadius: 7,
      yAxisID: 'y1'
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: true,
    interaction: {
      mode: 'index',
      intersect: false,
    },
    plugins: {
      legend: {
        display: true,
        position: 'top',
        labels: {
          usePointStyle: true,
          padding: 15,
          font: {
            size: 12,
            weight: 600,
            family: 'Poppins'
          }
        }
      },
      tooltip: {
        backgroundColor: 'rgba(0, 0, 0, 0.8)',
        padding: 12,
        cornerRadius: 8,
        titleFont: {
          size: 14,
          weight: 700,
          family: 'Poppins'
        },
        bodyFont: {
          size: 13,
          family: 'Poppins'
        }
      }
    },
    scales: {
      y: {
        type: 'linear',
        display: true,
        position: 'left',
        title: {
          display: true,
          text: 'Collection Rate (%)',
          font: {
            size: 12,
            weight: 600,
            family: 'Poppins'
          }
        },
        min: 85,
        max: 105,
        grid: {
          color: 'rgba(0, 0, 0, 0.05)'
        }
      },
      y1: {
        type: 'linear',
        display: true,
        position: 'right',
        title: {
          display: true,
          text: 'Amount (₱)',
          font: {
            size: 12,
            weight: 600,
            family: 'Poppins'
          }
        },
        min: 6000,
        max: 7000,
        grid: {
          drawOnChartArea: false,
        }
      },
      x: {
        grid: {
          display: false
        },
        ticks: {
          font: {
            size: 11,
            weight: 600,
            family: 'Poppins'
          }
        }
      }
    }
  }
});

// Payment Status Chart (Doughnut)
const statusCtx = document.getElementById('paymentStatusChart').getContext('2d');
const paymentStatusChart = new Chart(statusCtx, {
  type: 'doughnut',
  data: {
    labels: ['Paid Households', 'Pending Payment', 'Late Payment'],
    datasets: [{
      data: [<?= $paidHouseholds ?? 0 ?>, <?= $pendingCount ?? 0 ?>, <?= $latePayments ?? 0 ?>],
      backgroundColor: [
        '#10b981',
        '#f59e0b',
        '#ef4444'
      ],
      borderWidth: 0,
      hoverOffset: 15
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: true,
    plugins: {
      legend: {
        display: true,
        position: 'bottom',
        labels: {
          padding: 20,
          usePointStyle: true,
          font: {
            size: 13,
            weight: 600,
            family: 'Poppins'
          }
        }
      },
      tooltip: {
        backgroundColor: 'rgba(0, 0, 0, 0.8)',
        padding: 12,
        cornerRadius: 8,
        titleFont: {
          size: 14,
          weight: 700,
          family: 'Poppins'
        },
        bodyFont: {
          size: 13,
          family: 'Poppins'
        },
        callbacks: {
          label: function(context) {
            const label = context.label || '';
            const value = context.parsed || 0;
            const total = context.dataset.data.reduce((a, b) => a + b, 0);
            const percentage = ((value / total) * 100).toFixed(1);
            return `${label}: ${value} (${percentage}%)`;
          }
        }
      }
    }
  }
});

// Rate Distribution Chart (Pie Chart)
const rateDistCtx = document.getElementById('rateDistributionChart');
if (rateDistCtx) {
  new Chart(rateDistCtx.getContext('2d'), {
    type: 'pie',
    data: {
      labels: ['Normal (₱<?= $rateNormal ?? 60 ?>)', 'Senior (₱<?= $rateSenior ?? 48 ?>)', 'Alone (₱<?= $rateAlone ?? 30 ?>)'],
      datasets: [{
        data: [<?= $normalCount ?? 0 ?>, <?= $seniorCount ?? 0 ?>, <?= $aloneCount ?? 0 ?>],
        backgroundColor: ['#3b82f6', '#10b981', '#f59e0b'],
        borderWidth: 0,
        hoverOffset: 8
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      plugins: {
        legend: {
          display: true,
          position: 'bottom',
          labels: {
            padding: 10,
            usePointStyle: true,
            font: { size: 10, weight: 600, family: 'Poppins' }
          }
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              const label = context.label || '';
              const value = context.parsed || 0;
              const total = context.dataset.data.reduce((a, b) => a + b, 0);
              const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
              return `${label}: ${value} households (${percentage}%)`;
            }
          }
        }
      }
    }
  });
}

// Export functionality
document.querySelectorAll('.export-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    const format = this.textContent.trim();
    alert(`Exporting reports as ${format}...`);
  });
});

// View report
document.querySelectorAll('.btn-view').forEach(btn => {
  btn.addEventListener('click', function() {
    alert('Viewing detailed report...');
  });
});

// Individual card exports
document.querySelectorAll('.btn-export').forEach(btn => {
  btn.addEventListener('click', function() {
    alert('Exporting this report...');
  });
});
</script>
</body>
</html>