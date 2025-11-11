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

  /* === NEW MODAL STYLES START === */

  .modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(31, 41, 55, 0.7); /* Darker, on-brand overlay */
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
  }

  .modal.active {
    display: flex;
    align-items: center;
    justify-content: center;
    /* Add subtle fade-in for the overlay */
    animation: modalFadeIn 0.3s ease;
  }

  .modal-content {
    background: white;
    border-radius: 20px;
    padding: 1.75rem 2rem;
    max-width: 600px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 25px 60px rgba(0, 0, 0, 0.3);
    /* Use the slide-in animation from your original CSS */
    animation: modalSlideIn 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
  }

  /* Keyframe for the overlay fade-in */
  @keyframes modalFadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
  }

  /* Re-add your original slide-in keyframe */
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
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid var(--border);
  }

  .modal-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--dark);
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }

  .modal-close {
    background: var(--light);
    border: 2px solid var(--border);
    font-size: 1.5rem;
    cursor: pointer;
    color: var(--muted);
    transition: all 0.3s ease;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
  }

  .modal-close:hover {
    background: rgba(239, 68, 68, 0.1); /* Use danger color */
    border-color: rgba(239, 68, 68, 0.3);
    color: var(--danger);
    transform: rotate(90deg);
  }

  .modal-body {
    padding-top: 1rem;
  }

  /* This is the key change for scannability */
  .payment-details {
    background: var(--light);
    border: 1px solid var(--border);
    padding: 1.25rem;
    border-radius: 12px;
    display: grid;
    grid-template-columns: 140px 1fr; /* Label | Value */
    gap: 1rem;
    margin-bottom: 1.5rem;
  }

  .detail-row {
    /* This allows the grid to handle the layout */
    display: contents;
  }

  .detail-label {
    font-weight: 600;
    color: var(--muted);
    font-size: 0.85rem;
    align-self: center;
  }

  .detail-value {
    font-weight: 700;
    color: var(--dark);
    font-size: 0.9rem;
    align-self: center;
    word-break: break-word;
  }

  .detail-value.amount {
    font-size: 1.1rem;
    color: var(--success);
    font-weight: 800;
  }
  .detail-value.amount::before {
    content: '₱';
    margin-right: 2px;
  }

  .receipt-section {
    margin-bottom: 1.5rem;
  }

  /* Section title style from your original CSS */
  .receipt-section h3 {
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 0.75rem;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .receipt-container {
    position: relative;
  }

  #modalReceipt {
    width: 100%;
    border-radius: 12px;
    border: 2px solid var(--border);
    cursor: pointer;
    transition: all 0.3s ease;
    display: block;
  }

  #modalReceipt:hover {
    transform: scale(1.02);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    border-color: var(--primary);
  }

  .receipt-hint {
    font-size: 0.8rem;
    color: var(--muted);
    text-align: center;
    margin-top: 0.75rem;
  }

  /* Use your existing form styles */
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

  /* Use your existing button styles */
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

  /* Renamed to match your HTML */
  .btn-confirm-payment {
    background: linear-gradient(135deg, var(--success) 0%, #0d9488 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
  }

  .btn-confirm-payment:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
  }

  /* === NEW MODAL STYLES END === */


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

    /* Updated for new modal */
    .payment-details {
      grid-template-columns: 100px 1fr; /* Adjust for smaller screens */
      gap: 0.75rem 1rem;
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
    
    /* Updated for new modal */
    .payment-details {
        grid-template-columns: 1fr; /* Stack on very small screens */
        gap: 0.5rem;
    }
    .detail-label {
        font-size: 0.8rem;
        color: var(--muted);
    }
    .detail-value {
        font-size: 0.9rem;
        margin-bottom: 0.5rem; /* Add spacing when stacked */
    }
    .modal-actions {
        flex-direction: column-reverse; /* Stack buttons */
    }
    .btn-modal {
        width: 100%;
    }
  }
</style>
</head>
<body>

