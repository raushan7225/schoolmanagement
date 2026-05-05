<?php
// admin/subject-list.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Fetch active courses for dropdowns
$courses = $pdo->query("SELECT id, name FROM courses WHERE status = 1 ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
$academic_units = $pdo->query("SELECT * FROM academic_years WHERE status = 1 ORDER BY year_type DESC, year_label ASC")->fetchAll(PDO::FETCH_ASSOC);

// Filter Logic
$filter_course = (int)($_GET['filter_course'] ?? 0);
$filter_type = $_GET['filter_type'] ?? '';

$where = "WHERE 1=1";
$params = [];
if ($filter_course) {
    $where .= " AND s.course_id = ?";
    $params[] = $filter_course;
}
if ($filter_type) {
    $where .= " AND s.subject_type = ?";
    $params[] = $filter_type;
}

// Fetch Subjects
$stmt = $pdo->prepare("
    SELECT s.*, c.name as course_name 
    FROM subjects s
    LEFT JOIN courses c ON s.course_id = c.id
    $where
    ORDER BY c.name ASC, s.year_sem ASC, s.subject_name ASC
");
$stmt->execute($params);
$subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>Subject Management</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Course Management</li>
            <li class="breadcrumb-item active">All Subjects</li>
        </ol>
    </nav>
</div>

<section class="section">
<div class="row">



    <!-- ══ Right: Subject List ══════════════════════════════════ -->
    <div class="col-12">

        <!-- Filter Bar -->
        <div class="card mb-3">
            <div class="card-body py-2">
                <form class="row g-2 align-items-center" method="GET">
                    <div class="col-md-5">
                        <select name="filter_course" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">All Courses</option>
                            <?php foreach($courses as $c): ?>
                                <option value="<?php echo $c['id']; ?>" <?php echo $filter_course == $c['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select name="filter_type" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">All Types</option>
                            <option value="Theory" <?php echo $filter_type == 'Theory' ? 'selected' : ''; ?>>Theory</option>
                            <option value="Practical" <?php echo $filter_type == 'Practical' ? 'selected' : ''; ?>>Practical</option>
                            <option value="Both" <?php echo $filter_type == 'Both' ? 'selected' : ''; ?>>Both</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary flex-grow-1 fw-bold"><i class="fas fa-filter me-2"></i>APPLY</button>
                        <a href="subject-list.php" class="btn btn-sm btn-outline-secondary fw-bold">RESET</a>
                    </div>
                </form>
            </div>
        </div>
        <!-- Table Card -->
        <div class="card">
            <div class="card-body pt-3">
                <div class="table-responsive">
                    <table class="table table-hover align-middle datatable-premium"
                           data-add-btn='{"text":"Add New Subject","onclick":"openAddSubject()","icon":"fas fa-plus"}'>
                        <thead class="table-light">
                            <tr>
                                <th>S.No.</th>
                                <th>Subject Info</th>
                                <th>Course / Year</th>
                                <th data-no-sort>Type</th>
                                <th data-no-sort>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $sn=1; foreach($subjects as $s): ?>
                            <tr id="subrow-<?php echo $s['id']; ?>">
                                <td><?php echo $sn++; ?></td>
                                <td>
                                    <div class="fw-bold"><?php echo htmlspecialchars($s['subject_name']); ?></div>
                                    <div class="text-muted small">Code: <?php echo $s['subject_code'] ?: 'N/A'; ?> &nbsp;|&nbsp; Lessons: <?php echo $s['total_lessons']; ?></div>
                                </td>
                                <td>
                                    <div class="small fw-semibold"><?php echo htmlspecialchars($s['course_name']); ?></div>
                                    <span class="badge bg-light text-dark border"><?php echo htmlspecialchars($s['year_sem'] ?: 'N/A'); ?></span>
                                </td>
                                <td>
                                    <?php 
                                    $typeClass = ($s['subject_type'] == 'Theory') ? 'info' : (($s['subject_type'] == 'Practical') ? 'warning' : 'primary');
                                    ?>
                                    <span class="badge bg-<?php echo $typeClass; ?>-light text-<?php echo $typeClass; ?> rounded-pill"><?php echo $s['subject_type']; ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo $s['status'] == 1 ? 'success' : 'danger'; ?> rounded-pill">
                                        <?php echo $s['status'] == 1 ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-info btn-icon me-1" title="View" onclick="viewSubject(<?php echo $s['id']; ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-warning btn-icon me-1" title="Edit" onclick='editSubject(<?php echo json_encode($s); ?>)'>
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger btn-icon" title="Delete" onclick="deleteSubject(<?php echo $s['id']; ?>, '<?php echo addslashes($s['subject_name']); ?>')">
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

</div><!-- /.row -->

<!-- Add/Edit Modal -->
<div class="modal fade" id="modalSub" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="subModalTitle"><i class="fas fa-plus-circle me-2"></i>Add New Subject</h5>
            <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                <i class="<?php echo v('theme_modal_close_icon', 'fas fa-times'); ?>"></i>
            </button>
        </div>
        <form id="subjectForm" novalidate>
            <input type="hidden" name="action" id="sub-action" value="add_subject">
            <input type="hidden" name="id" id="sub-id" value="">
            <div class="modal-body px-3 py-2" style="max-height: 80vh; overflow-y: auto;">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-bold">Course <span class="text-danger">*</span></label>
                        <select class="form-select" name="course_id" id="sub-course" required>
                            <option value="">Select Course…</option>
                            <?php foreach($courses as $c): ?>
                                <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Please select a course.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Year / Semester</label>
                        <select class="form-select" name="year_sem" id="sub-year">
                            <option value="">Select Year/Semester</option>
                            <?php foreach($academic_units as $au): ?>
                                <option value="<?php echo htmlspecialchars($au['year_label']); ?>"><?php echo htmlspecialchars($au['year_label']); ?> (<?php echo $au['year_type']; ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Subject Type</label>
                        <select class="form-select" name="subject_type" id="sub-type">
                            <option value="Theory">Theory</option>
                            <option value="Practical">Practical</option>
                            <option value="Both">Both</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Subject Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="subject_name" id="sub-name" placeholder="e.g. Computer Fundamentals" required>
                        <div class="invalid-feedback">Subject name is required.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Subject Code</label>
                        <input type="text" class="form-control" name="subject_code" id="sub-code" placeholder="e.g. DCA-101">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Total Lessons</label>
                        <input type="number" class="form-control" name="total_lessons" id="sub-lessons" value="0" min="0">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Status</label>
                        <select class="form-select" name="status" id="sub-status">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary px-4" id="btn-save">
                    <i class="fas fa-save me-1"></i>Save Subject
                </button>
            </div>
        </form>
    </div>
  </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="modalViewSub" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title"><i class="fas fa-eye me-2"></i>Subject Details</h5>
            <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                <i class="<?php echo v('theme_modal_close_icon', 'fas fa-times'); ?>"></i>
            </button>
        </div>
        <div class="modal-body px-3 py-2" id="view-sub-body">
            <div class="text-center py-5"><div class="spinner-border text-primary"></div></div>
        </div>
    </div>
  </div>
</div>

</section>

<script>
const HANDLER = '<?php echo BASE_URL; ?>ajax/course_handler.php';
let modalSub, form;

document.addEventListener('DOMContentLoaded', function() {
    modalSub = new bootstrap.Modal(document.getElementById('modalSub'));
    form = document.getElementById('subjectForm');

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!form.checkValidity()) { form.classList.add('was-validated'); return; }
        
        const btn = document.getElementById('btn-save');
        btn.disabled = true;
        
        fetch(HANDLER, { method: 'POST', body: new FormData(form) })
        .then(r => r.json()).then(res => {
            btn.disabled = false;
            if(res.success) { 
                Swal.fire({ icon: 'success', title: 'Success', text: res.message, timer: 1500, showConfirmButton: false })
                .then(() => location.reload()); 
            }
            else Swal.fire('Error', res.message, 'error');
        });
    });

    document.getElementById('modalSub').addEventListener('hidden.bs.modal', resetForm);
});

function editSubject(s) {
    if(!form) form = document.getElementById('subjectForm');
    form.reset();
    document.getElementById('sub-id').value = s.id;
    document.getElementById('sub-action').value = 'edit_subject';
    document.getElementById('sub-course').value = s.course_id;
    document.getElementById('sub-year').value = s.year_sem;
    document.getElementById('sub-name').value = s.subject_name;
    document.getElementById('sub-code').value = s.subject_code;
    document.getElementById('sub-type').value = s.subject_type;
    document.getElementById('sub-lessons').value = s.total_lessons;
    document.getElementById('sub-status').value = s.status;
    document.getElementById('subModalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>Edit Subject';
    modalSub.show();
}

function openAddSubject() {
    resetForm();
    modalSub.show();
}

function resetForm() {
    form.reset();
    document.getElementById('sub-id').value = '';
    document.getElementById('sub-action').value = 'add_subject';
    document.getElementById('subModalTitle').innerHTML = '<i class="fas fa-plus-circle me-2"></i>Add New Subject';
}

function viewSubject(id) {
    document.getElementById('view-sub-body').innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>';
    new bootstrap.Modal(document.getElementById('modalViewSub')).show();
    
    fetch(`${HANDLER}?action=view_subject&id=${id}`)
    .then(r => r.json()).then(res => {
        if(!res.success) {
            document.getElementById('view-sub-body').innerHTML = `<p class="text-danger p-3">${res.message}</p>`;
            return;
        }
        const d = res.data;
        document.getElementById('view-sub-body').innerHTML = `
            <div class="enquiry-view-premium p-1">
                <div class="view-section mb-3">
                    <div class="view-section-header"><i class="fas fa-book me-2"></i>SUBJECT INFORMATION</div>
                    <div class="row g-3 px-3 py-2">
                        <div class="col-12">
                            <label class="view-label">Subject Name</label>
                            <div class="view-value text-primary fw-bold fs-5">${d.subject_name}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="view-label">Course</label>
                            <div class="view-value">${d.course_name}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="view-label">Code</label>
                            <div class="view-value">${d.subject_code || '—'}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="view-label">Year/Semester</label>
                            <div class="view-value">${d.year_sem || '—'}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="view-label">Type</label>
                            <div class="view-value">${d.subject_type}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="view-label">Lessons</label>
                            <div class="view-value">${d.total_lessons}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="view-label">Status</label>
                            <div class="view-value">
                                <span class="badge bg-${d.status == 1 ? 'success' : 'danger'} rounded-pill">
                                    ${d.status == 1 ? 'Active' : 'Inactive'}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
}

function deleteSubject(id, name) {
    window.confirmDelete({
        target: name,
        onConfirm: function(modal, btn) {
            const fd = new FormData();
            fd.append('action', 'delete_subject');
            fd.append('id', id);
            fetch(HANDLER, { method: 'POST', body: fd })
            .then(r => r.json()).then(res => {
                modal.hide();
                if(res.success) { 
                    location.reload();
                    Swal.fire('Deleted!', 'Subject has been removed.', 'success');
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            });
        }
    });
}
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>
