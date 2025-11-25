<header id="header" class="header fixed-top">
  <nav class="navbar navbar-expand-lg fixed-top">
    <div class="container-fluid">
      <a class="navbar-brand me-auto" href="#">MyAquabill</a>

      <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
        <div class="offcanvas-header">
          <h5 class="offcanvas-title" id="offcanvasNavbarLabel">Logo</h5>
          <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
          <ul class="navbar-nav justify-content-center flex-grow-1 pe-3">
            <li class="nav-item"><a class="nav-link mx-lg-2 active" href="<?= base_url('users') ?>">Home</a></li>
            <li class="nav-item"><a class="nav-link mx-lg-2" href="#">About</a></li>
            <li class="nav-item"><a class="nav-link mx-lg-2" href="<?= base_url('users/chat') ?>">Contact</a></li>
          </ul>
        </div>
      </div>

      <!-- Logout button (POST form) -->
      <form action="<?= base_url('logout') ?>" method="post" style="margin: 0;">
        <button type="submit" class="logout-button btn btn-outline-danger">Logout</button>
      </form>

      <button class="navbar-toggler pe-0" type="button" data-bs-toggle="offcanvas"
        data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
    </div>
  </nav>
</header>
