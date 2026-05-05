<?php
require_once('../common/config.php');

$admission = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['reg_no'])) {
    // Assuming format NEB/REG/000012 or just 12
    $reg_no_input = trim($_POST['reg_no']);
    $id = preg_replace('/[^0-9]/', '', $reg_no_input);
    
    if ($id) {
        $stmt = $pdo->prepare("
            SELECT a.*, c.name as course_name, f.center_name 
            FROM admissions a 
            LEFT JOIN courses c ON a.course_id = c.id 
            LEFT JOIN franchises f ON a.center_id = f.id 
            WHERE a.id = ? AND a.approval_status = 'approved'
        ");
        $stmt->execute([$id]);
        $admission = $stmt->fetch();
        
        if (!$admission) {
            $error = "Invalid Registration Number or Admission is not yet approved.";
        }
    } else {
        $error = "Invalid Registration Number format.";
    }
} elseif (isset($_SESSION['user_id']) && $_SESSION['role'] === 'student') {
    $stmt = $pdo->prepare("
        SELECT a.*, c.name as course_name, f.center_name 
        FROM admissions a 
        LEFT JOIN courses c ON a.course_id = c.id 
        LEFT JOIN franchises f ON a.center_id = f.id 
        WHERE a.user_id = ? AND a.approval_status = 'approved'
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $admission = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Academic Portal | Registration Verification</title>
    <?php include("../common/meta.php"); ?>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <!-- Dynamic Theme Colors from General Settings -->
    <link id="dynamic-theme-css" rel="stylesheet" href="<?php echo BASE_URL; ?>theme.php">
    <style>
        .verification-card {
            border: 2px dashed #198754;
            background: #fff;
            padding: 40px;
        }
        .v-icon { color: #198754; font-size: 60px; }
    </style>
</head>
<body class="bg-light">
    <?php include("../common/header.php"); ?>

    <div class="container py-5">
        <?php if ($admission): ?>
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="verification-card shadow-lg rounded-4 text-center">
                        <div class="v-icon mb-3">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h2 class="fw-bold text-success mb-2">Registration Verified</h2>
                        <p class="text-muted mb-5">This record is officially registered with NEBOOSASE India.</p>

                        <div class="row text-start g-4 mb-5">
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3">
                                    <label class="small text-muted d-block">STUDENT NAME</label>
                                    <span class="fw-bold"><?php echo strtoupper($admission['full_name']); ?></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3">
                                    <label class="small text-muted d-block">REGISTRATION NO.</label>
                                    <span class="fw-bold">NEB/REG/<?php echo str_pad($admission['id'], 6, '0', STR_PAD_LEFT); ?></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3">
                                    <label class="small text-muted d-block">COURSE NAME</label>
                                    <span class="fw-bold"><?php echo $admission['course_name']; ?></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3">
                                    <label class="small text-muted d-block">CENTER ALLOTTED</label>
                                    <span class="fw-bold"><?php echo $admission['center_name']; ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="text-center">
                            <span class="badge bg-success p-2 px-3 rounded-pill">Status: VERIFIED & ACTIVE</span>
                            <p class="mt-3 x-small text-muted">Verification ID: V-<?php echo md5($admission['id']); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="row justify-content-center">
                <div class="col-lg-5">
                    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                        <div class="card-header bg-success p-4 text-white text-center">
                            <h4 class="mb-0 fw-bold">Registration Verification</h4>
                            <p class="mb-0 small opacity-75">Instant online validation</p>
                        </div>
                        <div class="card-body p-4 p-md-5">
                            <?php if($error): ?>
                                <div class="alert alert-danger shadow-sm rounded-3 mb-4">
                                    <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?>
                                </div>
                            <?php endif; ?>
                            <form action="" method="POST">
                                <div class="mb-4">
                                    <label class="form-label small fw-bold">Registration / Enrollment No.</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-success"><i class="fas fa-id-badge"></i></span>
                                        <input type="text" class="form-control" name="reg_no" placeholder="Enter Registration No." required>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success w-100 py-3 rounded-pill fw-bold shadow">
                                    VERIFY REGISTRATION
                                </button>
                                <p class="text-center mt-3 small text-muted">Official digital verification by NEBOOSASE India.</p>
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