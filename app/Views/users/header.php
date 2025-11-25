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
              <li class="nav-item"><a class="nav-link mx-lg-2" href="#">Notification</a></li>
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
              <span id="notifBadge" class="notif-badge">0</span>
            </button>
            <div id="notifDropdown" class="notification-dropdown shadow-sm" role="menu" aria-hidden="true">
              <div class="notif-header">Notifications</div>
              <ul id="notifList" class="list-unstyled notif-list mb-0">
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

  <!-- Notification toggle & loader script -->
  <script>
  document.addEventListener('DOMContentLoaded', function () {
    const bell = document.getElementById('notifBell');
    const dropdown = document.getElementById('notifDropdown');
    const badge = document.getElementById('notifBadge');
    const list = document.getElementById('notifList');
    if (!bell || !dropdown || !badge || !list) return;

    const base = '<?= base_url() ?>';

    function renderList(items) {
      list.innerHTML = '';
      if (!items || items.length === 0) {
        const li = document.createElement('li');
        li.className = 'notif-item';
        li.textContent = 'No new notifications';
        list.appendChild(li);
        badge.textContent = '0';
        badge.style.display = 'none';
        return;
      }
      badge.style.display = '';
      badge.textContent = items.length;
      items.forEach(function (it) {
        const li = document.createElement('li');
        li.className = 'notif-item';
        const href = it.url ? it.url : '#';
        li.innerHTML = '<a href="'+href+'" class="notif-link" data-id="'+(it.id||'')+'">'
          + '<div class="notif-title">'+(it.title||'')+'</div>'
          + '<div class="notif-body">'+(it.body||'')+'</div>'
          + '</a>';
        list.appendChild(li);
      });
    }

    async function fetchNotifications() {
      try {
        const res = await fetch(base + '/notifications/json', { credentials: 'same-origin' });
        if (!res.ok) throw new Error('Network response not ok');
        const data = await res.json();
        renderList(data.notifications || []);
      } catch (err) {
        console.error('Failed to load notifications', err);
      }
    }

    async function markAllRead() {
      try {
        await fetch(base + '/notifications/mark_read', { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({}) });
        badge.textContent = '0';
        badge.style.display = 'none';
      } catch (err) {
        console.error('Failed to mark notifications read', err);
      }
    }

    bell.addEventListener('click', function (e) {
      e.stopPropagation();
      const willShow = !dropdown.classList.contains('show');
      dropdown.classList.toggle('show', willShow);
      bell.setAttribute('aria-expanded', willShow);
      dropdown.setAttribute('aria-hidden', !willShow);
      if (willShow) {
        fetchNotifications();
        // Optionally call markAllRead();
      }
    });

    dropdown.addEventListener('click', function (e) {
      e.stopPropagation();
    });

    document.addEventListener('click', function () {
      if (dropdown.classList.contains('show')) {
        dropdown.classList.remove('show');
        bell.setAttribute('aria-expanded', 'false');
        dropdown.setAttribute('aria-hidden', 'true');
      }
    });

    // initial load + polling
    fetchNotifications();
    setInterval(fetchNotifications, 30000);
  });
  </script>
