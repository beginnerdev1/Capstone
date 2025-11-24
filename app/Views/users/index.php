<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Dashboard - Aqua Bill</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <link href="<?= base_url('assets/Users/css/navbar.css') . '?' . time() ?>" rel="stylesheet">
    <link href="<?= base_url('assets/Users/css/main.css') . '?' . time() ?>" rel="stylesheet">
  <style>
    :root {
      --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
      --warning-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
      --danger-gradient: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
      --info-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
      overflow-x: hidden;
    }
    /* Hero Section with Welcome Message */
    .hero-dashboard {
      background: var(--primary-gradient);
      padding: 100px 0 60px 0;
      position: relative;
      overflow: hidden;
    }
    .hero-dashboard::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 20"><defs><radialGradient id="a" cx="50%" cy="0%" fx="50%" fy="0%"><stop offset="0%" stop-color="%23ffffff" stop-opacity="0.1"/><stop offset="100%" stop-color="%23ffffff" stop-opacity="0"/></radialGradient></defs><circle cx="25" cy="10" r="8" fill="url(%23a)"/><circle cx="75" cy="10" r="8" fill="url(%23a)"/></svg>') repeat;
      opacity: 0.1;
    }
    .welcome-card {
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(20px);
      border-radius: var(--border-radius);
      padding: 40px;
      border: 1px solid rgba(255, 255, 255, 0.2);
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
      position: relative;
      overflow: hidden;
    }
    .welcome-card::before {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
      transform: rotate(45deg);
      animation: shimmer 3s infinite;
    }
    @keyframes shimmer {
      0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
      100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
    }
    .welcome-title {
      font-size: 2.5rem;
      font-weight: 800;
      color: white;
      margin-bottom: 10px;
      background: linear-gradient(45deg, #ffffff, #e3f2fd);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    .welcome-subtitle {
      font-size: 1.2rem;
      color: rgba(255, 255, 255, 0.9);
      font-weight: 400;
    }
    .date-display {
      background: rgba(255, 255, 255, 0.2);
      padding: 15px 20px;
      border-radius: 15px;
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.3);
    }
    /* Enhanced Statistics Cards */
    .stats-section {
      margin-top: -40px;
      position: relative;
      z-index: 10;
    }
    .stat-card {
      background: white;
      border-radius: var(--border-radius);
      padding: 30px;
      box-shadow: var(--card-shadow);
      border: none;
      transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      position: relative;
      overflow: hidden;
      height: 100%;
    }
    .stat-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: var(--primary-gradient);
      transform: scaleX(0);
      transition: transform 0.3s ease;
    }
    .stat-card:hover {
      transform: translateY(-10px) scale(1.02);
      box-shadow: var(--card-shadow-hover);
    }
    .stat-card:hover::before {
      transform: scaleX(1);
    }
    .stat-icon {
      width: 80px;
      height: 80px;
      border-radius: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 36px;
      margin: 0 auto 20px;
      position: relative;
      overflow: hidden;
    }
    .stat-icon::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
      transition: left 0.6s;
    }
    .stat-card:hover .stat-icon::before {
      left: 100%;
    }
    .stat-value {
      font-size: 2.8rem;
      font-weight: 800;
      color: #2d3748;
      margin-bottom: 8px;
      font-family: 'Poppins', sans-serif;
    }
    .stat-label {
      color: #718096;
      font-size: 1rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .stat-trend {
      font-size: 0.9rem;
      margin-top: 10px;
      display: flex;
      align-items: center;
      gap: 5px;
      justify-content: center;
    }
    .trend-up { color: #48bb78; }
    .trend-down { color: #f56565; }
    /* Enhanced Quick Actions */
    .quick-action-card {
      background: white;
      border-radius: var(--border-radius);
      padding: 35px 25px;
      text-align: center;
      box-shadow: var(--card-shadow);
      border: none;
      transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      cursor: pointer;
      text-decoration: none;
      color: inherit;
      display: block;
      position: relative;
      overflow: hidden;
      height: 100%;
    }
    .quick-action-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: var(--primary-gradient);
      opacity: 0;
      transition: opacity 0.3s ease;
    }
    .quick-action-card:hover {
      transform: translateY(-8px) scale(1.05);
      box-shadow: var(--card-shadow-hover);
      color: white;
      text-decoration: none;
    }
    .quick-action-card:hover::before {
      opacity: 1;
    }
    .quick-action-card > * {
      position: relative;
      z-index: 2;
    }
    .action-icon {
      font-size: 60px;
      color: #667eea;
      margin-bottom: 20px;
      transition: all 0.3s ease;
    }
    .quick-action-card:hover .action-icon {
      color: white;
      transform: scale(1.1);
    }
    .action-title {
      font-size: 1.2rem;
      font-weight: 700;
      margin-bottom: 10px;
      transition: color 0.3s ease;
    }
    .quick-action-card:hover .action-title {
      color: white;
    }
    .action-description {
      font-size: 0.9rem;
      color: #718096;
      transition: color 0.3s ease;
    }
    .quick-action-card:hover .action-description {
      color: rgba(255, 255, 255, 0.9);
    }
    /* Enhanced Recent Bills Section */
    .bills-section {
      background: white;
      border-radius: var(--border-radius);
      padding: 35px;
      box-shadow: var(--card-shadow);
      height: 100%;
    }
    .section-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
      padding-bottom: 15px;
      border-bottom: 2px solid #f7fafc;
    }
    .section-title {
      font-size: 1.5rem;
      font-weight: 700;
      color: #2d3748;
      margin: 0;
    }
    .bill-item {
      padding: 20px;
      border-radius: 15px;
      margin-bottom: 15px;
      transition: all 0.3s ease;
      border: 1px solid #f7fafc;
      background: #fafafa;
    }
    .bill-item:hover {
      transform: translateX(10px);
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
      background: white;
      border-color: #667eea;
    }
    .bill-header {
      display: flex;
      justify-content: space-between;
      align-items: start;
      margin-bottom: 15px;
    }
    .bill-number {
      font-size: 1.1rem;
      font-weight: 700;
      color: #2d3748;
      margin-bottom: 5px;
    }
    .bill-month {
      font-size: 0.9rem;
      color: #718096;
    }
    .bill-amount {
      font-size: 1.3rem;
      font-weight: 800;
      color: #2d3748;
      margin-bottom: 8px;
    }
    .bill-status {
      padding: 8px 16px;
      border-radius: 50px;
      font-size: 0.8rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      border: none;
      display: inline-flex;
      align-items: center;
      gap: 5px;
    }
    .status-paid {
      background: linear-gradient(135deg, #48bb78, #38a169);
      color: white;
    }
    .status-pending {
      background: linear-gradient(135deg, #ed8936, #dd6b20);
      color: white;
    }
    .status-partial {
      background: linear-gradient(135deg, #4299e1, #3182ce);
      color: white;
    }
    .status-overdue {
      background: linear-gradient(135deg, #f56565, #e53e3e);
      color: white;
      animation: pulse 2s infinite;
    }
    @keyframes pulse {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.7; }
    }
    .bill-due-date {
      font-size: 0.9rem;
      color: #718096;
      margin-top: 10px;
      display: flex;
      align-items: center;
      gap: 5px;
    }
    /* Enhanced Chart Container */
    .chart-container {
      background: white;
      border-radius: var(--border-radius);
      padding: 35px;
      box-shadow: var(--card-shadow);
      height: 100%;
      position: relative;
    }
    /* Empty State */
    .empty-state {
      text-align: center;
      padding: 60px 20px;
      color: #718096;
    }
    .empty-state i {
      font-size: 4rem;
      margin-bottom: 20px;
      opacity: 0.5;
    }
    /* Loading Animation */
    .loading-container {
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 60px 20px;
    }
    .loading-spinner {
      width: 50px;
      height: 50px;
      border: 4px solid #f3f3f3;
      border-top: 4px solid #667eea;
      border-radius: 50%;
      animation: spin 1s linear infinite;
    }
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    /* Enhanced Modals */
    .modal-content {
      border-radius: var(--border-radius);
      border: none;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
    }
    .modal-header {
      border-bottom: 1px solid #f7fafc;
      padding: 25px 30px 20px;
    }
    .modal-title {
      font-weight: 700;
      color: #2d3748;
    }
    .modal-body {
      padding: 30px;
    }
    /* Enhanced responsive design for 6 cards */
    @media (max-width: 1199px) {
      .stats-section .col-lg-2 {
        flex: 0 0 33.333333%;
        max-width: 33.333333%;
      }
    }
    @media (max-width: 767px) {
      .stats-section .col-lg-2 {
        flex: 0 0 50%;
        max-width: 50%;
      }
            .stat-card {
        padding: 20px 15px;
      }
            .stat-value {
        font-size: 2rem;
      }
            .stat-icon {
        width: 60px;
        height: 60px;
        font-size: 28px;
      }
            .hero-dashboard {
        padding: 80px 0 40px 0;
      }
            .welcome-card {
        padding: 25px;
        text-align: center;
      }
            .welcome-title {
        font-size: 2rem;
      }
            .action-icon {
        font-size: 50px;
      }
      .bills-section, .chart-container {
        margin-bottom: 20px;
      }
    }
    @media (max-width: 575px) {
      .stat-label {
        font-size: 0.85rem;
      }
            .stat-trend {
        font-size: 0.8rem;
      }
            .small {
        font-size: 0.75rem !important;
      }
    }
    /* Animation for cards */
    .stat-card:nth-child(3) {
      animation-delay: 0.25s;
    }
    /* Enhanced card styling for better balance */
    .stat-card {
      min-height: 280px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }
    .stat-card .small {
      line-height: 1.6;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      padding: 0 5px;
    }
    /* Make details line more visible */
    #pendingDetailsLine,
    #partialInPaidLine {
      font-size: 0.8rem;
      font-weight: 500;
    }
    #pendingDetailsLine {
      color: #ed8936 !important;
    }
    #partialInPaidLine {
      color: #4299e1 !important;
    }
    /* Teal gradient for total paid */
    .stat-icon[style*="38b2ac"] {
      box-shadow: 0 8px 16px rgba(56, 178, 172, 0.3);
    }
    /* Enhanced button styling */
    #payNowCardBtn {
      transition: all 0.3s ease;
      box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
    }
    #payNowCardBtn:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(102, 126, 234, 0.5);
    }
    /* Scroll animations */
    .animate-on-scroll {
      opacity: 0;
      transform: translateY(30px);
      transition: all 0.6s ease;
    }
    .animate-on-scroll.in-view {
      opacity: 1;
      transform: translateY(0);
    }
    /* Custom scrollbar */
    ::-webkit-scrollbar {
      width: 8px;
    }
    ::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 10px;
    }
    ::-webkit-scrollbar-thumb {
      background: var(--primary-gradient);
      border-radius: 10px;
    }
    ::-webkit-scrollbar-thumb:hover {
      background: #667eea;
    }
  </style>
