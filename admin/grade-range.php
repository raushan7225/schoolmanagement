<?php
// admin/grade-range.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Fetch existing grade ranges
$stmt = $pdo->query("SELECT * FROM grade_ranges ORDER BY min_percentage DESC");
$grades = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>Grade System Configuration</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Exam Management</li>
            <li class="breadcrumb-item active">Grade Range</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <!-- Grade Range List -->
        <div class="col-12">
            <div class="card">
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle datatable-premium"
                               data-add-btn='{"text":"Add Grade Level","onclick":"openAddGrade()","icon":"fas fa-plus-circle"}'>
                            <thead class="table-light">
                                <tr>
                                    <th width="60">S.No.</th>
                                    <th>Grade</th>
                                    <th>Percentage Range</th>
                                    <th>Grade Point</th>
                                    <th>Remark</th>
                                    <th data-no-sort>Status</th>
                                    <th class="text-end" data-no-sort>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $sn=1; foreach($grades as $g): ?>
                                <tr id="graderow-<?php echo $g['id']; ?>">
                                    <td><?php echo $sn++; ?></td>
                                    <td><span class="badge bg-primary rounded-pill px-3 fs-6"><?php echo htmlspecialchars($g['grade_name']); ?></span></td>
                                    <td><div class="fw-bold text-dark"><?php echo number_format($g['min_percentage'], 2); ?>% - <?php echo number_format($g['max_percentage'], 2); ?>%</div></td>
                                    <td><div class="badge bg-light text-dark border px-3"><?php echo number_format($g['grade_point'], 1); ?></div></td>
                                    <td><small class="text-muted"><?php echo htmlspecialchars($g['remark']); ?></small></td>
                                    <td>
                                        <span class="badge bg-<?php echo $g['status'] == 1 ? 'success' : 'danger'; ?> rounded-pill px-3">
                                            <?php echo $g['status'] == 1 ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-warning btn-icon me-1" title="Edit" onclick='editGrade(<?php echo json_encode($g); ?>)'>
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger btn-icon" title="Delete" onclick="deleteGrade(<?php echo $g['id']; ?>, '<?php echo addslashes($g['grade_name']); ?>')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Add/Edit Modal -->
<div class="modal fade" id="modalGrade" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="modalTitle"><i class="fas fa-plus-circle me-2"></i>Add Grade Level</h5>
            <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                <i class="<?php echo v('theme_modal_close_icon', 'fas fa-times'); ?>"></i>
            </button>
        </div>
        <form id="gradeForm" novalidate>
            <input type="hidden" name="action" value="save_grade_range">
            <input type="hidden" name="id" id="grade-id" value="">
            <div class="modal-body px-3 py-4">
                <div class="mb-3">
                    <label class="form-label fw-bold">Grade Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="grade_name" id="g-name" placeholder="e.g. A+" required>
                    <div class="invalid-feedback">Please enter the grade name.</div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-bold">Min % <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="min_percentage" id="g-min" placeholder="e.g. 80" step="0.01" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold">Max % <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="max_percentage" id="g-max" placeholder="e.g. 100" step="0.01" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Grade Point</label>
                    <input type="number" class="form-control" name="grade_point" id="g-point" placeholder="e.g. 9.0" step="0.1">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Remark / Comment</label>
                    <input type="text" class="form-control" name="remark" id="g-remark" placeholder="e.g. Excellent">
                </div>
                <div class="mb-0">
                    <label class="form-label fw-bold">Status</label>
                    <select class="form-select" name="status" id="g-status">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer bg-light border-top py-2">
                <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">CANCEL</button>
                <button type="submit" class="btn btn-primary px-4 fw-bold" id="btn-save">
                    <i class="fas fa-save me-2"></i>SAVE GRADE
                </button>
            </div>
        </form>
    </div>
  </div>
</div>

<script>
'use strict';
const HANDLER = '<?php echo BASE_URL; ?>ajax/exam_handler.php';
let modalGrade;

document.addEventListener('DOMContentLoaded', function() {
    modalGrade = new bootstrap.Modal(document.getElementById('modalGrade'));
    const form = document.getElementById('gradeForm');

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!form.checkValidity()) { form.classList.add('was-validated'); return; }
        
        const btn = document.getElementById('btn-save');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>SAVING...';
        
        fetch(HANDLER, { method: 'POST', body: new FormData(form) })
        .then(r => r.json()).then(res => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-2"></i>SAVE GRADE';
            if(res.success) { location.reload(); }
            else alert(res.message);
        });
    });
});

function openAddGrade() {
    resetGradeForm();
    modalGrade.show();
}

function editGrade(g) {
    resetGradeForm();
    document.getElementById('grade-id').value = g.id;
    document.getElementById('g-name').value = g.grade_name;
    document.getElementById('g-min').value = g.min_percentage;
    document.getElementById('g-max').value = g.max_percentage;
    document.getElementById('g-point').value = g.grade_point;
    document.getElementById('g-remark').value = g.remark;
    document.getElementById('g-status').value = g.status;
    
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>Edit Grade Level';
    modalGrade.show();
}

function resetGradeForm() {
    const form = document.getElementById('gradeForm');
    form.reset();
    form.classList.remove('was-validated');
    document.getElementById('grade-id').value = '';
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-plus-circle me-2"></i>Add Grade Level';
}

function deleteGrade(id, name) {
    window.confirmDelete({
        target: name,
        onConfirm: function(modal, btn) {
            const fd = new FormData();
            fd.append('action', 'delete_grade_range');
            fd.append('id', id);
            fetch(HANDLER, { method: 'POST', body: fd })
            .then(r => r.json()).then(res => {
                modal.hide();
                if(res.success) { 
                    location.reload();
                    Swal.fire('Deleted!', 'Grade range has been removed.', 'success');
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            });
        }
    });
}
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>
