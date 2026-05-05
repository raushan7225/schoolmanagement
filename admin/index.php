<?php
// admin/index.php
include(__DIR__ . "/includes/header.php");

// --- Fetch Dynamic Dashboard Data ---
try {
    // 1. Total Counts
    $stmt = $pdo->query("SELECT COUNT(*) FROM admissions WHERE approval_status = 'approved'");
    $totalStudents = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM franchises WHERE status = 1");
    $totalFranchises = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT SUM(wallet_balance) FROM franchises");
    $totalBalance = $stmt->fetchColumn() ?: 0;

    // 2. Recent Transactions (Join with franchises for names)
    $stmt = $pdo->prepare("
        SELECT l.*, f.center_name 
        FROM franchise_wallet_ledger l 
        LEFT JOIN franchises f ON l.franchise_id = f.id 
        ORDER BY l.created_at DESC 
        LIMIT 5
    ");
    $stmt->execute();
    $recentTransactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 3. Online Enquiries
    $stmt = $pdo->query("SELECT * FROM enquiries ORDER BY created_at DESC LIMIT 5");
    $recentAdmissionEnquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->query("SELECT * FROM franchise_enquiries ORDER BY created_at DESC LIMIT 5");
    $recentFranchiseEnquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Log error or handle gracefully
}
?>

<div class="pagetitle">
    <h1>Dashboard</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
    </nav>
</div><!-- End Page Title -->

<section class="section dashboard">
    <div class="row">

        <!-- Left side columns -->
        <div class="col-lg-8">
            <div class="row">

                <!-- Action Cards Row -->
                <div class="col-6 col-md-3 mb-3">
                    <a href="franchise-list.php" class="card action-card text-white h-100" style="background: var(--theme-primary-color);">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded d-flex align-items-center justify-content-center bg-white bg-opacity-25 me-2">
                                    <i class="fas fa-store-alt"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 text-white" style="font-size: 13px;">Franchise List</h6>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-6 col-md-3 mb-3">
                    <a href="student-list.php" class="card action-card text-white h-100" style="background: var(--theme-primary-color); opacity: 0.9;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded d-flex align-items-center justify-content-center bg-white bg-opacity-25 me-2">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 text-white" style="font-size: 13px;">Student List</h6>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-6 col-md-3 mb-3">
                    <a href="fees-collection.php" class="card action-card text-white h-100" style="background: var(--theme-secondary-color);">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded d-flex align-items-center justify-content-center bg-white bg-opacity-25 me-2">
                                    <i class="fas fa-wallet"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 text-white" style="font-size: 13px;">Fees Collection</h6>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-6 col-md-3 mb-3">
                    <a href="course-list.php" class="card action-card text-white h-100" style="background: var(--theme-secondary-color); opacity: 0.9;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded d-flex align-items-center justify-content-center bg-white bg-opacity-25 me-2">
                                    <i class="fas fa-book"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 text-white" style="font-size: 13px;">Courses</h6>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Summary Cards -->
                <div class="col-12 col-md-6 col-xxl-4 mt-3">
                    <div class="card info-card sales-card" style="border-left: 5px solid var(--secondary-color);">
                        <div class="card-body">
                            <h5 class="card-title">Total Student <span>| Live</span></h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="ps-3">
                                    <h6 class="mb-0"><?php echo $totalStudents; ?></h6>
                                    <span class="text-success small pt-1 fw-bold">Active</span> <span class="text-muted small pt-2 ps-1">Admissions</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-xxl-4 mt-3">
                    <div class="card info-card revenue-card" style="border-left: 5px solid var(--success-color);">
                        <div class="card-body">
                            <h5 class="card-title">Total Franchise <span>| Live</span></h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success">
                                    <i class="fas fa-building"></i>
                                </div>
                                <div class="ps-3">
                                    <h6 class="mb-0"><?php echo $totalFranchises; ?></h6>
                                    <span class="text-success small pt-1 fw-bold">Verified</span> <span class="text-muted small pt-2 ps-1">Centers</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-xxl-4 col-xl-12 mt-3">
                    <div class="card info-card customers-card" style="border-left: 5px solid var(--warning-color);">
                        <div class="card-body">
                            <h5 class="card-title">Total Balance <span>| Current</span></h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning">
                                    <i class="fas fa-money-bill-1"></i>
                                </div>
                                <div class="ps-3">
                                    <h6 class="mb-0">₹ <?php echo number_format($totalBalance, 2); ?></h6>
                                    <span class="text-muted small pt-2">Combined Wallets</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reports -->
                <div class="col-12 mt-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Reports <span>| Student & Revenue</span></h5>
                            <!-- Line Chart -->
                            <div style="position: relative; height:350px; width:100%">
                                <canvas id="reportsChart"></canvas>
                            </div>
                            <script>
                                document.addEventListener("DOMContentLoaded", () => {
                                    new Chart(document.querySelector('#reportsChart'), {
                                        type: 'line',
                                        data: {
                                            labels: ['2025-11', '2025-12', '2026-01', '2026-02', '2026-03', '2026-04'],
                                            datasets: [{
                                                label: 'Student',
                                                data: [31, 40, 28, 51, 42, 82],
                                                borderColor: '#4154f1',
                                                backgroundColor: 'rgba(65, 84, 241, 0.1)',
                                                fill: true,
                                                tension: 0.4
                                            }, {
                                                label: 'Amount',
                                                data: [15, 11, 32, 18, 9, 24],
                                                borderColor: '#2eca6a',
                                                backgroundColor: 'rgba(46, 202, 106, 0.1)',
                                                fill: true,
                                                tension: 0.4
                                            }, {
                                                label: 'Franchise',
                                                data: [11, 32, 45, 32, 34, 52],
                                                borderColor: '#ff771d',
                                                backgroundColor: 'rgba(255, 119, 29, 0.1)',
                                                fill: true,
                                                tension: 0.4
                                            }]
                                        },
                                        options: {
                                            maintainAspectRatio: false,
                                            plugins: {
                                                legend: {
                                                    display: true,
                                                    position: 'bottom'
                                                }
                                            },
                                            scales: {
                                                y: {
                                                    beginAtZero: true
                                                }
                                            }
                                        }
                                    });
                                });
                            </script>
                        </div>
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="col-12 mt-4">
                    <div class="card recent-sales overflow-auto">
                        <div class="card-body">
                            <h5 class="card-title">Recent Transactions <span>| Latest 5</span></h5>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th scope="col">S.No.</th>
                                            <th scope="col">Franchise Name</th>
                                            <th scope="col">Amount</th>
                                            <th scope="col">Type</th>
                                            <th scope="col">Date</th>
                                            <th scope="col">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(!empty($recentTransactions)): $t_i = 1; foreach($recentTransactions as $tx): ?>
                                        <tr>
                                            <th scope="row"><?php echo $t_i++; ?></th>
                                            <td class="small fw-bold"><?php echo strtoupper($tx['center_name']); ?></td>
                                            <td class="fw-bold">₹ <?php echo number_format($tx['amount'], 2); ?></td>
                                            <td><span class="badge bg-<?php echo ($tx['type'] == 'credit' ? 'success' : 'danger'); ?>"><?php echo ($tx['type'] == 'credit' ? 'CR' : 'DR'); ?></span></td>
                                            <td class="small"><?php echo date('d-m-Y', strtotime($tx['created_at'])); ?></td>
                                            <td><span class="badge bg-<?php echo ($tx['status'] == 'success' ? 'success' : 'warning'); ?>"><?php echo ucfirst($tx['status']); ?></span></td>
                                        </tr>
                                        <?php endforeach; endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div><!-- End Left side columns -->

        <!-- Right side columns -->
        <div class="col-lg-4">

            <!-- Online Admission Enquiry -->
            <div class="card">
                <div class="card-body pb-0">
                    <h5 class="card-title">Online Admission Enquiry <span>| Recent</span></h5>
                    <div class="news">
                        <?php if(empty($recentAdmissionEnquiries)): ?>
                            <div class="post-item clearfix py-3 border-bottom text-muted small text-center">
                                No recent enquiries found.
                            </div>
                        <?php else: foreach($recentAdmissionEnquiries as $enq): ?>
                            <div class="post-item clearfix py-2 border-bottom">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0" style="font-size: 13px;"><a href="#" class="text-dark fw-bold"><?php echo $enq['full_name']; ?></a></h6>
                                    <span class="small text-muted"><?php echo date('d M', strtotime($enq['created_at'])); ?></span>
                                </div>
                                <p class="mb-0 small text-muted">Phone: <?php echo $enq['mobile']; ?></p>
                            </div>
                        <?php endforeach; endif; ?>
                    </div>
                </div>
            </div>

            <!-- Online Franchise Enquiry -->
            <div class="card mt-4">
                <div class="card-body pb-0">
                    <h5 class="card-title">Online Franchise Enquiry <span>| Recent</span></h5>
                    <div class="news">
                        <?php if(empty($recentFranchiseEnquiries)): ?>
                            <div class="post-item clearfix py-3 border-bottom text-muted small text-center">
                                No recent enquiries found.
                            </div>
                        <?php else: foreach($recentFranchiseEnquiries as $enq): ?>
                            <div class="post-item clearfix py-2 border-bottom">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0" style="font-size: 13px;"><a href="#" class="text-dark fw-bold"><?php echo $enq['director_name']; ?></a></h6>
                                    <span class="small text-muted"><?php echo date('d M', strtotime($enq['created_at'])); ?></span>
                                </div>
                                <p class="mb-0 small text-muted">Center: <?php echo $enq['center_name']; ?></p>
                            </div>
                        <?php endforeach; endif; ?>
                    </div>
                </div>
            </div>

            <!-- Website Banners (Mobile & Desktop) -->
            <div class="card mt-4 shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title">Website Banners <span>| Active</span></h5>
                    <div class="list-group list-group-flush">
                        <?php
                        $dashBanners = $pdo->query("SELECT * FROM frontend_banners WHERE status = 1 ORDER BY created_at DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
                        if(empty($dashBanners)):
                        ?>
                            <div class="text-center py-3 text-muted small">No active banners found.</div>
                        <?php else: foreach($dashBanners as $dban): ?>
                            <div class="list-group-item px-0 py-3 border-bottom-dashed">
                                <div class="d-flex gap-2 mb-2">
                                    <div class="flex-fill">
                                        <div class="small fw-bold text-dark text-truncate" style="max-width: 150px;"><?php echo $dban['title'] ?: 'Slider Banner'; ?></div>
                                        <div class="text-muted" style="font-size: 10px;"><?php echo date('d M Y', strtotime($dban['created_at'])); ?></div>
                                    </div>
                                    <div class="text-end">
                                        <a href="frontend-banners.php" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size: 10px;">Edit</a>
                                    </div>
                                </div>
                                <div class="d-flex gap-1">
                                    <div class="bg-light p-1 rounded border text-center flex-fill">
                                        <img src="<?php echo BASE_URL; ?>media/frontend/<?php echo $dban['image']; ?>" class="img-fluid rounded" style="height: 25px; object-fit: cover;">
                                        <div class="text-muted" style="font-size: 7px;">Desk</div>
                                    </div>
                                    <div class="bg-light p-1 rounded border text-center flex-fill">
                                        <?php if($dban['tablet_image']): ?>
                                            <img src="<?php echo BASE_URL; ?>media/frontend/<?php echo $dban['tablet_image']; ?>" class="img-fluid rounded" style="height: 25px; object-fit: cover;">
                                            <div class="text-muted" style="font-size: 7px;">Tab</div>
                                        <?php else: ?>
                                            <div style="height:25px;" class="d-flex align-items-center justify-content-center small text-muted">-</div>
                                            <div class="text-muted" style="font-size: 7px;">No Tab</div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="bg-light p-1 rounded border text-center flex-fill">
                                        <?php if($dban['mobile_image']): ?>
                                            <img src="<?php echo BASE_URL; ?>media/frontend/<?php echo $dban['mobile_image']; ?>" class="img-fluid rounded" style="height: 25px; width: 20px; object-fit: cover;">
                                            <div class="text-muted" style="font-size: 7px;">Mob</div>
                                        <?php else: ?>
                                            <div style="height:25px;" class="d-flex align-items-center justify-content-center small text-muted">-</div>
                                            <div class="text-muted" style="font-size: 7px;">No Mob</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; endif; ?>
                    </div>
                    <div class="text-center mt-3">
                        <a href="frontend-banners.php" class="small fw-bold text-primary text-decoration-none">Manage All Banners &rarr;</a>
                    </div>
                </div>
            </div>

        </div><!-- End Right side columns -->

    </div>
</section>

<?php include(__DIR__ . "/includes/footer.php"); ?>
