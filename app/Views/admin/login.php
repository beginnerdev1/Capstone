<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <title>Admin Login</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    html, body {
      height: 100%; /* Ensures full height */
    }

    body {
      font-family: 'Poppins', sans-serif;
      background: radial-gradient(circle at bottom left, #0072ff, #004aad);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
      position: relative;
      /* We allow vertical scrolling, but hide horizontal overflow */
      overflow-x: hidden; 
      overflow-y: auto; 
    }

    /* Animated background elements */
    /* KEY FIX: Changed from absolute to fixed so they don't add scroll space */
    body::before {
      content: '';
      position: fixed; 
      width: 600px;
      height: 600px;
      background: rgba(255, 255, 255, 0.05);
      border-radius: 50%;
      bottom: -200px;
      left: -200px;
      animation: float 20s infinite ease-in-out;
      pointer-events: none; /* Click-through */
      z-index: 0;
    }

    body::after {
      content: '';
      position: fixed;
      width: 500px;
      height: 500px;
      background: rgba(255, 255, 255, 0.05);
      border-radius: 50%;
      top: -100px;
      right: -150px;
      animation: float 15s infinite ease-in-out reverse;
      pointer-events: none; /* Click-through */
      z-index: 0;
    }

    @keyframes float {
      0%, 100% { transform: translateY(0) translateX(0); }
      50% { transform: translateY(-30px) translateX(20px); }
    }

    .login-container {
      position: relative;
      z-index: 1;
      width: 100%;
      max-width: 440px;
      margin: auto; /* Helps centering when scrolling is active */
    }

    .login-card {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border-radius: 24px;
      padding: 48px 40px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
      border: 1px solid rgba(255, 255, 255, 0.2);
      animation: slideUp 0.6s ease-out;
    }

    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .logo-section {
      text-align: center;
      margin-bottom: 40px;
    }

    .logo-icon {
      width: 70px;
      height: 70px;
      background: linear-gradient(135deg, #0072ff 0%, #004aad 100%);
      border-radius: 18px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 20px;
      box-shadow: 0 8px 24px rgba(0, 114, 255, 0.4);
      animation: pulse 2s infinite;
    }

    @keyframes pulse {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.05); }
    }

    .logo-icon i {
      color: white;
      font-size: 32px;
    }

    .login-card h3 {
      font-size: 28px;
      font-weight: 700;
      color: #1a202c;
      margin-bottom: 8px;
    }

    .login-card .subtitle {
      color: #718096;
      font-size: 15px;
      font-weight: 400;
    }

    .alert {
      border-radius: 12px;
      border: none;
      padding: 14px 18px;
      margin-bottom: 24px;
      font-size: 14px;
      animation: fadeIn 0.3s ease-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .alert-danger {
      background: #fee;
      color: #c53030;
    }

    .alert-success {
      background: #e6ffed;
      color: #22543d;
    }

    .form-group {
      margin-bottom: 24px;
    }

    .form-label {
      display: block;
      font-size: 14px;
      font-weight: 600;
      color: #2d3748;
      margin-bottom: 10px;
      display: flex;
      align-items: center;
    }

    .form-label i {
      margin-right: 8px;
      color: #0072ff;
      width: 16px;
    }

    .input-wrapper {
      position: relative;
    }

    .form-control {
      width: 100%;
      padding: 14px 16px;
      font-size: 15px;
      border: 2px solid #e2e8f0;
      border-radius: 12px;
      transition: all 0.3s ease;
      background: white;
      color: #2d3748;
    }

    .form-control:focus {
      outline: none;
      border-color: #0072ff;
      box-shadow: 0 0 0 4px rgba(0, 114, 255, 0.1);
      background: white;
    }

    .form-control::placeholder {
      color: #a0aec0;
    }

    #password {
      padding-right: 50px;
    }

    #togglePassword {
      position: absolute;
      right: 14px;
      top: 50%;
      transform: translateY(-50%);
      background: transparent;
      border: none;
      color: #a0aec0;
      cursor: pointer;
      padding: 8px;
      transition: color 0.2s ease;
      font-size: 18px;
    }

    #togglePassword:hover {
      color: #0072ff;
    }

    #togglePassword:focus {
      outline: 2px solid #0072ff;
      outline-offset: 2px;
      border-radius: 6px;
    }

    .btn-login {
      width: 100%;
      padding: 14px;
      font-size: 16px;
      font-weight: 600;
      background: linear-gradient(135deg, #0072ff 0%, #004aad 100%);
      border: none;
      border-radius: 12px;
      color: white;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(0, 114, 255, 0.4);
      margin-top: 8px;
    }

    .btn-login:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(0, 114, 255, 0.5);
      background: linear-gradient(135deg, #005ce6 0%, #003b8f 100%);
    }

    .btn-login:active {
      transform: translateY(0);
    }

    .forgot-password {
      text-align: center;
      margin-top: 24px;
    }

    .forgot-password a {
      color: #0072ff;
      text-decoration: none;
      font-size: 14px;
      font-weight: 500;
      transition: color 0.2s ease;
      display: inline-flex;
      align-items: center;
    }

    .forgot-password a:hover {
      color: #004aad;
      text-decoration: underline;
    }

    .forgot-password a i {
      margin-right: 6px;
      font-size: 12px;
    }

    .secure-badge {
      text-align: center;
      margin-top: 28px;
      padding-top: 20px;
      border-top: 1px solid #e2e8f0;
    }

    .secure-badge span {
      display: inline-flex;
      align-items: center;
      color: #718096;
      font-size: 13px;
    }

    .secure-badge i {
      margin-right: 6px;
      color: #48bb78;
    }

    /* Responsive */
    @media (max-width: 768px) {
      body::before {
        width: 400px;
        height: 400px;
        top: -200px;
        right: -150px;
      }

      body::after {
        width: 300px;
        height: 300px;
        bottom: -150px;
        left: -100px;
      }

      .login-container {
        padding: 0 10px; /* Safety padding for sides */
      }

      .login-card {
        padding: 40px 32px;
      }

      .logo-icon {
        width: 65px;
        height: 65px;
      }

      .logo-icon i {
        font-size: 28px;
      }

      .login-card h3 {
        font-size: 26px;
      }
    }

    @media (max-width: 480px) {
      body {
        padding: 16px;
        /* Allow body to grow with content */
        height: auto;
        min-height: 100vh;
      }

      body::before {
        width: 300px;
        height: 300px;
        top: -150px;
        right: -100px;
      }

      body::after {
        width: 250px;
        height: 250px;
        bottom: -125px;
        left: -75px;
      }

      .login-card {
        padding: 32px 24px;
        border-radius: 20px;
      }

      .logo-icon {
        width: 60px;
        height: 60px;
        border-radius: 14px;
        margin-bottom: 16px;
      }

      .logo-icon i {
        font-size: 26px;
      }

      .login-card h3 {
        font-size: 24px;
        margin-bottom: 6px;
      }

      .login-card .subtitle {
        font-size: 13px;
      }

      .form-group {
        margin-bottom: 20px;
      }

      .form-label {
        font-size: 13px;
        margin-bottom: 8px;
      }

      .form-control {
        padding: 12px 14px;
        font-size: 14px;
        border-radius: 10px;
      }

      .btn-login {
        padding: 13px;
        font-size: 15px;
        border-radius: 10px;
      }

      .secure-badge {
        margin-top: 24px;
        padding-top: 18px;
      }

      .secure-badge span {
        font-size: 12px;
      }
    }

    @media (max-width: 360px) {
      .login-card {
        padding: 28px 20px;
      }

      .login-card h3 {
        font-size: 22px;
      }

      .btn-login {
        font-size: 14px;
      }
    }

    /* Landscape mode adjustments */
    @media (max-height: 600px) and (orientation: landscape) {
      body {
        height: auto;
        min-height: 100vh;
        align-items: flex-start; /* Allows scrolling from top */
        padding: 20px 10px;
      }
      
      .login-container {
         margin-top: 10px;
         margin-bottom: 20px;
      }

      .login-card {
        padding: 24px 32px;
      }

      .logo-section {
        margin-bottom: 20px;
      }

      .logo-icon {
        width: 50px;
        height: 50px;
        margin-bottom: 10px;
      }

      .logo-icon i {
        font-size: 22px;
      }
      
      .forgot-password {
        margin-top: 16px;
      }
    }
  </style>
</head>

<body>
  <div class="login-container">
    <div class="login-card">
      <div class="logo-section">
        <div class="logo-icon">
          <i class="fas fa-user-shield"></i>
        </div>
        <h3>Admin Login</h3>
        <p class="subtitle">Access the administrator dashboard</p>
      </div>

      <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger text-center"><?= session()->getFlashdata('error') ?></div>
      <?php endif; ?>
      <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success text-center"><?= session()->getFlashdata('success') ?></div>
      <?php endif; ?>

      <form action="<?= base_url('admin/login') ?>" method="post">
        <div class="form-group">
          <label for="email" class="form-label">
            <i class="fa-solid fa-envelope"></i>
            Email Address
          </label>
          <input 
            type="email" 
            class="form-control" 
            id="email" 
            name="email" 
            placeholder="name@example.com" 
            required
          >
        </div>

        <div class="form-group">
          <label for="password" class="form-label">
            <i class="fa-solid fa-lock"></i>
            Password
          </label>
          <div class="input-wrapper">
            <input 
              type="password" 
              class="form-control" 
              id="password" 
              name="password" 
              placeholder="Enter your password"
              required
            >
            <button 
              type="button" 
              id="togglePassword" 
              aria-pressed="false" 
              aria-label="Show password"
            >
              <i class="fa-solid fa-eye"></i>
            </button>
          </div>
        </div>

        <button type="submit" class="btn-login">
          Login to Dashboard
        </button>

        <div class="forgot-password">
          <a href="<?= base_url('admin/forgot-password') ?>">
            <i class="fa-solid fa-key"></i>
            Forgot password?
          </a>
        </div>
      </form>

      <div class="secure-badge">
        <span>
          <i class="fa-solid fa-shield-halved"></i>
          Secure admin access only
        </span>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Toggle password visibility
    (function(){
      const toggleBtn = document.getElementById('togglePassword');
      const password = document.getElementById('password');
      if (!toggleBtn || !password) return;
      const icon = toggleBtn.querySelector('i');
      function doToggle(){
        const isPwd = password.type === 'password';
        password.type = isPwd ? 'text' : 'password';
        toggleBtn.setAttribute('aria-pressed', String(isPwd));
        if (icon) {
          icon.classList.remove(isPwd ? 'fa-eye' : 'fa-eye-slash');
          icon.classList.add(isPwd ? 'fa-eye-slash' : 'fa-eye');
        }
      }
      toggleBtn.addEventListener('click', function(e){ e.preventDefault(); doToggle(); });
      toggleBtn.addEventListener('keydown', function(e){ if(e.key === 'Enter' || e.key === ' '){ e.preventDefault(); doToggle(); }});
    })();
  </script>
</body>
</html>