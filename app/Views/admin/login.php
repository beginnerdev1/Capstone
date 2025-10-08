<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <title>Admin Login</title>

  <!-- Bootstrap + FontAwesome -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>

  <!-- Custom Admin CSS -->
  <link href="<?= base_url('assets/admin/css/admin-login.css?v=' . time()) ?>" rel="stylesheet" />
</head>

<body class="bg-gradient-primary">

  <div class="container d-flex align-items-center justify-content-center min-vh-100">
    <div class="card shadow-lg border-0 rounded-4 p-4" style="max-width: 430px; width: 100%;">
      <div class="text-center mb-4">
        <i class="fas fa-user-shield fa-3x text-primary mb-3"></i>
        <h3 class="fw-bold text-dark">Admin Login</h3>
        <p class="text-muted small">Access the administrator dashboard</p>
      </div>

      <!-- ✅ Flash message -->
      <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger text-center"><?= session()->getFlashdata('error') ?></div>
      <?php endif; ?>
      <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success text-center"><?= session()->getFlashdata('success') ?></div>
      <?php endif; ?>

      <form action="<?= base_url('admin/login') ?>" method="post">
        <div class="form-floating mb-3">
          <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
          <label for="email"><i class="fa-solid fa-envelope me-2"></i>Email address</label>
        </div>
        <div class="form-floating mb-3 position-relative">
          <input type="password" class="form-control" id="password" name="password" placeholder="Password" >
          <label for="password"><i class="fa-solid fa-lock me-2"></i>Password</label>
          <i class="fa-solid fa-eye position-absolute top-50 end-0 translate-middle-y me-3 text-muted" id="togglePassword" style="cursor:pointer;"></i>
        </div>

       <!--  <div class="d-flex justify-content-between align-items-center mb-4">
          <a href="<?= base_url('forgot-password') ?>" class="text-decoration-none small text-primary">Forgot password?</a>
        </div> -->

        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm">Login</button>
      </form>

      <!-- <div class="text-center mt-4 small text-muted">
        <i class="fa-solid fa-lock me-1"></i> Secure admin access only
      </div> -->
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // 👁️ Toggle password visibility
    const togglePassword = document.querySelector("#togglePassword");
    const password = document.querySelector("#password");
    togglePassword.addEventListener("click", function () {
      const type = password.getAttribute("type") === "password" ? "text" : "password";
      password.setAttribute("type", type);
      this.classList.toggle("fa-eye-slash");
    });
  </script>
</body>
</html>
