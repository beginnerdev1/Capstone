<!DOCTYPE html>
<html lang ='en'>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ENTER OTP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">  
</head>
<body class="d-flex align-items-center justify-content-center vh-100 bg-light">
    <div class="card shadow p-4" style ="width:400px;">
        <h4 class="text-center mb-3">Enter One-Time Password (OTP)</h4>
        <?php if(session()->getFlashdata('error')):?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>
        <?php if(session()->getFlashdata('info')):?>
            <div class="alert alert-info"><?= session()->getFlashdata('info') ?></div>
        <?php endif; ?>

        <form action="<?= base_url('superadmin/check-code') ?>" method="post">
            <div class="mb-3">
                <input type="text" name="admin_code" class="form-control" placeholder="Enter Admin Code" required inputmode="numeric" pattern="\d{6}" maxlength="6" oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,6)">
            </div>
            <button type="submit" class="btn btn-primary w-100">verify</button>
        </form>
        <div class="text-center mt-3">
            <form id="resendForm" action="<?= base_url('superadmin/resend-code') ?>" method="post" style="display:inline;">
                <?= csrf_field() ?>
                <button id="resendBtn" type="submit" class="btn btn-link">Resend code</button>
            </form>
            <div id="resendCountdown" class="small text-muted mt-2" style="display:none;">You can resend in <span id="resendSeconds">60</span>s</div>
        </div>

        <script>
        (function(){
            // Avoid starting multiple timers if script runs twice
            if (window.__superadmin_otp_timer_started) return;
            window.__superadmin_otp_timer_started = true;

            var KEY = 'superadmin_otp_last_resend';
            var COOLDOWN = 60; // seconds
            // Use querySelector to pick the first matching element if duplicates exist
            var btn = document.querySelector('#resendBtn');
            var form = document.querySelector('#resendForm');
            var countdown = document.querySelector('#resendCountdown');
            var secondsSpan = document.querySelector('#resendSeconds');

            function startCooldown(remaining) {
                if (!btn || !countdown || !secondsSpan) return;
                btn.disabled = true;
                countdown.style.display = 'block';
                secondsSpan.textContent = remaining;
                var iv = setInterval(function(){
                    remaining--; if (remaining <= 0) {
                        clearInterval(iv);
                        btn.disabled = false;
                        countdown.style.display = 'none';
                        try { localStorage.removeItem(KEY); } catch(e){}
                        btn.textContent = 'Resend code';
                        return;
                    }
                    secondsSpan.textContent = remaining;
                    btn.textContent = 'Resend code (' + remaining + 's)';
                }, 1000);
            }

            // On submit, store timestamp and start cooldown immediately
            if (form) {
                form.addEventListener('submit', function(e){
                    try { localStorage.setItem(KEY, Date.now().toString()); } catch(e){}
                    // start cooldown immediately for UX; server enforces limits
                    startCooldown(COOLDOWN);
                    // allow form to submit normally
                });
            }

            // On load check localStorage for existing timestamp
            try {
                var ts = parseInt(localStorage.getItem(KEY) || '0', 10);
                if (ts && !isNaN(ts)) {
                    var elapsed = Math.floor((Date.now() - ts) / 1000);
                    var remaining = COOLDOWN - elapsed;
                    if (remaining > 0) startCooldown(remaining);
                }
            } catch(e) { /* ignore storage errors */ }
        })();
        </script>
    </div>
</body> 