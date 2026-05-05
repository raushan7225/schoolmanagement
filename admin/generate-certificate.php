<?php
// admin/generate-certificate.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Fetch active filters
$exams = $pdo->query("SELECT id, exam_name FROM exams WHERE status = 1 ORDER BY exam_name ASC")->fetchAll(PDO::FETCH_ASSOC);
$centers = $pdo->query("SELECT id, center_code as code, center_name as name FROM franchises WHERE status = 1 ORDER BY center_name ASC")->fetchAll(PDO::FETCH_ASSOC);
$templates = $pdo->query("SELECT id, name FROM document_templates WHERE type = 'certificate' AND status = 1 ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Filter logic
$f_center = (int)($_GET['center_id'] ?? 0);

$where = "WHERE i.document_type = 'certificate' AND i.status = 1";
$params = [];

if ($f_center) { $where .= " AND a.center_id = ?"; $params[] = $f_center; }

$stmt = $pdo->prepare("
    SELECT i.*, a.full_name, a.roll_number, c.name as course_name, cnt.center_code, t.name as template_name
    FROM issued_documents i
    JOIN admissions a ON i.admission_id = a.id
    LEFT JOIN document_templates t ON i.template_id = t.id
    LEFT JOIN courses c ON a.course_id = c.id
    LEFT JOIN franchises cnt ON a.center_id = cnt.id
    $where
    ORDER BY i.issued_at DESC
");
$stmt->execute($params);
$issued = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>Generate Certificates</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Card Management</li>
            <li class="breadcrumb-item active">Generate Certificate</li>
        </ol>
    </nav>
</div>

<section class="section">
    <!-- Filter Bar -->
    <div class="card mb-4">
        <div class="card-body pt-3 pb-2">
            <form class="row g-2 align-items-end" method="GET">
                <div class="col-md-9">
                    <label class="form-label fw-bold small text-muted">FILTER BY CENTER (FRANCHISE)</label>
                    <select class="form-select select2-basic" name="center_id">
                        <option value="">All Centers…</option>
                        <?php foreach($centers as $ct): ?>
                            <option value="<?php echo $ct['id']; ?>" <?php echo $f_center == $ct['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($ct['code'] . ' - ' . $ct['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1 fw-bold"><i class="fas fa-filter me-2"></i>APPLY</button>
                    <a href="generate-certificate.php" class="btn btn-outline-secondary fw-bold">RESET</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-body pt-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle datatable-premium" data-add-btn='{"text":"Print Selected","onclick":"printSelected()","icon":"fas fa-print"}'>
                    <thead class="table-light">
                        <tr>
                            <th width="40" data-no-sort>
                                <div class="form-check"><input class="form-check-input" type="checkbox" id="selectAll"></div>
                            </th>
                            <th width="60">S.No.</th>
                            <th>Cert ID</th>
                            <th>Student Registration</th>
                            <th>Student Details</th>
                            <th>Course</th>
                            <th>Issue Date</th>
                            <th class="text-end" data-no-sort>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $sn=1; foreach($issued as $i): ?>
                        <tr>
                            <td><div class="form-check"><input class="form-check-input row-check" type="checkbox" value="<?php echo $i['id']; ?>"></div></td>
                            <td><?php echo $sn++; ?></td>
                            <td><code class="text-primary fw-bold"><?php echo htmlspecialchars($i['unique_id']); ?></code></td>
                            <td><div class="fw-bold text-dark"><?php echo htmlspecialchars($i['roll_number']); ?></div></td>
                            <td>
                                <div class="fw-bold text-dark fs-6"><?php echo strtoupper($i['full_name']); ?></div>
                                <div class="small text-muted">Center: <?php echo htmlspecialchars($i['center_code']); ?></div>
                            </td>
                            <td><span class="badge bg-light text-dark border border-secondary px-3"><?php echo htmlspecialchars($i['course_name']); ?></span></td>
                            <td><div class="small fw-bold text-dark"><?php echo date('d M Y', strtotime($i['issued_at'])); ?></div></td>
                            <td class="text-end">
                                <div class="btn-group shadow-sm border rounded">
                                    <button class="btn btn-sm btn-outline-info border-0 px-2" title="Preview Certificate" onclick="previewCertificate(<?php echo $i['id']; ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-success border-0 px-2" title="Print Certificate" onclick="printSingle(<?php echo $i['id']; ?>)">
                                        <i class="fas fa-print"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</section>

<script>
'use strict';

document.addEventListener('DOMContentLoaded', () => {
    const selAll = document.getElementById('selectAll');
    if(selAll) {
        selAll.addEventListener('change', function () {
            document.querySelectorAll('.row-check').forEach(cb => cb.checked = this.checked);
        });
    }

});

function printSelected() {
    const ids = Array.from(document.querySelectorAll('.row-check:checked')).map(cb => cb.value);
    if (ids.length === 0) { alert('Please select at least one certificate.'); return; }
    window.open(`print-certificates.php?ids=${ids.join(',')}`, '_blank');
}

function previewCertificate(id) {
    window.open(`preview-certificate.php?id=${id}`, '_blank');
}

function printSingle(id) {
    window.open(`print-certificates.php?ids=${id}`, '_blank');
}
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>
