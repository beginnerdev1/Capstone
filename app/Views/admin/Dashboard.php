<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Admin - Dashboard</title>
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

        /* Ensure footer remains at bottom of dashboard layout */
html, body {
  height: 100%;
}

.main-wrapper {
  display: flex;           /* already present in your file, re-assert */
  flex-direction: column;
  min-height: 100vh;       /* already present as well, but keep it */
}

/* make main content grow and push footer down */
.main-content {
  flex: 1 0 auto;
}

/* ensure footer participates in flow and is pushed down */
.footer {
  margin-top: auto !important;
  position: relative !important;
  z-index: 2;
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
  <a href="<?= base_url('admin/dashboard-content') ?>" class="nav-link ajax-link active">
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
                <a href="<?= base_url('admin/inactiveUsers') ?>" class="collapse-item ajax-link">Inactive Users</a>
                <a href="<?= base_url('admin/suspendedUsers') ?>" class="collapse-item ajax-link">Suspended Users</a>

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
            <a href="<?= base_url('admin/billingManagement') ?>" class="collapse-item ajax-link">Billing Management</a>
            <a href="<?= base_url('admin/failedTransactions') ?>" class="collapse-item ajax-link">Failed Transactions</a>
            <a href="<?= base_url('admin/overduePayments') ?>" class="collapse-item ajax-link">Overdue Payments</a>


           <a href="<?= base_url('admin/edit-profile') ?>" class="collapse-item ajax-link">Edit Profile</a>
           <a href="<?= base_url('admin/reports') ?>" class="collapse-item ajax-link">Reports</a>
        </div>
      </div>
    </li>

    <div class="sidebar-divider"></div>


    <!-- Activity Logs moved to SuperAdmin area -->
    <li class="nav-item">
        <form action="<?= base_url('admin/logout') ?>" method="post" style="margin:0;">
            <?= csrf_field() ?>
            <button type="submit" class="nav-link btn btn-link d-flex align-items-center" style="color: rgba(255,255,255,0.85); text-decoration: none;">
                <i class="fas fa-right-from-bracket me-2"></i>
                <span>Logout</span>
            </button>
        </form>
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
                    <a href="<?= base_url('admin/chat') ?>" class="ajax-link" title="Open chats">
                        <div class="topbar-icon" title="Unread user chats" style="display:inline-block;">
                            <i class="fas fa-envelope"></i>
                            <span id="unreadChatsCount" class="badge badge-danger badge-counter" style="display:none;">0</span>
                        </div>
                    </a>
                    <div style="height: 24px; width: 1px; background-color: #e3e6f0; margin: 0 0.5rem;"></div>
                    <a href="#" class="user-profile">
                        <span class="profile-name"><?= htmlspecialchars($displayName ?? (session()->get('admin_email') ?? 'Admin'), ENT_QUOTES, 'UTF-8') ?></span>
                    </a>
                    
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div id="mainContent"></div>

        <!-- Footer -->
        <div class="footer">
            <span>Copyright &copy; MyAquaBill 2025</span>
        </div>
    </div>

    <!-- Scroll to Top Button -->
    <button class="scroll-top" id="scrollTop">
        <i class="fas fa-angle-up"></i>
    </button>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    

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
$(document).on("click", ".ajax-link", function(e) {
    e.preventDefault();
    const url = $(this).attr("href");

    // Save last visited AJAX URL to localStorage
    localStorage.setItem("lastAjaxPage", url);

    loadAjaxPage(url);
});

function loadAjaxPage(url) {
    $.ajax({
        url: url,
        type: "GET",
        success: function(data) {
            try {
                const nodes = $.parseHTML(data, document, true);
                const $mc = $("#mainContent");
                $mc.empty().append(nodes);
            } catch (e) {
                // Fallback if parsing fails
                const $fallback = $('<div/>', { class: 'alert alert-danger m-3', role: 'alert' })
                    .append($('<i/>', { class: 'fas fa-exclamation-triangle me-2' }))
                    .append(document.createTextNode('Failed to render content.'));
                $("#mainContent").empty().append($fallback);
            }

            // Persist last AJAX page centrally
            try { localStorage.setItem("lastAjaxPage", url); } catch(_) {}

            // If reports view is loaded, sync filter controls from URL or defaults
            try {
                const $mc = $("#mainContent");
                if ($mc.find('.filters-section').length) {
                    const parsed = new URL(url, window.location.origin);
                    const qs = parsed.searchParams;
                    const start = qs.get('start');
                    const end = qs.get('end');
                    const type = qs.get('type');

                    const startEl = $mc.find('#startDate')[0];
                    const endEl = $mc.find('#endDate')[0];
                    const typeEl = $mc.find('.filter-select')[0];

                    const today = new Date();
                    const yyyy = today.getFullYear();
                    const mm = String(today.getMonth() + 1).padStart(2, '0');
                    const dd = String(today.getDate()).padStart(2, '0');
                    const defaultStart = `${yyyy}-01-01`;
                    const defaultEnd = `${yyyy}-${mm}-${dd}`;

                    if (startEl) startEl.value = start || defaultStart;
                    if (endEl) endEl.value = end || defaultEnd;
                    if (typeEl) typeEl.value = type || '';
                }
            } catch(err) {
                console.warn('[Reports] Failed to sync filters:', err);
            }

            if (typeof initTransactionPage === 'function' && $("#mainContent").find('#paymentTableBody').length) {
                initTransactionPage();
            }

            // Initialize dashboard charts if dashboard content is loaded
            if ($("#mainContent").find('#incomeChart').length || $("#mainContent").find('#revenueChart').length) {
                initializeDashboardCharts();
            }

            // Initialize reports charts if reports content is loaded
            if ($("#mainContent").find('#collectionChart').length || $("#mainContent").find('#paymentStatusChart').length) {
                initializeReportsCharts();
            }
        },
        error: function(xhr, status, error) {
            const $err = $('<div/>', { class: 'alert alert-danger m-3', role: 'alert' })
                .append($('<i/>', { class: 'fas fa-exclamation-triangle me-2' }))
                .append(document.createTextNode('Failed to load content. Please try again.'));
            $("#mainContent").empty().append($err);
        }
    });
}

// Dashboard Charts Initialization Function
function initializeDashboardCharts() {
    // Check if data is available
    if (typeof window.dashboardData === 'undefined') {
        return;
    }

    const data = window.dashboardData;

    // Earnings Overview Line Chart
    const incomeCtx = document.getElementById('incomeChart');
    if (incomeCtx) {
        new Chart(incomeCtx, {
            type: 'line',
            data: {
                labels: data.months || [],
                datasets: [{
                    label: 'Earnings',
                    data: data.incomeData || [],
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    borderWidth: 2,
                    pointRadius: 3,
                    pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointBorderColor: 'rgba(78, 115, 223, 1)',
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgb(255,255,255)',
                        bodyColor: '#858796',
                        titleMarginBottom: 10,
                        titleColor: '#6e707e',
                        titleFont: {
                            size: 14
                        },
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        padding: 15,
                        displayColors: false,
                        intersect: false,
                        mode: 'index',
                        caretPadding: 10,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += '₱' + context.parsed.y.toFixed(2);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            maxTicksLimit: 7
                        }
                    },
                    y: {
                        ticks: {
                            maxTicksLimit: 5,
                            padding: 10,
                            callback: function(value) {
                                return '₱' + value.toFixed(0);
                            }
                        },
                        grid: {
                            color: 'rgb(234, 236, 244)',
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineColor: 'rgb(234, 236, 244)',
                            zeroLineBorderDash: [2]
                        }
                    }
                }
            }
        });
    }

    // Revenue Sources Doughnut Chart
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        const normalRev = data.normalRevenue || 0;
        const seniorRev = data.seniorRevenue || 0;
        const aloneRev = data.aloneRevenue || 0;
        
        new Chart(revenueCtx, {
            type: 'doughnut',
            data: {
                labels: ['Normal Rate (₱60)', 'Senior Citizen (₱48)', 'Living Alone (₱30)'],
                datasets: [{
                    data: [normalRev, seniorRev, aloneRev],
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
                    hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf'],
                    hoverBorderColor: "rgba(234, 236, 244, 1)"
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            font: {
                                size: 11
                            },
                            padding: 10
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgb(255,255,255)',
                        bodyColor: '#858796',
                        titleColor: '#6e707e',
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        padding: 15,
                        displayColors: true,
                        caretPadding: 10,
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (context.parsed !== null) {
                                    label += ': ₱' + context.parsed.toFixed(2);
                                }
                                return label;
                            }
                        }
                    }
                },
                cutout: '70%'
            }
        });
    }
}

