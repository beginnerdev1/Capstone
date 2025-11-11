<style>
:root {
  --primary: #667eea;
  --primary-dark: #5568d3;
  --primary-light: #8b9eff;
  --success: #10b981;
  --warning: #f59e0b;
  --danger: #ef4444;
  --info: #06b6d4;
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

.payments-wrapper {
  padding: 2rem;
  max-width: 1400px;
  margin: 0 auto;
}

.payments-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 3rem;
  border-radius: 24px;
  margin-bottom: 3rem;
  box-shadow: 0 25px 60px rgba(102, 126, 234, 0.35);
  position: relative;
  overflow: hidden;
}

/* Animated background pattern */
.payments-header::before {
  content: '';
  position: absolute;
  top: -50%;
  right: -10%;
  width: 500px;
  height: 500px;
  background: radial-gradient(circle, rgba(255, 255, 255, 0.08) 0%, transparent 70%);
  border-radius: 50%;
}

.payments-header::after {
  content: '';
  position: absolute;
  bottom: -10%;
  left: 5%;
  width: 300px;
  height: 300px;
  background: radial-gradient(circle, rgba(255, 255, 255, 0.05) 0%, transparent 70%);
  border-radius: 50%;
}

.payments-header-content {
  position: relative;
  z-index: 1;
}

.payments-header-top {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2.5rem;
  flex-wrap: wrap;
  gap: 2rem;
}

.payments-header-title {
  font-size: 3rem;
  font-weight: 900;
  letter-spacing: -2px;
  display: flex;
  align-items: center;
  gap: 1rem;
}

.payments-header-icon {
  font-size: 3.5rem;
  animation: bounce 2s ease-in-out infinite;
}

@keyframes bounce {
  0%, 100% { transform: translateY(0) rotate(0deg); }
  50% { transform: translateY(-15px) rotate(5deg); }
}

.payments-date-range {
  background: rgba(255, 255, 255, 0.2);
  backdrop-filter: blur(20px);
  padding: 1rem 1.75rem;
  border-radius: 16px;
  font-size: 1.1rem;
  font-weight: 700;
  border: 2px solid rgba(255, 255, 255, 0.3);
  box-shadow: 0 8px 32px rgba(31, 38, 135, 0.2);
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.payments-stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 1.75rem;
}

.stat-item {
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(20px);
  padding: 2rem;
  border-radius: 18px;
  border: 2px solid rgba(255, 255, 255, 0.25);
  transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
  position: relative;
  overflow: hidden;
  min-height: 220px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.stat-item::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.15), transparent);
  transition: left 0.7s ease;
}

.stat-item:hover {
  background: rgba(255, 255, 255, 0.25);
  transform: translateY(-12px);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.2);
  border-color: rgba(255, 255, 255, 0.4);
}

.stat-item:hover::before {
  left: 100%;
}

.stat-label {
  font-size: 0.8rem;
  opacity: 0.85;
  margin-bottom: 1rem;
  text-transform: uppercase;
  letter-spacing: 1.5px;
  font-weight: 700;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  color: rgba(255, 255, 255, 0.9);
}

.stat-icon {
  font-size: 1.4rem;
  filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
}

.stat-value {
  font-size: 2.8rem;
  font-weight: 900;
  letter-spacing: -1.5px;
  line-height: 1;
  margin: 0.5rem 0;
  display: flex;
  align-items: baseline;
  gap: 0.75rem;
  color: white;
}

.stat-value.amount {
  font-size: 2.5rem;
}

.stat-value.amount::before {
  content: '₱';
  font-size: 2rem;
  opacity: 0.95;
  font-weight: 800;
}

.stat-change {
  font-size: 0.9rem;
  margin-top: 0.75rem;
  opacity: 0.9;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 600;
}

.stat-change.positive {
  color: #7ee8b7;
}

.stat-change.neutral {
  color: rgba(255, 255, 255, 0.75);
}

