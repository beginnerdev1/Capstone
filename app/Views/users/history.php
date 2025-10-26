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

    .history-subheader {
      padding: 0.75rem 1.5rem;
      font-size: 1.05rem;
      font-weight: 600;
      background-color: #ffffff;
      color: #2d3748;
      border-bottom: 1px solid #e2e8f0;
      position: sticky;
      top: 70px; /* stays below navbar */
      z-index: 10;
    }

    .transaction-list {
      background-color: #ffffff;
      border-radius: 0 0 12px 12px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
      max-height: 70vh;
      overflow-y: auto;
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
      <div class="history-header">
        <span>As of <?= date('M d, Y') ?></span>

        <div class="dropdown status-dropdown">
          <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="statusDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            Select Status
          </button>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="statusDropdown">
            <li><a class="dropdown-item" href="#">Successful</a></li>
            <li><a class="dropdown-item" href="#">Unsuccessful</a></li>
            <li><a class="dropdown-item" href="#">Waiting for Payment</a></li>
          </ul>
        </div>
      </div>

      <!-- Sticky "Today" -->
      <div class="history-subheader">Today</div>

      <div class="transaction-list">
        <?php
        $payments = [
          ['id' => 1, 'time' => '9:20 AM', 'name' => 'Cash-in', 'amount' => 300.00, 'is_credit' => true, 'ref_id' => 'CSH-920-A'],
          ['id' => 2, 'time' => '8:51 AM', 'name' => 'Cashin from Security Bank Co...', 'amount' => 1074.00, 'is_credit' => true, 'ref_id' => 'CSH-851-B'],
          ['id' => 3, 'time' => '7:15 AM', 'name' => 'Send Money', 'amount' => 100.00, 'is_credit' => false, 'ref_id' => 'SND-715-C'],
          ['id' => 4, 'time' => '7:06 AM', 'name' => 'Send Money', 'amount' => 1666.00, 'is_credit' => false, 'ref_id' => 'SND-706-D'],
          ['id' => 5, 'time' => '7:05 AM', 'name' => 'Cashin from Security Bank Co...', 'amount' => 500.00, 'is_credit' => true, 'ref_id' => 'CSH-705-E'],
          ['id' => 6, 'time' => '7:01 AM', 'name' => 'Online Payment - Web Pay', 'amount' => 2248.92, 'is_credit' => false, 'ref_id' => 'PAY-701-F'],
          ['id' => 7, 'time' => '6:59 AM', 'name' => 'Cashin from Security Bank ...', 'amount' => 3400.00, 'is_credit' => true, 'ref_id' => 'CSH-659-G'],
        ];

        foreach ($payments as $payment):
          $amount_prefix = $payment['is_credit'] ? '+' : '-';
          $amount_class = $payment['is_credit'] ? 'amount-credit' : 'amount-debit';
          $modalId = 'modalT' . $payment['id'];
        ?>
          <div class="transaction-item" data-bs-toggle="modal" data-bs-target="#<?= $modalId ?>">
            <div class="transaction-details">
              <div class="transaction-time"><?= esc($payment['time']) ?></div>
              <div class="transaction-name"><?= esc($payment['name']) ?></div>
            </div>
            <div class="transaction-amount <?= $amount_class ?>">
              ₱<?= $amount_prefix . number_format($payment['amount'], 2) ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </main>

  <?php foreach ($payments as $payment):
    $amount_prefix = $payment['is_credit'] ? '+' : '-';
    $modalId = 'modalT' . $payment['id'];
  ?>
    <div class="modal fade" id="<?= $modalId ?>" tabindex="-1" aria-labelledby="<?= $modalId ?>Label" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="<?= $modalId ?>Label"><?= esc($payment['name']) ?> Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p><strong>Transaction:</strong> <?= esc($payment['name']) ?></p>
            <p><strong>Time:</strong> <?= esc($payment['time']) ?></p>
            <p><strong>Amount:</strong> ₱<?= $amount_prefix . number_format($payment['amount'], 2) ?></p>
            <p><strong>Reference ID:</strong> <?= esc($payment['ref_id']) ?></p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <?php if ($payment['is_credit']): ?>
              <a href="<?= base_url('users/payments/download/' . $payment['id']) ?>" class="btn btn-primary">Download Receipt</a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  <?php endforeach; ?>

  <?= $this->include('Users/footer') ?>
  <a href="#" id="scrollTop" class="scroll-top">↑</a>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
