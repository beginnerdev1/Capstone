<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>SB Admin 2 - Dashboard</title>
    <!-- FontAwesome CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* ===================================
           TYPOGRAPHY SYSTEM
           =================================== */
        
        /* Font Family Variables */
        :root {
            /* Font Families */
            --font-primary: 'Poppins', sans-serif;
            --font-secondary: 'Nunito', sans-serif;
            --font-fallback: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;

            /* Font Weights */
            --fw-light: 300;
            --fw-normal: 400;
            --fw-medium: 500;
            --fw-semibold: 600;
            --fw-bold: 700;
            --fw-extrabold: 800;
            --fw-black: 900;

            /* Font Sizes */
            --fs-xs: 0.65rem;      /* Extra small - labels, badges */
            --fs-sm: 0.75rem;      /* Small - headings, timestamps */
            --fs-base: 0.9rem;     /* Base - body text */
            --fs-lg: 0.95rem;      /* Large - default text */
            --fs-xl: 1.1rem;       /* Extra large - subheadings */
            --fs-2xl: 1.3rem;      /* 2XL - brand text */
            --fs-3xl: 1.5rem;      /* 3XL - page titles */
            --fs-4xl: 1.75rem;     /* 4XL - stat values */
            --fs-5xl: 2rem;        /* 5XL - icons, large displays */

            /* Line Heights */
            --lh-tight: 1.2;
            --lh-normal: 1.5;
            --lh-relaxed: 1.75;

            /* Letter Spacing */
            --ls-tight: -0.5px;
            --ls-normal: 0;
            --ls-wide: 0.5px;
            --ls-wider: 0.8px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-primary);
            font-size: var(--fs-lg);
            font-weight: var(--fw-normal);
            line-height: var(--lh-normal);
            background-color: #f8f9fc;
        }

        /* ===================================
           SIDEBAR TYPOGRAPHY
           =================================== */

        .sidebar {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: 225px;
            overflow-y: auto;
            overflow-x: hidden;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.3) transparent;
        }

        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }

        /* Brand Section */
        .sidebar-brand {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            text-decoration: none;
            color: white;
            transition: all 0.3s ease;
        }

        .sidebar-brand:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .sidebar-brand-icon {
            font-size: var(--fs-5xl);
            margin-right: 0.5rem;
            transform: rotate(-15deg);
        }

        .sidebar-brand-text {
            font-family: var(--font-primary);
            font-size: var(--fs-2xl);
            font-weight: var(--fw-bold);
            letter-spacing: var(--ls-wide);
        }

        .sidebar-brand-text sup {
            font-size: var(--fs-sm);
            font-weight: var(--fw-semibold);
        }

        .sidebar-divider {
            border: 0;
            height: 1px;
            background: rgba(255, 255, 255, 0.2);
            margin: 1rem 0;
        }

        /* Sidebar Heading */
        .sidebar-heading {
            display: block;
            font-family: var(--font-primary);
            font-size: var(--fs-xs);
            font-weight: var(--fw-black);
            text-transform: uppercase;
            letter-spacing: var(--ls-wider);
            color: rgba(255, 255, 255, 0.4);
            padding: 1rem 1.25rem 0.5rem;
            margin-bottom: 0.5rem;
        }

        /* Nav Items */
        .sidebar-nav {
            list-style: none;
            padding: 0;
        }

        .nav-item {
            margin: 0.1rem 0;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 1rem 1.25rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-family: var(--font-primary);
            font-size: var(--fs-lg);
            font-weight: var(--fw-medium);
            transition: all 0.3s ease;
            cursor: pointer;
            border-left: 4px solid transparent;
            position: relative;
        }

        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            padding-left: 1.5rem;
        }

        .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            border-left-color: white;
            font-weight: var(--fw-semibold);
        }

        .nav-link i {
            margin-right: 0.75rem;
            font-size: var(--fs-xl);
        }

        .nav-link .arrow {
            margin-left: auto;
            transition: transform 0.3s ease;
            font-size: var(--fs-base);
        }

        .nav-link[data-toggle="collapse"].collapsed .arrow {
            transform: rotate(0deg);
        }

        .nav-link[data-toggle="collapse"]:not(.collapsed) .arrow {
            transform: rotate(-90deg);
        }

        /* Collapse Menu */
        .collapse-menu {
            background-color: rgba(0, 0, 0, 0.1);
            list-style: none;
            padding: 0.5rem 0;
            margin: 0;
            max-height: 500px;
            overflow: hidden;
        }

        .collapse-menu.show {
            display: block;
        }

        .collapse-header {
            font-family: var(--font-primary);
            font-size: var(--fs-sm);
            font-weight: var(--fw-black);
            text-transform: uppercase;
            letter-spacing: var(--ls-wider);
            color: rgba(255, 255, 255, 0.5);
            padding: 0.5rem 1.25rem;
            margin: 0.5rem 0 0.25rem 0;
        }

        .collapse-item {
            display: block;
            padding: 0.75rem 1.25rem 0.75rem 3rem;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            font-family: var(--font-primary);
            font-size: var(--fs-base);
            transition: all 0.3s ease;
        }

        .collapse-item:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
            padding-left: 3.25rem;
        }

        /* ===================================
           MAIN CONTENT TYPOGRAPHY
           =================================== */

        .main-wrapper {
            display: flex;
            flex-direction: column;
            margin-left: 225px;
            min-height: 100vh;
        }

        /* Topbar */
        .topbar {
            background-color: white;
            position: sticky;
            top: 0;
            z-index: 999;
            border-bottom: 1px solid #e3e6f0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            padding: 1rem 1.5rem;
        }

        .topbar-search {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .topbar-search input {
            background-color: #f8f9fc !important;
            border: 1px solid #e3e6f0 !important;
            color: #5a5c69;
            font-family: var(--font-primary);
            font-size: var(--fs-base);
        }

        .topbar-search input::placeholder {
            color: #a8adb5;
        }

        .topbar-search button {
            background-color: #4e73df;
            border: 1px solid #4e73df;
            color: white;
            padding: 0.45rem 0.75rem;
            border-radius: 0.35rem;
            transition: all 0.3s ease;
        }

        .topbar-search button:hover {
            background-color: #224abe;
            border-color: #224abe;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            margin-left: auto;
        }

        .topbar-icon {
            font-size: var(--fs-xl);
            color: #5a5c69;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }

        .topbar-icon:hover {
            color: #4e73df;
        }

        .badge-counter {
            position: absolute;
            top: -8px;
            right: -8px;
            font-family: var(--font-primary);
            font-size: var(--fs-xs);
            padding: 0.25rem 0.5rem;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            cursor: pointer;
            text-decoration: none;
            color: #5a5c69;
            transition: all 0.3s ease;
        }

        .user-profile:hover {
            color: #4e73df;
        }

        .profile-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: #4e73df;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-family: var(--font-primary);
            font-weight: var(--fw-semibold);
            font-size: var(--fs-xs);
        }

        .profile-name {
            font-family: var(--font-primary);
            font-size: var(--fs-base);
            font-weight: var(--fw-medium);
            display: none;
        }

        @media (min-width: 992px) {
            .profile-name {
                display: block;
            }
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 2rem;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .page-title {
            font-family: var(--font-primary);
            font-size: var(--fs-3xl);
            font-weight: var(--fw-bold);
            color: #2e59d9;
            margin: 0;
        }

        .btn-report {
            background-color: #4e73df;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.35rem;
            font-family: var(--font-primary);
            font-size: var(--fs-base);
            font-weight: var(--fw-medium);
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 2px 4px rgba(78, 115, 223, 0.2);
        }

        .btn-report:hover {
            background-color: #224abe;
            color: white;
            text-decoration: none;
            box-shadow: 0 4px 8px rgba(78, 115, 223, 0.3);
            transform: translateY(-1px);
        }

        /* Stats Cards */
        .stat-card {
            background-color: white;
            border-radius: 0.35rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .stat-card.primary {
            border-left-color: #4e73df;
        }

        .stat-card.success {
            border-left-color: #1cc88a;
        }

        .stat-card.info {
            border-left-color: #36b9cc;
        }

        .stat-card.warning {
            border-left-color: #f6c23e;
        }

        .stat-card-body {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .stat-label {
            font-family: var(--font-primary);
            font-size: var(--fs-xs);
            font-weight: var(--fw-bold);
            text-transform: uppercase;
            letter-spacing: var(--ls-wide);
            color: #4e73df;
            margin-bottom: 0.5rem;
        }

        .stat-card.success .stat-label {
            color: #1cc88a;
        }

        .stat-card.info .stat-label {
            color: #36b9cc;
        }

        .stat-card.warning .stat-label {
            color: #f6c23e;
        }

        .stat-value {
            font-family: var(--font-primary);
            font-size: var(--fs-4xl);
            font-weight: var(--fw-bold);
            color: #2e59d9;
            margin: 0;
        }

        .stat-icon {
            font-size: var(--fs-5xl);
            color: #e3e6f0;
        }

        /* Sidebar Overlay (Mobile) */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        .sidebar-overlay.show {
            display: block;
        }

        .hamburger-btn {
            background: none;
            border: none;
            color: #5a5c69;
            font-size: var(--fs-xl);
            cursor: pointer;
            display: none;
            padding: 0;
            margin-right: 1rem;
        }

        .hamburger-btn:hover {
            color: #4e73df;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .sidebar {
                width: 260px;
                transform: translateX(-100%);
                transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-wrapper {
                margin-left: 0;
            }

            .hamburger-btn {
                display: block;
            }
        }

        @media (max-width: 576px) {
            .sidebar {
                width: 85vw;
                max-width: 280px;
            }

            .main-content {
                padding: 1rem;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .page-title {
                font-size: var(--fs-3xl);
            }

            .btn-report {
                width: 100%;
            }

            .topbar {
                padding: 0.75rem 1rem;
            }

            .topbar-search {
                display: none;
            }

            .profile-name {
                display: none !important;
            }
        }

        /* Footer */
        .footer {
            background-color: white;
            padding: 1.5rem;
            text-align: center;
            border-top: 1px solid #e3e6f0;
            color: #5a5c69;
            font-family: var(--font-primary);
            font-size: var(--fs-base);
        }

        /* Scroll to top button */
        .scroll-top {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 40px;
            height: 40px;
            background-color: #4e73df;
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            display: none;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            z-index: 1001;
            font-size: var(--fs-xl);
        }

        .scroll-top.show {
            display: flex;
        }

        .scroll-top:hover {
            background-color: #224abe;
            transform: translateY(-2px);
        }
    </style>
</head>

<body>

    <!-- Sidebar -->
<div class="sidebar" id="sidebar">
  <a href="#" class="sidebar-brand">
    <div class="sidebar-brand-text">Admin Dashboard</div>
  </a>

  <div class="sidebar-divider"></div>

  <ul class="sidebar-nav">

    <li class="nav-item">
      <a href="#" class="nav-link active">
        <i class="fas fa-tachometer-alt"></i>
        
        <span>Dashboard</span>
      </a>
    </li>

    <div class="sidebar-divider"></div>

    <div class="sidebar-heading">Management</div>

    <li class="nav-item">
      <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseComponents"
         aria-expanded="false" aria-controls="collapseComponents">
        <i class="fas fa-cog"></i>
        <span>User Management</span>
        <i class="fas fa-chevron-right arrow"></i>
      </a>
      <div id="collapseComponents" class="collapse" aria-labelledby="headingComponents" data-parent="#accordionSidebar">
        <div class="py-2 collapse-inner">
          <div class="collapse-header">Components:</div>
                <a href="<?= base_url('admin/registeredUsers') ?>" class="collapse-item ajax-link">All Users</a>
                <a href="<?= base_url('admin/pendingAccounts') ?>" class="collapse-item ajax-link">Verify User</a>

        </div>
      </div>
    </li>

    <li class="nav-item">
      <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities"
         aria-expanded="false" aria-controls="collapseUtilities">
        <i class="fas fa-wrench"></i>
        <span>Utilities</span>
        <i class="fas fa-chevron-right arrow"></i>
      </a>
      <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
        <div class="py-2 collapse-inner">
          <h6 class="collapse-header">Custom Utilities:</h6>
            <a href="<?= base_url('admin/gcash-settings') ?>" class="collapse-item ajax-link">Gcash Settings</a>
            <a href="<?= base_url('admin/transactionRecords') ?>" class="collapse-item ajax-link">Transactions</a>


           <a href="<?= base_url('admin/edit-profile') ?>" class="collapse-item ajax-link">Edit Profile</a>
           <a href="<?= base_url('admin/reports') ?>" class="collapse-item ajax-link">Reports</a>
        </div>
      </div>
    </li>

    <div class="sidebar-divider"></div>

    <div class="sidebar-heading">Addons</div>

    <li class="nav-item">
      <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages"
         aria-expanded="false" aria-controls="collapsePages">
        <i class="fas fa-folder"></i>
        <span>Pages</span>
        <i class="fas fa-chevron-right arrow"></i>
      </a>
      <div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
        <div class="py-2 collapse-inner">
          <div class="collapse-header">Login Screens:</div>
          <a href="#" class="collapse-item">Login</a>
          <a href="#" class="collapse-item">Register</a>
          <a href="#" class="collapse-item">Forgot Password</a>
          <hr style="margin: 0.5rem 0; border: none; height: 1px; background: rgba(255,255,255,0.2);">
          <div class="collapse-header">Other Pages:</div>
          <a href="#" class="collapse-item">404 Page</a>
          <a href="#" class="collapse-item">Blank Page</a>
        </div>
      </div>
    </li>

    <li class="nav-item">
      <a href="#" class="nav-link">
        <i class="fas fa-chart-area"></i>
        <span>Charts</span>
      </a>
    </li>

    <li class="nav-item">
      <a href="#" class="nav-link">
        <i class="fas fa-table"></i>
        <span>Tables</span>
      </a>
    </li>

  </ul>
</div>
    <!-- Sidebar Overlay (Mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    
    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <!-- Topbar -->
        <div class="topbar">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; flex: 1;">
                    <button class="hamburger-btn" id="hamburgerBtn">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="topbar-search">
                        <input type="text" class="form-control" placeholder="Search for..." style="max-width: 300px;">
                        <button type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>

                <div class="topbar-right">
                    <div class="topbar-icon">
                        <i class="fas fa-bell"></i>
                        <span class="badge badge-danger badge-counter">3+</span>
                    </div>
                    <div class="topbar-icon">
                        <i class="fas fa-envelope"></i>
                        <span class="badge badge-danger badge-counter">7</span>
                    </div>
                    <div style="height: 24px; width: 1px; background-color: #e3e6f0; margin: 0 0.5rem;"></div>
                    <a href="#" class="user-profile">
                        <span class="profile-name">Douglas McGee</span>
                     
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div id="mainContent"></div>

        <!-- Footer -->
        <div class="footer">
            <span>Copyright &copy; Your Website 2024</span>
        </div>
    </div>

    <!-- Scroll to Top Button -->
    <button class="scroll-top" id="scrollTop">
        <i class="fas fa-angle-up"></i>
    </button>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <script>
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const hamburgerBtn = document.getElementById('hamburgerBtn');
        const scrollTop = document.getElementById('scrollTop');

        // Toggle Sidebar
        hamburgerBtn.addEventListener('click', function() {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        });

        // Close sidebar when clicking overlay
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });

        // Close sidebar when clicking a nav link
        const navLinks = document.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                if (window.innerWidth <= 992 && !this.getAttribute('data-toggle')) {
                    sidebar.classList.remove('show');
                    overlay.classList.remove('show');
                }
            });
        });

        // Collapse toggle functionality
        const collapseToggles = document.querySelectorAll('[data-toggle="collapse"]');
        collapseToggles.forEach(toggle => {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('data-target'));
                if (target) {
                    target.classList.toggle('show');
                    this.classList.toggle('collapsed');
                }
            });
        });

        // Scroll to top functionality
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                scrollTop.classList.add('show');
            } else {
                scrollTop.classList.remove('show');
            }
        });

        scrollTop.addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        // Close sidebar on window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 992) {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            }
        });

        // Close sidebar on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            }
        });
    </script>