// Reports Charts Initialization Function (for AJAX-loaded reports view)
function initializeReportsCharts() {
    if (typeof Chart === 'undefined') return;

    function getReportsData() {
        const el = document.getElementById('reports-data');
        if (!el) return null;
        try { return JSON.parse(el.textContent || '{}'); } catch (e) { return null; }
    }

    function showNoDataInline(canvasId, message) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return;
        const wrapper = canvas.parentElement || canvas;
        const placeholder = document.createElement('div');
        placeholder.textContent = message || 'No data to display';
        placeholder.style.cssText = 'display:flex;align-items:center;justify-content:center;height:160px;color:#6b7280;background:#f9fafb;border:1px dashed #e5e7eb;border-radius:8px;font-family:Poppins, sans-serif;font-weight:600;';
        canvas.style.display = 'none';
        wrapper.appendChild(placeholder);
    }

    const data = getReportsData() || {};

    // Monthly Collection Rate
    (function initCollection() {
        const canvas = document.getElementById('collectionChart');
        if (!canvas) return;
        const existing = Chart.getChart(canvas);
        if (existing) existing.destroy();
        const rates = Array.isArray(data.collectionRates) ? data.collectionRates : new Array(12).fill(0);
        const amounts = Array.isArray(data.collectionAmounts) ? data.collectionAmounts : new Array(12).fill(0);
        const partialRates = Array.isArray(data.partialRates) ? data.partialRates : new Array(12).fill(0);
        const sum = arr => (arr||[]).reduce((a,b)=>a+(Number(b)||0),0);
        if (sum(rates) === 0 && sum(amounts) === 0) {
            showNoDataInline('collectionChart', 'No monthly collections yet');
            return;
        }
        new Chart(canvas.getContext('2d'), {
            type: 'line',
            data: {
                labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
                datasets: [{
                    label: 'Collection Rate (%)',
                    data: rates,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59,130,246,0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 3,
                    yAxisID: 'y'
                },{
                    label: 'Amount Collected (₱)',
                    data: amounts,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16,185,129,0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 3,
                    yAxisID: 'y1'
                }, {
                    label: 'Partial Rate (%)',
                    data: partialRates,
                    borderColor: '#06b6d4',
                    backgroundColor: 'rgba(6,182,212,0.06)',
                    borderWidth: 2,
                    pointRadius: 4,
                    pointBackgroundColor: '#06b6d4',
                    fill: false,
                    tension: 0.25,
                    yAxisID: 'y'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                interaction: { mode: 'index', intersect: false },
                scales: {
                    y: { beginAtZero: true, position: 'left', max: 100 },
                    y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false } },
                    x: { grid: { display: false } }
                }
            }
        });
    })();

    // Payment Status (Doughnut)
    (function initPaymentStatus() {
        const canvas = document.getElementById('paymentStatusChart');
        if (!canvas) return;
        const existing = Chart.getChart(canvas);
        if (existing) existing.destroy();
        const paid = Number(data.paidHouseholds||0);
        const partial = Number(data.partialCount||0);
        const pending = Number(data.pendingCount||0);
        const late = Number(data.latePayments||0);
        const total = paid + partial + pending + late;
        if (total === 0) { showNoDataInline('paymentStatusChart', 'No payment status data yet'); return; }
        new Chart(canvas.getContext('2d'), {
            type: 'doughnut',
            data: { labels: ['Paid','Partial','Pending','Late'], datasets: [{ data: [paid, partial, pending, late], backgroundColor: ['#10b981','#06b6d4','#f59e0b','#ef4444'], borderWidth: 0 }] },
            options: { responsive: true, maintainAspectRatio: true, plugins: { tooltip: { callbacks: { label: function(ctx){ const v = ctx.raw||0; const pct = total>0?Math.round((v/total)*1000)/10:0; return ctx.label + ': ' + v + ' ('+pct+'%)'; } } } } }
        });
    })();

    // Rate Distribution (Pie)
    (function initRateDistribution() {
        const canvas = document.getElementById('rateDistributionChart');
        if (!canvas) return;
        const existing = Chart.getChart(canvas);
        if (existing) existing.destroy();
        const n = Number(data.normalCount||0), s = Number(data.seniorCount||0), a = Number(data.aloneCount||0);
        if (n+s+a === 0) { showNoDataInline('rateDistributionChart', 'No household distribution data'); return; }
        new Chart(canvas.getContext('2d'), {
            type: 'pie',
            data: { labels: [`Normal (₱${data.rateNormal||60})`,`Senior (₱${data.rateSenior||48})`,`Alone (₱${data.rateAlone||30})`], datasets: [{ data: [n,s,a], backgroundColor: ['#3b82f6','#10b981','#f59e0b'], borderWidth: 0 }] },
            options: { responsive: true, maintainAspectRatio: true }
        });
    })();

    // Mini Payment (Bar)
    (function initMini() {
        const canvas = document.getElementById('miniPaymentChart');
        if (!canvas) return;
        const existing = Chart.getChart(canvas);
        if (existing) existing.destroy();
        const paid = Number(data.paidHouseholds||0);
        const partial = Number(data.partialCount||0);
        const pending = Number(data.pendingCount||0);
        const late = Number(data.latePayments||0);
        if (paid+partial+pending+late === 0) { showNoDataInline('miniPaymentChart', 'No payment data yet'); return; }
        new Chart(canvas.getContext('2d'), {
            type: 'bar',
            data: { labels: ['Paid','Partial','Pending','Late'], datasets: [{ label: 'Households', data: [paid,partial,pending,late], backgroundColor: ['#10b981','#06b6d4','#f59e0b','#ef4444'], borderWidth: 0, borderRadius: 6 }] },
            options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true }, x: { grid: { display: false } } } }
        });
    })();
}

