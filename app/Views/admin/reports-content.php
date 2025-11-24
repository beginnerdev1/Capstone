<style>
:root {
  --primary: #4e73df;
  --primary-dark: #224abe;
  --secondary: #858796;
  --success: #1cc88a;
  --warning: #f6c23e;
  --danger: #e74a3b;
  --info: #36b9cc;
  --border: #e3e6f0;
  --dark: #5a5c69;
  --light: #f8f9fc;
  --muted: #858796;
  --white: #ffffff;
  --shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}

.main-content {
  margin-left: 0;
  padding: 1.5rem;
  background-color: var(--light);
  min-height: calc(100vh - 80px);
}

.page-header {
  background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
  color: white;
  padding: 2rem;
  border-radius: 0.5rem;
  margin-bottom: 1.5rem;
  box-shadow: var(--shadow);
}

.page-header h1 {
  font-size: 1.75rem;
  font-weight: 700;
  margin: 0;
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.page-header .icon {
  font-size: 2rem;
  animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.05); }
}

.header-actions {
  margin-top: 1rem;
  display: flex;
  gap: 0.75rem;
  flex-wrap: wrap;
}

.btn {
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 0.375rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
  text-decoration: none;
}

.btn-outline-light {
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.3);
  color: white;
}

.btn-outline-light:hover {
  background: rgba(255, 255, 255, 0.2);
  border-color: rgba(255, 255, 255, 0.5);
  color: white;
  transform: translateY(-1px);
}

.btn-success {
  background: var(--success);
  color: white;
  border: 1px solid var(--success);
}

.btn-success:hover {
  background: #17a673;
  border-color: #17a673;
  transform: translateY(-1px);
}

.card {
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: 0.5rem;
  box-shadow: var(--shadow);
  margin-bottom: 1.5rem;
}

.card-header {
  background: var(--white);
  border-bottom: 1px solid var(--border);
  padding: 1rem 1.25rem;
  font-weight: 700;
  color: var(--primary);
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.card-body {
  padding: 1.25rem;
}

.filters-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
  margin-bottom: 1rem;
}

.form-group {
  margin-bottom: 1rem;
}

.form-label {
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--dark);
  margin-bottom: 0.5rem;
  display: block;
}

.form-control {
  padding: 0.5rem 0.75rem;
  border: 1px solid var(--border);
  border-radius: 0.375rem;
  font-size: 0.875rem;
  background: var(--white);
  transition: all 0.3s ease;
  width: 100%;
}

.form-control:focus {
  outline: none;
  border-color: var(--primary);
  box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}

.filter-buttons {
  display: flex;
  gap: 0.5rem;
  align-items: flex-end;
}

.btn-primary {
  background: var(--primary);
  border: 1px solid var(--primary);
  color: white;
}

.btn-primary:hover {
  background: var(--primary-dark);
  border-color: var(--primary-dark);
}

.btn-secondary {
  background: var(--secondary);
  border: 1px solid var(--secondary);
  color: white;
}

.btn-secondary:hover {
  background: #6c757d;
  border-color: #6c757d;
}

.reports-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.report-card {
  background: var(--white);
  border-radius: 0.5rem;
  box-shadow: var(--shadow);
  border: 1px solid var(--border);
  overflow: hidden;
  transition: all 0.3s ease;
}

.report-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.2);
}

.report-card-header {
  background: linear-gradient(135deg, rgba(78, 115, 223, 0.1) 0%, rgba(34, 74, 190, 0.1) 100%);
  padding: 1.25rem;
  border-bottom: 1px solid var(--border);
  display: flex;
  align-items: center;
  gap: 1rem;
}

.report-icon {
  font-size: 1.75rem;
  width: 3.5rem;
  height: 3.5rem;
  background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
  border-radius: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
}

.report-title {
  font-size: 1rem;
  font-weight: 700;
  color: var(--dark);
  margin: 0;
}

.report-subtitle {
  font-size: 0.875rem;
  color: var(--muted);
  margin: 0.25rem 0 0;
}

.report-body {
  padding: 1.25rem;
}

