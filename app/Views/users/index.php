<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Dashboard - Aqua Bill</title>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Bootstrap Core CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <!-- AOS Animation -->
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

  <!-- Your Local CSS -->
  <link href="<?= base_url('assets/Users/css/main.css?v=' . time()) ?>" rel="stylesheet">
  <link href="<?= base_url('assets/Users/css/navbar.css?v=' . time()) ?>" rel="stylesheet">

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
      justify-content: between;
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

    /* Responsive Design */
    @media (max-width: 768px) {
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
      
      .stat-card, .quick-action-card {
        margin-bottom: 20px;
      }
      
      .stat-value {
        font-size: 2.2rem;
      }
      
      .action-icon {
        font-size: 50px;
      }

      .bills-section, .chart-container {
        margin-bottom: 20px;
      }
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

  <!-- Disconnection Notice Modal -->
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
    <!-- Hero Dashboard Section -->
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

    <!-- Dashboard Statistics -->
    <section class="stats-section py-5">
      <div class="container">
        <!-- Stats Row -->
        <div class="row g-4" data-aos="fade-up" data-aos-delay="200">
          <div class="col-lg-3 col-md-6">
            <div class="stat-card text-center">
              <div class="stat-icon mx-auto" style="background: linear-gradient(135deg, #48bb78, #38a169);">
                <i class="bi bi-receipt text-white"></i>
              </div>
              <div class="stat-value" id="totalBills">0</div>
              <div class="stat-label">Total Bills</div>
              <div class="stat-trend trend-up" id="totalTrend">
                <i class="bi bi-arrow-up"></i>
                <span>This month</span>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-6">
            <div class="stat-card text-center">
              <div class="stat-icon mx-auto" style="background: linear-gradient(135deg, #ed8936, #dd6b20);">
                <i class="bi bi-clock-history text-white"></i>
              </div>
              <div class="stat-value" id="pendingBills">0</div>
              <div class="stat-label">Pending Bills</div>
              <div class="stat-trend" id="pendingTrend">
                <i class="bi bi-exclamation-circle"></i>
                <span>Needs attention</span>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-6">
            <div class="stat-card text-center">
              <div class="stat-icon mx-auto" style="background: linear-gradient(135deg, #4299e1, #3182ce);">
                <i class="bi bi-check-circle text-white"></i>
              </div>
              <div class="stat-value" id="paidBills">0</div>
              <div class="stat-label">Paid Bills</div>
              <div class="stat-trend trend-up" id="paidTrend">
                <i class="bi bi-check-circle"></i>
                <span>All time</span>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-6">
            <div class="stat-card text-center">
              <div class="stat-icon mx-auto" style="background: linear-gradient(135deg, #9f7aea, #805ad5);">
                <i class="bi bi-currency-dollar text-white"></i>
              </div>
              <div class="stat-value" id="totalAmount">₱0.00</div>
              <div class="small text-muted mt-1" id="balanceLine">Balance: ₱0.00</div>
              <div class="small text-danger fw-semibold mt-1" id="overdueLine"></div>
              <div class="stat-label">Total Amount</div>
              <div class="stat-trend" id="amountTrend">
                <i class="bi bi-graph-up"></i>
                <span>Lifetime</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Actions Row -->
        <div class="row mt-5" data-aos="fade-up" data-aos-delay="300">
          <div class="col-12">
            <h2 class="section-title mb-4">
              <i class="bi bi-lightning-charge text-primary me-2"></i>
              Quick Actions
            </h2>
          </div>
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

        <!-- Recent Bills and Chart Row -->
        <div class="row mt-5 g-4" data-aos="fade-up" data-aos-delay="400">
          <!-- Recent Bills -->
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

          <!-- Payment Overview Chart -->
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

  <!-- Enhanced Payment Modal -->
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

  <!-- Enhanced Support Modal -->
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
          <!-- Contact Options -->
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

          <!-- FAQ Section -->
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

  <!-- Scroll Top -->
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

    // Initialize Chart.js
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
    document.getElementById('currentDate').textContent = now.toLocaleDateString('en-US', options);

    // Load dashboard data with staggered animation
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

    // Add scroll-based animations
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
});

