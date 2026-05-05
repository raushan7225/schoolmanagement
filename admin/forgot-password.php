<?php
include_once(__DIR__ . "/includes/config.php");
include_once(__DIR__ . "/../common/db.php");
session_start();

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
        $error = "Email address not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | ICSTIR Admin</title>
    <!-- Local CSS Files -->
    <link href="<?php echo BASE_URL; ?>assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>assets/vendor/fontawesome/css/all.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>assets/css/login.css" rel="stylesheet">
</head>
<body>
    <div class="login-card">
        <div class="logo">
            <img src="<?php echo BASE_URL; ?>media/general/favicon.png" alt="Logo">
            <h5>FORGOT PASSWORD</h5>
        </div>
        <p class="text-muted small text-center mb-4">Enter your email to reset your password</p>
        <?php if(isset($error)): ?>
            <div class="alert alert-danger py-2 small"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-4">
                <label class="form-label small fw-bold">Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="admin@example.com" required>
            </div>
            <button type="submit" class="btn btn-login shadow">Send Reset Link</button>
        </form>
        <div class="links">
            <a href="login.php">Back to Login</a>
        </div>
    </div>
    <!-- Local JS Files -->
    <script src="<?php echo BASE_URL; ?>assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