</head>
<body>
  <?= $this->include('Users/header') ?>
  <div class="modal fade" id="disconnectionModal" tabindex="-1" aria-labelledby="disconnectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-warning text-dark">
          <h5 class="modal-title" id="disconnectionModalLabel"><i class="bi bi-exclamation-triangle me-2"></i>Babala: Disconnection Notice</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p id="disconnectionMessage" class="mb-3">May overdue bills ka. Kung hindi mababayaran, maaari itong magresulta sa disconnection 1 araw bago ang suspension.</p>
          <ul id="disconnectionList" class="mb-3"></ul>
          <div class="text-end">
            <button id="payNowBtn" class="btn btn-primary me-2">Magbayad Ngayon</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Isara</button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php if (isset($_GET['payment']) && $_GET['payment'] === 'success'): ?>
  <div class="d-flex justify-content-center my-3" data-aos="fade-down">
    <div class="alert alert-success text-center w-50 border-0 rounded-4 shadow">
      <i class="bi bi-check-circle-fill me-2"></i>Payment successful! 💧
    </div>
  </div>
  <?php endif; ?>
  <main class="main">
    <section class="hero-dashboard">
      <div class="container">
        <div class="welcome-card" data-aos="fade-up" data-aos-delay="100">
          <div class="row align-items-center">
            <div class="col-lg-8">
              <h1 class="welcome-title">Welcome back! 👋</h1>
              <p class="welcome-subtitle">Manage your water bills and account with ease</p>
            </div>
            <div class="col-lg-4 text-end">
              <div class="date-display">
                <div class="text-white-50 small mb-1">Today's Date</div>
                <div class="h5 text-white mb-0" id="currentDate"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <br>
    <section class="stats-section py-5">
      <div class="container">
        <div class="row g-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="stat-card text-center">
              <div class="stat-icon mx-auto" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                <i class="bi bi-receipt text-white"></i>
              </div>
              <div class="stat-value" id="totalBills">0</div>
              <div class="stat-label">Total Bills</div>
              <div class="stat-trend" id="totalTrend">
                <i class="bi bi-graph-up"></i>
                <span>All time</span>
              </div>
            </div>
          </div>
          <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="stat-card text-center">
              <div class="stat-icon mx-auto" style="background: linear-gradient(135deg, #ed8936, #dd6b20);">
                <i class="bi bi-clock-history text-white"></i>
              </div>
              <div class="stat-value" id="pendingBills">0</div>
              <div class="small text-muted mt-2" id="pendingDetailsLine">
                <i class="bi bi-info-circle me-1"></i>Details
              </div>
              <div class="stat-label">Pending Bills</div>
              <div class="stat-trend" id="pendingTrend">
                <i class="bi bi-exclamation-circle"></i>
                <span>Needs attention</span>
              </div>
            </div>
          </div>
          <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="stat-card text-center">
              <div class="stat-icon mx-auto" style="background: linear-gradient(135deg, #4299e1, #3182ce);">
                <i class="bi bi-hourglass-split text-white"></i>
              </div>
              <div class="stat-value" id="partialBills">0</div>
              <div class="stat-label">Partial Paid</div>
              <div class="stat-trend" id="partialTrend">
                <i class="bi bi-arrow-right-circle"></i>
                <span>In progress</span>
              </div>
            </div>
          </div>
          <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="stat-card text-center">
              <div class="stat-icon mx-auto" style="background: linear-gradient(135deg, #48bb78, #38a169);">
                <i class="bi bi-check-circle text-white"></i>
              </div>
              <div class="stat-value" id="paidBills">0</div>
              <div class="small text-muted mt-2" id="partialInPaidLine">
                <i class="bi bi-hourglass-split me-1"></i>Partial: 0
              </div>
              <div class="stat-label">Paid Bills</div>
              <div class="stat-trend trend-up" id="paidTrend">
                <i class="bi bi-trophy"></i>
                <span>Completed</span>
              </div>
            </div>
          </div>
          <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="stat-card text-center">
              <div class="stat-icon mx-auto" style="background: linear-gradient(135deg, #38b2ac, #2c7a7b);">
                <i class="bi bi-cash-stack text-white"></i>
              </div>
              <div class="stat-value" id="totalPaidAmount">₱0.00</div>
              <div class="stat-label">Total Paid</div>
              <div class="stat-trend trend-up" id="paidAmountTrend">
                <i class="bi bi-check-circle-fill"></i>
                <span>Lifetime</span>
              </div>
            </div>
          </div>
          <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="stat-card text-center">
              <div class="stat-icon mx-auto" style="background: linear-gradient(135deg, #9f7aea, #805ad5);">
                <i class="bi bi-currency-dollar text-white"></i>
              </div>
              <div class="stat-value" id="totalAmount">₱0.00</div>
              <div class="small text-muted mt-2" id="balanceLine">
                <i class="bi bi-info-circle me-1"></i>Carryover: ₱0.00
              </div>
              <div class="small text-muted mt-1" id="currentBillLine">
                <i class="bi bi-file-earmark-text me-1"></i>Current: ₱0.00
              </div>
              <div class="stat-label">Outstanding</div>
              <div style="margin-top:10px;">
                <button id="payNowCardBtn" class="btn btn-sm btn-primary rounded-pill px-3">
                  <i class="bi bi-credit-card me-1"></i>Pay Now
                </button>
              </div>
            </div>
          </div>
        </div>
        <div class="row mt-5" data-aos="fade-up" data-aos-delay="300">
          <div class="col-12 text-center">
            <h2 class="section-title mb-4">
              <i class="bi bi-lightning-charge text-primary me-2"></i>
              Quick Actions
            </h2>
          </div>
        </div>
        <div class="row justify-content-center g-4" data-aos="fade-up" data-aos-delay="300">
          <div class="col-lg-3 col-md-6 mb-4">
            <a href="javascript:void(0);" id="openPaymentBtn" class="quick-action-card">
              <div class="action-icon">
                <i class="bi bi-credit-card"></i>
              </div>
              <h6 class="action-title">Pay Bills</h6>
              <p class="action-description mb-0">Make payments quickly and securely</p>
            </a>
          </div>
          <div class="col-lg-3 col-md-6 mb-4">
            <a href="<?= base_url('users/history') ?>" class="quick-action-card">
              <div class="action-icon">
                <i class="bi bi-clock-history"></i>
              </div>
              <h6 class="action-title">Payment History</h6>
              <p class="action-description mb-0">View all your past transactions</p>
            </a>
          </div>
          <div class="col-lg-3 col-md-6 mb-4">
            <a href="<?= base_url('users/profile') ?>" class="quick-action-card">
              <div class="action-icon">
                <i class="bi bi-person-circle"></i>
              </div>
              <h6 class="action-title">My Profile</h6>
              <p class="action-description mb-0">Update your account information</p>
            </a>
          </div>
          <div class="col-lg-3 col-md-6 mb-4">
            <a href="#" class="quick-action-card" data-bs-toggle="modal" data-bs-target="#supportModal">
              <div class="action-icon">
                <i class="bi bi-headset"></i>
              </div>
              <h6 class="action-title">Get Support</h6>
              <p class="action-description mb-0">Need help? We're here for you</p>
            </a>
          </div>
        </div>
        <div class="row mt-5 g-4" data-aos="fade-up" data-aos-delay="400">
          <div class="col-lg-8">
            <div class="bills-section">
              <div class="section-header">
                <h3 class="section-title">
                  <i class="bi bi-receipt-cutoff text-primary me-2"></i>
                  Recent Bills
                </h3>
                <a href="<?= base_url('users/history') ?>" class="btn btn-outline-primary btn-sm rounded-pill">
                  <i class="bi bi-arrow-right me-1"></i>
                  View All
                </a>
              </div>
              <div id="recentBillsList">
                <div class="loading-container">
                  <div class="loading-spinner"></div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="chart-container">
              <div class="section-header">
                <h5 class="section-title">
                  <i class="bi bi-pie-chart text-primary me-2"></i>
                  Payment Overview
                </h5>
              </div>
              <div style="position: relative; height: 300px;">
                <canvas id="paymentChart"></canvas>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
  <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="paymentModalLabel">
            <i class="bi bi-credit-card me-2 text-primary"></i>
            Payment Center
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="paymentModalBody">
          <div class="loading-container">
            <div class="loading-spinner"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="supportModal" tabindex="-1" aria-labelledby="supportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="supportModalLabel">
            <i class="bi bi-headset me-2 text-primary"></i>
            Support & Help Center
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row text-center mb-4">
            <div class="col-md-4">
              <div class="p-4 rounded-4 bg-light mb-3">
                <i class="bi bi-telephone display-4 text-primary mb-3"></i>
                <h6 class="fw-bold">Phone Support</h6>
                <p class="text-muted mb-0">+63 xxx-xxx-xxxx</p>
                <small class="text-muted">Mon-Fri 8AM-6PM</small>
              </div>
            </div>
            <div class="col-md-4">
              <div class="p-4 rounded-4 bg-light mb-3">
                <i class="bi bi-envelope display-4 text-primary mb-3"></i>
                <h6 class="fw-bold">Email Support</h6>
                <p class="text-muted mb-0">support@aquabill.com</p>
                <small class="text-muted">24/7 Response</small>
              </div>
            </div>
            <div class="col-md-4">
              <a href="<?= base_url('users/chat') ?>" class="text-decoration-none text-reset">
                <div class="p-4 rounded-4 bg-light mb-3">
                  <i class="bi bi-chat-dots display-4 text-primary mb-3"></i>
                  <h6 class="fw-bold">Live Chat</h6>
                  <p class="text-muted mb-0">Chat with us</p>
                  <small class="text-muted">Available now</small>
                </div>
              </a>
            </div>
          </div>
          <hr class="my-4">
          <h6 class="fw-bold mb-3">
            <i class="bi bi-question-circle me-2"></i>
            Frequently Asked Questions
          </h6>
          <div class="accordion accordion-flush" id="faqAccordion">
            <div class="accordion-item border-0 mb-2 rounded-3">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed rounded-3 fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                  <i class="bi bi-credit-card me-2 text-primary"></i>
                  How do I pay my water bills?
                </button>
              </h2>
              <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                  Simply click on "Pay Bills" in your dashboard to view all outstanding bills. You can pay using credit/debit cards, GCash, or other available payment methods.
                </div>
              </div>
            </div>
            <div class="accordion-item border-0 mb-2 rounded-3">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed rounded-3 fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                  <i class="bi bi-calendar-event me-2 text-primary"></i>
                  When are bills generated?
                </button>
              </h2>
              <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                  Bills are automatically generated at the beginning of each month based on your previous month's water consumption and meter readings.
                </div>
              </div>
            </div>
            <div class="accordion-item border-0 mb-2 rounded-3">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed rounded-3 fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                  <i class="bi bi-shield-check me-2 text-primary"></i>
                  Is my payment information secure?
                </button>
              </h2>
              <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                  Yes! We use industry-standard encryption and security measures to protect all your payment information. Your data is safe with us.
                </div>
              </div>
            </div>
            <div class="accordion-item border-0 mb-2 rounded-3">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed rounded-3 fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                  <i class="bi bi-clock me-2 text-primary"></i>
                  What if I miss a payment?
                </button>
              </h2>
              <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                  Late payments may incur additional fees. Contact our support team immediately if you're unable to pay on time to discuss payment arrangements.
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?= $this->include('Users/footer') ?>
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center">
    <i class="bi bi-arrow-up-short"></i>
  </a>
  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script src="<?= base_url('assets/Users/js/main.js') ?>"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  

 <script>
