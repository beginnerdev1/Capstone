<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>User Reports Dashboard - SB Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
     <link href="<?php echo base_url('assets/admin/css/styles.css') ?>" rel="stylesheet" />
    <style>
        #map { height: 400px; width: 100%; }
    </style>
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">
    <?php require_once(APPPATH . 'Views/admin/navbar.php'); ?>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">User Reports Dashboard</h1>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                Reports Table
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="datatable-table table table-striped table-bordered" id="reportsTable">
                                        <thead>
                                            <tr>
                                                <th>User</th>
                                                <th>Problem</th>
                                                <th>Date</th>
                                                <th>Location</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($reports)): ?>
                                                <?php foreach ($reports as $report): ?>
                                                    <tr data-lat="<?= $report['latitude'] ?>" data-lng="<?= $report['longitude'] ?>">
                                                        <td><?= htmlspecialchars($report['user']) ?></td>
                                                        <td><?= htmlspecialchars($report['issue_type']) ?></td>
                                                        <td><?= htmlspecialchars($report['description']) ?></td>
                                                        <td><?= htmlspecialchars($report['status']) ?></td>
                                                        <td><?= htmlspecialchars($report['address']) ?></td>
                                                        <td><?= htmlspecialchars($report['created_at']) ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="4" class="text-center">No reports found.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                Reported Locations
                            </div>
                            <div class="card-body">
                                <div id="map"></div>
                            </div>
                        </div>
                    </div>
                   
                </div>
            </div>
        </main>
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
    <!--
    Google Maps JS API (replace YOUR_API_KEY with your actual key)
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY"></script>
    <script>
        // Collect report locations from table
        const reports = [];
        document.querySelectorAll('#reportsTable tbody tr').forEach(row => {
            const lat = parseFloat(row.getAttribute('data-lat'));
            const lng = parseFloat(row.getAttribute('data-lng'));
            if (!isNaN(lat) && !isNaN(lng)) {
                reports.push({
                    lat: lat,
                    lng: lng,
                    info: row.cells[1].textContent + ' by ' + row.cells[0].textContent
                });
            }
        });

        function initMap() {
            const center = reports.length ? {lat: reports[0].lat, lng: reports[0].lng} : {lat: 0, lng: 0};
            const map = new google.maps.Map(document.getElementById('map'), {
                zoom: 12,
                center: center
            });

            reports.forEach(report => {
                const marker = new google.maps.Marker({
                    position: {lat: report.lat, lng: report.lng},
                    map: map
                });
                const infowindow = new google.maps.InfoWindow({
                    content: report.info
                });
                marker.addListener('click', function() {
                    infowindow.open(map, marker);
                });
            });
        }

        window.onload = initMap;
    </script>
    -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="<?= base_url('assets/admin/js/scripts.js') ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
</body>
</html>