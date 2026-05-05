<?php
// admin/daily-attendance-report.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// 1. Fetch Filter Data
$courses = $pdo->query("SELECT id, name FROM courses WHERE status = 1 ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// 2. Capture Filters
$date = $_GET['date'] ?? date('Y-m-d');
$course_id = (int)($_GET['course_id'] ?? 0);

// 3. Logic to fetch ALL students and their attendance for THIS date
$params = [$date, $date];
$filter_sql = "";
if ($course_id) { $filter_sql .= " AND adm.course_id = ?"; $params[] = $course_id; }

// Comprehensive query to get students and join with combined attendance
$sql = "
    SELECT 
        adm.id as student_id,
        adm.full_name,
        adm.roll_number,
        adm.photo,
        c.name as course_name,
        COALESCE(att.status, qr.status, 'Not Marked') as current_status,
        COALESCE(qr.check_in_time, '--') as check_in,
        att.remarks
    FROM admissions adm
    JOIN courses c ON adm.course_id = c.id
    LEFT JOIN student_attendance att ON adm.id = att.admission_id AND att.attendance_date = ?
    LEFT JOIN qr_attendance qr ON adm.id = qr.student_id AND DATE(qr.check_in_time) = ?
    WHERE adm.approval_status = 'approved' $filter_sql
    ORDER BY adm.full_name ASC
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Stats Calculation
$total = count($students);
$present = 0;
$absent = 0;
$leave = 0;
foreach($students as $s) {
    if($s['current_status'] == 'present') $present++;
    elseif($s['current_status'] == 'absent') $absent++;
    elseif($s['current_status'] == 'leave') $leave++;
}
?>

<div class="pagetitle">
    <h1>Daily Attendance Report <span class="badge bg-danger ms-2" style="font-size: 0.6rem; vertical-align: middle;">PRO</span></h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Report Management</li>
            <li class="breadcrumb-item active">Daily Attendance</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <!-- Filters -->
        <div class="col-lg-12 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex align-items-center">
                    <i class="fas fa-filter me-2"></i>
                    <h5 class="card-title mb-0">Filter Parameters</h5>
                </div>
                <div class="card-body pt-4">
                    <form class="row g-3" method="GET">
                        <div class="col-md-5">
                            <input type="date" name="date" class="form-control" value="<?php echo $date; ?>" required>
                        </div>
                        <div class="col-md-5">
                            <select class="form-select" name="course_id">
                                <option value="">All Courses</option>
                                <?php foreach($courses as $c): ?>
                                <option value="<?php echo $c['id']; ?>" <?php echo $course_id == $c['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-2"></i>GENERATE
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="col-lg-12 mb-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="card shadow-sm border-start border-primary border-5 mb-0">
                        <div class="card-body p-3">
                            <div class="text-muted small fw-bold">TOTAL STUDENTS</div>
                            <div class="h4 mb-0 fw-bold text-dark"><?php echo $total; ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm border-start border-success border-5 mb-0">
                        <div class="card-body p-3">
                            <div class="text-muted small fw-bold">PRESENT TODAY</div>
                            <div class="h4 mb-0 fw-bold text-success"><?php echo $present; ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm border-start border-danger border-5 mb-0">
                        <div class="card-body p-3">
                            <div class="text-muted small fw-bold">ABSENT TODAY</div>
                            <div class="h4 mb-0 fw-bold text-danger"><?php echo $absent; ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm border-start border-warning border-5 mb-0">
                        <div class="card-body p-3">
                            <div class="text-muted small fw-bold">ON LEAVE / PENDING</div>
                            <div class="h4 mb-0 fw-bold text-warning"><?php echo $leave + ($total - $present - $absent - $leave); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance List -->
        <div class="col-lg-12">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-calendar-day me-2"></i>
                        <h5 class="card-title mb-0">Daily Attendance Log: <?php echo date('d M Y', strtotime($date)); ?></h5>
                    </div>
                    <div class="btn-group">
                        <button class="btn btn-light btn-sm fw-bold px-3" onclick="window.print()"><i class="fas fa-print me-2"></i>PRINT PDF</button>
                    </div>
                </div>
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle datatable-premium">
                            <thead class="table-light">
                                <tr>
                                    <th width="60">S.No.</th>
                                    <th>Student Name</th>
                                    <th>Roll No</th>
                                    <th>Course</th>
                                    <th>Status</th>
                                    <th>Method</th>
                                    <th>Time</th>
                                    <th class="text-end" data-no-sort>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($students)): foreach($students as $index => $s): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo $s['photo'] ? BASE_URL . 'media/students/' . $s['photo'] : 'https://placehold.co/40'; ?>" class="rounded-circle border me-3" width="30" height="30" style="object-fit: cover;">
                                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($s['full_name']); ?></div>
                                        </div>
                                    </td>
                                    <td><div class="fw-bold text-primary"><?php echo htmlspecialchars($s['roll_number']); ?></div></td>
                                    <td>
                                        <div class="fw-bold text-dark small"><?php echo htmlspecialchars($s['course_name']); ?></div>
                                    </td>
                                    <td>
                                        <?php 
                                            $st = $s['current_status'];
                                            $badge = $st == 'present' ? 'success' : ($st == 'absent' ? 'danger' : ($st == 'leave' ? 'warning' : 'secondary'));
                                        ?>
                                        <span class="badge bg-<?php echo $badge; ?>-light text-<?php echo $badge; ?> border border-<?php echo $badge; ?> rounded-pill px-3">
                                            <?php echo strtoupper($st); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted small">
                                            <?php if($s['check_in'] !== '--'): ?>
                                                <i class="fas fa-qrcode me-1 text-info"></i> QR Scan
                                            <?php else: ?>
                                                <i class="fas fa-edit me-1 text-secondary"></i> Manual
                                            <?php endif; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark small">
                                            <?php echo $s['check_in'] !== '--' ? date('h:i A', strtotime($s['check_in'])) : '--'; ?>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <a href="view-student.php?id=<?php echo $s['student_id']; ?>" class="btn btn-sm btn-outline-primary btn-icon-only rounded-circle"><i class="fas fa-eye"></i></a>
                                    </td>
                                </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.btn-icon-only { width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; }
</style>

<?php include(__DIR__ . "/includes/footer.php"); ?>
