<?php
// franchise/includes/header.php
require_once(__DIR__ . "/../../common/config.php");
checkRole('franchise');

$username = $_SESSION['username'];
$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Franchise Portal - NEBOOSASE</title>
    
    <!-- Google Fonts -->
    <link href="<?php echo BASE_URL; ?>assets/vendor/google-fonts/google-fonts.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>assets/vendor/fontawesome/css/all.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- DataTables -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/vendor/datatable/css/datatables.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/franchise.css">
    
    <!-- Dynamic Theme -->
    <link id="dynamic-theme-css" rel="stylesheet" href="<?php echo BASE_URL; ?>theme.php">
</head>
<body class="bg-light">

    <!-- ======= Top Header ======= -->
    <header id="header" class="header fixed-top d-flex align-items-center bg-white shadow-sm" style="height: 70px; z-index: 997;">
        <div class="d-flex align-items-center justify-content-between px-4 w-100">
            <div class="d-flex align-items-center">
                <a href="<?php echo BASE_URL; ?>franchise/index.php" class="logo d-flex align-items-center text-decoration-none">
                    <img src="<?php echo BASE_URL; ?>media/general/favicon.png" alt="Logo" height="40">
                    <span class="d-none d-lg-block ms-2 fw-bold text-primary-theme fs-5">Franchise Portal</span>
                </a>
                <i class="fas fa-bars ms-4 cursor-pointer text-muted d-lg-none" id="toggle-sidebar"></i>
            </div>

            <div class="d-flex align-items-center gap-3">
                <!-- Quick Search (Optional) -->
                <div class="search-bar d-none d-md-block">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control rounded-start-pill border-end-0" placeholder="Search student...">
                        <button class="btn btn-outline-secondary rounded-end-pill border-start-0 bg-white" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>

                <!-- Notifications -->
                <div class="dropdown">
                    <a class="nav-link nav-icon position-relative" href="#" data-bs-toggle="dropdown">
                        <i class="far fa-bell fs-5"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 10px;">
                            3
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 mt-2" style="width: 300px;">
                        <li class="dropdown-header p-3 border-bottom">Notifications</li>
                        <li><a class="dropdown-item p-3 small border-bottom" href="#">New student lead assigned!</a></li>
                        <li><a class="dropdown-item p-3 small border-bottom" href="#">Wallet top-up approved.</a></li>
                        <li class="text-center p-2"><a href="#" class="small text-primary text-decoration-none">View all</a></li>
                    </ul>
                </div>

                <!-- User Profile -->
                <div class="dropdown ms-2">
                    <a class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark" href="#" data-bs-toggle="dropdown">
                        <img src="<?php echo BASE_URL; ?>assets/images/user-placeholder.png" alt="Profile" class="rounded-circle shadow-sm" width="35" height="35">
                        <span class="d-none d-md-block ps-2 small fw-bold"><?php echo $username; ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 mt-2">
                        <li class="dropdown-header p-3 border-bottom">
                            <h6 class="mb-0 fw-bold"><?php echo $username; ?></h6>
                            <small class="text-muted">Center Director</small>
                        </li>
                        <li><a class="dropdown-item py-2" href="profile.php"><i class="fas fa-user-circle me-2"></i> My Profile</a></li>
                        <li><a class="dropdown-item py-2" href="wallet.php"><i class="fas fa-wallet me-2"></i> Wallet</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item py-2 text-danger" href="<?php echo BASE_URL; ?>logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </header>

    <?php include(__DIR__ . "/sidebar.php"); ?>

    <main id="main" class="main p-0">
        <div class="container-fluid p-4">
