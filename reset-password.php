<?php
require_once('common/config.php');

if (!isset($_SESSION['reset_email'])) {
    header("Location: forgot-password.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_SESSION['reset_email'];

    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->execute([$password, $email]);

    unset($_SESSION['reset_email']);
    $_SESSION['success'] = "Password reset successful! You can now login.";
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Set New Password - NEBOOSASE</title>
    
    <!-- Local Assets -->
    <link href="<?php echo BASE_URL; ?>assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>assets/vendor/fontawesome/css/all.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>assets/css/login.css" rel="stylesheet">
</head>

<body>

    <main class="login-page d-flex align-items-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-8">

                    <div class="text-center mb-4">
                        <a href="<?php echo BASE_URL; ?>index.php" class="text-decoration-none">
                            <img src="<?php echo BASE_URL; ?>media/general/logo.jpeg" alt="Logo" class="logo-img rounded">
                        </a>
                    </div>

                    <div class="card login-card">
                        <div class="card-header-custom">
                            <h4 class="mb-1">New Password</h4>
                            <p class="mb-0 opacity-75">Secure your NEBOOSASE account</p>
                        </div>
                        <div class="card-body p-4 p-md-5">

                            <form class="row g-3" method="POST">
                                <div class="col-12 mb-2">
                                    <label class="form-label fw-bold small">New Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input type="password" name="password" id="password" class="form-control" placeholder="Create strong password" required>
                                    </div>
                                </div>

                                <div class="col-12 mb-4">
                                    <label class="form-label fw-bold small">Confirm Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-check-double"></i></span>
                                        <input type="password" class="form-control" placeholder="Repeat your password" required>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <button class="btn btn-login w-100 shadow-sm" type="submit">UPDATE PASSWORD</button>
                                </div>
                            </form>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </main>

    <!-- Local JS -->
    <script src="<?php echo BASE_URL; ?>assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>