.report-stat {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem 0;
  border-bottom: 1px solid #f8f9fc;
}

.report-stat:last-child {
  border-bottom: none;
}

.stat-label {
  color: var(--muted);
  font-size: 0.875rem;
  font-weight: 500;
}

.stat-value {
  color: var(--dark);
  font-size: 1rem;
  font-weight: 700;
}

.stat-highlight {
  border-top: 2px solid var(--primary);
  padding-top: 1rem;
  margin-top: 0.5rem;
}

.stat-highlight .stat-label {
  font-weight: 700;
}

.stat-highlight .stat-value {
  color: var(--primary);
  font-size: 1.125rem;
}

.report-footer {
  padding: 1rem 1.25rem;
  background: var(--light);
  border-top: 1px solid var(--border);
  display: flex;
  gap: 0.5rem;
}

.btn-sm {
  padding: 0.375rem 0.75rem;
  font-size: 0.875rem;
  border-radius: 0.375rem;
  flex: 1;
  text-align: center;
}

.btn-outline-primary {
  background: transparent;
  border: 1px solid var(--primary);
  color: var(--primary);
}

.btn-outline-primary:hover {
  background: var(--primary);
  color: white;
}

.chart-container {
  background: var(--white);
  border-radius: 0.5rem;
  box-shadow: var(--shadow);
  border: 1px solid var(--border);
  margin-bottom: 1.5rem;
}

.chart-header {
  padding: 1.25rem;
  border-bottom: 1px solid var(--border);
  background: var(--white);
  border-radius: 0.5rem 0.5rem 0 0;
}

.chart-title {
  font-size: 1.125rem;
  font-weight: 700;
  color: var(--dark);
  margin: 0;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.chart-body {
  padding: 1.25rem;
}

.chart-canvas {
  max-height: 300px;
}

.mini-chart {
  max-height: 120px;
}

/* Responsive Design */
@media (max-width: 768px) {
  .main-content {
    padding: 1rem;
  }
  
  .page-header {
    padding: 1.5rem;
  }
  
  .page-header h1 {
    font-size: 1.5rem;
  }
  
  .filters-grid {
    grid-template-columns: 1fr;
  }
  
  .filter-buttons {
    flex-direction: column;
  }
  
  .reports-grid {
    grid-template-columns: 1fr;
  }
  
  .header-actions {
    flex-direction: column;
  }
  
  .report-footer {
    flex-direction: column;
  }
}

@media (max-width: 480px) {
  .main-content {
    padding: 0.75rem;
  }
  
  .page-header {
    padding: 1rem;
  }
  
  .card-body {
    padding: 1rem;
  }
}

/* Modal contrast and responsive tweaks */
#partialBillingsModal .modal-content,
#reportPreviewModal .modal-content {
  background: #ffffff;
  color: #111827;
  border-radius: 0.5rem;
  box-shadow: 0 0.5rem 1.5rem rgba(15,23,42,0.18);
  max-width: 1100px;
  margin: 1.25rem auto;
}

#partialBillingsModal .modal-header,
#reportPreviewModal .modal-header {
  background: linear-gradient(90deg, #2b59d9 0%, #153f9a 100%);
  color: #ffffff;
  border-bottom: none;
}

#partialBillingsModal .modal-title,
#reportPreviewModal .modal-title {
  font-size: 1.125rem;
  font-weight: 700;
}

#partialBillingsModal .modal-body,
#reportPreviewModal .modal-body {
  color: #1f2937;
  line-height: 1.5;
  max-height: 65vh;
  overflow: auto;
  padding: 1rem;
  background: #ffffff;
}

.btn-close.btn-close-white {
  filter: drop-shadow(0 1px 0 rgba(0,0,0,0.12));
}

/* Ensure modal is full-width on small screens but keeps margins */
@media (max-width: 768px) {
  #partialBillingsModal .modal-dialog,
  #reportPreviewModal .modal-dialog {
    max-width: 95%;
    margin: 0.5rem;
  }
  #partialBillingsModal .modal-body { max-height: 60vh; padding: 0.75rem; font-size: 0.95rem; }
  .report-card .report-footer .btn { font-size: 0.85rem; padding: 0.4rem 0.6rem; }
}
</style>