// Global cache for latest bills (used as fallback for disconnection check)
let latestBills = [];
// Global instance for Chart.js
window.paymentChartInstance = null;
// Global flag for successful data load
let dataLoadedSuccessfully = false; 

/**
 * Helper function to safely parse a value to a number.
 * @param {*} v - The value to parse.
 * @returns {number} The parsed number, or 0 if parsing fails.
 */
const toNumber = v => {
    const n = parseFloat(v);
    return isNaN(n) ? 0 : n;
};

/**
 * Helper function to get the status class for recent bills.
 * @param {string} status - The bill status string.
 * @returns {string} The CSS class name.
 */
function getStatusClass(status) {
    const s = (status || '').toLowerCase();
    switch (s) {
        case 'paid':
            return 'text-success';
        case 'partial':
            return 'text-info';
        case 'pending':
            return 'text-warning';
        case 'overdue':
            return 'text-danger';
        default:
            return 'text-secondary';
    }
}

/**
 * Helper function to get the status icon for recent bills.
 * @param {string} status - The bill status string.
 * @param {boolean} isOverdue - Flag for overdue status.
 * @returns {string} The Bootstrap icon HTML.
 */
function getStatusIcon(status, isOverdue) {
    const s = (status || '').toLowerCase();
    if (isOverdue) return '<i class="bi bi-calendar-x-fill me-1"></i>';
    switch (s) {
        case 'paid':
            return '<i class="bi bi-check-circle-fill me-1"></i>';
        case 'partial':
            return '<i class="bi bi-hourglass-split me-1"></i>';
        case 'pending':
            return '<i class="bi bi-clock-fill me-1"></i>';
        default:
            return '<i class="bi bi-info-circle-fill me-1"></i>';
    }
}

