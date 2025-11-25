  <!DOCTYPE html>
  <html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Payment History - Aqua Bill</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Custom Navbar CSS -->
    <link href="<?= base_url('assets/Users/css/navbar.css?v=' . time()) ?>" rel="stylesheet"/>

    <style>
      :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        --warning-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        --danger-gradient: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
        --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        --card-shadow-hover: 0 20px 60px rgba(0, 0, 0, 0.15);
        --border-radius: 20px;
      }

      * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
      }

      body {
        font-family: 'Inter', sans-serif;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
        padding-top: 90px;
      }

      /* Page Header */
      .page-hero {
        background: var(--primary-gradient);
        padding: 60px 0 40px 0;
        position: relative;
        overflow: hidden;
        margin-bottom: 30px;
      }

      .page-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 20"><defs><radialGradient id="a" cx="50%" cy="0%" fx="50%" fy="0%"><stop offset="0%" stop-color="%23ffffff" stop-opacity="0.1"/><stop offset="100%" stop-color="%23ffffff" stop-opacity="0"/></radialGradient></defs><circle cx="25" cy="10" r="8" fill="url(%23a)"/><circle cx="75" cy="10" r="8" fill="url(%23a)"/></svg>') repeat;
        opacity: 0.1;
      }

      .page-title {
        color: white;
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 10px;
        background: linear-gradient(45deg, #ffffff, #e3f2fd);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
      }

      .page-subtitle {
        color: rgba(255, 255, 255, 0.9);
        font-size: 1.1rem;
        font-weight: 400;
      }

      /* Main Container */
      #main {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1rem;
        position: relative;
      }

      /* History Container */
      .history-container {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--card-shadow);
        overflow: visible;
        margin-top: 0;
        position: relative;
        z-index: 10;
        min-height: 300px;
      }

      .history-header {
        background: linear-gradient(135deg, #2d3748, #4a5568);
        color: white;
        padding: 25px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: relative;
        border-top-left-radius: var(--border-radius);
        border-top-right-radius: var(--border-radius);
      }

      .history-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        animation: shimmer 3s infinite;
      }

      @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
      }

      .header-info {
        display: flex;
        align-items: center;
        gap: 15px;
      }

      .header-icon {
        width: 50px;
        height: 50px;
        border-radius: 15px;
        background: rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        backdrop-filter: blur(10px);
      }

      .header-text h6 {
        margin: 0;
        font-weight: 700;
        font-size: 1.1rem;
      }

      .header-text p {
        margin: 0;
        opacity: 0.9;
        font-size: 0.9rem;
      }

      /* Enhanced Status Dropdown */
      .status-dropdown .btn {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border: 2px solid rgba(255, 255, 255, 0.3);
        font-weight: 600;
        padding: 12px 24px;
        border-radius: 50px;
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
        position: relative;
        z-index: 2;
      }

      .status-dropdown .btn:hover {
        background: rgba(255, 255, 255, 0.3);
        border-color: rgba(255, 255, 255, 0.5);
        transform: translateY(-2px);
        color: white;
      }

      .status-dropdown .btn:focus {
        box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.2);
        color: white;
      }

      .dropdown-menu {
        border: none;
        border-radius: 15px;
        box-shadow: var(--card-shadow);
        padding: 10px;
        margin-top: 10px;
      }

      .dropdown-item {
        border-radius: 10px;
        padding: 12px 20px;
        font-weight: 500;
        transition: all 0.2s ease;
        margin-bottom: 2px;
      }

      .dropdown-item:hover {
        background: var(--primary-gradient);
        color: white;
        transform: translateX(5px);
      }

      /* Transaction List */
      .transaction-list {
        max-height: 70vh;
        overflow-y: auto;
        position: relative;
        background: white;
        border-bottom-left-radius: var(--border-radius);
        border-bottom-right-radius: var(--border-radius);
        min-height: 200px;
        margin-bottom: 0;
      }

      .history-subheader {
        padding: 20px 30px 15px;
        font-size: 1.1rem;
        font-weight: 700;
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        color: #495057;
        border-bottom: 1px solid #dee2e6;
        position: sticky;
        top: 0;
        z-index: 5;
        display: flex;
        align-items: center;
        gap: 10px;
      }

      .history-subheader i {
        color: #667eea;
      }

      /* Enhanced Transaction Items */
      .transaction-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 25px 30px;
        border-bottom: 1px solid #f1f3f4;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        cursor: pointer;
        position: relative;
        background: white;
      }

      .transaction-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: transparent;
        transition: all 0.3s ease;
      }

      .transaction-item:hover {
        background: linear-gradient(135deg, #f8f9ff, #f0f4ff);
        transform: translateX(10px);
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.15);
      }

      .transaction-item:hover::before {
        background: var(--primary-gradient);
      }

      .transaction-item:last-child {
        border-bottom: none;
      }

      /* Transaction Details */
      .transaction-details {
        display: flex;
        align-items: center;
        gap: 20px;
      }

      .transaction-icon {
        width: 60px;
        height: 60px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
        position: relative;
        overflow: hidden;
      }

      .transaction-icon::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.6s;
      }

      .transaction-item:hover .transaction-icon::before {
        left: 100%;
      }

      .transaction-info {
        flex: 1;
      }

      .transaction-time {
        font-size: 0.85rem;
        color: #6b7280;
        margin-bottom: 5px;
        display: flex;
        align-items: center;
        gap: 5px;
        font-weight: 500;
      }

      .transaction-name {
        font-weight: 700;
        color: #1f2937;
        font-size: 1.1rem;
        margin-bottom: 5px;
      }

      .transaction-status {
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 4px 12px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
      }

      /* Enhanced Amount Styling */
      .transaction-amount {
        text-align: right;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 5px;
      }

      .amount-value {
        display: inline-block;
        background: linear-gradient(90deg, #f59e0b 60%, #fbbf24 100%);
        color: #fff !important;
        font-weight: 900;
        font-size: 1.3rem;
        font-family: 'Poppins', sans-serif;
        border-radius: 8px;
        padding: 6px 18px;
        box-shadow: 0 2px 8px rgba(245, 158, 11, 0.15);
        letter-spacing: 1px;
        margin-bottom: 2px;
        text-shadow: 0 1px 2px rgba(0,0,0,0.08);
      }

      .amount-label {
        font-size: 0.8rem;
        color: #6b7280;
        font-weight: 500;
      }

      /* Status Colors */
      .amount-credit, .status-paid { 
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
      }
      .amount-credit { color: #10b981; }

      .amount-pending, .status-pending { 
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
      }
      .amount-pending { color: #f59e0b; }

      .amount-debit, .status-failed { 
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
      }
      .amount-debit { color: #ef4444; }

      /* Partial payment styling */
      .amount-partial, .status-partial {
        background: linear-gradient(135deg, #f97316, #fb923c);
        color: white;
      }
      .amount-partial { color: #f97316; }

      /* Icon Backgrounds */
      .icon-paid { background: linear-gradient(135deg, #10b981, #059669); }
      .icon-pending { background: linear-gradient(135deg, #f59e0b, #d97706); }
      .icon-failed { background: linear-gradient(135deg, #ef4444, #dc2626); }

      /* Enhanced Modal */
      .modal-content {
        border: none;
        border-radius: var(--border-radius);
        box-shadow: 0 25px 80px rgba(0, 0, 0, 0.15);
        overflow: hidden;
      }

      .modal-header {
        background: var(--primary-gradient);
        color: white;
        border: none;
        padding: 25px 30px;
        position: relative;
      }

      .modal-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        animation: shimmer 3s infinite;
      }

      .modal-title {
        font-weight: 700;
        font-size: 1.3rem;
        display: flex;
        align-items: center;
        gap: 10px;
        position: relative;
        z-index: 2;
      }

      .modal-body {
        padding: 35px 30px;
        background: linear-gradient(135deg, #f8f9fa, #ffffff);
      }

      .receipt-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        margin-bottom: 10px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        border-left: 4px solid var(--primary-gradient);
        transition: all 0.2s ease;
      }

      .receipt-item:hover {
        transform: translateX(5px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      }

      .receipt-label {
        font-weight: 600;
        color: #4b5563;
        display: flex;
        align-items: center;
        gap: 8px;
      }

      .receipt-value {
        font-weight: 700;
        color: #1f2937;
      }

      .modal-footer {
        background: #f8f9fa;
        border: none;
        padding: 25px 30px;
        gap: 15px;
      }

      .btn-modal {
        padding: 12px 30px;
        border-radius: 50px;
        font-weight: 600;
        border: none;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
      }

      .btn-modal::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.6s;
      }

      .btn-modal:hover::before {
        left: 100%;
      }

      .btn-close-modal {
        background: #6b7280;
        color: white;
      }

      .btn-close-modal:hover {
        background: #4b5563;
        transform: translateY(-2px);
        color: white;
      }

      .btn-download {
        background: var(--primary-gradient);
        color: white;
      }

      .btn-download:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        color: white;
      }

      /* Empty State */
      .empty-state {
        text-align: center;
        padding: 80px 30px;
        color: #6b7280;
      }

      .empty-state i {
        font-size: 5rem;
        margin-bottom: 30px;
        color: #d1d5db;
      }

      .empty-state h4 {
        font-weight: 700;
        color: #374151;
        margin-bottom: 15px;
      }

      .empty-state p {
        font-size: 1.1rem;
        color: #6b7280;
        max-width: 400px;
        margin: 0 auto;
        line-height: 1.6;
      }

      /* Responsive Design */
      @media (max-width: 768px) {
        .page-title {
          font-size: 2rem;
        }
        
        .history-header {
          flex-direction: column;
          gap: 20px;
          text-align: center;
          padding: 20px;
        }
        
        .transaction-item {
          padding: 20px 15px;
          flex-direction: column;
          align-items: flex-start;
          gap: 15px;
        }
        
        .transaction-details {
          width: 100%;
        }
        
        .transaction-amount {
          width: 100%;
          align-items: flex-start;
          flex-direction: row;
          justify-content: space-between;
        }

        .modal-dialog {
          max-width: 500px;
          margin: 1.75rem auto;
        }
        
        .modal-content {
          border-radius: 20px;
          /* Remove any height: 100vh or overflow: hidden here */
        }
        
        .modal-dialog {
          max-width: 95vw;
          margin: 1rem auto;
        }
      }

      /* Custom Scrollbar */
      .transaction-list::-webkit-scrollbar {
        width: 8px;
      }

      .transaction-list::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
      }

      .transaction-list::-webkit-scrollbar-thumb {
        background: var(--primary-gradient);
        border-radius: 10px;
      }

      .transaction-list::-webkit-scrollbar-thumb:hover {
        background: #667eea;
      }

      /* Scroll to top button */
      #scrollTop {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 50px;
        height: 50px;
        background: var(--primary-gradient);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        font-weight: bold;
        font-size: 1.2rem;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        z-index: 1000;
      }

      #scrollTop:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6);
        color: white;
      }

      /* Spacer between main content and footer */
      .content-footer-spacer {
        height: 60px;
        width: 100%;
      }

      @media (max-width: 768px) {
        .content-footer-spacer { height: 40px; }
      }
    </style>

    
  </head>
  <body>
  <?= $this->include('Users/header') ?>

  <!-- Page Hero -->
  <section class="page-hero">
    <div class="container">
      <div class="row justify-content-center text-center" data-aos="fade-up">
        <div class="col-lg-8">
          <h1 class="page-title">Payment History</h1>
          <p class="page-subtitle">Track all your water bill payments and transactions</p>
        </div>
      </div>
    </div>
  </section>


  <!-- Loading Spinner -->
  <div id="history-loading" style="display:flex;align-items:center;justify-content:center;height:300px;">
    <div class="spinner-border text-primary" role="status" style="width:4rem;height:4rem;">
      <span class="visually-hidden">Loading...</span>
    </div>
  </div>

  <main id="main" style="display:none;">
    <div class="container-fluid p-0">
      <div class="history-container" data-aos="fade-up" data-aos-delay="200">
        <div class="history-header">
          <div class="header-info">
            <div class="header-icon">
              <i class="bi bi-receipt"></i>
            </div>
            <div class="header-text">
              <h6>Transaction History</h6>
              <p>As of <?= date('M d, Y') ?></p>
            </div>
          </div>

          <div class="dropdown status-dropdown">
            <button class="btn dropdown-toggle" type="button" id="statusDropdown" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="bi bi-funnel me-2"></i>
              Select Status
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="statusDropdown">
              <li><a class="dropdown-item" href="#"><i class="bi bi-list-ul me-2"></i>All Transactions</a></li>
              <li><a class="dropdown-item" href="#"><i class="bi bi-check-circle me-2"></i>Successful</a></li>
              <li><a class="dropdown-item" href="#"><i class="bi bi-x-circle me-2"></i>Unsuccessful</a></li>
              <li><a class="dropdown-item" href="#"><i class="bi bi-exclamation-circle me-2"></i>Partial</a></li>
              <li><a class="dropdown-item" href="#"><i class="bi bi-clock me-2"></i>Pending</a></li>
            </ul>
          </div>
        </div>

        <div class="transaction-list">
          <?php if (empty($payments)): ?>
            <div class="empty-state">
              <i class="bi bi-receipt"></i>
              <h4>No Payment History</h4>
              <p>You haven't made any payments yet. Your payment history will appear here once you start making transactions.</p>
            </div>
          <?php else: ?>
            <?php
            $currentDate = '';
            $groupOpen = false;
            foreach ($payments as $index => $payment):
                $paymentDate = date('F d, Y', strtotime($payment['paid_at'] ?? $payment['created_at']));
                $time = date('h:i A', strtotime($payment['paid_at'] ?? $payment['created_at']));
                if ($paymentDate !== $currentDate):
                    if ($groupOpen):
                        // close previous date group
                        ?>
                        </div>
                        <?php
                    endif;
                    $currentDate = $paymentDate;
                    $groupOpen = true;
            ?>
              <div class="date-group" data-date="<?= esc($currentDate) ?>">
                <div class="history-subheader">
                  <i class="bi bi-calendar-event"></i>
                  <?= $currentDate ?>
                </div>
            <?php endif;

                  // Normalize status from possible fields and detect partial payments
                  $rawStatus = strtolower(trim(
                    $payment['status'] ??
                    $payment['payment_status'] ??
                    $payment['txn_status'] ??
                    $payment['transaction_status'] ??
                    ''
                  ));
                  $totalDue = floatval($payment['bill_amount'] ?? $payment['total_amount'] ?? $payment['amount_due'] ?? 0);
                  $paidAmount = floatval($payment['amount'] ?? 0);
                  // Improved partial payment detection: show as partial if paidAmount < totalDue and paidAmount > 0
                  $isPartial = (strpos($rawStatus, 'partial') !== false) || ($totalDue > 0 && $paidAmount > 0 && $paidAmount < $totalDue);
                  // If status is paid but paidAmount < totalDue, treat as partial
                  if ($rawStatus === 'paid' && $totalDue > 0 && $paidAmount > 0 && $paidAmount < $totalDue) {
                    $isPartial = true;
                  }

                  if ($isPartial) {
                    $amount_class = 'amount-partial';
                    $status_class = 'status-partial';
                    $amount_prefix = '+';
                    $icon_class = 'icon-pending';
                    $icon = 'bi-exclamation-circle';
                    $label = 'Partial';
                  } elseif ($rawStatus === 'paid') {
                    $amount_class = 'amount-credit';
                    $status_class = 'status-paid';
                    $amount_prefix = '+';
                    $icon_class = 'icon-paid';
                    $icon = 'bi-check-circle';
                    $label = 'Paid';
                  } elseif ($rawStatus === 'pending') {
                    $amount_class = 'amount-pending';
                    $status_class = 'status-pending';
                    $amount_prefix = '';
                    $icon_class = 'icon-pending';
                    $icon = 'bi-clock';
                    $label = 'Pending';
                  } else {
                    $amount_class = 'amount-debit';
                    $status_class = 'status-failed';
                    $amount_prefix = '-';
                    $icon_class = 'icon-failed';
                    $icon = 'bi-x-circle';
                    $label = 'Failed';
                  }

                  $modalId = 'modalT' . $payment['id'];
                  // Normalize method label for display
                  $rawMethod = strtolower(trim($payment['method'] ?? ''));
                  if ($rawMethod === 'manual') {
                    $methodLabel = 'Manual Payment';
                  } elseif ($rawMethod === 'offline' || $rawMethod === 'over the counter') {
                    $methodLabel = 'Over the Counter';
                  } else {
                    $methodLabel = 'Online Payment';
                  }
            ?>
              <div class="transaction-item" data-bs-toggle="modal" data-bs-target="#<?= $modalId ?>" data-aos="fade-up" data-aos-delay="<?= $index * 50 ?>">
                <div class="transaction-details">
                  <div class="transaction-icon <?= $icon_class ?>">
                    <i class="bi <?= $icon ?>"></i>
                  </div>
                  <div class="transaction-info">
                    <div class="transaction-time">
                      <i class="bi bi-clock"></i>
                      <?= esc($time) ?>
                    </div>
                    <div class="transaction-name">
                      <?= esc($methodLabel) ?>
                    </div>
                    <span class="transaction-status <?= $status_class ?>">
                      <i class="bi <?= $icon ?>"></i>
                      <?= $label ?>
                    </span>
                  </div>
                </div>
                <div class="transaction-amount">
                  <div class="amount-value <?= $amount_class ?>">
                    ₱<?= number_format($payment['amount'], 2) ?>
                  </div>
                  <div class="amount-label">Bill Payment</div>
                </div>
              </div>
            <?php endforeach; ?>
            <?php if ($groupOpen): ?>
              </div>
            <?php endif; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </main>

  <!-- MODALS: Move all modals here, outside the history-container and main -->
  <?php if (!empty($payments)): ?>
    <?php
    foreach ($payments as $index => $payment):
          // Normalize status from possible fields and detect partial payments for modal
          $rawStatus = strtolower(trim(
            $payment['status'] ??
            $payment['payment_status'] ??
            $payment['txn_status'] ??
            $payment['transaction_status'] ??
            ''
          ));
        $totalDue = floatval($payment['bill_amount'] ?? $payment['total_amount'] ?? $payment['amount_due'] ?? 0);
        $paidAmount = floatval($payment['amount'] ?? 0);
          // Improved partial payment detection for modal
          $isPartial = (strpos($rawStatus, 'partial') !== false) || ($totalDue > 0 && $paidAmount > 0 && $paidAmount < $totalDue);
          if ($rawStatus === 'paid' && $totalDue > 0 && $paidAmount > 0 && $paidAmount < $totalDue) {
            $isPartial = true;
          }

          if ($isPartial) {
            $status_class = 'status-partial';
            $icon = 'bi-exclamation-circle';
            $label = 'Partial';
          } elseif ($rawStatus === 'paid') {
            $status_class = 'status-paid';
            $icon = 'bi-check-circle';
            $label = 'Paid';
          } elseif ($rawStatus === 'pending') {
            $status_class = 'status-pending';
            $icon = 'bi-clock';
            $label = 'Pending';
          } else {
            $status_class = 'status-failed';
            $icon = 'bi-x-circle';
            $label = 'Failed';
          }
          $modalId = 'modalT' . $payment['id'];
          // Normalize method label for modal display as well
          $rawMethod = strtolower(trim($payment['method'] ?? ''));
          if ($rawMethod === 'manual') {
            $methodLabel = 'Manual Payment';
          } elseif ($rawMethod === 'offline' || $rawMethod === 'over the counter') {
            $methodLabel = 'Over the Counter';
          } else {
            $methodLabel = 'Online Payment';
          }
        $paymentDate = date('F d, Y', strtotime($payment['paid_at'] ?? $payment['created_at']));
        $time = date('h:i A', strtotime($payment['paid_at'] ?? $payment['created_at']));
        $remaining = ($isPartial && $totalDue > 0) ? number_format(max(0, $totalDue - $paidAmount), 2) : null;
    ?>
    <div class="modal fade" id="<?= $modalId ?>" tabindex="-1" aria-labelledby="<?= $modalId ?>Label" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="<?= $modalId ?>Label">
              <i class="bi bi-receipt-cutoff"></i>
              Payment Receipt
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body" id="receiptContent<?= $payment['id'] ?>">
            <div class="receipt-item">
              <span class="receipt-label">
                <i class="bi bi-clock text-primary"></i>
                Transaction Time
              </span>
              <span class="receipt-value"><?= esc($time . ', ' . $paymentDate) ?></span>
            </div>
            <div class="receipt-item">
              <span class="receipt-label">
                <i class="bi bi-credit-card text-primary"></i>
                Payment Method
              </span>
              <span class="receipt-value"><?= esc($methodLabel) ?></span>
            </div>
            <div class="receipt-item">
              <span class="receipt-label">
                <i class="bi bi-file-earmark-text text-primary"></i>
                Bill Number
              </span>
              <span class="receipt-value"><?= esc($payment['bill_no'] ?? 'N/A') ?></span>
            </div>
            <div class="receipt-item">
              <span class="receipt-label">
                <i class="bi bi-calendar-check text-primary"></i>
                Due Date
              </span>
              <span class="receipt-value"><?= esc($payment['due_date'] ? date('M d, Y', strtotime($payment['due_date'])) : 'N/A') ?></span>
            </div>
            <div class="receipt-item">
              <span class="receipt-label">
                <i class="bi bi-currency-dollar text-primary"></i>
                Amount Paid
              </span>
              <span class="receipt-value">₱<?= number_format($payment['amount'], 2) ?></span>
            </div>
            <?php if ($totalDue > 0): ?>
            <div class="receipt-item">
              <span class="receipt-label">
                <i class="bi bi-bank text-primary"></i>
                Original Bill Amount
              </span>
              <span class="receipt-value">₱<?= number_format($totalDue, 2) ?></span>
            </div>
            <?php endif; ?>
            <?php if ($remaining !== null): ?>
            <div class="receipt-item">
              <span class="receipt-label">
                <i class="bi bi-arrow-down-right text-primary"></i>
                Remaining Balance
              </span>
              <span class="receipt-value">₱<?= $remaining ?></span>
            </div>
            <?php endif; ?>
            <div class="receipt-item">
              <span class="receipt-label">
                <i class="bi <?= $icon ?> text-primary"></i>
                Payment Status
              </span>
              <span class="receipt-value">
                <span class="transaction-status <?= $status_class ?>">
                  <i class="bi <?= $icon ?>"></i>
                  <?= $label ?>
                </span>
              </span>
            </div>
            <div class="receipt-item">
              <span class="receipt-label">
                <i class="bi bi-hash text-primary"></i>
                Reference ID
              </span>
              <span class="receipt-value"><?= esc($payment['reference_number']) ?></span>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-modal btn-close-modal" data-bs-dismiss="modal">
              <i class="bi bi-x me-2"></i>Close
            </button>
            <button type="button" class="btn btn-modal btn-download" onclick="downloadReceipt(<?= $payment['id'] ?>, this)">
              <i class="bi bi-download me-2"></i>Download Receipt
            </button>
          </div>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  <?php endif; ?>

  <div class="content-footer-spacer"></div>
  <?= $this->include('Users/footer') ?>
  <a href="#" id="scrollTop" class="scroll-top">
    <i class="bi bi-arrow-up"></i>
  </a>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

  <script>
  // Initialize AOS animations
  AOS.init({
      duration: 800,
      easing: 'ease-in-out-cubic',
      once: true
  });

  // Enhanced filter functionality (maintaining original logic)
  document.querySelectorAll('.status-dropdown .dropdown-item').forEach(item => {
    item.addEventListener('click', function(e) {
      e.preventDefault();

      const selected = this.textContent.trim();
      const dropdownBtn = document.getElementById('statusDropdown');
      
      // Update button text with icon
      const icon = this.querySelector('i').className;
      dropdownBtn.innerHTML = `<i class="${icon} me-2"></i>${selected}`;

      // Reset visibility with animation
      document.querySelectorAll('.transaction-item').forEach((el, index) => {
        el.style.display = 'flex';
        el.style.animation = `fadeInUp 0.3s ease forwards ${index * 0.05}s`;
      });
      // Make sure date groups are visible before filtering
      document.querySelectorAll('.date-group').forEach(g => g.style.display = 'block');

      // Filtering logic (maintaining original functionality)
      if (selected.includes('Successful')) {
        document.querySelectorAll('.transaction-item').forEach(el => {
          if (el.querySelector('.transaction-amount .amount-value').classList.contains('amount-debit') || 
              el.querySelector('.transaction-amount .amount-value').classList.contains('amount-pending')) {
            el.style.display = 'none';
          }
        });
      } else if (selected.includes('Partial')) {
        document.querySelectorAll('.transaction-item').forEach(el => {
          const amountEl = el.querySelector('.transaction-amount .amount-value');
          const statusEl = el.querySelector('.transaction-status');
          const isPartial = (amountEl && amountEl.classList.contains('amount-partial')) || (statusEl && statusEl.textContent && statusEl.textContent.toLowerCase().includes('partial'));
          if (!isPartial) {
            el.style.display = 'none';
          }
        });
      } else if (selected.includes('Unsuccessful')) {
        document.querySelectorAll('.transaction-item').forEach(el => {
          const amountEl = el.querySelector('.transaction-amount .amount-value');
          // Hide successful, pending, and partial payments
          if (
            amountEl.classList.contains('amount-credit') ||
            amountEl.classList.contains('amount-pending') ||
            amountEl.classList.contains('amount-partial')
          ) {
            el.style.display = 'none';
          }
        });
      } else if (selected.includes('Pending')) {
        document.querySelectorAll('.transaction-item').forEach(el => {
          if (!el.querySelector('.transaction-amount .amount-value').classList.contains('amount-pending')) {
            el.style.display = 'none';
          }
        });
      } 

      // Hide date groups that have no visible transactions after filtering
      document.querySelectorAll('.date-group').forEach(group => {
        const anyVisible = Array.from(group.querySelectorAll('.transaction-item')).some(it => it.style.display !== 'none');
        group.style.display = anyVisible ? 'block' : 'none';
      });

      // Show success message
      showFilterMessage(selected);
    });
  });

  // Enhanced download function (maintaining original functionality)
  function downloadReceipt(id, btn) {
    let button = btn || (typeof event !== 'undefined' ? event.target : null);
    // If an inner element was clicked (icon), climb to nearest button
    if (button && button.tagName && button.tagName.toLowerCase() !== 'button') {
      const maybeBtn = button.closest && button.closest('button');
      if (maybeBtn) {
        button = maybeBtn;
      }
    }
    const originalContent = button ? button.innerHTML : '<i class="bi bi-download me-2"></i>Download Receipt';
    if (button) {
      button.innerHTML = '<i class="bi bi-download me-2"></i>Generating...';
      button.disabled = true;
    }

    const element = document.getElementById('receiptContent' + id);
    html2canvas(element, {
      scale: 2,
      backgroundColor: '#ffffff',
      logging: false,
      useCORS: true
    }).then(canvas => {
      const link = document.createElement('a');
      link.download = 'AquaBill-Receipt-' + id + '.png';
      link.href = canvas.toDataURL('image/png');
      link.click();

      if (button) {
        button.innerHTML = originalContent;
        button.disabled = false;
      }

      showSuccessToast('Receipt downloaded successfully!');
    }).catch(error => {
      console.error('Error generating receipt:', error);
      if (button) {
        button.innerHTML = originalContent;
        button.disabled = false;
      }
      showErrorToast('Failed to generate receipt. Please try again.');
    });
  }

  // Utility functions for user feedback
  function showFilterMessage(filter) {
      const toast = createToast(`Showing ${filter.toLowerCase()} transactions`, 'info');
      document.body.appendChild(toast);
      setTimeout(() => toast.remove(), 3000);
  }

  function showSuccessToast(message) {
      const toast = createToast(message, 'success');
      document.body.appendChild(toast);
      setTimeout(() => toast.remove(), 3000);
  }

  function showErrorToast(message) {
      const toast = createToast(message, 'error');
      document.body.appendChild(toast);
      setTimeout(() => toast.remove(), 3000);
  }

  function createToast(message, type) {
      const toast = document.createElement('div');
      toast.className = 'position-fixed top-0 end-0 p-3';
      toast.style.zIndex = '9999';
      
      const iconMap = {
          'success': 'bi-check-circle',
          'error': 'bi-x-circle',
          'info': 'bi-info-circle'
      };
      
      const colorMap = {
          'success': 'bg-success',
          'error': 'bg-danger',
          'info': 'bg-primary'
      };
      
      toast.innerHTML = `
          <div class="toast show" role="alert">
              <div class="toast-body ${colorMap[type]} text-white rounded-3 d-flex align-items-center">
                  <i class="bi ${iconMap[type]} me-2"></i>
                  ${message}
              </div>
          </div>
      `;
      return toast;
  }

  // Smooth scroll to top
  document.getElementById('scrollTop').addEventListener('click', function(e) {
      e.preventDefault();
      window.scrollTo({ top: 0, behavior: 'smooth' });
  });

  // Show/hide scroll to top button
  window.addEventListener('scroll', function() {
      const scrollTop = document.getElementById('scrollTop');
      if (window.pageYOffset > 300) {
          scrollTop.style.opacity = '1';
          scrollTop.style.visibility = 'visible';
      } else {
          scrollTop.style.opacity = '0';
          scrollTop.style.visibility = 'hidden';
      }
  });

  // Add CSS for fade animation
  const style = document.createElement('style');
  style.textContent = `
      @keyframes fadeInUp {
          from {
              opacity: 0;
              transform: translateY(20px);
          }
          to {
              opacity: 1;
              transform: translateY(0);
          }
      }
  `;
  document.head.appendChild(style);

  // Enhance modal show/hide animations
  document.querySelectorAll('.modal').forEach(modal => {
      modal.addEventListener('shown.bs.modal', function() {
          this.querySelector('.modal-content').style.animation = 'fadeInUp 0.3s ease forwards';
      });
  });

  // Hide any date-group that has no visible transactions (run on load)
  function hideEmptyDateGroups() {
    document.querySelectorAll('.date-group').forEach(group => {
      const anyVisible = Array.from(group.querySelectorAll('.transaction-item')).some(it => {
        // treat elements without inline display as visible (computed style)
        if (it.style.display && it.style.display === 'none') return false;
        const cs = window.getComputedStyle(it);
        return cs.display !== 'none' && cs.visibility !== 'hidden' && cs.opacity !== '0';
      });
      group.style.display = anyVisible ? 'block' : 'none';
    });
  }

  document.addEventListener('DOMContentLoaded', function() {
    // run after a short delay to allow any AOS or scripts to initialize
    setTimeout(function() {
      hideEmptyDateGroups();
      // Hide spinner and show history
      document.getElementById('history-loading').style.display = 'none';
      document.getElementById('main').style.display = 'block';
    }, 300);
  });
  </script>

  </body>
  </html>
