<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>MyAquaBill Dashboard</title>
  <body>

<header>
  <h1>💧 MyAquaBill</h1>
  <div class="user-info">
    Welcome, <?php echo $userName; ?> | <a href="logout.php" style="color:white;">Logout</a>
  </div>
</header>

<div class="container">
  <!-- Sidebar -->
  <nav>
    <a href="dashboard.php">🏠 Dashboard</a>
    <a href="billing.php">💳 Water Bills</a>
    <a href="payments.php">📑 Payments</a>
    <a href="pressure.php">📈 Water Pressure</a>
    <a href="report.php">⚠️ Report a Problem</a>
    <a href="profile.php">👤 Profile Settings</a>
  </nav>

  <!-- Main Content -->
  <main>
    <div class="cards">
      <!-- Current Bill -->
      <div class="card">
        <h2>Current Bill</h2>
        <p><strong>Amount Due:</strong> ₱<?php echo $currentBill; ?></p>
        <p><strong>Due Date:</strong> <?php echo $dueDate; ?></p>
        <p><strong>Status:</strong> <?php echo $billStatus; ?></p>
        <a href="pay.php" class="btn">Pay Now</a>
      </div>

      <!-- Water Pressure -->
      <div class="card">
        <h2>Water Pressure</h2>
        <p><strong>Current:</strong> <?php echo $waterPressure; ?></p>
        <p><strong>Status:</strong> <?php echo $pressureStatus; ?></p>
        <p><small>Last Updated: <?php echo $lastUpdated; ?></small></p>
      </div>
    </div>

    <!-- Billing History -->
    <div class="card">
      <h2>Billing History</h2>
      <table>
        <tr>
          <th>Month</th>
          <th>Amount</th>
          <th>Status</th>
          <th>Payment Date</th>
          <th>Action</th>
        </tr>
        <tr>
          <td>July</td>
          <td>₱1200</td>
          <td>Paid</td>
          <td>July 28</td>
          <td><a href="#" class="btn">Receipt</a></td>
        </tr>
        <tr>
          <td>June</td>
          <td>₱1150</td>
          <td>Paid</td>
          <td>June 28</td>
          <td><a href="#" class="btn">Receipt</a></td>
        </tr>
        <tr>
          <td>May</td>
          <td>₱1100</td>
          <td>Paid</td>
          <td>May 28</td>
          <td><a href="#" class="btn">Receipt</a></td>
        </tr>
      </table>
    </div>

    <!-- Quick Problem Report -->
    <div class="card" style="margin-top:20px;">
      <h2>Quick Problem Report</h2>
      <p>Experiencing low water pressure or issues?</p>
      <a href="report.php" class="btn">Report Issue</a>
    </div>
  </main>
</div>

<footer>
  © 2025 MyAquaBill - Powered by LGU Waterworks
</footer>

</body>
</html>