// Animate numerical counters
function animateCounter(elementId, target, isCurrency = false) {
    const element = document.getElementById(elementId);
    if (!element) return;
    const start = 0;
    const duration = 2000;
    const startTime = performance.now();

    function animate(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);

        // Easing function for smooth animation
        const easedProgress = 1 - Math.pow(1 - progress, 3);
        const current = Math.floor(start + (target - start) * easedProgress);

        if (isCurrency) {
            // Updated currency formatting for better realism
            element.textContent = '₱' + current.toLocaleString('en-PH', { 
                minimumFractionDigits: 2, 
                maximumFractionDigits: 2 
            }).replace(/\.00$/, ''); // Keep .00 until full precision
        } else {
            element.textContent = current.toLocaleString();
        }

        if (progress < 1) {
            requestAnimationFrame(animate);
        } else if (isCurrency) {
             // Final update with full target precision (to include cents)
            const finalValue = Number(target || 0).toLocaleString('en-PH', { 
                minimumFractionDigits: 2, 
                maximumFractionDigits: 2 
            });
            element.textContent = '₱' + finalValue;
        }
    }

    requestAnimationFrame(animate);
}

// ENHANCED: Update trend indicators to account for partial payments
function updateTrendIndicators(pending, paid, partial = 0) {
    const pendingTrend = document.getElementById('pendingTrend');
    const paidTrend = document.getElementById('paidTrend');
    
    if (!pendingTrend || !paidTrend) return; // Guard clause

    const totalUnpaid = pending + partial;
    
    if (totalUnpaid > 0) {
        pendingTrend.className = 'stat-trend trend-down';
        if (partial > 0) {
            // Show both partial and pending count
            pendingTrend.innerHTML = `<i class="bi bi-hourglass-split"></i><span>${partial} partial, ${pending} pending</span>`;
        } else {
            pendingTrend.innerHTML = '<i class="bi bi-exclamation-triangle"></i><span>Needs attention</span>';
        }
    } else {
        pendingTrend.className = 'stat-trend trend-up';
        pendingTrend.innerHTML = '<i class="bi bi-check-circle"></i><span>All caught up!</span>';
    }
    
    // Update paid trend with a specific message
    if (paid > 0) {
        paidTrend.className = 'stat-trend trend-up';
        paidTrend.innerHTML = `<i class="bi bi-trophy"></i><span>${paid} completed</span>`;
    } else {
        paidTrend.className = 'stat-trend trend-neutral';
        paidTrend.innerHTML = `<i class="bi bi-info-circle"></i><span>No paid bills yet</span>`;
    }
}

