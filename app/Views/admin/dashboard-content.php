<script>
// Pass PHP data to JavaScript
window.dashboardData = {
    months: <?= $months ?? '[]' ?>,
    incomeData: <?= $incomeData ?? '[]' ?>,
    totalCollected: <?= $totalCollected ?? 0 ?>,
    monthlyTotal: <?= $monthlyTotal ?? 0 ?>,
    active: <?= $active ?? 0 ?>,
    pending: <?= $pending ?? 0 ?>,
    inactive: <?= $inactive ?? 0 ?>,
    normalRevenue: <?= $normalRevenue ?? 0 ?>,
    seniorRevenue: <?= $seniorRevenue ?? 0 ?>,
    aloneRevenue: <?= $aloneRevenue ?? 0 ?>
};
</script>

<style>
/* Dashboard - Clean, Professional Design (tweaked for logo alignment and small UI helpers) */

@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

:root{
  --bg-primary: #f8fafc;
  --bg-secondary: #ffffff;
  --text-primary: #1e293b;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --border-color: #e2e8f0;
  --primary: #3b82f6;
  --primary-hover: #2563eb;
  --success: #10b981;
  --warning: #f59e0b;
  --danger: #ef4444;
  --purple: #8b5cf6;
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
  --radius: 12px;
  --radius-lg: 16px;
}

/* Reset */
* { box-sizing: border-box; }

/* Page basics */
body { font-family: 'Inter', system-ui, -apple-system, sans-serif; background: var(--bg-primary); color: var(--text-primary); line-height: 1.6; }
.container-fluid { padding: 2rem; max-width: 1600px; margin: 0 auto; background: var(--bg-primary); min-height: 100vh; }
.main-content { width: 100%; }

/* Header */
.page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem; background: var(--bg-secondary); padding: 1.5rem 2rem; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); border: 1px solid var(--border-color); }
.page-title { font-weight: 700; color: var(--text-primary); font-size: 1.75rem; letter-spacing: -0.02em; margin: 0; }
.btn-report { background: var(--primary); border: none; color: #fff; padding: 0.75rem 1.5rem; border-radius: var(--radius); font-weight: 600; font-size: 0.95rem; box-shadow: var(--shadow-sm); transition: all 0.2s ease; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; }
.btn-report:hover { background: var(--primary-hover); transform: translateY(-1px); box-shadow: var(--shadow-md); }

/* Stats Grid: force a 2x2 layout on desktop for consistent appearance */
.stats-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1.5rem; margin-bottom: 2rem; align-items: stretch; }
.stat-card { background: var(--bg-secondary); border-radius: var(--radius); padding: 1.5rem 1.75rem; box-shadow: var(--shadow-sm); border: 1px solid var(--border-color); transition: all 0.18s ease; display: flex; align-items: center; gap: 1rem; min-height: 110px; }
.stat-card.is-clickable { transition: all 0.18s ease; }
.stat-card.is-clickable:hover { box-shadow: var(--shadow-md); transform: translateY(-4px); border-color: var(--primary); cursor: pointer; }

/* Layout fix: ensure content and icon align regardless of text wrap */
.stat-content { flex: 1 1 auto; min-width: 0; }
.stat-label { color: var(--text-secondary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.75rem; }
.stat-value { font-size: 2.25rem; font-weight: 800; color: var(--text-primary); line-height: 1; margin-bottom: 0.5rem; letter-spacing: -0.03em; }
.stat-description { color: var(--text-muted); font-size: 0.875rem; font-weight: 500; }

/* Icon wrapper: ensure perfect centering and consistent size */
.stat-icon-wrapper { width: 64px; height: 64px; border-radius: calc(var(--radius) - 4px); display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-left: 8px; }
.stat-icon-wrapper i { font-size: 1.75rem; line-height: 1; }

/* color variants */
.stat-icon-wrapper.blue { background: rgba(59, 130, 246, 0.1); color: var(--primary); }
.stat-icon-wrapper.green { background: rgba(16, 185, 129, 0.1); color: var(--success); }
.stat-icon-wrapper.purple { background: rgba(139, 92, 246, 0.1); color: var(--purple); }
.stat-icon-wrapper.orange { background: rgba(245, 158, 11, 0.1); color: var(--warning); }

/* Charts and cards */
.charts-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 2rem; }
.card { background: var(--bg-secondary); border-radius: var(--radius); box-shadow: var(--shadow-sm); border: 1px solid var(--border-color); overflow: hidden; }
.card-header { background: var(--bg-secondary); padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-color); }
.card-header h6 { margin: 0; font-weight: 700; color: var(--text-primary); font-size: 1.05rem; letter-spacing: -0.01em; }
.card-body { padding: 1.5rem; }

