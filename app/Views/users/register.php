<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>

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
        
        <!-- Right Register Form Section -->
        <div class="col-md-6 right-box">
            <form action="<?= site_url('register') ?>" method="post">
                <div class="row align-items-center">
                    
                    <!-- Header -->
                    <div class="header-text mb-4">
                        <h2>Create Account</h2>
                        <p>Fill in the details to register.</p>
                    </div>

                    <!-- Name -->
                    <div class="input-group mb-3">
                        <input type="text" name="username" class="form-control form-control-lg bg-light fs-6" placeholder="Full Name" required>
                    </div>
                    <!-- Street -->
                    <div class="input-group mb-3">
                        <input type="text" name="street" class="form-control form-control-lg bg-light fs-6" placeholder="Street" required>
                    </div>
                    <!-- Address -->
                    <div class="input-group mb-3">
                        <input type="text" name="address" class="form-control form-control-lg bg-light fs-6" placeholder="Address" required>
                    </div>
                    <!-- Phone Number -->
                    <div class="input-group mb-3">
                        <input type="text" name="phone" class="form-control form-control-lg bg-light fs-6" placeholder="Phone Number" required>
                    </div>
                    <!-- Email -->
                    <div class="input-group mb-3">
                        <input type="email" name="email" class="form-control form-control-lg bg-light fs-6" placeholder="Email" required>
                    </div>
                    <!-- Password -->
                    <div class="input-group mb-3">
                        <input type="password" name="password" class="form-control form-control-lg bg-light fs-6" placeholder="Password" required>
                    </div>
                    <!-- Submit -->
                    <div class="input-group mb-3 mt-3">
                        <button type="submit" class="btn btn-lg btn-success w-100 fs-6">Register</button>
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

                    <!-- Login Link -->
                    <div class="row">
                        <small>Already have an account? 
                            <a href="<?= base_url('login') ?>">Login</a>
                        </small>
                    </div>
                </div>
            </form>
        </div> 
    </div>
</div>

</body>
</html>
