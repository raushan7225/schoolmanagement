<?php
require_once('common/config.php');

// If already logged in, redirect to dashboard
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    header("Location: " . $_SESSION['role'] . "/index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $role = $_POST['role'];
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password_raw = $_POST['password'];

    // Server-side validation
    if (empty($role) || empty($username) || empty($email) || empty($password_raw)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (strlen($password_raw) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        $password = password_hash($password_raw, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        try {
            $stmt->execute([$username, $email, $password, $role]);
            $_SESSION['success'] = "Registration successful! You can now login.";
            header("Location: login.php");
            exit();
        } catch (PDOException $e) {
            $error = "Registration failed. Username or email already exists.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Create Account - NEBOOSASE</title>
    
    <!-- Local Assets -->
    <link href="<?php echo BASE_URL; ?>assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>assets/vendor/fontawesome/css/all.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>assets/css/login.css" rel="stylesheet">
</head>

<body>

    <main class="login-page d-flex align-items-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8">

                    <div class="text-center mb-4">
                        <a href="<?php echo BASE_URL; ?>index.php" class="text-decoration-none">
                            <img src="<?php echo BASE_URL; ?>media/general/logo.jpeg" alt="Logo" class="logo-img rounded">
                        </a>
                    </div>

                    <div class="card login-card">
                        <div class="card-header-custom">
                            <h4 class="mb-1">Create Account</h4>
                            <p class="mb-0 opacity-75">Join the NEBOOSASE Educational Network</p>
                        </div>
                        <div class="card-body p-4 p-md-5">

                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger mb-4 py-2 small text-center"><?php echo $error; ?></div>
                            <?php endif; ?>

                            <form class="row g-3 needs-validation" method="POST" novalidate>
                                <div class="col-md-12 mb-2">
                                    <label class="form-label fw-bold small">Register As</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                        <select name="role" class="form-select" required>
                                            <option value="student">Student</option>
                                            <option value="franchise">Franchise (Center)</option>
                                            <option value="partner">Partner</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-2">
                                    <label class="form-label fw-bold small">Full Name / Username</label>
                                    <div class="input-group has-validation">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <input type="text" name="username" class="form-control" placeholder="Choose username" required minlength="3">
                                        <div class="invalid-feedback">Username required (min 3 chars).</div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-2">
                                    <label class="form-label fw-bold small">Email Address</label>
                                    <div class="input-group has-validation">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        <input type="email" name="email" class="form-control" placeholder="admin@example.com" required>
                                        <div class="invalid-feedback">Please provide a valid email.</div>
                                    </div>
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold small">Create Password</label>
                                    <div class="input-group has-validation">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input type="password" name="password" id="password" class="form-control" placeholder="Minimum 6 characters" required minlength="6">
                                        <button class="btn" type="button" id="togglePassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <div class="invalid-feedback">Password must be at least 6 characters.</div>
                                    </div>
                                </div>

                                <div class="col-12 mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="terms" required>
                                        <label class="form-check-label text-muted small" for="terms">
                                            I agree to the <a href="#" class="text-decoration-none">Terms & Conditions</a>
                                        </label>
                                        <div class="invalid-feedback">You must agree before submitting.</div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <button class="btn btn-login w-100 shadow-sm" type="submit">CREATE ACCOUNT</button>
                                </div>
                                
                                <div class="col-12 mt-4 text-center">
                                    <p class="small mb-0 text-muted">Already have an account? <a href="login.php" class="text-decoration-none fw-bold" style="color: var(--secondary-color);">Login Now</a>  <br> OR <br> <a href="index.php" class="text-decoration-none fw-bold" style="color: var(--primary-color);"> ← Back to Home</a></p>
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
