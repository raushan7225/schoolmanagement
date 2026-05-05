<?php
require_once('../common/config.php');

$admission = null;
if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'student') {
    $stmt = $pdo->prepare("
        SELECT a.*, c.name as course_name, f.center_name 
        FROM admissions a 
        LEFT JOIN courses c ON a.course_id = c.id 
        LEFT JOIN franchises f ON a.center_id = f.id 
        WHERE a.user_id = ? AND a.approval_status = 'completed'
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $admission = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Academic Portal | Certificate Verification</title>
    <?php include("../common/meta.php"); ?>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <!-- Dynamic Theme Colors from General Settings -->
    <link id="dynamic-theme-css" rel="stylesheet" href="<?php echo BASE_URL; ?>theme.php">
    <style>
        .cert-card {
            border: 5px double #ffc107;
            background: #fff;
            padding: 50px;
            position: relative;
        }
        .cert-watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 100px;
            color: #ffc107;
            opacity: 0.1;
            z-index: 0;
        }
    </style>
</head>
<body class="bg-light">
    <?php include("../common/header.php"); ?>

    <div class="container py-5">
        <?php if ($admission): ?>
            <div class="row justify-content-center">
                <div class="col-lg-9">
                    <div class="cert-card shadow-lg rounded-4 text-center">
                        <div class="cert-watermark"><i class="fas fa-certificate"></i></div>
                        <div class="position-relative" style="z-index: 1;">
                            <h5 class="fw-bold text-secondary-theme mb-4">CERTIFICATE VERIFICATION RESULT</h5>
                            <h2 class="display-6 fw-bold text-primary-theme mb-4">Verification Successful</h2>
                            
                            <p class="lead mb-5">This is to certify that <strong><?php echo strtoupper($admission['full_name']); ?></strong> has successfully completed the <strong><?php echo $admission['course_name']; ?></strong> from our authorized center <strong><?php echo $admission['center_name']; ?></strong>.</p>

                            <div class="row text-start g-4 justify-content-center mb-5">
                                <div class="col-md-5">
                                    <div class="p-3 border rounded-3 bg-light">
                                        <small class="text-muted d-block">CERTIFICATE NO.</small>
                                        <span class="fw-bold">NEB/CERT/<?php echo date('Y'); ?>/<?php echo str_pad($admission['id'], 5, '0', STR_PAD_LEFT); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="p-3 border rounded-3 bg-light">
                                        <small class="text-muted d-block">ISSUE DATE</small>
                                        <span class="fw-bold"><?php echo date('d M, Y', strtotime($admission['updated_at'])); ?></span>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center">
                                <span class="badge bg-warning text-dark p-2 px-4 rounded-pill fw-bold">GENUINE RECORD</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="row justify-content-center">
                <div class="col-lg-5">
                    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                        <div class="card-header bg-warning p-4 text-dark text-center">
                            <h4 class="mb-0 fw-bold">Certificate Verification</h4>
                            <p class="mb-0 small opacity-75">Verify authenticity of issued awards</p>
                        </div>
                        <div class="card-body p-4 p-md-5">
                            <form action="#" method="POST">
                                <div class="mb-4">
                                    <label class="form-label small fw-bold">Certificate Serial No.</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-warning"><i class="fas fa-medal"></i></span>
                                        <input type="text" class="form-control" name="cert_no" placeholder="Enter Serial No." required>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-warning w-100 py-3 rounded-pill fw-bold shadow">
                                    VERIFY CERTIFICATE
                                </button>
                                <p class="text-center mt-3 small text-muted">Use this portal to check if your certificate is genuine.</p>
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