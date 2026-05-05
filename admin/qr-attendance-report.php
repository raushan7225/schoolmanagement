<?php
// admin/qr-attendance-report.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

$date = $_GET['date'] ?? date('Y-m-d');
$course_id = (int)($_GET['course_id'] ?? 0);
$search = trim($_GET['search'] ?? '');

$where = "WHERE DATE(a.check_in_time) = ?";
$params = [$date];

if ($course_id) { $where .= " AND adm.course_id = ?"; $params[] = $course_id; }
if ($search) { $where .= " AND (adm.full_name LIKE ? OR adm.roll_number LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }

// Fetch Logs
$stmt = $pdo->prepare("
    SELECT a.*, adm.full_name as student_name, adm.roll_number, c.name as course_name 
    FROM qr_attendance a 
    JOIN admissions adm ON a.student_id = adm.id 
    JOIN courses c ON adm.course_id = c.id 
    $where 
    ORDER BY a.check_in_time DESC
");
$stmt->execute($params);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch Courses for Filter
$courses = $pdo->query("SELECT id, name as course_name FROM courses WHERE status = 1")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>QR Attendance Report <span class="badge bg-danger ms-2" style="font-size: 0.6rem; vertical-align: middle;">PRO</span></h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">QR Attendance</li>
            <li class="breadcrumb-item active">Report</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <!-- Filters -->
        <div class="col-lg-12 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex align-items-center">
                    <i class="fas fa-filter text-white me-2"></i>
                    <h5 class="card-title text-white mb-0">Filter Attendance</h5>
                </div>
                <div class="card-body pt-4">
                    <form class="row g-3" method="GET">
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Select Date</label>
                            <input type="date" name="date" class="form-control" value="<?php echo $date; ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Course</label>
                            <select class="form-select" name="course_id">
                                <option value="">All Courses</option>
                                <?php foreach($courses as $co): ?>
                                <option value="<?php echo $co['id']; ?>" <?php echo $course_id == $co['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($co['course_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Student Name/ID</label>
                            <input type="text" name="search" class="form-control" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search me-1"></i>Filter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Attendance Stats -->
        <div class="col-lg-12 mb-4">
            <div class="row">
                <div class="col-md-4">
                    <div class="card shadow-none border-start border-success border-4 bg-white">
                        <div class="card-body p-3">
                            <div class="text-muted small fw-bold">TOTAL SCANS TODAY</div>
                            <div class="h4 mb-0 fw-bold text-success"><?php echo count($logs); ?> Students</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Table -->
        <div class="col-lg-12">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-calendar-check text-white me-2"></i>
                        <h5 class="card-title text-white mb-0">Attendance Log</h5>
                    </div>
                    <button class="btn btn-light btn-sm fw-bold" onclick="window.print()"><i class="fas fa-print me-2"></i>PRINT REPORT</button>
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
                                    <th>Time</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th class="text-end" data-no-sort>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($logs)): foreach($logs as $index => $l): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><div class="fw-bold text-dark"><?php echo htmlspecialchars($l['student_name']); ?></div></td>
                                    <td><div class="fw-bold text-primary small"><?php echo htmlspecialchars($l['roll_number']); ?></div></td>
                                    <td><div class="small fw-bold"><?php echo htmlspecialchars($l['course_name']); ?></div></td>
                                    <td><div class="fw-bold text-dark"><?php echo date('h:i A', strtotime($l['check_in_time'])); ?></div></td>
                                    <td><div class="small text-muted"><?php echo date('d M Y', strtotime($l['check_in_time'])); ?></div></td>
                                    <td><span class="badge bg-success-light text-success border border-success rounded-pill px-3">Present</span></td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-primary btn-icon" title="View Full Month"><i class="fas fa-chart-line"></i></button>
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

<?php include(__DIR__ . "/includes/footer.php"); ?>
