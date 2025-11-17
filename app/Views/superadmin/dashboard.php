<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Super Admin</title>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <style>
            body { font-family: 'Poppins', sans-serif; background:#f8f9fc; }
            .sidebar { background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); position:fixed; top:0; left:0; bottom:0; width:225px; color:#fff; z-index:1000; overflow-y:auto; }
            .sidebar a { color:#fff; text-decoration:none; display:block; padding:1rem 1.25rem; }
            .sidebar .sidebar-brand { font-weight:700; font-size:1.2rem; padding:1.25rem; border-bottom:1px solid rgba(255,255,255,.15); }
            .sidebar .nav-link { display:flex; align-items:center; gap:.75rem; border-left:4px solid transparent; }
            .sidebar .nav-link.active { background:rgba(255,255,255,.2); border-left-color:#fff; }
            .sidebar .sidebar-heading { text-transform:uppercase; font-size:.7rem; opacity:.7; padding:.75rem 1.25rem .25rem; }
            .main-wrapper { margin-left:225px; min-height:100vh; display:flex; flex-direction:column; }
            .topbar { background:#fff; border-bottom:1px solid #e3e6f0; padding:1rem 1.5rem; position:sticky; top:0; z-index:999; }
            .main-content { flex:1; padding:2rem; }
            .footer { background:#fff; border-top:1px solid #e3e6f0; padding:1rem; text-align:center; }
            .sidebar-backdrop{ display:none; position:fixed; inset:0; background:rgba(0,0,0,.35); z-index:900; }
            .sidebar-open .sidebar-backdrop{ display:block; }
            @media (max-width: 992px){ .main-wrapper{ margin-left:0; } .sidebar{ transform:translateX(-100%); transition:transform .3s; width:260px; } .sidebar.show{ transform:translateX(0); } .hamburger-btn{ display:inline-block; } }
            .hamburger-btn{ display:none; background:none; border:none; font-size:1.25rem; color:#5a5c69; }
        </style>
</head>
<body>
    <div class="sidebar" id="sidebar" aria-hidden="true">
        <div class="sidebar-brand">Super Admin</div>
        <div class="sidebar-heading">Core</div>
        <a href="<?= base_url('superadmin/dashboard-content') ?>" class="nav-link ajax-link active"><i class="fas fa-gauge"></i><span>Dashboard</span></a>
        <div class="sidebar-heading">Management</div>
        <a href="<?= base_url('superadmin/users') ?>" class="nav-link ajax-link"><i class="fas fa-user-shield"></i><span>Admins</span></a>
        <a href="<?= base_url('superadmin/settings') ?>" class="nav-link ajax-link"><i class="fas fa-gear"></i><span>Settings</span></a>
        <a href="<?= base_url('superadmin/logs') ?>" class="nav-link ajax-link"><i class="fas fa-file-lines"></i><span>Logs</span></a>
        <div class="sidebar-heading">Account</div>
        <a href="<?= base_url('superadmin/logout') ?>" class="nav-link"><i class="fas fa-right-from-bracket"></i><span>Logout</span></a>
    </div>
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <div class="main-wrapper">
        <div class="topbar d-flex align-items-center justify-content-between">
            <button id="hamburgerBtn" class="hamburger-btn"><i class="fas fa-bars"></i></button>
            <div class="fw-semibold">Super Admin Panel</div>
            <div></div>
        </div>

        <div id="mainContent" class="main-content"></div>

        <div class="footer">&copy; <?= date('Y') ?> Super Admin</div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Sidebar toggle (mobile)
        const sidebar = document.getElementById('sidebar');
        const hamburgerBtn = document.getElementById('hamburgerBtn');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');
        function setSidebarOpen(open){
            if(open){
                sidebar.classList.add('show');
                document.body.classList.add('sidebar-open');
                sidebar.setAttribute('aria-hidden','false');
            } else {
                sidebar.classList.remove('show');
                document.body.classList.remove('sidebar-open');
                sidebar.setAttribute('aria-hidden','true');
            }
        }
        hamburgerBtn && hamburgerBtn.addEventListener('click', function(){ setSidebarOpen(!sidebar.classList.contains('show')); });
        sidebarBackdrop && sidebarBackdrop.addEventListener('click', function(){ setSidebarOpen(false); });
        document.addEventListener('keydown', function(e){ if(e.key === 'Escape'){ setSidebarOpen(false); } });

        // AJAX navigation
        $(document).on('click', '.ajax-link', function(e){
            e.preventDefault();
            const url = $(this).attr('href');
            try { localStorage.setItem('sa_lastAjax', url); } catch(_) {}
            loadAjaxPage(url);
            // Active state
            $('.sidebar .nav-link').removeClass('active');
            $(this).addClass('active');
            if (window.matchMedia('(max-width: 992px)').matches) { setSidebarOpen(false); }
        });

        function loadAjaxPage(url){
            $.ajax({ url: url, type:'GET' }).done(function(html){
                try {
                    const nodes = $.parseHTML(html, document, true);
                    $('#mainContent').empty().append(nodes);
                } catch(e){ $('#mainContent').html(html); }
            }).fail(function(){
                $('#mainContent').html('<div class="alert alert-danger">Failed to load content.</div>');
            });
        }

        // Initial load
        $(function(){
            const last = localStorage.getItem('sa_lastAjax') || "<?= base_url('superadmin/dashboard-content') ?>";
            loadAjaxPage(last);
        });
    </script>
</body>
</html>