<div class="main-content reports-wrapper">
  <!-- Page Header -->
  <div class="page-header">
    <h1>
      <span class="icon">📊</span>
      Fixed-Rate Water Bill Reports
    </h1>
    <div class="header-actions">
      <a href="<?= site_url('admin/reports') ?>" class="btn btn-outline-light ajax-link">
        <i class="fas fa-sync-alt"></i>
        Refresh
      </a>
      <select id="exportFormat" class="form-control" style="max-width:140px; display:inline-block; margin-right:0.5rem;">
        <option value="csv">CSV</option>
        <option value="xlsx">Excel (.xlsx)</option>
        <option value="pdf">PDF</option>
        <option value="print">Print</option>
      </select>
      <button id="btnExport" class="btn btn-success">
        <i class="fas fa-download"></i>
        Export
      </button>
    </div>
  </div>

  <!-- Filters Card -->
  <div class="card">
    <div class="card-header">
      <i class="fas fa-filter"></i>
      Filter Reports
    </div>
    <div class="card-body">
      <div class="filters-grid">
        <div class="form-group">
          <label class="form-label">Start Date</label>
          <input type="date" class="form-control" id="startDate">
        </div>
        <div class="form-group">
          <label class="form-label">End Date</label>
          <input type="date" class="form-control" id="endDate">
        </div>
        <div class="form-group">
          <label class="form-label">Report Type</label>
          <select class="form-control" id="reportType">
            <option value="">All Reports</option>
            <option value="collection">Collection Status</option>
            <option value="payment">Payment History</option>
            <option value="household">Household Summary</option>
          </select>
        </div>
        <div class="filter-buttons">
          <button class="btn btn-primary" id="applyFilters">
            <i class="fas fa-search"></i>
            Apply
          </button>
          <button class="btn btn-secondary" id="resetFilters">
            <i class="fas fa-undo"></i>
            Reset
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Report Cards Grid -->
  <div class="reports-grid">
    <!-- Fixed Bill Summary Report -->
    <div class="report-card">
      <div class="report-card-header">
        <div class="report-icon">💰</div>
        <div>
          <h3 class="report-title">Fixed Bill Summary</h3>
          <p class="report-subtitle">Year <?= date('Y') ?> Overview</p>
        </div>
      </div>
      <div class="report-body">
        <div class="report-stat">
          <span class="stat-label">Normal Rate (<?= $normalCount ?? 0 ?> households)</span>
          <span class="stat-value">₱<?= number_format($rateNormal ?? 60, 2) ?></span>
        </div>
        <div class="report-stat">
          <span class="stat-label">Senior Citizen Rate (<?= $seniorCount ?? 0 ?> households)</span>
          <span class="stat-value">₱<?= number_format($rateSenior ?? 48, 2) ?></span>
        </div>
        <div class="report-stat">
          <span class="stat-label">Living Alone Rate (<?= $aloneCount ?? 0 ?> households)</span>
          <span class="stat-value">₱<?= number_format($rateAlone ?? 30, 2) ?></span>
        </div>
        <div class="report-stat stat-highlight">
          <span class="stat-label">Expected Monthly Collection</span>
          <span class="stat-value">₱<?= number_format($monthlyExpected ?? 0, 2) ?></span>
        </div>
      </div>
      <div class="report-footer">
        <button class="btn btn-sm btn-outline-primary btn-view" data-report="summary">
          <i class="fas fa-eye"></i> View
        </button>
      </div>
    </div>

    <!-- Payment Collection Report -->
    <div class="report-card">
      <div class="report-card-header">
        <div class="report-icon">💳</div>
        <div>
          <h3 class="report-title">Payment Collection</h3>
          <p class="report-subtitle">Collection Status</p>
        </div>
      </div>
      <div class="report-body">
        <div class="report-stat">
          <span class="stat-label">Collected This Month</span>
          <span class="stat-value">₱<?= number_format($currentMonthCollected ?? 0, 2) ?></span>
        </div>
        <div class="report-stat">
          <span class="stat-label">Paid Households</span>
          <span class="stat-value"><?= $paidHouseholds ?? 0 ?> of <?= $totalHouseholds ?? 0 ?></span>
        </div>
          <div class="report-stat">
            <span class="stat-label">Partial Payments</span>
            <span class="stat-value"><?= $partialCount ?? 0 ?> partially paid</span>
          </div>
        <div class="report-stat">
          <span class="stat-label">Pending Collection</span>
          <span class="stat-value">₱<?= number_format($pendingAmount ?? 0, 2) ?></span>
        </div>
        <div class="report-stat">
          <span class="stat-label">Collection Rate</span>
          <span class="stat-value"><?= $collectionRate ?? 0 ?>%</span>
        </div>
      </div>
      <div class="report-footer">
        <button class="btn btn-sm btn-outline-primary btn-view" data-report="collection">
          <i class="fas fa-eye"></i> View
        </button>
      </div>
    </div>

    <!-- Partial Payments Preview -->
    <div class="report-card">
      <div class="report-card-header">
        <div class="report-icon">🟦</div>
        <div>
          <h3 class="report-title">Partial Payments</h3>
          <p class="report-subtitle">Recently Partially Paid Bills</p>
        </div>
      </div>
      <div class="report-body">
        <div style="max-height:220px; overflow:auto;">
          <table class="table table-sm table-striped mb-0">
            <thead>
              <tr>
                <th>Bill No</th>
                <th>User</th>
                <th>Balance</th>
                <th>Updated</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($partialBills)): foreach (array_slice($partialBills, 0, 8) as $b): ?>
              <tr>
                <td><?= esc($b['bill_no'] ?? $b['id']) ?></td>
                <td><?= esc($b['name'] ?? 'Unknown') ?></td>
                <td>₱<?= number_format($b['balance'] ?? 0, 2) ?></td>
                <td><?= esc($b['updated_at'] ?? '') ?></td>
              </tr>
              <?php endforeach; else: ?>
              <tr><td colspan="4" class="text-center muted">No partial payments in this range.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
      <div class="report-footer">
        <a href="<?= site_url('admin/partialBillings') ?>" class="btn btn-sm btn-outline-primary btn-view-partials">View All</a>
      </div>
    </div>

    <!-- Rate Distribution Report -->
    <div class="report-card">
      <div class="report-card-header">
        <div class="report-icon">📊</div>
        <div>
          <h3 class="report-title">Rate Distribution</h3>
          <p class="report-subtitle">Household Categories</p>
        </div>
      </div>
      <div class="report-body">
        <div class="report-stat">
          <span class="stat-label">Normal Households</span>
          <span class="stat-value"><?= $normalCount ?? 0 ?> 
            <small class="text-muted">(₱<?= number_format(($normalCount ?? 0) * ($rateNormal ?? 60), 2) ?>)</small>
          </span>
        </div>
        <div class="report-stat">
          <span class="stat-label">Senior Citizens</span>
          <span class="stat-value"><?= $seniorCount ?? 0 ?> 
            <small class="text-muted">(₱<?= number_format(($seniorCount ?? 0) * ($rateSenior ?? 48), 2) ?>)</small>
          </span>
        </div>
        <div class="report-stat">
          <span class="stat-label">Living Alone</span>
          <span class="stat-value"><?= $aloneCount ?? 0 ?> 
            <small class="text-muted">(₱<?= number_format(($aloneCount ?? 0) * ($rateAlone ?? 30), 2) ?>)</small>
          </span>
        </div>
        <div class="report-stat stat-highlight">
          <span class="stat-label">Total Households</span>
          <span class="stat-value"><?= $totalHouseholds ?? 0 ?></span>
        </div>
        <div style="margin-top: 1rem; padding: 1rem; background: #f8f9fc; border-radius: 0.375rem;">
          <canvas id="rateDistributionChart" class="mini-chart"></canvas>
        </div>
      </div>
      <div class="report-footer">
        <button class="btn btn-sm btn-outline-primary btn-view" data-report="distribution">
          <i class="fas fa-eye"></i> View
        </button>
      </div>
    </div>

    <!-- Community Statistics -->
    <div class="report-card">
      <div class="report-card-header">
        <div class="report-icon">🏘️</div>
        <div>
          <h3 class="report-title">Community Statistics</h3>
          <p class="report-subtitle">Household Overview</p>
        </div>
      </div>
      <div class="report-body">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
          <div style="text-align: center; padding: 0.75rem; background: #f8f9fc; border-radius: 0.375rem;">
            <div class="stat-label">Active Households</div>
            <div class="stat-value" style="font-size: 1.25rem; color: var(--primary);"><?= $totalHouseholds ?? 0 ?></div>
          </div>
          <div style="text-align: center; padding: 0.75rem; background: #f8f9fc; border-radius: 0.375rem;">
            <div class="stat-label">Pending Payments</div>
            <div class="stat-value" style="font-size: 1.25rem; color: var(--warning);"><?= $pendingCount ?? 0 ?></div>
          </div>
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
          <div style="text-align: center; padding: 0.75rem; background: #f8f9fc; border-radius: 0.375rem;">
            <div class="stat-label">Payment Compliance</div>
            <div class="stat-value" style="font-size: 1.25rem; color: var(--success);"><?= $collectionRate ?? 0 ?>%</div>
          </div>
          <div style="text-align: center; padding: 0.75rem; background: #f8f9fc; border-radius: 0.375rem;">
            <div class="stat-label">Late Payments</div>
            <div class="stat-value" style="font-size: 1.25rem; color: var(--danger);"><?= $latePayments ?? 0 ?></div>
          </div>
        </div>
        <div style="margin-top: 1rem;">
          <canvas id="miniPaymentChart" class="mini-chart"></canvas>
        </div>
      </div>
      <div class="report-footer">
        <button class="btn btn-sm btn-outline-primary btn-view" data-report="community">
          <i class="fas fa-eye"></i> View
        </button>
      </div>
    </div>
  </div>

  <!-- Charts Section -->
  <div class="row">
    <div class="col-lg-8">
      <!-- Monthly Collection Chart -->
      <div class="chart-container">
        <div class="chart-header">
          <h3 class="chart-title">
            <i class="fas fa-chart-line text-primary"></i>
            Monthly Collection Rate (<?= date('Y') ?>)
          </h3>
        </div>
        <div class="chart-body">
          <canvas id="collectionChart" class="chart-canvas"></canvas>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <!-- Payment Status Chart -->
      <div class="chart-container">
        <div class="chart-header">
          <h3 class="chart-title">
            <i class="fas fa-chart-pie text-success"></i>
            Payment Status
          </h3>
        </div>
        <div class="chart-body">
          <canvas id="paymentStatusChart" class="chart-canvas"></canvas>
        </div>
      </div>
    </div>
  </div>

        <!-- Partial Billings Modal (loads partialBillings view via AJAX) -->
        <div class="modal fade" id="partialBillingsModal" tabindex="-1" aria-labelledby="partialBillingsLabel" aria-hidden="true">
          <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
              <div class="modal-header" style="background: linear-gradient(90deg,var(--primary), var(--primary-dark)); color:white;">
                <h5 class="modal-title" id="partialBillingsLabel"><i class="fas fa-list me-2"></i>Partial Billings</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body" style="min-height:40vh;">
                <div class="text-center text-muted">Loading...</div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline btn-sm" data-bs-dismiss="modal">Close</button>
              </div>
            </div>
          </div>
        </div>