<div class="payments-wrapper">
  <div class="payments-header">
    <div class="payments-header-content">
      <div class="payments-header-top">
        <div class="payments-header-title">
          <span class="payments-header-icon">💰</span>
          <span>Monthly Payments</span>
        </div>
        <div class="payments-date-range">
          📅 <span id="currentMonth"><?= esc(date('F Y', strtotime($current_month ?? 'now'))) ?></span>
        </div>
      </div>

      <div class="payments-stats">
        <div class="stat-item">
          <div>
            <div class="stat-label"><span class="stat-icon">👥</span>Total Users Paid</div>
            <div class="stat-value" id="totalUsers"><?= esc($stats['total_users'] ?? 0) ?></div>
          </div>
        </div>

        <div class="stat-item">
          <div>
            <div class="stat-label"><span class="stat-icon">💵</span>Total Amount Collected</div>
            <div class="stat-value amount"><span id="totalAmount"><?= number_format($stats['total_amount'] ?? 0, 2) ?></span></div>
          </div>
        </div>

        <div class="stat-item">
          <div>
            <div class="stat-label"><span class="stat-icon">💳</span>Payment Gateway</div>
            <div class="stat-value" id="gatewayCount"><?= esc($stats['gateway'] ?? 0) ?></div>
          </div>
        </div>

        <div class="stat-item">
          <div>
            <div class="stat-label"><span class="stat-icon">📱</span>Manual GCash</div>
            <div class="stat-value" id="gcashCount"><?= esc($stats['gcash'] ?? 0) ?></div>
          </div>
        </div>

        <div class="stat-item">
          <div>
            <div class="stat-label"><span class="stat-icon">🏪</span>Over the Counter</div>
            <div class="stat-value" id="counterCount"><?= esc($stats['counter'] ?? 0) ?></div>
          </div>
        </div>

        <div class="stat-item">
          <div>
            <div class="stat-label"><span class="stat-icon">📊</span>Collection Rate</div>
            <div class="stat-value" id="collectionRate"><?= esc($stats['collection_rate'] ?? 0) ?>%</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="filters-section">
    <div class="filters-title">🔍 Filters</div>
    <form method="get" action="<?= base_url('admin/monthly-payments') ?? '#' ?>">
      <div class="filters-grid">
        <div class="filter-group">
          <label class="filter-label">Search User</label>
          <input type="text" id="searchInput" class="filter-input" name="search" placeholder="Name or Email" value="<?= esc($filters['search'] ?? '') ?>">
        </div>
        <div class="filter-group">
          <label class="filter-label">Payment Method</label>
          <select id="methodFilter" class="filter-select" name="method">
            <option value="">All Methods</option>
            <option value="Payment Gateway" <?= ($filters['method'] ?? '') == 'Payment Gateway' ? 'selected' : '' ?>>Payment Gateway</option>
            <option value="Manual GCash" <?= ($filters['method'] ?? '') == 'Manual GCash' ? 'selected' : '' ?>>Manual GCash</option>
            <option value="Over the Counter" <?= ($filters['method'] ?? '') == 'Over the Counter' ? 'selected' : '' ?>>Over the Counter</option>
          </select>
        </div>
        <div class="filter-group">
          <label class="filter-label">Month</label>
          <input type="month" id="monthFilter" class="filter-input" name="month" value="<?= esc($filters['month'] ?? date('Y-m')) ?>">
        </div>
        <div class="filter-buttons">
          <button type="button" id="searchBtn" class="filter-btn filter-btn-search">Search</button>
          <button type="button" id="resetBtn" class="filter-btn filter-btn-reset">Reset</button>
        </div>
      </div>
    </form>
  </div>

  <div class="table-container">
    <div class="table-header">
      <div class="table-header-title">Payment Records</div>
      <div class="table-header-actions">
        <button class="export-btn" id="exportBtn">📊 Export CSV</button>
      </div>
    </div>

    <div class="table-wrapper">
      <div id="loadingIndicator" class="loading-indicator" style="display: none;">
        <p>Loading payments...</p>
      </div>
      <table>
        <thead>
          <tr>
            <th>User Information</th>
            <th>Amount Paid</th>
            <th>Payment Method</th>
            <th>Status</th>
            <th>Date Paid</th>
            <th>Reference</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody id="paymentTableBody">
          <tr>
            <td>
              <div class="user-info">
                <div class="user-avatar">JD</div>
                <div class="user-details">
                  <h4>Juan dela Cruz</h4>
                  <p>juan@example.com</p>
                </div>
              </div>
            </td>
            <td class="amount">₱500.00</td>
            <td><span class="badge badge-gcash">📱 Manual GCash</span></td>
            <td><span class="badge badge-pending">⏳ Pending</span></td>
            <td>Nov 12, 2025</td>
            <td>USER_REF_123</td>
            <td><button class="btn-confirm" onclick="openGCashModal()">Review</button></td>
          </tr>
          <tr>
            <td>
              <div class="user-info">
                <div class="user-avatar">MS</div>
                <div class="user-details">
                  <h4>Maria Santos</h4>
                  <p>maria@example.com</p>
                </div>
              </div>
            </td>
            <td class="amount">₱500.00</td>
            <td><span class="badge badge-gateway">💳 Payment Gateway</span></td>
            <td><span class="badge badge-confirmed">✅ Confirmed</span></td>
            <td>Nov 11, 2025</td>
            <td>PAY_XYZ_456</td>
            <td></td>
          </tr>
          </tbody>
      </table>
    </div>
  </div>
