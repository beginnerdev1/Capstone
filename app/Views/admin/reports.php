<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fixed-Rate Water Bill Reports</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;900&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        <a class="btn btn-primary reports-refresh" href="<?= site_url('admin/reports') ?>">
          <span>🔄</span>
          <span>Refresh</span>
        </a>
        <a class="btn btn-success export-btn" data-format="csv" data-export-url="<?= site_url('admin/exportReports') ?>" href="<?= site_url('admin/exportReports') ?>?format=csv&filename=reports_<?= date('Y-m-d') ?>" target="_blank" rel="noopener">
          <span>📥</span>
          <span>Export All</span>
        </a>
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
        <a class="btn-small btn-export" href="<?= site_url('admin/exportReports') ?>?format=csv&filename=reports_<?= date('Y-m-d') ?>" target="_blank" rel="noopener">📥 Export</a>
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
        <a class="btn-small btn-export" href="<?= site_url('admin/exportReports') ?>?format=csv&filename=reports_<?= date('Y-m-d') ?>" target="_blank" rel="noopener">📥 Export</a>
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
        <a class="btn-small btn-export" href="<?= site_url('admin/exportReports') ?>?format=csv&filename=reports_<?= date('Y-m-d') ?>" target="_blank" rel="noopener">📥 Export</a>
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
      <span>Monthly Collection Rate (<?= date('Y') ?>)</span>
    </div>
    <canvas id="collectionChart" class="chart-canvas"></canvas>
  </div>

  <!-- Payment Status Chart -->
  <div class="chart-container">
    <div class="chart-title">
      <span>📊</span>
      <span>Payment Status Overview<?= isset($statusScope) && $statusScope === 'YTD' ? ' (YTD)' : '' ?></span>
    </div>
    <canvas id="paymentStatusChart" class="chart-canvas"></canvas>
  </div>
</div>

<!-- Export Confirmation Modal (lightweight) -->
<div id="exportConfirmModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:9999; align-items:center; justify-content:center;">
  <div style="background:#fff; width:95%; max-width:520px; border-radius:14px; box-shadow:0 20px 60px rgba(0,0,0,.25); overflow:hidden; font-family:Poppins, sans-serif;">
    <div style="padding:16px 20px; background:linear-gradient(135deg,#3b82f6,#0ea5e9); color:#fff; font-weight:700;">Confirm Export</div>
    <div style="padding:16px 20px; color:#1f2937;">
      <div style="margin-bottom:12px;">Do you want to export the report?</div>
      <div style="display:flex; gap:10px; align-items:center; margin-top:8px;">
        <label for="exportFileName" style="font-weight:600; min-width:110px;">File name</label>
        <input id="exportFileName" type="text" style="flex:1; padding:10px 12px; border:2px solid #e5e7eb; border-radius:10px;" placeholder="reports_<?= date('Y-m-d') ?>">
      </div>
      <div style="display:flex; gap:10px; align-items:center; margin-top:12px;">
        <label style="font-weight:600; min-width:110px;">Format</label>
        <div id="exportFormatDisplay" style="padding:6px 10px; background:#f3f4f6; border-radius:8px; font-weight:600;">CSV</div>
      </div>
    </div>
    <div style="padding:12px 20px; background:#f9fafb; display:flex; gap:10px; justify-content:flex-end;">
      <button id="exportCancelBtn" style="padding:.7rem 1.2rem; border:2px solid #e5e7eb; background:#fff; border-radius:10px; font-weight:700;">Cancel</button>
      <button id="exportConfirmBtn" style="padding:.7rem 1.2rem; background:#10b981; color:#fff; border:none; border-radius:10px; font-weight:700;">Export</button>
    </div>
  </div>
  
</div>