$(document).ready(function() {
    // Load last page if exists, otherwise default to dashboard
    const lastPage = localStorage.getItem("lastAjaxPage");
    const defaultPage = "<?= base_url('admin/dashboard-content') ?>";
    const urlToLoad = lastPage || defaultPage;

    loadAjaxPage(urlToLoad);
});
</script>
<!-- Global Export Confirmation Modal (available for AJAX-injected pages) -->
<div id="globalExportConfirmModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#fff; width:95%; max-width:560px; border-radius:14px; box-shadow:0 20px 60px rgba(0,0,0,.25); overflow:hidden; font-family:Poppins, sans-serif;">
        <div style="padding:16px 20px; background:linear-gradient(135deg,#3b82f6,#0ea5e9); color:#fff; font-weight:700;">Confirm Export</div>
        <div style="padding:16px 20px; color:#1f2937;">
            <div style="margin-bottom:12px;">Choose your export options.</div>
            <div style="display:flex; gap:10px; align-items:center; margin-top:8px;">
                <label for="globalExportFileName" style="font-weight:600; min-width:110px;">File name</label>
                <input id="globalExportFileName" type="text" style="flex:1; padding:10px 12px; border:2px solid #e5e7eb; border-radius:10px;" placeholder="reports_<?= date('Y-m-d') ?>">
            </div>
            <div style="display:flex; gap:10px; align-items:center; margin-top:12px;">
                <label for="globalExportFormat" style="font-weight:600; min-width:110px;">Format</label>
                <select id="globalExportFormat" style="flex:1; padding:10px 12px; border:2px solid #e5e7eb; border-radius:10px;">
                    <option value="csv">CSV</option>
                    <option value="excel">Excel (.xlsx)</option>
                    <option value="pdf">PDF</option>
                    <option value="print">Print</option>
                </select>
            </div>
        </div>
        <div style="padding:12px 20px; background:#f9fafb; display:flex; gap:10px; justify-content:flex-end;">
            <button id="globalExportCancelBtn" style="padding:.7rem 1.2rem; border:2px solid #e5e7eb; background:#fff; border-radius:10px; font-weight:700;">Cancel</button>
            <button id="globalExportConfirmBtn" style="padding:.7rem 1.2rem; background:#10b981; color:#fff; border:none; border-radius:10px; font-weight:700;">Export</button>
        </div>
    </div>
