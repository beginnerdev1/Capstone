    
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
  /* Force a stable 2x2 layout like the dashboard: 0 \t\t 0 \n 0 \t\t 0 where 0 = stat-item */
  .payments-stats { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1.5rem; }
  .stat-item { background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(20px); padding: 1.5rem; border-radius: 12px; border: 2px solid rgba(255, 255, 255, 0.18); transition: all 0.18s ease; position: relative; overflow: hidden; min-height: 110px; display: flex; flex-direction: column; justify-content: space-between; }
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
  /* tables: more card-like spacing to match dashboard stat-cards */
  table { width: 100%; border-collapse: separate; border-spacing: 0 10px; }
  th { padding: 0.9rem 1.25rem; text-align: left; font-weight: 700; color: var(--dark); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid var(--border); background: transparent; }
  td { padding: 1rem 1.25rem; font-size: 0.95rem; vertical-align: middle; background: #ffffff; }

  /* container styling to resemble stat-cards */
  .table-container { padding: 1rem; border-radius: 12px; box-shadow: 0 12px 30px rgba(15, 23, 42, 0.04); border: 1px solid #e5e7eb; background: white; overflow: hidden; margin-bottom: 1.5rem; }
  .table-header { padding: 1rem 1.25rem; }
  .table-wrapper { padding: 0.5rem 1rem 1rem 1rem; }

  tbody tr td:first-child { border-top-left-radius: 8px; border-bottom-left-radius: 8px; }
  tbody tr td:last-child { border-top-right-radius: 8px; border-bottom-right-radius: 8px; }
  tbody tr { transition: transform 0.18s ease, box-shadow 0.18s ease; }
  tbody tr:hover td { box-shadow: 0 8px 20px rgba(15, 23, 42, 0.04); transform: translateY(-4px); }
  .user-info { display: flex; align-items: center; gap: 0.75rem; }
  .user-avatar { width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.9rem; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3); }
  .user-details h4 { margin: 0; font-weight: 700; color: var(--dark); font-size: 0.95rem; }
  .user-details p { margin: 0.3rem 0 0; color: var(--muted); font-size: 0.85rem; }
  .badge { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.2rem; border-radius: 20px; font-size: 0.85rem; font-weight: 700; white-space: nowrap; margin: 0.25rem; box-shadow: 0 2px 6px rgba(0,0,0,0.05); }
  .badge-gateway { background: rgba(102, 126, 234, 0.2); color: #4f46e5; border: 1.5px solid rgba(102, 126, 234, 0.4); }
  .badge-gcash { background: rgba(16, 185, 129, 0.2); color: #059669; border: 1.5px solid rgba(16, 185, 129, 0.4); }
  .badge-overcounter { background: rgba(245, 158, 11, 0.25); color: #92400e; border: 1.5px solid rgba(245, 158, 11, 0.4); box-shadow: 0 2px 4px rgba(245, 158, 11, 0.1); }
  .badge-pending { background: rgba(245, 158, 11, 0.25); color: #92400e; border: 1.5px solid rgba(245, 158, 11, 0.4); }
  .badge-confirmed { background: rgba(16, 185, 129, 0.2); color: #059669; border: 1.5px solid rgba(16, 185, 129, 0.4); }
  .badge-paid { background: rgba(16, 185, 129, 0.2); color: #059669; border: 1.5px solid rgba(16, 185, 129, 0.4); }
  .badge-rejected { background: rgba(239, 68, 68, 0.12); color: #b91c1c; border: 1.5px solid rgba(239,68,68,0.18); }
  .amount { font-weight: 700; color: var(--dark); font-size: 0.95rem; }
  .no-data { text-align: center; padding: 4rem 2rem; color: var(--muted); }
  .no-data-icon { font-size: 3.5rem; margin-bottom: 1rem; opacity: 0.4; }
  .btn-confirm { padding: 0.5rem 1rem; background: linear-gradient(135deg, var(--success) 0%, #0d9488 100%); color: white; border: none; border-radius: 8px; font-size: 0.85rem; font-weight: 700; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3); text-align: center; display: inline-block; min-width: 120px; }
  .btn-confirm:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4); }
  .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(31, 41, 55, 0.7); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); }
  .modal.active { display: flex; align-items: center; justify-content: center; animation: modalFadeIn 0.3s ease; }
  @keyframes modalFadeIn { from { opacity: 0; } to { opacity: 1; } }
  @keyframes modalSlideIn { from { transform: translateY(-50px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
  .modal-content { background: white; border-radius: 20px; padding: 1.75rem 2rem; max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto; box-shadow: 0 25px 60px rgba(0, 0, 0, 0.3); animation: modalSlideIn 0.3s cubic-bezier(0.25, 0.8, 0.25, 1); }
  .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 2px solid var(--border); }
  .modal-title { font-size: 1.5rem; font-weight: 700; color: var(--dark); display: flex; align-items: center; gap: 0.75rem; }
  .modal-close { background: var(--light); border: 2px solid var(--border); font-size: 1.5rem; cursor: pointer; color: var(--muted); transition: all 0.3s ease; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; line-height: 1; }
  .modal-close:hover { background: rgba(239, 68, 68, 0.1); border-color: rgba(239, 68, 68, 0.3); color: var(--danger); transform: rotate(90deg); }
  .modal-body { padding-top: 1rem; }
  .payment-details { background: var(--light); border: 1px solid var(--border); padding: 1.25rem; border-radius: 12px; display: grid; grid-template-columns: 140px 1fr; gap: 1rem; margin-bottom: 1.5rem; }
  .detail-row { display: contents; }
  .detail-label { font-weight: 600; color: var(--muted); font-size: 0.85rem; align-self: center; }
  .detail-value { font-weight: 700; color: var(--dark); font-size: 0.9rem; align-self: center; word-break: break-word; }
  .detail-value.amount { font-size: 1.1rem; color: var(--success); font-weight: 800; }
  .detail-value.amount::before { content: '₱'; margin-right: 2px; }
  .receipt-section { margin-bottom: 1.5rem; }
  .receipt-section h3 { font-weight: 700; color: var(--dark); margin-bottom: 0.75rem; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px; }
  .receipt-container { position: relative; }
  #modalReceipt { width: 100%; border-radius: 12px; border: 2px solid var(--border); cursor: pointer; transition: all 0.3s ease; display: block; }
  #modalReceipt:hover { transform: scale(1.02); box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15); border-color: var(--primary); }
  .receipt-hint { font-size: 0.8rem; color: var(--muted); text-align: center; margin-top: 0.75rem; }
  .form-group { margin-bottom: 1.5rem; }
  .form-label { display: block; font-weight: 700; color: var(--dark); margin-bottom: 0.5rem; font-size: 0.9rem; }
  .form-input { width: 100%; padding: 0.85rem; border: 2px solid var(--border); border-radius: 10px; font-size: 0.9rem; font-family: 'Poppins', sans-serif; background: var(--light); transition: all 0.3s ease; }
  .form-input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1); background: white; }
  .modal-actions { display: flex; gap: 1rem; margin-top: 2rem; }
  .btn-modal { flex: 1; padding: 0.85rem 1.5rem; border: none; border-radius: 10px; font-weight: 700; cursor: pointer; transition: all 0.3s ease; font-size: 0.9rem; }
  .btn-cancel { background: white; color: var(--dark); border: 2px solid var(--border); }
  .btn-cancel:hover { background: var(--light); border-color: var(--danger); color: var(--danger); }
  #pagination button { padding: 0.6rem 0.9rem; border: 2px solid var(--border); border-radius: 8px; background: white; cursor: pointer; font-weight: 600; transition: all 0.3s; color: var(--dark); }
  #pagination button:hover { background-color: var(--primary); color: white; border-color: var(--primary); }
  #pagination button.active { background-color: var(--primary); color: white; border-color: var(--primary); }
  @media (max-width: 1024px) { .payments-header { padding: 2.5rem; } .payments-header-title { font-size: 2.2rem; } .payments-stats { grid-template-columns: repeat(2, 1fr); gap: 1.5rem; } .stat-item { min-height: 200px; padding: 1.75rem; } .stat-value { font-size: 2.2rem; } }
  @media (max-width: 768px) { .payments-wrapper { padding: 1rem; } .payments-header { padding: 1.75rem; } .payments-header-top { flex-direction: column; align-items: flex-start; margin-bottom: 1.5rem; } .payments-header-title { font-size: 1.8rem; } .payments-stats { grid-template-columns: 1fr; } .stat-item { min-height: 120px; padding: 1.25rem; } .stat-value { font-size: 2rem; } .filters-grid { grid-template-columns: 1fr; } th, td { padding: 0.75rem; } .modal-content { width: 95%; padding: 1.5rem; } .payment-details { grid-template-columns: 100px 1fr; gap: 0.75rem 1rem; } .modal-actions { flex-direction: column-reverse; } .btn-modal { width: 100%; } }
  @media (max-width: 480px) { .payments-header { padding: 1.5rem; } .payments-header-title { font-size: 1.5rem; } .payments-stats { gap: 1rem; } .stat-item { min-height: auto; padding: 1.25rem; } .stat-value { font-size: 1.75rem; } .stat-label { font-size: 0.75rem; } .payment-details { grid-template-columns: 1fr; gap: 0.5rem; } .detail-label { font-size: 0.8rem; color: var(--muted); } .detail-value { font-size: 0.9rem; margin-bottom: 0.5rem; } .modal-actions { flex-direction: column-reverse; } .btn-modal { width: 100%; } }
  @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
</style>

<div class="payments-wrapper">
  <div class="payments-header">
    <div class="payments-header-content">
      <div class="payments-header-top">
        <div class="payments-header-title">
          <span class="payments-header-icon">⚠️</span>
          <span>Failed & Rejected Transactions</span>
        </div>
        <div class="payments-date-range">
          <span>📅</span>
          <span id="currentMonth">Loading...</span>
        </div>
      </div>

      <div class="payments-stats">
        <div class="stat-item">
          <div>
            <div class="stat-label">
              <span class="stat-icon">💵</span>
              Total Failed Amount
            </div>
            <div class="stat-value amount" id="totalAmount">0.00</div>
          </div>
          <div class="stat-change neutral">
            <span>📊</span>
            <span>Failure Overview</span>
          </div>
        </div>

        <div class="stat-item">
          <div>
            <div class="stat-label">
              <span class="stat-icon">📈</span>
              Total Failed Count
            </div>
            <div class="stat-value" id="totalUsers">0</div>
          </div>
          <div class="stat-change neutral">
            <span>📋</span>
            <span>Records</span>
          </div>
        </div>

        <div class="stat-item">
          <div>
            <div class="stat-label">
              <span class="stat-icon">📱</span>
              Manual GCash Failures
            </div>
            <div class="stat-value" id="gcashCount">0</div>
          </div>
          <div class="stat-change neutral">
            <span>Method</span>
          </div>
        </div>

        <div class="stat-item">
          <div>
            <div class="stat-label">
              <span class="stat-icon">💳</span>
              Gateway Failures
            </div>
            <div class="stat-value" id="gatewayCount">0</div>
          </div>
          <div class="stat-change neutral">
            <span>Method</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="filters-section">
    <h3 class="filters-title">
      <span>🔎</span>
      Filter & Search
    </h3>
    <div class="filters-grid">
      <div class="filter-group">
        <label class="filter-label">📅 Month</label>
        <input type="month" id="monthFilter" class="filter-input">
      </div>
      <div class="filter-group">
        <label class="filter-label">💳 Payment Method</label>
        <select id="methodFilter" class="filter-select">
          <option value="">All Methods</option>
          <option value="manual">Manual GCash</option>
          <option value="gateway">Payment Gateway</option>
        </select>
      </div>
      <div class="filter-group">
        <label class="filter-label">🔎 Search</label>
        <input type="text" id="searchInput" class="filter-input" placeholder="Name, email, or reference...">
      </div>
    </div>
    <div style="margin-top: 1rem; display: flex; gap: 0.75rem; flex-wrap: wrap;">
      <button id="searchBtn" class="filter-btn filter-btn-search">🔍 Search</button>
      <button id="resetBtn" class="filter-btn filter-btn-reset">↻ Reset</button>
    </div>
  </div>

  <div id="loadingIndicator" style="display: none; text-align: center; padding: 3rem; background: white; border-radius: 18px; margin-bottom: 2rem;">
    <div style="border: 4px solid #e5e7eb; border-top: 4px solid #667eea; border-radius: 50%; width: 50px; height: 50px; animation: spin 1s linear infinite; margin: 0 auto 1rem;"></div>
    <p style="color: #6b7280;">Loading failed/rejected payments...</p>
  </div>

  <div class="table-container">
    <div class="table-header">
      <h3 class="table-header-title">⚠️ Failed & Rejected Records</h3>
      <div style="display:flex; gap:0.75rem;">
        <button onclick="printPaymentReport()" class="export-btn" style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); box-shadow: 0 4px 12px rgba(6, 182, 212, 0.3);">🖨️ Print</button>
      </div>
    </div>

    <div class="table-wrapper">
      <table>
        <thead>
          <tr>
            <th>User Information</th>
            <th>Amount</th>
            <th>Payment Method</th>
            <th>Status</th>
            <th>Reference Number</th>
          </tr>
        </thead>
        <tbody id="paymentTableBody">
          <tr>
            <td colspan="5" class="no-data">
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

<!-- DETAILS MODAL (view-only) -->
<div id="detailsModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h2 class="modal-title"><span>🔍</span> Transaction Details</h2>
      <button class="modal-close" onclick="closeDetailsModal()">&times;</button>
    </div>
    <div class="modal-body">
      <div class="payment-details">
        <div class="detail-row">
          <div class="detail-label">User Name</div>
          <div class="detail-value" id="modalUserName">-</div>
        </div>
        <div class="detail-row">
          <div class="detail-label">Email</div>
          <div class="detail-value" id="modalUserEmail">-</div>
        </div>
        <div class="detail-row">
          <div class="detail-label">Amount</div>
          <div class="detail-value amount" id="modalAmount">0.00</div>
        </div>
        <div class="detail-row">
          <div class="detail-label">Reference</div>
          <div class="detail-value" id="modalUserRef">-</div>
        </div>
        <div class="detail-row">
          <div class="detail-label">Status</div>
          <div class="detail-value" id="modalStatus">-</div>
        </div>
        <div class="detail-row">
          <div class="detail-label">Date</div>
          <div class="detail-value" id="modalDate">-</div>
        </div>
      </div>

      <div class="receipt-section">
        <h3>Receipt Image</h3>
        <div class="receipt-container">
          <img id="modalReceipt" src="" alt="Receipt" style="display: none;">
          <p class="receipt-hint">Click to enlarge</p>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Rejection Reason</label>
        <div id="modalRejectReason" style="color: #6b7280;">-</div>
      </div>
    </div>

    <div class="modal-actions">
      <button class="btn-modal btn-cancel" onclick="closeDetailsModal()">Close</button>
    </div>
  </div>
</div>

<!-- RECEIPT LIGHTBOX -->
<div id="receiptModal" class="modal" style="background: rgba(0, 0, 0, 0.95);">
  <div class="modal-content" style="background: transparent; box-shadow: none; max-width: 90vw; padding: 0;">
    <button class="modal-close" onclick="closeReceiptModal()" style="position: absolute; top: 20px; right: 20px; background: rgba(255,255,255,0.2); z-index:1001; border-color: rgba(255,255,255,0.3);">&times;</button>
    <img id="receiptImage" src="" alt="Receipt Full View" style="width: 100%; height: auto; max-height: 85vh; object-fit: contain; border-radius: 8px;">
  </div>
</div>

<script>
function initFailedTransactionsPage() {
  const currentMonthDisplay = document.getElementById('currentMonth');
  const paymentTableBody = document.getElementById('paymentTableBody');
  const loadingIndicator = document.getElementById('loadingIndicator');
  const detailsModal = document.getElementById('detailsModal');
  const receiptModal = document.getElementById('receiptModal');
  const receiptImage = document.getElementById('receiptImage');
  const modalReceipt = document.getElementById('modalReceipt');

  let paymentsData = [];
  const currentMonth = new Date().toISOString().slice(0,7);
  let currentPage = 1;
  const perPage = 20;

  function showLoading() {
    if (loadingIndicator) loadingIndicator.style.display = 'block';
    if (paymentTableBody) paymentTableBody.innerHTML = '';
  }
  function hideLoading() {
    if (loadingIndicator) loadingIndicator.style.display = 'none';
  }

  function loadPayments(filters = {}) {
    showLoading();
    const month = filters.month || currentMonth;
    const method = filters.method || '';
    const search = filters.search || '';
    const page = filters.page || currentPage;

    const url = `<?= site_url('admin/getFailedPaymentsData') ?>?month=${month}&method=${encodeURIComponent(method)}&search=${encodeURIComponent(search)}&page=${page}`;

    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          window.failedPaymentsData = data.payments || [];
          paymentsData = data.payments || [];
          renderTable(paymentsData);
          updateStats(data.stats || {});
          const date = new Date(month + '-01');
          if (currentMonthDisplay) currentMonthDisplay.textContent = date.toLocaleString('en-US', { month: 'long', year: 'numeric' });

          // prefer pagination.total, fallback to stats.total_users
          const totalItems = (data.pagination && data.pagination.total)
            ? data.pagination.total
            : (data.stats && data.stats.total_users ? data.stats.total_users : 0);

          renderPagination(totalItems, page, perPage);
        } else {
          alert('Error: ' + (data.message || 'Failed to load payments'));
        }
        hideLoading();
      })
      .catch(err => {
        console.error(err);
        hideLoading();
        if (paymentTableBody) paymentTableBody.innerHTML = `<tr><td colspan="5">❌ Failed to load payments. Check console.</td></tr>`;
      });
  }

  function updateStats(stats) {
    document.getElementById('totalUsers').textContent = stats.total_users || 0;
    document.getElementById('totalAmount').textContent = (stats.total_amount || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('gatewayCount').textContent = stats.gateway || 0;
    document.getElementById('gcashCount').textContent = stats.gcash || 0;
  }

  function createBadge(type, value, rawMethod = null) {
    let className = '', icon = '';
    if (type === 'method') {
      const methodValue = (rawMethod || '').toLowerCase().trim();
      if (methodValue === 'gateway') { className = 'badge-gateway'; icon = '💳'; value = 'Payment Gateway'; }
      else if (methodValue === 'offline') { className = 'badge-overcounter'; icon = '🏪'; value = 'Over the Counter'; }
      else if (methodValue === 'manual') { className = 'badge-gcash'; icon = '📱'; value = 'Manual GCash'; }
      else { className = 'badge-gateway'; icon = '❓'; }
    } else if (type === 'status') {
      const lower = (value || '').toLowerCase().trim();
      if (lower === 'pending') { className = 'badge-pending'; icon = '⏳'; }
      else if (lower === 'confirmed') { className = 'badge-confirmed'; icon = '✅'; }
      else if (lower === 'paid') { className = 'badge-paid'; icon = '✅'; }
      else if (lower === 'rejected' || lower === 'failed') { className = 'badge-rejected'; icon = '⚠️'; }
    }
    return `<span class="badge ${className}">${icon} ${value}</span>`;
  }

  function mapMethod(method) {
    if (!method) return 'Unknown';
    const lower = method.toLowerCase().trim();
    if (lower === 'manual') return 'Manual GCash';
    if (lower === 'offline' || lower === 'over the counter') return 'Over the Counter';
    if (lower === 'gateway') return 'Payment Gateway';
    return method;
  }

  function renderTable(data) {
    if (!paymentTableBody) return;
    paymentTableBody.innerHTML = '';
    const list = Array.isArray(data) ? data : [];

    // Filter only failed/rejected, cancelled-but-already-expired, or already-expired items
    const filtered = list.filter(item => {
      const s = (item.status || '').toLowerCase().trim();
      if (s === 'rejected' || s === 'failed') return true;

      // Cancelled and expired
      if (s === 'cancelled') {
        if (item.expires_at) {
          const exp = new Date(item.expires_at);
          if (!isNaN(exp.getTime()) && exp.getTime() <= Date.now()) return true;
        }
      }

      // Any payment that has already expired (expires_at in the past) should also be shown
      if (item.expires_at) {
        const exp2 = new Date(item.expires_at);
        if (!isNaN(exp2.getTime()) && exp2.getTime() <= Date.now()) {
          // avoid showing paid records as failed
          if ((s || '').toLowerCase().trim() !== 'paid') return true;
        }
      }

      return false;
    });

    if (!filtered.length) {
      paymentTableBody.innerHTML = `<tr><td colspan="5" class="no-data"><div class="no-data-icon">📋</div><p>No failed or rejected payment records.</p></td></tr>`;
      return;
    }

    filtered.forEach(item => {
      const methodLabel = mapMethod(item.method);
      const statusLabel = item.status || 'Unknown';

      const row = document.createElement('tr');
      row.innerHTML = `
        <td><div>${item.user_name || '-'}<br>${item.email || '-'}</div></td>
        <td>₱${parseFloat(item.amount || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
        <td>${createBadge('method', methodLabel, item.method)}</td>
        <td>${createBadge('status', statusLabel)}</td>
        <td>${item.reference_number || '-'}${item.admin_reference ? '<br><small>Admin: ' + item.admin_reference + '</small>' : ''}</td>
      `;
      paymentTableBody.appendChild(row);
    });

    // No actions column anymore; details modal remains accessible via other UI where needed.
  }

  // Open details modal
  window.openDetailsModal = function(id) {
    const list = window.failedPaymentsData || [];
    const item = list.find(p => p.id == id);
    if (!item) return;

    document.getElementById('modalUserName').textContent = item.user_name || '-';
    document.getElementById('modalUserEmail').textContent = item.email || '-';
    document.getElementById('modalAmount').textContent = `₱${parseFloat(item.amount || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    document.getElementById('modalUserRef').textContent = item.reference_number || '-';
    document.getElementById('modalStatus').textContent = item.status || '-';
    document.getElementById('modalDate').textContent = item.paid_at || item.created_at || '-';
    document.getElementById('modalRejectReason').textContent = item.reject_reason || item.failure_reason || '-';

    const modalReceiptEl = document.getElementById('modalReceipt');
    if (modalReceiptEl) {
      if (item.receipt_image) {
        modalReceiptEl.src = item.receipt_image;
        modalReceiptEl.dataset.fullSrc = item.receipt_image;
        modalReceiptEl.style.display = 'block';
      } else {
        modalReceiptEl.src = '';
        modalReceiptEl.dataset.fullSrc = '';
        modalReceiptEl.style.display = 'none';
      }
    }

    detailsModal.classList.add('active');
  };

  window.closeDetailsModal = function() {
    detailsModal.classList.remove('active');
    const modalReceiptEl = document.getElementById('modalReceipt');
    if (modalReceiptEl) {
      modalReceiptEl.src = '';
      modalReceiptEl.dataset.fullSrc = '';
      modalReceiptEl.style.display = 'none';
    }
  };

  // receipt lightbox
  if (modalReceipt) {
    modalReceipt.addEventListener('click', e => {
      const src = e.target.dataset.fullSrc;
      if (!src) return;
      receiptImage.src = src;
      receiptModal.classList.add('active');
    });
  }

  window.closeReceiptModal = function() {
    receiptModal.classList.remove('active');
    receiptImage.src = '';
  };

  if (receiptModal) {
    receiptModal.addEventListener('click', e => {
      if (e.target === receiptModal) closeReceiptModal();
    });
  }

  // Search / filters
  if (document.getElementById('searchBtn')) {
    document.getElementById('searchBtn').addEventListener('click', () => {
      currentPage = 1;
      loadPayments({
        month: document.getElementById('monthFilter').value,
        method: document.getElementById('methodFilter').value,
        search: document.getElementById('searchInput').value,
        page: 1
      });
    });
  }
  if (document.getElementById('resetBtn')) {
    document.getElementById('resetBtn').addEventListener('click', () => {
      document.getElementById('monthFilter').value = currentMonth;
      document.getElementById('methodFilter').value = '';
      document.getElementById('searchInput').value = '';
      currentPage = 1;
      loadPayments({ page: 1 });
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
        loadPayments({
          month: document.getElementById('monthFilter').value,
          method: document.getElementById('methodFilter').value,
          search: document.getElementById('searchInput').value,
          page: i
        });
      });
      paginationEl.appendChild(btn);
    }
  }

  // Print functions reused from main page
  window.printReceipt = function(transactionId) {
    const list = window.failedPaymentsData || [];
    const transaction = list.find(p => p.id == transactionId);
    if (!transaction) {
      alert('Transaction not found.');
      return;
    }
    const receiptContent = `
      <div class="receipt-print">
        <h2>Receipt</h2>
        <p><strong>User:</strong> ${transaction.user_name || 'N/A'}</p>
        <p><strong>Email:</strong> ${transaction.email || 'N/A'}</p>
        <p><strong>Amount:</strong> ₱${parseFloat(transaction.amount || 0).toLocaleString('en-US', { minimumFractionDigits: 2 })}</p>
        <p><strong>Payment Method:</strong> ${mapMethod(transaction.method)}</p>
        <p><strong>Reference Number:</strong> ${transaction.reference_number || '-'}</p>
        <p><strong>Date:</strong> ${transaction.paid_at || transaction.created_at || '-'}</p>
      </div>
      <style>
        body { margin: 0; padding: 0; }
        .receipt-print { width: 350px; margin: 40px auto; font-size: 16px; padding: 20px; border: 1px solid #e5e7eb; border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.08); background: #fff; }
        .receipt-print h2 { font-size: 22px; margin-bottom: 18px; }
        .receipt-print p { margin: 8px 0; }
      </style>
    `;
    const printWindow = window.open('', '', 'width=400,height=500');
    printWindow.document.write('<html><head><title>Receipt</title></head><body>');
    printWindow.document.write(receiptContent);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    setTimeout(() => { printWindow.focus(); printWindow.print(); printWindow.close(); }, 100);
  };

  // initial load
  document.getElementById('monthFilter').value = currentMonth;
  loadPayments();
}

// Auto-run when the partial is loaded via AJAX
if (typeof window !== 'undefined') {
  setTimeout(() => {
    if (typeof initFailedTransactionsPage === 'function') initFailedTransactionsPage();
  }, 20);
}
</script>