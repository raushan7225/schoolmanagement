<?php
// admin/id-card-generate.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Fetch active filters
$centers = $pdo->query("SELECT id, code, name FROM centers WHERE status = 1 ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
$categories = $pdo->query("SELECT id, name FROM course_categories WHERE status = 1 ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
$courses = $pdo->query("SELECT id, name FROM courses WHERE status = 1 ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
$templates = $pdo->query("SELECT id, name FROM document_templates WHERE type = 'id_card' AND status = 1 ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Filter logic
$f_center = (int)($_GET['center_id'] ?? 0);
$f_cat = (int)($_GET['category_id'] ?? 0);
$f_course = (int)($_GET['course_id'] ?? 0);
$search = trim($_GET['search'] ?? '');

$where = "WHERE a.status = 1 AND a.approval_status = 'approved'";
$params = [];

if ($f_center) { $where .= " AND a.center_id = ?"; $params[] = $f_center; }
if ($f_cat) { $where .= " AND c.category_id = ?"; $params[] = $f_cat; }
if ($f_course) { $where .= " AND a.course_id = ?"; $params[] = $f_course; }
if ($search) { $where .= " AND (a.full_name LIKE ? OR a.roll_number LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }

$stmt = $pdo->prepare("
    SELECT a.*, c.name as course_name, cnt.code as center_code
    FROM admissions a
    LEFT JOIN courses c ON a.course_id = c.id
    LEFT JOIN centers cnt ON a.center_id = cnt.id
    $where
    ORDER BY a.full_name ASC
");
$stmt->execute($params);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>Generate ID Cards</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Card Management</li>
            <li class="breadcrumb-item active">Generate ID Card</li>
        </ol>
    </nav>
</div>

<section class="section">
<div class="row">

    <!-- ══ Filter Card ══════════════════════════════════════════ -->
    <div class="col-lg-12">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center">
                <i class="fas fa-search text-white me-2"></i>
                <h5 class="card-title text-white mb-0">Search &amp; Filter Students</h5>
            </div>
            <div class="card-body pt-4">
                <form class="row g-3 align-items-end" method="GET">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Center (Franchise)</label>
                        <select class="form-select" name="center_id">
                            <option value="">All Centers…</option>
                            <?php foreach($centers as $ct): ?>
                                <option value="<?php echo $ct['id']; ?>" <?php echo $f_center == $ct['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($ct['code'] . ' - ' . $ct['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Category</label>
                        <select class="form-select" name="category_id">
                            <option value="">All Categories…</option>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo $f_cat == $cat['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Course / Program</label>
                        <select class="form-select" name="course_id">
                            <option value="">All Courses…</option>
                            <?php foreach($courses as $co): ?>
                                <option value="<?php echo $co['id']; ?>" <?php echo $f_course == $co['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($co['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Search</label>
                        <input type="text" class="form-control" name="search" placeholder="Name/Roll No" value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-2"></i>Search
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ══ Results Table Card ════════════════════════════════════ -->
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="fas fa-users text-white me-2"></i>
                    <h5 class="card-title text-white mb-0">Students List</h5>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <select class="form-select form-select-sm bg-white" style="min-width:180px;" id="template-select">
                        <option value="">Select Template…</option>
                        <?php foreach($templates as $tp): ?>
                            <option value="<?php echo $tp['id']; ?>"><?php echo htmlspecialchars($tp['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn btn-sm btn-light fw-semibold" id="generateSelectedBtn">
                        <i class="fas fa-id-card me-1"></i>Generate Selected
                    </button>
                </div>
            </div>
            <div class="card-body pt-3">
                <div class="table-responsive">
                    <table class="table table-hover align-middle datatable-premium">
                        <thead class="table-light">
                            <tr>
                                <th style="width:40px;">
                                    <input class="form-check-input" type="checkbox" id="selectAll">
                                </th>
                                <th>S.No.</th>
                                <th>Roll Number</th>
                                <th>Student Name</th>
                                <th>Course</th>
                                <th>Center Code</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($students)): $sn=1; foreach($students as $s): ?>
                            <tr>
                                <td><input class="form-check-input row-check" type="checkbox" value="<?php echo $s['id']; ?>"></td>
                                <td><?php echo $sn++; ?></td>
                                <td><span class="fw-bold"><?php echo htmlspecialchars($s['roll_number'] ?: 'PENDING'); ?></span></td>
                                <td><?php echo htmlspecialchars($s['full_name']); ?></td>
                                <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($s['course_name']); ?></span></td>
                                <td><span class="badge border text-dark"><?php echo htmlspecialchars($s['center_code']); ?></span></td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-info btn-icon me-1" title="Preview ID Card" onclick="previewCard(<?php echo $s['id']; ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-success btn-icon" title="Generate ID Card" onclick="generateSingle(<?php echo $s['id']; ?>)">
                                        <i class="fas fa-id-card"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

</div><!-- /.row -->
</section>

<script>
'use strict';
const HANDLER = '<?php echo BASE_URL; ?>ajax/card_handler.php';

document.getElementById('selectAll').addEventListener('change', function () {
    document.querySelectorAll('.row-check').forEach(cb => cb.checked = this.checked);
});

function getSelectedIds() {
    return Array.from(document.querySelectorAll('.row-check:checked')).map(cb => cb.value);
}

document.getElementById('generateSelectedBtn').addEventListener('click', function() {
    const ids = getSelectedIds();
    const tempId = document.getElementById('template-select').value;
    
    if (ids.length === 0) { alert('Please select at least one student.'); return; }
    if (!tempId) { alert('Please select a template.'); return; }
    
    // In a real scenario, this would redirect to a PDF generation page or bulk AJAX
    window.open(`print-id-cards.php?ids=${ids.join(',')}&template_id=${tempId}`, '_blank');
});

function previewCard(id) {
    const tempId = document.getElementById('template-select').value;
    if (!tempId) { alert('Please select a template for preview.'); return; }
    window.open(`preview-id-card.php?id=${id}&template_id=${tempId}`, '_blank');
}

function generateSingle(id) {
    const tempId = document.getElementById('template-select').value;
    if (!tempId) { alert('Please select a template.'); return; }
    window.open(`print-id-cards.php?ids=${id}&template_id=${tempId}`, '_blank');
}
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>
