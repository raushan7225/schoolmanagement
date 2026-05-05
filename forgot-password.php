<?php
require_once('common/config.php');

// If already logged in, redirect to dashboard
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    header("Location: " . $_SESSION['role'] . "/index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $_SESSION['reset_email'] = $email;
        header("Location: reset-password.php");
        exit();
    } else {
        $error = "Email address not found in our records.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Recover Access - NEBOOSASE</title>
    
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
                            <h4 class="mb-1">Recover Access</h4>
                            <p class="mb-0 opacity-75">Reset your account password</p>
                        </div>
                        <div class="card-body p-4 p-md-5">

                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger mb-4 py-2 small text-center"><?php echo $error; ?></div>
                            <?php endif; ?>

                            <form class="row g-3" method="POST">
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold small">Email Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        <input type="email" name="email" class="form-control" placeholder="Enter registered email" required>
                                    </div>
                                    <div class="form-text small mt-2">We'll send you instructions to reset your password.</div>
                                </div>

                                <div class="col-12">
                                    <button class="btn btn-login w-100 shadow-sm" type="submit">SEND RESET LINK</button>
                                </div>
                                
                                <div class="col-12 mt-4 text-center">
                                    <a href="login.php" class="text-decoration-none fw-bold" style="color: var(--secondary-color);"><i class="fas fa-arrow-left me-2"></i>Back to Login</a>
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