</div>

<script>
// Global delegated handlers so AJAX-injected content works
(function(){
    const EXPORT_URL = "<?= site_url('admin/exportReports') ?>";
    const modal = document.getElementById('globalExportConfirmModal');
    const nameInput = document.getElementById('globalExportFileName');
    const fmtSelect = document.getElementById('globalExportFormat');
    const cancelBtn = document.getElementById('globalExportCancelBtn');
    const confirmBtn = document.getElementById('globalExportConfirmBtn');
    let pendingFormat = 'csv';
    let pendingUrl = EXPORT_URL;

    function openModal(fmt, url){
        pendingFormat = (fmt||'csv').toLowerCase();
        pendingUrl = url || EXPORT_URL;
        try { if (fmtSelect) fmtSelect.value = pendingFormat; } catch(_) {}
        if (!nameInput.value) {
            nameInput.value = `reports_${new Date().toISOString().slice(0,10)}`;
        }
        modal.style.display = 'flex';
    }
    function closeModal(){ modal.style.display='none'; }

    // Intercept export clicks inside Reports view only
    document.addEventListener('click', function(ev){
        const target = ev.target && ev.target.closest ? ev.target.closest('#mainContent .export-btn, #mainContent .btn-export') : null;
        if (!target) return;
        const reportsRoot = document.querySelector('#mainContent .reports-wrapper');
        if (!reportsRoot || !reportsRoot.contains(target)) {
            return; // let other pages (e.g., Transactions) handle their own exports
        }
        ev.preventDefault();
        // Prefer data-format; if absent, derive from href query param
        let fmt = target.getAttribute('data-format');
        if (!fmt) {
            const href = target.getAttribute('href') || '';
            try {
                const u = new URL(href, window.location.origin);
                fmt = u.searchParams.get('format');
            } catch(_) { fmt = null; }
        }
        fmt = (fmt || 'csv').toLowerCase();
        const url = target.getAttribute('data-export-url') || EXPORT_URL;
        openModal(fmt, url);
    }, true);

    // Refresh button (AJAX) inside reports
    document.addEventListener('click', function(ev){
        const a = ev.target && ev.target.closest ? ev.target.closest('#mainContent .reports-refresh') : null;
        if (!a) return;
        ev.preventDefault();
        const href = a.getAttribute('href') || "<?= site_url('admin/reports') ?>";
        try {
            try { localStorage.setItem('lastAjaxPage', href); } catch(_) {}
            loadAjaxPage(href);
        } catch(e) { window.location.href = href; }
    });

    // Apply filters and Reset (diagnostic wiring)
    document.addEventListener('click', function(ev){
        const apply = ev.target && ev.target.closest ? ev.target.closest('#mainContent .btn-apply') : null;
        if (apply) {
            ev.preventDefault();
            const root = document.querySelector('#mainContent');
            const startEl = root ? root.querySelector('#startDate') : null;
            const endEl = root ? root.querySelector('#endDate') : null;
            const typeEl = root ? root.querySelector('.filter-select') : null;

            const start = startEl && startEl.value ? startEl.value : '';
            const end = endEl && endEl.value ? endEl.value : '';
            const type = typeEl && typeEl.value ? typeEl.value : '';

            // Determine base reports URL from Refresh button or fallback
            const refreshA = root ? root.querySelector('.reports-refresh') : null;
            const baseUrl = (refreshA && refreshA.getAttribute('href')) || "<?= site_url('admin/reports') ?>";

            const params = new URLSearchParams();
            if (start) params.set('start', start);
            if (end) params.set('end', end);
            if (type) params.set('type', type);
            const url = params.toString() ? `${baseUrl}?${params.toString()}` : baseUrl;
            try { localStorage.setItem('lastAjaxPage', url); } catch(_) {}
            try { loadAjaxPage(url); } catch(e) { window.location.href = url; }
            return;
        }
        const reset = ev.target && ev.target.closest ? ev.target.closest('#mainContent .btn-reset') : null;
        if (reset) {
            ev.preventDefault();
            const root = document.querySelector('#mainContent');
            const refreshA = root ? root.querySelector('.reports-refresh') : null;
            const baseUrl = (refreshA && refreshA.getAttribute('href')) || "<?= site_url('admin/reports') ?>";
            try { localStorage.setItem('lastAjaxPage', baseUrl); } catch(_) {}
            try { loadAjaxPage(baseUrl); } catch(e) { window.location.href = baseUrl; }
            return;
        }
    });

    cancelBtn.addEventListener('click', closeModal);
    confirmBtn.addEventListener('click', function(){
        const baseName = (nameInput.value || `reports_${new Date().toISOString().slice(0,10)}`).trim();
        if (!baseName) { alert('Please enter a file name.'); return; }
        // Use selected format if available
        let chosenFmt = pendingFormat;
        if (fmtSelect && fmtSelect.value) { chosenFmt = fmtSelect.value.toLowerCase(); }

        // Include current filters in export URL so scope matches the view
        const mc = document.getElementById('mainContent');
        const startEl = mc ? mc.querySelector('#startDate') : null;
        const endEl = mc ? mc.querySelector('#endDate') : null;
        const typeEl = mc ? mc.querySelector('.filter-select') : null;
        const params = new URLSearchParams();
        params.set('format', chosenFmt);
        params.set('filename', baseName);
        if (startEl && startEl.value) params.set('start', startEl.value);
        if (endEl && endEl.value) params.set('end', endEl.value);
        if (typeEl && typeEl.value) params.set('type', typeEl.value);

        // Append params correctly whether pendingUrl already has query string
        const sep = pendingUrl.indexOf('?') === -1 ? '?' : '&';
        const href = `${pendingUrl}${sep}${params.toString()}`;
        closeModal();
        window.open(href, '_blank');
    });
})();
</script>

