<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Transaction History</title>

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet"/>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>

  <!-- Custom Navbar CSS -->
  <link href="<?= base_url('assets/Users/css/navbar.css?v=' . time()) ?>" rel="stylesheet"/>

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f7f9fb;
      padding-top: calc(70px + 32px);
    }

    #main {
      max-width: 700px;
      margin: 40px auto 0;
      padding: 0 1rem;
    }

    .history-header {
      padding: 1rem 1.5rem;
      color: #4a5568;
      font-size: 1rem;
      font-weight: 600;
      background-color: #e2f0ef;
      border-radius: 12px 12px 0 0;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .status-dropdown .btn {
      background-color: transparent;
      color: #2c7a7b;
      border: 1px solid #2c7a7b;
      font-weight: 500;
    }

    .status-dropdown .btn:hover {
      background-color: #2c7a7b;
      color: #fff;
    }

    .transaction-list {
      background-color: #ffffff;
      border-radius: 0 0 12px 12px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
      max-height: 70vh;
      overflow-y: auto;
    }

    .history-subheader {
      padding: 0.75rem 1.5rem;
      font-size: 1.05rem;
      font-weight: 600;
      background-color: #ffffff;
      color: #2d3748;
      border-bottom: 1px solid #e2e8f0;
      position: sticky;
      top: 0;
      z-index: 5;
    }

    .transaction-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1rem 1.5rem;
      border-bottom: 1px solid #edf2f7;
      transition: background-color 0.2s ease;
      cursor: pointer;
    }

    .transaction-item:hover {
      background-color: #f1f5f9;
    }

    .transaction-item:last-child {
      border-bottom: none;
    }

    .transaction-details {
      display: flex;
      flex-direction: column;
      text-align: left;
    }

    .transaction-time {
      font-size: 0.8rem;
      color: #a0aec0;
      margin-bottom: 0.25rem;
    }

    .transaction-name {
      font-weight: 600;
      color: #2d3748;
      font-size: 1rem;
    }

    .transaction-amount {
      font-weight: 600;
      font-size: 1rem;
      text-align: right;
    }

    .amount-credit { color: #2f855a; }
    .amount-debit { color: #c53030; }
    .amount-pending { color: #d69e2e; }

    @media (max-width: 576px) {
      .modal-dialog {
        max-width: 100%;
        margin: 0;
        height: 100vh;
      }
      .modal-content {
        height: 100%;
        border-radius: 0;
      }
    }
  </style>
</head>
<body>
<?= $this->include('Users/header') ?>

<main id="main">
  <div class="container-fluid p-0">
    <div class="history-header bg-dark text-light rounded-top-4 d-flex justify-content-between align-items-center p-3">
      <span>As of <?= date('M d, Y') ?></span>

      <div class="dropdown status-dropdown">
        <button class="btn btn-outline-light dropdown-toggle" type="button" id="statusDropdown" data-bs-toggle="dropdown" aria-expanded="false">
          Select Status
        </button>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="statusDropdown">
          <li><a class="dropdown-item" href="#">All</a></li>
          <li><a class="dropdown-item" href="#">Successful</a></li>
          <li><a class="dropdown-item" href="#">Unsuccessful</a></li>
          <li><a class="dropdown-item" href="#">Pending</a></li>
        </ul>
      </div>
    </div>

    <div class="transaction-list">
      <?php
      $currentDate = '';
      foreach ($payments as $payment):
          // Use paid_at if available, else created_at
          $paymentDate = date('F d, Y', strtotime($payment['paid_at'] ?? $payment['created_at']));
          $time = date('h:i A', strtotime($payment['paid_at'] ?? $payment['created_at']));
          
          // Show date subheader if date changes
          if ($paymentDate !== $currentDate):
              $currentDate = $paymentDate;
      ?>
        <div class="history-subheader"><?= $currentDate ?></div>
      <?php endif;

          // Determine amount prefix and class
          if ($payment['status'] === 'paid') {
              $amount_class = 'amount-credit';
              $amount_prefix = '+';
          } elseif ($payment['status'] === 'pending') {
              $amount_class = 'amount-pending';
              $amount_prefix = '-';
          } else {
              $amount_class = 'amount-debit';
              $amount_prefix = '-';
          }

          $modalId = 'modalT' . $payment['id'];
      ?>
        <div class="transaction-item" data-bs-toggle="modal" data-bs-target="#<?= $modalId ?>">
          <div class="transaction-details">
            <div class="transaction-time"><?= esc($time) ?></div>
            <div class="transaction-name">
                <?= esc($payment['method'] === 'manual' ? 'Manual Payment' : 'Online Payment') ?>
                - <span class="<?= $amount_class ?>"><?= ucfirst($payment['status']) ?></span>
            </div>
          </div>
          <div class="transaction-amount <?= $amount_class ?>">
            ₱<?= $amount_prefix . number_format($payment['amount'], 2) ?>
          </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="<?= $modalId ?>" tabindex="-1" aria-labelledby="<?= $modalId ?>Label" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
              <div class="modal-header bg-dark text-light rounded-top-4">
                <h5 class="modal-title" id="<?= $modalId ?>Label">Receipt - <?= esc($payment['reference_number']) ?></h5>
              </div>
              <div class="modal-body bg-light" id="receiptContent<?= $payment['id'] ?>">
                <p><strong>Time:</strong> <?= esc($time) ?></p>
                <p><strong>Transaction:</strong> <?= esc($payment['method'] === 'manual' ? 'Manual Payment' : 'Online Payment') ?></p>
                <p><strong>Amount:</strong> ₱<?= number_format($payment['amount'], 2) ?></p>
                <p><strong>Status:</strong> <?= ucfirst($payment['status']) ?></p>
                <p><strong>Reference ID:</strong> <?= esc($payment['reference_number']) ?></p>
              </div>
              <div class="modal-footer bg-light border-top-0">
                <button type="button" class="btn btn-dark px-4" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary px-4" onclick="downloadReceipt(<?= $payment['id'] ?>)">Download</button>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</main>

<?= $this->include('Users/footer') ?>
<a href="#" id="scrollTop" class="scroll-top">↑</a>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
document.querySelectorAll('.status-dropdown .dropdown-item').forEach(item => {
  item.addEventListener('click', function(e) {
    e.preventDefault();

    const selected = this.textContent.trim();
    const dropdownBtn = document.getElementById('statusDropdown');
    dropdownBtn.textContent = selected;

    // Reset visibility
    document.querySelectorAll('.transaction-item').forEach(el => el.style.display = 'flex');

    // Filtering logic
    if (selected === 'Successful') {
      document.querySelectorAll('.transaction-item').forEach(el => {
        if (el.querySelector('.transaction-amount').classList.contains('amount-debit') || 
            el.querySelector('.transaction-amount').classList.contains('amount-pending')) {
          el.style.display = 'none';
        }
      });
    } else if (selected === 'Unsuccessful') {
      document.querySelectorAll('.transaction-item').forEach(el => {
        if (el.querySelector('.transaction-amount').classList.contains('amount-credit') || 
            el.querySelector('.transaction-amount').classList.contains('amount-pending')) {
          el.style.display = 'none';
        }
      });
    } else if (selected === 'Pending') {
      document.querySelectorAll('.transaction-item').forEach(el => {
        if (!el.querySelector('.transaction-amount').classList.contains('amount-pending')) {
          el.style.display = 'none';
        }
      });
    } 
  });
});

function downloadReceipt(id) {
    const element = document.getElementById('receiptContent' + id);
    html2canvas(element).then(canvas => {
        const link = document.createElement('a');
        link.download = 'Receipt-' + id + '.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
    });
}
</script>

</body>
</html>
