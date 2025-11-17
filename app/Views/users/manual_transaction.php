<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Upload Payment Proof</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      margin: 0;
    }
    .card {
      border-radius: 1.25rem;
      border: none;
      box-shadow: 0 8px 32px rgba(102,126,234,0.10);
      max-width: 440px;
      width: 100%;
      padding: 2rem 1.5rem 1.5rem 1.5rem;
      background: #fff;
      position: relative;
    }
    .card-header {
      background: linear-gradient(90deg, #667eea, #48bb78);
      border-radius: 1rem 1rem 0 0;
      color: #fff;
      text-align: center;
      padding: 1.2rem 1rem 1rem 1rem;
      margin: -2rem -1.5rem 1.5rem -1.5rem;
      box-shadow: 0 4px 16px rgba(102,126,234,0.08);
    }
    .card-header i {
      font-size: 2rem;
      margin-bottom: 0.5rem;
    }
    .bill-details-box {
      background: linear-gradient(90deg, #f8fafc 80%, #e2e8f0 100%);
      border-radius: 0.75rem;
      border: 1px solid #e5e7eb;
      padding: 1.2rem 1.2rem 1rem 1.2rem;
      margin-bottom: 1.2rem;
      font-size: 1rem;
      box-shadow: 0 2px 8px rgba(102,126,234,0.04);
      color: #374151;
      display: flex;
      align-items: flex-start;
      gap: 1.2rem;
    }
    .bill-details-icon {
      font-size: 2.2rem;
      color: #667eea;
      flex-shrink: 0;
      margin-right: 0.5rem;
      margin-top: 0.1rem;
    }
    .bill-details-info {
      flex: 1;
    }
    .bill-details-info div {
      margin-bottom: 0.3rem;
    }
    .bill-details-info .bill-amount {
      font-size: 1.15rem;
      font-weight: 700;
      color: #48bb78;
      margin-top: 0.2rem;
    }
    .form-label {
      font-weight: 500;
      color: #374151;
      margin-bottom: 0.3rem;
    }
    .form-control {
      border-radius: 0.7rem;
      font-size: 1rem;
      padding: 0.6rem 1rem;
      border: 1px solid #e5e7eb;
      background: #f9fafc;
      transition: border-color 0.2s;
    }
    .form-control:focus {
      border-color: #667eea;
      box-shadow: 0 0 0 2px rgba(102,126,234,0.12);
    }
    .upload-box {
      border: 2px dashed #667eea;
      border-radius: 0.75rem;
      background: #f0f3ff;
      padding: 1.5rem 1rem;
      transition: background 0.3s;
      cursor: pointer;
      margin-bottom: 0.5rem;
      position: relative;
    }
    .upload-box:hover { background: #e9ecff; }
    .upload-box i { font-size: 2.2rem; color: #667eea; }
    .upload-box .btn { margin-top: 0.7rem; }
    #previewContainer {
      display: none;
      margin-top: 10px;
      text-align: center;
    }
    #previewContainer .preview-frame {
      border: 1px solid #e0e0e0;
      border-radius: 8px;
      padding: 6px;
      background: #fff;
      box-shadow: 0 2px 6px rgba(0,0,0,0.05);
      display: inline-block;
    }
    #preview {
      max-height: 180px;
      border-radius: 8px;
      object-fit: contain;
      box-shadow: 0 2px 8px rgba(102,126,234,0.08);
    }
    .btn-primary {
      background: linear-gradient(90deg, #667eea, #48bb78);
      border: none;
      font-size: 1.05rem;
      padding: 0.7rem;
      border-radius: 0.8rem;
      font-weight: 600;
      box-shadow: 0 4px 16px rgba(102,126,234,0.08);
      transition: background 0.2s;
    }
    .btn-primary:hover {
      background: linear-gradient(90deg, #5a67d8, #38a169);
    }
    .alert {
      border-radius: 0.7rem;
      font-size: 0.97rem;
      margin-bottom: 1rem;
      padding: 0.7rem 1rem;
    }
    @media (max-width: 600px) {
      .card { padding: 1rem 0.5rem; }
      .card-header { margin: -1rem -0.5rem 1rem -0.5rem; padding: 1rem 0.5rem; }
      .bill-details-box { padding: 0.7rem 0.5rem; }
    }
  </style>
</head>
<body>
  <div class="card">
    <div class="card-header">
      <i class="bi bi-receipt-cutoff"></i>
      <div class="fw-semibold fs-5">Upload Payment Proof</div>
      <div class="small opacity-75">Please fill out the details below to verify your payment</div>
    </div>

    <!-- Bill Details will be injected here -->
    <div id="billDetails" class="bill-details-box" style="display:none;">
      <!-- JS will inject content here -->
    </div>

    <!-- Alert placeholder -->
    <div id="alertBox" class="alert alert-warning d-none" role="alert"></div>

    <form id="proofForm" method="post" enctype="multipart/form-data" action="<?= site_url('users/uploadProof') ?>">
      <!-- CHANGED: name and id -> billing_id so backend receives it -->
      <input type="hidden" name="billing_id" id="billing_id" value="">

      <!-- Reference Number -->
      <div class="mb-3">
        <label for="referenceNumber" class="form-label">GCash Reference Number</label>
        <input
          type="text"
          name="referenceNumber"
          id="referenceNumber"
          class="form-control"
          placeholder="Enter GCash reference number"
          required
        />
      </div>

      <!-- Upload Screenshot -->
      <div class="mb-3">
        <label for="screenshot" class="form-label">Upload Screenshot</label>
        <div class="upload-box text-center" onclick="document.getElementById('screenshot').click();">
          <i class="bi bi-cloud-arrow-up mb-2"></i>
          <div class="mb-1 text-muted small">Click to upload your payment screenshot</div>
          <input
            type="file"
            name="screenshot"
            id="screenshot"
            class="form-control d-none"
            accept="image/*"
            required
          />
          <button type="button" class="btn btn-outline-primary btn-sm">Choose File</button>
        </div>
        <!-- Preview -->
        <div id="previewContainer">
          <div class="small text-muted mb-2">Preview</div>
          <div class="preview-frame">
            <img id="preview" class="img-fluid" alt="Preview">
          </div>
        </div>
      </div>

      <!-- Amount Input -->
      <div class="mb-3">
        <label for="amount" class="form-label">Amount Paid (₱)</label>
        <input
          type="number"
          name="amount"
          id="amount"
          class="form-control"
          placeholder="Enter amount paid"
          min="0"
          step="0.01"
          required
        />
      </div>

      <!-- Submit -->
      <button type="submit" class="btn btn-primary w-100 fw-semibold">
        <i class="bi bi-send-fill me-1"></i>Submit Proof
      </button>
    </form>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // --- Bill Details Fetch ---
    function getQueryParam(name) {
      const url = new URL(window.location.href);
      return url.searchParams.get(name);
    }

    const billId = getQueryParam('bill_id');
    const amount = getQueryParam('amount');
    if (billId) {
      // CHANGED: set billing_id hidden input
      $('#billing_id').val(billId);

      $.getJSON('<?= site_url('users/getBillDetails') ?>', { bill_id: billId }, function(data) {
        // Display bill details
        $('#billDetails').html(
          `<span class="bill-details-icon"><i class="bi bi-file-earmark-text"></i></span>
           <div class="bill-details-info">
             <div><strong>Bill #:</strong> ${data.bill_no}</div>
             <div><strong>Billing Month:</strong> ${data.billing_month}</div>
             <div><strong>Due Date:</strong> ${data.due_date}</div>
             <div class="bill-amount"><i class="bi bi-cash-stack me-1"></i>₱${parseFloat(data.amount_due).toFixed(2)}</div>
           </div>`
        ).css('display', 'flex');
        // Set amount field
        if (amount) $('#amount').val(amount);
        else $('#amount').val(data.amount_due);
      });
    }

    // Screenshot preview
    const screenshotInput = document.getElementById('screenshot');
    const preview = document.getElementById('preview');
    const previewContainer = document.getElementById('previewContainer');
    const alertBox = document.getElementById('alertBox');

    screenshotInput.addEventListener('change', function() {
      const file = this.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          preview.src = e.target.result;
          previewContainer.style.display = 'block';
        };
        reader.readAsDataURL(file);
      } else {
        previewContainer.style.display = 'none';
        preview.src = '';
      }
    });

    // Intercept form submit
    $('#proofForm').on('submit', function(e) {
      e.preventDefault(); // stop normal submit

      const ref = $('#referenceNumber').val().trim();
      if (!ref) {
        alertBox.classList.remove('d-none');
        alertBox.textContent = 'Please enter a reference number.';
        return;
      }

      $.post("<?= site_url('users/checkReference') ?>", { referenceNumber: ref }, function(data) {
        if (data && data.exists) {
          alertBox.classList.remove('d-none');
          alertBox.textContent = 'This reference number has already been used. Please enter a different one.';
        } else {
          alertBox.classList.add('d-none');
          e.currentTarget.submit();
        }
      }, 'json')
      .fail(function() {
        alertBox.classList.remove('d-none');
        alertBox.textContent = 'Unable to verify the reference right now. Please try again.';
      });
    });
  </script>
</body>
</html>
