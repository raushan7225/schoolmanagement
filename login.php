<?php
require_once('common/config.php');

// If already logged in, redirect to dashboard
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    header("Location: " . $_SESSION['role'] . "/index.php");
    exit();
}

// Handle Login Logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role     = $_POST['role'];

    if (empty($username) || empty($password) || empty($role)) {
        $error = "All fields are required.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE (username = ? OR email = ?) AND role = ?");
        $stmt->execute([$username, $username, $role]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['success'] = "Welcome back, " . $user['username'];
            
            switch ($role) {
                case 'admin': header("Location: " . BASE_URL . "admin/index.php"); break;
                case 'student': header("Location: " . BASE_URL . "student/index.php"); break;
                case 'franchise': header("Location: " . BASE_URL . "franchise/index.php"); break;
                case 'partner': header("Location: " . BASE_URL . "partner/index.php"); break;
            }
            exit();
        } else {
            $error = "Invalid credentials for the selected role.";
        }
    }
}

// Pre-select role if passed via GET
$selected_role = isset($_GET['role']) ? $_GET['role'] : 'student';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Unified Login - NEBOOSASE</title>
    
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
                            <h4 class="mb-1">Welcome Back!</h4>
                            <p class="mb-0 opacity-75">Select your role and enter credentials</p>
                        </div>
                        <div class="card-body p-4 p-md-5">

                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger mb-4 py-2 small text-center"><?php echo $error; ?></div>
                            <?php endif; ?>

                            <form class="row g-3 needs-validation" method="POST" novalidate>

                                <div class="col-12 mb-2">
                                    <label class="form-label fw-bold small">Login As</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                        <select name="role" class="form-select" required>
                                            <option value="student" <?php echo $selected_role == 'student' ? 'selected' : ''; ?>>Student</option>
                                            <option value="franchise" <?php echo $selected_role == 'franchise' ? 'selected' : ''; ?>>Franchise / Center</option>
                                            <option value="partner" <?php echo $selected_role == 'partner' ? 'selected' : ''; ?>>Partner</option>
                                            <option value="admin" <?php echo $selected_role == 'admin' ? 'selected' : ''; ?>>Administrator</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-12 mb-2">
                                    <label class="form-label fw-bold small">Username / Email ID</label>
                                    <div class="input-group has-validation">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <input type="text" name="username" class="form-control" placeholder="Enter your ID" required minlength="3">
                                        <div class="invalid-feedback">Please enter a valid username or email.</div>
                                    </div>
                                </div>

                                <div class="col-12 mb-2">
                                    <div class="d-flex justify-content-between">
                                        <label class="form-label fw-bold small">Password</label>
                                        <a href="forgot-password.php" class="small text-decoration-none" style="color: var(--secondary-color);">Forgot Password?</a>
                                    </div>
                                    <div class="input-group has-validation">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input type="password" name="password" id="password" class="form-control" placeholder="Enter password" required minlength="6">
                                        <button class="btn" type="button" id="togglePassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <div class="invalid-feedback">Password must be at least 6 characters.</div>
                                    </div>
                                </div>

                                <div class="col-12 mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="rememberMe">
                                        <label class="form-check-label text-muted small" for="rememberMe">Remember me on this device</label>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <button class="btn btn-login w-100 shadow-sm" type="submit">LOG IN TO PORTAL</button>
                                </div>
                                
                                <div class="col-12 mt-4 text-center">
                                    <p class="small mb-0 text-muted">Don't have an account? <a href="register.php" class="text-decoration-none fw-bold" style="color: var(--secondary-color);">Register Now</a></p>
                                    <p class="small mt-2 text-muted">Trouble logging in? <a href="#" class="text-decoration-none fw-bold" style="color: var(--primary-color);">Contact Support</a> <br> OR <br> <a href="index.php" class="text-decoration-none fw-bold" style="color: var(--primary-color);"> ← Back to Home</a></p>
                                </div>
                            </form>

                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <p class="small text-muted">&copy; 2026 NEBOOSASE India. All Rights Reserved.</p>
                        <p class="small text-muted">Developed by <a href="https://www.nsprowebtech.com/" class="text-decoration-none">NS Pro Web Tech</a></p>
                    </div>

                </div>
            </div>
        </div>
    </main>

    <!-- Local JS -->
    <script src="<?php echo BASE_URL; ?>assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo BASE_URL; ?>assets/js/login.js"></script>
    <script>
    // Bootstrap validation script
    (function () {
      'use strict'
      var forms = document.querySelectorAll('.needs-validation')
      Array.prototype.slice.call(forms)
        .forEach(function (form) {
          form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
              event.preventDefault()
              event.stopPropagation()
            }
            form.classList.add('was-validated')
          }, false)
        })
    })()
    </script>

</body>

</html>