/* Chart containers */
.chart-container { position: relative; width: 100%; height: 350px; }
.chart-pie { width: 100%; height: 280px; display:flex; justify-content:center; align-items:center; }

/* Table */
.table-responsive { border-radius: var(--radius); overflow: auto; }
.table { width: 100%; border-collapse: collapse; background: transparent; }
.table th, .table td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--border-color); font-size: 0.9rem; }
.table thead th { background: var(--bg-primary); font-size: 0.8rem; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700; border-bottom: 2px solid var(--border-color); }
.table tbody tr { transition: background 0.15s ease; }
.table tbody tr:hover { background: var(--bg-primary); }
.table tbody tr:last-child td { border-bottom: none; }

/* Small search input inserted when DataTables not available */
.table-search { display:block; width: 100%; padding: .5rem .75rem; margin-bottom: .5rem; border:1px solid var(--border-color); border-radius:8px; font-size:.95rem; }

/* Badges and project styles unchanged (kept for compatibility) */
.badge { display:inline-flex; align-items:center; gap:0.5rem; padding:0.5rem 1rem; border-radius:999px; font-weight:600; font-size:0.85rem; }
.badge.primary { background: rgba(59,130,246,0.1); color: var(--primary); }
.badge.success { background: rgba(16,185,129,0.1); color: var(--success); }
.badge.accent { background: rgba(6,182,212,0.1); color: #0891b2; }

/* Progress and projects styling preserved */
.projects-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
.project-item { margin-bottom: 1.5rem; }
.project-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; }
.project-name { font-size: 0.9rem; font-weight: 600; color: var(--text-primary); margin: 0; }
.project-percentage { font-size: 0.85rem; font-weight: 600; color: var(--text-secondary); }
.progress { height: 10px; border-radius: 999px; background: var(--bg-primary); overflow: hidden; }
.progress-bar { height: 100%; border-radius: 999px; transition: width 0.6s ease; background: var(--primary); }
.progress-bar.bg-danger { background: var(--danger); }
.progress-bar.bg-warning { background: var(--warning); }
.progress-bar.bg-info { background: #06b6d4; }
.progress-bar.bg-success { background: var(--success); }

/* Scoped: revenue legend chips (targets only the legend right after .chart-pie) */
.chart-pie + .badge-legend {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 0.6rem;
  margin-top: 12px;
  padding-left: 4px;
}

/* Pill styling scoped to that legend */
.chart-pie + .badge-legend .badge {
  position: relative;
  display: inline-block;
  padding-left: 44px;   /* space for the fixed dot */
  padding-right: 14px;
  gap: 0;               /* remove gap so text lines up exactly */
  text-align: left;
  white-space: nowrap;
  border-radius: 999px;
  font-weight: 700;
  font-size: 0.95rem;
  box-shadow: 0 8px 18px rgba(15,23,42,0.04);
  background-clip: padding-box;
  color: inherit;
}

/* Fixed dot in pill */
.chart-pie + .badge-legend .badge i {
  position: absolute;
  left: 14px;           /* fixed dot x-position */
  top: 50%;
  transform: translateY(-50%);
  width: 12px;
  height: 12px;
  min-width: 12px;
  min-height: 12px;
  line-height: 1;
  margin: 0;
  padding: 0;
  display: block;
}

/* keep the same soft backgrounds and dot colors */
.chart-pie + .badge-legend .badge.primary { background: rgba(59,130,246,0.10); }
.chart-pie + .badge-legend .badge.success { background: rgba(16,185,129,0.08); }
.chart-pie + .badge-legend .badge.accent  { background: rgba(6,182,212,0.08); }

.chart-pie + .badge-legend .badge.primary i { color: #3b82f6; }
.chart-pie + .badge-legend .badge.success i { color: #10b981; }
.chart-pie + .badge-legend .badge.accent  i { color: #06b6d4; }

/* Slight responsive shrink */
@media (max-width: 576px) {
  .chart-pie + .badge-legend .badge { padding-left: 36px; padding-right: 10px; }
  .chart-pie + .badge-legend .badge i { left: 12px; width:10px; height:10px; }
}

/* Responsive */
@media (max-width: 1200px) { .charts-grid { grid-template-columns: 1fr; } .projects-grid { grid-template-columns: 1fr; } }
@media (max-width: 992px) {
  .container-fluid { padding: 1.75rem 1rem; }
  .page-header { flex-direction: column; align-items: stretch; gap: 1rem; padding: 1.25rem 1.5rem; }
  .page-title { font-size: 1.6rem; }
  .btn-report { width: 100%; justify-content: center; }
}

@media (max-width: 768px) {
  .stats-grid { grid-template-columns: 1fr; gap: 1rem; }
  .container-fluid { padding: 1.5rem 1rem; }
  .page-header { padding: 1.25rem 1.5rem; }
  .page-title { font-size: 1.5rem; }
  .btn-report { width: 100%; justify-content: center; }
  .stat-value { font-size: 1.75rem; }
  .stat-icon-wrapper { width: 56px; height: 56px; }
  .stat-icon-wrapper i { font-size: 1.5rem; }
  .charts-grid { gap: 1rem; }
  .projects-grid { gap: 1rem; }
  .chart-container { height: 280px; }
}
@media (max-width: 576px) {
  .stat-card { padding: 1.25rem; }
  .stat-value { font-size: 1.5rem; }
  .stat-label { font-size: 0.75rem; }
  .card-body { padding: 1.25rem; }
  .table th, .table td { padding: 0.75rem 0.5rem; font-size: 0.85rem; }
}

/* subtle animation */
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
.stat-card, .card { animation: fadeIn 0.4s ease-out backwards; }
.stat-card:nth-child(1) { animation-delay: 0.05s; }
.stat-card:nth-child(2) { animation-delay: 0.1s; }
.stat-card:nth-child(3) { animation-delay: 0.15s; }
.stat-card:nth-child(4) { animation-delay: 0.2s; }
</style>

<!-- Main Content -->
<div class="container-fluid">
    <div class="main-content">
        <!-- Header -->
        <div class="page-header">
            <h1 class="page-title">Dashboard</h1>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-label">Earnings (Monthly)</div>
                    <div class="stat-value">₱<?= number_format($monthlyTotal, 2) ?></div>
                    <div class="stat-description">This month</div>
                </div>
                <div class="stat-icon-wrapper blue" aria-hidden="true">
                    <i class="fas fa-calendar"></i>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-label">Earnings (Annual)</div>
                    <div class="stat-value">₱<?= number_format($totalCollected, 2) ?></div>
                    <div class="stat-description">Year to date</div>
                </div>
                <div class="stat-icon-wrapper green" aria-hidden="true">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-label">Active Users</div>
                    <div class="stat-value"><?= number_format($active ?? 0) ?></div>
                    <div class="stat-description">Currently active</div>
                </div>
                <div class="stat-icon-wrapper purple" aria-hidden="true">
                    <i class="fas fa-users"></i>
                </div>
            </div>

            <div class="stat-card">
              <div class="stat-content">
                <div class="stat-label">Pending Payments</div>
                <div class="stat-value"><?= number_format($pendingPayments ?? 0) ?></div>
                <div class="stat-description">Unsettled billings</div>
              </div>
              <div class="stat-icon-wrapper orange" aria-hidden="true">
                <i class="fas fa-file-invoice-dollar"></i>
              </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="charts-grid">
            <!-- Area Chart -->
            <div class="card">
                <div class="card-header">
                    <h6>Earnings Overview</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="incomeChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Pie Chart -->
            <div class="card">
                <div class="card-header">
                    <h6>Revenue by Rate Category</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie">
                        <canvas id="revenueChart"></canvas>
                    </div>
                    <div class="badge-legend" style="margin-top:12px;">
                        <span class="badge primary">
                            <i class="fas fa-circle"></i>
                            Normal (₱60)
                        </span>
                        <span class="badge success">
                            <i class="fas fa-circle"></i>
                            Senior (₱48)
                        </span>
                        <span class="badge accent">
                            <i class="fas fa-circle"></i>
                            Living Alone (₱30)
                        </span>
                    </div>
                </div>
            </div>
        </div>


    </div>
</div>


<!-- JS: safe initializers for charts + table (no external deps required; will use Chart.js/DataTables if present) -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  // Ensure icons are vertically centered (redundant guard)
  document.querySelectorAll('.stat-icon-wrapper').forEach(function (el) {
    el.style.display = 'flex';
    el.style.alignItems = 'center';
    el.style.justifyContent = 'center';
  });

  // --- Charts (Chart.js) ---
  if (window.Chart && window.dashboardData) {
    try {
      // Income line chart
      const months = Array.isArray(window.dashboardData.months) ? window.dashboardData.months : [];
      const incomeData = Array.isArray(window.dashboardData.incomeData) ? window.dashboardData.incomeData : [];

      const incomeCanvas = document.getElementById('incomeChart');
      if (incomeCanvas && incomeCanvas.getContext) {
        const ctx = incomeCanvas.getContext('2d');
        // Destroy existing chart instance if present to avoid duplicates on PJAX/AJAX reloads
        if (incomeCanvas._chart) incomeCanvas._chart.destroy();

        incomeCanvas._chart = new Chart(ctx, {
          type: 'line',
          data: {
            labels: months,
            datasets: [{
              label: 'Earnings',
              data: incomeData,
              borderColor: '#3b82f6',
              backgroundColor: 'rgba(59,130,246,0.08)',
              pointBackgroundColor: '#fff',
              pointBorderColor: '#3b82f6',
              tension: 0.35,
              fill: true,
              pointRadius: 3,
              borderWidth: 2
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: { display: false },
              tooltip: { mode: 'index', intersect: false }
            },
            scales: {
              x: { grid: { display: false }, ticks: { color: '#475569' } },
              y: { grid: { color: 'rgba(15,23,42,0.04)' }, ticks: { color: '#475569', beginAtZero: true } }
            }
          }
        });
      }

      // Revenue doughnut chart
      const revenueCanvas = document.getElementById('revenueChart');
      if (revenueCanvas && revenueCanvas.getContext) {
        const rctx = revenueCanvas.getContext('2d');
        if (revenueCanvas._chart) revenueCanvas._chart.destroy();

        const rdata = [
          parseFloat(window.dashboardData.normalRevenue || 0),
          parseFloat(window.dashboardData.seniorRevenue || 0),
          parseFloat(window.dashboardData.aloneRevenue || 0)
        ];

        revenueCanvas._chart = new Chart(rctx, {
          type: 'doughnut',
          data: {
            labels: ['Normal (₱60)', 'Senior (₱48)', 'Living Alone (₱30)'],
            datasets: [{
              data: rdata,
              backgroundColor: ['#3b82f6', '#10b981', '#06b6d4'],
              hoverOffset: 6
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: { position: 'bottom', labels: { color: '#475569' } },
              tooltip: { callbacks: { label: function(context) {
                const val = context.raw ?? 0;
                return context.label + ': ₱' + Number(val).toLocaleString('en-US', { minimumFractionDigits: 2 });
              } } }
            }
          }
        });
      }
    } catch (err) {
      console.error('Chart initialization error:', err);
    }
  } else {
    // Chart.js not loaded — safe fallback
    if (!window.Chart) console.info('Chart.js not detected - income/revenue charts will not render.');
  }

  // --- Data table initialization: prefer DataTables if available, otherwise add light search ---
  const dataTableEl = document.getElementById('dataTable');
  if (window.jQuery && window.jQuery.fn && window.jQuery.fn.DataTable && dataTableEl) {
    try {
      const $table = window.jQuery(dataTableEl);
      if ($table.DataTable && !$table.hasClass('dataTable-initialized')) {
        $table.DataTable({
          responsive: true,
          pageLength: 5,
          lengthChange: false,
          searching: true,
          ordering: true
        });
        $table.addClass('dataTable-initialized');
      }
    } catch (err) {
      console.warn('DataTables initialization failed:', err);
    }
  } else if (dataTableEl) {
    // Lightweight fallback: add search box that filters rows
    const wrapper = dataTableEl.closest('.table-responsive');
    if (wrapper && !wrapper.querySelector('.table-search')) {
      const search = document.createElement('input');
      search.type = 'search';
      search.placeholder = 'Search table...';
      search.className = 'table-search';
      wrapper.insertBefore(search, wrapper.firstChild);

      search.addEventListener('input', function () {
        const q = this.value.trim().toLowerCase();
        const rows = dataTableEl.tBodies[0].rows;
        for (let i = 0; i < rows.length; i++) {
          const row = rows[i];
          const text = row.textContent.toLowerCase();
          row.style.display = text.indexOf(q) !== -1 ? '' : 'none';
        }
      });
    }
  }

  // Accessibility: add role/aria attributes for the stat icon wrappers if not present
  document.querySelectorAll('.stat-icon-wrapper').forEach(function (el) {
    if (!el.hasAttribute('role')) el.setAttribute('role', 'img');
  });
});
</script>

