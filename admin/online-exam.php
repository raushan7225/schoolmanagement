<?php
// admin/online-exam.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Fetch courses for dropdown
$courses = $pdo->query("SELECT id, name FROM courses WHERE status = 1 ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch existing exams
$exams = $pdo->query("SELECT e.*, c.name as course_name FROM online_exams e JOIN courses c ON e.course_id = c.id ORDER BY e.start_datetime DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>Online Exam Configuration <span class="badge bg-danger ms-2" style="font-size: 0.6rem; vertical-align: middle;">PRO</span></h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Online Exam</li>
            <li class="breadcrumb-item active">Configuration</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <!-- Exam List -->
        <div class="col-12">
            <div class="card">
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle datatable-premium"
                               data-add-btn='{"text":"Schedule New Exam","onclick":"openAddExam()","icon":"fas fa-laptop-code"}'>
                            <thead class="table-light">
                                <tr>
                                    <th width="60">S.No.</th>
                                    <th>Exam Title</th>
                                    <th>Course</th>
                                    <th>Schedule Details</th>
                                    <th class="text-center" data-no-sort>Questions</th>
                                    <th class="text-center" data-no-sort>Status</th>
                                    <th class="text-end" data-no-sort>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $sn=1; foreach($exams as $e): 
                                    $qCount = $pdo->prepare("SELECT COUNT(*) FROM online_exam_questions WHERE exam_id = ?");
                                    $qCount->execute([$e['id']]);
                                    $totalQ = $qCount->fetchColumn();
                                ?>
                                <tr id="examrow-<?php echo $e['id']; ?>">
                                    <td><?php echo $sn++; ?></td>
                                    <td>
                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($e['title']); ?></div>
                                        <div class="small text-muted">Pass: <?php echo $e['pass_percentage']; ?>% | Dur: <?php echo $e['duration_mins']; ?>m</div>
                                    </td>
                                    <td><span class="badge bg-primary-light text-primary border border-primary px-3"><?php echo htmlspecialchars($e['course_name']); ?></span></td>
                                    <td>
                                        <div class="small fw-bold text-dark"><i class="far fa-calendar-alt me-1 text-primary"></i> <?php echo date('d M, h:i A', strtotime($e['start_datetime'])); ?></div>
                                        <div class="small text-muted"><i class="far fa-clock me-1"></i> Ends: <?php echo date('d M, h:i A', strtotime($e['end_datetime'])); ?></div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info-light text-info border border-info px-3"><?php echo $totalQ; ?> Questions</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-<?php echo $e['status'] == 1 ? 'success' : 'secondary'; ?> rounded-pill px-2">
                                            <?php echo $e['status'] == 1 ? 'Active' : 'Off'; ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="add-question.php?exam_id=<?php echo $e['id']; ?>" class="btn btn-sm btn-outline-primary btn-icon me-1" title="Manage Questions">
                                            <i class="fas fa-plus"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline-warning btn-icon me-1" title="Edit" onclick='editExam(<?php echo json_encode($e); ?>)'>
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger btn-icon" title="Delete" onclick="deleteExam(<?php echo $e['id']; ?>, '<?php echo addslashes($e['title']); ?>')">
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
<div class="modal fade" id="modalExam" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="modalTitle"><i class="fas fa-laptop-code me-2"></i>Schedule Online Exam</h5>
            <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                <i class="<?php echo v('theme_modal_close_icon', 'fas fa-times'); ?>"></i>
            </button>
        </div>
        <form id="examForm" novalidate>
            <input type="hidden" name="action" value="save_exam">
            <input type="hidden" name="id" id="exam-id" value="">
            <div class="modal-body px-3 py-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Exam Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="title" id="exam-title" placeholder="e.g. Mid-Term MCQ Assessment" required>
                        <div class="invalid-feedback">Please enter exam title.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Select Course <span class="text-danger">*</span></label>
                        <select class="form-select" name="course_id" id="exam-course" required>
                            <option value="">Select Course...</option>
                            <?php foreach($courses as $c): ?>
                                <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Please select a course.</div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Duration (Min) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="duration_mins" id="exam-duration" value="60" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Pass % <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="pass_percentage" id="exam-pass" value="40" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Immediate Result?</label>
                        <select class="form-select" name="immediate_result" id="exam-result">
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Status</label>
                        <select class="form-select" name="status" id="exam-status">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Start Date & Time <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control" name="start_datetime" id="exam-start" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">End Date & Time <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control" name="end_datetime" id="exam-end" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top py-2">
                <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">CANCEL</button>
                <button type="submit" class="btn btn-primary px-4 fw-bold" id="btn-save">
                    <i class="fas fa-save me-2"></i>SAVE EXAM
                </button>
            </div>
        </form>
    </div>
  </div>
</div>

<script>
'use strict';
const HANDLER = '<?php echo BASE_URL; ?>ajax/exam_handler.php';
let modalExam;

document.addEventListener('DOMContentLoaded', function() {
    modalExam = new bootstrap.Modal(document.getElementById('modalExam'));
    const form = document.getElementById('examForm');

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!form.checkValidity()) { form.classList.add('was-validated'); return; }
        
        const btn = document.getElementById('btn-save');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>SAVING...';
        
        fetch(HANDLER, { method: 'POST', body: new FormData(form) })
        .then(r => r.json()).then(res => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-2"></i>SAVE EXAM';
            if(res.success) { location.reload(); }
            else alert(res.message);
        });
    });
});

function openAddExam() {
    resetForm();
    modalExam.show();
}

function editExam(e) {
    resetForm();
    document.getElementById('exam-id').value = e.id;
    document.getElementById('exam-title').value = e.title;
    document.getElementById('exam-course').value = e.course_id;
    document.getElementById('exam-duration').value = e.duration_mins;
    document.getElementById('exam-pass').value = e.pass_percentage;
    document.getElementById('exam-start').value = e.start_datetime.replace(' ', 'T');
    document.getElementById('exam-end').value = e.end_datetime.replace(' ', 'T');
    document.getElementById('exam-result').value = e.immediate_result;
    document.getElementById('exam-status').value = e.status;
    
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>Edit Scheduled Exam';
    modalExam.show();
}

function resetForm() {
    const form = document.getElementById('examForm');
    form.reset();
    form.classList.remove('was-validated');
    document.getElementById('exam-id').value = '';
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-laptop-code me-2"></i>Schedule Online Exam';
}

function deleteExam(id, name) {
    window.confirmDelete({
        target: name,
        onConfirm: function(modal, btn) {
            const fd = new FormData();
            fd.append('action', 'delete_exam');
            fd.append('id', id);
            fetch(HANDLER, { method: 'POST', body: fd })
            .then(r => r.json()).then(res => {
                modal.hide();
                if(res.success) { 
                    location.reload();
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            });
        }
    });
}
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>
