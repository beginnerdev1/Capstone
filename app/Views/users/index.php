<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Index - Home</title>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- Bootstrap Core CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Your Local CSS -->
  <link href="<?= base_url('assets/Users/css/main.css?v=' . time()) ?>" rel="stylesheet">
  <link href="<?= base_url('assets/Users/css/payment.css?v=' . time()) ?>" rel="stylesheet">
  <link href="<?= base_url('assets/Users/css/navbar.css?v=' . time()) ?>" rel="stylesheet">
</head>

<body class="index-page">

  <?= $this->include('Users/header') ?>

  <?php if (isset($_GET['payment']) && $_GET['payment'] === 'success'): ?>
    <div class="alert alert-success text-center">
      Payment successful! 💧
    </div>
  <?php endif; ?>

  <main class="main">

    <!-- Hero Section -->
    <section id="hero" class="hero section accent-background">
      <div class="container position-relative" data-bs-spy="scroll">
        <div class="row gy-5 justify-content-between">
          <div class="col-lg-6 d-flex flex-column justify-content-center">
            <h2><span>Welcome to </span><span class="accent">Aqua Bill</span></h2>
            <p>Manage your water bills easily and securely with Aqua Bill.</p>
          </div>
        </div>
      </div>

      <div class="icon-boxes position-relative mt-5">
        <div class="container">
          <div class="row gy-4">

            <div class="col-xl-3 col-md-6">
              <div class="icon-box text-center">
                <div class="icon"><i class="bi bi-wallet2"></i></div>
                <h4 class="title">
                  <a href="javascript:void(0);" id="openPaymentBtn" class="stretched-link">Payments</a>
                </h4>
              </div>
            </div>

            <div class="col-xl-3 col-md-6">
              <div class="icon-box text-center">
                <div class="icon"><i class="bi bi-bell"></i></div>
                <h4 class="title"><a href="#" class="stretched-link">Notification</a></h4>
              </div>
            </div>

            <div class="col-xl-3 col-md-6">
              <div class="icon-box text-center">
                <div class="icon"><i class="bi bi-clock-history"></i></div>
                <h4 class="title"><a href="<?= base_url('users/history') ?>" class="stretched-link">History</a></h4>
              </div>
            </div>

            <div class="col-xl-3 col-md-6">
              <div class="icon-box text-center">
                <div class="icon"><i class="bi bi-person-circle"></i></div>
                <h4 class="title"><a href="<?= base_url('users/profile') ?>" class="stretched-link">Profile</a></h4>
              </div>
            </div>

          </div>
        </div>
      </div>
    </section>

       <!-- About Section -->
    <section id="about" class="about section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>About Us<br></h2>
        <p>Necessitatibus eius consequatur ex aliquid fuga eum quidem sint consectetur velit</p>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row gy-4">
          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
            <h3>Voluptatem dignissimos provident laboris nisi ut aliquip ex ea commodo</h3>
            <img src="<?= base_url('assets/img/about.jpg') ?>" class="img-fluid rounded-4 mb-4" alt="">
            <p>Ut fugiat ut sunt quia veniam. Voluptate perferendis perspiciatis quod nisi et. Placeat debitis quia recusandae odit et consequatur voluptatem. Dignissimos pariatur consectetur fugiat voluptas ea.</p>
            <p>Temporibus nihil enim deserunt sed ea. Provident sit expedita aut cupiditate nihil vitae quo officia vel. Blanditiis eligendi possimus et in cum. Quidem eos ut sint rem veniam qui. Ut ut repellendus nobis tempore doloribus debitis explicabo similique sit. Accusantium sed ut omnis beatae neque deleniti repellendus.</p>
          </div>
          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="250">
            <div class="content ps-0 ps-lg-5">
              <p class="fst-italic">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore
                magna aliqua.
              </p>
              <ul>
                <li><i class="bi bi-check-circle-fill"></i> <span>Ullamco laboris nisi ut aliquip ex ea commodo consequat.</span></li>
                <li><i class="bi bi-check-circle-fill"></i> <span>Duis aute irure dolor in reprehenderit in voluptate velit.</span></li>
                <li><i class="bi bi-check-circle-fill"></i> <span>Ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate trideta storacalaperda mastiro dolore eu fugiat nulla pariatur.</span></li>
              </ul>
              <p>
                Ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate
                velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident
              </p>

          
            </div>
          </div>
        </div>

      </div>

    </section><!-- /About Section -->
  </main>

  <!-- Payment Modal -->
  <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="paymentModalLabel">Payments</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="paymentModalBody">Loading...</div>
      </div>
    </div>
  </div>

  <?= $this->include('Users/footer') ?>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center">
    <i class="bi bi-arrow-up-short"></i>
  </a>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <!-- jQuery (for Payment Modal AJAX) -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

  <!-- Your Local JS -->
  <script src="<?= base_url('assets/Users/js/main.js') ?>"></script>

  <script>
    $(function () {
      $("#openPaymentBtn").on("click", function (e) {
        e.preventDefault();
        $("#paymentModalBody").load("<?= base_url('users/payments') ?>", function () {
          new bootstrap.Modal(document.getElementById("paymentModal")).show();
        });
      });
    });
  </script>

</body>

</html>
