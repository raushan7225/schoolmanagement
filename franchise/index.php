<?php
require_once('../common/config.php');
checkRole('franchise');

// Basic user info from session
$username = $_SESSION['username'];
$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Franchise Dashboard - NEBOOSASE</title>
    <?php include("../common/meta.php"); ?>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/franchise.css">
    <link id="dynamic-theme-css" rel="stylesheet" href="<?php echo BASE_URL; ?>theme.php">
    <script src="<?php echo BASE_URL; ?>assets/vendor/chartjs/chart.min.js"></script>
</head>
<body class="bg-light">
    <?php include("../common/header.php"); ?>
    
    <div class="container py-5">
        <div class="row mb-4 align-items-center">
            <div class="col-md-6">
                <h1 class="display-6 fw-bold text-primary-theme">Franchise Portal</h1>
                <p class="text-muted">Welcome back, <?php echo $username; ?> (Director)</p>
            </div>
            <div class="col-md-6 text-md-end">
                <div class="d-flex justify-content-md-end gap-2">
                    <button onclick="initDashboard()" class="btn btn-outline-primary rounded-pill px-3" title="Refresh Dashboard">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                    <a href="franchise-application.php" class="btn btn-primary-theme rounded-pill px-4 shadow-sm">
                        <i class="fas fa-edit me-2"></i>Update Center Info
                    </a>
                </div>
            </div>
        </div>

        <!-- Vertical Tab Layout -->
        <div class="row g-4">
            <!-- Sidebar Navigation -->
            <div class="col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white">
                    <div class="nav flex-column nav-pills dashboard-nav-vertical" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <button class="nav-link active mb-2" id="overview-tab" data-bs-toggle="pill" data-bs-target="#overview" type="button" role="tab">
                            <i class="fas fa-th-large me-3"></i>Dashboard
                        </button>
                        <button class="nav-link mb-2" id="center-tab" data-bs-toggle="pill" data-bs-target="#center-details" type="button" role="tab">
                            <i class="fas fa-building me-3"></i>My Center
                        </button>
                        <button class="nav-link mb-2" id="leads-tab" data-bs-toggle="pill" data-bs-target="#leads-view" type="button" role="tab">
                            <i class="fas fa-bullseye me-3"></i>Lead Manager
                        </button>
                        <button class="nav-link mb-2" id="students-tab" data-bs-toggle="pill" data-bs-target="#students-list" type="button" role="tab">
                            <i class="fas fa-user-graduate me-3"></i>Our Students
                        </button>
                        <button class="nav-link mb-2" id="admission-tab" data-bs-toggle="pill" data-bs-target="#admission-view" type="button" role="tab">
                            <i class="fas fa-user-plus me-3"></i>New Admission
                        </button>
                        <button class="nav-link mb-2" id="wallet-tab" data-bs-toggle="pill" data-bs-target="#wallet-view" type="button" role="tab">
                            <i class="fas fa-wallet me-3"></i>Wallet & Ledger
                        </button>
                        <button class="nav-link mb-2" id="vault-tab" data-bs-toggle="pill" data-bs-target="#vault-view" type="button" role="tab">
                            <i class="fas fa-folder-open me-3"></i>Document Vault
                        </button>
                        <button class="nav-link mb-2" id="attendance-tab" data-bs-toggle="pill" data-bs-target="#attendance-view" type="button" role="tab">
                            <i class="fas fa-qrcode me-3"></i>Attendance
                        </button>
                        <button class="nav-link" id="support-tab" data-bs-toggle="pill" data-bs-target="#support-view" type="button" role="tab">
                            <i class="fas fa-headset me-3"></i>Support Desk
                        </button>
                    </div>

                    <div class="mt-auto pt-4">
                        <div class="bg-primary-light p-3 rounded-4 border">
                            <h6 class="fw-bold text-primary-theme small mb-1">Need Assistance?</h6>
                            <p class="text-muted small mb-3">Contact your regional manager or open a ticket.</p>
                            <a href="tel:+919430204280" class="btn btn-primary-theme btn-sm w-100 rounded-pill">Call Support</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="col-lg-9">
                <div class="tab-content h-100" id="v-pills-tabContent">
                    <!-- Tab 1: Overview -->
                    <div class="tab-pane fade show active" id="overview" role="tabpanel">
                        <div class="row g-4">
                            <!-- Left Column: Main Stats & Wallet -->
                            <div class="col-lg-8">
                                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 wallet-card-gradient">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h6 class="opacity-75 text-white text-uppercase small ls-1 mb-2">Current Balance</h6>
                                            <h1 class="display-5 fw-bold text-white mb-3" id="walletBalance">₹ 0.00</h1>
                                            <div class="d-flex flex-wrap gap-4">
                                                <div><small class="opacity-75 d-block">Center Code</small> <strong id="centerCode">Loading...</strong></div>
                                                <div><small class="opacity-75 d-block">Account Status</small> <span class="badge bg-success" id="centerStatus">ACTIVE</span></div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-md-end mt-4 mt-md-0">
                                            <i class="fas fa-wallet display-1 opacity-25"></i>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row g-4 mb-4">
                                    <div class="col-md-4">
                                        <div class="card border-0 shadow-sm rounded-4 p-4 text-center stat-card h-100">
                                            <div class="fs-1 mb-2 text-primary-theme"><i class="fas fa-users"></i></div>
                                            <h3 class="fw-bold mb-0" id="statTotalStudents">0</h3>
                                            <p class="small text-muted mb-0">Total Students</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card border-0 shadow-sm rounded-4 p-4 text-center stat-card h-100">
                                            <div class="fs-1 mb-2 text-success"><i class="fas fa-check-circle"></i></div>
                                            <h3 class="fw-bold mb-0" id="statApprovedStudents">0</h3>
                                            <p class="small text-muted mb-0">Approved Admissions</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card border-0 shadow-sm rounded-4 p-4 text-center stat-card h-100">
                                            <div class="fs-1 mb-2 text-warning"><i class="fas fa-clock"></i></div>
                                            <h3 class="fw-bold mb-0" id="statPendingStudents">0</h3>
                                            <p class="small text-muted mb-0">Pending Reviews</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Student Analytics Chart -->
                                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                                    <h5 class="fw-bold mb-4">Admission Analytics</h5>
                                    <div style="height: 250px;">
                                        <canvas id="admissionChart"></canvas>
                                    </div>
                                </div>

                                <!-- Recent Notices -->
                                <div class="card border-0 shadow-sm rounded-4 p-4">
                                    <h5 class="fw-bold mb-4"><i class="fas fa-bullhorn me-2 text-primary-theme"></i>Latest Announcements</h5>
                                    <div id="noticeList" class="list-group list-group-flush">
                                        <p class="text-muted small text-center py-4">No recent announcements.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column: Profile Summary -->
                            <div class="col-lg-4">
                                <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                                    <h5 class="fw-bold mb-4">Quick Actions</h5>
                                    <div class="d-grid gap-3">
                                        <button onclick="document.getElementById('admission-tab').click()" class="btn btn-success-light text-success border-0 rounded-4 p-3 text-start d-flex align-items-center">
                                            <div class="rounded-circle bg-success text-white p-2 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="fas fa-user-plus"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold">New Admission</div>
                                                <small class="opacity-75">Enroll a student</small>
                                            </div>
                                        </button>
                                        <button onclick="document.getElementById('wallet-tab').click()" class="btn bg-primary-light text-primary border-0 rounded-4 p-3 text-start d-flex align-items-center">
                                            <div class="rounded-circle bg-primary text-white p-2 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="fas fa-plus-circle"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold">Add Wallet Credit</div>
                                                <small class="opacity-75">Request top-up</small>
                                            </div>
                                        </button>
                                        <button onclick="document.getElementById('leads-tab').click()" class="btn bg-info-light text-info border-0 rounded-4 p-3 text-start d-flex align-items-center">
                                            <div class="rounded-circle bg-info text-white p-2 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="fas fa-bullseye"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold">Manage Leads</div>
                                                <small class="opacity-75">View potential students</small>
                                            </div>
                                        </button>
                                    </div>
                                    <hr class="my-4">
                                    <h5 class="fw-bold mb-4">Director Profile</h5>
                                    <div class="text-center mb-4">
                                        <div class="avatar-container mb-3">
                                            <img src="<?php echo BASE_URL; ?>assets/images/user-placeholder.png" id="directorPhoto" class="rounded-circle shadow-sm border border-3 border-white" style="width: 100px; height: 100px; object-fit: cover;">
                                        </div>
                                        <h5 class="mt-2 mb-0 fw-bold" id="sidebarDirectorName">Loading...</h5>
                                        <span class="badge bg-primary-theme opacity-75 small" id="sidebarCenterCode">CENTER #---</span>
                                    </div>
                                    <hr>
                                    <div class="profile-info-list small">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Institute:</span>
                                            <span class="fw-bold text-end" id="sidebarCenterName">...</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Director Mobile:</span>
                                            <span class="fw-bold" id="sidebarMobile">...</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Center Email:</span>
                                            <span class="fw-bold" id="sidebarEmail">...</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Established:</span>
                                            <span class="fw-bold" id="sidebarEstd">...</span>
                                        </div>
                                    </div>
                                    <div class="mt-4 pt-3 border-top">
                                        <button onclick="document.getElementById('center-tab').click()" class="btn btn-outline-primary btn-sm w-100 rounded-pill">View Full Details</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 2: Center Details -->
                    <div class="tab-pane fade" id="center-details" role="tabpanel">
                        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white" id="fullCenterContent">
                            <div class="text-center py-5">
                                <div class="spinner-border text-primary"></div>
                                <p class="mt-2">Loading profile details...</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 3: Lead Manager -->
                    <div class="tab-pane fade" id="leads-view" role="tabpanel">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                            <div class="card-header bg-white p-4 border-0 d-flex justify-content-between align-items-center">
                                <h5 class="fw-bold mb-0">Local Lead Manager</h5>
                                <div class="d-flex gap-2">
                                    <div class="input-group input-group-sm w-auto">
                                        <input type="text" class="form-control" id="leadSearchInput" placeholder="Search leads..." onkeyup="filterTable('leadSearchInput', 'leadsTableBody')">
                                        <button class="btn btn-primary-theme"><i class="fas fa-search"></i></button>
                                    </div>
                                    <button class="btn btn-sm btn-primary-theme rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addLeadModal">
                                        <i class="fas fa-plus me-1"></i>New Lead
                                    </button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4">Date</th>
                                            <th>Potential Student</th>
                                            <th>Interested Course</th>
                                            <th>Follow-up</th>
                                            <th>Status</th>
                                            <th class="pe-4 text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="leadsTableBody">
                                        <tr><td colspan="6" class="text-center py-5 text-muted">No leads found. Start by adding a potential student.</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 4: Students List -->
                    <div class="tab-pane fade" id="students-list" role="tabpanel">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                            <div class="card-header bg-white p-4 border-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="fw-bold mb-0">Registered Students</h5>
                                    <div class="d-flex gap-2">
                                        <button onclick="document.getElementById('admission-tab').click()" class="btn btn-sm btn-success rounded-pill px-3">
                                            <i class="fas fa-user-plus me-1"></i>Direct Admission
                                        </button>
                                        <div class="input-group input-group-sm w-auto">
                                            <input type="text" class="form-control" id="studentSearchInput" placeholder="Search student..." onkeyup="filterTable('studentSearchInput', 'studentsTableBody')">
                                            <button class="btn btn-primary-theme"><i class="fas fa-search"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4">Reg. No</th>
                                            <th>Student Name</th>
                                            <th>Course</th>
                                            <th>Joined Date</th>
                                            <th>Status</th>
                                            <th class="pe-4 text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="studentsTableBody">
                                        <tr><td colspan="6" class="text-center py-5 text-muted">Loading student directory...</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 4.5: Direct Admission (Integrated) -->
                    <div class="tab-pane fade" id="admission-view" role="tabpanel">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                            <div class="card-header bg-white p-4 border-0">
                                <h5 class="fw-bold mb-0">Direct Student Admission</h5>
                                <p class="text-muted small mb-0">Complete the form below to enroll a new student.</p>
                            </div>
                            <div class="card-body p-4">
                                <form id="direct-admission-form" class="row g-4">
                                    <div class="col-md-6">
                                        <div class="p-3 bg-light rounded-4">
                                            <label class="form-label small fw-bold">Select Course <span class="text-danger">*</span></label>
                                            <select class="form-select border-0 shadow-none" name="course_id" required id="admissionCourseSelect">
                                                <option value="">Loading courses...</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 bg-light rounded-4">
                                            <label class="form-label small fw-bold">Academic Session <span class="text-danger">*</span></label>
                                            <select class="form-select border-0 shadow-none" name="session_id" required id="admissionSessionSelect">
                                                <option value="">Loading sessions...</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="p-3 bg-light rounded-4 h-100">
                                            <h6 class="fw-bold text-muted small mb-3">PERSONAL INFORMATION</h6>
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label x-small fw-bold text-muted">FULL NAME</label>
                                                    <input type="text" class="form-control" name="full_name" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label x-small fw-bold text-muted">MOBILE NO</label>
                                                    <input type="text" class="form-control" name="mobile" maxlength="10" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label x-small fw-bold text-muted">EMAIL ADDRESS</label>
                                                    <input type="email" class="form-control" name="email">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label x-small fw-bold text-muted">DATE OF BIRTH</label>
                                                    <input type="date" class="form-control" name="dob" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label x-small fw-bold text-muted">FATHER\'S NAME</label>
                                                    <input type="text" class="form-control" name="father_name" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label x-small fw-bold text-muted">GENDER</label>
                                                    <select class="form-select" name="gender" required>
                                                        <option value="Male">Male</option>
                                                        <option value="Female">Female</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 bg-light rounded-4 h-100">
                                            <h6 class="fw-bold text-muted small mb-3">DOCUMENTS & ADDRESS</h6>
                                            <div class="mb-3">
                                                <label class="form-label x-small fw-bold text-muted">PHOTO</label>
                                                <input type="file" class="form-control form-control-sm" name="photo" accept="image/*" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label x-small fw-bold text-muted">SIGNATURE</label>
                                                <input type="file" class="form-control form-control-sm" name="signature" accept="image/*" required>
                                            </div>
                                            <div class="mb-0">
                                                <label class="form-label x-small fw-bold text-muted">FULL ADDRESS</label>
                                                <textarea class="form-control" name="address" rows="3" required></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 text-center pt-3">
                                        <button type="submit" class="btn btn-primary-theme rounded-pill px-5 py-2 shadow-sm">
                                            <i class="fas fa-check-circle me-2"></i>Submit Admission Request
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 5: Wallet & Ledger -->
                    <div class="tab-pane fade" id="wallet-view" role="tabpanel">
                        <div class="row g-4">
                            <!-- Pending Requests Section -->
                            <div class="col-lg-12 mb-4 d-none" id="pendingRequestsSection">
                                <div class="card border-0 shadow-sm rounded-4 overflow-hidden border-warning border-start border-5">
                                    <div class="card-header bg-white p-4 border-0">
                                        <h6 class="fw-bold mb-0 text-warning">Pending Approval Top-ups</h6>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table mb-0 table-sm">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th class="ps-4">Date</th>
                                                    <th>Amount</th>
                                                    <th>Method</th>
                                                    <th class="pe-4 text-end">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody id="pendingRequestsBody"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                                    <div class="card-header bg-white p-4 border-0 d-flex justify-content-between align-items-center">
                                        <h5 class="fw-bold mb-0">Wallet Transaction Ledger</h5>
                                        <button class="btn btn-sm btn-primary-theme rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#topupModal">
                                            <i class="fas fa-plus-circle me-1"></i>Add Credit
                                        </button>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table mb-0">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th class="ps-4">Date</th>
                                                    <th>Transaction ID</th>
                                                    <th>Description</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                    <th class="pe-4 text-end">Receipt</th>
                                                </tr>
                                            </thead>
                                            <tbody id="ledgerTableBody">
                                                <tr><td colspan="6" class="text-center py-5 text-muted">No transactions found.</td></tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 6: Document Vault -->
                    <div class="tab-pane fade" id="vault-view" role="tabpanel">
                        <div class="row g-4">
                            <div class="col-lg-4">
                                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                                    <h6 class="fw-bold mb-3"><i class="fas fa-upload me-2 text-primary-theme"></i>Submit Documents</h6>
                                    <form id="vaultUploadForm">
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold">Document Title</label>
                                            <input type="text" class="form-control form-control-sm" placeholder="e.g. GST Certificate">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold">File</label>
                                            <input type="file" class="form-control form-control-sm">
                                        </div>
                                        <button type="submit" class="btn btn-primary-theme btn-sm w-100 rounded-pill">Upload to Vault</button>
                                    </form>
                                </div>
                                <div class="card border-0 shadow-sm rounded-4 p-4 bg-primary-theme text-white">
                                    <h6 class="fw-bold mb-2">Need Branding?</h6>
                                    <p class="small opacity-75 mb-3">Download the latest marketing kit including logos and posters.</p>
                                    <button class="btn btn-light btn-sm w-100 rounded-pill">Download Kit</button>
                                </div>
                            </div>
                            <div class="col-lg-8">
                                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                                    <div class="card-header bg-white p-4 border-0">
                                        <h6 class="fw-bold mb-0">Official & Submitted Documents</h6>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table mb-0">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th class="ps-4">Document</th>
                                                    <th>Type</th>
                                                    <th>Date</th>
                                                    <th class="pe-4 text-end">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody id="vaultTableBody">
                                                <tr>
                                                    <td class="ps-4"><i class="fas fa-file-pdf text-danger me-2"></i>Franchise Certificate</td>
                                                    <td>Official</td>
                                                    <td><small>15 Jan 2024</small></td>
                                                    <td class="pe-4 text-end"><span class="badge bg-success">Verified</span></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 7: Attendance -->
                    <div class="tab-pane fade" id="attendance-view" role="tabpanel">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                            <div class="card-header bg-white p-4 border-0 d-flex justify-content-between align-items-center">
                                <h5 class="fw-bold mb-0">Center Attendance Log</h5>
                                <a href="../admin/qr-attendance.php" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill">Open Scanner</a>
                            </div>
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4">Date & Time</th>
                                            <th>Student Name</th>
                                            <th>Reg. No</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="attendanceTableBody">
                                        <tr><td colspan="4" class="text-center py-5 text-muted">Loading attendance logs...</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 8: Support -->
                    <div class="tab-pane fade" id="support-view" role="tabpanel">
                        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white text-center mb-4">
                            <div class="mb-4">
                                <i class="fas fa-headset display-1 text-light"></i>
                            </div>
                            <h4 class="fw-bold mb-2">Help & Support Desk</h4>
                            <p class="text-muted mb-4 mx-auto mx-max-500">Have a question about admissions, wallet credits, or academic results? Our support team is here to help.</p>
                            <div class="d-flex justify-content-center gap-3">
                                <button class="btn btn-primary-theme rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#newTicketModal">
                                    <i class="fas fa-plus me-2"></i>Create New Ticket
                                </button>
                                <a href="tel:+919430204280" class="btn btn-outline-primary rounded-pill px-4">
                                    <i class="fas fa-phone-alt me-2"></i>Call Support
                                </a>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                            <div class="card-header bg-white p-4 border-0">
                                <h5 class="fw-bold mb-0">Your Recent Tickets</h5>
                            </div>
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4">Reference</th>
                                            <th>Subject</th>
                                            <th>Department</th>
                                            <th>Date</th>
                                            <th class="pe-4 text-end">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="ticketsTableBody">
                                        <tr><td colspan="5" class="text-center py-5 text-muted">No support tickets found.</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div> <!-- Close tab-content -->
            </div> <!-- Close col-lg-9 -->
        </div> <!-- Close row -->
    </div> <!-- Close container -->

    <!-- Lead Modal -->
    <div class="modal fade" id="addLeadModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header bg-primary py-3">
                    <h5 class="modal-title fw-bold text-white"><i class="fas fa-bullseye me-2"></i>Add New Potential Lead</h5>
                    <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                        <i class="fas fa-times text-white"></i>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <form id="leadForm" novalidate>
                        <input type="hidden" name="action" value="add_student_lead">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Student Name <span class="text-danger">*</span></label>
                            <input type="text" name="director_name" class="form-control" required placeholder="Full name of student">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Mobile Number <span class="text-danger">*</span></label>
                            <input type="tel" name="phone" class="form-control" required placeholder="10-digit mobile">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Interested Course</label>
                            <input type="text" name="course" class="form-control" placeholder="e.g. ADCA, DCA">
                        </div>
                        <button type="submit" class="btn btn-primary-theme w-100 rounded-pill mt-3">Save Lead</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Topup Modal -->
    <div class="modal fade" id="topupModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header bg-primary py-3">
                    <h5 class="modal-title fw-bold text-white"><i class="fas fa-plus-circle me-2"></i>Request Wallet Top-up</h5>
                    <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                        <i class="fas fa-times text-white"></i>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <form id="topupForm" novalidate>
                        <input type="hidden" name="action" value="request_wallet_topup">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Top-up Amount (₹) <span class="text-danger">*</span></label>
                            <input type="number" name="amount" class="form-control" required placeholder="Enter amount to add">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Payment Method</label>
                            <select name="payment_method" class="form-select">
                                <option value="UPI">UPI / GPay / PhonePe</option>
                                <option value="Bank Transfer">Bank Transfer (IMPS/NEFT)</option>
                                <option value="Cash">Cash Deposit</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Payment Proof (Screenshot) <span class="text-danger">*</span></label>
                            <input type="file" name="proof_file" class="form-control" required>
                        </div>
                        <p class="text-muted xsmall mb-3">Please transfer the amount to the official bank account and upload the screenshot here.</p>
                        <button type="submit" class="btn btn-primary-theme w-100 rounded-pill mt-3">Submit Request</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Ticket Modal -->
    <div class="modal fade" id="newTicketModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header bg-primary py-3">
                    <h5 class="modal-title fw-bold text-white"><i class="fas fa-headset me-2"></i>Open Support Ticket</h5>
                    <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                        <i class="fas fa-times text-white"></i>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <form id="ticketForm" novalidate>
                        <input type="hidden" name="action" value="open_support_ticket">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Subject <span class="text-danger">*</span></label>
                            <input type="text" name="subject" class="form-control" required placeholder="What is the issue?">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Department</label>
                            <select name="department" class="form-select">
                                <option value="General">General Inquiry</option>
                                <option value="Accounts">Accounts & Wallet</option>
                                <option value="Technical">Technical Issue</option>
                                <option value="Academic">Academic Support</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Message <span class="text-danger">*</span></label>
                            <textarea name="message" class="form-control" rows="4" required placeholder="Describe your issue in detail..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary-theme w-100 rounded-pill mt-3">Submit Ticket</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Detail Modal -->
    <div class="modal fade" id="studentDetailModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header bg-primary py-3">
                    <h5 class="modal-title fw-bold text-white"><i class="fas fa-user-graduate me-2"></i>Student Full Profile</h5>
                    <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                        <i class="fas fa-times text-white"></i>
                    </button>
                </div>
                <div class="modal-body p-0" id="studentDetailBody">
                    <!-- Dynamic Content -->
                </div>
            </div>
        </div>
    </div>

    <?php include("../common/footer.php"); ?>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let admissionChart = null;

        function filterTable(inputId, tableBodyId) {
            const input = document.getElementById(inputId);
            const filter = input.value.toLowerCase();
            const tbody = document.getElementById(tableBodyId);
            const rows = tbody.getElementsByTagName('tr');

            for (let i = 0; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName('td');
                let found = false;
                for (let j = 0; j < cells.length; j++) {
                    if (cells[j].innerText.toLowerCase().indexOf(filter) > -1) {
                        found = true;
                        break;
                    }
                }
                rows[i].style.display = found ? '' : 'none';
            }
        }

        function renderAdmissionChart(stats) {
            const ctx = document.getElementById('admissionChart').getContext('2d');
            if (admissionChart) admissionChart.destroy();

            admissionChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Approved', 'Pending', 'Remaining'],
                    datasets: [{
                        data: [parseInt(stats.approved_students), parseInt(stats.pending_students), Math.max(0, parseInt(stats.total_students) - parseInt(stats.approved_students) - parseInt(stats.pending_students))],
                        backgroundColor: ['#0b933a', '#ff771d', '#e9ecef'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    },
                    cutout: '70%'
                }
            });
        }

        function filterTable(inputId, tableId) {
            const input = document.getElementById(inputId);
            const filter = input.value.toUpperCase();
            const table = document.getElementById(tableId);
            const tr = table.getElementsByTagName("tr");
            for (let i = 0; i < tr.length; i++) {
                const td = tr[i].getElementsByTagName("td");
                let found = false;
                for (let j = 0; j < td.length; j++) {
                    if (td[j].textContent.toUpperCase().indexOf(filter) > -1) {
                        found = true;
                        break;
                    }
                }
                tr[i].style.display = found ? "" : "none";
            }
        }

        function handleFormSubmit(formId, modalId) {
            const form = document.getElementById(formId);
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                if (!this.checkValidity()) {
                    this.classList.add('was-validated');
                    return;
                }

                const btn = this.querySelector('button[type="submit"]');
                const originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';

                const formData = new FormData(this);
                
                fetch('../ajax/franchise_handler.php', {
                    method: 'POST',
                    body: formData
                })
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        Swal.fire('Success', res.message, 'success');
                        const modal = bootstrap.Modal.getInstance(document.getElementById(modalId));
                        modal.hide();
                        form.reset();
                        form.classList.remove('was-validated');
                        initDashboard();
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                });
            });
        }

        function viewStudent(id) {
            const modal = new bootstrap.Modal(document.getElementById('studentDetailModal'));
            const body = document.getElementById('studentDetailBody');
            body.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-2">Fetching profile...</p></div>';
            modal.show();

            fetch(`../ajax/franchise_handler.php?action=get_student_detail&id=${id}`)
                .then(r => r.json())
                .then(res => {
                    if(res.success) {
                        const s = res.data;
                        body.innerHTML = `
                            <div class="p-4 bg-light border-bottom d-flex align-items-center">
                                <img src="../media/students/${s.photo || '../general/default-avatar.png'}" class="student-profile-img rounded-circle me-4 border shadow-sm">
                                <div>
                                    <h3 class="fw-bold mb-0 text-primary-theme">${s.full_name}</h3>
                                    <p class="text-muted mb-0">REG: #REG-${s.id.toString().padStart(5, '0')} | ${s.course_name}</p>
                                </div>
                                <div class="ms-auto text-end">
                                    <button class="btn btn-sm btn-outline-secondary rounded-pill" onclick="window.print()"><i class="fas fa-print me-1"></i>Print</button>
                                </div>
                            </div>
                            <div class="p-4">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <h6 class="fw-bold text-muted small text-uppercase">Personal Information</h6>
                                        <table class="table table-sm table-borderless small mt-2">
                                            <tr><td class="text-muted">Father\'s Name:</td><td class="fw-bold">${s.father_name}</td></tr>
                                            <tr><td class="text-muted">Mother\'s Name:</td><td class="fw-bold">${s.mother_name}</td></tr>
                                            <tr><td class="text-muted">Email Address:</td><td class="fw-bold text-primary">${s.email}</td></tr>
                                            <tr><td class="text-muted">Gender / DOB:</td><td class="fw-bold">${s.gender} / ${s.dob}</td></tr>
                                            <tr><td class="text-muted">Contact No:</td><td class="fw-bold">${s.phone}</td></tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="fw-bold text-muted small text-uppercase">Academic Details</h6>
                                        <table class="table table-sm table-borderless small mt-2">
                                            <tr><td class="text-muted">Course:</td><td class="fw-bold">${s.course_name}</td></tr>
                                            <tr><td class="text-muted">Duration:</td><td class="fw-bold">${s.duration} Months</td></tr>
                                            <tr><td class="text-muted">Center:</td><td class="fw-bold text-primary-theme">${s.center_name}</td></tr>
                                            <tr><td class="text-muted">Admission Date:</td><td class="fw-bold">${new Date(s.created_at).toLocaleDateString()}</td></tr>
                                        </table>
                                    </div>
                                    <div class="col-12 mt-3 p-3 bg-primary-light rounded-3">
                                        <h6 class="fw-bold text-primary-theme small mb-1">Office Remark</h6>
                                        <p class="small mb-0 text-muted">${s.remark || 'No official remarks available.'}</p>
                                    </div>
                                </div>
                            </div>
                        `;
                    } else {
                        body.innerHTML = `<div class="alert alert-danger m-4">${res.message}</div>`;
                    }
                });
        }

        function requestProfileUpdate() {
            const modal = new bootstrap.Modal(document.getElementById('newTicketModal'));
            const form = document.getElementById('ticketForm');
            // Pre-fill ticket for profile update
            form.querySelector('[name="subject"]').value = "Profile Update Request";
            form.querySelector('[name="department"]').value = "Technical";
            form.querySelector('[name="message"]').value = "I would like to request an update to our center information. Specifically regarding: ";
            modal.show();
        }

        function initDashboard() {
            fetch('../ajax/get_franchise_data.php')
                .then(r => r.json())
                .then(res => {
                    if (res.status === 'success') {
                        const f = res.franchise;
                        // Update Stats
                        document.getElementById('walletBalance').innerText = '₹ ' + parseFloat(f.wallet_balance).toLocaleString();
                        document.getElementById('centerCode').innerText = f.center_code;
                        const statusBadge = document.getElementById('centerStatus');
                        if (parseInt(f.status) === 1) {
                            statusBadge.innerText = 'ACTIVE';
                            statusBadge.className = 'badge bg-success';
                        } else {
                            statusBadge.innerText = 'INACTIVE';
                            statusBadge.className = 'badge bg-danger';
                        }
                        
                        document.getElementById('statTotalStudents').innerText = res.stats.total_students;
                        document.getElementById('statApprovedStudents').innerText = res.stats.approved_students;
                        document.getElementById('statPendingStudents').innerText = res.stats.pending_students;
                        
                        renderAdmissionChart(res.stats);
                                           // Update Sidebar Profile
                        try {
                            document.getElementById('sidebarDirectorName').innerText = f.director_name || 'N/A';
                            document.getElementById('sidebarCenterCode').innerText = 'CENTER #' + (f.center_code || '---');
                            document.getElementById('sidebarCenterName').innerText = f.center_name || 'N/A';
                            document.getElementById('sidebarMobile').innerText = f.director_mobile || 'N/A';
                            document.getElementById('sidebarEmail').innerText = f.email || 'N/A';
                            document.getElementById('sidebarEstd').innerText = f.estd_date || 'N/A';
                            if (f.director_photo) {
                                document.getElementById('directorPhoto').src = '../media/franchise/directors/' + f.director_photo;
                            }
                        } catch(e) { console.error("Sidebar update error", e); }
                        
                        // Update Notices
                        try {
                            const noticeList = document.getElementById('noticeList');
                            if (res.notices && res.notices.length > 0) {
                                noticeList.innerHTML = res.notices.map(n => `
                                    <div class="list-group-item px-0 py-3 border-0 border-bottom">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <h6 class="mb-0 fw-bold text-primary-theme">${n.subject || 'Notice'}</h6>
                                            <small class="text-muted">${n.created_at ? new Date(n.created_at).toLocaleDateString() : ''}</small>
                                        </div>
                                        <p class="mb-0 small text-muted">${n.message || ''}</p>
                                        ${n.attachment ? `<a href="../media/franchise/notices/${n.attachment}" target="_blank" class="btn btn-sm btn-link p-0 text-primary small mt-1"><i class="fas fa-paperclip me-1"></i>View Attachment</a>` : ''}
                                    </div>
                                `).join('');
                            } else {
                                noticeList.innerHTML = '<p class="text-muted small text-center py-4">No recent announcements.</p>';
                            }
                        } catch(e) { console.error("Notices update error", e); }

                        // Update Leads Table
                        try {
                            const leadsBody = document.getElementById('leadsTableBody');
                            if(res.leads && res.leads.length > 0) {
                                leadsBody.innerHTML = res.leads.map(l => `
                                    <tr>
                                        <td class="ps-4 small">${l.created_at ? new Date(l.created_at).toLocaleDateString() : 'N/A'}</td>
                                        <td><div class="fw-bold">${l.full_name || 'N/A'}</div><small class="text-muted">${l.mobile || ''}</small></td>
                                        <td><span class="badge bg-primary-light text-primary border">${(l.message || '').replace('Interested in: ', '') || 'General'}</span></td>
                                        <td><small>${l.prob_admission_date || 'Not scheduled'}</small></td>
                                        <td><span class="badge bg-warning-light text-warning border">${(l.approval_status || 'NEW').toUpperCase()}</span></td>
                                        <td class="pe-4 text-end">
                                            <button class="btn btn-sm btn-outline-primary rounded-pill"><i class="fas fa-edit"></i></button>
                                        </td>
                                    </tr>
                                `).join('');
                            }
                        } catch(e) { console.error("Leads update error", e); }

                        // Update Students Table
                        try {
                            const studentBody = document.getElementById('studentsTableBody');
                            if(res.students && res.students.length > 0) {
                                studentBody.innerHTML = res.students.map(s => `
                                    <tr>
                                        <td class="ps-4 small">#REG-${(s.id || 0).toString().padStart(5, '0')}</td>
                                        <td><div class="fw-bold text-primary-theme">${s.full_name || 'N/A'}</div><small class="text-muted">${s.mobile || ''}</small></td>
                                        <td><small>${s.course_name || 'N/A'}</small></td>
                                        <td><small>${s.created_at ? new Date(s.created_at).toLocaleDateString() : ''}</small></td>
                                        <td><span class="badge bg-success-light text-success border">Active</span></td>
                                        <td class="pe-4 text-end">
                                            <button class="btn btn-sm btn-outline-primary rounded-pill" onclick="viewStudent(${s.id})">View Profile</button>
                                        </td>
                                    </tr>
                                `).join('');
                            } else {
                                studentBody.innerHTML = '<tr><td colspan="6" class="text-center py-5 text-muted">No students found.</td></tr>';
                            }
                        } catch(e) { console.error("Students update error", e); }

                        // Update Admission Form Dropdowns
                        try {
                            const courseSelect = document.getElementById('admissionCourseSelect');
                            if (courseSelect) {
                                courseSelect.innerHTML = '<option value="">Choose Course...</option>' + res.courses.map(c => `
                                    <option value="${c.id}">${c.name} (${c.code})</option>
                                `).join('');
                            }

                            const sessionSelect = document.getElementById('admissionSessionSelect');
                            if (sessionSelect) {
                                sessionSelect.innerHTML = res.sessions.map(s => `
                                    <option value="${s.id}" ${s.is_active == 1 ? 'selected' : ''}>${s.session_name}</option>
                                `).join('');
                            }
                        } catch(e) { console.error("Admission dropdowns error", e); }

                        // Update Ledger Table
                        try {
                            const ledgerBody = document.getElementById('ledgerTableBody');
                            if(res.ledger && res.ledger.length > 0) {
                                ledgerBody.innerHTML = res.ledger.map(l => `
                                    <tr>
                                        <td class="ps-4 small">${l.created_at ? new Date(l.created_at).toLocaleDateString() : ''}</td>
                                        <td><code class="text-primary">TXN-${(l.id || 0).toString().padStart(6, '0')}</code></td>
                                        <td>${l.description || ''}</td>
                                        <td class="fw-bold ${(l.type || 'credit') == 'credit' ? 'text-success' : 'text-danger'}">
                                            ${(l.type || 'credit') == 'credit' ? '+' : '-'} ₹${parseFloat(l.amount || 0).toLocaleString()}
                                        </td>
                                        <td><span class="badge bg-success-light text-success border">Completed</span></td>
                                        <td class="pe-4 text-end">
                                            <button class="btn btn-sm btn-light rounded-pill"><i class="fas fa-download"></i></button>
                                        </td>
                                    </tr>
                                `).join('');
                            }
                        } catch(e) { console.error("Ledger update error", e); }

                        // Update Attendance Table
                        try {
                            const attendBody = document.getElementById('attendanceTableBody');
                            if(res.attendance && res.attendance.length > 0) {
                                attendBody.innerHTML = res.attendance.map(a => `
                                    <tr>
                                        <td class="ps-4 small">${a.check_in_time ? new Date(a.check_in_time).toLocaleString() : ''}</td>
                                        <td class="fw-bold">${a.student_name || 'N/A'}</td>
                                        <td><code>#REG-${(a.student_id || 0).toString().padStart(5, '0')}</code></td>
                                        <td><span class="badge bg-success-light text-success border">${(a.status || 'PRESENT').toUpperCase()}</span></td>
                                    </tr>
                                `).join('');
                            } else {
                                attendBody.innerHTML = '<tr><td colspan="4" class="text-center py-5 text-muted">No attendance logs found.</td></tr>';
                            }
                        } catch(e) { console.error("Attendance update error", e); }

                        // Update Vault Table
                        try {
                            const vaultBody = document.getElementById('vaultTableBody');
                            if(res.vault && res.vault.length > 0) {
                                vaultBody.innerHTML = res.vault.map(v => `
                                    <tr>
                                        <td class="ps-4"><i class="fas fa-file-alt text-primary me-2"></i>${v.title || 'Document'}</td>
                                        <td>${v.type || 'Other'}</td>
                                        <td><small>${v.created_at ? new Date(v.created_at).toLocaleDateString() : ''}</small></td>
                                        <td class="pe-4 text-end"><span class="badge bg-success">Stored</span></td>
                                    </tr>
                                `).join('');
                            }
                        } catch(e) { console.error("Vault update error", e); }

                        // Update Support Tickets Table
                        try {
                            const ticketsBody = document.getElementById('ticketsTableBody');
                            if(res.tickets && res.tickets.length > 0) {
                                ticketsBody.innerHTML = res.tickets.map(t => `
                                    <tr>
                                        <td class="ps-4"><code>#${t.id}</code></td>
                                        <td>${t.subject || 'Support Request'}</td>
                                        <td>${t.department || 'General'}</td>
                                        <td><small>${t.created_at ? new Date(t.created_at).toLocaleDateString() : ''}</small></td>
                                        <td class="pe-4 text-end">
                                            <span class="badge bg-${(t.status || 'open') === 'open' ? 'warning' : 'success'}">${(t.status || 'OPEN').toUpperCase()}</span>
                                        </td>
                                    </tr>
                                `).join('');
                            }
                        } catch(e) { console.error("Tickets update error", e); }

                        // Update Pending Wallet Requests
                        try {
                            const pendingRequestsBody = document.getElementById('pendingRequestsBody');
                            const pendingSection = document.getElementById('pendingRequestsSection');
                            if(res.wallet_requests && res.wallet_requests.length > 0) {
                                if (pendingSection) pendingSection.classList.remove('d-none');
                                if (pendingRequestsBody) {
                                    pendingRequestsBody.innerHTML = res.wallet_requests.map(w => `
                                        <tr>
                                            <td class="ps-4 small">${w.created_at ? new Date(w.created_at).toLocaleDateString() : ''}</td>
                                            <td class="fw-bold">₹${parseFloat(w.amount || 0).toLocaleString()}</td>
                                            <td>${w.payment_method || ''}</td>
                                            <td class="pe-4 text-end"><span class="badge bg-warning">PENDING</span></td>
                                        </tr>
                                    `).join('');
                                }
                            } else {
                                if (pendingSection) pendingSection.classList.add('d-none');
                            }
                        } catch(e) { console.error("Wallet requests error", e); }

                        // Full Center View
                        try {
                            const centerView = document.getElementById('fullCenterContent');
                            if (centerView) {
                                centerView.innerHTML = `
                                    <div class="row g-4">
                                        <div class="col-12 border-bottom pb-4 mb-4 d-flex justify-content-between align-items-center">
                                            <div>
                                                <h4 class="fw-bold text-primary-theme mb-1">Official Center Information</h4>
                                                <p class="text-muted small">Center Registration ID: ${f.id}</p>
                                            </div>
                                            <button onclick="requestProfileUpdate()" class="btn btn-outline-primary rounded-pill px-4"><i class="fas fa-edit me-2"></i>Request Profile Update</button>
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <div class="label-pill">CENTER IDENTIFICATION</div>
                                            <table class="table table-sm table-borderless mt-2">
                                                <tr><td class="text-muted">Center Name:</td><td class="fw-bold">${f.center_name || 'N/A'}</td></tr>
                                                <tr><td class="text-muted">Center Code:</td><td class="fw-bold text-primary-theme">${f.center_code || '---'}</td></tr>
                                                <tr><td class="text-muted">Established:</td><td class="fw-bold">${f.estd_date || 'N/A'}</td></tr>
                                                <tr><td class="text-muted">Status:</td><td><span class="badge bg-success">ACTIVE</span></td></tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <div class="label-pill">DIRECTOR PROFILE</div>
                                            <table class="table table-sm table-borderless mt-2">
                                                <tr><td class="text-muted">Full Name:</td><td class="fw-bold">${f.director_name || 'N/A'}</td></tr>
                                                <tr><td class="text-muted">Mobile:</td><td class="fw-bold">${f.director_mobile || 'N/A'}</td></tr>
                                                <tr><td class="text-muted">Aadhar:</td><td class="fw-bold">${f.aadhar_no || 'Not Verified'}</td></tr>
                                                <tr><td class="text-muted">Email:</td><td class="fw-bold">${f.email || 'N/A'}</td></tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <div class="label-pill">GEOGRAPHIC INFO</div>
                                            <table class="table table-sm table-borderless mt-2">
                                                <tr><td class="text-muted">Address:</td><td class="fw-bold">${f.address || 'N/A'}</td></tr>
                                                <tr><td class="text-muted">City:</td><td class="fw-bold">${f.city_name || 'N/A'}</td></tr>
                                                <tr><td class="text-muted">District:</td><td class="fw-bold">${f.district_name || 'N/A'}</td></tr>
                                                <tr><td class="text-muted">State / Pin:</td><td class="fw-bold">${f.state_name || 'N/A'} - ${f.pincode || ''}</td></tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <div class="label-pill">INFRASTRUCTURE</div>
                                            <table class="table table-sm table-borderless mt-2">
                                                <tr><td class="text-muted">Computers:</td><td class="fw-bold">${f.computers || 0} Nodes</td></tr>
                                                <tr><td class="text-muted">Area:</td><td class="fw-bold">${f.area_sqft || 0} Sq. Ft.</td></tr>
                                                <tr><td class="text-muted">Internet:</td><td class="fw-bold">${f.internet_type || 'N/A'}</td></tr>
                                                <tr><td class="text-muted">Documents:</td><td class="fw-bold">
                                                    <span class="badge bg-success small"><i class="fas fa-check"></i> All Verified</span>
                                                </td></tr>
                                            </table>
                                        </div>
                                        <div class="col-12 mt-4">
                                            <div class="label-pill mb-3">CENTER PHOTOS</div>
                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <div class="border rounded-3 p-2 bg-light text-center">
                                                        <img src="../media/franchise/centers/${f.photo_front || 'default.png'}" class="img-fluid rounded-2 mb-2 shadow-sm" style="height: 150px; width: 100%; object-fit: cover;">
                                                        <small class="fw-bold text-muted">Front View</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="border rounded-3 p-2 bg-light text-center">
                                                        <img src="../media/franchise/centers/${f.photo_lab || 'default.png'}" class="img-fluid rounded-2 mb-2 shadow-sm" style="height: 150px; width: 100%; object-fit: cover;">
                                                        <small class="fw-bold text-muted">Computer Lab</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="border rounded-3 p-2 bg-light text-center">
                                                        <img src="../media/franchise/centers/${f.photo_office || 'default.png'}" class="img-fluid rounded-2 mb-2 shadow-sm" style="height: 150px; width: 100%; object-fit: cover;">
                                                        <small class="fw-bold text-muted">Office/Reception</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 mt-5 text-center">
                                            <button onclick="window.print()" class="btn btn-light rounded-pill px-4 me-2"><i class="fas fa-print me-2"></i>Print Profile</button>
                                            <a href="franchise-application.php" class="btn btn-primary-theme rounded-pill px-4">Update Details</a>
                                        </div>
                                    </div>
                                `;
                            }
                        } catch(e) { console.error("Center view error", e); }

                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                });
        }

        // Adjust DataTables columns on tab change
        document.querySelectorAll('button[data-bs-toggle="pill"]').forEach(tabEl => {
            tabEl.addEventListener('shown.bs.tab', () => {
                $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
            });
        });

        document.addEventListener('DOMContentLoaded', () => {
            initDashboard();
            handleFormSubmit('leadForm', 'addLeadModal');
            handleFormSubmit('topupForm', 'topupModal');
            handleFormSubmit('ticketForm', 'newTicketModal');

            // Tab Persistence logic
            const activeTab = localStorage.getItem('franchiseActiveTab');
            if (activeTab) {
                const tabEl = document.querySelector(`[href="${activeTab}"]`);
                if (tabEl) {
                    const tab = new bootstrap.Tab(tabEl);
                    tab.show();
                }
            }

            // Save active tab on change
            document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tabEl => {
                tabEl.addEventListener('shown.bs.tab', (e) => {
                    localStorage.setItem('franchiseActiveTab', e.target.getAttribute('href'));
                });
            });

            // Header Search Integration
            const headerSearch = document.querySelector('.search-bar input');
            if (headerSearch) {
                headerSearch.addEventListener('keyup', (e) => {
                    const val = e.target.value;
                    const studentTab = document.getElementById('students-tab');
                    if (studentTab) {
                        const tab = new bootstrap.Tab(studentTab);
                        tab.show();
                        const studentSearch = document.getElementById('studentSearchInput');
                        studentSearch.value = val;
                        filterTable('studentSearchInput', 'studentsTableBody');
                    }
                });
            }
... (remaining handleFormSubmit logic) ...

            // Handle Direct Admission Form (Integrated)
            const admissionForm = document.getElementById('direct-admission-form');
            if (admissionForm) {
                admissionForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const btn = this.querySelector('button[type="submit"]');
                    const originalText = btn.innerHTML;
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing Admission...';

                    const formData = new FormData(this);
                    formData.append('action', 'direct_admission');

                    fetch('../ajax/admission_handler.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(r => r.json())
                    .then(res => {
                        if (res.status === 'success') {
                            Swal.fire('Success', 'Student admitted successfully! Refreshing dashboard...', 'success');
                            this.reset();
                            document.getElementById('overview-tab').click();
                            initDashboard();
                        } else {
                            Swal.fire('Error', res.message, 'error');
                        }
                    })
                    .catch(err => Swal.fire('Error', 'Connection error. Please try again.', 'error'))
                    .finally(() => {
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                    });
                });
            }
        });
    </script>
</body>
</html>
