<?php
// admin/admit-download-date.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Fetch active exams
$exams = $pdo->query("SELECT id, exam_name FROM exams WHERE status = 1 ORDER BY exam_name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch existing windows
$stmt = $pdo->query("
    SELECT w.*, e.exam_name 
    FROM admit_download_settings w
    LEFT JOIN exams e ON w.exam_id = e.id
    ORDER BY w.start_date DESC
");
$windows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>Admit Download Window</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Card Management</li>
            <li class="breadcrumb-item active">Admit Download Date</li>
        </ol>
    </nav>
</div>

<section class="section">
<div class="row">

    <!-- ══ Left: Set Window Form ════════════════════════════════ -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <i class="fas fa-calendar-alt text-white me-2"></i>
                <h5 class="card-title text-white mb-0" id="form-title">Set Download Window</h5>
            </div>
            <div class="card-body pt-4">
                <form id="windowForm" novalidate>
                    <input type="hidden" name="action" id="win-action" value="save_download_window">
                    <input type="hidden" name="id" id="win-id" value="">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Exam <span class="text-danger">*</span></label>
                        <select class="form-select" name="exam_id" id="win-exam" required>
                            <option value="">Select Exam…</option>
                            <?php foreach($exams as $ex): ?>
                                <option value="<?php echo $ex['id']; ?>"><?php echo htmlspecialchars($ex['exam_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Please select an exam.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Start Date &amp; Time <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control" name="start_date" id="win-start" required>
                        <div class="invalid-feedback">Start date &amp; time is required.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">End Date &amp; Time <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control" name="end_date" id="win-end" required>
                        <div class="invalid-feedback">End date &amp; time is required.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Status</label>
                        <select class="form-select" name="status" id="win-status">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary" id="btn-save">
                            <i class="fas fa-save me-2"></i>Save Window
                        </button>
                        <button type="button" class="btn btn-outline-secondary d-none" id="btn-cancel" onclick="resetForm()">
                            Cancel Edit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ══ Right: Active Windows List ═══════════════════════════ -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <i class="fas fa-calendar-check text-white me-2"></i>
                <h5 class="card-title text-white mb-0">Active Download Windows</h5>
            </div>
            <div class="card-body pt-3">
                <div class="table-responsive">
                    <table class="table table-hover align-middle datatable-premium">
                        <thead class="table-light">
                            <tr>
                                <th>S.No.</th>
                                <th>Exam</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th data-no-sort>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($windows)): $sn=1; foreach($windows as $w): 
                                $is_expired = strtotime($w['end_date']) < time(); ?>
                            <tr id="winrow-<?php echo $w['id']; ?>">
                                <td><?php echo $sn++; ?></td>
                                <td><span class="fw-bold"><?php echo htmlspecialchars($w['exam_name']); ?></span></td>
                                <td><small><?php echo date('d M Y, h:i A', strtotime($w['start_date'])); ?></small></td>
                                <td><small><?php echo date('d M Y, h:i A', strtotime($w['end_date'])); ?></small></td>
                                <td>
                                    <?php if($is_expired): ?>
                                        <span class="badge bg-danger rounded-pill">Expired</span>
                                    <?php else: ?>
                                        <span class="badge bg-<?php echo $w['status'] == 1 ? 'success' : 'secondary'; ?> rounded-pill">
                                            <?php echo $w['status'] == 1 ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-warning btn-icon me-1" title="Edit" onclick='editWindow(<?php echo json_encode($w); ?>)'>
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger btn-icon" title="Delete" onclick="deleteWindow(<?php echo $w['id']; ?>, '<?php echo addslashes($w['exam_name']); ?>')">
                                        <i class="fas fa-trash"></i>
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
const HANDLER = '<?php echo BASE_URL; ?>ajax/card_handler.php';

(function () {
    'use strict';
    const form = document.getElementById('windowForm');
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!form.checkValidity()) { form.classList.add('was-validated'); return; }
        
        const btn = document.getElementById('btn-save');
        btn.disabled = true;
        
        fetch(HANDLER, { method: 'POST', body: new FormData(form) })
        .then(r => r.json()).then(res => {
            btn.disabled = false;
            if(res.success) { location.reload(); }
            else alert(res.message);
        });
    });
})();

function editWindow(w) {
    document.getElementById('win-id').value = w.id;
    document.getElementById('win-exam').value = w.exam_id;
    // Format dates for datetime-local (YYYY-MM-DDTHH:MM)
    document.getElementById('win-start').value = w.start_date.replace(' ', 'T').substring(0, 16);
    document.getElementById('win-end').value = w.end_date.replace(' ', 'T').substring(0, 16);
    document.getElementById('win-status').value = w.status;
    
    document.getElementById('form-title').textContent = 'Edit Download Window';
    document.getElementById('btn-cancel').classList.remove('d-none');
    window.scrollTo({top: 0, behavior: 'smooth'});
}

function resetForm() {
    document.getElementById('windowForm').reset();
    document.getElementById('win-id').value = '';
    document.getElementById('form-title').textContent = 'Set Download Window';
    document.getElementById('btn-cancel').classList.add('d-none');
}

function deleteWindow(id, name) {
    if(confirm(`Are you sure you want to delete window for "${name}"?`)) {
        const fd = new FormData();
        fd.append('action', 'delete_download_window');
        fd.append('id', id);
        fetch(HANDLER, { method: 'POST', body: fd })
        .then(r => r.json()).then(res => {
            if(res.success) { location.reload(); }
            else alert(res.message);
        });
    }
}
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>