<script>
// Delegated handlers for Registered Users deactivate modal (AJAX-injected views)
(function(){
    // Open global deactivate modal when clicking a deactivate button in injected content
    document.addEventListener('click', function(ev){
        const btn = ev.target && ev.target.closest ? ev.target.closest('#mainContent .deactivateUserBtn') : null;
        if (!btn) return;
        ev.preventDefault();
        try { console.debug('[Deactivate] Click captured', btn); } catch(_) {}
        const userId = btn.getAttribute('data-id');
        const name = btn.getAttribute('data-name') || 'this user';
        const modalEl = document.getElementById('globalDeactivateModal');
        try { console.debug('[Deactivate] Modal element found?', !!modalEl); } catch(_) {}
        if (modalEl) {
            modalEl.dataset.userId = userId || '';
            const nameEl = modalEl.querySelector('#deactivateUserName');
            const reasonEl = modalEl.querySelector('#deactivateReason');
            if (nameEl) nameEl.textContent = name;
            if (reasonEl) reasonEl.value = '';
            try {
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
            } catch (e) {
                try { $(modalEl).modal('show'); } catch(_) {}
            }
        } else {
            alert('Deactivate modal not found in the page markup.');
        }
    });

    // Confirm deactivate from global modal
    document.addEventListener('click', function(ev){
        const btn = ev.target && ev.target.closest ? ev.target.closest('#confirmDeactivateBtn') : null;
        if (!btn) return;
        ev.preventDefault();
        const modalEl = btn.closest('.modal') || document.getElementById('globalDeactivateModal');
        try { console.debug('[Deactivate] Confirm clicked; modal present?', !!modalEl); } catch(_) {}
        if (!modalEl) return;
        const userId = modalEl.dataset.userId;
        if (!userId) return;
        const form = modalEl.querySelector('#deactivateUserForm');
        const data = form ? $(form).serialize() : { reason: (modalEl.querySelector('#deactivateReason') || {}).value || '' };
        const url = "<?= site_url('admin/deactivateUser') ?>/" + encodeURIComponent(userId);
        const $btn = $(btn);
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Deactivating...');
        $.post(url, data).done(function(res){
            if (res && res.success) {
                const $container = $('#mainContent .container-fluid').first();
                const successAlert = $('<div class="alert alert-success alert-dismissible fade show" role="alert">'
                    + '<i class="fas fa-check-circle me-2"></i>' + (res.message || 'User deactivated and archived successfully.')
                    + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>'
                    + '</div>');
                if ($container.length) { $container.prepend(successAlert); }
                try { bootstrap.Modal.getInstance(modalEl).hide(); } catch(_) {}
                // Cleanup any lingering backdrops/classes to restore clickability
                try {
                    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                    document.body.classList.remove('modal-open');
                    document.body.style.removeProperty('padding-right');
                } catch(_) {}
                // Reload current page content
                const current = localStorage.getItem('lastAjaxPage') || "<?= base_url('admin/registeredUsers') ?>";
                try { loadAjaxPage(current); } catch(e) { window.location.href = current; }
            } else {
                const msg = res && res.message ? res.message : 'Failed to deactivate user.';
                const $form = $(modalEl).find('#deactivateUserForm');
                const errorAlert = $('<div class="alert alert-danger alert-dismissible fade show" role="alert">'
                    + '<i class="fas fa-exclamation-triangle me-2"></i>' + msg
                    + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>'
                    + '</div>');
                if ($form.length) { $form.prepend(errorAlert); }
            }
        }).fail(function(){
            const $form = $(modalEl).find('#deactivateUserForm');
            const errorAlert = $('<div class="alert alert-danger alert-dismissible fade show" role="alert">'
                + '<i class="fas fa-exclamation-triangle me-2"></i>Error deactivating user. Please try again.'
                + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>'
                + '</div>');
            if ($form.length) { $form.prepend(errorAlert); }
        }).always(function(){
            $btn.prop('disabled', false).html('<i class="fas fa-user-slash me-1"></i>Deactivate');
        });
    });

    // Safety: clean backdrops on any modal hide
    document.addEventListener('hidden.bs.modal', function(){
        try {
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('padding-right');
        } catch(_) {}
    });
})();
</script>

