<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Aquabill | Verify Account</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: radial-gradient(circle at bottom left, #0072ff, #004aad);
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      overflow: hidden;
      position: relative;
      padding: 1rem; /* Added for better mobile spacing */
      font-size: 16px; /* Base font size for scalability */
    }

    body::before, body::after {
      content: "";
      position: absolute;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.05);
      z-index: 0;
    }

    body::before { 
      width: 600px; 
      height: 600px; 
      bottom: -200px; 
      left: -200px; 
    }
    body::after { 
      width: 500px; 
      height: 500px; 
      top: -100px; 
      right: -150px; 
    }

    .verify-card {
      position: relative;
      width: 100%;
      max-width: 420px;
      background: #fff;
      border-radius: 25px;
      padding: 3rem 2.5rem 2rem;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
      text-align: center;
      z-index: 1;
    }

    .title {
      font-weight: 700;
      font-size: 1.6rem;
      color: #004aad;
      margin-bottom: 0.3rem;
      line-height: 1.2;
    }

    .subtitle {
      font-size: 0.9rem;
      color: #6c757d;
      margin-bottom: 1.8rem;
      line-height: 1.4;
    }

    .form-control {
      border-radius: 12px;
      padding: 1rem;
      font-size: 1.1rem;
      text-align: center;
      letter-spacing: 3px;
      font-weight: 600;
      line-height: 1.5;
    }

    .btn-verify {
      background-color: #004aad;
      color: #fff;
      border-radius: 12px;
      padding: 0.75rem;
      font-weight: 600;
      font-size: 1.1rem;
      border: none;
      transition: all 0.3s ease;
      line-height: 1.2;
    }

    .btn-verify:hover {
      background-color: #003b8f;
      transform: translateY(-2px);
    }

    .resend-section {
      margin-top: 1rem;
      font-size: 0.9rem;
      line-height: 1.4;
    }

    .resend-section button {
      border: none;
      background: none;
      color: #004aad;
      font-weight: 600;
      text-decoration: none;
    }

    .resend-section button:disabled {
      color: #aaa;
      cursor: not-allowed;
    }

    .toast-container {
      z-index: 2000;
    }

    /* Large screens (laptops and desktops) */
    @media (min-width: 1200px) {
      .title {
        font-size: 1.8rem; /* Slightly larger for better readability */
      }
      .subtitle {
        font-size: 1rem;
      }
      .form-control {
        font-size: 1.2rem;
        padding: 1.1rem;
      }
      .btn-verify {
        font-size: 1.2rem;
        padding: 0.8rem;
      }
      .resend-section {
        font-size: 1rem;
      }
    }

    /* Tablet and smaller desktop adjustments */
    @media (max-width: 992px) {
      body::before {
        width: 400px;
        height: 400px;
        bottom: -150px;
        left: -150px;
      }
      body::after {
        width: 350px;
        height: 350px;
        top: -75px;
        right: -100px;
      }
      .title {
        font-size: 1.5rem;
      }
      .subtitle {
        font-size: 0.9rem;
      }
      .form-control {
        font-size: 1.1rem;
      }
      .btn-verify {
        font-size: 1.1rem;
      }
      .resend-section {
        font-size: 0.9rem;
      }
    }

    /* Mobile adjustments */
    @media (max-width: 768px) {
      .verify-card { 
        padding: 3.5rem 2rem 2rem; 
        max-width: 90%; 
        margin: 0 auto; /* Center on small screens */
      }
      .title {
        font-size: 1.4rem;
      }
      .subtitle {
        font-size: 0.85rem;
        margin-bottom: 1.5rem;
      }
      .form-control {
        font-size: 1rem;
        padding: 0.9rem;
        letter-spacing: 2px; /* Slightly reduce spacing for smaller screens */
      }
      .btn-verify {
        font-size: 1rem;
        padding: 0.7rem;
      }
      .resend-section {
        font-size: 0.85rem;
      }
    }

    /* Very small screens (phones in portrait) */
    @media (max-width: 480px) {
      body {
        padding: 0.5rem;
      }
      body::before, body::after {
        display: none; /* Hide background circles for better performance */
      }
      .verify-card {
        padding: 3rem 1.5rem 1.5rem;
        border-radius: 20px;
      }
      .title {
        font-size: 1.3rem;
      }
      .subtitle {
        font-size: 0.8rem;
        margin-bottom: 1.2rem;
      }
      .form-control {
        font-size: 0.95rem;
        padding: 0.8rem;
        letter-spacing: 2px;
      }
      .btn-verify {
        font-size: 0.95rem;
        padding: 0.65rem;
      }
      .resend-section {
        font-size: 0.8rem;
      }
    }
  </style>
</head>
<body>

<?php if (session()->getFlashdata('error')): ?>
<div class="toast-container position-fixed top-0 start-50 translate-middle-x p-3">
  <div id="errorToast" class="toast align-items-center text-white bg-danger border-0 show">
    <div class="d-flex">
      <div class="toast-body text-center">
        <?= session()->getFlashdata('error') ?>
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  </div>
</div>
<?php endif; ?>

<?php if (session()->getFlashdata('message')): ?>
<div class="toast-container position-fixed top-0 start-50 translate-middle-x p-3">
  <div id="successToast" class="toast align-items-center text-white bg-success border-0 show">
    <div class="d-flex">
      <div class="toast-body text-center">
        <?= session()->getFlashdata('message') ?>
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  </div>
</div>
<?php endif; ?>

<div class="verify-card">
  <h2 class="title">Verify Your Account</h2>
  <p class="subtitle">Enter the 6-digit code sent to your email</p>

  <form action="<?= site_url('verifyOtp') ?>" method="post">
    <input type="text" name="otp" maxlength="6" class="form-control mb-3" placeholder="Enter code" required>
    <button type="submit" class="btn-verify w-100">Verify</button>
  </form>

  <div class="resend-section">
    <small id="countdownText">
      Resend available in <span id="timer">60</span> seconds
    </small><br>
    <button id="resendBtn" type="button" disabled onclick="resendOtp()">Resend OTP</button>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Countdown timer
  let timeLeft = 60;
  const timerEl = document.getElementById('timer');
  const resendBtn = document.getElementById('resendBtn');
  const countdownText = document.getElementById('countdownText');

  const countdown = setInterval(() => {
    timeLeft--;
    timerEl.textContent = timeLeft;

    if (timeLeft <= 0) {
      clearInterval(countdown);
      countdownText.textContent = "You can now resend the OTP";
      resendBtn.disabled = false;
    }
  }, 1000);

  function resendOtp() {
    window.location.href = "<?= site_url('resendOtp') ?>";
  }
</script>
</body>
</html>
