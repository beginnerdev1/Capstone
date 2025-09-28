<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Reset Password</title>
        <link="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class ="bg-light d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow p-4" style="max-width: 400px; width: 100%;">
            <h3 class="text-center mb-4">Reset Password</h3>
            
            <?php if(session()->getFlashdata('error')):?>
                <div class="alert alert-danger">
                 <?=session()->getFlashdata('error')?>
                </div>
             <?php endif;?>

            <form action="<?= base_url("/reset-password")?>" method="post">
            <input type="hidden" name="email" value="<?= esc($email) ?>">
            <input type="hidden" name="token" value="<?= esc($token) ?>">

            <div class="mb-3">
                <label for="password" class="form-label">New Password</label>
                <input type="password" name="password" class="form-control" required>
             </div>

            <div class="mb-3">
                 <label for="confirm_password" class="form-label">Confirm New Password</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Reset Password</button>
            </form>
        </div>
   </body>
</html>