// ENHANCED: Update payment chart to include partial payments (Consolidated and improved)
function updatePaymentChart(paid, pending, partial = 0) {
    const chartElement = document.getElementById('paymentChart');
    if (!chartElement) return;

    const ctx = chartElement.getContext('2d');
    
    // Destroy existing chart if it exists
    if (window.paymentChartInstance) {
        window.paymentChartInstance.destroy();
    }
    
    window.paymentChartInstance = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Paid Bills', 'Pending Bills', 'Partial Payments'],
            datasets: [{
                data: [paid, pending, partial],
                backgroundColor: [
                    'rgba(72, 187, 120, 1)',    // Green for paid
                    'rgba(237, 137, 54, 1)',     // Orange for pending
                    'rgba(66, 153, 225, 1)'      // Blue for partial
                ],
                borderWidth: 0,
                hoverBorderWidth: 3,
                hoverBorderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: {
                            family: 'Inter',
                            size: 11,
                            weight: '600'
                        },
                        filter: function(item, chart) {
                            // Hide labels if value is 0
                            return chart.data.datasets[0].data[item.index] !== 0;
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleFont: { family: 'Inter', size: 14, weight: '600' },
                    bodyFont: { family: 'Inter', size: 13 },
                    cornerRadius: 10,
                    displayColors: true,
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            },
            animation: {
                animateRotate: true,
                duration: 2000,
                easing: 'easeInOutQuart'
            }
        }
    });
}

// Helper function to update payment card (if you have a custom card component)
function updatePaymentCard(totalPaid, carryover, currentBill, totalOutstanding, partialBills = 0) {
    const paymentCard = document.querySelector('.stat-card .stat-value#totalAmount')?.closest('.stat-card');
    
    if (paymentCard && partialBills > 0) {
        // Find existing indicator or create a new one
        let partialIndicator = paymentCard.querySelector('.partial-indicator');
        if (!partialIndicator) {
            partialIndicator = document.createElement('div');
            partialIndicator.className = 'partial-indicator small text-info mt-2';
            paymentCard.querySelector('.stat-trend')?.before(partialIndicator);
        }
        partialIndicator.innerHTML = `<i class="bi bi-info-circle me-1"></i>${partialBills} bill${partialBills > 1 ? 's' : ''} partially paid`;
    } else if (paymentCard) {
         // Remove indicator if no partial bills
        paymentCard.querySelector('.partial-indicator')?.remove();
    }
}