</div>

<!-- Data for Charts -->
<script type="application/json" id="reports-data">
<?= json_encode([
  'collectionRates' => $collectionRates ?? array_fill(0, 12, 0),
  'collectionAmounts' => $collectionAmounts ?? array_fill(0, 12, 0),
  'partialRates' => $partialRates ?? array_fill(0, 12, 0),
  'paidHouseholds' => (int)($paidHouseholds ?? 0),
  'pendingCount' => (int)($pendingCount ?? 0),
  'partialCount' => (int)($partialCount ?? 0),
  'latePayments' => (int)($latePayments ?? 0),
  'normalCount' => (int)($normalCount ?? 0),
  'seniorCount' => (int)($seniorCount ?? 0),
  'aloneCount' => (int)($aloneCount ?? 0),
  'rateNormal' => (float)($rateNormal ?? 60),
  'rateSenior' => (float)($rateSenior ?? 48),
  'rateAlone' => (float)($rateAlone ?? 30),
  'year' => (int)date('Y'),
  'filterStart' => $filterStart ?? null,
  'filterEnd' => $filterEnd ?? null,
  'filterType' => $filterType ?? null,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
</script>

<!-- Report Preview Modal -->
<div class="modal fade" id="reportPreviewModal" tabindex="-1" aria-labelledby="reportPreviewLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header" style="background: linear-gradient(90deg,var(--primary), var(--primary-dark)); color:white;">
        <h5 class="modal-title" id="reportPreviewLabel"><i class="fas fa-file-alt me-2"></i>Report Preview</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" style="min-height:60vh;">
        <div class="text-center text-muted">Loading preview...</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline btn-sm" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
(function() {
    'use strict';
    
    // Initialize when content loads
    function initializeReports() {
        console.log('[Reports] Initializing AJAX content...');
        
        // Set default dates
        const today = new Date();
        const startOfYear = new Date(today.getFullYear(), 0, 1);
        
        const startDateEl = document.getElementById('startDate');
        const endDateEl = document.getElementById('endDate');
        
        if (startDateEl && !startDateEl.value) {
            startDateEl.valueAsDate = startOfYear;
        }
        if (endDateEl && !endDateEl.value) {
            endDateEl.valueAsDate = today;
        }
        
        // Initialize charts
        initializeCharts();
        
        // Setup event listeners
        setupEventListeners();
        
        console.log('[Reports] Initialization complete');
    }
    
    function initializeCharts() {
        try {
            const dataElement = document.getElementById('reports-data');
            if (!dataElement) {
                console.warn('[Reports] No data element found');
                return;
            }
            
            const data = JSON.parse(dataElement.textContent);
            console.log('[Reports] Chart data:', data);
            
            // Initialize Collection Chart
            const collectionCanvas = document.getElementById('collectionChart');
            if (collectionCanvas && data.collectionRates) {
                  initCollectionChart(collectionCanvas, data);
            }
            
            // Initialize Payment Status Chart
            const statusCanvas = document.getElementById('paymentStatusChart');
            if (statusCanvas) {
                initPaymentStatusChart(statusCanvas, data);
            }
            
            // Initialize Rate Distribution Chart
            const rateCanvas = document.getElementById('rateDistributionChart');
            if (rateCanvas) {
                initRateDistributionChart(rateCanvas, data);
            }
            
            // Initialize Mini Payment Chart
            const miniCanvas = document.getElementById('miniPaymentChart');
            if (miniCanvas) {
                initMiniPaymentChart(miniCanvas, data);
            }
            
        } catch (error) {
            console.error('[Reports] Chart initialization error:', error);
        }
    }
    
    function initCollectionChart(canvas, data) {
        const ctx = canvas.getContext('2d');
      // Destroy previous chart instance if present (handles AJAX reloads)
      window.reportsCharts = window.reportsCharts || {};
      if (window.reportsCharts.collectionChart) {
        try { window.reportsCharts.collectionChart.destroy(); } catch (e) { /* ignore */ }
      }

      window.reportsCharts.collectionChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Collection Rate (%)',
                    data: data.collectionRates || [],
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
          }, {
                    label: 'Amount Collected (₱)',
                    data: data.collectionAmounts || [],
                    borderColor: '#1cc88a',
                    backgroundColor: 'rgba(28, 200, 138, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    yAxisID: 'y1'
          }, {
            label: 'Partial Rate (%)',
            data: data.partialRates || [],
            borderColor: '#36b9cc',
            backgroundColor: 'rgba(54, 185, 204, 0.12)',
            borderWidth: 3,
            pointRadius: 4,
            pointBackgroundColor: '#36b9cc',
            pointHoverRadius: 6,
            fill: false,
            tension: 0.25,
            spanGaps: true
          }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        type: 'linear',
                    display: true,
                        position: 'left',
                    beginAtZero: true,
                    max: 100
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        beginAtZero: true,
                        grid: {
                            drawOnChartArea: false
                        }
                    }
                }
            }
        });
    }
    
    function initPaymentStatusChart(canvas, data) {
        const ctx = canvas.getContext('2d');
      // Coerce counts to numbers and ensure partial is included
      const counts = [
        Number(data.paidHouseholds || 0),
        Number(data.partialCount || 0),
        Number(data.pendingCount || 0),
        Number(data.latePayments || 0)
      ];

      const total = counts.reduce((s, v) => s + v, 0);

      // If everything is zero, show a placeholder slice to avoid Chart.js hiding legend
      const displayCounts = total === 0 ? [1, 0, 0, 0] : counts;

      window.reportsCharts = window.reportsCharts || {};
      if (window.reportsCharts.paymentStatusChart) {
        try { window.reportsCharts.paymentStatusChart.destroy(); } catch (e) { }
      }

      window.reportsCharts.paymentStatusChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: ['Paid', 'Partial', 'Pending', 'Late'],
          datasets: [{
            data: displayCounts,
            backgroundColor: ['#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
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
              position: 'bottom'
            },
            tooltip: {
              callbacks: {
                label: function(context) {
                  const label = context.label || '';
                  const value = context.raw || 0;
                  const t = displayCounts.reduce((s, v) => s + v, 0);
                  const pct = t > 0 ? Math.round((value / t) * 100 * 10) / 10 : 0;
                  return `${label}: ${value} (${pct}%)`;
                }
              }
            }
          }
        }
      });
    }
    
    function initRateDistributionChart(canvas, data) {
        const ctx = canvas.getContext('2d');
        window.reportsCharts = window.reportsCharts || {};
        if (window.reportsCharts.rateDistributionChart) {
          try { window.reportsCharts.rateDistributionChart.destroy(); } catch (e) { }
        }

        window.reportsCharts.rateDistributionChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Normal', 'Senior', 'Alone'],
                datasets: [{
                    data: [data.normalCount || 0, data.seniorCount || 0, data.aloneCount || 0],
                    backgroundColor: ['#4e73df', '#1cc88a', '#f6c23e'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }
    
    function initMiniPaymentChart(canvas, data) {
        const ctx = canvas.getContext('2d');
        window.reportsCharts = window.reportsCharts || {};
        if (window.reportsCharts.miniPaymentChart) {
          try { window.reportsCharts.miniPaymentChart.destroy(); } catch (e) { }
        }

        window.reportsCharts.miniPaymentChart = new Chart(ctx, {
            type: 'bar',
            data: {
          labels: ['Paid', 'Partial', 'Pending', 'Late'],
          datasets: [{
            data: [
              data.paidHouseholds || 0,
              data.partialCount || 0,
              data.pendingCount || 0,
              data.latePayments || 0
            ],
            backgroundColor: ['#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
            borderWidth: 0
          }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        display: false
                    },
                    x: {
                        display: false
                    }
                }
            }
        });
    }
    
    function setupEventListeners() {
        // Filter apply button
        const applyBtn = document.getElementById('applyFilters');
        if (applyBtn) {
            applyBtn.addEventListener('click', function() {
                console.log('[Reports] Apply filters clicked');
                applyFilters();
            });
        }
        
        // Filter reset button
        const resetBtn = document.getElementById('resetFilters');
        if (resetBtn) {
            resetBtn.addEventListener('click', function() {
                console.log('[Reports] Reset filters clicked');
                resetFilters();
            });
        }
        
        // View buttons
        document.querySelectorAll('.btn-view').forEach(btn => {
            btn.addEventListener('click', function() {
                const reportType = this.getAttribute('data-report');
                console.log('[Reports] View report:', reportType);
                viewReport(reportType);
            });
        });

        // Partial Billings: open in modal via AJAX instead of navigating away
        const partialBtn = document.querySelector('.btn-view-partials');
        if (partialBtn) {
          partialBtn.addEventListener('click', function(e){
            e.preventDefault();
            const url = this.href;
            const modalEl = document.getElementById('partialBillingsModal');
            const body = modalEl.querySelector('.modal-body');
            body.innerHTML = '<div class="text-center text-muted">Loading...</div>';
            fetch(url, { credentials: 'same-origin' })
              .then(r => r.text())
              .then(html => {
              // Inject the returned partialBillings view into the modal body
              body.innerHTML = html;
              try {
                const bsModal = new bootstrap.Modal(modalEl);
                bsModal.show();
              } catch (err) {
                console.warn('Bootstrap modal not available, fallback to alert');
              }
              }).catch(err => {
              console.error('Failed to load partial billings:', err);
              body.innerHTML = '<div class="text-center text-danger">Failed to load. See console for details.</div>';
              });
          });
        }
    }
    
    function applyFilters() {
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const reportType = document.getElementById('reportType').value;
        
        const params = new URLSearchParams();
        if (startDate) params.set('start', startDate);
        if (endDate) params.set('end', endDate);
        if (reportType) params.set('type', reportType);
        
        const url = '<?= site_url("admin/reports") ?>' + (params.toString() ? '?' + params.toString() : '');
        
        // Use the dashboard's AJAX loading function
        if (typeof loadAjaxPage === 'function') {
            loadAjaxPage(url);
        } else {
            window.location.href = url;
        }
    }
    
    function resetFilters() {
        document.getElementById('startDate').value = '';
        document.getElementById('endDate').value = '';
        document.getElementById('reportType').value = '';
        
        const today = new Date();
        const startOfYear = new Date(today.getFullYear(), 0, 1);
        document.getElementById('startDate').valueAsDate = startOfYear;
        document.getElementById('endDate').valueAsDate = today;
        
        // Reload without parameters
        const url = '<?= site_url("admin/reports") ?>';
        if (typeof loadAjaxPage === 'function') {
            loadAjaxPage(url);
        } else {
            window.location.href = url;
        }
    }
    
    function viewReport(reportType) {
      const startDate = document.getElementById('startDate').value;
      const endDate = document.getElementById('endDate').value;
      const params = new URLSearchParams();
      if (startDate) params.set('start', startDate);
      if (endDate) params.set('end', endDate);
      if (reportType) params.set('type', reportType);
      // Use print format to get printer-friendly HTML
      params.set('format', 'print');

      const url = '<?= site_url("admin/exportReports") ?>' + '?' + params.toString();
      console.log('[Reports] Fetching preview:', url);

      fetch(url, { credentials: 'same-origin' })
        .then(r => r.text())
        .then(html => {
          let modal = document.getElementById('reportPreviewModal');
          if (!modal) {
            console.warn('Preview modal not found');
            // Fallback: open in new tab
            const win = window.open();
            win.document.write(html);
            win.document.close();
            return;
          }
          const body = modal.querySelector('.modal-body');
          body.innerHTML = html;
          try {
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
          } catch (e) {
            // fallback if bootstrap not available
            modal.style.display = 'block';
          }
        }).catch(err => {
          console.error('Preview fetch error', err);
          alert('Failed to load preview. Check console for details.');
        });
    }
    
    // Export format selector logic
    document.getElementById('btnExport')?.addEventListener('click', function() {
      const format = document.getElementById('exportFormat')?.value || 'csv';
      const baseUrl = '<?= site_url('admin/exportReports') ?>';
      // Collect filter params
      const startDate = document.getElementById('startDate')?.value;
      const endDate = document.getElementById('endDate')?.value;
      const reportType = document.getElementById('reportType')?.value;
      const params = new URLSearchParams();
      if (startDate) params.set('start', startDate);
      if (endDate) params.set('end', endDate);
      if (reportType) params.set('type', reportType);
      params.set('format', format);
      const url = baseUrl + '?' + params.toString();
      if (format === 'print') {
        window.open(url, '_blank');
      } else {
        window.location.href = url;
      }
    });
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeReports);
    } else {
        initializeReports();
    }

})();
</script>