// Load dashboard data with animations
let latestBills = []; // cache latest bills fetched from server (used as fallback)
function loadDashboardData() {
    $.get("<?= base_url('users/getBillingsAjax') ?>", { limit: 100 }, function(bills) {
    // cache bills for fallback disconnection check
    latestBills = Array.isArray(bills) ? bills : [];
        let totalBills = bills.length;
        let pendingBills = bills.filter(b => b.status.toLowerCase() === 'pending').length;
        let paidBills = bills.filter(b => b.status.toLowerCase() === 'paid').length;
        let totalAmount = bills.reduce((sum, bill) => sum + (parseFloat(bill.amount) || 0), 0);
        // compute outstanding and overdue amounts
        const toNumber = v => {
          const n = parseFloat(v);
          return isNaN(n) ? 0 : n;
        };
        const pendingList = bills.filter(b => (b.status || '').toLowerCase() === 'pending');
        const totalOutstanding = pendingList.reduce((s, b) => s + toNumber(b.amount || b.balance || b.due_amount || 0), 0);
        const now = new Date();
        const overdueList = pendingList.filter(b => {
          const d = new Date(b.due_date || b.dueDate || b.due || '');
          return !isNaN(d) && d < now;
        });
        const overdueAmount = overdueList.reduce((s, b) => s + toNumber(b.amount || b.balance || b.due_amount || 0), 0);

        // Animate counters with staggered timing
        setTimeout(() => animateCounter('totalBills', totalBills), 100);
        setTimeout(() => animateCounter('pendingBills', pendingBills), 200);
        setTimeout(() => animateCounter('paidBills', paidBills), 300);
        setTimeout(() => animateCounter('totalAmount', totalAmount, true), 400);

        // Update outstanding/overdue lines in stats
        try {
          const fmt = v => '₱' + Number(v).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
          const outEl = document.getElementById('balanceLine');
          const overEl = document.getElementById('overdueLine');
          if (outEl) outEl.textContent = totalOutstanding > 0 ? `Balance: ${fmt(totalOutstanding)}` : 'Balance: ₱0.00';
          if (overEl) overEl.textContent = overdueAmount > 0 ? `Overdue: ${fmt(overdueAmount)}` : '';
        } catch (e) { console.warn('Error updating outstanding/overdue lines', e); }

        // Update chart with animation
        setTimeout(() => updatePaymentChart(paidBills, pendingBills), 500);

        // Update trend indicators
        updateTrendIndicators(pendingBills, paidBills);

        // Dynamic: check disconnection status after loading bills
        try { fetchDisconnectionStatus(); } catch (e) { console.warn('fetchDisconnectionStatus error', e); }
    }).fail(function() {
        console.error('Failed to load dashboard data');
        showErrorState();
    });
}

// Animate numerical counters
function animateCounter(elementId, target, isCurrency = false) {
    const element = document.getElementById(elementId);
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
            element.textContent = '₱' + current.toLocaleString() + '.00';
        } else {
            element.textContent = current.toLocaleString();
        }

        if (progress < 1) {
            requestAnimationFrame(animate);
        }
    }

    requestAnimationFrame(animate);
}

// Update trend indicators based on data
function updateTrendIndicators(pending, paid) {
    const pendingTrend = document.getElementById('pendingTrend');
    const paidTrend = document.getElementById('paidTrend');
    
    if (pending > 0) {
        pendingTrend.className = 'stat-trend trend-down';
        pendingTrend.innerHTML = '<i class="bi bi-exclamation-triangle"></i><span>Needs attention</span>';
    } else {
        pendingTrend.className = 'stat-trend trend-up';
        pendingTrend.innerHTML = '<i class="bi bi-check-circle"></i><span>All caught up!</span>';
    }
}

