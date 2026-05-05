<?php
// admin/student-promotion.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

$filter_center = (int)($_GET['center_id'] ?? 0);
$filter_course = (int)($_GET['course_id'] ?? 0);
$current_year = $_GET['current_year'] ?? '';

$where = "WHERE a.approval_status = 'approved'";
$params = [];

if ($filter_center) {
    $where .= " AND a.center_id = ?";
    $params[] = $filter_center;
}
if ($filter_course) {
    $where .= " AND a.course_id = ?";
    $params[] = $filter_course;
}

$stmt = $pdo->prepare("
    SELECT a.*, c.name as course_name, f.center_name, f.center_code
    FROM admissions a
    LEFT JOIN courses c ON a.course_id = c.id
    LEFT JOIN franchises f ON a.center_id = f.id
    $where
    ORDER BY a.full_name ASC
");
$stmt->execute($params);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Dropdowns
$franchises = $pdo->query("SELECT id, center_name, center_code FROM franchises WHERE status = 1 ORDER BY center_name ASC")->fetchAll(PDO::FETCH_ASSOC);
$courses = $pdo->query("SELECT id, name as course_name FROM courses WHERE status = 1 ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>Student Promotion</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Student Management</li>
            <li class="breadcrumb-item active">Promotion</li>
        </ol>
    </nav>
</div>

<section class="section">
    <!-- Eligibility Stat Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-none border-start border-primary border-4 mb-0">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="bg-primary-light p-2 rounded text-primary"><i class="fas fa-users fa-lg"></i></div>
                    <div>
                        <div class="text-muted small fw-bold">ELIGIBLE STUDENTS</div>
                        <div class="h5 mb-0 fw-bold"><?php echo count($students); ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-none border-start border-success border-4 mb-0">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="bg-success-light p-2 rounded text-success"><i class="fas fa-arrow-circle-up fa-lg"></i></div>
                    <div>
                        <div class="text-muted small fw-bold">PROMOTED (THIS YEAR)</div>
                        <div class="h5 mb-0 fw-bold text-success">0</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-none border-start border-warning border-4 mb-0">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="bg-warning-light p-2 rounded text-warning"><i class="fas fa-graduation-cap fa-lg"></i></div>
                    <div>
                        <div class="text-muted small fw-bold">GRADUATING</div>
                        <div class="h5 mb-0 fw-bold text-warning">0</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body pt-3 pb-2">
            <form class="row g-2" method="GET">
                <div class="col-md-5">
                    <select name="center_id" class="form-select">
                        <option value="">All Franchises / Centers</option>
                        <?php foreach($franchises as $f): ?>
                            <option value="<?php echo $f['id']; ?>" <?php echo $filter_center == $f['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($f['center_name']); ?> (<?php echo $f['center_code']; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <select name="course_id" class="form-select">
                        <option value="">All Courses</option>
                        <?php foreach($courses as $c): ?>
                            <option value="<?php echo $c['id']; ?>" <?php echo $filter_course == $c['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($c['course_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1"><i class="fas fa-search me-1"></i>Fetch</button>
                    <a href="student-promotion.php" class="btn btn-outline-secondary" title="Reset Filters"><i class="fas fa-undo"></i></a>
                </div>
            </form>
        </div>
    </div>

    <!-- Promotion Table -->
    <div class="card">
        <div class="card-body pt-3">
            <div id="promote-controls" class="d-none me-2">
                <div class="d-flex align-items-center gap-2">
                    <label class="fw-bold text-danger small mb-0">PROMOTE SELECTED TO:</label>
                    <select class="form-select form-select-sm" id="target-session" style="width:180px;">
                        <option value="2">2nd Year</option>
                        <option value="pass">Graduated / Passed</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle datatable-premium">
                    <thead class="table-light">
                        <tr>
                            <th width="40" data-no-sort>
                                <input type="checkbox" class="form-check-input" id="checkAll" onchange="toggleAllRows(this)">
                            </th>
                            <th width="60">S.No.</th>
                            <th>Reg No</th>
                            <th>Student Name</th>
                            <th>Current Session</th>
                            <th data-no-sort>Exam Performance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $sn=1; foreach($students as $s): ?>
                        <tr>
                            <td><input type="checkbox" class="form-check-input row-check" value="<?php echo $s['id']; ?>"></td>
                            <td><?php echo $sn++; ?></td>
                            <td><span class="badge bg-light text-dark border fw-bold">NEB/REG/<?php echo str_pad($s['id'], 6, '0', STR_PAD_LEFT); ?></span></td>
                            <td>
                                <div class="fw-bold text-dark"><?php echo strtoupper(htmlspecialchars($s['full_name'])); ?></div>
                                <small class="text-muted"><?php echo htmlspecialchars($s['center_name']); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($s['course_name']); ?></td>
                            <td>
                                <div class="small fw-bold text-success mb-1">N/A</div>
                                <div class="progress" style="height:4px;">
                                    <div class="progress-bar bg-success" style="width:0%"></div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-light border-top py-3 text-end">
            <button type="button" class="btn btn-primary px-5 fw-bold" onclick="initPromotion()">
                <i class="fas fa-arrow-circle-up me-2"></i>BULK PROMOTE SELECTED
            </button>
        </div>
    </div>
</section>

<!-- Confirm Modal -->
<div class="modal fade" id="modalConfirm" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-arrow-circle-up me-2"></i>Confirm Promotion</h5>
                <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="bg-primary-light text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;">
                    <i class="fas fa-users fa-2x"></i>
                </div>
                <h5 class="fw-bold mb-2">Are you sure?</h5>
                <p class="text-muted mb-0" id="confirm-msg">You are about to promote the selected students.</p>
                <p class="text-danger small mt-2 fw-bold">This action cannot be undone.</p>
            </div>
            <div class="modal-footer bg-light border-top py-2">
                <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">CANCEL</button>
                <button type="button" class="btn btn-primary px-4 fw-bold" onclick="processPromotion()">
                    <i class="fas fa-check me-2"></i>YES, PROMOTE
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    setTimeout(function() {
        const promoteControls = document.getElementById('promote-controls');
        const dtButtons = document.querySelector('.dt-buttons');
        if (promoteControls && dtButtons) {
            dtButtons.prepend(promoteControls);
            promoteControls.classList.remove('d-none');
            promoteControls.classList.add('d-flex');
        }
    }, 100);
});

function toggleAllRows(chk) {
    document.querySelectorAll('.row-check').forEach(c => c.checked = chk.checked);
}

function initPromotion() {
    const selected = document.querySelectorAll('.row-check:checked');
    if(selected.length === 0) {
        alert('Please select at least one student.');
        return;
    }
    const target = document.getElementById('target-session').selectedOptions[0].text;
    document.getElementById('confirm-msg').innerHTML = `Promoting <strong>${selected.length}</strong> student(s) to <strong>${target}</strong>.`;
    new bootstrap.Modal(document.getElementById('modalConfirm')).show();
}

function processPromotion() {
    // Logic for promotion via AJAX
    alert('Promotion logic will be executed here.');
    location.reload();
}
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>
