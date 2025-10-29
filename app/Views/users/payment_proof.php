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
      background: #f9fafc;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      margin: 0;
    }
    .card {
      border-radius: 0.75rem;
      border: none;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
      max-width: 420px;
      width: 100%;
      padding: 1rem;
    }
    h5 { font-size: 1.1rem; color: #2c3e50; }
    .upload-box {
      border: 2px dashed #6c63ff;
      border-radius: 8px;
      background: #f0f3ff;
      padding: 1.5rem 1rem;
      transition: all 0.3s ease;
      cursor: pointer;
    }
    .upload-box:hover { background: #e9ecff; }
    .upload-box i { font-size: 1.8rem; color: #6c63ff; }
    .btn-primary { background: #6c63ff; border: none; font-size: 0.9rem; padding: 0.6rem; }
    .form-control { border-radius: 8px; font-size: 0.9rem; padding: 0.5rem 0.75rem; }
    label { font-size: 0.85rem; }
    #previewContainer { display: none; margin-top: 12px; }
    #previewContainer .preview-frame {
      border: 1px solid #e0e0e0;
      border-radius: 8px;
      padding: 6px;
      background: #fff;
      box-shadow: 0 2px 6px rgba(0,0,0,0.05);
      display: inline-block;
    }
    #preview { max-height: 200px; border-radius: 6px; object-fit: contain; }
  </style>
</head>
<body>
  <div class="card">
    <h5 class="fw-semibold text-center mb-3">
      <i class="bi bi-receipt-cutoff text-primary me-1"></i>Upload Payment Proof
    </h5>

    <!-- Alert placeholder -->
    <div id="alertBox" class="alert alert-warning d-none" role="alert"></div>

    <form id="proofForm" method="post" enctype="multipart/form-data" action="<?= site_url('users/uploadProof') ?>">
      <!-- Reference Number -->
      <div class="mb-2">
        <label for="referenceNumber" class="form-label">Transaction Reference Number</label>
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
          <p class="mb-1 text-muted small">Click to upload</p>
          <input
            type="file"
            name="screenshot"
            id="screenshot"
            class="form-control d-none"
            accept="image/*"
            required
          />
          <button type="button" class="btn btn-outline-primary btn-sm mt-1">Choose File</button>
        </div>

        <!-- Preview -->
        <div id="previewContainer" class="text-center">
          <p class="small text-muted mb-2">Preview</p>
          <div class="preview-frame">
            <img id="preview" class="img-fluid" alt="Preview">
          </div>
        </div>
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