                <div class="container-fluid px-4">
                    <h1 class="mt-4">Dashboard</h1>

                    <!-- Info Cards -->
                    <div class="row">
                        <?php
                        $cards = [
                            ['title' => 'Registered Users', 'color' => 'primary', 'link' => 'users'],
                            ['title' => 'Ongoing Bills', 'color' => 'warning', 'link' => 'bills'],
                            ['title' => 'Paid User Bills', 'color' => 'success', 'link' => 'paid'],
                            ['title' => 'Reports', 'color' => 'danger', 'link' => 'reports'],
                        ];
                        foreach ($cards as $card): ?>
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-<?= $card['color'] ?> text-white mb-4">
                                <div class="card-body"><?= $card['title'] ?></div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-white stretched-link ajax-link" href="<?= base_url('superadmin/' . $card['link']) ?>">View Details</a>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Charts -->
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="card mb-4">
                                <div class="card-header"><i class="fas fa-chart-area me-1"></i> Area Chart Example</div>
                                <div class="card-body"><canvas id="myAreaChart" width="100%" height="40"></canvas></div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card mb-4">
                                <div class="card-header"><i class="fas fa-chart-bar me-1"></i> Bar Chart Example</div>
                                <div class="card-body"><canvas id="myBarChart" width="100%" height="40"></canvas></div>
                            </div>
                        </div>
                    </div>
                </div>