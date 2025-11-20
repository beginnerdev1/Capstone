<header id="header" class="header fixed-top">
  <nav class="navbar navbar-expand-lg fixed-top">
    <div class="container-fluid">
      <a class="navbar-brand me-auto" href="#">Logo</a>

      <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
        <div class="offcanvas-header">
          <h5 class="offcanvas-title" id="offcanvasNavbarLabel">Logo</h5>
          <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
          <ul class="navbar-nav justify-content-center flex-grow-1 pe-3">
            <li class="nav-item"><a class="nav-link mx-lg-2 active" href="<?= base_url('users') ?>">Home</a></li>
            <li class="nav-item"><a class="nav-link mx-lg-2" href="#">About</a></li>
            <li class="nav-item"><a class="nav-link mx-lg-2" href="#">Services</a></li>
            <li class="nav-item"><a class="nav-link mx-lg-2" href="<?= base_url('users/chat') ?>">Contact</a></li>
          </ul>
        </div>
      </div>

      <!-- Logout button (POST form) -->
      <form action="<?= base_url('logout') ?>" method="post" style="margin: 0;">
        <button type="submit" class="logout-button btn btn-outline-danger">Logout</button>
      </form>

      <!-- Notification Bell -->
      <div class="nav-icons d-flex align-items-center">
        <div class="notification-wrapper position-relative">
          <button id="notifBell" class="btn btn-link notification-btn" type="button" aria-expanded="false" aria-label="Notifications">
            <i class="bi bi-bell" aria-hidden="true"></i>
            <span class="notif-badge">0</span>
          </button>
          <div id="notifDropdown" class="notification-dropdown shadow-sm" role="menu" aria-hidden="true">
            <div class="notif-header">Notifications</div>
            <ul class="list-unstyled notif-list mb-0">
              <li class="notif-item">No new notifications</li>
            </ul>
            <div class="notif-footer text-center"><a href="#">View all</a></div>
          </div>
        </div>
      </div>

      <button class="navbar-toggler pe-0" type="button" data-bs-toggle="offcanvas"
        data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
    </div>
  </nav>
</header>

<!-- Notification toggle script -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  const bell = document.getElementById('notifBell');
  const dropdown = document.getElementById('notifDropdown');
  if (!bell || !dropdown) return;

  // Toggle dropdown when bell clicked
  bell.addEventListener('click', function (e) {
    e.stopPropagation();
    dropdown.classList.toggle('show');
    const isShown = dropdown.classList.contains('show');
    bell.setAttribute('aria-expanded', isShown);
    dropdown.setAttribute('aria-hidden', !isShown);
  });

  // Prevent clicks inside dropdown from closing it
  dropdown.addEventListener('click', function (e) {
    e.stopPropagation();
  });

  // Close dropdown when clicking outside
  document.addEventListener('click', function () {
    if (dropdown.classList.contains('show')) {
      dropdown.classList.remove('show');
      bell.setAttribute('aria-expanded', 'false');
      dropdown.setAttribute('aria-hidden', 'true');
    }
  });
});
</script>