/* Rest of the styles */
.filters-section {
  background: white;
  padding: 1.75rem;
  border-radius: 18px;
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
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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
  box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
  background: white;
}

.filter-buttons {
  display: flex;
  gap: 0.75rem;
  align-items: flex-end;
}

.filter-btn {
  padding: 0.85rem 1.5rem;
  border: none;
  border-radius: 10px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 0.9rem;
}

.filter-btn-search {
  background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
  color: white;
  flex: 1;
  box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.filter-btn-search:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

.filter-btn-reset {
  background: white;
  color: var(--dark);
  border: 2px solid var(--border);
}

.filter-btn-reset:hover {
  background: var(--light);
  border-color: var(--primary);
}

.table-container {
  background: white;
  border-radius: 18px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
  border: 1px solid var(--border);
  overflow: hidden;
}

.table-header {
  padding: 1.75rem;
  background: linear-gradient(135deg, var(--light) 0%, white 100%);
  border-bottom: 2px solid var(--border);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.table-header-title {
  font-weight: 700;
  color: var(--dark);
  font-size: 1.1rem;
}

.export-btn {
  padding: 0.7rem 1.5rem;
  background: linear-gradient(135deg, var(--success) 0%, #0d9488 100%);
  color: white;
  border: none;
  border-radius: 10px;
  font-size: 0.9rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.export-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
}

.table-wrapper {
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
}

th {
  padding: 1rem 1.5rem;
  text-align: left;
  font-weight: 700;
  color: var(--dark);
  font-size: 0.85rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  border-bottom: 2px solid var(--border);
  background: var(--light);
}

td {
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid var(--border);
  font-size: 0.9rem;
}

tbody tr {
  transition: all 0.2s ease;
}

tbody tr:hover {
  background: rgba(102, 126, 234, 0.03);
}

.user-info {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.user-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 0.9rem;
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.user-details h4 {
  margin: 0;
  font-weight: 700;
  color: var(--dark);
  font-size: 0.95rem;
}

.user-details p {
  margin: 0.3rem 0 0;
  color: var(--muted);
  font-size: 0.85rem;
}

.badge {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 700;
  white-space: nowrap;
}

.badge-gateway {
  background: rgba(102, 126, 234, 0.15);
  color: var(--primary);
  border: 1.5px solid rgba(102, 126, 234, 0.3);
}

.badge-gcash {
  background: rgba(16, 185, 129, 0.15);
  color: var(--success);
  border: 1.5px solid rgba(16, 185, 129, 0.3);
}

.badge-counter {
  background: rgba(245, 158, 11, 0.15);
  color: var(--warning);
  border: 1.5px solid rgba(245, 158, 11, 0.3);
}

.badge-pending {
  background: rgba(245, 158, 11, 0.15);
  color: var(--warning);
  border: 1.5px solid rgba(245, 158, 11, 0.3);
}

.badge-confirmed {
  background: rgba(16, 185, 129, 0.15);
  color: var(--success);
  border: 1.5px solid rgba(16, 185, 129, 0.3);
}

.amount {
  font-weight: 700;
  color: var(--dark);
  font-size: 0.95rem;
}

.no-data {
  text-align: center;
  padding: 4rem 2rem;
  color: var(--muted);
}

.no-data-icon {
  font-size: 3.5rem;
  margin-bottom: 1rem;
  opacity: 0.4;
}

.btn-confirm {
  padding: 0.5rem 1rem;
  background: linear-gradient(135deg, var(--success) 0%, #0d9488 100%);
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 0.85rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.btn-confirm:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
}

/* Modal Styles */
.modal {
  display: none;
  position: fixed;
  z-index: 1000;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.7);
  backdrop-filter: blur(5px);
}

.modal.active {
  display: flex;
  align-items: center;
  justify-content: center;
}

.modal-content {
  background: white;
  border-radius: 20px;
  padding: 2rem;
  max-width: 600px;
  width: 90%;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 25px 60px rgba(0, 0, 0, 0.3);
  animation: modalSlideIn 0.3s ease;
}

@keyframes modalSlideIn {
  from {
    transform: translateY(-50px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 2px solid var(--border);
}

.modal-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--dark);
}

.modal-close {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  color: var(--muted);
  transition: all 0.3s ease;
  width: 35px;
  height: 35px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.modal-close:hover {
  background: var(--light);
  color: var(--danger);
}

.modal-section {
  margin-bottom: 1.5rem;
}

.modal-section-title {
  font-weight: 700;
  color: var(--dark);
  margin-bottom: 0.75rem;
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.info-grid {
  display: grid;
  grid-template-columns: 140px 1fr;
  gap: 0.75rem;
  background: var(--light);
  padding: 1rem;
  border-radius: 12px;
}

.info-label {
  font-weight: 600;
  color: var(--muted);
  font-size: 0.85rem;
}

.info-value {
  font-weight: 700;
  color: var(--dark);
  font-size: 0.9rem;
}

.receipt-preview {
  width: 100%;
  border-radius: 12px;
  border: 2px solid var(--border);
  cursor: pointer;
  transition: all 0.3s ease;
}

.receipt-preview:hover {
  transform: scale(1.02);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-label {
  display: block;
  font-weight: 700;
  color: var(--dark);
  margin-bottom: 0.5rem;
  font-size: 0.9rem;
}

.form-input {
  width: 100%;
  padding: 0.85rem;
  border: 2px solid var(--border);
  border-radius: 10px;
  font-size: 0.9rem;
  font-family: 'Poppins', sans-serif;
  background: var(--light);
  transition: all 0.3s ease;
}

.form-input:focus {
  outline: none;
  border-color: var(--primary);
  box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
  background: white;
}

.modal-actions {
  display: flex;
  gap: 1rem;
  margin-top: 2rem;
}

.btn-modal {
  flex: 1;
  padding: 0.85rem 1.5rem;
  border: none;
  border-radius: 10px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 0.9rem;
}

.btn-cancel {
  background: white;
  color: var(--dark);
  border: 2px solid var(--border);
}

.btn-cancel:hover {
  background: var(--light);
  border-color: var(--danger);
  color: var(--danger);
}

.btn-submit {
  background: linear-gradient(135deg, var(--success) 0%, #0d9488 100%);
  color: white;
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.btn-submit:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
}

@media (max-width: 1024px) {
  .payments-header {
    padding: 2.5rem;
  }

  .payments-header-title {
    font-size: 2.2rem;
  }

  .payments-stats {
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
  }

  .stat-item {
    min-height: 200px;
    padding: 1.75rem;
  }

  .stat-value {
    font-size: 2.2rem;
  }
}

@media (max-width: 768px) {
  .payments-wrapper {
    padding: 1rem;
  }

  .payments-header {
    padding: 1.75rem;
  }

  .payments-header-top {
    flex-direction: column;
    align-items: flex-start;
    margin-bottom: 1.5rem;
  }

  .payments-header-title {
    font-size: 1.8rem;
  }

  .payments-stats {
    grid-template-columns: 1fr;
  }

  .stat-item {
    min-height: 180px;
    padding: 1.5rem;
  }

  .stat-value {
    font-size: 2rem;
  }

  .filters-grid {
    grid-template-columns: 1fr;
  }

  .filter-buttons {
    flex-direction: column-reverse;
  }

  .filter-btn {
    width: 100%;
  }

  th, td {
    padding: 0.75rem;
  }

  .modal-content {
    width: 95%;
    padding: 1.5rem;
  }

  .info-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 480px) {
  .payments-header {
    padding: 1.5rem;
  }

  .payments-header-title {
    font-size: 1.5rem;
  }

  .payments-stats {
    gap: 1rem;
  }

  .stat-item {
    min-height: auto;
    padding: 1.25rem;
  }

  .stat-value {
    font-size: 1.75rem;
  }

  .stat-label {
    font-size: 0.75rem;
  }
}
</style>

<div class="payments-wrapper">
  <!-- Header -->
  <div class="payments-header">
    <div class="payments-header-content">
      <div class="payments-header-top">
        <div class="payments-header-title">
          <span class="payments-header-icon">💰</span>
          <span>Monthly Payments</span>
        </div>
        <div class="payments-date-range">
          📅 <span id="currentMonth"></span>
        </div>
      </div>

      <div class="payments-stats">
        <!-- Total Users Paid -->
        <div class="stat-item">
          <div>
            <div class="stat-label">
              <span class="stat-icon">👥</span>
              Total Users Paid
            </div>
            <div class="stat-value" id="totalUsers">5</div>
          </div>
          <div class="stat-change positive">↑ From last month</div>
        </div>

        <!-- Total Amount -->
        <div class="stat-item">
          <div>
            <div class="stat-label">
              <span class="stat-icon">💵</span>
              Total Amount Collected
            </div>
            <div class="stat-value amount">
              <span id="totalAmount">12,500.00</span>
            </div>
          </div>
          <div class="stat-change positive">↑ +12% increase</div>
        </div>

        <!-- Payment Gateway -->
        <div class="stat-item">
          <div>
            <div class="stat-label">
              <span class="stat-icon">💳</span>
              Payment Gateway
            </div>
            <div class="stat-value" id="gatewayCount">2</div>
          </div>
          <div class="stat-change neutral">Online Payments</div>
        </div>

        <!-- Manual GCash -->
        <div class="stat-item">
          <div>
            <div class="stat-label">
              <span class="stat-icon">📱</span>
              Manual GCash
            </div>
            <div class="stat-value" id="gcashCount">2</div>
          </div>
          <div class="stat-change neutral">Manual Transfers</div>
        </div>

        <!-- Over the Counter -->
        <div class="stat-item">
          <div>
            <div class="stat-label">
              <span class="stat-icon">🏪</span>
              Over the Counter
            </div>
            <div class="stat-value" id="counterCount">1</div>
          </div>
          <div class="stat-change neutral">Direct Payments</div>
        </div>

        <!-- Collection Rate -->
        <div class="stat-item">
          <div>
            <div class="stat-label">
              <span class="stat-icon">📊</span>
              Collection Rate
            </div>
            <div class="stat-value" id="collectionRate">10%</div>
          </div>
          <div class="stat-change positive">↑ Excellent</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Filters -->
  <div class="filters-section">
    <div class="filters-title">🔍 Filters</div>
    <div class="filters-grid">
      <div class="filter-group">
        <label class="filter-label">Search User</label>
        <input type="text" class="filter-input" id="searchInput" placeholder="Name or Email">
      </div>
      <div class="filter-group">
        <label class="filter-label">Payment Method</label>
        <select class="filter-select" id="methodFilter">
          <option value="">All Methods</option>
          <option value="Payment Gateway">Payment Gateway</option>
          <option value="Manual GCash">Manual GCash</option>
          <option value="Over the Counter">Over the Counter</option>
        </select>
      </div>
      <div class="filter-group">
        <label class="filter-label">Month</label>
        <input type="month" class="filter-input" id="monthFilter">
      </div>
      <div class="filter-buttons">
        <button class="filter-btn filter-btn-search" id="searchBtn">Search</button>
        <button class="filter-btn filter-btn-reset" id="resetBtn">Reset</button>
      </div>
    </div>
  </div>

  <!-- Table -->
  <div class="table-container">
    <div class="table-header">
      <div class="table-header-title">Payment Records</div>
      <div class="table-header-actions">
        <button class="export-btn" id="exportBtn">📊 Export CSV</button>
      </div>
    </div>

    <div class="table-wrapper">
      <table>
        <thead>
          <tr>
            <th>User Information</th>
            <th>Amount Paid</th>
            <th>Payment Method / Status</th>
            <th>Date Paid</th>
            <th>Reference</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody id="paymentTableBody">
          <tr>
            <td colspan="6" class="no-data">
              <div class="no-data-icon">📭</div>
              <div>Loading payment data...</div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- GCash Confirmation Modal -->
<div id="gcashModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h2 class="modal-title">Confirm Manual GCash Payment</h2>
      <button class="modal-close" onclick="closeGCashModal()">&times;</button>
    </div>

    <div class="modal-section">
      <div class="modal-section-title">User Information</div>
      <div class="info-grid">
        <span class="info-label">Name:</span>
        <span class="info-value" id="modalUserName">-</span>
        
        <span class="info-label">Email:</span>
        <span class="info-value" id="modalUserEmail">-</span>
        
        <span class="info-label">Amount:</span>
        <span class="info-value" id="modalAmount">-</span>
        
        <span class="info-label">User Reference:</span>
        <span class="info-value" id="modalUserRef">-</span>
        
        <span class="info-label">Date Submitted:</span>
        <span class="info-value" id="modalDate">-</span>
      </div>
    </div>

    <div class="modal-section">
      <div class="modal-section-title">Receipt Proof</div>
      <img id="modalReceipt" class="receipt-preview" src="" alt="Receipt" onclick="openImageInNewTab()">
      <p style="text-align: center; color: var(--muted); font-size: 0.85rem; margin-top: 0.5rem;">Click image to view full size</p>
    </div>

    <form id="confirmGCashForm">
      <div class="form-group">
        <input type="hidden" id="modalPaymentId" name="payment_id">
        <label for="adminRef" class="form-label">Admin Transaction Reference (Optional)</label>
        <input type="text" id="adminRef" name="admin_reference" class="form-input" placeholder="Enter transaction ID for record keeping">
      </div>

      <div class="modal-actions">
        <button type="button" class="btn-modal btn-cancel" onclick="closeGCashModal()">Cancel</button>
        <button type="submit" class="btn-modal btn-submit" id="confirmBtn">Confirm Payment</button>
      </div>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. Initial Data and State
    const currentMonthDisplay = document.getElementById('currentMonth');
    const paymentTableBody = document.getElementById('paymentTableBody');
    const gcashModal = document.getElementById('gcashModal');
    const confirmGCashForm = document.getElementById('confirmGCashForm');
    
    // Initial data - should be loaded via AJAX in a real application
    let paymentsData = [
        { id: 101, name: 'Juan Dela Cruz', email: 'juan@example.com', amount: 2500.00, method: 'Payment Gateway', status: 'Confirmed', date: '2025-10-05', ref: 'PGW-12345', avatar: 'JD', receipt: null },
        { id: 102, name: 'Maria Santos', email: 'maria@example.com', amount: 2500.00, method: 'Manual GCash', status: 'Pending', date: '2025-10-06', ref: 'TXN-98765', avatar: 'MS', receipt: 'https://via.placeholder.com/600x400/94a3b8/ffffff?text=GCASH+Receipt+98765' },
        { id: 103, name: 'Peter Kim', email: 'peter@example.com', amount: 5000.00, method: 'Over the Counter', status: 'Confirmed', date: '2025-10-07', ref: 'OTC-00101', avatar: 'PK', receipt: null },
        { id: 104, name: 'Sana Minatozaki', email: 'sana@example.com', amount: 2500.00, method: 'Manual GCash', status: 'Pending', date: '2025-10-08', ref: 'TXN-54321', avatar: 'SM', receipt: 'https://via.placeholder.com/600x400/34d399/ffffff?text=GCASH+Receipt+54321' },
    ];
    
    // Set current month display
    currentMonthDisplay.textContent = new Date().toLocaleString('en-US', { month: 'long', year: 'numeric' });

    // 2. NEW: Function to update statistics based on current data
    function updateStats(data) {
        const totalUsers = data.length;
        // Calculate total amount, ensuring 'amount' is treated as a number
        const totalAmount = data.reduce((sum, item) => sum + item.amount, 0); 
        
        // Count methods
        const gatewayCount = data.filter(item => item.method === 'Payment Gateway').length;
        const gcashCount = data.filter(item => item.method === 'Manual GCash').length;
        const counterCount = data.filter(item => item.method === 'Over the Counter').length;
        
        // Count confirmed payments for collection rate base (assuming 20 total users for demo)
        const totalConfirmed = data.filter(item => item.status === 'Confirmed').length;
        const totalPossible = 20; // Static dummy value for demo
        const collectionRate = totalUsers > 0 ? (totalConfirmed / totalPossible * 100).toFixed(0) : 0;


        // Update the DOM elements
        document.getElementById('totalUsers').textContent = totalUsers;
        document.getElementById('totalAmount').textContent = totalAmount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('gatewayCount').textContent = gatewayCount;
        document.getElementById('gcashCount').textContent = gcashCount;
        document.getElementById('counterCount').textContent = counterCount;
        document.getElementById('collectionRate').textContent = `${collectionRate}%`;
    }
    
    // 3. Rendering Functions
    
    /**
     * Creates the badge HTML based on payment method or status.
     * @param {string} type - 'method' or 'status'
     * @param {string} value - The method or status string
     * @returns {string} HTML string
     */
    function createBadge(type, value) {
        let className = '';
        let icon = '';
        const lowerValue = value.toLowerCase().replace(/\s/g, '-');

        if (type === 'method') {
            if (lowerValue.includes('gcash')) {
                className = 'badge-gcash';
                icon = '📱';
            } else if (lowerValue.includes('gateway')) {
                className = 'badge-gateway';
                icon = '💳';
            } else if (lowerValue.includes('counter')) {
                className = 'badge-counter';
                icon = '🏪';
            }
        } else if (type === 'status') {
            if (lowerValue === 'pending') {
                className = 'badge-pending';
                icon = '⏳';
            } else if (lowerValue === 'confirmed') {
                className = 'badge-confirmed';
                icon = '✅';
            }
        }

        return `<span class="badge ${className}">${icon} ${value}</span>`;
    }

    /**
     * Renders the payment table rows.
     * @param {Array<Object>} data - The filtered payment data
     */
    function renderTable(data) {
        paymentTableBody.innerHTML = '';

        if (data.length === 0) {
            paymentTableBody.innerHTML = `<tr>
                <td colspan="6" class="no-data">
                    <div class="no-data-icon">🤷‍♂️</div>
                    <div>No payment records found for the current filter.</div>
                </td>
            </tr>`;
            return;
        }

        data.forEach(item => {
            const isPendingGCash = item.method === 'Manual GCash' && item.status === 'Pending';
            
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <div class="user-info">
                        <div class="user-avatar">${item.avatar}</div>
                        <div class="user-details">
                            <h4>${item.name}</h4>
                            <p>${item.email}</p>
                        </div>
                    </div>
                </td>
                <td class="amount">₱${item.amount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                <td>
                    ${createBadge('method', item.method)}<br>
                    ${createBadge('status', item.status)}
                </td>
                <td>${item.date}</td>
                <td>${item.ref}</td>
                <td>
                    ${isPendingGCash 
                        ? `<button class="btn-confirm" data-id="${item.id}">Review</button>`
                        : `<span style="color:var(--success); font-weight:600;">-</span>`
                    }
                </td>
            `;
            paymentTableBody.appendChild(row);
        });

        // Attach event listeners to the new 'Review' buttons
        document.querySelectorAll('.btn-confirm').forEach(button => {
            button.addEventListener('click', (e) => {
                const id = parseInt(e.target.dataset.id);
                openGCashModal(id);
            });
        });
    }

    // 4. Filter and Search Logic (Placeholder)

    // A simple filter function for demonstration
    function applyFilters() {
        const searchVal = document.getElementById('searchInput').value.toLowerCase();
        const methodVal = document.getElementById('methodFilter').value;
        const monthVal = document.getElementById('monthFilter').value;

        const filteredData = paymentsData.filter(item => {
            const matchesSearch = item.name.toLowerCase().includes(searchVal) || item.email.toLowerCase().includes(searchVal);
            const matchesMethod = methodVal === '' || item.method === methodVal;
            const matchesMonth = monthVal === '' || item.date.startsWith(monthVal); // Simple month check

            return matchesSearch && matchesMethod && matchesMonth;
        });

        // Update both table and stats with the filtered data
        renderTable(filteredData);
        updateStats(filteredData); // <--- KEY FIX: Update stats here
    }

    document.getElementById('searchBtn').addEventListener('click', applyFilters);
    document.getElementById('resetBtn').addEventListener('click', () => {
        document.getElementById('searchInput').value = '';
        document.getElementById('methodFilter').value = '';
        document.getElementById('monthFilter').value = '';
        applyFilters();
    });
    
    // Initial render and stat calculation
    renderTable(paymentsData);
    updateStats(paymentsData); // <--- KEY FIX: Initial stats calculation


    // 5. Modal Functions

    let currentPayment = null;

    window.openGCashModal = function(id) {
        currentPayment = paymentsData.find(p => p.id === id);
        
        if (!currentPayment || currentPayment.status !== 'Pending' || currentPayment.method !== 'Manual GCash') return;

        // Populate modal fields
        document.getElementById('modalUserName').textContent = currentPayment.name;
        document.getElementById('modalUserEmail').textContent = currentPayment.email;
        // Format amount for display in modal
        document.getElementById('modalAmount').textContent = `₱${currentPayment.amount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`; 
        document.getElementById('modalUserRef').textContent = currentPayment.ref;
        document.getElementById('modalDate').textContent = currentPayment.date;
        document.getElementById('modalReceipt').src = currentPayment.receipt;
        document.getElementById('modalReceipt').dataset.fullSrc = currentPayment.receipt; // Store full image source
        document.getElementById('modalPaymentId').value = currentPayment.id;
        
        // Show modal
        gcashModal.classList.add('active');
    };

    window.closeGCashModal = function() {
        gcashModal.classList.remove('active');
        currentPayment = null;
        // Reset form fields
        document.getElementById('adminRef').value = '';
    };

    window.openImageInNewTab = function() {
        const fullSrc = document.getElementById('modalReceipt').dataset.fullSrc;
        if (fullSrc) {
            window.open(fullSrc, '_blank');
        }
    }
    
    // Close modal on outside click
    gcashModal.addEventListener('click', (e) => {
        if (e.target === gcashModal) {
            closeGCashModal();
        }
    });

    // 6. Form Submission (Confirmation Logic)

    confirmGCashForm.addEventListener('submit', (e) => {
        e.preventDefault();
        
        if (!currentPayment) return;

        const paymentId = document.getElementById('modalPaymentId').value;
        const adminRef = document.getElementById('adminRef').value;
        const confirmBtn = document.getElementById('confirmBtn');

        confirmBtn.textContent = 'Confirming...';
        confirmBtn.disabled = true;

        // --- Simulated AJAX call to server to confirm payment ---
        setTimeout(() => {
            // Find the payment in the local array and update its status
            const index = paymentsData.findIndex(p => p.id == paymentId);
            if (index !== -1) {
                paymentsData[index].status = 'Confirmed';
                paymentsData[index].adminRef = adminRef || 'N/A'; // Add admin ref for record
                
                // Show success message (in a real app, you'd use a notification library)
                alert(`Payment ID ${paymentId} for ${paymentsData[index].name} has been Confirmed!`);
            }

            // Re-render the table and update stats to reflect the change
            applyFilters(); 
            
            // Close the modal and reset button state
            closeGCashModal();
            confirmBtn.textContent = 'Confirm Payment';
            confirmBtn.disabled = false;
            
        }, 1500); // Simulate network delay
    });
});
</script>