// Load dashboard data with animations - ENHANCED PARTIAL PAYMENT SUPPORT
function loadDashboardData() {
    $.get("<?= base_url('users/getBillingsAjax') ?>", { limit: 100 }, function(response) {
        dataLoadedSuccessfully = true;
        // Normalize response: support either an array of bills or an object { totals, bills }
        const payload = Array.isArray(response) ? response : (response && Array.isArray(response.bills) ? response.bills : []);
        // cache bills for fallback disconnection check
        latestBills = Array.isArray(payload) ? payload.slice() : [];

        // ENHANCED: Count bills by status separately for better visibility
        let pendingBills = payload.filter(b => {
            const status = (b.status || '').toLowerCase();
            return status === 'pending';
        }).length;

        let partialBills = payload.filter(b => {
            const status = (b.status || '').toLowerCase();
            return status === 'partial';
        }).length;

        let paidBills = payload.filter(b => {
            const status = (b.status || '').toLowerCase();
            return status === 'paid';
        }).length;

        // ENHANCED: Calculate total paid including partial payments
        let totalPaid = payload.reduce((sum, bill) => {
            const status = (bill.status || '').toLowerCase();
            let paidAmount = 0;

            // Priority order for paid amount determination
            if (bill.paid_amount !== undefined && bill.paid_amount !== null) {
                // Use explicit paid_amount field (most reliable)
                paidAmount = toNumber(bill.paid_amount);
            } else if (status === 'paid') {
                // Fully paid bills - use total amount
                paidAmount = toNumber(bill.amount || bill.amount_due || 0);
            } else if (status === 'partial') {
                // Partial payments - calculate from balance
                const totalAmount = toNumber(bill.amount || bill.amount_due || 0);
                const remainingBalance = toNumber(bill.balance || bill.outstanding || 0);
                // Ensure paid amount is positive and not more than original amount
                paidAmount = Math.max(0, totalAmount - remainingBalance);
            }

            return sum + paidAmount;
        }, 0);

        let totalBills = payload.length;

        // --- Totals from Backend (Preferred) or Fallback Calculation ---
        let payloadWrapper = response;
        let pendingList = [];
        let totalAmount = 0; // Total Outstanding to be paid
        let invoiceTotal = 0; // Total original amount of all open bills
        let totalOutstanding = 0;
        let balance = 0; // Carryover/Previous Balance
        let currentBill = 0; // Current Month's new charge

        const totalsObject = (payloadWrapper && payloadWrapper.totals) ? payloadWrapper.totals : 
                            (payloadWrapper && (payloadWrapper.totalPaid !== undefined || 
                             payloadWrapper.total_paid !== undefined || 
                             payloadWrapper.total_outstanding !== undefined || 
                             payloadWrapper.lastBillingBalance !== undefined) ? payloadWrapper : null);

        const explicitLastBill = payloadWrapper && (payloadWrapper.lastBillingBalance ?? 
                                                     payloadWrapper.lastBillBalance ?? 
                                                     payloadWrapper.lastBill_balance ?? 
                                                     payloadWrapper.last_bill_balance ?? 
                                                     payloadWrapper.balance ?? null);

        if (totalsObject) {
            const t = totalsObject;
            // Use server-provided total paid if available, otherwise use calculated
            totalPaid = (typeof payloadWrapper.totalPaid === 'number') ? payloadWrapper.totalPaid : 
                        toNumber(t.totalPaid ?? t.total_paid ?? t.paid ?? payloadWrapper.totalPaid ?? totalPaid);

            balance = (typeof payloadWrapper.carryover === 'number') ? payloadWrapper.carryover
                    : (typeof payloadWrapper.lastBillingBalance === 'number') ? payloadWrapper.lastBillingBalance
                    : (typeof t.lastBillingBalance === 'number') ? t.lastBillingBalance
                    : toNumber(explicitLastBill ?? t.balance ?? t.carried ?? 0);

            currentBill = (typeof t.currentBill === 'number') ? t.currentBill : 
                          toNumber(t.currentBill ?? t.current_bill ?? t.current ?? 0);
            totalOutstanding = (typeof t.totalOutstanding === 'number') ? t.totalOutstanding : 
                               toNumber(t.totalOutstanding ?? t.total_outstanding ?? t.outstanding ?? (balance + currentBill));
            totalAmount = totalOutstanding;
            invoiceTotal = Number(balance) + Number(currentBill);
            pendingList = Array.isArray(payloadWrapper.bills) ? payloadWrapper.bills : (Array.isArray(payload) ? payload : []);
        } else if (Array.isArray(payload)) {
            // Recalculate based on list if no totals object provided
            pendingList = payload.filter(b => {
                const s = (b.status || '').toLowerCase();
                // Include pending and partial in what needs attention
                return s === 'pending' || s === 'partial'; 
            });

            totalOutstanding = pendingList.reduce((s, b) => {
                const out = (b.outstanding !== undefined && b.outstanding !== null) ? b.outstanding : 
                            ((b.balance !== undefined && b.balance !== null) ? b.balance : 
                             (b.amount || b.amount_due || 0));
                return s + toNumber(out);
            }, 0);
            totalAmount = totalOutstanding; // Use totalOutstanding as the main outstanding amount
            
            invoiceTotal = payload.reduce((sum, bill) => {
                const original = (bill.amount !== undefined && bill.amount !== null) ? bill.amount : 
                                 (bill.amount_due || 0);
                return sum + toNumber(original);
            }, 0);
            
            balance = 0; // Cannot reliably determine carryover without specific field
            currentBill = 0; // Cannot reliably determine current bill without specific field
        }
        // --- End of Totals Logic ---

        // Animate counters with staggered timing - show each status separately
        setTimeout(() => animateCounter('totalBills', totalBills), 100);
        setTimeout(() => animateCounter('pendingBills', pendingBills), 200);
        setTimeout(() => animateCounter('partialBills', partialBills), 250); // New partial counter
        setTimeout(() => animateCounter('paidBills', paidBills), 300);
        setTimeout(() => animateCounter('totalAmount', totalAmount, true), 400); // Total Outstanding
        
        // Update total paid display separately
        setTimeout(() => {
            const totalPaidEl = document.getElementById('totalPaidAmount');
            if (totalPaidEl) {
                animateCounter('totalPaidAmount', totalPaid, true);
            }
        }, 450);

        // ENHANCED: Update stat card details with better formatting
        try {
            const paidEl = document.getElementById('totalPaidLine');
            const outEl = document.getElementById('balanceLine');
            const curEl = document.getElementById('currentBillLine');
            const invoiceEl = document.getElementById('invoiceTotalLine');
            const overEl = document.getElementById('overdueLine'); // Using this for partial info now

            const fmt = v => Number(v || 0).toLocaleString('en-PH', { 
                minimumFractionDigits: 2, 
                maximumFractionDigits: 2 
            });

            const totalPaidVal = Number(totalPaid || 0);
            let carryoverVal = toNumber(balance);
            let currentBillVal = toNumber(currentBill);
            let invoiceTotalVal = toNumber(invoiceTotal);
            let totalOutstandingVal = toNumber(totalOutstanding);

            // Update UI elements with formatted values
            if (paidEl) {
                paidEl.innerHTML = `<i class="bi bi-check-circle-fill text-success me-1"></i>Total paid: ₱${fmt(totalPaidVal)}`;
            }
            if (outEl) {
                outEl.innerHTML = carryoverVal > 0 
                    ? `<i class="bi bi-arrow-right-circle text-info me-1"></i>Carryover: ₱${fmt(carryoverVal)}` 
                    : '<i class="bi bi-check-circle text-success me-1"></i>Carryover: ₱0.00';
            }
            if (curEl) {
                curEl.innerHTML = currentBillVal > 0 
                    ? `<i class="bi bi-file-earmark-text text-primary me-1"></i>Current bill: ₱${fmt(currentBillVal)}` 
                    : '<i class="bi bi-check-circle text-success me-1"></i>Current bill: ₱0.00';
            }
            if (invoiceEl) {
                invoiceEl.innerHTML = totalOutstandingVal > 0 
                    ? `<i class="bi bi-receipt text-warning me-1"></i>Outstanding Total: ₱${fmt(totalOutstandingVal)}` 
                    : '<i class="bi bi-check-circle text-success me-1"></i>Outstanding: ₱0.00';
            }
            
            // ENHANCED: Show partial payment info if applicable
            if (partialBills > 0 && overEl) {
                overEl.innerHTML = `<i class="bi bi-hourglass-split me-1"></i>${partialBills} bill${partialBills > 1 ? 's' : ''} with partial payment`;
                overEl.className = 'small text-info fw-semibold mt-1';
            } else if (overEl) {
                overEl.textContent = '';
            }

            // Store global totals for payment processing
            window.dashboardTotals = {
                totalPaid: totalPaidVal,
                carryover: carryoverVal,
                balance: carryoverVal,
                lastBillingBalance: carryoverVal,
                currentBill: currentBillVal,
                totalOutstanding: totalOutstandingVal,
                invoiceTotal: invoiceTotalVal,
                partialBills: partialBills // Store partial bill count
            };

            window.preselectedPaymentAmount = Number(totalOutstandingVal) || 0;

            try {
                const openPaymentBtn = document.getElementById('openPaymentBtn');
                if (openPaymentBtn) openPaymentBtn.dataset.defaultAmount = String(window.preselectedPaymentAmount);
            } catch (e) { }

            try { 
                updatePaymentCard(totalPaidVal, carryoverVal, currentBillVal, totalOutstandingVal, partialBills); 
            } catch (e) { }

        } catch (e) {
            console && console.warn && console.warn('Error updating outstanding/overdue lines', e);
        }

        // ENHANCED: Update chart to show partial payments as separate category
        setTimeout(() => updatePaymentChart(paidBills, pendingBills, partialBills), 500);
        updateTrendIndicators(pendingBills, paidBills, partialBills);

        // Assume fetchDisconnectionStatus exists and is needed
        try { fetchDisconnectionStatus(); } catch (e) { console.warn('fetchDisconnectionStatus error', e); }
    }).fail(function() {
        console.error('Failed to load dashboard data');
        showErrorState();
    });
}

