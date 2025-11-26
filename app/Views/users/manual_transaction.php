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
    .upload-box.dragover { background: #e1e7ff; border-color: #4f46e5; }
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
    .filename {
      font-size: 0.9rem;
      color: #374151;
      margin-top: 0.35rem;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }
    .btn-spinner {
      display: inline-flex;
      align-items: center;
      gap: .5rem;
    }
    .spinner-dot {
      width: 10px;
      height: 10px;
      border-radius: 50%;
      background: rgba(255,255,255,0.9);
      box-shadow: 0 0 0 0 rgba(255,255,255,0.6);
      animation: spinner-pulse 1s infinite;
    }
    @keyframes spinner-pulse { 0% { transform: scale(1); } 50% { transform: scale(1.4); } 100% { transform: scale(1); } }
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

    <!-- Fee waived / note placeholder -->
    <div id="feeWaivedNote" class="alert alert-info d-none small" role="status">Note: Fee waived for manual transactions.</div>

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
          inputmode="numeric"
          pattern="\d*"
          maxlength="13"
          title="Numbers only, maximum 13 digits"
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
          <button type="button" id="chooseFileBtn" class="btn btn-outline-primary btn-sm">Choose File</button>
          <div id="chosenFilename" class="filename d-none"></div>
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
        // Safely parse numeric parts and compute net due = carryover + current - payments
        const carryover = parseFloat(data.carryover || 0) || 0;
        const current = parseFloat(data.amount_due || 0) || 0;
        const payments = parseFloat(data.paymentsMade || data.payments || 0) || 0;
        const netDue = Math.max(0, (carryover + current - payments));

        // expose netDue globally for later validation
        window.netDueAmount = netDue;

        // Display bill details with breakdown and computed net due
        $('#billDetails').html(
          `<span class="bill-details-icon"><i class="bi bi-file-earmark-text"></i></span>
           <div class="bill-details-info">
             <div><strong>Bill #:</strong> ${data.bill_no}</div>
             <div><strong>Billing Month:</strong> ${data.billing_month}</div>
             <div><strong>Due Date:</strong> ${data.due_date}</div>
             <div style="margin-top:.5rem; font-size:.95rem; color:#6b7280">
               <div>Carryover: ₱${carryover.toFixed(2)}</div>
               <div>Current Charges: ₱${current.toFixed(2)}</div>
               <div>Payments Made: -₱${payments.toFixed(2)}</div>
             </div>
             <div class="bill-amount" style="margin-top:.5rem"><i class="bi bi-cash-stack me-1"></i>₱${netDue.toFixed(2)}</div>
           </div>`
        ).css('display', 'flex');

        // enforce max on the amount input so it cannot exceed billed net due
        $('#amount').attr('max', netDue.toFixed(2));

        // Prefill amount input: prefer explicit `amount` query param; otherwise use server-calculated netDue
        if (amount) {
          // ensure numeric
          let amt = parseFloat(amount) || 0;
          if (amt > netDue) {
            amt = netDue;
            alertBox.classList.remove('d-none');
            alertBox.textContent = `Entered amount exceeded billed amount. Amount adjusted to ₱${netDue.toFixed(2)}.`;
          } else {
            // hide any previous alert
            alertBox.classList.add('d-none');
          }
          $('#amount').val(amt.toFixed(2));
          // show fee-waived note when arriving from manual flow (if present)
          $('#feeWaivedNote').removeClass('d-none');
        } else {
          $('#amount').val(netDue.toFixed(2));
          $('#feeWaivedNote').addClass('d-none');
        }

        // Client-side enforcement: if user types a value greater than max, clamp and show warning
        $('#amount').on('input', function() {
          const max = parseFloat($(this).attr('max')) || 0;
          let val = parseFloat($(this).val()) || 0;
          if (val > max) {
            $(this).val(max.toFixed(2));
            alertBox.classList.remove('d-none');
            alertBox.textContent = `Amount cannot exceed billed amount (₱${max.toFixed(2)}).`;
          } else {
            alertBox.classList.add('d-none');
          }
        });
      });
    }

    // Screenshot preview
    const screenshotInput = document.getElementById('screenshot');
    const preview = document.getElementById('preview');
    const previewContainer = document.getElementById('previewContainer');
    const alertBox = document.getElementById('alertBox');
    const chooseFileBtn = document.getElementById('chooseFileBtn');
    const chosenFilename = document.getElementById('chosenFilename');

    // Helper to show alerts with type: 'warning'|'danger'|'success'|'info'
    function showAlert(type, message, autoHideMs) {
      alertBox.className = 'alert d-block';
      alertBox.classList.add('alert-' + (type || 'warning'));
      alertBox.textContent = message;
      if (autoHideMs && autoHideMs > 0) {
        setTimeout(function() { alertBox.classList.add('d-none'); }, autoHideMs);
      }
    }

    // Reference input: allow only digits and enforce maxlength=13 on input
    const refInput = document.getElementById('referenceNumber');
    if (refInput) {
      refInput.addEventListener('input', function() {
        // remove non-digits and trim to 13 characters
        this.value = this.value.replace(/\D/g, '').slice(0,13);
      });
    }

    // handle file selection and preview
    function handleFileSelection(file) {
      if (!file) {
        previewContainer.style.display = 'none';
        preview.src = '';
        chosenFilename.classList.add('d-none');
        chosenFilename.textContent = '';
        return;
      }
      // show filename
      chosenFilename.textContent = file.name;
      chosenFilename.classList.remove('d-none');

      const reader = new FileReader();
      reader.onload = function(e) {
        preview.src = e.target.result;
        previewContainer.style.display = 'block';
      };
      reader.readAsDataURL(file);
    }

    screenshotInput.addEventListener('change', function() {
      handleFileSelection(this.files[0]);
    });

    // Drag & drop support for upload box
    const uploadBox = document.querySelector('.upload-box');
    if (uploadBox) {
      uploadBox.addEventListener('dragover', function(e) { e.preventDefault(); uploadBox.classList.add('dragover'); });
      uploadBox.addEventListener('dragleave', function(e) { e.preventDefault(); uploadBox.classList.remove('dragover'); });
      uploadBox.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadBox.classList.remove('dragover');
        const files = e.dataTransfer.files;
        if (files && files[0]) {
          screenshotInput.files = files;
          handleFileSelection(files[0]);
        }
      });
      // wire choose file button to trigger input
      if (chooseFileBtn) chooseFileBtn.addEventListener('click', function() { screenshotInput.click(); });
    }

    // Intercept form submit
    $('#proofForm').on('submit', function(e) {
      e.preventDefault(); // stop normal submit

      const ref = $('#referenceNumber').val().trim();
      if (!ref) {
        showAlert('warning', 'Please enter a reference number.');
        return;
      }
        // quick client-side validations before calling server
        const amountVal = parseFloat($('#amount').val()) || 0;
        if (window.netDueAmount !== undefined && amountVal > window.netDueAmount) {
          showAlert('warning', `Amount cannot exceed billed amount (₱${window.netDueAmount.toFixed(2)}).`);
          return;
        }
        if (!screenshotInput.files || !screenshotInput.files[0]) {
          showAlert('warning', 'Please attach a screenshot of your payment.');
          return;
        }

        // disable submit and show spinner
        const submitBtn = $(this).find('button[type=submit]');
        submitBtn.prop('disabled', true);
        const origHtml = submitBtn.html();
        submitBtn.html('<span class="btn-spinner"><span class="spinner-dot"></span>Submitting...</span>');

        $.post("<?= site_url('users/checkReference') ?>", { referenceNumber: ref }, function(data) {
          if (data && data.exists) {
            showAlert('danger', 'This reference number has already been used. Please enter a different one.');
            submitBtn.prop('disabled', false).html(origHtml);
          } else {
            // proceed with actual form submit (allow file upload)
            showAlert('info', 'Submitting proof...');
            e.currentTarget.submit();
          }
        }, 'json')
        .fail(function() {
          showAlert('danger', 'Unable to verify the reference right now. Please try again later.');
          submitBtn.prop('disabled', false).html(origHtml);
        });
    });
  </script>
</body>
</html>
