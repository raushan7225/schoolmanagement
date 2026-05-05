<?php
// admin/online-marks.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

$exam_id = (int)($_GET['exam_id'] ?? 0);
$course_id = (int)($_GET['course_id'] ?? 0);

$where = "WHERE 1=1";
$params = [];
if ($exam_id) { $where .= " AND r.exam_id = ?"; $params[] = $exam_id; }
if ($course_id) { $where .= " AND e.course_id = ?"; $params[] = $course_id; }

// Fetch Results
$stmt = $pdo->prepare("
    SELECT r.*, adm.full_name as student_name, adm.roll_number, e.title as exam_title, c.name as course_name 
    FROM online_exam_results r 
    JOIN admissions adm ON r.student_id = adm.id 
    JOIN online_exams e ON r.exam_id = e.id 
    JOIN courses c ON e.course_id = c.id 
    $where 
    ORDER BY r.submitted_at DESC
");
$stmt->execute($params);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch Exams for Filter
$exams = $pdo->query("SELECT id, title FROM online_exams WHERE status = 1")->fetchAll(PDO::FETCH_ASSOC);
// Fetch Courses for Filter
$courses = $pdo->query("SELECT id, name as course_name FROM courses WHERE status = 1")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>Online Marks Report <span class="badge bg-danger ms-2" style="font-size: 0.6rem; vertical-align: middle;">PRO</span></h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Online Exam</li>
            <li class="breadcrumb-item active">Marks Report</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <!-- Filters -->
        <div class="col-lg-12 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-filter text-primary me-2"></i>
                        <h5 class="card-title text-dark mb-0 fw-bold">Search & Filter Reports</h5>
                    </div>
                </div>
                <div class="card-body pt-4">
                    <form class="row g-3 align-items-end" method="GET">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">EXAM TITLE</label>
                            <select class="form-select border-primary-light" name="exam_id">
                                <option value="">All Online Exams</option>
                                <?php foreach($exams as $ex): ?>
                                <option value="<?php echo $ex['id']; ?>" <?php echo $exam_id == $ex['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($ex['title']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">COURSE / PROGRAM</label>
                            <select class="form-select border-primary-light" name="course_id">
                                <option value="">All Courses</option>
                                <?php foreach($courses as $co): ?>
                                <option value="<?php echo $co['id']; ?>" <?php echo $course_id == $co['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($co['course_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary px-4 fw-bold flex-grow-1">
                                    <i class="fas fa-search me-2"></i>APPLY FILTER
                                </button>
                                <a href="online-marks.php" class="btn btn-outline-dark border px-4 fw-bold">
                                    <i class="fas fa-undo me-2"></i>RESET
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Results Table -->
        <div class="col-lg-12">
            <div class="card shadow-sm border-0">

                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle datatable-premium">
                            <thead class="table-light">
                                <tr>
                                    <th width="60">S.No.</th>
                                    <th>Student Information</th>
                                    <th>Examination</th>
                                    <th>Score Detail</th>
                                    <th>Performance</th>
                                    <th class="text-center">Verdict</th>
                                    <th class="text-end" data-no-sort>Options</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($results)): foreach($results as $index => $r): ?>
                                <tr>
                                    <td><span class="text-muted fw-bold"><?php echo $index + 1; ?></span></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle-sm bg-primary-light text-primary me-2 fw-bold">
                                                <?php echo strtoupper(substr($r['student_name'], 0, 1)); ?>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark"><?php echo htmlspecialchars($r['student_name']); ?></div>
                                                <small class="badge bg-light text-primary border border-primary-light x-small fw-normal">
                                                    Roll: <?php echo htmlspecialchars($r['roll_number']); ?>
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark small"><?php echo htmlspecialchars($r['exam_title']); ?></div>
                                        <div class="text-muted x-small italic"><?php echo htmlspecialchars($r['course_name']); ?></div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark mb-0">
                                            <?php echo $r['obtained_marks']; ?> 
                                            <span class="text-muted fw-normal x-small">/ <?php echo $r['total_marks']; ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center" style="min-width: 120px;">
                                            <div class="flex-grow-1 me-2">
                                                <div class="progress" style="height: 5px;">
                                                    <div class="progress-bar bg-<?php echo $r['percentage'] >= 75 ? 'success' : ($r['percentage'] >= 40 ? 'primary' : 'danger'); ?>" 
                                                         role="progressbar" style="width: <?php echo $r['percentage']; ?>%;"></div>
                                                </div>
                                            </div>
                                            <small class="fw-bold text-dark"><?php echo round($r['percentage']); ?>%</small>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <?php $is_pass = (strtolower($r['result_status']) == 'pass'); ?>
                                        <span class="badge bg-<?php echo $is_pass ? 'success' : 'danger'; ?>-light text-<?php echo $is_pass ? 'success' : 'danger'; ?> border border-<?php echo $is_pass ? 'success' : 'danger'; ?>-light rounded-pill px-3 shadow-none">
                                            <i class="fas <?php echo $is_pass ? 'fa-check-circle' : 'fa-times-circle'; ?> me-1"></i>
                                            <?php echo strtoupper($r['result_status']); ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group shadow-sm">
                                            <button class="btn btn-sm btn-white border btn-icon" title="View Response"><i class="fas fa-eye text-primary"></i></button>
                                            <button class="btn btn-sm btn-white border btn-icon" title="Print Certificate"><i class="fas fa-certificate text-warning"></i></button>
                                        </div>
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
.avatar-circle-sm {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
}
.bg-primary-light { background-color: rgba(65, 84, 241, 0.1); }
.bg-success-light { background-color: rgba(46, 202, 106, 0.1); }
.bg-danger-light { background-color: rgba(255, 66, 66, 0.1); }
.x-small { font-size: 0.7rem; }
</style>

<?php include(__DIR__ . "/includes/footer.php"); ?>