// Load recent bills with enhanced design and animations
function loadRecentBills() {
    $.get("<?= base_url('users/getBillingsAjax') ?>", { limit: 5 }, function(response) {
        const container = document.getElementById('recentBillsList');
        if (!container) return; // Exit if container doesn't exist
        const bills = Array.isArray(response) ? response : (response && Array.isArray(response.bills) ? response.bills : []);
            
        if (bills.length === 0) {
            container.innerHTML = `
                <div class="empty-state p-4 text-center text-muted">
                    <i class="bi bi-receipt display-4"></i>
                    <h6 class="mt-2">No bills found</h6>
                    <p class="mb-0">Your bills will appear here once they're generated.</p>
                </div>
            `;
            return;
        }

        let html = '';
        bills.forEach((bill, index) => {
            const status = (bill.status || '').toLowerCase();
            const statusClass = getStatusClass(bill.status);
            const dueDate = bill.due_date ? new Date(bill.due_date) : null;
            const isOverdue = dueDate && (dueDate < new Date()) && status === 'pending';
            const statusIcon = getStatusIcon(bill.status, isOverdue);

            // Prefer server-provided `outstanding` when available, then balance, then amount_due
            const outstanding = (bill.outstanding !== undefined && bill.outstanding !== null)
                ? toNumber(bill.outstanding)
                : ((bill.balance !== undefined && bill.balance !== null) ? toNumber(bill.balance) : toNumber(bill.amount || bill.amount_due || 0));
            const original = toNumber(bill.amount || bill.amount_due || outstanding || 0);

            // Format numbers safely
            const fmt = v => {
                const n = Number(v) || 0;
                return n.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            };

            html += `
                <div class="bill-item animate-on-scroll card mb-2 shadow-sm" data-aos="fade-up" data-aos-delay="${index * 100}">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div class="flex-grow-1">
                            <div class="fw-bold text-dark bill-number">${bill.bill_no}</div>
                            <div class="small text-muted bill-month">
                                <i class="bi bi-calendar3 me-1"></i>
                                ${bill.billing_month || 'Current Month'}
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="h5 mb-0 text-primary bill-amount">₱${fmt(outstanding)}</div>
                            <div style="font-size:0.85rem; color:#6b7280;">Invoice: ₱${fmt(original)}</div>
                            <span class="badge rounded-pill ${statusClass === 'text-success' ? 'bg-success-subtle text-success' : 
                                                            statusClass === 'text-info' ? 'bg-info-subtle text-info' : 
                                                            'bg-warning-subtle text-warning'} mt-1">
                                ${statusIcon}
                                ${isOverdue ? 'Overdue' : (status === 'partial' ? 'Partial' : (bill.status ? bill.status : 'Pending'))}
                            </span>
                        </div>
                    </div>
                    <div class="card-footer p-2 bg-light d-flex justify-content-between">
                        <small class="text-muted bill-due-date">
                            <i class="bi bi-clock me-1"></i>
                            Due: ${dueDate ? dueDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : 'N/A'}
                        </small>
                        <a href="<?= base_url('users/viewBill/') ?>${bill.bill_id ?? bill.id}" class="btn btn-sm btn-link p-0">
                            View Bill <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            `;
        });
        container.innerHTML = html;
        // Re-observe elements after loading new content
        document.querySelectorAll('.animate-on-scroll').forEach(el => {
            if (!el.classList.contains('in-view')) observer.observe(el);
        });
    }).fail(function() {
        document.getElementById('recentBillsList').innerHTML = `
            <div class="empty-state p-4 text-center text-danger">
                <i class="bi bi-x-octagon display-4"></i>
                <h6 class="mt-2">Failed to Load Bills</h6>
                <p class="mb-0">There was an error connecting to the server. Please refresh.</p>
            </div>
        `;
    });
}

