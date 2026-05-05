<?php
// admin/includes/header.php
include_once(__DIR__ . "/config.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>ICSTIR | Admin Dashboard</title>
    
    <!-- Google Fonts: Outfit & Poppins (Localized if available, otherwise fallback) -->
    <link href="<?php echo BASE_URL; ?>assets/vendor/google-fonts/google-fonts.css" rel="stylesheet">
    
    <link href="<?php echo BASE_URL; ?>assets/vendor/fontawesome/css/all.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Dropzone.js for premium uploads -->
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
    <!-- DataTables CSS -->
    <!-- DataTables Local -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/vendor/datatable/css/datatables.min.css">
    
    <link href="<?php echo BASE_URL; ?>assets/css/admin.css" rel="stylesheet">
    <!-- Dynamic Theme Colors from General Settings -->
    <link id="dynamic-theme-css" href="<?php echo BASE_URL; ?>theme.php" rel="stylesheet">
</head>

<body>

    <!-- ======= Header ======= -->
    <header id="header" class="header fixed-top d-flex align-items-center">

        <div class="d-flex align-items-center justify-content-between">
            <a href="<?php echo ADMIN_BASE_URL; ?>index.php" class="logo d-flex align-items-center text-decoration-none">
                <img src="<?php echo $BASE_URL; ?>media/general/favicon.png" alt="">
                <span class="d-none d-lg-block ms-2 fw-bold text-primary-theme">Dashboard</span>
            </a>
            <i class="fas fa-bars toggle-sidebar-btn cursor-pointer ms-3" id="toggle-sidebar"></i>
        </div><!-- End Logo -->

        <div class="search-bar" id="search-bar">
            <form class="search-form d-flex align-items-center" method="POST" action="#">
                <input type="text" name="query" placeholder="Search by name, reg no, or phone..." title="Enter search keyword">
                <button type="submit" title="Search"><i class="fas fa-search"></i></button>
            </form>
        </div><!-- End Search Bar -->

        <nav class="header-nav ms-auto">
            <ul class="d-flex align-items-center">

                <li class="nav-item d-block d-lg-none">
                    <a class="nav-link nav-icon search-bar-toggle" href="#">
                        <i class="fas fa-search"></i>
                    </a>
                </li><!-- End Search Icon-->

                <li class="nav-item">
                    <a class="nav-link nav-icon" href="<?php echo BASE_URL; ?>logout.php?redirect=home" title="Logout and Go Home">
                        <i class="fas fa-home"></i>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link nav-icon" href="<?php echo ADMIN_BASE_URL; ?>student-list.php" title="Student List">
                        <i class="fas fa-user-graduate"></i>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link nav-icon" href="<?php echo ADMIN_BASE_URL; ?>fees-collection.php" title="Fees Collection">
                        <i class="fas fa-wallet"></i>
                    </a>
                </li>

                <li class="nav-item dropdown pe-2">
                    <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
                        <i class="far fa-bell"></i>
                        <span class="badge bg-primary badge-number">4</span>
                    </a><!-- End Notification Icon -->

                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications">
                        <li class="dropdown-header">
                            You have 4 new notifications
                            <a href="#"><span class="badge rounded-pill bg-primary p-2 ms-2">View all</span></a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li class="notification-item">
                            <i class="fas fa-exclamation-circle text-warning"></i>
                            <div>
                                <h4>New Admission</h4>
                                <p>Rahul Kumar applied for DCA</p>
                                <p>30 min. ago</p>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li class="notification-item">
                            <i class="fas fa-check-circle text-success"></i>
                            <div>
                                <h4>Payment Received</h4>
                                <p>₹5000 received from ICSTIR001</p>
                                <p>1 hr. ago</p>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li class="dropdown-footer">
                            <a href="#">Show all notifications</a>
                        </li>
                    </ul><!-- End Notification Dropdown Items -->
                </li><!-- End Notification Nav -->

                <li class="nav-item dropdown pe-3">
                    <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                        <img src="<?php echo $BASE_URL; ?>media/users/avatar.png" alt="Profile" class="rounded-circle">
                        <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo ucfirst($_SESSION['role']); ?></span>
                    </a><!-- End Profile Image Icon -->

                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                        <li class="dropdown-header">
                            <h6><?php echo htmlspecialchars($_SESSION['username']); ?></h6>
                            <span><?php echo ucfirst($_SESSION['role']); ?></span>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="<?php echo ADMIN_BASE_URL; ?>admin-profile.php">
                                <i class="fas fa-user-circle"></i>
                                <span>My Profile</span>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="<?php echo ADMIN_BASE_URL; ?>general-settings.php">
                                <i class="fas fa-cog"></i>
                                <span>Settings</span>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center text-danger" href="<?php echo BASE_URL; ?>logout.php">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Sign Out</span>
                            </a>
                        </li>
                    </ul><!-- End Profile Dropdown Items -->
                </li><!-- End Profile Nav -->

            </ul>
        </nav><!-- End Icons Navigation -->

    </header><!-- End Header -->

    <?php include(__DIR__ . "/sidebar.php"); ?>

    <main id="main" class="main">