<!-- Global Deactivate Modal (for AJAX-injected Registered Users) -->
<div class="modal fade" id="globalDeactivateModal" tabindex="-1" aria-labelledby="globalDeactivateLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="globalDeactivateLabel">Deactivate <span id="deactivateUserName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="deactivateUserForm">
                    <div class="mb-3">
                        <label for="deactivateReason" class="form-label">Reason for deactivation (optional)</label>
                        <textarea class="form-control" id="deactivateReason" name="reason" rows="3" placeholder="Provide a reason (optional)"></textarea>
                    </div>
                </form>
                <div class="alert alert-warning" role="alert" style="margin-bottom:0;">
                    <i class="fas fa-exclamation-triangle me-2"></i>This will archive the user's last 2 years of billings.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeactivateBtn">
                    <i class="fas fa-user-slash me-1"></i>Deactivate
                </button>
            </div>
        </div>
    </div>
    </div>

</body>

</html>

    <script>
    // Poll admin unread user chat count and update topbar badge
    (function(){
        const el = document.getElementById('unreadChatsCount');
        if (!el) return;
        const url = "<?= site_url('admin/chat/unreadCount') ?>";

        async function fetchCount(){
            try {
                const r = await fetch(url, { credentials: 'same-origin' });
                if (!r.ok) return;
                const j = await r.json();
                const c = Number(j && j.count ? j.count : 0) || 0;
                if (c > 0) {
                    el.textContent = String(c);
                    el.style.display = '';
                } else {
                    el.style.display = 'none';
                }
            } catch (e) {
                // silent fail
                //console.warn('Unread count failed', e);
            }
        }

        // adaptive poll loop: only poll when page visible; exponential backoff on failures
        (function(){
            const base = 8000; // 8s
            const max = 60000; // 60s
            let delay = base;
            let failures = 0;

            async function loop(){
                if (document.hidden) {
                    // when tab hidden, poll less frequently
                    delay = max;
                    setTimeout(loop, delay);
                    return;
                }
                try {
                    await fetchCount();
                    failures = 0;
                    delay = base;
                } catch (e) {
                    failures++;
                    delay = Math.min(max, base * Math.pow(2, Math.min(failures, 6)));
                }
                setTimeout(loop, delay);
            }

            // start
            loop();
        })();
    })();
    </script>