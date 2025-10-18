<header id="header" class="header fixed-top">
  <div class="branding d-flex align-items-center">
    <div class="container position-relative d-flex align-items-center justify-content-between">
      
      <a href="<?= site_url() ?>" class="logo d-flex align-items-center">
        <!-- Use an SVG logo instead of sitename -->
        <img src="<?= site_url('assets/images/logo/navbar.png') ?>" alt="AquaBill Logo" style="height:100px;">
        <!-- Optional: keep the sitename text next to logo -->
        <!-- <span class="ms-2">AquaBill</span> -->
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="<?= base_url('users') ?>" class="active">Home</a></li>
          <li><a href="#about">About</a></li>
          <li><a href="#services">Services</a></li>
          <li><a href="#contact">Contact</a></li>
          <li class="dropdown">
            <a href="#"><span>Account</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
            <ul>
              <li>
                <form action="<?= site_url('logout') ?>" method="post">
                  <button type="submit" class="dropdown-item">
                    <i class="bi bi-box-arrow-right"></i> Logout
                  </button>
                </form>
              </li>
              <li><a href="#">Dropdown 2</a></li>
            </ul>
          </li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

    </div>
  </div>
</header>
