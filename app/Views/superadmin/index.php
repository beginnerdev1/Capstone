<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Dashboard - Super Admin</title>

    
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="<?= base_url('assets/superadmin/css/style.css?v=' . time()) ?>" rel="stylesheet">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">
    <?php require_once(APPPATH . 'Views/superadmin/navbar.php'); ?>

    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <?php require_once(APPPATH . 'Views/superadmin/sidebar.php'); ?>
        </div>

        <div id="layoutSidenav_content">
            ✅ AJAX Target Area 
            <main id="content-area">

            </main>

            Footer
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; Your Website 2023</div>
                        <div>
                            <a href="#">Privacy Policy</a>
                            &middot;
                            <a href="#">Terms &amp; Conditions</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

     JS Libraries 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="<?= base_url('assets/admin/js/scripts.js') ?>"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="<?= base_url('assets/admin/demo/chart-area-demo.js') ?>"></script>
    <script src="<?= base_url('assets/admin/demo/chart-bar-demo.js') ?>"></script>
    <script src="<?= base_url('assets/admin/demo/chart-pie-demo.js') ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script src="<?= base_url('assets/admin/js/datatables-simple-demo.js') ?>"></script>

✅ AJAX Navigation 
    <script>
   $(document).on("click", ".ajax-link", function(e) {
    e.preventDefault();
    var url = $(this).attr("href");

    $.ajax({
        url: url,
        type: "GET",
        success: function(data) {
            $("#content-area").html(data); // Inject full layout block
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error:", status, error);
            alert("Failed to load page.");
        }
    });
});
    // ✅ Load dashboard on initial page load
    $(document).ready(function() {
    // ✅ Automatically load dashboard when superadmin logs in
    $.ajax({
        url: "<?= base_url('superadmin/dashboard') ?>",
        type: "GET",
        success: function(data) {
            $("#content-area").html(data);
        },
        error: function() {
            $("#content-area").html("<p class='text-danger p-3'>Failed to load dashboard.</p>");
        }
    });
});

    </script>
</body>
</html> -->