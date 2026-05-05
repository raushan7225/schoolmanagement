<?php
require_once('../common/config.php');

$admission = null;
$marks = [];
if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'student') {
    // Get Admission Info
    $stmt = $pdo->prepare("
        SELECT a.*, c.name as course_name, ctr.name as center_name 
        FROM admissions a 
        LEFT JOIN courses c ON a.course_id = c.id 
        LEFT JOIN centers ctr ON a.center_id = ctr.id 
        WHERE a.user_id = ?
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $admission = $stmt->fetch();

    if ($admission) {
        // Get Marks Info
        $stmt = $pdo->prepare("SELECT * FROM marks WHERE admission_id = ?");
        $stmt->execute([$admission['id']]);
        $marks = $stmt->fetchAll();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Academic Portal | Marksheet</title>
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
                <div class="col-lg-10">
                    <div class="marksheet-card shadow-lg rounded-4">
                        <div class="result-header text-center pb-3">
                            <h3 class="fw-bold text-primary-theme">NEBOOSASE INDIA</h3>
                            <h5 class="text-secondary-theme">STATEMENT OF MARKS</h5>
                            <p class="mb-0 small fw-bold">SESSION: <?php echo $admission['session_name']; ?></p>
                        </div>

                        <div class="row mb-5">
                            <div class="col-md-7">
                                <table class="table table-sm table-borderless small">
                                    <tr><td class="text-muted">Student Name:</td><td class="fw-bold"><?php echo strtoupper($admission['full_name']); ?></td></tr>
                                    <tr><td class="text-muted">Roll Number:</td><td class="fw-bold">#NEB-<?php echo str_pad($admission['id'], 5, '0', STR_PAD_LEFT); ?></td></tr>
                                    <tr><td class="text-muted">Course:</td><td class="fw-bold"><?php echo $admission['course_name']; ?></td></tr>
                                </table>
                            </div>
                            <div class="col-md-5 text-md-end">
                                <table class="table table-sm table-borderless small">
                                    <tr><td class="text-muted">Center:</td><td class="fw-bold"><?php echo $admission['center_name']; ?></td></tr>
                                    <tr><td class="text-muted">Status:</td><td><span class="badge bg-success">REGULAR</span></td></tr>
                                </table>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered marks-table text-center">
                                <thead>
                                    <tr>
                                        <th>SUBJECT NAME</th>
                                        <th>MAX MARKS</th>
                                        <th>MARKS OBTAINED</th>
                                        <th>GRADE / STATUS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($marks): ?>
                                        <?php foreach($marks as $m): ?>
                                            <tr>
                                                <td class="text-start"><?php echo $m['subject_name']; ?></td>
                                                <td><?php echo (int)$m['max_marks']; ?></td>
                                                <td class="fw-bold"><?php echo (int)$m['marks_obtained']; ?></td>
                                                <td><span class="<?php echo $m['status'] ? 'text-success' : 'text-danger'; ?> fw-bold"><?php echo $m['status'] ? 'PASS' : 'FAIL'; ?></span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="py-5 text-muted">
                                                <i class="fas fa-info-circle me-2"></i> Results are currently being processed for your session.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-5 text-center d-print-none">
                            <button onclick="window.print()" class="btn btn-primary-theme rounded-pill px-5">
                                <i class="fas fa-download me-2"></i>DOWNLOAD MARKSHEET
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
                            <h4 class="mb-0 fw-bold text-white">View Marksheet</h4>
                            <p class="mb-0 small opacity-75">Enter details to verify results</p>
                        </div>
                        <div class="card-body p-4 p-md-5">
                            <form action="#" method="POST">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Enrollment No. / Roll Code</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-id-card-alt"></i></span>
                                        <input type="text" class="form-control" name="enroll_year" placeholder="Enter Details" required>
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
                                    VIEW MARKSHEET
                                </button>
                                <p class="text-center mt-3 small text-muted">Results are also sent to your registered email.</p>
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