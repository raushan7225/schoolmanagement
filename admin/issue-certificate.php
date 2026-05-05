<?php
// admin/issue-certificate.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

$exams = $pdo->query("SELECT id, exam_name FROM exams WHERE status = 1 ORDER BY exam_name ASC")->fetchAll(PDO::FETCH_ASSOC);
$templates = $pdo->query("SELECT id, name FROM document_templates WHERE type = 'certificate' AND status = 1 ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>Issue Certificate</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Card Management</li>
            <li class="breadcrumb-item active">Issue Certificate</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <!-- Step 1: Search -->
        <div class="col-lg-12">
            <div class="card shadow-sm border-start border-4 border-primary mb-4">
                <div class="card-body pt-3">
                    <h5 class="card-title fw-bold text-primary mb-3"><i class="fas fa-search me-2"></i>STEP 1: FIND STUDENT</h5>
                    <form class="row g-3 align-items-end" id="searchForm" novalidate>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-dark small">SELECT EXAM <span class="text-danger">*</span></label>
                            <select class="form-select" name="exam_id" required>
                                <option value="">Select Exam…</option>
                                <?php foreach($exams as $ex): ?>
                                    <option value="<?php echo $ex['id']; ?>"><?php echo htmlspecialchars($ex['exam_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small">STUDENT ROLL NO. / MOBILE <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-id-badge"></i></span>
                                <input type="text" class="form-control" name="reg_no" placeholder="Enter Registration No. or Mobile Number" required>
                                <button class="btn btn-primary fw-bold" type="submit" id="btn-search">
                                    <i class="fas fa-search me-2"></i>SEARCH
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Step 2: Authorize -->
        <div class="col-lg-12 d-none" id="issueSection">
            <div class="card shadow-sm border-start border-4 border-success">
                <div class="card-body pt-3">
                    <h5 class="card-title fw-bold text-success mb-3"><i class="fas fa-award me-2"></i>STEP 2: AUTHORIZE CERTIFICATE</h5>
                    <form id="issueCertForm" novalidate>
                        <input type="hidden" name="action" value="issue_certificate">
                        <input type="hidden" name="admission_id" id="st-id" value="">
                        
                        <div class="row g-4">
                            <!-- Student Verification Data -->
                            <div class="col-md-6">
                                <div class="p-4 bg-light rounded-3 border h-100 shadow-sm">
                                    <div class="d-flex align-items-center mb-4 pb-2 border-bottom">
                                        <div class="bg-primary text-white p-2 rounded me-3"><i class="fas fa-user-check"></i></div>
                                        <h6 class="fw-bold text-dark mb-0">STUDENT VERIFICATION</h6>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-12"><label class="view-label">Student Name</label><div class="view-value fw-bold text-dark fs-5" id="st-name">-</div></div>
                                        <div class="col-md-6"><label class="view-label">Roll Number</label><div class="view-value fw-bold text-primary" id="st-roll">-</div></div>
                                        <div class="col-md-6"><label class="view-label">Course</label><div class="view-value text-dark" id="st-course">-</div></div>
                                        <div class="col-12"><label class="view-label">Franchise Center</label><div class="view-value text-dark" id="st-center">-</div></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Certificate Configuration -->
                            <div class="col-md-6">
                                <div class="p-4 bg-white rounded-3 border h-100 shadow-sm">
                                    <div class="d-flex align-items-center mb-4 pb-2 border-bottom">
                                        <div class="bg-success text-white p-2 rounded me-3"><i class="fas fa-certificate"></i></div>
                                        <h6 class="fw-bold text-dark mb-0">CERTIFICATE CONFIGURATION</h6>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-dark">Select Template <span class="text-danger">*</span></label>
                                        <select class="form-select" name="template_id" required>
                                            <option value="">Choose Template…</option>
                                            <?php foreach($templates as $tp): ?>
                                                <option value="<?php echo $tp['id']; ?>"><?php echo htmlspecialchars($tp['name']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-dark">Certificate Serial No. <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control fw-bold text-primary" name="unique_id" placeholder="e.g. CERT/2024/0001" required>
                                    </div>
                                    <div class="mb-0">
                                        <label class="form-label fw-bold text-dark">Issue Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="issued_at" value="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer bg-light border-top py-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary fw-bold px-4" onclick="location.reload()">CANCEL</button>
                    <button type="submit" form="issueCertForm" class="btn btn-success fw-bold px-5 shadow-sm" id="btn-issue">
                        <i class="fas fa-check-circle me-2"></i>AUTHORIZE & ISSUE
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
'use strict';
const HANDLER = '<?php echo BASE_URL; ?>ajax/card_handler.php';

document.getElementById('searchForm').addEventListener('submit', function(e) {
    e.preventDefault();
    if (!this.checkValidity()) { this.classList.add('was-validated'); return; }
    
    const btn = document.getElementById('btn-search');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>SEARCHING...';
    
    fetch(HANDLER, { method: 'POST', body: new FormData(this) })
    .then(r => r.json()).then(res => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-search me-2"></i>SEARCH';
        if(res.success) {
            const s = res.data;
            document.getElementById('st-id').value = s.id;
            document.getElementById('st-name').textContent = s.full_name.toUpperCase();
            document.getElementById('st-roll').textContent = s.roll_number || 'NOT GENERATED';
            document.getElementById('st-course').textContent = s.course_name;
            document.getElementById('st-center').textContent = `${s.center_code} - ${s.center_name}`;
            
            document.getElementById('issueSection').classList.remove('d-none');
            window.scrollTo({top: document.getElementById('issueSection').offsetTop - 100, behavior: 'smooth'});
        } else {
            alert(res.message);
            document.getElementById('issueSection').classList.add('d-none');
        }
    });
});

document.getElementById('issueCertForm').addEventListener('submit', function(e) {
    e.preventDefault();
    if (!this.checkValidity()) { this.classList.add('was-validated'); return; }
    
    const btn = document.getElementById('btn-issue');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>ISSUING...';
    
    fetch(HANDLER, { method: 'POST', body: new FormData(this) })
    .then(r => r.json()).then(res => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check-circle me-2"></i>AUTHORIZE & ISSUE';
        if(res.success) {
            alert(res.message);
            location.href = 'generate-certificate.php';
        } else {
            alert(res.message);
        }
    });
});
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>
