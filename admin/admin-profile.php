<?php
// admin/admin-profile.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Fetch the admin user record from DB
$stmt = $pdo->prepare("SELECT id, username, email, role, status, created_at FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$admin) {
    echo "<div class='alert alert-danger'>Admin record not found.</div>";
    include(__DIR__ . "/includes/footer.php");
    exit();
}
?>

<div class="pagetitle">
    <h1>My Profile</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item active">My Profile</li>
        </ol>
    </nav>
</div>

<section class="section profile">
<div class="row">

    <!-- Left: Profile Card -->
    <div class="col-xl-4">
        <div class="card">
            <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
                <div class="profile-avatar rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center mb-3">
                    <i class="fas fa-user-shield fa-4x text-primary"></i>
                </div>
                <h2><?php echo htmlspecialchars($admin['username']); ?></h2>
                <h3><?php echo ucfirst($admin['role']); ?></h3>
                <span class="badge bg-success mt-2 rounded-pill">
                    <?php echo $admin['status'] == 1 ? 'Active Account' : 'Inactive Account'; ?>
                </span>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <h5 class="card-title small text-uppercase fw-bold text-primary">Account Details</h5>
                <div class="mb-2">
                    <small class="text-muted d-block">User ID</small>
                    <span class="fw-bold">#<?php echo $admin['id']; ?></span>
                </div>
                <hr class="my-2">
                <div class="mb-2">
                    <small class="text-muted d-block">Role</small>
                    <span class="fw-bold text-capitalize"><?php echo $admin['role']; ?></span>
                </div>
                <hr class="my-2">
                <div class="mb-2">
                    <small class="text-muted d-block">Account Created</small>
                    <span class="fw-bold"><?php echo date('d M Y', strtotime($admin['created_at'])); ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Right: Edit Form -->
    <div class="col-xl-8">
        <div class="card">
            <div class="card-body pt-3">
                <ul class="nav nav-tabs nav-tabs-bordered">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-overview">Overview</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-change-password">Change Password</button>
                    </li>
                </ul>

                <div class="tab-content pt-3">

                    <!-- Overview Tab -->
                    <div class="tab-pane fade show active" id="tab-overview">
                        <div id="profile-alert" class="d-none mb-3"></div>
                        <form id="profile-form" novalidate>
                            <input type="hidden" name="action" value="update_profile">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Username <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="username"
                                           value="<?php echo htmlspecialchars($admin['username']); ?>" required>
                                    <div class="invalid-feedback">Username is required.</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Email Address</label>
                                    <input type="email" class="form-control" name="email"
                                           value="<?php echo htmlspecialchars($admin['email']); ?>"
                                           placeholder="admin@example.com">
                                </div>
                            </div>
                            <div class="mt-3 text-end">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-save me-2"></i>Update Profile
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Change Password Tab -->
                    <div class="tab-pane fade" id="tab-change-password">
                        <div id="pwd-alert" class="d-none mb-3"></div>
                        <form id="change-pwd-form" novalidate>
                            <input type="hidden" name="action" value="change_password">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Current Password <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" name="current_password"
                                               id="cur_pwd" placeholder="Enter current password" required>
                                        <button class="btn btn-outline-secondary toggle-pass" type="button" data-target="cur_pwd">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="invalid-feedback">Current password is required.</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">New Password <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" name="new_password"
                                               id="new_pwd" placeholder="Minimum 8 characters" required minlength="8">
                                        <button class="btn btn-outline-secondary toggle-pass" type="button" data-target="new_pwd">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="invalid-feedback">New password must be at least 8 characters.</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Confirm New Password <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" name="confirm_password"
                                               id="con_pwd" placeholder="Repeat new password" required minlength="8">
                                        <button class="btn btn-outline-secondary toggle-pass" type="button" data-target="con_pwd">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="invalid-feedback">Please confirm your new password.</div>
                                </div>
                            </div>
                            <div class="mt-3 text-end">
                                <button type="submit" class="btn btn-warning px-4">
                                    <i class="fas fa-key me-2"></i>Change Password
                                </button>
                            </div>
                        </form>
                    </div>

                </div><!-- end tab-content -->
            </div>
        </div>
    </div>

</div>
</section>

<script>
(function () {
    'use strict';

    // Toggle password visibility
    document.querySelectorAll('.toggle-pass').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const targetId = this.getAttribute('data-target');
            const inp = document.getElementById(targetId);
            const icon = this.querySelector('i');
            if (inp.type === 'password') {
                inp.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                inp.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    });

    function showAlert(boxId, type, msg) {
        const box = document.getElementById(boxId);
        box.className = 'alert alert-' + type + ' mb-3';
        box.innerHTML = '<i class="fas fa-' + (type === 'success' ? 'check-circle' : 'exclamation-circle') + ' me-2"></i>' + msg;
        box.classList.remove('d-none');
        box.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function handleForm(formId, alertBoxId) {
        const form = document.getElementById(formId);
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            form.classList.add('was-validated');
            if (!form.checkValidity()) return;

            const btn = form.querySelector('button[type="submit"]');
            const orig = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving...';

            fetch('<?php echo BASE_URL; ?>ajax/admin_profile_handler.php', {
                method: 'POST',
                body: new FormData(form)
            })
            .then(r => r.json())
            .then(res => {
                btn.disabled = false;
                btn.innerHTML = orig;
                showAlert(alertBoxId, res.success ? 'success' : 'danger', res.message);
                if (res.success && formId === 'change-pwd-form') form.reset();
            })
            .catch(() => {
                btn.disabled = false;
                btn.innerHTML = orig;
                showAlert(alertBoxId, 'danger', 'An error occurred. Please try again.');
            });
        });
    }

    handleForm('profile-form', 'profile-alert');
    handleForm('change-pwd-form', 'pwd-alert');
})();
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>
