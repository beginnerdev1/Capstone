
<!-- login page structure-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
      <link href="<?= base_url('public/assets/login/loginstyle.css') ?>" rel="stylesheet">
    <title>Bootstrap Login | Ludiflex</title>
</head>
<body>

<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="row border rounded-5 p-3 bg-white shadow box-area">
        <div class="col-md-6 rounded-4 left-box d-flex justify-content-center align-items-center p-0 d-none d-md-flex">
            <img src="image/1.jpg" class="img-fluid img-centered">
        </div>        
        <div class="col-md-6 right-box">
            <form action="" method="POST">
                <div class="row align-items-center">
                    <div class="header-text mb-4">
                        <h2>Hello, Again</h2>
                        <p>We are happy to have you back.</p>
                    </div>
                    <div class="input-group mb-3">
                        <input type="text" name="username" class="form-control form-control-lg bg-light fs-6" placeholder="username" required>
                    </div>
                    <div class="input-group mb-1">
                        <input type="password" name="password" class="form-control form-control-lg bg-light fs-6" placeholder="Password" required>
                    </div>
                    <div class="input-group mb-3 mt-4">
                        <button type="submit" class="btn btn-lg btn-primary w-100 fs-6">Login</button>
                    </div>

                    <?php if (!empty($error)): ?>
                        <div class="text-danger mb-3 text-center">
                            <?= $error ?>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <small>Don't have an account? <a href="signup.php">Sign Up</a></small>
                    </div>
                </div>
            </form>
        </div> 
    </div>
</div>

</body>
</html>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="loginstyle.css">
    <title>Bootstrap Login | Ludiflex</title>
</head>
<body>

<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="row border rounded-5 p-3 bg-white shadow box-area">
        <div class="col-md-6 rounded-4 left-box d-flex justify-content-center align-items-center p-0 d-none d-md-flex">
            <img src="image/1.jpg" class="img-fluid img-centered">
        </div>        
        <div class="col-md-6 right-box">
            <form action="" method="POST">
                <div class="row align-items-center">
                    <div class="header-text mb-4">
                        <h2>Hello, Again</h2>
                        <p>We are happy to have you back.</p>
                    </div>
                    <div class="input-group mb-3">
                        <input type="text" name="username" class="form-control form-control-lg bg-light fs-6" placeholder="username" required>
                    </div>
                    <div class="input-group mb-1">
                        <input type="password" name="password" class="form-control form-control-lg bg-light fs-6" placeholder="Password" required>
                    </div>
                    <div class="input-group mb-3 mt-4">
                        <button type="submit" class="btn btn-lg btn-primary w-100 fs-6">Login</button>
                    </div>

                    <?php if (!empty($error)): ?>
                        <div class="text-danger mb-3 text-center">
                            <?= $error ?>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <small>Don't have an account? <a href="signup.php">Sign Up</a></small>
                    </div>
                </div>
            </form>
        </div> 
    </div>
</div>

</body>
</html>
