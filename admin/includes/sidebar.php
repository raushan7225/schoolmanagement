<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">

        <li class="nav-heading">Main</li>

        <li class="nav-item">
            <a class="nav-link " href="<?php echo ADMIN_BASE_URL; ?>index.php">
                <i class="fas fa-gauge-high"></i>
                <span>Dashboard</span>
            </a>
        </li><!-- End Dashboard Nav -->

        <li class="nav-heading">Modules</li>

        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#enquiry-nav" data-bs-toggle="collapse" href="#">
                <i class="fas fa-headset"></i><span>Enquiry Management</span><i class="fas fa-chevron-down ms-auto"></i>
            </a>
            <ul id="enquiry-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                <li><a href="<?php echo ADMIN_BASE_URL; ?>admission-enquiry.php"><i class="far fa-circle nav-icon-sub"></i><span>Admission Enquiry</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>franchise-enquiry.php"><i class="far fa-circle nav-icon-sub"></i><span>Franchise Enquiry</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>online-admission-enquiry.php"><i class="far fa-circle nav-icon-sub"></i><span>Online Admission Enquiry</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>online-franchise-application.php"><i class="far fa-circle nav-icon-sub"></i><span>Online Franchise Application</span></a></li>
            </ul>
        </li>

        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#partner-nav" data-bs-toggle="collapse" href="#">
                <i class="fas fa-handshake"></i><span>Partner Managements</span><i class="fas fa-chevron-down ms-auto"></i>
            </a>
            <ul id="partner-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                <li><a href="<?php echo ADMIN_BASE_URL; ?>partner-list.php"><i class="far fa-circle nav-icon-sub"></i><span>Partner List</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>partner-transaction.php"><i class="far fa-circle nav-icon-sub"></i><span>Partner Transaction</span></a></li>
            </ul>
        </li>

        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#franchise-nav" data-bs-toggle="collapse" href="#">
                <i class="fas fa-store"></i><span>Franchise Management</span><i class="fas fa-chevron-down ms-auto"></i>
            </a>
            <ul id="franchise-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                <li><a href="<?php echo ADMIN_BASE_URL; ?>franchise-list.php"><i class="far fa-circle nav-icon-sub"></i><span>Franchise List</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>franchise-wallet-approval.php"><i class="far fa-circle nav-icon-sub"></i><span>Franchise Wallet Approval</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>franchise-wallet-ledger.php"><i class="far fa-circle nav-icon-sub"></i><span>Wallet Balance Ledger</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>franchise-document.php"><i class="far fa-circle nav-icon-sub"></i><span>Franchise Document</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>franchise-notice.php"><i class="far fa-circle nav-icon-sub"></i><span>Franchise Notice</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>certificate-template.php"><i class="far fa-circle nav-icon-sub"></i><span>Certificate Template</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>generate-certificate.php"><i class="far fa-circle nav-icon-sub"></i><span>Certificate Generate</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>franchise-registration-transaction.php"><i class="far fa-circle nav-icon-sub"></i><span>Registration Transaction</span></a></li>
            </ul>
        </li>

        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#student-nav" data-bs-toggle="collapse" href="#">
                <i class="fas fa-user-graduate"></i><span>Student Management</span><i class="fas fa-chevron-down ms-auto"></i>
            </a>
            <ul id="student-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                <li><a href="<?php echo ADMIN_BASE_URL; ?>student-list.php"><i class="far fa-circle nav-icon-sub"></i><span>Student List</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>online-admission-list.php"><i class="far fa-circle nav-icon-sub"></i><span>Online Admission List</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>student-attendance.php"><i class="far fa-circle nav-icon-sub"></i><span>Student Attendance</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>student-promotion.php"><i class="far fa-circle nav-icon-sub"></i><span>Student Promotion</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>student-registration-transaction.php"><i class="far fa-circle nav-icon-sub"></i><span>Student Reg. Transaction</span></a></li>
            </ul>
        </li>

        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#course-nav" data-bs-toggle="collapse" href="#">
                <i class="fas fa-book-open"></i><span>Course Management</span><i class="fas fa-chevron-down ms-auto"></i>
            </a>
            <ul id="course-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                <li><a href="<?php echo ADMIN_BASE_URL; ?>course-category.php"><i class="far fa-circle nav-icon-sub"></i><span>Course Category</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>course-list.php"><i class="far fa-circle nav-icon-sub"></i><span>Course</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>subject-list.php"><i class="far fa-circle nav-icon-sub"></i><span>All Subject</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>study-material.php"><i class="far fa-circle nav-icon-sub"></i><span>Study Material</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>all-years.php"><i class="far fa-circle nav-icon-sub"></i><span>All Years</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>course-duration.php"><i class="far fa-circle nav-icon-sub"></i><span>Course Duration</span></a></li>

            </ul>
        </li>

        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#card-nav" data-bs-toggle="collapse" href="#">
                <i class="fas fa-id-card"></i><span>Card Management</span><i class="fas fa-chevron-down ms-auto"></i>
            </a>
            <ul id="card-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                <li><a href="<?php echo ADMIN_BASE_URL; ?>id-card-template.php"><i class="far fa-circle nav-icon-sub"></i><span>ID Card Template</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>id-card-generate.php"><i class="far fa-circle nav-icon-sub"></i><span>ID Card Generate</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>admit-template.php"><i class="far fa-circle nav-icon-sub"></i><span>Admit Template</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>admit-generate.php"><i class="far fa-circle nav-icon-sub"></i><span>Admit Generate</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>admit-download-date.php"><i class="far fa-circle nav-icon-sub"></i><span>Admit Download Date</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>marksheet-template.php"><i class="far fa-circle nav-icon-sub"></i><span>Marksheet Template</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>marksheet-generate.php"><i class="far fa-circle nav-icon-sub"></i><span>Marksheet Generate</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>certificate-template.php"><i class="far fa-circle nav-icon-sub"></i><span>Certificate Template</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>issue-certificate.php"><i class="far fa-circle nav-icon-sub"></i><span>Issue Certificate</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>generate-certificate.php"><i class="far fa-circle nav-icon-sub"></i><span>Generate Certificate</span></a></li>
            </ul>
        </li>

        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#accounting-nav" data-bs-toggle="collapse" href="#">
                <i class="fas fa-calculator"></i><span>Student Accounting</span><i class="fas fa-chevron-down ms-auto"></i>
            </a>
            <ul id="accounting-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                <li><a href="<?php echo ADMIN_BASE_URL; ?>fees-type.php"><i class="far fa-circle nav-icon-sub"></i><span>Fees Type</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>fees-group.php"><i class="far fa-circle nav-icon-sub"></i><span>Fees Group</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>fees-allocation.php"><i class="far fa-circle nav-icon-sub"></i><span>Fees Allocation</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>fees-collection.php"><i class="far fa-circle nav-icon-sub"></i><span>Fees Collection</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>due-fees.php"><i class="far fa-circle nav-icon-sub"></i><span>Due Fees</span></a></li>
            </ul>
        </li>

        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#exam-nav" data-bs-toggle="collapse" href="#">
                <i class="fas fa-file-invoice"></i><span>Exam Management</span><i class="fas fa-chevron-down ms-auto"></i>
            </a>
            <ul id="exam-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                <li><a href="<?php echo ADMIN_BASE_URL; ?>add-exam.php"><i class="far fa-circle nav-icon-sub"></i><span>Add Exam</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>marks-entry.php"><i class="far fa-circle nav-icon-sub"></i><span>Marks Entry</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>marks-entry-list.php"><i class="far fa-circle nav-icon-sub"></i><span>Marks Entry List</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>grade-range.php"><i class="far fa-circle nav-icon-sub"></i><span>Grade Range</span></a></li>
            </ul>
        </li>

        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#office-nav" data-bs-toggle="collapse" href="#">
                <i class="fas fa-building-columns"></i><span>Office Accounting</span><i class="fas fa-chevron-down ms-auto"></i>
            </a>
            <ul id="office-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                <li><a href="<?php echo ADMIN_BASE_URL; ?>voucher-head.php"><i class="far fa-circle nav-icon-sub"></i><span>Voucher Head</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>voucher-deposit.php"><i class="far fa-circle nav-icon-sub"></i><span>Voucher Deposit</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>voucher-expense.php"><i class="far fa-circle nav-icon-sub"></i><span>Voucher Expense</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>office-ledger.php"><i class="far fa-circle nav-icon-sub"></i><span>Accounting Ledger</span></a></li>
            </ul>
        </li>

        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#frontend-nav" data-bs-toggle="collapse" href="#">
                <i class="fas fa-display"></i><span>Frontend</span><i class="fas fa-chevron-down ms-auto"></i>
            </a>
            <ul id="frontend-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                <li><a href="<?php echo ADMIN_BASE_URL; ?>frontend-overview.php"><i class="far fa-circle nav-icon-sub"></i><span>Overview</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>frontend-page-section.php"><i class="far fa-circle nav-icon-sub"></i><span>Page Section</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>frontend-page-manage.php"><i class="far fa-circle nav-icon-sub"></i><span>Page Manage</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>frontend-menu.php"><i class="far fa-circle nav-icon-sub"></i><span>Front Menu</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>frontend-banners.php"><i class="far fa-circle nav-icon-sub"></i><span>Banners</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>frontend-testimonial.php"><i class="far fa-circle nav-icon-sub"></i><span>Testimonial</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>frontend-events.php"><i class="far fa-circle nav-icon-sub"></i><span>Events</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>frontend-notices.php"><i class="far fa-circle nav-icon-sub"></i><span>Notices</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>frontend-center-certificates.php"><i class="far fa-circle nav-icon-sub"></i><span>Center Certificates</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>frontend-affiliation-logo.php"><i class="far fa-circle nav-icon-sub"></i><span>Affiliation Logo</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>frontend-student-achievement.php"><i class="far fa-circle nav-icon-sub"></i><span>Student Achievement</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>frontend-gallery.php"><i class="far fa-circle nav-icon-sub"></i><span>Gallery</span></a></li>
            </ul>
        </li>

        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#online-exam-nav" data-bs-toggle="collapse" href="#">
                <i class="fas fa-laptop-code text-danger"></i><span>Online Exam <span class="badge bg-danger">pro</span></span><i class="fas fa-chevron-down ms-auto"></i>
            </a>
            <ul id="online-exam-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                <li><a href="<?php echo ADMIN_BASE_URL; ?>online-exam.php"><i class="far fa-circle nav-icon-sub"></i><span>Online Exam</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>question-group.php"><i class="far fa-circle nav-icon-sub"></i><span>Question Group</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>add-question.php"><i class="far fa-circle nav-icon-sub"></i><span>Add Question</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>online-marks.php"><i class="far fa-circle nav-icon-sub"></i><span>Online Marks</span></a></li>
            </ul>
        </li>

        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#online-class-nav" data-bs-toggle="collapse" href="#">
                <i class="fas fa-video text-danger"></i><span>Online Class <span class="badge bg-danger">pro</span></span><i class="fas fa-chevron-down ms-auto"></i>
            </a>
            <ul id="online-class-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                <li><a href="<?php echo ADMIN_BASE_URL; ?>online-class-list.php"><i class="far fa-circle nav-icon-sub"></i><span>Online Class List</span></a></li>
            </ul>
        </li>

        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#qr-attendance-nav" data-bs-toggle="collapse" href="#">
                <i class="fas fa-qrcode text-danger"></i><span>QR Attendance <span class="badge bg-danger">pro</span></span><i class="fas fa-chevron-down ms-auto"></i>
            </a>
            <ul id="qr-attendance-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                <li><a href="<?php echo ADMIN_BASE_URL; ?>qr-attendance.php"><i class="far fa-circle nav-icon-sub"></i><span>Attendance</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>qr-attendance-report.php"><i class="far fa-circle nav-icon-sub"></i><span>Attendance Report</span></a></li>
            </ul>
        </li>

        <li class="nav-item">
            <a class="nav-link collapsed" href="<?php echo ADMIN_BASE_URL; ?>reports.php">
                <i class="fas fa-chart-pie"></i><span>Report Management</span>
            </a>
        </li><!-- End All Reports Nav -->

        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#locations-nav" data-bs-toggle="collapse" href="#">
                <i class="fas fa-map-marker-alt"></i><span>Locations</span><i class="fas fa-chevron-down ms-auto"></i>
            </a>
            <ul id="locations-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                <li><a href="<?php echo ADMIN_BASE_URL; ?>manage-countries.php"><i class="far fa-circle nav-icon-sub"></i><span>Countries</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>manage-states.php"><i class="far fa-circle nav-icon-sub"></i><span>States</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>manage-districts.php"><i class="far fa-circle nav-icon-sub"></i><span>Districts</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>manage-cities.php"><i class="far fa-circle nav-icon-sub"></i><span>Cities</span></a></li>
            </ul>
        </li>

        <li class="nav-heading">Settings</li>

        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#user-nav" data-bs-toggle="collapse" href="#">
                <i class="fas fa-users-gear"></i><span>User Management</span><i class="fas fa-chevron-down ms-auto"></i>
            </a>
            <ul id="user-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                <li><a href="<?php echo ADMIN_BASE_URL; ?>all-users.php"><i class="far fa-circle nav-icon-sub"></i><span>All Users</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>user-roles.php"><i class="far fa-circle nav-icon-sub"></i><span>User Roles</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>access-control.php"><i class="far fa-circle nav-icon-sub"></i><span>Access Control</span></a></li>
            </ul>
        </li>

        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#settings-nav" data-bs-toggle="collapse" href="#">
                <i class="fas fa-sliders"></i><span>System Settings</span><i class="fas fa-chevron-down ms-auto"></i>
            </a>
            <ul id="settings-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                <li><a href="<?php echo ADMIN_BASE_URL; ?>general-settings.php"><i class="far fa-circle nav-icon-sub"></i><span>General Settings</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>payment-gateway.php"><i class="far fa-circle nav-icon-sub"></i><span>Payment Gateway</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>payment-qr.php"><i class="far fa-circle nav-icon-sub"></i><span>Payment Qr</span></a></li>
                <li><a href="<?php echo ADMIN_BASE_URL; ?>franchise-gateway.php"><i class="far fa-circle nav-icon-sub"></i><span>Franchise Gateway</span></a></li>
            </ul>
        </li>

        <li class="nav-item">
            <a class="nav-link collapsed" href="<?php echo ADMIN_BASE_URL; ?>print-session.php">
                <i class="fas fa-calendar-days"></i><span>Academic Session</span>
            </a>
        </li>

        <li class="nav-item mt-4">
            <a class="nav-link collapsed text-danger" style="background: rgba(243, 66, 53, 0.05);" href="<?php echo ADMIN_BASE_URL; ?>logout.php">
                <i class="fas fa-sign-out-alt text-danger"></i>
                <span>Sign Out</span>
            </a>
        </li>
    </ul>
