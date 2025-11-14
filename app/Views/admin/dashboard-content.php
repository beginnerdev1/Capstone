<?php
// app/Views/admin/Dashboard-content.php
?>
<script>
// Pass PHP data to JavaScript
window.dashboardData = {
    months: <?= $months ?? '[]' ?>,
    incomeData: <?= $incomeData ?? '[]' ?>,
    totalCollected: <?= $totalCollected ?? 0 ?>,
    monthlyTotal: <?= $monthlyTotal ?? 0 ?>,
    active: <?= $active ?? 0 ?>,
    pending: <?= $pending ?? 0 ?>,
    inactive: <?= $inactive ?? 0 ?>,
    normalRevenue: <?= $normalRevenue ?? 0 ?>,
    seniorRevenue: <?= $seniorRevenue ?? 0 ?>,
    aloneRevenue: <?= $aloneRevenue ?? 0 ?>
};
</script>

<!-- Main Content -->
<div class="container-fluid">
    <div class="main-content" >
        <div class="page-header d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="page-title h3 mb-0 text-gray-800">Dashboard</h1>
            <a href="#" class="btn-report d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-download fa-sm text-white-50"></i> Generate Report
            </a>
        </div>

        <!-- Stats Cards Row -->
        <div class="row">
            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mb-4">
                <div class="stat-card primary">
                    <div class="stat-card-body">
                        <div>
                            <div class="stat-label">Earnings (Monthly)</div>
                            <div class="stat-value"><?= number_format($monthlyTotal, 2)?></div>
                        </div>
                        <i class="fas fa-calendar stat-icon"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mb-4">
                <div class="stat-card success">
                    <div class="stat-card-body">
                        <div>
                            <div class="stat-label">Earnings (Annual)</div>
                            <div class="stat-value"><?= number_format($totalCollected, 2,)?></div>
                        </div>
                        <i class="fas fa-dollar-sign stat-icon"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mb-4">
                <div class="stat-card info">
                    <div class="stat-card-body">
                        <div>
                            <div class="stat-label">Active Users</div>
                            <div class="stat-value"><?= number_format($active ?? 0) ?></div>
                        </div>
                        <i class="fas fa-users stat-icon"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mb-4">
                <div class="stat-card warning">
                    <div class="stat-card-body">
                        <div>
                            <div class="stat-label">Pending Requests</div>
                            <div class="stat-value"><?= number_format($pending)?></div>
                        </div>
                        <i class="fas fa-comments stat-icon"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mt-4">

            <!-- Area Chart -->
            <div class="col-xl-8 col-lg-8 col-md-12 col-sm-12 col-12 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Earnings Overview</h6>
                    </div>
                    <div class="card-body" style="width: 100%; max-width: 700px; margin: 0 auto;">
                        <div class="chart-container" style="position: relative; height:40vh; width:80%">
                            <canvas id="incomeChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            
            <!-- Pie Chart -->
            <div class="col-xl-4 col-lg-4 col-md-12 col-sm-12 col-12 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Revenue by Rate Category</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-pie pt-4 pb-2">
                            <canvas id="revenueChart"></canvas>
                        </div>
                        <div class="mt-4 text-center small">
                            <span class="mr-2">
                                <i class="fas fa-circle text-primary"></i> Normal (₱60)
                            </span>
                            <span class="mr-2">
                                <i class="fas fa-circle text-success"></i> Senior Citizen (₱48)
                            </span>
                            <span class="mr-2">
                                <i class="fas fa-circle text-info"></i> Living Alone (₱30)
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Projects & Table Row -->
        <div class="row">

            <!-- Projects Column -->
            <div class="col-lg-6 col-md-12 col-sm-12 col-12 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Projects</h6>
                    </div>
                    <div class="card-body">
                        <h4 class="small font-weight-bold">Server Migration <span class="float-right">20%</span></h4>
                        <div class="progress mb-4">
                            <div class="progress-bar bg-danger" role="progressbar" style="width: 20%"
                                aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <h4 class="small font-weight-bold">Sales Tracking <span class="float-right">40%</span></h4>
                        <div class="progress mb-4">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: 40%"
                                aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <h4 class="small font-weight-bold">Customer Database <span class="float-right">60%</span></h4>
                        <div class="progress mb-4">
                            <div class="progress-bar" role="progressbar" style="width: 60%"
                                aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <h4 class="small font-weight-bold">Payout Details <span class="float-right">80%</span></h4>
                        <div class="progress mb-4">
                            <div class="progress-bar bg-info" role="progressbar" style="width: 80%"
                                aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <h4 class="small font-weight-bold">Account Setup <span class="float-right">Complete!</span></h4>
                        <div class="progress">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 100%"
                                aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Table Column -->
            <div class="col-lg-6 col-md-12 col-sm-12 col-12 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Data Table</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Position</th>
                                        <th>Office</th>
                                        <th>Age</th>
                                        <th>Salary</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Tiger Nixon</td>
                                        <td>System Architect</td>
                                        <td>Edinburgh</td>
                                        <td>61</td>
                                        <td>$320,800</td>
                                    </tr>
                                    <tr>
                                        <td>Garrett Winters</td>
                                        <td>Accountant</td>
                                        <td>Tokyo</td>
                                        <td>63</td>
                                        <td>$170,750</td>
                                    </tr>
                                    <tr>
                                        <td>Ashton Cox</td>
                                        <td>Junior Technical Author</td>
                                        <td>San Francisco</td>
                                        <td>66</td>
                                        <td>$86,000</td>
                                    </tr>
                                    <tr>
                                        <td>Cedric Kelly</td>
                                        <td>Senior Javascript Developer</td>
                                        <td>Edinburgh</td>
                                        <td>22</td>
                                        <td>$433,060</td>
                                    </tr>
                                    <tr>
                                        <td>Airi Satou</td>
                                        <td>Accountant</td>
                                        <td>Tokyo</td>
                                        <td>33</td>
                                        <td>$162,700</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>