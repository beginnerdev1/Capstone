<div class="d-flex">
    <div class="bg-dark text-white p-3 vh-100%" style="width: 220px;">
        <h5>Menu</h5>
        <ul class="nav flex-column">
            <li>
                <a class="nav-link text-white" href="<?= base_url('admin') ?>">
                 Dashboard
                </a>
            </li>
            <li>
                <a class="nav-link text-white" href="<?= base_url('admin/registeredUsers') ?>">
                 Registered Users
                </a>
            </li>
            <li>
                <a class="nav-link text-white" href="<?= base_url('admin/manageAccounts') ?>">
                    Manage Accounts
                </a>
            </li>
            <li>
               <a class="nav-link text-white" href="<?= base_url('admin/paidBills') ?>">

                     Paid User Bills
                </a>   
            </li>
            <li><a href="<?= site_url('admin/reports') ?>" class="nav-link text-white">Reports</a></li>
        </ul>
    </div>
    <div class="flex-grow-1 p-4">