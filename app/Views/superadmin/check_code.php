<!DOCTYPE html>
<html lang ='en'>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Check Code</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">  
</head>
<body class="d-flex align-items-center justify-content-center vh-100 bg-light">
    <div class="card shadow p-4" style ="width:400px;">
        <h4 class="text-center mb-3">Enter Super Admin Code</h4>
        <?php if(session()->getFlashdata('error')):?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <form action="<?= base_url('superadmin/check-code') ?>" method="post">
            <div class="mb-3">
                <input type="text" name="admin_code" class="form-control" placeholder="Enter Admin Code" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">verify</button>
        </form>
    </div>
</body> 