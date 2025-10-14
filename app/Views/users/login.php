<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Aquabill Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: radial-gradient(circle at bottom left, #0072ff, #004aad);
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      overflow: hidden;
      position: relative;
    }

    /* circular bg */
    body::before, body::after {
      content: "";
      position: absolute;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.05);
      z-index: 0;
    }
    body::before {
      width: 600px; height: 600px;
      bottom: -200px; left: -200px;
    }
    body::after {
      width: 500px; height: 500px;
      top: -100px; right: -150px;
    }

    .container-fluid {
      z-index: 1;
      max-width: 1200px;
      padding: 0 2rem;
    }

    .left-panel {
      color: white;
      padding: 4rem 3rem;
    }

    .left-panel h1 {
      font-size: 3rem;
      font-weight: 700;
    }

    .left-panel h2 {
      font-size: 1.2rem;
      font-weight: 500;
      margin-bottom: 1rem;
    }

    .left-panel p {
      font-size: 0.95rem;
      max-width: 420px;
      line-height: 1.6;
    }

    .right-panel {
      display: flex;
      justify-content: center;
      align-items: center;
      background: transparent;
      padding: 3rem 2rem;
      position: relative;
    }

    /* slimmer and clean white box */
    .login-card {
      position: relative;
      width: 100%;
      max-width: 420px; /* slimmer */
      text-align: center;
      background: #fff;
      border-radius: 25px;
      padding: 3rem 2rem 2rem;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
    }

    /* logo bigger */
    .logo-img {
      position: absolute;
      top: -80px;
      left: 50%;
      transform: translateX(-50%);
      width: 160px; /* bigger */
      height: auto;
      background: #fff;
      border-radius: 50%;
      padding: 12px;
      box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
    }

    .form-control {
      border-radius: 12px;
      padding: 0.9rem 1rem;
      font-size: 0.95rem;
    }

    .position-relative button.show-btn {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      border: none;
      background: transparent;
      color: #003366;
      font-weight: 600;
      cursor: pointer;
      font-size: 0.85rem;
    }

    .btn-login {
      background-color: #004aad;
      color: white;
      border-radius: 12px;
      padding: 0.75rem;
      font-weight: 600;
      font-size: 1.1rem;
      border: none;
      transition: all 0.3s ease;
    }

    .btn-login:hover {
      background-color: #003b8f;
      transform: translateY(-2px);
    }

    .form-check-label, .forgot-link, .signup {
      font-size: 0.9rem;
    }

    .forgot-link {
      text-decoration: none;
      color: #003366;
    }

    .forgot-link:hover {
      text-decoration: underline;
    }

    .signup a {
      color: #004aad;
      font-weight: 600;
      text-decoration: none;
    }

    .signup a:hover {
      text-decoration: underline;
    }

    @media (max-width: 991px) {
    .left-panel { 
        display: none; 
    }

    .login-card { 
        padding-top: 4rem; 
        max-width: 450px; /* wider */
        padding: 2.5rem 2rem; 
    }

    .logo-img { 
        width: 140px; 
        top: -65px; 
    }

    .form-control, 
    .btn-login, 
    .form-check-label, 
    .forgot-link, 
    .signup { 
        font-size: 0.8rem; /* slightly smaller text */
    }

    body::before, 
    body::after { 
        display: none; 
    }
    }

  </style>
</head>
<body>
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-lg-6 left-panel">
        <h1>WELCOME</h1>
        <h2>To AQUABILL</h2>
        <p>AquaBill is your modern water billing companion, built to make tracking and paying your bills easier, faster, and more convenient than ever before.</p>
      </div>

      <div class="col-lg-6 right-panel">
        <div class="login-card">
         <img src="<?= base_url('assets/images/logo/Aquabill.png') ?>" alt="Aquabill Logo" class="logo-img">

            <form action="<?= base_url('login') ?>" method="post" class="pt-5">
                <?= csrf_field() ?> <!-- Protects against CSRF attacks -->

                <div class="mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                </div>

                <div class="mb-3 position-relative">
                    <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
                    <button type="button" class="show-btn" onclick="togglePassword()">SHOW</button>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check">
                        <!-- Remember me checkbox (optional) -->
                        <!-- <input class="form-check-input" type="checkbox" id="remember"> -->
                    </div>
                    <a href="<?= base_url('forgot-password') ?>" class="forgot-link">Forgot Password?</a>
                </div>

                <button type="submit" class="btn btn-login w-100">Log In</button>

                <div class="signup mt-3">
                    Don't have an account? <a href="<?= base_url('register') ?>">Sign Up</a>
                </div>
            </form>


        </div>
      </div>
    </div>
  </div>

  <script>
    function togglePassword() {
      const input = document.getElementById("password");
      input.type = input.type === "password" ? "text" : "password";
    }
  </script>
</body>
</html>
