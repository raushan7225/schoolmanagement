<?php
// admin/reports.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");
?>

<div class="pagetitle">
    <h1>Report Management Center <span class="badge bg-danger ms-2" style="font-size: 0.6rem; vertical-align: middle;">PRO</span></h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item active">Reports Hub</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <!-- ── Attendance Reports ─────────────────────────────────── -->
        <div class="col-12 mb-4">
            <div class="d-flex align-items-center mb-3">
                <div class="category-icon bg-primary bg-opacity-10 text-primary me-3">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <h4 class="mb-0 fw-bold">Student & Attendance Reports</h4>
            </div>
            <div class="row g-3">
                <div class="col-md-3">
                    <a href="daily-attendance-report.php" class="report-card card h-100 shadow-sm border-0 text-decoration-none">
                        <div class="card-body p-4 text-center">
                            <i class="fas fa-calendar-day text-primary fs-2 mb-3"></i>
                            <h6 class="fw-bold text-dark mb-1">Daily Attendance</h6>
                            <p class="small text-muted mb-0">View student presence matrix for any specific date.</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="attendance-report.php" class="report-card card h-100 shadow-sm border-0 text-decoration-none">
                        <div class="card-body p-4 text-center">
                            <i class="fas fa-history text-success fs-2 mb-3"></i>
                            <h6 class="fw-bold text-dark mb-1">Detailed Log</h6>
                            <p class="small text-muted mb-0">Historical attendance logs with filtering & stats.</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="qr-attendance-report.php" class="report-card card h-100 shadow-sm border-0 text-decoration-none">
                        <div class="card-body p-4 text-center">
                            <i class="fas fa-qrcode text-info fs-2 mb-3"></i>
                            <h6 class="fw-bold text-dark mb-1">QR Scan Reports</h6>
                            <p class="small text-muted mb-0">Monitor digital check-ins via student QR IDs.</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="student-attendance.php" class="report-card card h-100 shadow-sm border-0 text-decoration-none">
                        <div class="card-body p-4 text-center">
                            <i class="fas fa-user-edit text-warning fs-2 mb-3"></i>
                            <h6 class="fw-bold text-dark mb-1">Mark Attendance</h6>
                            <p class="small text-muted mb-0">Manual batch attendance marking interface.</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- ── Academic & Exam Reports ──────────────────────────────── -->
        <div class="col-12 mb-4">
            <div class="d-flex align-items-center mb-3">
                <div class="category-icon bg-danger bg-opacity-10 text-danger me-3">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <h4 class="mb-0 fw-bold">Academic & Exam Reports</h4>
            </div>
            <div class="row g-3">
                <div class="col-md-3">
                    <a href="online-marks.php" class="report-card card h-100 shadow-sm border-0 text-decoration-none">
                        <div class="card-body p-4 text-center">
                            <i class="fas fa-award text-danger fs-2 mb-3"></i>
                            <h6 class="fw-bold text-dark mb-1">Online Marks</h6>
                            <p class="small text-muted mb-0">CBT exam results with pass/fail analytics.</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="marks-entry-list.php" class="report-card card h-100 shadow-sm border-0 text-decoration-none">
                        <div class="card-body p-4 text-center">
                            <i class="fas fa-file-signature text-secondary fs-2 mb-3"></i>
                            <h6 class="fw-bold text-dark mb-1">Marksheet Log</h6>
                            <p class="small text-muted mb-0">View and manage student marksheet entries.</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="#" class="report-card card h-100 shadow-sm border-0 text-decoration-none opacity-50">
                        <div class="card-body p-4 text-center">
                            <i class="fas fa-chart-line text-purple fs-2 mb-3"></i>
                            <h6 class="fw-bold text-dark mb-1">Exam Trends</h6>
                            <p class="small text-muted mb-0">Analyze subject-wise pass % (Coming Soon).</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- ── Financial Reports ───────────────────────────────────── -->
        <div class="col-12 mb-4">
            <div class="d-flex align-items-center mb-3">
                <div class="category-icon bg-success bg-opacity-10 text-success me-3">
                    <i class="fas fa-wallet"></i>
                </div>
                <h4 class="mb-0 fw-bold">Financial & Accounting Reports</h4>
            </div>
            <div class="row g-3">
                <div class="col-md-3">
                    <a href="fees-collection.php" class="report-card card h-100 shadow-sm border-0 text-decoration-none">
                        <div class="card-body p-4 text-center">
                            <i class="fas fa-money-bill-wave text-success fs-2 mb-3"></i>
                            <h6 class="fw-bold text-dark mb-1">Fees Collection</h6>
                            <p class="small text-muted mb-0">Detailed list of fee payments by students.</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="due-fees.php" class="report-card card h-100 shadow-sm border-0 text-decoration-none">
                        <div class="card-body p-4 text-center">
                            <i class="fas fa-exclamation-triangle text-danger fs-2 mb-3"></i>
                            <h6 class="fw-bold text-dark mb-1">Due Fees Report</h6>
                            <p class="small text-muted mb-0">Track outstanding balances per student/course.</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="office-ledger.php" class="report-card card h-100 shadow-sm border-0 text-decoration-none">
                        <div class="card-body p-4 text-center">
                            <i class="fas fa-book text-info fs-2 mb-3"></i>
                            <h6 class="fw-bold text-dark mb-1">Office Ledger</h6>
                            <p class="small text-muted mb-0">General expense and deposit transaction logs.</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="franchise-wallet-ledger.php" class="report-card card h-100 shadow-sm border-0 text-decoration-none">
                        <div class="card-body p-4 text-center">
                            <i class="fas fa-university text-secondary fs-2 mb-3"></i>
                            <h6 class="fw-bold text-dark mb-1">Wallet Transactions</h6>
                            <p class="small text-muted mb-0">Track all franchise wallet recharges & usage.</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- ── Management Reports ────────────────────────────────── -->
        <div class="col-12 mb-4">
            <div class="d-flex align-items-center mb-3">
                <div class="category-icon bg-warning bg-opacity-10 text-warning me-3">
                    <i class="fas fa-sitemap"></i>
                </div>
                <h4 class="mb-0 fw-bold">Institutional Management</h4>
            </div>
            <div class="row g-3">
                <div class="col-md-3">
                    <a href="#" class="report-card card h-100 shadow-sm border-0 text-decoration-none opacity-50">
                        <div class="card-body p-4 text-center">
                            <i class="fas fa-users-cog text-warning fs-2 mb-3"></i>
                            <h6 class="fw-bold text-dark mb-1">Admission Analytics</h6>
                            <p class="small text-muted mb-0">Enrollment trends and center growth (Coming Soon).</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="franchise-list.php" class="report-card card h-100 shadow-sm border-0 text-decoration-none">
                        <div class="card-body p-4 text-center">
                            <i class="fas fa-store-alt text-primary fs-2 mb-3"></i>
                            <h6 class="fw-bold text-dark mb-1">Center Performance</h6>
                            <p class="small text-muted mb-0">List and performance status of all franchises.</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.category-icon { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
.report-card { transition: all 0.3s cubic-bezier(.25,.8,.25,1); border-radius: 15px !important; }
.report-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; border: 1px solid var(--bs-primary) !important; }
.report-card i { transition: transform 0.3s ease; }
.report-card:hover i { transform: scale(1.1); }
.text-purple { color: #6f42c1; }
</style>

<?php include(__DIR__ . "/includes/footer.php"); ?>
