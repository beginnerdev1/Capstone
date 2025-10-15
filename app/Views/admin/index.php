<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Dashboard - MyAquaBill Admin</title>

        <!-- DataTables -->
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />

        <!-- Custom Styles -->
        <link href="<?= base_url('assets/admin/css/styles.css') ?>" rel="stylesheet" />

        <!-- Font Awesome -->
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    </head>

    <body class="sb-nav-fixed">

        <!-- Navbar -->
        <?php require_once(APPPATH . 'Views/admin/navbar.php'); ?>

        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Dashboard</h1>
                    <?php require_once(APPPATH . 'Views/admin/set_password.php'); ?>

                    <!-- Dashboard Cards -->
                    <div class="row">
                        <!-- Registered Users -->
                        <div class="col-12 col-sm-6 col-lg-4 mb-4">
                            <div class="card dashboard-card bg-primary text-white h-100">
                                <div class="card-body d-flex flex-column align-items-start justify-content-center">
                                    <div class="dashboard-title">Registered Users</div>
                                    <div class="dashboard-icon"><i class="fas fa-users"></i></div>
                                    <div class="dashboard-number"><?= $registeredUsers ?? '0' ?></div>
                                    
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-white stretched-link" href="<?= base_url('admin/registeredUsers') ?>">View Details</a>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>

                        <!-- Create Billing -->
                        <div class="col-12 col-sm-6 col-lg-4 mb-4">
                            <div class="card dashboard-card bg-warning text-white h-100">
                                <div class="card-body d-flex flex-column align-items-start justify-content-center">
                                    <div class="dashboard-title">Create Billing</div>
                                    <div class="dashboard-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                                    <div class="dashboard-number"><?= $billings ?? '0' ?></div>
                                    
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-white stretched-link" href="<?= base_url('admin/billings') ?>">View Details</a>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>

                        <!-- Paid User Bills -->
                        <div class="col-12 col-sm-6 col-lg-4 mb-4">
                            <div class="card dashboard-card bg-success text-white h-100">
                                <div class="card-body d-flex flex-column align-items-start justify-content-center">
                                    <div class="dashboard-title">Paid User Bills</div>
                                    <div class="dashboard-icon"><i class="fas fa-check-circle"></i></div>
                                    <div class="dashboard-number"><?= $paidBills ?? '0' ?></div>
                                    
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-white stretched-link" href="<?= base_url('admin/paidBills') ?>">View Details</a>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>

                    <!-- Charts Row -->
                    <div class="row">
                        <div class="col-xl-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <i class="fas fa-chart-area me-1"></i>
                                    Area Chart Example
                                </div>
                                <div class="card-body">
                                    <canvas id="myAreaChart" width="100%" height="40"></canvas>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <i class="fas fa-chart-bar me-1"></i>
                                    Bar Chart Example
                                </div>
                                <div class="card-body">
                                    <canvas id="myBarChart" width="100%" height="40"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <!-- Footer -->
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">&copy; MyAquaBill <?= date('Y') ?></div>
                        <div>
                            <a href="#">Privacy Policy</a>
                            &middot;
                            <a href="#">Terms &amp; Conditions</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>

        <!-- JS Dependencies -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="<?= base_url('assets/admin/js/scripts.js') ?>"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script src="<?= base_url('assets/admin/demo/chart-area-demo.js') ?>"></script>
        <script src="<?= base_url('assets/admin/demo/chart-bar-demo.js') ?>"></script>
        <script src="<?= base_url('assets/admin/demo/chart-pie-demo.js') ?>"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="<?= base_url('assets/admin/js/datatables-simple-demo.js') ?>"></script>
    </body>
</html>
