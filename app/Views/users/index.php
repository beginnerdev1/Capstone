<!-- dashboard.php -->
<?php
// Example: Fetch user data (replace with real DB queries)
$userName = "John Doe";
$currentBill = 1250;
$dueDate = "August 30, 2025";
$billStatus = "Unpaid";
$waterPressure = "45 PSI";
$pressureStatus = "Normal";
$lastUpdated = "2:15 PM";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>MyAquaBill Dashboard</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <link href="<?php echo base_url('public/assets/css/users_css.css') ?>" rel="stylesheet" />
 
</head>
<body class="bg-light">

<header class="navbar navbar-dark bg-primary sticky-top flex-md-nowrap p-2 shadow">
  <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="#">💧 MyAquaBill</a>
  <button class="navbar-toggler d-md-none collapsed" type="button" 
          data-bs-toggle="collapse" data-bs-target="#sidebarMenu" 
          aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="user-info text-white ms-auto me-3">
    Welcome, <?php echo $userName; ?> | <a href="logout.php" class="text-white">Logout</a>
  </div>
  <button class="btn btn-dark d-md-none m-2" 
        type="button" 
        data-bs-toggle="collapse" 
        data-bs-target="#sidebarMenu" 
        aria-controls="sidebarMenu" 
        aria-expanded="false" 
        aria-label="Toggle navigation">
  ☰ Menu
</button>
</header>

<div class="container-fluid">
  <div class="row">

    <!-- Sidebar -->
    <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse vh-100 position-fixed">
  <div class="position-sticky pt-3 text-white">
    <h4 class="px-3 mb-4">💧 MyAquaBill</h4>
    <?php require(APPPATH . 'Views/users/sidebar.php'); ?>
  </div>
</nav>

    <!-- Main Content -->
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4" style="margin-left:220px;">
  <div class="pt-4">
        <!-- Current Bill -->
        <div class="col-md-6 col-lg-4">
          <div class="card shadow-sm">
            <div class="card-body">
              <h5 class="card-title">Current Bill</h5>
              <p><strong>Amount Due:</strong> ₱<?php echo $currentBill; ?></p>
              <p><strong>Due Date:</strong> <?php echo $dueDate; ?></p>
              <p><strong>Status:</strong> <?php echo $billStatus; ?></p>
              <a href="pay.php" class="btn btn-primary btn-sm">Pay Now</a>
            </div>
          </div>
        </div>

        <!-- Water Pressure -->
        <div class="col-md-6 col-lg-4">
          <div class="card shadow-sm">
            <div class="card-body">
              <h5 class="card-title">Water Pressure</h5>
              <p><strong>Current:</strong> <?php echo $waterPressure; ?></p>
              <p><strong>Status:</strong> <?php echo $pressureStatus; ?></p>
              <p><small>Last Updated: <?php echo $lastUpdated; ?></small></p>
            </div>
          </div>
        </div>

        <!-- Quick Problem Report -->
        <div class="col-md-12 col-lg-4">
          <div class="card shadow-sm">
            <div class="card-body">
              <h5 class="card-title">Quick Problem Report</h5>
              <p>Experiencing low water pressure or issues?</p>
              <a href="report.php" class="btn btn-warning btn-sm">Report Issue</a>
            </div>
          </div>
        </div>
      </div>

      <!-- Billing History -->
      <div class="card mt-4 shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Billing History</h5>
          <div class="table-responsive">
            <table class="table table-striped align-middle">
              <thead>
                <tr>
                  <th>Month</th>
                  <th>Amount</th>
                  <th>Status</th>
                  <th>Payment Date</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>July</td>
                  <td>₱1200</td>
                  <td>Paid</td>
                  <td>July 28</td>
                  <td><a href="#" class="btn btn-outline-secondary btn-sm">Receipt</a></td>
                </tr>
                <tr>
                  <td>June</td>
                  <td>₱1150</td>
                  <td>Paid</td>
                  <td>June 28</td>
                  <td><a href="#" class="btn btn-outline-secondary btn-sm">Receipt</a></td>
                </tr>
                <tr>
                  <td>May</td>
                  <td>₱1100</td>
                  <td>Paid</td>
                  <td>May 28</td>
                  <td><a href="#" class="btn btn-outline-secondary btn-sm">Receipt</a></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>

<footer class="text-center py-3 bg-light border-top">
  © 2025 MyAquaBill - Powered by LGU Waterworks
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>