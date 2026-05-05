<?php
include_once(__DIR__ . "/includes/config.php");
include_once(__DIR__ . "/../common/db.php");
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    try {
        $stmt->execute([$username, $email, $password]);
        $_SESSION['success'] = "Registration successful! You can now login.";
        header("Location: login.php");
        exit();
    } catch (PDOException $e) {
        $error = "Username or Email already exists.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | ICSTIR Admin</title>
    <!-- Local CSS Files -->
    <link href="<?php echo BASE_URL; ?>assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>assets/vendor/fontawesome/css/all.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>assets/css/login.css" rel="stylesheet">
</head>
<body>

    <div class="login-card">
        <div class="logo">
            <img src="<?php echo BASE_URL; ?>media/general/favicon.png" alt="Logo">
            <h5>ADMIN REGISTRATION</h5>
        </div>

        <?php if(isset($error)): ?>
            <div class="alert alert-danger py-2 small"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Full Name / Username</label>
                <input type="text" name="username" class="form-control" placeholder="Choose a username" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="admin@example.com" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Create a strong password" required>
            </div>
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="terms" required>
                    <label class="form-check-label small" for="terms">I agree to terms & conditions</label>
                </div>
            </div>
            <button type="submit" class="btn btn-login shadow">Create Account</button>
        </form>

        <div class="links">
            <p class="mb-0 text-muted">Already have an account? <a href="login.php">Login Now</a></p>
        </div>
    </div>
    <!-- Local JS Files -->
    <script src="<?php echo BASE_URL; ?>assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
