<?php
// admin/attendance-report.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// 1. Fetch Filter Data
$centers = $pdo->query("SELECT id, center_name as name FROM franchises WHERE status = 1 ORDER BY center_name ASC")->fetchAll(PDO::FETCH_ASSOC);
$courses = $pdo->query("SELECT id, name FROM courses WHERE status = 1 ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// 2. Capture Filters
$from_date = $_GET['from_date'] ?? date('Y-m-01');
$to_date = $_GET['to_date'] ?? date('Y-m-d');
$course_id = (int)($_GET['course_id'] ?? 0);
$center_id = (int)($_GET['center_id'] ?? 0);
$search = trim($_GET['search'] ?? '');

// 3. Build Query Logic
$params = [$from_date, $to_date];
$filter_sql = "";

if ($course_id) { $filter_sql .= " AND adm.course_id = ?"; $params[] = $course_id; }
if ($center_id) { $filter_sql .= " AND adm.center_id = ?"; $params[] = $center_id; }
if ($search) { $filter_sql .= " AND adm.full_name LIKE ?"; $params[] = "%$search%"; }

// Query for stats first
$stats_sql = "
    SELECT 
        COUNT(*) as total_records,
        SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_count
    FROM (
        SELECT a.status, adm.course_id, adm.center_id, adm.full_name, a.attendance_date as log_date
        FROM student_attendance a
        JOIN admissions adm ON a.admission_id = adm.id
        UNION ALL
        SELECT q.status, adm.course_id, adm.center_id, adm.full_name, DATE(q.check_in_time) as log_date
        FROM qr_attendance q
        JOIN admissions adm ON q.student_id = adm.id
    ) as unified
    WHERE log_date BETWEEN ? AND ? $filter_sql
";

$statStmt = $pdo->prepare($stats_sql);
$statStmt->execute($params);
$stats = $statStmt->fetch(PDO::FETCH_ASSOC);

$total_rec = $stats['total_records'] ?: 1; 
$avg_percent = round(($stats['present_count'] / $total_rec) * 100, 1);

// Working Days
$daysStmt = $pdo->prepare("
    SELECT COUNT(DISTINCT log_date) as working_days FROM (
        SELECT attendance_date as log_date FROM student_attendance
        UNION
        SELECT DATE(check_in_time) as log_date FROM qr_attendance
    ) as dates WHERE log_date BETWEEN ? AND ?
");
$daysStmt->execute([$from_date, $to_date]);
$working_days = $daysStmt->fetch(PDO::FETCH_ASSOC)['working_days'] ?? 0;

// Fetch Detailed Logs
$logs_sql = "
    SELECT * FROM (
        SELECT 
            a.attendance_date as log_date, 
            adm.full_name, 
            adm.roll_number, 
            'Manual' as method, 
            '--' as log_time, 
            a.status,
            adm.course_id,
            adm.center_id
        FROM student_attendance a
        JOIN admissions adm ON a.admission_id = adm.id
        UNION ALL
        SELECT 
            DATE(q.check_in_time) as log_date, 
            adm.full_name, 
            adm.roll_number, 
            'QR Scan' as method, 
            TIME_FORMAT(q.check_in_time, '%h:%i %p') as log_time, 
            q.status,
            adm.course_id,
            adm.center_id
        FROM qr_attendance q
        JOIN admissions adm ON q.student_id = adm.id
    ) as unified
    WHERE log_date BETWEEN ? AND ? $filter_sql
    ORDER BY log_date DESC, log_time DESC
";

$logStmt = $pdo->prepare($logs_sql);
$logStmt->execute($params);
$logs = $logStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>Attendance Report</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Report Management</li>
            <li class="breadcrumb-item active">Attendance Report</li>
        </ol>
    </nav>
</div>

<section class="section">
    <!-- Advanced Filters -->
    <div class="card mb-4">
        <div class="card-body pt-3 pb-2">
            <form class="row g-3" method="GET">
                <div class="col-md-2">
                    <label class="form-label fw-bold small">From Date</label>
                    <input type="date" name="from_date" class="form-control" value="<?php echo $from_date; ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold small">To Date</label>
                    <input type="date" name="to_date" class="form-control" value="<?php echo $to_date; ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small">Course</label>
                    <select class="form-select" name="course_id">
                        <option value="">All Courses</option>
                        <?php foreach($courses as $c): ?>
                        <option value="<?php echo $c['id']; ?>" <?php echo $course_id == $c['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small">Franchise / Center</label>
                    <select class="form-select" name="center_id">
                        <option value="">All Centers</option>
                        <?php foreach($centers as $ct): ?>
                        <option value="<?php echo $ct['id']; ?>" <?php echo $center_id == $ct['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($ct['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-2"></i>Apply
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Analytical Stats -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-none border-start border-info border-4 mb-0">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="text-muted small fw-bold">AVG. ATTENDANCE %</div>
                        <div class="h5 mb-0 fw-bold text-info"><?php echo $avg_percent; ?>%</div>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-info" style="width: <?php echo $avg_percent; ?>%;"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-none border-start border-success border-4 mb-0">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="bg-success-light p-2 rounded text-success"><i class="fas fa-calendar-day fa-lg"></i></div>
                    <div>
                        <div class="text-muted small fw-bold">WORKING DAYS</div>
                        <div class="h5 mb-0 fw-bold"><?php echo $working_days; ?> Days</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-none border-start border-warning border-4 mb-0">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="bg-warning-light p-2 rounded text-warning"><i class="fas fa-clipboard-list fa-lg"></i></div>
                    <div>
                        <div class="text-muted small fw-bold">TOTAL PRESENT LOGS</div>
                        <div class="h5 mb-0 fw-bold text-warning"><?php echo $stats['present_count']; ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Historical Log -->
    <div class="card">
        <div class="card-body pt-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle datatable-premium">
                    <thead class="table-light">
                        <tr>
                            <th width="60">S.No.</th>
                            <th>Date</th>
                            <th>Student Name</th>
                            <th>Roll No</th>
                            <th>Method</th>
                            <th>Time In</th>
                            <th data-no-sort>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($logs as $index => $l): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><div class="fw-bold text-dark"><?php echo date('d M Y', strtotime($l['log_date'])); ?></div></td>
                            <td><div class="fw-bold"><?php echo htmlspecialchars($l['full_name']); ?></div></td>
                            <td><code class="text-primary fw-bold"><?php echo htmlspecialchars($l['roll_number']); ?></code></td>
                            <td><span class="badge bg-light text-dark border px-2"><?php echo $l['method']; ?></span></td>
                            <td><div class="fw-bold small"><?php echo $l['log_time']; ?></div></td>
                            <td>
                                <span class="badge bg-<?php echo $l['status'] == 'present' ? 'success' : ($l['status'] == 'absent' ? 'danger' : 'warning'); ?>-light text-<?php echo $l['status'] == 'present' ? 'success' : ($l['status'] == 'absent' ? 'danger' : 'warning'); ?> border border-<?php echo $l['status'] == 'present' ? 'success' : ($l['status'] == 'absent' ? 'danger' : 'warning'); ?> rounded-pill px-3">
                                    <?php echo ucfirst($l['status']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<?php include(__DIR__ . "/includes/footer.php"); ?>