<!-- Embed reports data as JSON so AJAX injection still has data available -->
<script type="application/json" id="reports-data">
<?= json_encode([
  'collectionRates' => $collectionRates ?? array_fill(0, 12, 0),
  'collectionAmounts' => $collectionAmounts ?? array_fill(0, 12, 0),
  'paidHouseholds' => (int)($paidHouseholds ?? 0),
  'pendingCount' => (int)($pendingCount ?? 0),
  'latePayments' => (int)($latePayments ?? 0),
  'normalCount' => (int)($normalCount ?? 0),
  'seniorCount' => (int)($seniorCount ?? 0),
  'aloneCount' => (int)($aloneCount ?? 0),
  'rateNormal' => (float)($rateNormal ?? 60),
  'rateSenior' => (float)($rateSenior ?? 48),
  'rateAlone' => (float)($rateAlone ?? 30),
  'year' => (int)date('Y')
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
</script>

<script>

// --- Debug instrumentation ---
(function(){
  try {
    console.info('[Reports] Script loaded v1.3');
    const originalAlert = window.alert;
    window.alert = function(msg){
      try {
        console.warn('[Reports][DEBUG] window.alert called with:', msg);
        console.debug('[Reports][DEBUG] alert stack:', new Error('stack').stack);
      } catch(ex) {}
      return originalAlert.call(window, msg);
    };
  } catch(err) {
    console.error('[Reports] Debug init error:', err);
  }
})();

function showNoData(canvasId, message) {
  const canvas = document.getElementById(canvasId);
  if (!canvas) return true;
  const totalWrapper = canvas.parentElement || canvas;
  const placeholder = document.createElement('div');
  placeholder.textContent = message || 'No data to display';
  placeholder.style.cssText = 'display:flex;align-items:center;justify-content:center;height:160px;color:#6b7280;background:#f9fafb;border:1px dashed #e5e7eb;border-radius:8px;font-family:Poppins, sans-serif;font-weight:600;';
  canvas.style.display = 'none';
  totalWrapper.appendChild(placeholder);
  return true;
}

// Set default dates (current year)
const today = new Date();
const startOfYear = new Date(today.getFullYear(), 0, 1);

document.getElementById('startDate').valueAsDate = startOfYear;
document.getElementById('endDate').valueAsDate = today;

// Monthly Collection Rate Chart
const collectionCanvas = document.getElementById('collectionChart');
const _cr = <?= json_encode($collectionRates ?? array_fill(0, 12, 0)) ?>;
const _ca = <?= json_encode($collectionAmounts ?? array_fill(0, 12, 0)) ?>;
const sumArray = arr => (arr || []).reduce((a,b)=>a+(Number(b)||0),0);
if (collectionCanvas && sumArray(_cr) === 0 && sumArray(_ca) === 0) {
  showNoData('collectionChart', 'No monthly collections yet');
} else if (collectionCanvas) {
const collectionCtx = collectionCanvas.getContext('2d');
const collectionChart = new Chart(collectionCtx, {
  type: 'line',
  data: {
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    datasets: [{
      label: 'Collection Rate (%)',
      data: _cr,
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
      data: _ca,
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
        beginAtZero: true,
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
        beginAtZero: true,
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
}

// Payment Status Chart (Doughnut)
const paymentCanvas = document.getElementById('paymentStatusChart');
const _paid = <?= (int)($paidHouseholds ?? 0) ?>;
const _pending = <?= (int)($pendingCount ?? 0) ?>;
const _late = <?= (int)($latePayments ?? 0) ?>;
if (paymentCanvas && (_paid + _pending + _late) === 0) {
  showNoData('paymentStatusChart', 'No payment status data yet');
} else if (paymentCanvas) {
const statusCtx = paymentCanvas.getContext('2d');
const paymentStatusChart = new Chart(statusCtx, {
  type: 'doughnut',
  data: {
    labels: ['Paid Households', 'Pending Payment', 'Late Payment'],
    datasets: [{
      data: [_paid, _pending, _late],
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
            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : '0.0';
            return `${label}: ${value} (${percentage}%)`;
          }
        }
      }
    }
  }
});
}

// Rate Distribution Chart (Pie Chart)
const rateDistCtx = document.getElementById('rateDistributionChart');
if (rateDistCtx) {
  const _n = <?= (int)($normalCount ?? 0) ?>;
  const _s = <?= (int)($seniorCount ?? 0) ?>;
  const _a = <?= (int)($aloneCount ?? 0) ?>;
  if ((_n + _s + _a) === 0) {
    showNoData('rateDistributionChart', 'No household distribution data');
  } else {
  new Chart(rateDistCtx.getContext('2d'), {
    type: 'pie',
    data: {
      labels: ['Normal (₱<?= $rateNormal ?? 60 ?>)', 'Senior (₱<?= $rateSenior ?? 48 ?>)', 'Alone (₱<?= $rateAlone ?? 30 ?>)'],
      datasets: [{
        data: [_n, _s, _a],
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
}

// Mini Payment Chart (Community Statistics Card)
const miniPaymentCtx = document.getElementById('miniPaymentChart');
if (miniPaymentCtx) {
  if ((_paid + _pending + _late) === 0) {
    showNoData('miniPaymentChart', 'No payment data yet');
  } else {
  new Chart(miniPaymentCtx.getContext('2d'), {
    type: 'bar',
    data: {
      labels: ['Paid', 'Pending', 'Late'],
      datasets: [{
        label: 'Households',
        data: [_paid, _pending, _late],
        backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
        borderWidth: 0,
        borderRadius: 6
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      plugins: {
        legend: {
          display: false
        },
        tooltip: {
          backgroundColor: 'rgba(0, 0, 0, 0.8)',
          padding: 10,
          cornerRadius: 6,
          titleFont: { size: 12, weight: 700, family: 'Poppins' },
          bodyFont: { size: 11, family: 'Poppins' }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            font: { size: 10, family: 'Poppins' }
          },
          grid: {
            color: 'rgba(0, 0, 0, 0.05)'
          }
        },
        x: {
          grid: {
            display: false
          },
          ticks: {
            font: { size: 10, weight: 600, family: 'Poppins' }
          }
        }
      }
    }
  });
  }
}

// Export functionality
// ===== Export handling with confirmation and filename =====
(function(){
  const exportModal = document.getElementById('exportConfirmModal');
  const fileInput = document.getElementById('exportFileName');
  const formatDisplay = document.getElementById('exportFormatDisplay');
  const cancelBtn = document.getElementById('exportCancelBtn');
  const confirmBtn = document.getElementById('exportConfirmBtn');
  let pendingFormat = 'csv';
  let pendingUrl = null; // server endpoint to GET

  function openExportModal(fmt){
    console.log('[Reports] openExportModal called with format:', fmt, 'url:', pendingUrl);
    pendingFormat = (fmt || 'csv').toLowerCase();
    formatDisplay.textContent = pendingFormat.toUpperCase();
    if(!fileInput.value){ fileInput.value = `reports_${new Date().toISOString().slice(0,10)}`; }
    if (!exportModal) {
      console.error('[Reports] exportModal element not found!');
      return;
    }
    exportModal.style.display = 'flex';
    console.log('[Reports] export modal opened');
  }
  function closeExportModal(){ exportModal.style.display='none'; }

  // Intercept export clicks only when inside this Reports view
  document.addEventListener('click', function(ev){
    const target = ev.target && ev.target.closest ? ev.target.closest('.export-btn, .btn-export') : null;
    if (!target) return;
    const wrapper = document.querySelector('#mainContent .reports-wrapper') || document.querySelector('.reports-wrapper');
    if (!wrapper || !wrapper.contains(target)) {
      return; // allow other pages (e.g., Transactions) to handle their own buttons
    }
    ev.preventDefault();
    if (ev.stopImmediatePropagation) ev.stopImmediatePropagation();
    ev.stopPropagation();
    const isGridBtn = target.classList.contains('export-btn');
    const fmt = isGridBtn ? (target.getAttribute('data-format') || 'csv') : 'csv';
    console.log('[Reports] Export click intercepted. Target:', target, 'format:', fmt);
    pendingUrl = target.getAttribute('data-export-url') || '<?= site_url('admin/exportReports') ?>';
    openExportModal(fmt);
  }, true);

  cancelBtn.addEventListener('click', closeExportModal);

  function buildCsv(){
    console.log('[Reports] buildCsv invoked');
    try {
      const dataEl = document.getElementById('reports-data');
      const data = dataEl ? JSON.parse(dataEl.textContent || '{}') : {};
      console.log('[Reports] CSV data snapshot:', data);
      const lines = [];
      // Section: Monthly Collection
      lines.push('Section,Monthly Collection');
      lines.push('Month,Collection Rate (%),Amount (PHP)');
      const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
      const rates = data.collectionRates || [];
      const amts = data.collectionAmounts || [];
      for(let i=0;i<12;i++){
        lines.push(`${months[i]},${rates[i]??0},${amts[i]??0}`);
      }
      lines.push('');
      // Section: Payment Status
      lines.push('Section,Payment Status');
      lines.push('Paid Households,Pending,Late');
      lines.push(`${data.paidHouseholds||0},${data.pendingCount||0},${data.latePayments||0}`);
      lines.push('');
      // Section: Rate Distribution
      lines.push('Section,Rate Distribution');
      lines.push('Normal,Senior,Alone');
      lines.push(`${data.normalCount||0},${data.seniorCount||0},${data.aloneCount||0}`);
      lines.push('');
      // Section: Rates
      lines.push('Section,Rates (PHP)');
      lines.push('Normal,Senior,Alone');
      lines.push(`${data.rateNormal||60},${data.rateSenior||48},${data.rateAlone||30}`);
      return lines.join('\r\n');
    } catch(e){
      alert('Failed to prepare CSV: ' + e.message);
      return '';
    }
  }

  function triggerDownload(content, filename, mime){
    console.log('[Reports] triggerDownload:', { filename, mime });
    const blob = new Blob([content], {type: mime||'text/plain;charset=utf-8;'});
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    setTimeout(()=>{ URL.revokeObjectURL(url); a.remove(); }, 0);
  }

  confirmBtn.addEventListener('click', function(){
    const baseName = (fileInput.value || `reports_${new Date().toISOString().slice(0,10)}`).trim();
    if(!baseName){ alert('Please enter a file name.'); return; }
    const fmt = pendingFormat;
    console.log('[Reports] Confirm export clicked:', { baseName, fmt });

    if (fmt === 'print' || fmt === 'pdf') {
      // Let users choose PDF via browser print dialog
      closeExportModal();
      console.log('[Reports] Opening print dialog');
      window.print();
      return;
    }

    if (fmt === 'excel' || fmt === 'csv') {
      // Prefer server-side endpoint; fallback to client CSV
      if (pendingUrl) {
        const href = `${pendingUrl}?format=${encodeURIComponent(fmt)}&filename=${encodeURIComponent(baseName)}`;
        console.log('[Reports] Navigating to server export:', href);
        closeExportModal();
        window.location.href = href;
      } else {
        const csv = buildCsv();
        if (!csv) { return; }
        const name = baseName + '.csv';
        triggerDownload(csv, name, 'text/csv;charset=utf-8;');
        closeExportModal();
      }
      return;
    }

    // Fallback
    closeExportModal();
    alert('Unsupported export format.');
  });
})();

// View report
document.querySelectorAll('.btn-view').forEach(btn => {
  btn.addEventListener('click', function() {
    console.log('[Reports] View clicked');
    alert('Viewing detailed report...');
  });
});

// Individual card exports
// per-card export now handled above with confirmation modal
</script>
</body>
</html>