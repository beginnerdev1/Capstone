<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Forgot Password - Aquabill</title>
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
    body::before, body::after {
      content: "";
      position: absolute;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.05);
      z-index: 0;
    }
    body::before { width: 600px; height: 600px; bottom: -200px; left: -200px; }
    body::after  { width: 500px; height: 500px; top: -100px; right: -150px; }

    .container-fluid { z-index: 1; max-width: 1200px; padding: 0 2rem; }

    .left-panel {
      color: white;
      padding: 4rem 3rem;
    }
    .left-panel h1 { font-size: 3rem; font-weight: 700; }
    .left-panel h2 { font-size: 1.2rem; font-weight: 500; margin-bottom: 1rem; }
    .left-panel p  { font-size: 0.95rem; max-width: 420px; line-height: 1.6; }

    .right-panel { display: flex; justify-content: center; align-items: center; background: transparent; padding: 3rem 2rem; position: relative; }

    .card-box {
      position: relative;
      width: 100%;
      max-width: 420px;
      text-align: center;
      background: #fff;
      border-radius: 25px;
      padding: 3rem 2rem 2rem;
      box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    }

    .logo-img {
      position: absolute;
      top: -80px;
      left: 50%;
      transform: translateX(-50%);
      width: 140px;
      height: auto;
      background: #fff;
      border-radius: 50%;
      padding: 10px;
      box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
    }

    h3.title { margin-top: 40px; margin-bottom: 8px; font-weight:700; }
    p.subtitle { margin-bottom: 1.6rem; color: #6b7280; }

    .form-control { border-radius: 12px; padding: 0.9rem 1rem; font-size: 0.95rem; }
    .btn-primary-custom {
      background-color: #004aad;
      color: #fff;
      border-radius: 12px;
      padding: 0.75rem;
      font-weight: 600;
      font-size: 1rem;
      border: none;
      transition: all .2s ease;
    }
    .btn-primary-custom:hover { background-color: #003b8f; transform: translateY(-1px); }

    .link-muted { color: #6b7280; text-decoration: none; }
    .link-muted:hover { text-decoration: underline; }

    @media (max-width: 991px) {
      .left-panel { display: none; }
      .card-box { padding-top: 4rem; max-width: 450px; padding: 2.5rem 2rem; }
      .logo-img { width: 120px; top: -65px; }
    }
  </style>
</head>
<body>
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-lg-6 left-panel">
        <h1>WELCOME</h1>
        <h2>To AQUABILL</h2>
        <p>AquaBill is your modern water billing companion — easily request a password reset and get back to managing your account quickly.</p>
      </div>

      <div class="col-lg-6 right-panel">
        <div class="card-box">
          <img src="<?= base_url('assets/images/logo/Aquabill.png') ?>" alt="Aquabill Logo" class="logo-img">

          <h3 class="title">Forgot Password</h3>
          <p class="subtitle">Enter your email and we'll send a secure reset link. The link will expire in one hour.</p>

          <?php if(session()->getFlashdata('message')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('message') ?></div>
          <?php endif; ?>

          <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
          <?php endif; ?>

          <form action="<?= base_url('forgot-password') ?>" method="post" class="pt-2">
            <?= csrf_field() ?>
            <input type="hidden" name="actor" value="<?= esc($actor ?? 'user') ?>">
            <div class="mb-3">
              <input type="email" name="email" class="form-control" placeholder="Email address" required>
            </div>

            <div class="d-grid mb-2">
              <button type="submit" class="btn btn-primary-custom">Send Reset Link</button>
            </div>
          </form>

          <div class="mt-3">
            <?php $backLogin = (isset($actor) && $actor === 'admin') ? base_url('admin/login') : base_url('login'); ?>
            <?php $backLabel = (isset($actor) && $actor === 'admin') ? 'Back to admin login' : 'Back to Login'; ?>
            <a href="<?= $backLogin ?>" class="link-muted"><?= $backLabel ?></a>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