// Placeholder for missing function
function fetchDisconnectionStatus() {
    // Check disconnection status logic here (using latestBills cache)
    if (latestBills.length > 0) {
        // Example check: if any bill is overdue, show disconnection warning
        const isOverdue = latestBills.some(bill => {
            const status = (bill.status || '').toLowerCase();
            const dueDate = bill.due_date ? new Date(bill.due_date) : null;
            return status === 'pending' && dueDate && (dueDate < new Date());
        });

        const disconnectionModal = document.getElementById('disconnectionModal');
        if (isOverdue && disconnectionModal) {
            // Show modal if needed, assuming it's hidden by default
            // new bootstrap.Modal(disconnectionModal).show();
            console.log('Overdue bill detected. Disconnection warning logic triggered.');
        }
    }
}
function showErrorState() {
     // Implement error state UI logic here
     console.error('Displaying generic error state for dashboard data.');
}


// --- Main DOM Ready Function ---
$(function () {
    // Initialize AOS animations
    AOS.init({
        duration: 800,
        easing: 'ease-in-out',
        once: true
    });

    // Update current date with animation
    const now = new Date();
    const options = { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    };
    const currentDateEl = document.getElementById('currentDate');
    if (currentDateEl) {
        currentDateEl.textContent = now.toLocaleDateString('en-US', options);
    }

    // Load dashboard data with staggered animation
    // The previous updatePaymentChart and updateTrendIndicators functions were removed here
    setTimeout(() => loadDashboardData(), 500);
    setTimeout(() => loadRecentBills(), 800);

    // Hook pay now button in disconnection modal to open payment flow
    document.getElementById('payNowBtn')?.addEventListener('click', function (e) {
        e.preventDefault();
        // Trigger the existing payment flow if available
        const openPayment = document.getElementById('openPaymentBtn');
        if (openPayment) openPayment.click();
        // Close modal after initiating
        const modalEl = document.getElementById('disconnectionModal');
        if (modalEl) {
            const inst = bootstrap.Modal.getInstance(modalEl) || bootstrap.Modal.getOrCreateInstance(modalEl);
            try { inst.hide(); } catch (e) { console.warn('Error hiding disconnection modal', e); }
            // ensure any leftover backdrop or modal-open state is removed
            setTimeout(() => {
                document.body.classList.remove('modal-open');
                document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
            }, 200);
        }
    });

    // Enhanced payment button handler
    $("#openPaymentBtn").on("click", function (e) {
        e.preventDefault();
        
        // Add loading state
        const originalContent = $(this).html();
        $(this).addClass('pointer-events-none opacity-75');
        
        $.get("<?= base_url('users/getAccountStatus') ?>", function(data) {
            const status = (data.account_status || '').toLowerCase();

            const swalClasses = {
                popup: 'rounded-4',
                confirmButton: 'btn btn-primary rounded-pill px-4',
                cancelButton: 'btn btn-secondary rounded-pill px-4'
            };
            
            // Re-use logic from the original script
            switch (status) {
                case 'approved':
                    $("#paymentModalBody").load("<?= base_url('users/payments') ?>", function () {
                        new bootstrap.Modal(document.getElementById("paymentModal")).show();
                    });
                    break;

                case 'pending':
                case 'pending approval':
                    Swal.fire({
                        icon: 'warning',
                        title: 'Account Pending Approval',
                        text: 'Your account is still pending approval. Payments will be available once your account is approved.',
                        confirmButtonText: 'Understood',
                        customClass: swalClasses,
                        buttonsStyling: false
                    });
                    break;

                case 'rejected':
                    Swal.fire({
                        icon: 'error',
                        title: 'Account Rejected',
                        html: 'Your account application was rejected. Please contact support for more details.',
                        showCancelButton: true,
                        confirmButtonText: 'Contact Support',
                        cancelButtonText: 'Close',
                        customClass: swalClasses,
                        buttonsStyling: false
                    }).then((res) => {
                        if (res.isConfirmed) {
                            new bootstrap.Modal(document.getElementById('supportModal')).show();
                        }
                    });
                    break;

                case 'inactive':
                    Swal.fire({
                        icon: 'info',
                        title: 'Account Inactive',
                        text: 'Your account is currently inactive. Contact support to reactivate your account.',
                        confirmButtonText: 'Contact Support',
                        customClass: swalClasses,
                        buttonsStyling: false
                    }).then((res) => {
                        if (res.isConfirmed) {
                            new bootstrap.Modal(document.getElementById('supportModal')).show();
                        }
                    });
                    break;

                case 'suspended':
                    Swal.fire({
                        icon: 'error',
                        title: 'Account Suspended',
                        text: 'Your account has been suspended. Contact support to resolve this.',
                        confirmButtonText: 'Contact Support',
                        customClass: swalClasses,
                        buttonsStyling: false
                    }).then((res) => {
                        if (res.isConfirmed) {
                            new bootstrap.Modal(document.getElementById('supportModal')).show();
                        }
                    });
                    break;

                default:
                    Swal.fire({
                        icon: 'info',
                        title: 'Account Status',
                        text: 'Current status: ' + (data.account_status || 'Unknown') + '.',
                        confirmButtonText: 'OK',
                        customClass: swalClasses,
                        buttonsStyling: false
                    });
                    break;
            }
        }).fail(function() {
            Swal.fire({
                icon: 'error',
                title: 'Connection Error',
                text: 'Unable to check account status. Please try again.',
                customClass: {
                    popup: 'rounded-4',
                    confirmButton: 'btn btn-danger rounded-pill px-4'
                },
                buttonsStyling: false
            });
        }).always(function() {
            // Remove loading state
            $("#openPaymentBtn").removeClass('pointer-events-none opacity-75');
        });
    });

    // Add scroll-based animations (retains original logic)
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('in-view');
            }
        });
    }, observerOptions);

    document.querySelectorAll('.animate-on-scroll').forEach(el => {
        observer.observe(el);
    });

    // Wire Pay Now button in the stats card to open the payment flow
    const payNowCardBtn = document.getElementById('payNowCardBtn');
    if (payNowCardBtn) {
        payNowCardBtn.addEventListener('click', function (e) {
            e.preventDefault();
            const openPayment = document.getElementById('openPaymentBtn');
            if (openPayment) openPayment.click();
        });
    }

    // Initialize Bootstrap tooltips for any info icons
    try {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function (el) { new bootstrap.Tooltip(el); });
    } catch (err) { /* ignore if bootstrap not present */ }
});

// The two duplicate functions (animateCounter and loadRecentBills) were also updated and consolidated above.
// The duplicated updateTrendIndicators and updatePaymentChart functions at the end of the original script have been removed.

</script>

</body>
</html>
