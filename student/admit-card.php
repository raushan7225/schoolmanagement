<?php
require_once('../common/config.php');

$admission = null;
if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'student') {
    $stmt = $pdo->prepare("
        SELECT a.*, c.name as course_name, ctr.name as center_name 
        FROM admissions a 
        LEFT JOIN courses c ON a.course_id = c.id 
        LEFT JOIN centers ctr ON a.center_id = ctr.id 
        WHERE a.user_id = ?
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $admission = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Academic Portal | Admit Card</title>
    <?php include("../common/meta.php"); ?>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <!-- Dynamic Theme Colors from General Settings -->
    <link id="dynamic-theme-css" rel="stylesheet" href="<?php echo BASE_URL; ?>theme.php">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/student.css">
</head>
<body class="bg-light">
    <?php include("../common/header.php"); ?>

    <div class="container py-5">
        <?php if ($admission): ?>
            <div class="row justify-content-center">
                <div class="col-lg-9">
                    <div class="admit-card-box shadow-lg rounded-4 overflow-hidden">
                        <div class="watermark fw-bold">NEBOOSASE INDIA</div>
                        <div class="admit-header text-center">
                            <h4 class="fw-bold text-primary-theme mb-1">NATIONAL EXAMINATION BOARD</h4>
                            <p class="small text-muted mb-0">BOARD OF OPEN SCHOOLING AND SKILL EDUCATION</p>
                            <h5 class="mt-3 fw-bold text-secondary-theme">EXAMINATION ADMIT CARD</h5>
                        </div>
                        
                        <div class="row g-4 position-relative" style="z-index: 1;">
                            <div class="col-md-8">
                                <table class="table table-sm table-borderless">
                                    <tr><td class="text-muted w-25">Roll Number:</td><td class="fw-bold">#NEB-<?php echo str_pad($admission['id'], 5, '0', STR_PAD_LEFT); ?></td></tr>
                                    <tr><td class="text-muted">Student Name:</td><td class="fw-bold"><?php echo strtoupper($admission['full_name']); ?></td></tr>
                                    <tr><td class="text-muted">Father's Name:</td><td class="fw-bold"><?php echo $admission['father_name']; ?></td></tr>
                                    <tr><td class="text-muted">Course:</td><td class="fw-bold"><?php echo $admission['course_name']; ?></td></tr>
                                    <tr><td class="text-muted">Exam Center:</td><td class="fw-bold"><?php echo $admission['center_name']; ?></td></tr>
                                    <tr><td class="text-muted">Session:</td><td class="fw-bold"><?php echo $admission['session_name']; ?></td></tr>
                                </table>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="bg-light border rounded-3 mb-2 mx-auto d-flex align-items-center justify-content-center" style="width: 120px; height: 140px;">
                                    <i class="fas fa-user-tie display-4 text-muted"></i>
                                </div>
                                <div class="small fw-bold">STUDENT PHOTO</div>
                            </div>
                        </div>

                        <div class="alert alert-secondary mt-4 mb-0 small">
                            <strong>Note:</strong> Please reach the examination center 30 minutes before the scheduled time. Carry a valid photo ID proof along with this admit card.
                        </div>
                        
                        <div class="mt-4 text-center d-print-none">
                            <button onclick="window.print()" class="btn btn-primary-theme rounded-pill px-5">
                                <i class="fas fa-print me-2"></i>PRINT ADMIT CARD
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="row justify-content-center">
                <div class="col-lg-5">
                    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                        <div class="card-header bg-primary-theme p-4 text-white text-center">
                            <h4 class="mb-0 fw-bold text-white">Download Admit Card</h4>
                            <p class="mb-0 small opacity-75">Public Verification Portal</p>
                        </div>
                        <div class="card-body p-4 p-md-5">
                            <form action="#" method="POST">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Enrollment / Roll No.</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        <input type="text" class="form-control" name="reg_roll" placeholder="Enter Details" required>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label small fw-bold">Select Examination</label>
                                    <select class="form-select" name="exam_type" required>
                                        <option value="annual">Annual Examination 2026</option>
                                        <option value="half_yearly">Half Yearly Examination</option>
                                        <option value="supplementary">Supplementary Exam</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary-theme w-100 py-3 rounded-pill fw-bold shadow">
                                    SEARCH ADMIT CARD
                                </button>
                                <p class="text-center mt-3 small text-muted">Contact your study center if details are not found.</p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php include("../common/footer.php"); ?>
    <?php include("../common/requirejs.php"); ?>
</body>
</html>