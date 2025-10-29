<div class="container bg-light d-md-flex align-items-start p-4 rounded shadow">
  <!-- LEFT BOX -->
  <div class="card box1 shadow-sm p-md-5 p-4 me-md-4 mb-4 mb-md-0 flex-fill">
    <div class="fw-bolder mb-4 fs-3">
      <i class="fas fa-dollar-sign"></i>
      <span class="ps-1">599.00</span>
    </div>
    <div class="d-flex flex-column">
      <div class="d-flex align-items-center justify-content-between text">
        <span>Commission</span>
        <span>₱1.99</span>
      </div>
      <div class="d-flex align-items-center justify-content-between text mb-4">
        <span>Total</span>
        <span>₱600.99</span>
      </div>
      <hr class="mb-4" />
      <div class="mb-4">
        <span><i class="far fa-file-alt"></i><span class="ps-2">Invoice ID:</span></span>
        <div class="ps-3">SN8478042099</div>
      </div>
      <div class="mb-5">
        <span><i class="far fa-calendar-alt"></i><span class="ps-2">Next payment:</span></span>
        <div class="ps-3">22 July, 2018</div>
      </div>
      <div class="d-flex align-items-center justify-content-between text mt-5">
        <div>
          <div>Customer Support:</div>
          <div>Online chat 24/7</div>
        </div>
        <button type="button" class="btn btn-primary rounded-circle">
          <i class="fas fa-comment-alt"></i>
        </button>
      </div>
    </div>
  </div>

  <!-- RIGHT BOX -->
  <div class="card box2 shadow-sm flex-fill">
    <div class="d-flex align-items-center justify-content-between p-md-5 p-4">
      <h5 class="fw-bold m-0">Payment methods</h5>
    </div>

    <!-- NAV TABS -->
    <ul class="nav nav-tabs mb-3 px-md-4 px-2">
      <li class="nav-item">
        <a class="nav-link px-2 active" href="#" id="creditTab">Gcash Payment</a>
      </li>
      <li class="nav-item">
        <a class="nav-link px-2" href="#" id="mobileTab">Manual Payment</a>
      </li>
    </ul>
    <br />

    <!-- CREDIT CARD FORM -->
    <div id="creditContent">
      <form id="payForm" action="<?= site_url('users/createCheckout') ?>" method="post">
        <div class="row">
          <div class="col-12">
            <div class="d-flex flex-column px-md-5 px-4 mb-4">
              <label for="cardNumber">Credit Card</label>
              <div class="inputWithIcon position-relative">
                <input id="cardNumber" class="form-control" type="text" value="5136 1845 5468 3894" />
                <span class="position-absolute end-0 me-3">
                  <img src="https://www.freepnglogos.com/uploads/mastercard-png/mastercard-logo-logok-15.png" alt="Mastercard" width="40" />
                </span>
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="d-flex flex-column ps-md-5 px-md-0 px-4 mb-4">
              <label for="expiry">Expiration Date</label>
              <div class="inputWithIcon position-relative">
                <input id="expiry" type="text" class="form-control" value="05/20" />
                <i class="fas fa-calendar-alt position-absolute end-0 me-3"></i>
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="d-flex flex-column pe-md-5 px-md-0 px-4 mb-4">
              <label for="cvv">Code CVV</label>
              <div class="inputWithIcon position-relative">
                <input id="cvv" type="password" class="form-control" value="123" />
                <i class="fas fa-lock position-absolute end-0 me-3"></i>
              </div>
            </div>
          </div>

          <div class="col-12">
            <div class="d-flex flex-column px-md-5 px-4 mb-4">
              <label for="cardName">Name</label>
              <div class="inputWithIcon position-relative">
                <input id="cardName" class="form-control text-uppercase" type="text" value="Valdimir Berezovkiy" />
                <i class="far fa-user position-absolute end-0 me-3"></i>
              </div>
            </div>
          </div>

          <div class="col-12 px-md-5 px-4 mt-3">
            <button type="submit" class="btn btn-primary w-100">Pay ₱599.00</button>
          </div>
        </div>
      </form>
    </div>

    <!-- ALTERNATIVE / MOBILE PAYMENT -->
    <div id="mobileContent" style="display: none;">
      <div class="px-md-5 px-4 mb-4">
        <h6 class="fw-bold mb-3">Alternative Mobile Payment</h6>

        <!-- GCash Info -->
        <div class="text-center mb-4">
          <p class="fw-semibold">Pay via GCash</p>
          <div class="bg-light border rounded p-3 d-inline-block">
            <p class="mb-1">GCash Number:</p>
            <h5 id="gcashNumber" class="fw-bold text-primary">09XX-XXX-XXXX</h5>
            <img src="your_qr_code.png" alt="GCash QR Code" class="img-fluid rounded mt-3" width="150" />
            <div class="mt-3 d-flex justify-content-center gap-2">
              <button type="button" class="btn btn-outline-primary btn-sm" id="copyNumber">Copy Number</button>
              <a href="your_qr_code.png" download class="btn btn-outline-secondary btn-sm">Download QR</a>
            </div>
          </div>
        </div>

        <!-- Pay Button -->
        <div class="mb-4">
          <button type="button" class="btn btn-success w-100" id="createTransaction">Pay It</button>
        </div>

        <!-- Upload Proof (Improved Design) -->
        <div id="uploadSection" style="display: none;">
          <div class="card shadow-sm border-0 rounded-4 p-4">
            <h5 class="fw-bold text-center mb-4">
              <i class="bi bi-receipt-cutoff text-primary me-2"></i>Upload Payment Proof
            </h5>
            <form id="proofForm" method="post" enctype="multipart/form-data" action="<?= site_url('users/uploadProof') ?>">
              <div class="mb-3">
                <label for="referenceNumber" class="form-label fw-semibold">Transaction Reference Number</label>
                <input
                  type="text"
                  name="referenceNumber"
                  id="referenceNumber"
                  class="form-control border-2 rounded-3 py-2"
                  placeholder="Enter GCash reference number"
                  required
                />
              </div>

              <div class="mb-4">
                <label for="screenshot" class="form-label fw-semibold">Upload Screenshot</label>
                <div class="border border-2 border-dashed rounded-3 p-4 text-center bg-light" id="uploadBox">
                  <i class="bi bi-cloud-arrow-up fs-1 text-primary mb-3"></i>
                  <p class="mb-2 text-muted">Drag & drop or click to upload</p>
                  <input
                    type="file"
                    name="screenshot"
                    id="screenshot"
                    class="form-control d-none"
                    accept="image/*"
                    required
                  />
                  <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="uploadTrigger">
                    Choose File
                  </button>
                  <div id="previewContainer" class="mt-3"></div>
                </div>
              </div>

              <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                <i class="bi bi-send-fill me-2"></i>Submit Proof
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- SCRIPT -->
<script>
  const creditTab = document.getElementById('creditTab');
  const mobileTab = document.getElementById('mobileTab');
  const creditContent = document.getElementById('creditContent');
  const mobileContent = document.getElementById('mobileContent');
  const copyNumber = document.getElementById('copyNumber');
  const createTransaction = document.getElementById('createTransaction');
  const uploadSection = document.getElementById('uploadSection');
  const gcashNumber = document.getElementById('gcashNumber');

  creditTab.addEventListener('click', (e) => {
    e.preventDefault();
    creditContent.style.display = 'block';
    mobileContent.style.display = 'none';
    creditTab.classList.add('active');
    mobileTab.classList.remove('active');
  });

  mobileTab.addEventListener('click', (e) => {
    e.preventDefault();
    creditContent.style.display = 'none';
    mobileContent.style.display = 'block';
    mobileTab.classList.add('active');
    creditTab.classList.remove('active');
  });

  copyNumber.addEventListener('click', () => {
    navigator.clipboard.writeText(gcashNumber.textContent);
    alert('GCash number copied!');
  });

  createTransaction.addEventListener('click', () => {
    alert('Transaction created! Please upload your payment proof.');
    uploadSection.style.display = 'block';
    createTransaction.disabled = true;
  });

  // Upload trigger
  document.getElementById('uploadTrigger').addEventListener('click', function () {
    document.getElementById('screenshot').click();
  });

  // Preview uploaded image
  document.getElementById('screenshot').addEventListener('change', function (event) {
    const file = event.target.files[0];
    const previewContainer = document.getElementById('previewContainer');
    previewContainer.innerHTML = '';
    if (file) {
      const img = document.createElement('img');
      img.src = URL.createObjectURL(file);
      img.className = 'img-fluid rounded mt-2 shadow-sm';
      img.style.maxHeight = '200px';
      previewContainer.appendChild(img);
    }
  });
</script>
