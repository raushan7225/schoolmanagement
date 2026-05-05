<?php
// admin/marks-entry-list.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Fetch filters
$exams = $pdo->query("SELECT id, exam_name FROM exams WHERE status = 1 ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
$courses = $pdo->query("SELECT id, name FROM courses WHERE status = 1 ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Filter logic
$f_exam = (int)($_GET['exam_id'] ?? 0);
$f_course = (int)($_GET['course_id'] ?? 0);
$f_search = trim($_GET['search'] ?? '');

$where = "WHERE 1=1";
$params = [];

if ($f_exam) { $where .= " AND sm.exam_id = ?"; $params[] = $f_exam; }
if ($f_course) { $where .= " AND a.course_id = ?"; $params[] = $f_course; }
if ($f_search) { 
    $where .= " AND (a.full_name LIKE ? OR a.roll_number LIKE ?)"; 
    $params[] = "%$f_search%";
    $params[] = "%$f_search%";
}

$sql = "
    SELECT sm.*, a.full_name, a.roll_number, a.mobile, sub.subject_name, sub.subject_code, e.exam_name, c.name as course_name,
    (SELECT grade_name FROM grade_ranges WHERE (sm.marks_obtained/sm.max_marks)*100 BETWEEN min_percentage AND max_percentage AND status = 1 LIMIT 1) as grade
    FROM student_marks sm
    JOIN admissions a ON sm.admission_id = a.id
    JOIN exams e ON sm.exam_id = e.id
    JOIN subjects sub ON sm.subject_id = sub.id
    JOIN courses c ON a.course_id = c.id
    $where
    ORDER BY sm.created_at DESC
";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$marks_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>Marks Entry List</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Exam Management</li>
            <li class="breadcrumb-item active">Marks Entry List</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <!-- Filter Section -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
                    <i class="fas fa-search text-primary me-2"></i>
                    <h5 class="card-title text-dark mb-0 fw-bold">Search Submitted Marks</h5>
                </div>
                <div class="card-body pt-4">
                    <form class="row g-3" method="GET" id="marksSearchForm">
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Select Exam</label>
                            <select class="form-select" name="exam_id">
                                <option value="">All Exams...</option>
                                <?php foreach($exams as $ex): ?>
                                    <option value="<?php echo $ex['id']; ?>" <?php echo $f_exam == $ex['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($ex['exam_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Select Course</label>
                            <select class="form-select" name="course_id">
                                <option value="">All Courses...</option>
                                <?php foreach($courses as $co): ?>
                                    <option value="<?php echo $co['id']; ?>" <?php echo $f_course == $co['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($co['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Search Student</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-user text-muted"></i></span>
                                <input type="text" class="form-control" name="search" placeholder="Enter Registration No. or Name" value="<?php echo htmlspecialchars($f_search); ?>">
                                <button class="btn btn-primary px-4 fw-bold" type="submit">SEARCH</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Marks List -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
                    <i class="fas fa-clipboard-list text-primary me-2"></i>
                    <h5 class="card-title text-dark mb-0 fw-bold">Submitted Marks List</h5>
                </div>
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle datatable-premium">
                            <thead class="table-light">
                                <tr>
                                    <th width="60">S.No.</th>
                                    <th>Exam</th>
                                    <th>Course</th>
                                    <th>Student Details</th>
                                    <th>Subject</th>
                                    <th>Marks</th>
                                    <th class="text-center">Grade</th>
                                    <th class="text-end" data-no-sort>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($marks_list)): $sn=1; foreach($marks_list as $m): ?>
                                <tr>
                                    <td><?php echo $sn++; ?></td>
                                    <td><span class="badge bg-primary-light text-primary border rounded-pill px-2"><?php echo htmlspecialchars($m['exam_name']); ?></span></td>
                                    <td><span class="badge bg-light text-dark border rounded-pill px-2"><?php echo htmlspecialchars($m['course_name']); ?></span></td>
                                    <td>
                                        <div class="fw-bold"><?php echo htmlspecialchars($m['full_name']); ?></div>
                                        <small class="text-primary fw-semibold"><?php echo htmlspecialchars($m['roll_number'] ?: 'PENDING'); ?></small>
                                    </td>
                                    <td class="fw-semibold">
                                        <?php echo htmlspecialchars($m['subject_name']); ?>
                                        <div class="small text-muted"><?php echo htmlspecialchars($m['subject_code']); ?></div>
                                    </td>
                                    <td>
                                        <div class="fw-bold fs-6 <?php echo $m['marks_obtained'] < $m['passing_marks'] ? 'text-danger' : 'text-success'; ?>">
                                            <?php echo number_format($m['marks_obtained'], 2); ?> / <?php echo number_format($m['max_marks'], 2); ?>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <?php if($m['grade']): ?>
                                            <span class="badge bg-success rounded-pill px-3"><?php echo $m['grade']; ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary rounded-pill px-3">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <a href="marks-entry.php?exam_id=<?php echo $m['exam_id']; ?>&course_id=<?php echo $m['course_id']; ?>&subject_id=<?php echo $m['subject_id']; ?>" class="btn btn-sm btn-outline-warning btn-icon me-1" title="Edit Marks"><i class="fas fa-edit"></i></a>
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