// Update payment chart with animation
function updatePaymentChart(paid, pending) {
    const ctx = document.getElementById('paymentChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Paid Bills', 'Pending Bills'],
            datasets: [{
                data: [paid, pending],
                backgroundColor: [
                    'rgba(72, 187, 120, 1)',
                    'rgba(237, 137, 54, 1)'
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
                        padding: 25,
                        font: {
                            family: 'Inter',
                            size: 12,
                            weight: '600'
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleFont: { family: 'Inter', size: 14, weight: '600' },
                    bodyFont: { family: 'Inter', size: 13 },
                    cornerRadius: 10,
                    displayColors: true
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


// Load recent bills with enhanced design and animations
function loadRecentBills() {
    $.get("<?= base_url('users/getBillingsAjax') ?>", { limit: 5 }, function(bills) {
        const container = document.getElementById('recentBillsList');
        
        if (bills.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <i class="bi bi-receipt"></i>
                    <h6>No bills found</h6>
                    <p class="mb-0">Your bills will appear here once they're generated.</p>
                </div>
            `;
            return;
        }

        let html = '';
        bills.forEach((bill, index) => {
            const statusClass = getStatusClass(bill.status);
            const dueDate = new Date(bill.due_date);
            const isOverdue = dueDate < new Date() && bill.status.toLowerCase() === 'pending';
            const statusIcon = getStatusIcon(bill.status, isOverdue);
            
            html += `
                <div class="bill-item animate-on-scroll" style="animation-delay: ${index * 0.1}s;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="bill-number">${bill.bill_no}</div>
                            <div class="bill-month">
                                <i class="bi bi-calendar3 me-1"></i>
                                ${bill.month || 'Current Month'}
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="bill-amount">₱${bill.amount.toLocaleString()}.00</div>
                            <span class="bill-status ${statusClass}">
                                ${statusIcon}
                                ${isOverdue ? 'Overdue' : bill.status}
                            </span>
                        </div>
                    </div>
                    <div class="bill-due-date">
                        <i class="bi bi-clock me-1"></i>
                        Due: ${dueDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
        
        // Trigger animations for new elements
        setTimeout(() => {
            container.querySelectorAll('.animate-on-scroll').forEach(el => {
                el.classList.add('in-view');
            });
        }, 100);

    }).fail(function() {
        document.getElementById('recentBillsList').innerHTML = `
            <div class="empty-state">
                <i class="bi bi-exclamation-triangle text-warning"></i>
                <h6>Unable to load bills</h6>
                <p class="mb-0">Please check your connection and try again.</p>
            </div>
        `;
    });
}

// Helper functions for status styling
function getStatusClass(status) {
    switch(status.toLowerCase()) {
        case 'paid': return 'status-paid';
        case 'pending': return 'status-pending';
        default: return 'status-pending';
    }
}

// Helper function to get status icon HTML
function getStatusIcon(status, isOverdue = false) {
    if (isOverdue) return '<i class="bi bi-exclamation-triangle me-1"></i>';
    
    switch(status.toLowerCase()) {
        case 'paid': return '<i class="bi bi-check-circle me-1"></i>';
        case 'pending': return '<i class="bi bi-clock me-1"></i>';
        default: return '<i class="bi bi-circle me-1"></i>';
    }
}

function showErrorState() {
    ['totalBills', 'pendingBills', 'paidBills'].forEach(id => {
        document.getElementById(id).textContent = '--';
    });
    document.getElementById('totalAmount').textContent = '₱--.--';
}

// Call fetchDisconnectionStatus on load (see bottom where it's also attached)

// Auto-refresh data every 5 minutes with user-friendly notification
let refreshInterval = setInterval(() => {
    loadDashboardData();
    loadRecentBills();
  fetchDisconnectionStatus();
    
    // Show subtle notification
    const toast = document.createElement('div');
    toast.className = 'position-fixed top-0 end-0 p-3';
    toast.style.zIndex = '9999';
    toast.innerHTML = `
        <div class="toast show" role="alert">
            <div class="toast-body bg-primary text-white rounded">
                <i class="bi bi-arrow-clockwise me-2"></i>
                Dashboard refreshed
            </div>
        </div>
    `;
    document.body.appendChild(toast);
    
    setTimeout(() => toast.remove(), 3000);
}, 300000);

// Clean up interval on page unload
window.addEventListener('beforeunload', () => {
    clearInterval(refreshInterval);
});

// Disconnection status check and modal handling
function showModalWithBills(bills) {
  const modalEl = document.getElementById('disconnectionModal');
  const listEl = document.getElementById('disconnectionList');
  if (!modalEl || !listEl) return;

  listEl.innerHTML = '';
  bills.forEach(b => {
    const li = document.createElement('li');
    // support different property names gracefully
    const dueVal = b.due_date || b.dueDate || b.due || '';
    const due = isNaN(new Date(dueVal)) ? new Date() : new Date(dueVal);
    li.textContent = `Bill: ${b.bill_no || b.billNo || 'N/A'} — Due: ${due.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}`;
    li.className = 'mb-1';
    listEl.appendChild(li);
  });

  const now = new Date();
  const anyOverdue = bills.some(b => {
    const d = new Date(b.due_date || b.dueDate || b.due || '');
    return !isNaN(d) && d < now && (b.status || '').toLowerCase() === 'pending';
  });

  document.getElementById('disconnectionMessage').textContent = anyOverdue
    ? 'May overdue bills ka na. Agad na magbayad upang maiwasan ang suspension.'
    : 'May mga bill na malapit nang due. Magbayad sa loob ng isang araw upang maiwasan ang disconnection.';

  const disModal = bootstrap.Modal.getOrCreateInstance(modalEl, { backdrop: 'static', keyboard: false });
  disModal.show();
}

function hideDisconnectionModal() {
  const modalEl = document.getElementById('disconnectionModal');
  if (!modalEl) return;
  const bsModal = bootstrap.Modal.getInstance(modalEl);
  if (bsModal) {
    try { bsModal.hide(); } catch (e) { console.warn('Error hiding modal', e); }
    try { bsModal.dispose(); } catch (e) { /* ignore */ }
  }
  // Remove any leftover backdrop elements and body class to avoid blocking clicks
  setTimeout(() => {
    document.body.classList.remove('modal-open');
    document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
  }, 200);
}

function fetchDisconnectionStatus() {
  fetch('<?= base_url('users/getDisconnectionStatus') ?>', {
    credentials: 'same-origin',
    headers: { 'X-Requested-With': 'XMLHttpRequest' }
  })
  .then(r => {
    const ct = r.headers.get('content-type') || '';
    if (ct.indexOf('application/json') !== -1) return r.json();
    return r.text().then(text => {
      try { return JSON.parse(text); } catch (e) { console.warn('Non-JSON response from getDisconnectionStatus', text); return null; }
    });
  })
  .then(data => {
    console.log('Disconnection status response:', data);

    // If server returned usable bills list, use it
    if (data && Array.isArray(data.bills) && data.bills.length > 0) {
      showModalWithBills(data.bills);
      return;
    }

    // If server returned a count but no bills, or returned nothing, fall back to client-side check using latestBills
    if (Array.isArray(latestBills) && latestBills.length > 0) {
      const now = new Date();
      const overdue = latestBills.filter(b => {
        const due = new Date(b.due_date || b.dueDate || b.due || '');
        return !isNaN(due) && due < now && (b.status || '').toLowerCase() === 'pending';
      });
      if (overdue.length > 0) {
        console.log('Showing disconnection modal from client-side fallback:', overdue);
        showModalWithBills(overdue);
        return;
      }
    }

    // Nothing to show
    hideDisconnectionModal();
  })
  .catch(err => {
    console.error('Disconnection status fetch error', err);
    // fallback to client-side bills
    if (Array.isArray(latestBills) && latestBills.length > 0) {
      const now = new Date();
      const overdue = latestBills.filter(b => {
        const due = new Date(b.due_date || b.dueDate || b.due || '');
        return !isNaN(due) && due < now && (b.status || '').toLowerCase() === 'pending';
      });
      if (overdue.length > 0) {
        console.log('Showing disconnection modal from client-side fallback after fetch error:', overdue);
        showModalWithBills(overdue);
        return;
      }
    }
    hideDisconnectionModal();
  });
}

// call on load:
document.addEventListener('DOMContentLoaded', fetchDisconnectionStatus);

// optionally re-check after successful payment
function onPaymentComplete() {
  fetchDisconnectionStatus();
}
  </script>

  <!-- Profile / Account Status Alerts with Enhanced Design -->
  <?php if (isset($profile_complete) && $profile_complete == 0): ?>
  <script>
    Swal.fire({
      icon: 'info',
      title: 'Complete Your Profile',
      text: 'Please complete your profile to access all features of the system.',
      confirmButtonText: 'Complete Profile',
      allowOutsideClick: false,
      allowEscapeKey: false,
      allowEnterKey: false,
      customClass: {
        popup: 'rounded-4',
        confirmButton: 'btn btn-primary rounded-pill px-4'
      },
      buttonsStyling: false
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = "<?= base_url('users/profile') ?>";
      }
    });
  </script>
  <?php elseif (isset($account_status) && $account_status === 'Pending'): ?>
  <script>
    Swal.fire({
      icon: 'warning',
      title: 'Account Pending Approval',
      text: 'Your account is being reviewed by our team. Some features may be limited until approval.',
      confirmButtonText: 'I Understand',
      allowOutsideClick: false,
      allowEscapeKey: false,
      allowEnterKey: false,
      customClass: {
        popup: 'rounded-4',
        confirmButton: 'btn btn-warning rounded-pill px-4'
      },
      buttonsStyling: false
    });
  </script>
  <?php endif; ?>

</body>
</html>
