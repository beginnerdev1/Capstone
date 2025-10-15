<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Billings - SB Admin</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
     <link href="<?php echo base_url('assets/admin/css/styles.css') ?>" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">
    <?php require_once(APPPATH . 'Views/admin/navbar.php'); ?>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">Billings </h1>
                <!-- <form id="createBillingForm">
                <div class="mb-3">
                    <label>User ID</label>
                    <input type="number" name="user_id" id="user_id" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Name</label>
                    <input type="text" id="username" class="form-control" placeholder="Enter username (optional)">
                </div>

                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" id="email" class="form-control" readonly>
                </div>

                <div class="mb-3">
                    <label>Address</label>
                    <input type="text" id="address" class="form-control" readonly>
                </div>

                <div class="mb-3">
                    <label>Billing Amount (₱)</label>
                    <input type="number" name="amount" class="form-control" required step="0.01">
                </div>

                <div class="mb-3"> <label>Description</label>
                    <input type="text" name="description" class="form-control">
                </div>

                <button type="submit" class="btn btn-primary">Create Bill</button>
                </form>

                <div id="responseMsg" class="mt-3"></div> -->
                
                <div class="card mb-4 mt-4">
                   <div class="card-header d-flex justify-content-between align-items-center">
                        <?php
                        // Temporary data for development
                        $puroks = ['1', '2', '3', '4', '5'];
                        $statuses = ['Paid', 'Unpaid', 'Overdue'];

                        // Keep selected values when page reloads (GET)
                        $selectedPurok = $_GET['purok'] ?? '';
                        $selectedStatus = $_GET['status'] ?? '';
                        ?>
                        <div><i class="fas fa-file-invoice-dollar me-1"></i> Users</div>

                        <form method="get" action="" class="d-flex align-items-center">
                            <label for="purok" class="me-2 fw-bold">Purok:</label>
                            <select name="purok" id="purok" class="form-select form-select-sm me-2">
                                <option value="">All</option>
                                <?php foreach ($puroks as $p): ?>
                                    <option value="<?= $p ?>" <?= isset($selectedPurok) && $selectedPurok == $p ? 'selected' : '' ?>>
                                        Purok <?= $p ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                        </form>
                    </div>
                    <div class="card-body">
                        <table id="billingsTable" class="table table-hover table-striped align-middle shadow-sm">
                            <thead class="table-primary">
                                <tr class="table-primary">
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Address</th>
                                    <th>Phone Number</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                </tbody>
                        </table>
                    </div>
                </div>
                <div class="card mb-4 mt-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <?php
                        // Temporary data for development
                        $puroks = ['1', '2', '3', '4', '5'];
                        $statuses = ['Paid', 'Unpaid', 'Overdue'];

                        // Keep selected values when page reloads (GET)
                        $selectedPurok = $_GET['purok'] ?? '';
                        $selectedStatus = $_GET['status'] ?? '';
                        ?>
                        <div><i class="fas fa-file-invoice-dollar me-1"></i> Bills</div>

                        <form method="get" action="" class="d-flex align-items-center">
                            <label for="purok" class="me-2 fw-bold">Purok:</label>
                            <select name="purok" id="purok" class="form-select form-select-sm me-2">
                                <option value="">All</option>
                                <?php foreach ($puroks as $p): ?>
                                    <option value="<?= $p ?>" <?= isset($selectedPurok) && $selectedPurok == $p ? 'selected' : '' ?>>
                                        Purok <?= $p ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                        </form>
                    </div>
                    <div class="card-body">
                        <table id="billingsTable" class="table table-hover table-striped align-middle shadow-sm">
                            <thead class="table-primary">
                                <tr class="table-primary">
                                    <th>Bill ID</th>
                                    <th>Name</th>
                                    <th>Address</th>
                                    <th>Amount</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                </tbody>
                        </table>
                    </div>
                </div>
                </div>
        </main>
        <footer class="py-4 bg-light mt-auto">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted">Copyright &copy; Your Website 2023</div>
                    <div>
                        <a href="#">Privacy Policy</a>
                        &middot;
                        <a href="#">Terms &amp; Conditions</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="<?= base_url('assets/admin/js/scripts.js') ?>"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
</body>
</html>



