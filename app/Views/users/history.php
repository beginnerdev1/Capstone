<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bill History</title>

  <!-- Google Fonts: Poppins -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Custom CSS -->
  <link href="<?= base_url('assets/Users/css/main.css?v=' . time()) ?>" rel="stylesheet">
  <link href="<?= base_url('assets/Users/css/navbar.css?v=' . time()) ?>" rel="stylesheet">

  <style>
    body, h1, h2, h3, h4, h5, h6, p, table, th, td, button {
      font-family: 'Poppins', sans-serif;
    }

    /* Make buttons smaller on mobile */
    @media (max-width: 576px) {
      .btn-sm {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
      }
    }

    /* Make modal full screen on small devices */
    @media (max-width: 576px) {
      .modal-dialog {
        max-width: 95%;
        margin: 1rem;
      }
    }
  </style>
</head>
<body>
  <?= $this->include('Users/header') ?>

  <main id="main">
    <div class="page-title">
      <div class="heading text-center">
        <h1>Bill History</h1>
        <p class="mb-0">Check your past bills and payment records</p>
      </div>
    </div>

    <div class="container my-4 text-center">
      <!-- Filter Buttons -->
      <div class="d-flex flex-wrap justify-content-center gap-2">
        <button class="btn btn-success btn-sm" onclick="filterStatus('successful')">Successful</button>
        <button class="btn btn-warning btn-sm" onclick="filterStatus('unsuccessful')">Unsuccessful</button>
        <button class="btn btn-danger btn-sm" onclick="filterStatus('failed')">Failed</button>
        <button class="btn btn-secondary btn-sm" onclick="filterStatus('all')">Show All</button>
      </div>
    </div>

    <div class="container my-3">
      <div class="table-responsive">
        <table class="table table-striped table-bordered align-middle">
          <thead class="table-dark">
            <tr>
              <th scope="col">#</th>
              <th scope="col">Bill ID</th>
              <th scope="col">Billing Date</th>
              <th scope="col">Amount</th>
              <th scope="col">Status</th>
              <th scope="col">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($payments as $payment): 
              $status = strtolower($payment['status']);
              $modalId = 'modalReceipt' . $payment['id'];
            ?>
            <tr data-status="<?= $status ?>">
              <th scope="row"><?= $payment['id'] ?></th>
              <td><?= $payment['payment_intent_id'] ?></td>
              <td><?= date('F d, Y', strtotime($payment['paid_at'] ?? $payment['created_at'])) ?></td>
              <td>₱<?= number_format($payment['amount'], 2) ?></td>
              <td>
                <?php
                switch($status) {
                  case 'successful':
                    echo '<span class="badge bg-success">Successful</span>';
                    break;
                  case 'unsuccessful':
                    echo '<span class="badge bg-warning text-dark" title="Payment not completed. Please retry.">Unsuccessful</span>';
                    break;
                  case 'failed':
                    echo '<span class="badge bg-danger" title="Payment failed due to an error or cancellation.">Failed</span>';
                    break;
                  default:
                    echo '<span class="badge bg-secondary">Pending</span>';
                }
                ?>
              </td>
              <td>
                <!-- Trigger modal -->
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#<?= $modalId ?>">View</button>
                <?php if(in_array($status, ['unsuccessful','failed'])): ?>
                  <button class="btn btn-success btn-sm">Retry Payment</button>
                <?php endif; ?>
              </td>
            </tr>

            <!-- Modal -->
            <div class="modal fade" id="<?= $modalId ?>" tabindex="-1" aria-labelledby="<?= $modalId ?>Label" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="<?= $modalId ?>Label">Receipt #<?= $payment['payment_intent_id'] ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <p><strong>Bill ID:</strong> <?= $payment['payment_intent_id'] ?></p>
                    <p><strong>Date:</strong> <?= date('F d, Y H:i', strtotime($payment['paid_at'] ?? $payment['created_at'])) ?></p>
                    <p><strong>Amount:</strong> ₱<?= number_format($payment['amount'], 2) ?></p>
                    <p><strong>Status:</strong> <?= ucfirst($status) ?></p>
                    <!-- Additional details can go here -->
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <?php if($status === 'successful'): ?>
                      <a href="<?= base_url('users/payments/download/' . $payment['id']) ?>" class="btn btn-primary">Download Receipt</a>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>

            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <?= $this->include('Users/footer') ?>
  <a href="#" id="scrollTop" class="scroll-top">↑</a>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Filter Script -->
  <script>
    function filterStatus(status) {
      const rows = document.querySelectorAll('table tbody tr');
      rows.forEach(row => {
        row.style.display = (status === 'all' || row.dataset.status === status) ? '' : 'none';
      });
    }

    // Initialize Bootstrap tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'))
    tooltipTriggerList.map(function (el) {
      return new bootstrap.Tooltip(el)
    })
  </script>
</body>
</html>
