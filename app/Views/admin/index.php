<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Dashboard - SB Admin</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="<?php echo base_url('assets/admin/css/styles.css') ?>" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    </head>
    <body class="sb-nav-fixed">
     <?php require_once(APPPATH . 'Views/admin/navbar.php'); ?>  
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Dashboard</h1>
                    <?php require_once(APPPATH . 'Views/admin/set_password.php'); ?>
                    <div class="row">
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-primary text-white mb-4">
                                <div class="card-body">Registered Users</div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-white stretched-link" href="<?= base_url('admin/registeredUsers') ?>">View Details</a>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-warning text-white mb-4">
                                <div class="card-body">Ongoing Bills</div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-white stretched-link" href="<?= base_url('admin/billings') ?>">View Details</a>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-success text-white mb-4">
                                <div class="card-body">Paid User Bills</div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-white stretched-link" href="<?= base_url('admin/paidBills') ?>">View Details</a>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-danger text-white mb-4">
                                <div class="card-body">Reports</div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-white stretched-link" href="<?= base_url('admin/reports') ?>">View Details</a>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <i class="fas fa-chart-area me-1"></i>
                                    Area Chart Example
                                </div>
                                <div class="card-body"><canvas id="myAreaChart" width="100%" height="40"></canvas></div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <i class="fas fa-chart-bar me-1"></i>
                                    Bar Chart Example
                                </div>
                                <div class="card-body"><canvas id="myBarChart" width="100%" height="40"></canvas></div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </main>
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; Your Website 2023</div>
                        <div>
                            <a href="#">Privacy Policy</a>
                            &middot;
                            <a href="#">Terms &amp; Conditions</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="<?= base_url('assets/admin/js/scripts.js') ?>"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script src="<?= base_url('assets/admin/demo/chart-area-demo.js') ?>"></script>
        <script src="<?= base_url('assets/admin/demo/chart-bar-demo.js') ?>"></script>
        <script src="<?= base_url('assets/admin/demo/chart-pie-demo.js') ?>"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="<?= base_url('assets/admin/js/datatables-simple-demo.js') ?>"></script>
        
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const setPasswordModalElement = document.getElementById('setPasswordModal');
                const setPasswordForm = document.getElementById('setPasswordForm');
                if (!setPasswordForm) return;

                const newPasswordInput = document.getElementById('new_password');
                const confirmPasswordInput = document.getElementById('confirm_password');
                const setPasswordMsg = document.getElementById('setPasswordMsg');
                const saveButton = setPasswordForm.querySelector('button[type="submit"]'); 

                // Modal display (use DOM element for constructor)
                <?php if (session()->getFlashdata('show_password_modal')): ?>
                    <script>
                        const modal = new bootstrap.Modal(document.getElementById('setPasswordModal'));
                        modal.show();
                    </script>
                <?php endif; ?>

                // Submission handler
                setPasswordForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const newPass = newPasswordInput.value.trim();
                    const confirmPass = confirmPasswordInput.value.trim();

                    if (newPass !== confirmPass) {
                        setPasswordMsg.innerHTML = '<div class="alert alert-danger">Passwords do not match!</div>';
                        return;
                    }

                    setPasswordMsg.innerHTML = '<div class="alert alert-info">Saving password...</div>';
                    saveButton.disabled = true;

                    const data = new URLSearchParams();
                    data.append('password', newPass);

                    fetch("<?= base_url('admin/setPassword') ?>", {
                        method: "POST",
                        body: data 
                    })
                    .then(res => {
                        if (!res.ok) throw new Error('Server error occurred.');
                        return res.json();
                    })
                    .then(data => {
                        if (data.status === 'success') {
                            setPasswordMsg.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                            setTimeout(() => window.location.reload(), 1500);
                        } else {
                            setPasswordMsg.innerHTML = `<div class="alert alert-danger">${data.message || 'Failed to set password.'}</div>`;
                        }
                    })
                    .catch(error => {
                        console.error("Fetch Error:", error);
                        setPasswordMsg.innerHTML = '<div class="alert alert-danger">An error occurred. Try again.</div>';
                    })
                    .finally(() => {
                        saveButton.disabled = false;
                    });
                });
            });
        </script>
    </body>
</html>