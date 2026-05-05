<?php
// admin/add-exam.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Fetch active filters
$sessions = $pdo->query("SELECT id, session_label FROM admission_sessions WHERE status = 1 ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
$categories = $pdo->query("SELECT id, name FROM course_categories WHERE status = 1 ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch existing exams
$stmt = $pdo->query("
    SELECT e.*, s.session_label, c.name as category_name
    FROM exams e
    LEFT JOIN admission_sessions s ON e.session_year_id = s.id
    LEFT JOIN course_categories c ON e.category_id = c.id
    ORDER BY e.created_at DESC
");
$exams = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>Exam Configuration</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Exam Management</li>
            <li class="breadcrumb-item active">Add Exam</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <!-- Add Exam Form -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <i class="fas fa-file-signature text-white me-2"></i>
                    <h5 class="card-title text-white mb-0" id="form-title">Create New Exam</h5>
                </div>
                <div class="card-body pt-4">
                    <form id="examForm" novalidate>
                        <input type="hidden" name="action" value="save_exam">
                        <input type="hidden" name="id" id="exam-id" value="">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Session Year <span class="text-danger">*</span></label>
                            <select class="form-select" name="session_year_id" id="ex-session" required>
                                <option value="">Select Session...</option>
                                <?php foreach($sessions as $sy): ?>
                                    <option value="<?php echo $sy['id']; ?>"><?php echo htmlspecialchars($sy['session_label']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Please select a session year.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Course Category <span class="text-danger">*</span></label>
                            <select class="form-select" name="category_id" id="ex-cat" required>
                                <option value="">Select Category...</option>
                                <?php foreach($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Please select a course category.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Exam Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="exam_name" id="ex-name" placeholder="e.g. Final Examination 2024" required>
                            <div class="invalid-feedback">Please enter the exam name.</div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold">Start Date</label>
                                <input type="date" class="form-control" name="start_date" id="ex-start">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold">End Date</label>
                                <input type="date" class="form-control" name="end_date" id="ex-end">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Description / Note</label>
                            <textarea class="form-control" name="description" id="ex-desc" rows="2" placeholder="Optional notes..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Status</label>
                            <select class="form-select" name="status" id="ex-status">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary" id="btn-save">
                                <i class="fas fa-save me-2"></i>Save Exam
                            </button>
                            <button type="button" class="btn btn-outline-secondary d-none" id="btn-cancel" onclick="resetForm()">
                                Cancel Edit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Exam List -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <i class="fas fa-list-alt text-white me-2"></i>
                    <h5 class="card-title text-white mb-0">Exam List</h5>
                </div>
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle datatable-premium">
                            <thead class="table-light">
                                <tr>
                                    <th width="60">S.No.</th>
                                    <th>Session</th>
                                    <th>Exam Name</th>
                                    <th>Category</th>
                                    <th>Date Range</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-end" data-no-sort>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($exams)): $sn=1; foreach($exams as $e): ?>
                                <tr id="examrow-<?php echo $e['id']; ?>">
                                    <td><?php echo $sn++; ?></td>
                                    <td><span class="badge bg-primary-light text-primary border rounded-pill px-3"><?php echo htmlspecialchars($e['session_label'] ?? 'N/A'); ?></span></td>
                                    <td>
                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($e['exam_name']); ?></div>
                                        <small class="text-muted"><?php echo htmlspecialchars($e['description']); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($e['category_name']); ?></td>
                                    <td><small class="fw-semibold"><?php echo $e['start_date'] ? date('d M', strtotime($e['start_date'])) : '-'; ?> - <?php echo $e['end_date'] ? date('d M', strtotime($e['end_date'])) : '-'; ?></small></td>
                                    <td class="text-center">
                                        <span class="badge bg-<?php echo $e['status'] == 1 ? 'success' : 'danger'; ?> rounded-pill px-3">
                                            <?php echo $e['status'] == 1 ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-warning btn-icon me-1" title="Edit" onclick='editExam(<?php echo json_encode($e); ?>)'>
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger btn-icon" title="Delete" onclick="deleteExam(<?php echo $e['id']; ?>, '<?php echo addslashes($e['exam_name']); ?>')">
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
    </div>
</section>

<script>
'use strict';
const HANDLER = '<?php echo BASE_URL; ?>ajax/exam_handler.php';

(function () {
    const form = document.getElementById('examForm');
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

function editExam(e) {
    document.getElementById('exam-id').value = e.id;
    document.getElementById('ex-session').value = e.session_year_id;
    document.getElementById('ex-cat').value = e.category_id;
    document.getElementById('ex-name').value = e.exam_name;
    document.getElementById('ex-start').value = e.start_date;
    document.getElementById('ex-end').value = e.end_date;
    document.getElementById('ex-desc').value = e.description;
    document.getElementById('ex-status').value = e.status;
    
    document.getElementById('form-title').textContent = 'Edit Exam';
    document.getElementById('btn-cancel').classList.remove('d-none');
    window.scrollTo({top: 0, behavior: 'smooth'});
}

function resetForm() {
    document.getElementById('examForm').reset();
    document.getElementById('exam-id').value = '';
    document.getElementById('form-title').textContent = 'Create New Exam';
    document.getElementById('btn-cancel').classList.add('d-none');
}

function deleteExam(id, name) {
    if(confirm(`Are you sure you want to delete "${name}"?`)) {
        const fd = new FormData();
        fd.append('action', 'delete_exam');
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
