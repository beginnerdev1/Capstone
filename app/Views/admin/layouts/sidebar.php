<nav id="sidebarMenu" class="offcanvas offcanvas-start bg-dark text-white" tabindex="-1" aria-labelledby="sidebarMenuLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="sidebarMenuLabel">Admin Panel</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>

  <div class="offcanvas-body d-flex flex-column">
    <ul class="nav flex-column">
      <li class="nav-item text-uppercase small text-secondary mt-2">Core</li>
      <li class="nav-item">
        <a class="nav-link text-white" href="<?= base_url('admin/') ?>">
          <i class="bi bi-speedometer2 me-2"></i>Dashboard
        </a>
      </li>

      <li class="nav-item text-uppercase small text-secondary mt-3">Management</li>

      <!-- Collapsible Registered Users -->
      <li class="nav-item">
        <a class="nav-link text-white d-flex justify-content-between align-items-center" 
           data-bs-toggle="collapse" href="#registeredUsersMenu" role="button" 
           aria-expanded="false" aria-controls="registeredUsersMenu">
          <span><i class="bi bi-people me-2"></i>Users</span>
          <i class="bi bi-chevron-down small"></i>
        </a>
        <div class="collapse ms-4" id="registeredUsersMenu">
          <ul class="nav flex-column">
            <li><a class="nav-link text-white-50" href="<?= base_url('admin/registeredUsers') ?>">All Users</a></li>
            <li><a class="nav-link text-white-50" href="<?= base_url('admin/pendingAccounts') ?>">Account Approval</a></li>
          </ul>
        </div>
      </li>

      <!-- Manage Accounts -->
      <li class="nav-item">
        <a class="nav-link text-white" href="<?= base_url('admin/manageAccounts') ?>">
          <i class="bi bi-person-lines-fill me-2"></i>Manage Accounts
        </a>
      </li>

      <li class="nav-item text-uppercase small text-secondary mt-3">Addons</li>
      <li class="nav-item"><a class="nav-link text-white" href="<?= base_url('admin/paidBills') ?>"><i class="bi bi-check-circle me-2"></i>Paid User Bills</a></li>
      <li class="nav-item"><a class="nav-link text-white" href="<?= base_url('admin/inactiveList') ?>"><i class="bi bi-folder2-open me-2"></i>Inactive Users</a></li>
      <li class="nav-item"><a class="nav-link text-white" href="<?= base_url('admin/charts') ?>"><i class="bi bi-bar-chart-line me-2"></i>Charts</a></li>
      <li class="nav-item"><a class="nav-link text-white" href="<?= base_url('admin/tables') ?>"><i class="bi bi-table me-2"></i>Tables</a></li>
    </ul>
  </div>
</nav>
