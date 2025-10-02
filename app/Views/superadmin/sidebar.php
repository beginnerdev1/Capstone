<!-- Sidebar -->
<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
            <div class="sb-sidenav-menu">
                <div class="nav">

                    <div class="sb-sidenav-menu-heading">Core</div>
                    <a class="nav-link ajax-link" href="<?= base_url('superadmin/dashboard') ?>">
                        <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                        Dashboard
                    </a>

                    <div class="sb-sidenav-menu-heading">Management</div>
                    <a class="nav-link ajax-link" href="<?= base_url('superadmin/users') ?>">
                        <div class="sb-nav-link-icon"><i class="fas fa-user"></i></div>
                        Users
                    </a>
                    <a class="nav-link ajax-link" href="<?= base_url('superadmin/settings') ?>">
                        <div class="sb-nav-link-icon"><i class="fas fa-cog"></i></div>
                        Settings
                    </a>

                    <div class="sb-sidenav-menu-heading">Reports</div>
                    <a class="nav-link ajax-link" href="<?= base_url('superadmin/reports') ?>">
                        <div class="sb-nav-link-icon"><i class="fas fa-file-alt"></i></div>
                        Reports
                    </a>

                </div>
            </div>
            <div class="sb-sidenav-footer">
                <div class="small">Logged in as:</div>
                Super Admin
            </div>
        </nav>
    </div>
</div>
