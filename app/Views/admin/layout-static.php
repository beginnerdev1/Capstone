<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Static Navigation - SB Admin</title>
         <link href="<?php echo base_url('assets/admin/css/styles.css') ?>" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    </head>
    <body>
         <?php require_once(APPPATH . 'Views/admin/navbar.php'); ?>
            <div id="layoutSidenav_content">
                <main>
<div class="container">
  <h3 class="mb-3">Assign Water Bill</h3>
  <div class="table-responsive">
    <table class="table table-striped table-bordered table-hover align-middle">
      <thead class="table-dark">
        <tr>
          <th>#</th>
          <th>User</th>
          <th>Email</th>
          <th>Billing Month</th>
          <th>Usage (m³)</th>
          <th>Rate</th>
          <th>Amount</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>1</td>
          <td>John Doe</td>
          <td>john@example.com</td>
          <td>2025-09</td>
          <td><input type="number" class="form-control form-control-sm" value="15.50"></td>
          <td>20.00</td>
          <td>310.00</td>
          <td><button class="btn btn-primary btn-sm">Assign</button></td>
        </tr>
        <tr>
          <td>2</td>
          <td>Jane Smith</td>
          <td>jane@example.com</td>
          <td>2025-09</td>
          <td><input type="number" class="form-control form-control-sm" value="25.00"></td>
          <td>20.00</td>
          <td>500.00</td>
          <td><button class="btn btn-primary btn-sm">Assign</button></td>
        </tr>
      </tbody>
    </table>
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
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="<?php echo base_url('assets/admin/js/scripts.js') ?>"></script>
    </body>
</html>
