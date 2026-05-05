<?php
require_once('../common/config.php');
checkRole('partner');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partner Dashboard - NEBOOSASE</title>
    <?php include("../common/meta.php"); ?>
</head>
<body>
    <?php include("../common/header.php"); ?>
    
    <div class="container py-5 text-center">
        <h1 class="display-4 fw-bold text-primary-theme">Partner Dashboard</h1>
        <p class="lead text-muted">Welcome to the Partner Portal. Your dashboard is currently being prepared.</p>
        <hr class="my-5">
        <div class="row g-4 justify-content-center">
            <div class="col-md-3">
                <a href="<?php echo BASE_URL; ?>logout.php" class="btn btn-outline-danger w-100 p-3 rounded-4">
                    <i class="fas fa-sign-out-alt fs-3 mb-2 d-block"></i> Logout
                </a>
            </div>
        </div>
    </div>

    <?php include("../common/footer.php"); ?>
    <?php include("../common/requirejs.php"); ?>
</body>
</html>
