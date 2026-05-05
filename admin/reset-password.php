<?php
include_once(__DIR__ . "/includes/config.php");
include_once(__DIR__ . "/../common/db.php");
session_start();

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
    $_SESSION['success'] = "Password has been reset successfully!";
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | ICSTIR Admin</title>
    <!-- Local CSS Files -->
    <link href="<?php echo BASE_URL; ?>assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>assets/vendor/fontawesome/css/all.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>assets/css/login.css" rel="stylesheet">
</head>
<body>
    <div class="login-card">
        <div class="logo">
            <img src="<?php echo BASE_URL; ?>media/general/favicon.png" alt="Logo">
            <h5>RESET PASSWORD</h5>
        </div>
        <p class="text-muted small text-center mb-4">Enter your new secure password</p>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label small fw-bold">New Password</label>
                <input type="password" name="password" class="form-control" placeholder="Min 6 characters" required>
            </div>
            <div class="mb-4">
                <label class="form-label small fw-bold">Confirm Password</label>
                <input type="password" class="form-control" placeholder="Repeat password" required>
            </div>
            <button type="submit" class="btn btn-login shadow">Update Password</button>
        </form>
    </div>
    <!-- Local JS Files -->
    <script src="<?php echo BASE_URL; ?>assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
