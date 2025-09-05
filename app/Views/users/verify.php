<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Account</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">

    <!-- Custom CSS -->
    <link href="<?= base_url('assets/login/registerstyle.css?v=' . time()) ?>" rel="stylesheet">
</head>
<body>

<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="row border rounded-5 p-4 bg-white shadow box-area">
        
        <!-- OTP Verify Section -->
        <div class="col-md-6 right-box">
            <form action="<?= site_url('verifyOtp') ?>" method="post">
                <div class="row align-items-center">
                    
                    <!-- Header -->
                    <div class="header-text mb-4">
                        <h2>Email Verification</h2>
                        <p>Enter the 6-digit code we sent to your email.</p>
                    </div>

                    <!-- OTP Input -->
                    <div class="input-group mb-3">
                        <input type="text" name="otp" maxlength="6" class="form-control form-control-lg bg-light fs-6 text-center" placeholder="Enter 6-digit code" required>
                    </div>

                    <!-- Verify Button -->
                    <div class="input-group mb-3 mt-3">
                        <button type="submit" class="btn btn-lg btn-primary w-100 fs-6">Verify</button>
                    </div>

                    <!-- Error Message -->
                    <?php if(session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger w-100">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>

                    <!-- Success Message -->
                    <?php if(session()->getFlashdata('message')): ?>
                        <div class="alert alert-success w-100">
                            <?= session()->getFlashdata('message') ?>
                        </div>
                    <?php endif; ?>

                    <!-- Countdown & Resend -->
                    <div class="row text-center mt-3">
                        <small id="countdownText">Resend available in <span id="timer">60</span> seconds</small>
                        <button id="resendBtn" type="button" class="btn btn-link mt-2" disabled onclick="resendOtp()">Resend OTP</button>
                    </div>
                </div>
            </form>
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

    function resendOtp() {
        // Call your backend route to resend OTP
        window.location.href = "<?= site_url('resendOtp') ?>";
    }
</script>

</body>
</html>
