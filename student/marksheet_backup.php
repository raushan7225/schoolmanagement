<!DOCTYPE html>
<html lang="en">

<head>
    <title>School Board | Marksheet Backup</title>
    <?php include("../common/meta.php"); ?>
    <meta name="keywords" content="School Board">
    <meta name="description" content="">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <!-- Dynamic Theme Colors from General Settings -->
    <link id="dynamic-theme-css" rel="stylesheet" href="<?php echo BASE_URL; ?>theme.php">
</head>

<body>

    <?php include("../common/header.php"); ?>

    <!-- Page Header -->
    <div class="page-header text-center" style="background: linear-gradient(rgba(11, 28, 61, 0.9), rgba(11, 28, 61, 0.9)), url('<?php echo BASE_URL; ?>/school-management/media/banners/about-us.png') center center;">
        <div class="container">
            <h1 class="display-4 fw-bold text-white mb-3">Marksheet</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center mb-0">
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/school-management/index.php" class="text-white text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active text-white" aria-current="page">Marksheet</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="section-padding bg-light">
        <div class="container py-5">
            <div class="auth-card shadow-lg rounded-4 overflow-hidden bg-white">
                <div class="card-header-theme text-center">
                    <i class="fas fa-poll mb-3 fs-1 text-secondary-theme"></i>
                    <h4 class="mb-0 fw-bold text-white">Download Marksheet</h4>
                    <p class="mb-0 small opacity-75">View and download your academic results</p>
                </div>
                <div class="card-body p-4 p-md-5">
                    <form action="#" method="POST">
                        <div class="mb-4">
                            <label for="enroll_year" class="form-label">Enrollment No. / Roll Code</label>
                            <input type="text" class="form-control" id="enroll_year" name="enroll_year" placeholder="Enter Details" required>
                        </div>
                        <button type="submit" class="btn btn-form-submit w-100 py-2 shadow-sm">VIEW MARKSHEET</button>

                        <div class="text-center mt-3">
                            <p class="text-xs text-muted">Select session/year from the next page after verification.</p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <?php include("../common/footer.php"); ?>

    <?php include("../common/requirejs.php"); ?>

</body>

</html>