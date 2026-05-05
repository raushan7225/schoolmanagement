<?php
// admin/student-attendance.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

$filter_center = (int)($_GET['center_id'] ?? 0);
$filter_course = (int)($_GET['course_id'] ?? 0);
$att_date = $_GET['att_date'] ?? date('Y-m-d');

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

// Fetch students for attendance
$stmt = $pdo->prepare("
    SELECT a.*, c.name as course_name, f.center_name, f.center_code,
           att.status as current_status, att.remarks as current_remarks
    FROM admissions a
    LEFT JOIN courses c ON a.course_id = c.id
    LEFT JOIN franchises f ON a.center_id = f.id
    LEFT JOIN student_attendance att ON a.id = att.admission_id AND att.attendance_date = ?
    $where
    ORDER BY a.full_name ASC
");
$stmt->execute(array_merge([$att_date], $params));
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Stats for today
$totalToday = count($students);
$presentToday = count(array_filter($students, fn($s) => $s['current_status'] == 'present'));
$absentToday = count(array_filter($students, fn($s) => $s['current_status'] == 'absent'));
$leaveToday = count(array_filter($students, fn($s) => $s['current_status'] == 'leave'));

// Dropdowns
$franchises = $pdo->query("SELECT id, center_name, center_code FROM franchises WHERE status = 1 ORDER BY center_name ASC")->fetchAll(PDO::FETCH_ASSOC);
$courses = $pdo->query("SELECT id, name as course_name FROM courses WHERE status = 1 ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>Student Attendance</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Student Management</li>
            <li class="breadcrumb-item active">Attendance</li>
        </ol>
    </nav>
</div>

<section class="section">
    <!-- Summary Stat Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-none border-start border-primary border-4 mb-0">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="bg-primary-light p-2 rounded text-primary"><i class="fas fa-users fa-lg"></i></div>
                    <div>
                        <div class="text-muted small fw-bold">TOTAL STUDENTS</div>
                        <div class="h5 mb-0 fw-bold"><?php echo $totalToday; ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-none border-start border-success border-4 mb-0">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="bg-success-light p-2 rounded text-success"><i class="fas fa-user-check fa-lg"></i></div>
                    <div>
                        <div class="text-muted small fw-bold">PRESENT</div>
                        <div class="h5 mb-0 fw-bold text-success"><?php echo $presentToday; ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-none border-start border-danger border-4 mb-0">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="bg-danger-light p-2 rounded text-danger"><i class="fas fa-user-times fa-lg"></i></div>
                    <div>
                        <div class="text-muted small fw-bold">ABSENT</div>
                        <div class="h5 mb-0 fw-bold text-danger"><?php echo $absentToday; ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-none border-start border-warning border-4 mb-0">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="bg-warning-light p-2 rounded text-warning"><i class="fas fa-user-clock fa-lg"></i></div>
                    <div>
                        <div class="text-muted small fw-bold">ON LEAVE</div>
                        <div class="h5 mb-0 fw-bold text-warning"><?php echo $leaveToday; ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body pt-3 pb-2">
            <form class="row g-2" method="GET">
                <div class="col-md-4">
                    <select class="form-select" name="center_id">
                        <option value="">All Franchises / Centers</option>
                        <?php foreach($franchises as $f): ?>
                            <option value="<?php echo $f['id']; ?>" <?php echo $filter_center == $f['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($f['center_name']); ?> (<?php echo $f['center_code']; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="course_id">
                        <option value="">All Courses</option>
                        <?php foreach($courses as $c): ?>
                            <option value="<?php echo $c['id']; ?>" <?php echo $filter_course == $c['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($c['course_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control" name="att_date" value="<?php echo $att_date; ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search me-1"></i>Filter</button>
                </div>
            </form>
        </div>
    </div>

    <form id="attendance-form">
        <input type="hidden" name="att_date" value="<?php echo $att_date; ?>">
        <input type="hidden" name="action" value="save_attendance">
        <div class="card">
            <div class="card-body pt-3">
                <div id="attendance-alert" class="d-none mb-3"></div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle datatable-premium">
                        <thead class="table-light">
                            <tr>
                                <th width="60">S.No.</th>
                                <th>Student Name</th>
                                <th>Roll Number</th>
                                <th>Center / Course</th>
                                <th class="text-center" data-no-sort>Attendance</th>
                                <th data-no-sort>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $sn=1; foreach($students as $s): ?>
                            <tr>
                                <td><?php echo $sn++; ?></td>
                                <td><div class="fw-bold text-dark"><?php echo strtoupper(htmlspecialchars($s['full_name'])); ?></div></td>
                                <td><code class="text-primary fw-bold"><?php echo htmlspecialchars($s['roll_number']); ?></code></td>
                                <td>
                                    <div class="small fw-semibold"><?php echo htmlspecialchars($s['center_name']); ?></div>
                                    <div class="small text-muted"><?php echo htmlspecialchars($s['course_name']); ?></div>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <input type="radio" class="btn-check" name="attendance[<?php echo $s['id']; ?>]" id="p-<?php echo $s['id']; ?>" value="present" <?php echo ($s['current_status']=='present' || !$s['current_status']) ? 'checked' : ''; ?>>
                                        <label class="btn btn-outline-success btn-sm px-3" for="p-<?php echo $s['id']; ?>">P</label>
                                        
                                        <input type="radio" class="btn-check" name="attendance[<?php echo $s['id']; ?>]" id="a-<?php echo $s['id']; ?>" value="absent" <?php echo ($s['current_status']=='absent') ? 'checked' : ''; ?>>
                                        <label class="btn btn-outline-danger btn-sm px-3" for="a-<?php echo $s['id']; ?>">A</label>
                                        
                                        <input type="radio" class="btn-check" name="attendance[<?php echo $s['id']; ?>]" id="l-<?php echo $s['id']; ?>" value="leave" <?php echo ($s['current_status']=='leave') ? 'checked' : ''; ?>>
                                        <label class="btn btn-outline-warning btn-sm px-3" for="l-<?php echo $s['id']; ?>">L</label>
                                    </div>
                                </td>
                                <td><input type="text" name="remarks[<?php echo $s['id']; ?>]" class="form-control form-control-sm" placeholder="Remark" value="<?php echo htmlspecialchars($s['current_remarks'] ?? ''); ?>"></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-light border-top py-3 text-end">
                <button type="submit" class="btn btn-primary px-5 fw-bold" id="save-btn">
                    <i class="fas fa-save me-2"></i>SAVE ATTENDANCE
                </button>
            </div>
        </div>
    </form>
</section>

<script>
document.getElementById('attendance-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('save-btn');
    const alertBox = document.getElementById('attendance-alert');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>SAVING...';

    const fd = new FormData(this);
    fetch('<?php echo BASE_URL; ?>ajax/attendance_handler.php', {
        method: 'POST',
        body: fd
    })
    .then(r => r.json())
    .then(res => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save me-2"></i>SAVE ATTENDANCE';
        if(res.success) {
            alertBox.className = 'alert alert-success mb-3';
            alertBox.innerHTML = '<i class="fas fa-check-circle me-2"></i>' + res.message;
            alertBox.classList.remove('d-none');
            setTimeout(() => { location.reload(); }, 1000);
        } else {
            alertBox.className = 'alert alert-danger mb-3';
            alertBox.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i>' + res.message;
            alertBox.classList.remove('d-none');
        }
    });
});
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>
