
<style>
  :root { --primary: #667eea; --primary-dark: #5568d3; --primary-light: #8b9eff; --success: #10b981; --warning: #f59e0b; --danger: #ef4444; --info: #06b6d4; --border: #e5e7eb; --dark: #1f2937; --light: #f9fafb; --muted: #6b7280; }
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #f0f4f8 0%, #d9e2ec 100%); min-height: 100vh; }
  .payments-wrapper { padding: 2rem; max-width: 1400px; margin: 0 auto; }
  .payments-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 3rem; border-radius: 24px; margin-bottom: 3rem; box-shadow: 0 25px 60px rgba(102, 126, 234, 0.35); position: relative; overflow: hidden; }
  .payments-header::before { content: ''; position: absolute; top: -50%; right: -10%; width: 500px; height: 500px; background: radial-gradient(circle, rgba(255, 255, 255, 0.08) 0%, transparent 70%); border-radius: 50%; }
  .payments-header::after { content: ''; position: absolute; bottom: -10%; left: 5%; width: 300px; height: 300px; background: radial-gradient(circle, rgba(255, 255, 255, 0.05) 0%, transparent 70%); border-radius: 50%; }
  .payments-header-content { position: relative; z-index: 1; }
  .payments-header-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem; flex-wrap: wrap; gap: 2rem; }
  .payments-header-title { font-size: 3rem; font-weight: 900; letter-spacing: -2px; display: flex; align-items: center; gap: 1rem; }
  .payments-header-icon { font-size: 3.5rem; animation: bounce 2s ease-in-out infinite; }
  @keyframes bounce { 0%, 100% { transform: translateY(0) rotate(0deg); } 50% { transform: translateY(-15px) rotate(5deg); } }
  .payments-date-range { background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(20px); padding: 1rem 1.75rem; border-radius: 16px; font-size: 1.1rem; font-weight: 700; border: 2px solid rgba(255, 255, 255, 0.3); box-shadow: 0 8px 32px rgba(31, 38, 135, 0.2); display: flex; align-items: center; gap: 0.75rem; }
  .payments-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.75rem; }
  .stat-item { background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(20px); padding: 2rem; border-radius: 18px; border: 2px solid rgba(255, 255, 255, 0.25); transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); position: relative; overflow: hidden; min-height: 220px; display: flex; flex-direction: column; justify-content: space-between; }
  .stat-item::before { content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.15), transparent); transition: left 0.7s ease; }
  .stat-item:hover { background: rgba(255, 255, 255, 0.25); transform: translateY(-12px); box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.2); border-color: rgba(255, 255, 255, 0.4); }
  .stat-item:hover::before { left: 100%; }
  .stat-label { font-size: 0.8rem; opacity: 0.85; margin-bottom: 1rem; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 700; display: flex; align-items: center; gap: 0.75rem; color: rgba(255, 255, 255, 0.9); }
  .stat-icon { font-size: 1.4rem; filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1)); }
  .stat-value { font-size: 2.8rem; font-weight: 900; letter-spacing: -1.5px; line-height: 1; margin: 0.5rem 0; display: flex; align-items: baseline; gap: 0.75rem; color: white; }
  .stat-value.amount { font-size: 2.5rem; }
  .stat-value.amount::before { content: '₱'; font-size: 2rem; opacity: 0.95; font-weight: 800; }
  .stat-change { font-size: 0.9rem; margin-top: 0.75rem; opacity: 0.9; display: inline-flex; align-items: center; gap: 0.5rem; font-weight: 600; }
  .stat-change.positive { color: #7ee8b7; }
  .stat-change.neutral { color: rgba(255, 255, 255, 0.75); }
  .filters-section { background: white; padding: 1.75rem; border-radius: 18px; margin-bottom: 2rem; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); border: 1px solid var(--border); }
  .filters-title { font-weight: 700; color: var(--dark); margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.75rem; font-size: 1.1rem; }
  .filters-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin-bottom: 1rem; }
  .filter-group { display: flex; flex-direction: column; }
  .filter-label { font-size: 0.85rem; font-weight: 700; color: var(--dark); margin-bottom: 0.6rem; }
  .filter-input, .filter-select { padding: 0.85rem; border: 2px solid var(--border); border-radius: 10px; font-size: 0.9rem; font-family: 'Poppins', sans-serif; background: var(--light); transition: all 0.3s ease; }
  .filter-input:focus, .filter-select:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1); background: white; }
  .filter-btn { padding: 0.85rem 1.5rem; border: none; border-radius: 10px; font-weight: 700; cursor: pointer; transition: all 0.3s ease; font-size: 0.9rem; }
  .filter-btn-search { background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); color: white; flex: 1; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3); }
  .filter-btn-search:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4); }
  .filter-btn-reset { background: white; color: var(--dark); border: 2px solid var(--border); }
  .filter-btn-reset:hover { background: var(--light); border-color: var(--primary); }
  .export-btn { padding: 0.7rem 1.5rem; background: linear-gradient(135deg, var(--success) 0%, #0d9488 100%); color: white; border: none; border-radius: 10px; font-size: 0.9rem; font-weight: 700; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3); }
  .export-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4); }
  .table-container { background: white; border-radius: 18px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); border: 1px solid var(--border); overflow: hidden; margin-bottom: 2rem; }
  .table-header { padding: 1.75rem; background: linear-gradient(135deg, var(--light) 0%, white 100%); border-bottom: 2px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
  .table-header-title { font-weight: 700; color: var(--dark); font-size: 1.1rem; }
  .table-wrapper { overflow-x: auto; }
  table { width: 100%; border-collapse: collapse; }
  th { padding: 1rem 1.5rem; text-align: left; font-weight: 700; color: var(--dark); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid var(--border); background: var(--light); }
  td { padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border); font-size: 0.9rem; }
  tbody tr { transition: all 0.2s ease; }
  tbody tr:hover { background: rgba(102, 126, 234, 0.03); }
  .user-info { display: flex; align-items: center; gap: 0.75rem; }
  .user-avatar { width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.9rem; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3); }
  .user-details h4 { margin: 0; font-weight: 700; color: var(--dark); font-size: 0.95rem; }
  .user-details p { margin: 0.3rem 0 0; color: var(--muted); font-size: 0.85rem; }
  .badge { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.2rem; border-radius: 20px; font-size: 0.85rem; font-weight: 700; white-space: nowrap; margin: 0.25rem; box-shadow: 0 2px 6px rgba(0,0,0,0.05); }
  .badge-overdue { background: rgba(245, 158, 11, 0.25); color: #92400e; border: 1.5px solid rgba(245, 158, 11, 0.4); }
  .amount { font-weight: 700; color: var(--dark); font-size: 0.95rem; }
  .no-data { text-align: center; padding: 4rem 2rem; color: var(--muted); }
  .no-data-icon { font-size: 3.5rem; margin-bottom: 1rem; opacity: 0.4; }
  #pagination button { padding: 0.6rem 0.9rem; border: 2px solid var(--border); border-radius: 8px; background: white; cursor: pointer; font-weight: 600; transition: all 0.3s; color: var(--dark); }
  #pagination button:hover { background-color: var(--primary); color: white; border-color: var(--primary); }
  #pagination button.active { background-color: var(--primary); color: white; border-color: var(--primary); }
  @media (max-width: 1024px) { .payments-header { padding: 2.5rem; } .payments-header-title { font-size: 2.2rem; } .payments-stats { grid-template-columns: repeat(2, 1fr); gap: 1.5rem; } .stat-item { min-height: 200px; padding: 1.75rem; } .stat-value { font-size: 2.2rem; } }
  @media (max-width: 768px) { .payments-wrapper { padding: 1rem; } .payments-header { padding: 1.75rem; } .payments-header-top { flex-direction: column; align-items: flex-start; margin-bottom: 1.5rem; } .payments-header-title { font-size: 1.8rem; } .payments-stats { grid-template-columns: 1fr; } .stat-item { min-height: 180px; padding: 1.5rem; } .stat-value { font-size: 2rem; } .filters-grid { grid-template-columns: 1fr; } th, td { padding: 0.75rem; } .modal-content { width: 95%; padding: 1.5rem; } .payment-details { grid-template-columns: 100px 1fr; gap: 0.75rem 1rem; } .modal-actions { flex-direction: column-reverse; } .btn-modal { width: 100%; } }
  @media (max-width: 480px) { .payments-header { padding: 1.5rem; } .payments-header-title { font-size: 1.5rem; } .payments-stats { gap: 1rem; } .stat-item { min-height: auto; padding: 1.25rem; } .stat-value { font-size: 1.75rem; } .stat-label { font-size: 0.75rem; } .payment-details { grid-template-columns: 1fr; gap: 0.5rem; } .detail-label { font-size: 0.8rem; color: var(--muted); } .detail-value { font-size: 0.9rem; margin-bottom: 0.5rem; } .modal-actions { flex-direction: column-reverse; } .btn-modal { width: 100%; } }
  @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
</style>

<div class="payments-wrapper">
  <div class="payments-header">
    <div class="payments-header-content">
      <div class="payments-header-top">
        <div class="payments-header-title">
          <span class="payments-header-icon">⏰</span>
          <span>Overdue Payments</span>
        </div>
        <div class="payments-date-range">
          <span>📅</span>
          <span id="currentMonth">Loading...</span>
        </div>
      </div>
      <div class="payments-stats">
        <div class="stat-item">
          <div>
            <div class="stat-label"><span class="stat-icon">💵</span>Total Overdue Amount</div>
            <div class="stat-value amount" id="totalAmount">0.00</div>
          </div>
          <div class="stat-change neutral"><span>📊</span><span>Overview</span></div>
        </div>
        <div class="stat-item">
          <div>
            <div class="stat-label"><span class="stat-icon">👤</span>Total Users</div>
            <div class="stat-value" id="totalUsers">0</div>
          </div>
          <div class="stat-change neutral"><span>Records</span></div>
        </div>
      </div>
    </div>
  </div>

  <div class="filters-section">
    <h3 class="filters-title"><span>🔎</span>Filter & Search</h3>
    <div class="filters-grid">
      <div class="filter-group">
        <label class="filter-label">📅 Month</label>
        <input type="month" id="monthFilter" class="filter-input">
      </div>
      <div class="filter-group">
        <label class="filter-label">🔎 Search</label>
        <input type="text" id="searchInput" class="filter-input" placeholder="Name, email, or bill no...">
      </div>
    </div>
    <div style="margin-top: 1rem; display: flex; gap: 0.75rem; flex-wrap: wrap;">
      <button id="searchBtn" class="filter-btn filter-btn-search">🔍 Search</button>
      <button id="resetBtn" class="filter-btn filter-btn-reset">↻ Reset</button>
      <button id="printBtn" class="export-btn">🖨 Print</button>
    </div>
  </div>

  <div id="loadingIndicator" style="display: none; text-align: center; padding: 3rem; background: white; border-radius: 18px; margin-bottom: 2rem;">
    <div style="border: 4px solid #e5e7eb; border-top: 4px solid #667eea; border-radius: 50%; width: 50px; height: 50px; animation: spin 1s linear infinite; margin: 0 auto 1rem;"></div>
    <p style="color: #6b7280;">Loading overdue payments...</p>
  </div>

  <div class="table-container">
    <div class="table-header">
      <div>
        <h3 class="table-header-title">⏰ Overdue Records</h3>
        <div id="tableMonthLabel" style="font-size:0.95rem;color:var(--muted);margin-top:6px;"></div>
      </div>
    </div>
    <div class="table-wrapper">
      <table>
        <thead>
          <tr>
            <th>User Information</th>
            <th>Amount Due</th>
            <th>Billing Month</th>
            <th>Due Date</th>
            <th>Status</th>
            <th>Bill No</th>
          </tr>
        </thead>
        <tbody id="overdueTableBody">
          <tr>
            <td colspan="6" class="no-data">
              <div class="no-data-icon">📭</div>
              <p>Loading data...</p>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  <div id="pagination" style="display:flex; justify-content:center; gap:0.5rem; margin-top:2rem; flex-wrap:wrap;"></div>
</div>

<script>
function initOverduePaymentsPage() {
  const currentMonthDisplay = document.getElementById('currentMonth');
  const overdueTableBody = document.getElementById('overdueTableBody');
  const loadingIndicator = document.getElementById('loadingIndicator');
  let overdueData = [];
  const currentMonth = new Date().toISOString().slice(0,7);
  let currentPage = 1;
  const perPage = 20;

  function showLoading() {
    if (loadingIndicator) loadingIndicator.style.display = 'block';
    if (overdueTableBody) overdueTableBody.innerHTML = '';
  }
  function hideLoading() {
    if (loadingIndicator) loadingIndicator.style.display = 'none';
  }

  function loadOverduePayments(filters = {}) {
    showLoading();
    const month = filters.month || currentMonth;
    const search = filters.search || '';
    const page = filters.page || currentPage;
    const url = `<?= site_url('admin/getOverduePaymentsData') ?>?month=${encodeURIComponent(month)}&billing_month=${encodeURIComponent(month)}&search=${encodeURIComponent(search)}&page=${page}`;
    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(res => res.json())
      .then(data => {
        overdueData = data.payments || [];
        // Ensure client-side we only show records for the selected month (YYYY-MM)
        if (month) {
          overdueData = overdueData.filter(it => {
            if (!it.billing_month) return false;
            // billing_month may be full date 'YYYY-MM-DD' or 'YYYY-MM'
            return it.billing_month.indexOf(month) === 0;
          });
        }
        renderTable(overdueData);
        updateStats(data.stats || {});
        const date = new Date(month + '-01');
        if (currentMonthDisplay) currentMonthDisplay.textContent = date.toLocaleString('en-US', { month: 'long', year: 'numeric' });
        const tableMonthLabel = document.getElementById('tableMonthLabel');
        if (tableMonthLabel) tableMonthLabel.textContent = date.toLocaleString('en-US', { month: 'long', year: 'numeric' });
        renderPagination(data.stats.total_users || 0, page, perPage);
        hideLoading();
      })
      .catch(err => {
        console.error(err);
        hideLoading();
        if (overdueTableBody) overdueTableBody.innerHTML = `<tr><td colspan="6">❌ Failed to load overdue payments. Check console.</td></tr>`;
      });
  }

  function updateStats(stats) {
    document.getElementById('totalUsers').textContent = stats.total_users || 0;
    document.getElementById('totalAmount').textContent = (stats.total_amount || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function renderTable(data) {
    if (!overdueTableBody) return;
    overdueTableBody.innerHTML = '';
    const list = Array.isArray(data) ? data : [];
    if (!list.length) {
      overdueTableBody.innerHTML = `<tr><td colspan="6" class="no-data"><div class="no-data-icon">📋</div><p>No overdue payment records.</p></td></tr>`;
      return;
    }
    list.forEach(item => {
      const row = document.createElement('tr');
      row.innerHTML = `
        <td><div>${item.user_name || '-'}<br>${item.email || '-'}</div></td>
        <td>₱${parseFloat(item.amount_due || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
        <td>${item.billing_month || '-'}</td>
        <td>${item.due_date || '-'}</td>
        <td><span class="badge badge-overdue">⏰ Overdue</span></td>
        <td>${item.bill_no || '-'}</td>
      `;
      overdueTableBody.appendChild(row);
    });
  }

  function renderPagination(totalItems, currentPageLocal = 1, perPageLocal = 20) {
    const paginationEl = document.getElementById('pagination');
    if (!paginationEl) return;
    paginationEl.innerHTML = '';
    const totalPages = Math.ceil(totalItems / perPageLocal);
    if (totalPages <= 1) return;
    for (let i = 1; i <= totalPages; i++) {
      const btn = document.createElement('button');
      btn.textContent = i;
      btn.className = (i === currentPageLocal) ? 'active-page' : '';
      btn.addEventListener('click', () => {
        currentPage = i;
        loadOverduePayments({
          month: document.getElementById('monthFilter').value,
          search: document.getElementById('searchInput').value,
          page: i
        });
      });
      paginationEl.appendChild(btn);
    }
  }

  // Search / filters
  const monthFilterEl = document.getElementById('monthFilter');
  const searchInputEl = document.getElementById('searchInput');
  const searchBtnEl = document.getElementById('searchBtn');
  const resetBtnEl = document.getElementById('resetBtn');

  // Trigger load when month changes
  if (monthFilterEl) {
    monthFilterEl.addEventListener('change', () => {
      currentPage = 1;
      loadOverduePayments({
        month: monthFilterEl.value || currentMonth,
        search: (searchInputEl && searchInputEl.value) || '',
        page: 1
      });
    });
  }

  // Trigger search when pressing Enter in search input
  if (searchInputEl) {
    searchInputEl.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') {
        e.preventDefault();
        currentPage = 1;
        loadOverduePayments({
          month: (monthFilterEl && monthFilterEl.value) || currentMonth,
          search: searchInputEl.value || '',
          page: 1
        });
      }
    });
  }

  // Search button
  if (searchBtnEl) {
    searchBtnEl.addEventListener('click', () => {
      currentPage = 1;
      loadOverduePayments({
        month: (monthFilterEl && monthFilterEl.value) || currentMonth,
        search: (searchInputEl && searchInputEl.value) || '',
        page: 1
      });
    });
  }

  // Reset button
  if (resetBtnEl) {
    resetBtnEl.addEventListener('click', () => {
      if (monthFilterEl) monthFilterEl.value = currentMonth;
      if (searchInputEl) searchInputEl.value = '';
      currentPage = 1;
      loadOverduePayments({
        month: (monthFilterEl && monthFilterEl.value) || currentMonth,
        search: '',
        page: 1
      });
    });
  }
  if (document.getElementById('printBtn')) {
    document.getElementById('printBtn').addEventListener('click', () => {
      const month = document.getElementById('monthFilter').value || currentMonth;
      const search = document.getElementById('searchInput').value || '';
      const indication = `Printing: ${month}${search ? ' | Search: ' + search : ''}`;

      // Clone the table container HTML for printing
      const tableWrapper = document.querySelector('.table-container .table-wrapper');
      const printHtml = tableWrapper ? tableWrapper.innerHTML : document.querySelector('table').outerHTML;

      const win = window.open('', '_blank');
      win.document.write('<!doctype html><html><head><meta charset="utf-8"><title>Print Overdue Payments</title>');
      // Minimal styles to keep table readable in print
      win.document.write('<style>body{font-family:Arial,Helvetica,sans-serif;padding:20px;color:#111}table{width:100%;border-collapse:collapse}th,td{padding:8px;border:1px solid #ddd;text-align:left}th{background:#f3f4f6;font-weight:700;text-transform:uppercase;font-size:12px} .indication{margin-bottom:12px;font-weight:700;}</style>');
      win.document.write('</head><body>');
      win.document.write('<div class="indication">' + indication + '</div>');
      win.document.write(printHtml);
      win.document.write('</body></html>');
      win.document.close();
      win.focus();
      // Give the new window a moment to render then print
      setTimeout(() => { try { win.print(); win.close(); } catch (e) { console.error(e); } }, 500);
    });
  }

  // initial load
  document.getElementById('monthFilter').value = currentMonth;
  loadOverduePayments();
}

if (typeof window !== 'undefined') {
  setTimeout(() => {
    if (typeof initOverduePaymentsPage === 'function') initOverduePaymentsPage();
  }, 20);
}
</script>