</div>

<div id="gcashModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h2 class="modal-title">📱 Review GCash Payment</h2>
      <button type="button" class="modal-close" onclick="closeGCashModal()">&times;</button>
    </div>
    <div class="modal-body">
      <div class="payment-details">
        <div class="detail-row">
          <span class="detail-label">User:</span>
          <span id="modalUserName" class="detail-value"></span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Email:</span>
          <span id="modalUserEmail" class="detail-value"></span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Amount:</span>
          <span id="modalAmount" class="detail-value amount"></span>
        </div>
        <div class="detail-row">
          <span class="detail-label">User Reference:</span>
          <span id="modalUserRef" class="detail-value"></span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Date:</span>
          <span id="modalDate" class="detail-value"></span>
        </div>
      </div>

      <div class="receipt-section">
        <h3>Receipt Image</h3>
        <div class="receipt-container">
          <img id="modalReceipt" src="" alt="Payment Receipt" onclick="openImageInNewTab()">
          <p class="receipt-hint">Click image to view full size</p>
        </div>
      </div>

      <form id="confirmGCashForm">
        <input type="hidden" id="modalPaymentId" name="payment_id">
        <div class="form-group">
          <label for="adminRef" class="form-label">Admin Reference Number (Optional)</label>
          <input type="text" id="adminRef" name="admin_reference" class="form-input" placeholder="Enter reference number">
        </div>
        <div class="modal-actions">
          <button type="button" class="btn-modal btn-cancel" onclick="closeGCashModal()">Cancel</button>
          <button type="submit" id="confirmBtn" class="btn-modal btn-confirm-payment">Confirm Payment</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
