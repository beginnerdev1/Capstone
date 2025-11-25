<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="height: 100vh;">
    <script></script>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <?php if(session()->getFlashdata('error')): ?>
                <div class="alert alert-danger">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <div class="card shadow">
                <div class="card-header text-center">
                    <h4>Super Admin Login</h4>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('superadmin/login') ?>" method="post">
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                    <div class="text-center mt-3">
                        <a href="#" id="show-forgot">Forgot password?</a>
                    </div>

                    <div id="forgot-section" class="mt-3" style="display:none;">
                        <div class="border rounded p-3 bg-white">
                            <h6>Reset Super Admin Password</h6>
                            <p class="small text-muted">Enter the email for the super admin account. We'll send a reset link or OTP if the account exists.</p>
                            <div id="forgot-alert"></div>
                            <form id="forgot-form">
                                <?= csrf_field() ?>
                                <div class="mb-2">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" id="forgot-email" class="form-control" required>
                                </div>
                                <div class="d-grid">
                                    <button id="forgot-submit" class="btn btn-outline-primary" type="submit">Send Reset Email</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

                <script>
                document.addEventListener('DOMContentLoaded', function(){
                    var toggle = document.getElementById('show-forgot');
                    var section = document.getElementById('forgot-section');
                    var form = document.getElementById('forgot-form');
                    var alertBox = document.getElementById('forgot-alert');

                    toggle && toggle.addEventListener('click', function(e){
                        e.preventDefault();
                        if (section.style.display === 'none') section.style.display = 'block'; else section.style.display = 'none';
                    });

                    if (form) {
                        form.addEventListener('submit', function(e){
                            e.preventDefault();
                            alertBox.innerHTML = '';
                            var btn = document.getElementById('forgot-submit');
                            btn.disabled = true;
                            var email = document.getElementById('forgot-email').value;
                            var data = new FormData(form);

                            fetch('<?= base_url('superadmin/forgot') ?>', {
                                method: 'POST',
                                body: data,
                                credentials: 'same-origin',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            }).then(function(res){
                                return res.json().catch(function(){ return { error: 'Invalid response from server' }; });
                            }).then(function(json){
                                if (json && (json.success || json.updated)) {
                                    alertBox.innerHTML = '<div class="alert alert-success">'+ (json.message || 'Reset instructions sent if the account exists.') +'</div>';
                                    form.reset();
                                } else if (json && json.error) {
                                    alertBox.innerHTML = '<div class="alert alert-danger">'+ json.error +'</div>';
                                } else {
                                    alertBox.innerHTML = '<div class="alert alert-info">'+ (json.message || 'If that email exists we will send reset instructions.') +'</div>';
                                }
                            }).catch(function(err){
                                alertBox.innerHTML = '<div class="alert alert-danger">Network error. Please try again later.</div>';
                            }).finally(function(){
                                btn.disabled = false;
                            });
                        });
                    }
                });
                </script>

                </body>
                </html>
