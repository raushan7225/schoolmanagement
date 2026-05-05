<?php
require_once('../common/config.php');
checkRole('student');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Student Portal - NEBOOSASE</title>
    <?php include("../common/meta.php"); ?>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <!-- Dynamic Theme Colors from General Settings -->
    <link id="dynamic-theme-css" rel="stylesheet" href="<?php echo BASE_URL; ?>theme.php">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/student.css">
</head>
<body class="bg-light">
    <?php include("../common/header.php"); ?>
    
    <div class="container py-5">
        <div class="row mb-4 align-items-center">
            <div class="col-md-6">
                <h1 class="display-6 fw-bold text-primary-theme">My Portal</h1>
                <p class="text-muted">Welcome back, <?php echo $_SESSION['username']; ?></p>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="online-student-admission.php" class="btn btn-primary-theme rounded-pill px-4 shadow-sm">
                    <i class="fas fa-edit me-2"></i>Update Application
                </a>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <ul class="nav dashboard-nav mb-4 bg-white shadow-sm rounded-4 px-3" id="pills-tab" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" id="overview-tab" data-bs-toggle="pill" data-bs-target="#overview">
                    <i class="fas fa-th-large me-2"></i>Overview
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="exam-tab" data-bs-toggle="pill" data-bs-target="#exams">
                    <i class="fas fa-laptop-code me-2"></i>Online Exams
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="class-tab" data-bs-toggle="pill" data-bs-target="#classes">
                    <i class="fas fa-video me-2"></i>Online Classes
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="attendance-tab" data-bs-toggle="pill" data-bs-target="#attendance">
                    <i class="fas fa-qrcode me-2"></i>Attendance
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="admission-tab" data-bs-toggle="pill" data-bs-target="#admission-view">
                    <i class="fas fa-file-invoice me-2"></i>My Admission Form
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="enquiry-tab" data-bs-toggle="pill" data-bs-target="#enquiry-list">
                    <i class="fas fa-comments me-2"></i>Enquiry History
                </button>
            </li>
        </ul>

        <div class="tab-content" id="pills-tabContent">
            <!-- Tab 1: Overview -->
            <div class="tab-pane fade show active" id="overview">
                <div id="statusAlert"></div>
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4" style="background: linear-gradient(135deg, #1b1260, #2c2185); color: white;">
                            <div id="admissionSummary">
                                <div class="spinner-border text-light" role="status"></div>
                            </div>
                        </div>
                        
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm rounded-4 p-4 text-center">
                                    <div class="display-6 mb-2 text-primary-theme"><i class="fas fa-id-badge"></i></div>
                                    <h6 class="fw-bold">ID Card</h6>
                                    <p class="small text-muted mb-3">Your official digital identity</p>
                                    <button class="btn btn-outline-primary btn-sm rounded-pill w-100 disabled" id="idCardBtn">Not Available</button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm rounded-4 p-4 text-center">
                                    <div class="display-6 mb-2 text-primary-theme"><i class="fas fa-id-card"></i></div>
                                    <h6 class="fw-bold">Admit Card</h6>
                                    <p class="small text-muted mb-3">Download your exam permit</p>
                                    <button class="btn btn-outline-primary btn-sm rounded-pill w-100 disabled" id="admitBtn">Not Available</button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm rounded-4 p-4 text-center">
                                    <div class="display-6 mb-2 text-primary-theme"><i class="fas fa-file-alt"></i></div>
                                    <h6 class="fw-bold">Marksheet</h6>
                                    <p class="small text-muted mb-3">Check your academic results</p>
                                    <button class="btn btn-outline-primary btn-sm rounded-pill w-100 disabled" id="marksBtn">Not Available</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                            <h5 class="fw-bold mb-4">Profile Summary</h5>
                            <div class="text-center mb-4">
                                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                    <i class="fas fa-user-circle fs-1 text-primary-theme"></i>
                                </div>
                                <h5 class="mt-3 mb-0 fw-bold"><?php echo $_SESSION['username']; ?></h5>
                                <div id="profileRollNo" class="mt-1">
                                    <span class="badge bg-primary-theme opacity-75 small">Loading Roll No...</span>
                                </div>
                            </div>
                            <hr>
                            <ul class="list-unstyled small mb-0">
                                <li class="mb-2 d-flex justify-content-between">
                                    <span class="text-muted">Username:</span>
                                    <span class="fw-bold"><?php echo $_SESSION['username']; ?></span>
                                </li>
                                <li class="mb-2 d-flex justify-content-between">
                                    <span class="text-muted">Portal Role:</span>
                                    <span class="fw-bold">Student</span>
                                </li>
                                <li class="d-flex justify-content-between">
                                    <span class="text-muted">Last Activity:</span>
                                    <span class="fw-bold"><?php echo date('d M, Y'); ?></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab: Online Exams -->
            <div class="tab-pane fade" id="exams">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Scheduled Online Exams</h5>
                        <span class="badge bg-primary-light text-primary rounded-pill">CBT Module</span>
                    </div>
                    <div id="examContent">
                        <div class="text-center py-5"><div class="spinner-border text-primary-theme" role="status"></div></div>
                    </div>
                </div>
            </div>

            <!-- Tab: Online Classes -->
            <div class="tab-pane fade" id="classes">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Online Learning Center</h5>
                        <span class="badge bg-danger-light text-danger rounded-pill">Live & Recorded</span>
                    </div>
                    <div id="classContent">
                        <div class="text-center py-5"><div class="spinner-border text-primary-theme" role="status"></div></div>
                    </div>
                </div>
            </div>

            <!-- Tab: Attendance -->
            <div class="tab-pane fade" id="attendance">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">My Attendance Log</h5>
                        <button class="btn btn-primary-theme btn-sm rounded-pill px-3" onclick="alert('Please show your Student ID Card QR at the center scanner.')">
                            <i class="fas fa-qrcode me-2"></i>My ID QR
                        </button>
                    </div>
                    <div id="attendanceContent">
                        <div class="text-center py-5"><div class="spinner-border text-primary-theme" role="status"></div></div>
                    </div>
                </div>
            </div>

            <!-- Tab 2: Admission Form View -->
            <div class="tab-pane fade" id="admission-view">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
                    <div id="fullAdmissionContent">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary-theme" role="status"></div>
                            <p class="mt-3 text-muted">Loading your application details...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 3: Enquiry List -->
            <div class="tab-pane fade" id="enquiry-list">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                    <div class="card-header bg-white p-4 border-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0 text-white">Enquiry History</h5>
                            <a href="online-student-enquiry.php" class="btn btn-sm btn-outline-primary rounded-pill">New Enquiry</a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table data-table mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">S.No.</th>
                                    <th>Date</th>
                                    <th>Subject / Course</th>
                                    <th>Message</th>
                                    <th>Status</th>
                                    <th class="pe-4 text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody id="enquiryTableBody">
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer bg-white border-0 p-3">
                        <nav id="enquiryPagination" class="d-flex justify-content-center"></nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include("../common/footer.php"); ?>
    <?php include("../common/requirejs.php"); ?>

    <script>
        let allEnquiries = [];
        const ENQ_PAGE_SIZE = 20;

        function viewEnquiryDetails(index) {
            const e = allEnquiries[index];
            if(!e) return;
            
            let html = `
                <div class="row g-3">
                    <div class="col-md-6"><small class="text-muted d-block">Student Name</small> <strong>${e.full_name}</strong></div>
                    <div class="col-md-6"><small class="text-muted d-block">Phone / Email</small> <strong>${e.mobile} / ${e.email || 'N/A'}</strong></div>
                    <div class="col-md-6"><small class="text-muted d-block">Course</small> <strong>${e.course_name || 'General Enquiry'}</strong></div>
                    <div class="col-md-6"><small class="text-muted d-block">Enquiry Date</small> <strong>${new Date(e.created_at).toLocaleString()}</strong></div>
                    <hr>
                    <div class="col-md-4"><small class="text-muted d-block">Date of Birth</small> <strong>${e.dob || 'N/A'}</strong></div>
                    <div class="col-md-4"><small class="text-muted d-block">Gender</small> <strong class="text-capitalize">${e.gender || 'N/A'}</strong></div>
                    <div class="col-md-4"><small class="text-muted d-block">Qualification</small> <strong>${e.qualification || 'N/A'}</strong></div>
                    <div class="col-md-12"><small class="text-muted d-block">Address</small> <strong>${e.address || 'N/A'}</strong></div>
                    <div class="col-md-12 bg-light p-3 rounded-3 mt-3">
                        <small class="text-muted d-block mb-1">Message / Query</small>
                        <p class="mb-0 text-dark">${e.message || 'No message provided.'}</p>
                    </div>
                </div>
            `;
            document.getElementById('enqDetailContent').innerHTML = html;
            new bootstrap.Modal(document.getElementById('enquiryDetailModal')).show();
        }

        function renderEnquiries(page = 1) {
            const start = (page - 1) * ENQ_PAGE_SIZE;
            const end = start + ENQ_PAGE_SIZE;
            const paginated = allEnquiries.slice(start, end);
            
            const enqTable = document.getElementById('enquiryTableBody');
            const pagContainer = document.getElementById('enquiryPagination');
            
            if(allEnquiries.length > 0) {
                enqTable.innerHTML = paginated.map((e, idx) => `
                    <tr>
                        <td class="ps-4">${start + idx + 1}</td>
                        <td>${new Date(e.created_at).toLocaleDateString()}</td>
                        <td class="fw-bold">${e.course_name || 'General Query'}</td>
                        <td><span class="text-muted small">${(e.message || '').substring(0, 40)}...</span></td>
                        <td><span class="badge bg-light text-dark border">${e.approval_status.toUpperCase()}</span></td>
                        <td class="pe-4 text-end">
                            <button class="btn btn-sm btn-outline-primary rounded-pill" onclick="viewEnquiryDetails(${start + idx})">
                                <i class="fas fa-eye me-1"></i> View
                            </button>
                        </td>
                    </tr>
                `).join('');

                // Render Pagination Buttons
                const totalPages = Math.ceil(allEnquiries.length / ENQ_PAGE_SIZE);
                if(totalPages > 1) {
                    let html = `<ul class="pagination pagination-sm mb-0">`;
                    for(let i=1; i<=totalPages; i++) {
                        html += `<li class="page-item ${i === page ? 'active' : ''}"><a class="page-link" href="javascript:void(0)" onclick="renderEnquiries(${i})">${i}</a></li>`;
                    }
                    html += `</ul>`;
                    pagContainer.innerHTML = html;
                } else {
                    pagContainer.innerHTML = '';
                }
            } else {
                enqTable.innerHTML = `<tr><td colspan="5" class="text-center py-5 text-muted">No enquiry history found.</td></tr>`;
                pagContainer.innerHTML = '';
            }
        }

        function initDashboard() {
            fetch('../ajax/get_student_data.php')
            .then(r => r.json())
            .then(res => {
                if(res.status === 'success') {
                    // 1. Overview Summary
                    const summary = document.getElementById('admissionSummary');
                    if(res.admission) {
                        summary.innerHTML = `
                            <div class="row align-items-center">
                                <div class="col-md-9">
                                    <h6 class="opacity-75 text-white text-uppercase small ls-1 mb-2">Enrollment Active</h6>
                                    <h2 class="fw-bold text-white mb-3">${res.admission.course_name}</h2>
                                    <div class="d-flex flex-wrap gap-4">
                                        <div><small class="opacity-75 d-block">Roll Number</small> <strong>${res.admission.roll_number || 'PENDING'}</strong></div>
                                        <div><small class="opacity-75 d-block">Center</small> <strong>${res.admission.center_name}</strong></div>
                                        <div><small class="opacity-75 d-block">Status</small> <span class="badge bg-success">${res.admission.approval_status.toUpperCase()}</span></div>
                                    </div>
                                </div>
                                <div class="col-md-3 text-md-end mt-4 mt-md-0">
                                    <i class="fas fa-award display-3 opacity-25"></i>
                                </div>
                            </div>`;

                        // Update Profile Roll No
                        document.getElementById('profileRollNo').innerHTML = `<span class="badge bg-primary-theme opacity-75 small">Roll: ${res.admission.roll_number || 'PENDING'}</span>`;
                        
                        // 2. Full Admission View
                        const fullView = document.getElementById('fullAdmissionContent');
                        const photoUrl = res.admission.photo ? `../media/students/${res.admission.photo}` : '../assets/img/default-avatar.png';
                        const sigUrl = res.admission.signature ? `../media/students/${res.admission.signature}` : null;
                        
                        fullView.innerHTML = `
                            <div class="admission-form-preview p-4 p-md-5" style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333;">
                                <div class="d-flex justify-content-between align-items-start mb-4 border-bottom pb-4">
                                    <div class="header-section">
                                        <h2 style="color: #ff9800; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px;">Admission Form</h2>
                                        <p class="text-muted small mb-0">Official Student Profile | Ref: ADM-${res.admission.id}</p>
                                    </div>
                                    <div class="action-section text-end">
                                        <a href="print-admission.php" target="_blank" class="btn btn-sm btn-outline-warning rounded-pill px-3 mb-2">
                                            <i class="fas fa-print me-1"></i> Download / Print Form
                                        </a>
                                        <div class="mt-1">
                                            <span class="badge bg-success-light text-success border border-success rounded-pill px-3 py-1 small">
                                                <i class="fas fa-check-circle me-1"></i> ${res.admission.approval_status.toUpperCase()}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-9">
                                        <div class="row g-3">
                                            <div class="col-md-3">
                                                <small class="text-muted d-block">Date</small>
                                                <div style="border-bottom: 1px dotted #999; padding-bottom: 2px;">${new Date(res.admission.admission_date).toLocaleDateString()}</div>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted d-block">Roll Number</small>
                                                <div style="border-bottom: 1px dotted #999; padding-bottom: 2px;">${res.admission.roll_number || 'PENDING'}</div>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted d-block">Session</small>
                                                <div style="border-bottom: 1px dotted #999; padding-bottom: 2px;">${res.admission.session_name || '2024-25'}</div>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted d-block">SL NO</small>
                                                <div style="border-bottom: 1px dotted #999; padding-bottom: 2px;">${res.admission.id}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 text-center">
                                        <div style="width: 100px; height: 120px; border: 1px dashed #ff9800; margin: 0 auto; overflow: hidden; background: #fff;">
                                            <img src="${photoUrl}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='../assets/img/default-avatar.png'">
                                        </div>
                                        <small class="text-muted mt-1 d-block small">STUDENT PHOTO</small>
                                    </div>
                                </div>

                                <h5 style="color: #ff9800; font-weight: bold; margin-bottom: 15px; border-left: 4px solid #ff9800; padding-left: 10px; font-size: 16px;">Personal Info:</h5>
                                <div class="row g-3 mb-4">
                                    <div class="col-12">
                                        <div class="d-flex align-items-baseline">
                                            <span style="min-width: 160px; font-weight: 500;">Full Name (English):</span>
                                            <span style="flex-grow: 1; border-bottom: 1px dotted #999; padding-left: 10px; font-weight: bold;">${res.admission.full_name.toUpperCase()}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-baseline">
                                            <span style="min-width: 160px; font-weight: 500;">Date Of Birth:</span>
                                            <span style="flex-grow: 1; border-bottom: 1px dotted #999; padding-left: 10px;">${res.admission.dob}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-baseline">
                                            <span style="min-width: 100px; font-weight: 500;">Phone No:</span>
                                            <span style="flex-grow: 1; border-bottom: 1px dotted #999; padding-left: 10px;">+91 ${res.admission.mobile}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-center">
                                            <span style="min-width: 100px; font-weight: 500;">Gender:</span>
                                            <span class="ms-2 px-2 py-1 bg-light border rounded small text-uppercase">${res.admission.gender}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-center">
                                            <span style="min-width: 100px; font-weight: 500;">Religion:</span>
                                            <span class="ms-2 px-2 py-1 bg-light border rounded small text-uppercase">${res.admission.religion || 'N/A'}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-center">
                                            <span style="min-width: 100px; font-weight: 500;">Category:</span>
                                            <span class="ms-2 px-2 py-1 bg-light border rounded small text-uppercase">${res.admission.caste || 'GENERAL'}</span>
                                        </div>
                                    </div>
                                </div>

                                <h5 style="color: #ff9800; font-weight: bold; margin-bottom: 15px; border-left: 4px solid #ff9800; padding-left: 10px; font-size: 16px;">Family Details:</h5>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-baseline">
                                            <span style="min-width: 160px; font-weight: 500;">Father's Name:</span>
                                            <span style="flex-grow: 1; border-bottom: 1px dotted #999; padding-left: 10px;">${res.admission.father_name.toUpperCase()}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-baseline">
                                            <span style="min-width: 160px; font-weight: 500;">Mother's Name:</span>
                                            <span style="flex-grow: 1; border-bottom: 1px dotted #999; padding-left: 10px;">${res.admission.mother_name.toUpperCase()}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-baseline">
                                            <span style="min-width: 160px; font-weight: 500;">Guardian Contact:</span>
                                            <span style="flex-grow: 1; border-bottom: 1px dotted #999; padding-left: 10px;">${res.admission.guardian_phone || 'N/A'}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-baseline">
                                            <span style="min-width: 100px; font-weight: 500;">Email Id:</span>
                                            <span style="flex-grow: 1; border-bottom: 1px dotted #999; padding-left: 10px;">${res.admission.email}</span>
                                        </div>
                                    </div>
                                </div>

                                <h5 style="color: #ff9800; font-weight: bold; margin-bottom: 15px; border-left: 4px solid #ff9800; padding-left: 10px; font-size: 16px;">Location & Education:</h5>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-baseline">
                                            <span style="min-width: 160px; font-weight: 500;">Division/State:</span>
                                            <span style="flex-grow: 1; border-bottom: 1px dotted #999; padding-left: 10px;">${res.admission.state_name || 'N/A'}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-baseline">
                                            <span style="min-width: 100px; font-weight: 500;">District:</span>
                                            <span style="flex-grow: 1; border-bottom: 1px dotted #999; padding-left: 10px;">${res.admission.district_name || 'N/A'}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="d-flex align-items-baseline">
                                            <span style="min-width: 160px; font-weight: 500;">Full Address:</span>
                                            <span style="flex-grow: 1; border-bottom: 1px dotted #999; padding-left: 10px;">${res.admission.address}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-baseline">
                                            <span style="min-width: 100px; font-weight: 500;">Pin Code:</span>
                                            <span style="flex-grow: 1; border-bottom: 1px dotted #999; padding-left: 10px;">${res.admission.pincode}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-baseline">
                                            <span style="min-width: 160px; font-weight: 500;">Qualification:</span>
                                            <span style="flex-grow: 1; border-bottom: 1px dotted #999; padding-left: 10px;">${res.admission.qualification}</span>
                                        </div>
                                    </div>
                                </div>

                                <h5 style="color: #ff9800; font-weight: bold; margin-bottom: 15px; border-left: 4px solid #ff9800; padding-left: 10px; font-size: 16px;">Courses & Services Info:</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered mb-0" style="border-color: #ff9800;">
                                        <thead style="background: #fff5e6;">
                                            <tr>
                                                <th class="text-center" style="color: #e67e22; width: 80px;">SL NO</th>
                                                <th style="color: #e67e22;">Course Name</th>
                                                <th class="text-center" style="color: #e67e22;">Center</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="text-center">1</td>
                                                <td class="fw-bold">${res.admission.course_name}</td>
                                                <td class="text-center">${res.admission.center_name}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="row mt-5">
                                    <div class="col-md-6 text-center">
                                        ${sigUrl ? `
                                            <div class="p-2 border rounded-3 bg-white d-inline-block mb-2" style="border-style: dashed !important;">
                                                <img src="${sigUrl}" style="max-height: 50px;" alt="Signature">
                                            </div>
                                            <div class="small text-muted">STUDENT SIGNATURE</div>
                                        ` : ''}
                                    </div>
                                    <div class="col-md-6 text-center d-flex align-items-end justify-content-center">
                                        <div style="border-top: 1px dashed #999; width: 150px; padding-top: 5px;" class="small text-muted">OFFICIAL SEAL / SIGN</div>
                                    </div>
                                </div>
                            </div>
                        `;
                        
                        // 3. Document Buttons (ID Card / Admit Card / Marksheet)
                        const idCardBtn = document.getElementById('idCardBtn');
                        const admitBtn = document.getElementById('admitBtn');
                        const marksBtn = document.getElementById('marksBtn');
                        
                        // ID Card is available if approved and has roll number
                        if (res.admission.approval_status === 'approved' && res.admission.roll_number) {
                            idCardBtn.classList.remove('disabled', 'btn-outline-primary');
                            idCardBtn.classList.add('btn-primary-theme');
                            idCardBtn.innerText = 'View ID Card';
                            idCardBtn.onclick = () => window.location.href = 'id-card.php';
                        }

                        // Check for issued admit card
                        const hasAdmitCard = res.issued_docs.some(d => d.document_type === 'admit_card');
                        if (hasAdmitCard) {
                            admitBtn.classList.remove('disabled', 'btn-outline-primary');
                            admitBtn.classList.add('btn-primary-theme');
                            admitBtn.innerText = 'View Permit';
                            admitBtn.onclick = () => window.location.href = 'admit-card.php';
                        }

                        // Check for marks
                        if (res.marks.length > 0) {
                            marksBtn.classList.remove('disabled', 'btn-outline-primary');
                            marksBtn.classList.add('btn-primary-theme');
                            marksBtn.innerText = 'View Result';
                            marksBtn.onclick = () => window.location.href = 'marksheet.php';
                        }
                    } else {
                        summary.innerHTML = `<h5 class="fw-bold mb-0">No active enrollment found. <a href="online-student-admission.php" class="text-white text-decoration-underline">Apply Now</a></h5>`;
                        document.getElementById('profileRollNo').innerHTML = `<span class="badge bg-danger small">No Roll No</span>`;
                        document.getElementById('fullAdmissionContent').innerHTML = `
                            <div class="text-center py-5">
                                <i class="fas fa-folder-open display-1 text-light mb-4"></i>
                                <h5>No Application Data</h5>
                                <p class="text-muted">You haven't submitted your admission form yet.</p>
                                <a href="online-student-admission.php" class="btn btn-primary-theme rounded-pill px-4">Fill Admission Form</a>
                            </div>`;
                    }

                    // 3. Exams
                    const examBox = document.getElementById('examContent');
                    if(res.exams.length > 0) {
                        examBox.innerHTML = `<div class="row g-3">` + res.exams.map(e => `
                            <div class="col-md-6">
                                <div class="card border shadow-none rounded-4 p-3 h-100 bg-light">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="badge bg-primary rounded-pill px-2 small">${e.duration_mins} Mins</span>
                                        <span class="text-muted small"><i class="far fa-calendar-alt me-1"></i> ${new Date(e.start_datetime).toLocaleDateString()}</span>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-2">${e.title}</h6>
                                    <p class="small text-muted mb-3">Passing Marks: ${e.pass_percentage}%</p>
                                    <a href="take-exam.php?id=${e.id}" class="btn btn-sm btn-primary-theme rounded-pill w-100">Start Computer Test</a>
                                </div>
                            </div>
                        `).join('') + `</div>`;
                    } else {
                        examBox.innerHTML = `<div class="text-center py-4 text-muted small">No exams scheduled for your course yet.</div>`;
                    }

                    // 4. Classes
                    const classBox = document.getElementById('classContent');
                    if(res.classes.length > 0) {
                        classBox.innerHTML = `<div class="row g-3">` + res.classes.map(cl => `
                            <div class="col-md-6">
                                <div class="card border shadow-none rounded-4 p-3 h-100">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="avatar-sm rounded bg-${cl.class_type === 'live' ? 'danger' : 'info'}-light text-${cl.class_type === 'live' ? 'danger' : 'info'} d-flex align-items-center justify-content-center me-3" style="width:45px; height:45px;">
                                            <i class="fas ${cl.class_type === 'live' ? 'fa-broadcast-tower' : 'fa-play-circle'} fa-lg"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-0">${cl.title}</h6>
                                            <small class="text-muted">${cl.class_type.toUpperCase()} SESSION</small>
                                        </div>
                                    </div>
                                    <a href="${cl.class_type === 'live' ? cl.live_link : 'view-class.php?id='+cl.id}" target="${cl.class_type === 'live' ? '_blank' : '_self'}" class="btn btn-sm btn-outline-primary rounded-pill w-100">
                                        ${cl.class_type === 'live' ? 'Join Live Class' : 'Watch Recording'}
                                    </a>
                                </div>
                            </div>
                        `).join('') + `</div>`;
                    } else {
                        classBox.innerHTML = `<div class="text-center py-4 text-muted small">No online classes available for your course yet.</div>`;
                    }

                    // 5. Attendance
                    const attendBox = document.getElementById('attendanceContent');
                    if(res.attendance.length > 0) {
                        attendBox.innerHTML = `
                            <div class="table-responsive">
                                <table class="table table-sm small">
                                    <thead><tr><th>Date</th><th>Center</th><th>Time</th><th>Status</th></tr></thead>
                                    <tbody>
                                        ${res.attendance.map(a => `
                                            <tr>
                                                <td>${new Date(a.check_in_time).toLocaleDateString()}</td>
                                                <td>${a.center_name || 'N/A'}</td>
                                                <td>${new Date(a.check_in_time).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</td>
                                                <td><span class="badge bg-success-light text-success border border-success rounded-pill px-2">PRESENT</span></td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                            </div>`;
                    } else {
                        attendBox.innerHTML = `<div class="text-center py-4 text-muted small">No attendance records found.</div>`;
                    }

                    // 6. Enquiry Table
                    allEnquiries = res.enquiries || [];
                    renderEnquiries(1);
                }
            });
        }

        document.addEventListener('DOMContentLoaded', initDashboard);
    </script>
    <!-- Enquiry Detail Modal -->
    <div class="modal fade" id="enquiryDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-primary-theme text-white border-0 p-4">
                    <h5 class="modal-title fw-bold"><i class="fas fa-info-circle me-2"></i>Enquiry Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 p-md-5" id="enqDetailContent">
                    <!-- Dynamic Content -->
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
