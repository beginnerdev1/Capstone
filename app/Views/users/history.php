
<!DOCTYPE html>
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Bill History</title>

  <!-- Vendor CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/@srexi/purecounterjs/dist/purecounter_vanilla.js"></script>

  <!-- Main CSS File -->
  <link href="<?= base_url('assets/Users/css/main.css?v=' . time()) ?>" rel="stylesheet">
</head>

<body>

  <!-- ======= Header ======= -->
  <?= $this->include('Users/header') ?>
  <!-- End Header -->

  <main id="main">

    <!-- Page Title -->
    <div class="page-title">
      <div class="heading text-center">
        <h1>Bill History</h1>
        <p class="mb-0">Check your past bills and payment records</p>
      </div>
    </div><!-- End Page Title -->

    <!-- Bill History Section -->
    <section id="bill-history" class="section">
      <div class="container" data-aos="fade-up">

        <div class="section-title">
          <h2>Your Bills</h2>
          <p>Overview of your water bills</p>
        </div>
        <!-- Limit records form -->
     <label for="limit">Show:</label>
    <select id="limit" class="form-select" style="width:120px; display:inline-block;">
        <option value="5">5</option>
        <option value="10" selected>10</option>
        <option value="15">15</option>
        <option value="20">20</option>
    </select> bills
    <!-- End Limit records form --> 
        <div class="table-responsive">
          <table class="table table-striped table-hover align-middle">
            <thead class="table-primary">
              <tr>
                <th>Invoice ID</th>
                <th scope="col">Billing Month</th>
                <th scope="col">Amount</th>
                <th scope="col">Status</th>
                <th scope="col">Date Paid</th>
                <th scope="col">Action</th>
              </tr>
            </thead>
           <tbody id = "billingTableBody">
               
            </tbody>
          </table>
        </div>
                <!--Script for limit show js-->
        <script>
            function loadBillings(limit = 10) {
                fetch(`<?= base_url('users/getBillingsAjax') ?>?limit=${limit}`)
                    .then(response => response.json())
                    .then(data => {
                        console.log(data); // Debugging

                        let rows = '';
                        if (data.length > 0) {
                            data.forEach(bill => {
                                rows += `
                                    <tr>
                                        <td>${bill.id}</td>
                                        <td>${new Date(bill.due_date).toLocaleDateString('en-US', { month: 'long', year: 'numeric' })}</td>
                                        <td>₱${parseFloat(bill.amount).toFixed(2)}</td>
                                        <td>
                                            ${bill.status === 'paid' 
                                                ? '<span class="badge bg-success">Paid</span>' 
                                                : '<span class="badge bg-danger">Unpaid</span>'}
                                        </td>
                                        <td>${bill.date_paid ? new Date(bill.date_paid).toLocaleDateString('en-US') : '-'}</td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-receipt"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                `;
                            });
                        } else {
                            rows = `
                                <tr>
                                    <td colspan="7" class="text-center">No bills found.</td>
                                </tr>
                            `;
                        }
                        document.getElementById('billingTableBody').innerHTML = rows;
                    });
            }
        
            document.addEventListener("DOMContentLoaded", function(){
                loadBillings(); // Initial load with default limit

                document.getElementById('limit').addEventListener('change',function(){
                    loadBillings(this.value);//load with user input limit
                });
            });

        </script>
        <!-- End Table -->
        <div class="section-title my-5">
          <h2>Expenses Overview</h2>
          <p>Summary of your monthly and yearly expenses</p>
        </div>
        <div class="alert alert-info" role="alert">
            <?php if (isset($monthlyExpensesWithChange)): ?>
                <?php $lastChange = end($monthlyExpensesWithChange)['percent_change']; ?>
                <?php if ($lastChange > 0): ?>
                    <strong>Increase:</strong> Your expenses increased by <?= $lastChange ?>% compared to the previous month.
                <?php elseif ($lastChange < 0): ?>
                    <strong>Decrease:</strong> Your expenses decreased by <?= abs($lastChange) ?>% compared to the previous month.
                <?php else: ?>
                    <strong>No Change:</strong> Your expenses remained the same as the previous month.
                <?php endif; ?>
            <?php else: ?>
                <strong>Info:</strong> Not enough data to calculate monthly change.
            <?php endif; ?>
        </div>
        <div class="row">
            <div class="col-md-6">
                <canvas id="monthlyExpensesChart"></canvas>
            </div>
            <div class="col-md-6">
                <canvas id="yearlyExpensesChart"></canvas>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const monthlyCtx = document.getElementById('monthlyExpensesChart').getContext('2d');
            const yearlyCtx = document.getElementById('yearlyExpensesChart').getContext('2d');
                    
            const monthlyChart = new Chart(monthlyCtx, {
                type: 'bar',
                data: {
                    labels: <?= json_encode(array_column($monthlyExpenses, 'month')) ?>,
                    datasets: [{
                        label: 'Monthly Expenses(₱)',
                        data: <?= json_encode(array_column($monthlyExpenses, 'total')) ?>,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    }]
                }
            });

            const yearlyChart = new Chart(yearlyCtx, {
                type: 'line',
                data: {
                    labels: <?= json_encode(array_column($yearlyExpenses, 'year')) ?>,
                    datasets: [{
                        label: 'Yearly Expenses (₱)',
                        data: <?= json_encode(array_column($yearlyExpenses, 'total')) ?>,
                        backgroundColor: 'rgba(153, 102, 255, 0.2)',
                        borderColor: 'rgba(153, 102, 255, 1)',
                        borderWidth: 1
                    }]
                }
            });
        </script>
      </div>
    </section><!-- End Bill History Section -->

  </main>

  <!-- ======= Footer ======= -->
  <?= $this->include('Users/footer') ?>
  <!-- End Footer -->

  <a href="#" id="scrollTop" class="scroll-top">↑</a>

  <!-- Vendor JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>

  <!-- Main JS File -->
  <script src="<?= base_url('assets/Users/js/main.js') ?>"></script>
</body>

</html>
