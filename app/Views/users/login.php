<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <!-- Google Fonts (Poppins) -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS (from CDN) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">

    <!-- Custom CSS for Login Page (stored in /public/assets/login/loginstyle.css) -->
    <link href="<?= base_url('assets/login/loginstyle.css?v=' . time()) ?>" rel="stylesheet">

</head>
<body>

<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="row border rounded-5 p-3 bg-white shadow box-area">
        
        <!-- Left Image Section -->
        <div class="col-md-6 rounded-4 left-box d-flex justify-content-center align-items-center p-0 d-none d-md-flex">
            <img src="<?= base_url('assets/login/img/Login.jpg') ?>" class="img-fluid img-centered" alt="Login Image">
        </div>        

        <!-- Right Login Form Section -->
        <div class="col-md-6 right-box">
           <form action="<?= route_to('attemptLogin') ?>" method="post">
                <div class="row align-items-center">
                    
                    <!-- Header -->
                    <div class="header-text mb-4">
                        <h2>Hello, Again</h2>
                        <p>We are happy to have you back.</p>
                    </div>

                    <!-- Username Input -->
                    <div class="input-group mb-3">
                        <input type="text" name="username" class="form-control form-control-lg bg-light fs-6" placeholder="Username" required>
                    </div>

                    <!-- Password Input -->
                    <div class="input-group mb-1">
                        <input type="password" name="password" class="form-control form-control-lg bg-light fs-6" placeholder="Password" required>
                    </div>

                    <!-- Login Button -->
                    <div class="input-group mb-3 mt-4">
                        <button type="submit" class="btn btn-lg btn-primary w-100 fs-6">Login</button>
                    </div>

                    <!-- Error Message -->
                    <?php if(session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger w-100">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>

                    <!-- Signup Link -->
                    <div class="row">
                        <small>Don't have an account? 
                            <a href="<?= site_url('signup') ?>">Sign Up</a>
                        </small>
                    </div>
                </div>
            </form>
        </div> 
    </div>
</div>

</body>
</html>