function initTransactionPage() {
    const currentMonthDisplay = document.getElementById('currentMonth');
    const paymentTableBody = document.getElementById('paymentTableBody');
    const loadingIndicator = document.getElementById('loadingIndicator');
    const gcashModal = document.getElementById('gcashModal');
    const confirmGCashForm = document.getElementById('confirmGCashForm');
    const exportBtn = document.getElementById('exportBtn');

    let paymentsData = [];
    const currentMonth = new Date().toISOString().slice(0, 7);

    function showLoading() {
        if (loadingIndicator) loadingIndicator.style.display = 'block';
        if (paymentTableBody) paymentTableBody.innerHTML = '';
    }

    function hideLoading() {
        if (loadingIndicator) loadingIndicator.style.display = 'none';
    }

    // Load payments data with optional filters
function loadPayments(filters = {}) {
    showLoading();
    const month = filters.month || currentMonth;
    const method = filters.method || '';
    const search = filters.search || '';

    const url = `<?= site_url('admin/getPaymentsData') ?>?month=${month}&method=${encodeURIComponent(method)}&search=${encodeURIComponent(search)}`;

    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                paymentsData = data.payments || [];
                renderTable(paymentsData);

                // Calculate stats dynamically
                const totalAmount = paymentsData.reduce((sum, p) => sum + parseFloat(p.amount || 0), 0);
                const totalCollected = paymentsData
                    .filter(p => (p.status || '').toLowerCase() === 'paid')
                    .reduce((sum, p) => sum + parseFloat(p.amount || 0), 0);
                const collectionRate = totalAmount ? ((totalCollected / totalAmount) * 100).toFixed(2) : 0;

                // Update stats in DOM
                document.getElementById('totalAmount').textContent = totalCollected.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                document.getElementById('collectionRate').textContent = collectionRate + '%';
                document.getElementById('totalUsers').textContent = paymentsData.length;

                const gcashCount = paymentsData.filter(p => p.method.toLowerCase() === 'manual').length;
                const counterCount = paymentsData.filter(p => p.method.toLowerCase() === 'offline').length;
                const gatewayCount = paymentsData.filter(p => p.method.toLowerCase() === 'gateway').length;

                document.getElementById('gcashCount').textContent = gcashCount;
                document.getElementById('counterCount').textContent = counterCount;
                document.getElementById('gatewayCount').textContent = gatewayCount;

                const date = new Date(month + '-01');
                if (currentMonthDisplay) {
                    currentMonthDisplay.textContent = date.toLocaleString('en-US', { month: 'long', year: 'numeric' });
                }
            } else {
                alert('Error: ' + (data.message || 'Failed to load payments'));
            }
            hideLoading();
        })
        .catch(err => {
            console.error(err);
            hideLoading();
            if (paymentTableBody) {
                paymentTableBody.innerHTML = `<tr><td colspan="6">❌ Failed to load payments. Check console.</td></tr>`;
            }
        });
}


    // Update statistics section
    function updateStats(stats) {
        document.getElementById('totalUsers').textContent = stats.total_users || 0;
        document.getElementById('totalAmount').textContent = (stats.total_amount || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('gatewayCount').textContent = stats.gateway || 0;
        document.getElementById('gcashCount').textContent = stats.gcash || 0;
        document.getElementById('counterCount').textContent = stats.counter || 0;
        document.getElementById('collectionRate').textContent = `${stats.collection_rate || 0}%`;
    }

// Create badge HTML
function createBadge(type, value) {
    let className = '', icon = '';
    const lower = (value || '').toLowerCase();

    if (type === 'method') {
        if (lower.includes('gcash')) { className = 'badge-gcash'; icon = '📱'; }
        else if (lower.includes('gateway')) { className = 'badge-gateway'; icon = '💳'; }
        else if (lower.includes('counter')) { className = 'badge-counter'; icon = '🏪'; }
    } else if (type === 'status') {
        if (lower === 'pending') { className = 'badge-pending'; icon = '⏳'; }
        else if (lower === 'confirmed') { className = 'badge-confirmed'; icon = '✅'; }
        else if (lower === 'paid') { className = 'badge-paid'; icon = '✅'; } // added for Paid
    }

    return `<span class="badge ${className}">${icon} ${value}</span>`;
}


// Map API method values to readable labels
function mapMethod(method) {
    if (!method) return 'Unknown';
    const lower = method.toLowerCase();
    if (lower === 'manual') return 'Manual GCash';
    if (lower === 'offline') return 'Over the Counter';
    if (lower === 'gateway') return 'Payment Gateway';
    return method;
}

// Render payment table
function renderTable(data) {
    if (!paymentTableBody) return;
    paymentTableBody.innerHTML = '';

    if (!data.length) {
        paymentTableBody.innerHTML = `<tr><td colspan="7">No payment records found.</td></tr>`;
        return;
    }

    data.forEach(item => {
        const methodLabel = mapMethod(item.method);
        const statusLabel = item.status || 'Unknown';
        const isPendingGCash = methodLabel === 'Manual GCash' && statusLabel.toLowerCase() === 'pending';

        const row = document.createElement('tr');
        row.innerHTML = `
            <td><div>${item.user_name || '-' }<br>${item.email || '-'}</div></td>
            <td>₱${parseFloat(item.amount || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
            <td>${createBadge('method', methodLabel)}</td>
            <td>${createBadge('status', statusLabel)}</td>
            <td>${item.paid_at || '-'}</td>
            <td>${item.reference_number || '-'}${item.admin_reference ? '<br><small>Admin: ' + item.admin_reference + '</small>' : ''}</td>
            <td>${isPendingGCash ? `<button class="btn-confirm" data-id="${item.id}">Review</button>` : '-'}</td>
        `;
        paymentTableBody.appendChild(row);
    });

    // Add click listeners for modal buttons
    document.querySelectorAll('.btn-confirm').forEach(btn => {
        btn.addEventListener('click', e => {
            const id = parseInt(e.target.dataset.id);
            openGCashModal(id);
        });
    });
}





    // --- Filter Handlers ---
    if (document.getElementById('searchBtn')) {
        document.getElementById('searchBtn').addEventListener('click', () => {
            loadPayments({
                month: document.getElementById('monthFilter').value,
                method: document.getElementById('methodFilter').value,
                search: document.getElementById('searchInput').value
            });
        });
    }

    // Reset Filters
    if (document.getElementById('resetBtn')) {
        document.getElementById('resetBtn').addEventListener('click', () => {
            document.getElementById('monthFilter').value = currentMonth;
            document.getElementById('methodFilter').value = '';
            document.getElementById('searchInput').value = '';
            loadPayments();
        });
    }

    // Export CSV
    if (exportBtn) {
        exportBtn.addEventListener('click', () => {
            const month = document.getElementById('monthFilter').value || currentMonth;
            const method = document.getElementById('methodFilter').value || '';
            const search = document.getElementById('searchInput').value || '';
            const exportUrl = `<?= site_url('admin/exportPayments') ?>?month=${month}&method=${encodeURIComponent(method)}&search=${encodeURIComponent(search)}`;
            window.location.href = exportUrl;
        });
    }

    // --- Modal ---
    let currentPayment = null;

window.openGCashModal = function(id) {
    currentPayment = paymentsData.find(p => p.id == id);
    if (!currentPayment || currentPayment.status.toLowerCase() !== 'pending' || currentPayment.method.toLowerCase() !== 'manual') return;

    document.getElementById('modalUserName').textContent = currentPayment.user_name;
    document.getElementById('modalUserEmail').textContent = currentPayment.email;
    document.getElementById('modalAmount').textContent = `₱${parseFloat(currentPayment.amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    document.getElementById('modalUserRef').textContent = currentPayment.reference_number;
    document.getElementById('modalDate').textContent = currentPayment.paid_at || currentPayment.created_at;
    document.getElementById('modalReceipt').src = currentPayment.receipt_image || '';
    document.getElementById('modalReceipt').dataset.fullSrc = currentPayment.receipt_image || '';
    document.getElementById('modalPaymentId').value = currentPayment.id;
    document.getElementById('adminRef').value = currentPayment.admin_reference || '';

    gcashModal.classList.add('active');
};


    window.closeGCashModal = function() {
        gcashModal.classList.remove('active');
        currentPayment = null;
        document.getElementById('adminRef').value = '';
    };

    if (gcashModal) {
        gcashModal.addEventListener('click', e => {
            if (e.target === gcashModal) closeGCashModal();
        });
    }

    if (confirmGCashForm) {
        confirmGCashForm.addEventListener('submit', e => {
            e.preventDefault();
            if (!currentPayment) return;

            const paymentId = document.getElementById('modalPaymentId').value;
            const adminRef = document.getElementById('adminRef').value;
            const confirmBtn = document.getElementById('confirmBtn');

            confirmBtn.textContent = 'Confirming...';
            confirmBtn.disabled = true;

            const confirmUrl = `<?= site_url('admin/confirmGCashPayment') ?>`;

            fetch(confirmUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ payment_id: paymentId, admin_reference: adminRef })
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    alert(`✅ Payment ID ${paymentId} confirmed!`);
                    closeGCashModal();
                    loadPayments({
                        month: document.getElementById('monthFilter').value,
                        method: document.getElementById('methodFilter').value,
                        search: document.getElementById('searchInput').value
                    });
                } else {
                    alert('❌ ' + res.message);
                }
            })
            .catch(err => {
                console.error(err);
                alert('❌ An error occurred.');
            })
            .finally(() => {
                confirmBtn.textContent = 'Confirm Payment';
                confirmBtn.disabled = false;
            });
        });
    }

    // Initial load
    loadPayments();
}

// On full page load
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('paymentTableBody')) {
        initTransactionPage();
    }
});

// --- AJAX helper for sidebar links ---
function loadAjaxPage(url) {
    fetch(url)
        .then(res => res.text())
        .then(html => {
            document.getElementById('mainContent').innerHTML = html;
            // If payment table exists in loaded content, init the page
            if (document.getElementById('paymentTableBody')) {
                initTransactionPage();
            }
        })
        .catch(err => console.error('AJAX load error:', err));
}
</script>