</aside>

<style>
    .sidebar-nav .nav-content a.active {
        background: rgba(var(--theme-primary-rgb, 27, 18, 96), 0.05) !important;
        font-weight: 600 !important;
        border-radius: 8px;
        margin: 2px 10px;
        padding-left: 35px !important;
    }
    .sidebar-nav .nav-content a.active i {
        color: var(--primary-color) !important;
        transform: scale(1.2);
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Get current page name without extension
        const currentPath = window.location.pathname.split("/").pop().replace(/\.php$/, "") || "index";
        const sidebarLinks = document.querySelectorAll(".sidebar-nav a");

        sidebarLinks.forEach(link => {
            const href = link.getAttribute("href");
            if (!href || href === "#") return;

            // Get link filename without extension
            const linkPath = href.split("/").pop().replace(/\.php$/, "");
            
            if (linkPath === currentPath) {
                // If it's a sub-menu link
                const parentMenu = link.closest(".nav-content");
                if (parentMenu) {
                    const parentToggle = document.querySelector(`[data-bs-target="#${parentMenu.id}"]`);
                    
                    link.classList.add("active");
                    parentMenu.classList.add("show");
                    if (parentToggle) {
                        parentToggle.classList.remove("collapsed");
                        parentToggle.setAttribute("aria-expanded", "true");
                    }
                } else {
                    // Top-level links
                    link.classList.remove("collapsed");
                }
            }
        });
    });
</script>
