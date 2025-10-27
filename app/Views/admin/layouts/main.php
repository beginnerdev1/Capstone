<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= $this->include('admin/layouts/header') ?>
</head>
<body class="sb-nav-fixed">
    <?= $this->include('admin/layouts/navbar') ?>
    <div id="layoutSidenav">
        <?= $this->include('admin/layouts/sidebar') ?>
        <div id="layoutSidenav_content">
            <main class="container-fluid px-5 mt-4">
                <div class="row">
                    <div class="col-12">
                        <?= $this->renderSection('content') ?>
                    </div>
                </div>
            </main>
            <?= $this->include('admin/layouts/footer') ?>
        </div>
    </div>

    <!-- ✅ Force Password Change Modal (moved outside layoutSidenav) -->
    <div class="modal fade" id="forceChangePasswordModal" tabindex="-1" aria-labelledby="forceChangePasswordLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-danger">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="forceChangePasswordLabel">Security Notice</h5>
                </div>
                <div class="modal-body">
                    <p class="text-danger fw-bold">
                        You are still using the default password. Please change your password before continuing.
                    </p>
                    <form id="forceChangePasswordForm">
                        <div class="mb-3">
                            <label for="newPassword" class="form-label">New Password</label>
                            <input type="password" name="password" id="newPassword" class="form-control" required minlength="6">
                        </div>
                        <button type="submit" class="btn btn-danger w-100">Change Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ✅ Script to show modal (AFTER footer scripts load) -->
    <?php if (session()->get('force_password_change')): ?>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal = new bootstrap.Modal(document.getElementById('forceChangePasswordModal'), {
            backdrop: 'static',
            keyboard: false
        });
        modal.show();
    });
    </script>
    <?php else: ?>
    <script>console.log('No force_password_change flag found');</script>
    <?php endif; ?>

    <!-- ✅ Form submit handler -->
    <script>
    document.querySelector('#forceChangePasswordForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);

        const res = await fetch('<?= site_url('admin/setPassword') ?>', {
            method: 'POST',
            body: formData
        });

        const data = await res.json();
        alert(data.message);

        if (data.status === 'success') {
            location.reload();
        }
    });
    </script>

</body>
</html>
