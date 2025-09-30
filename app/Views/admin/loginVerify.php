<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin OTP Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow-lg p-4" style="width: 400px;">
        
        <!-- Flash Messages -->
        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <?php if(session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <!-- Header -->
        <h4 class="mb-3 text-center">OTP Verification</h4>
        <p class="text-muted text-center">We’ve sent a 6-digit code to your email.</p>

        <!-- OTP Form -->
        <form action="<?= site_url('admin/loginVerify') ?>" method="post">
            <?= csrf_field() ?>
            <div class="mb-3">
                <input type="text" name="otp" maxlength="6" class="form-control text-center" placeholder="Enter OTP" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Verify</button>
        </form>

        <!-- Resend -->
        <div class="text-center mt-3">
            <small id="countdownText">Resend available in <span id="timer">60</span> seconds</small>
            <button id="resendBtn" type="button" class="btn btn-link mt-2" 
                    onclick="window.location.href='<?= site_url('admin/resendOtp') ?>'" disabled> Resend OTP
            </button>
        </div>
    </div>
</div>

<!-- Countdown Script -->
<script>
    let timeLeft = 60;
    let timerEl = document.getElementById('timer');
    let resendBtn = document.getElementById('resendBtn');
    let countdown = setInterval(() => {
        timeLeft--;
        timerEl.textContent = timeLeft;

        if (timeLeft <= 0) {
            clearInterval(countdown);
            document.getElementById('countdownText').textContent = "You can now resend the OTP";
            resendBtn.disabled = false;
        }
    }, 1000);
</script>

</body>
</html>
