<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Paid User Bills Dashboard - SB Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
     <link href="<?php echo base_url('assets/admin/css/styles.css') ?>" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">
    <?php require_once(APPPATH . 'Views/admin/navbar.php'); ?>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">Paid User Bills Dashboard</h1>
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-money-check-alt me-1"></i>
                        Paid Bills (Last Year to Today)
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="datatable-table table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Bill ID</th>
                                        <th>User</th>
                                        <th>Amount</th>
                                        <th>Date Paid</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($paidBills)): ?>
                                        <?php foreach ($paidBills as $bill): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($bill['id']) ?></td>
                                                <td><?= htmlspecialchars($bill['user']) ?></td>
                                                <td><?= htmlspecialchars(number_format($bill['amount'], 2)) ?></td>
                                                <td><?= htmlspecialchars($bill['date']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center">No paid bills found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <footer class="py-4 bg-light mt-auto">
            <!-- <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted">Copyright &copy; Your Website 2023</div>
                    <div>
                        <a href="#">Privacy Policy</a>
                        &middot;
                        <a href="#">Terms &amp; Conditions</a>
                    </div>
                </div> -->
            </div>
        </footer>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="<?= base_url('assets/admin/js/scripts.js') ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script src="<?= base_url('assets/admin/js/datatables-simple-demo.js') ?>"></script>
</body>
</html>