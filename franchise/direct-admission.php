<?php
require_once('../common/config.php');
checkRole('franchise');

// Fetch courses for the dropdown
$courses = $pdo->query("SELECT * FROM courses WHERE status = 'active' ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
// Fetch sessions
$sessions = $pdo->query("SELECT id, session_label FROM admission_sessions ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Direct Admission - Franchise Portal</title>
    <?php include("../common/meta.php"); ?>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/franchise.css">
    <link id="dynamic-theme-css" rel="stylesheet" href="<?php echo BASE_URL; ?>theme.php">
</head>
<body class="bg-light">
    <?php include("../common/header.php"); ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="d-flex align-items-center mb-4">
                    <a href="index.php" class="btn btn-outline-secondary rounded-pill me-3">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <h2 class="fw-bold mb-0 text-primary-theme">Direct Student Admission</h2>
                        <p class="text-muted mb-0">Enroll a new student directly to your center.</p>
                    </div>
                </div>

                <div id="alert-container"></div>

                <form id="direct-admission-form" class="row g-4">
                    <!-- Academic Info -->
                    <div class="col-12">
                        <div class="card border-0 shadow-sm rounded-4 p-4">
                            <h5 class="fw-bold mb-3 text-primary-theme"><i class="fas fa-graduation-cap me-2"></i>Academic Selection</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Select Course <span class="text-danger">*</span></label>
                                    <select class="form-select" name="course_id" required>
                                        <option value="">Choose Course...</option>
                                        <?php foreach($courses as $c): ?>
                                            <option value="<?php echo $c['id']; ?>"><?php echo $c['name']; ?> (<?php echo $c['code']; ?>)</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Admission Session <span class="text-danger">*</span></label>
                                    <select class="form-select" name="session_id" required>
                                        <?php foreach($sessions as $s): ?>
                                            <option value="<?php echo $s['id']; ?>" <?php echo $s['is_active'] ? 'selected' : ''; ?>>
                                                <?php echo $s['session_name']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Student Personal Info -->
                    <div class="col-12">
                        <div class="card border-0 shadow-sm rounded-4 p-4">
                            <h5 class="fw-bold mb-3 text-primary-theme"><i class="fas fa-user me-2"></i>Personal Details</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Student Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="full_name" required placeholder="As per documents">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Mobile Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="mobile" maxlength="10" required placeholder="10-digit number">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Email Address</label>
                                    <input type="email" class="form-control" name="email" placeholder="student@example.com">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">Date of Birth <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="dob" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">Gender <span class="text-danger">*</span></label>
                                    <select class="form-select" name="gender" required>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Father's Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="father_name" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Mother's Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="mother_name" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Address & Verification -->
                    <div class="col-12">
                        <div class="card border-0 shadow-sm rounded-4 p-4">
                            <h5 class="fw-bold mb-3 text-primary-theme"><i class="fas fa-map-marker-alt me-2"></i>Contact Address & Verification</h5>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label small fw-bold">Full Postal Address <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="address" rows="2" required></textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Pincode <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="pincode" maxlength="6" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Student Photograph <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control" name="photo" accept="image/*" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Student Signature <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control" name="signature" accept="image/*" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 text-center py-4">
                        <div class="form-check d-inline-block mb-4">
                            <input class="form-check-input" type="checkbox" id="confirmData" required>
                            <label class="form-check-label small" for="confirmData">
                                I confirm that all documents have been verified and the information is correct.
                            </label>
                        </div>
                        <br>
                        <button type="submit" class="btn btn-primary-theme btn-lg rounded-pill px-5 shadow">
                            <i class="fas fa-check-circle me-2"></i>Complete Registration
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include("../common/footer.php"); ?>
    <?php include("../common/requirejs.php"); ?>

    <script>
        document.getElementById('direct-admission-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = e.target.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';

            const formData = new FormData(this);
            formData.append('action', 'direct_admission');

            fetch('../ajax/admission_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(res => {
                const alertBox = document.getElementById('alert-container');
                if(res.status === 'success') {
                    alertBox.innerHTML = `<div class="alert alert-success rounded-4 border-0 shadow-sm"><i class="fas fa-check-circle me-2"></i>Student registered successfully! Redirecting...</div>`;
                    setTimeout(() => window.location.href = 'index.php', 2000);
                } else {
                    alertBox.innerHTML = `<div class="alert alert-danger rounded-4 border-0 shadow-sm"><i class="fas fa-exclamation-circle me-2"></i>${res.message}</div>`;
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Complete Registration';
                }
            });
        });
    </script>
</body>
</html>