<script>
    // 1. AJAX Link Handler (The Main Fix)
    $(document).on("click", ".ajax-link", function(e) {
        e.preventDefault();
        var url = $(this).attr("href");

        $.ajax({
            url: url,
            type: "GET",
            success: function(data) {
                // 1. Insert the new content
                $("#mainContent").html(data);

                // 2. CHECK & INITIALIZE: This is the critical step for AJAX loads.
                // It checks if the function exists (script must be loaded in the main layout)
                // AND if the required element is in the newly loaded HTML.
                if (typeof initTransactionPage === 'function' && $("#mainContent").find('#paymentTableBody').length) {
                    initTransactionPage();
                    console.log("Transaction page initialized via AJAX success.");
                }
                
    
                 // Initialize Registered Users page when loaded
                if (typeof initRegisteredUsersPage === 'function' && $("#mainContent").find('#usersTable').length) {
                    initRegisteredUsersPage();
                    console.log("✅ Registered Users page initialized after AJAX load.");
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error);
                $("#mainContent").html("<p class='text-danger p-3'>Failed to load content.</p>");
            }
        });
    });

    // 2. Initial Dashboard Load
    $(document).ready(function() {
        $.ajax({
            url: "<?= base_url('admin/dashboard-content') ?>",
            type: "GET",
            success: function(data) {
                $("#mainContent").html(data);
                // Initialize dashboard charts after content loads
                setTimeout(initializeDashboardCharts, 100);
            },
            error: function(xhr, status, error) {
                console.error("Dashboard Load Error:", status, error, xhr.responseText);
                $("#mainContent").html("<p class='text-danger p-3'>Failed to load dashboard. Error: " + error + "</p>");
            }
        });
    });

    // 3. Initialize Dashboard Charts
    function initializeDashboardCharts() {
        const incomeChartCanvas = document.getElementById('incomeChart');
        const pieChartCanvas = document.getElementById('myPieChart');
        
        if (!incomeChartCanvas) {
            console.error('Income chart canvas not found');
            return;
        }

        // Get data from window object (set by dashboard-content.php)
        const months = window.dashboardData?.months || ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        const incomeData = window.dashboardData?.incomeData || [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

        console.log('Initializing charts with data:', { months, incomeData });

        // Income Line Chart
        const incomeCtx = incomeChartCanvas.getContext('2d');
        new Chart(incomeCtx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label: 'Monthly Earnings (₱)',
                    data: incomeData,
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: { size: 12, family: 'Poppins' }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: { size: 14, family: 'Poppins' },
                        bodyFont: { size: 13, family: 'Poppins' },
                        callbacks: {
                            label: function(context) {
                                return 'Earnings: ₱' + context.parsed.y.toLocaleString('en-PH', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₱' + value.toLocaleString();
                            },
                            font: { size: 11, family: 'Poppins' }
                        },
                        grid: { color: 'rgba(0, 0, 0, 0.05)' }
                    },
                    x: {
                        ticks: { font: { size: 11, family: 'Poppins' } },
                        grid: { display: false }
                    }
                }
            }
        });

        // Pie Chart
        if (pieChartCanvas) {
            const pieCtx = pieChartCanvas.getContext('2d');
            new Chart(pieCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Direct', 'Social', 'Referral'],
                    datasets: [{
                        data: [55, 30, 15],
                        backgroundColor: [
                            'rgba(78, 115, 223, 0.8)',
                            'rgba(28, 200, 138, 0.8)',
                            'rgba(54, 185, 204, 0.8)'
                        ],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: { size: 14, family: 'Poppins' },
                            bodyFont: { size: 13, family: 'Poppins' },
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + context.parsed + '%';
                                }
                            }
                        }
                    }
                }
            });
        }

        console.log('Charts initialized successfully');
    }
</script>
</body>

</html>