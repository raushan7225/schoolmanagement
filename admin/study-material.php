<?php
// admin/study-material.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Fetch active courses for dropdowns
$courses = $pdo->query("SELECT id, name FROM courses WHERE status = 1 ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Filter Logic
$filter_course = (int)($_GET['filter_course'] ?? 0);
$search = trim($_GET['search'] ?? '');

$where = "WHERE 1=1";
$params = [];
if ($filter_course) {
    $where .= " AND m.course_id = ?";
    $params[] = $filter_course;
}
if ($search) {
    $where .= " AND m.title LIKE ?";
    $params[] = "%$search%";
}

// Fetch Materials
$stmt = $pdo->prepare("
    SELECT m.*, c.name as course_name, s.subject_name
    FROM study_materials m
    LEFT JOIN courses c ON m.course_id = c.id
    LEFT JOIN subjects s ON m.subject_id = s.id
    $where
    ORDER BY m.created_at DESC
");
$stmt->execute($params);
$materials = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>Study Material</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Course Management</li>
            <li class="breadcrumb-item active">Study Material</li>
        </ol>
    </nav>
</div>

<section class="section">
<div class="row">
<div class="col-12">

    <div class="col-12 mb-3 text-end">
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalMat">
            <i class="fas fa-plus me-1"></i>Upload New Material
        </button>
    </div>

    <!-- ══ Filter Bar ══════════════════════════════════════════════ -->
    <div class="card mb-3">
        <div class="card-body py-3">
            <form class="row g-2 align-items-center" method="GET">
                <div class="col-md-4">
                    <select name="filter_course" class="form-select form-select-sm">
                        <option value="">All Courses</option>
                        <?php foreach($courses as $c): ?>
                            <option value="<?php echo $c['id']; ?>" <?php echo $filter_course == $c['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($c['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-5">
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" class="form-control" placeholder="Search title…" value="<?php echo htmlspecialchars($search); ?>">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                        <a href="study-material.php" class="btn btn-outline-secondary">Clear</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- ══ Resources List Card ════════════════════════════════════ -->
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <i class="fas fa-folder-open me-2"></i>
            <h5 class="card-title mb-0">Shared Resources</h5>
        </div>
        <div class="card-body pt-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle datatable-premium">
                    <thead class="table-light">
                        <tr>
                            <th>S.No.</th>
                            <th>Material Title</th>
                            <th>Course / Subject</th>
                            <th data-no-sort>Status</th>
                            <th>Date Added</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($materials)): $sn=1; foreach($materials as $m): ?>
                        <tr id="matrow-<?php echo $m['id']; ?>">
                            <td><?php echo $sn++; ?></td>
                            <td>
                                <div class="fw-bold text-primary-theme"><?php echo htmlspecialchars($m['title']); ?></div>
                                <?php if($m['file_path']): ?>
                                    <small class="text-muted"><i class="fas fa-file-pdf me-1"></i><?php echo $m['file_path']; ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="small fw-semibold"><?php echo htmlspecialchars($m['course_name']); ?></div>
                                <small class="text-muted"><?php echo htmlspecialchars($m['subject_name'] ?: 'General'); ?></small>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo $m['status'] == 1 ? 'success' : 'danger'; ?> rounded-pill">
                                    <?php echo $m['status'] == 1 ? 'Active' : 'Inactive'; ?>
                                </span>
                            </td>
                            <td><small><?php echo date('d M Y', strtotime($m['created_at'])); ?></small></td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-info btn-icon me-1" title="View" onclick="viewMaterial(<?php echo $m['id']; ?>)">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <?php if($m['file_path']): ?>
                                <a href="<?php echo BASE_URL . 'media/materials/' . $m['file_path']; ?>" class="btn btn-sm btn-outline-primary btn-icon me-1" title="Download" target="_blank">
                                    <i class="fas fa-download"></i>
                                </a>
                                <?php endif; ?>
                                <button class="btn btn-sm btn-outline-warning btn-icon me-1" title="Edit" onclick='editMaterial(<?php echo json_encode($m); ?>)'>
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger btn-icon" title="Delete" onclick="deleteMaterial(<?php echo $m['id']; ?>, '<?php echo addslashes($m['title']); ?>')">
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

<!-- Add/Edit Modal -->
<div class="modal fade" id="modalMat" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="matModalTitle"><i class="fas fa-cloud-upload-alt me-2"></i>Upload Resource</h5>
            <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                <i class="<?php echo v('theme_modal_close_icon', 'fas fa-times'); ?>"></i>
            </button>
        </div>
        <form id="materialForm" novalidate enctype="multipart/form-data">
            <input type="hidden" name="action" id="mat-action" value="add_material">
            <input type="hidden" name="id" id="mat-id" value="">
            <div class="modal-body px-3 py-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Course <span class="text-danger">*</span></label>
                        <select class="form-select" name="course_id" id="mat-course" required onchange="loadSubjects(this.value)">
                            <option value="">Select Course…</option>
                            <?php foreach($courses as $c): ?>
                                <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Please select a course.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Subject (Optional)</label>
                        <select class="form-select" name="subject_id" id="mat-subject">
                            <option value="">All Subjects / General</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Material Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="title" id="mat-title" placeholder="e.g. MS Office Guide" required>
                        <div class="invalid-feedback">Title is required.</div>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-bold">Upload File</label>
                        <input type="file" class="form-control" name="material_file" id="mat-file" accept=".pdf,.doc,.docx,.ppt,.pptx">
                        <small class="text-muted">Allowed: PDF, DOC, DOCX, PPT, PPTX</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Status</label>
                        <select class="form-select" name="status" id="mat-status">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Short Description</label>
                        <textarea class="form-control" name="description" id="mat-desc" rows="3" placeholder="What's inside this material?"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary px-4" id="btn-save">
                    <i class="fas fa-upload me-1"></i>Save Material
                </button>
            </div>
        </form>
    </div>
  </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="modalViewMat" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title"><i class="fas fa-eye me-2"></i>Material Details</h5>
            <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                <i class="<?php echo v('theme_modal_close_icon', 'fas fa-times'); ?>"></i>
            </button>
        </div>
        <div class="modal-body px-3 py-2" id="view-mat-body">
            <div class="text-center py-5"><div class="spinner-border text-primary"></div></div>
        </div>
    </div>
  </div>
</div>

</section>

<script>
const HANDLER = '<?php echo BASE_URL; ?>ajax/course_handler.php';
let modalMat, form;

document.addEventListener('DOMContentLoaded', function() {
    modalMat = new bootstrap.Modal(document.getElementById('modalMat'));
    form = document.getElementById('materialForm');

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

    document.getElementById('modalMat').addEventListener('hidden.bs.modal', resetForm);
});

function loadSubjects(courseId, selectedId = 0) {
    const subSel = document.getElementById('mat-subject');
    subSel.innerHTML = '<option value="">Loading...</option>';
    if(!courseId) { subSel.innerHTML = '<option value="">All Subjects / General</option>'; return; }

    fetch(`${HANDLER}?action=get_subjects&course_id=${courseId}`)
    .then(r => r.json()).then(res => {
        let html = '<option value="">All Subjects / General</option>';
        if(res.success) {
            res.data.forEach(s => {
                html += `<option value="${s.id}" ${s.id == selectedId ? 'selected' : ''}>${s.subject_name}</option>`;
            });
        }
        subSel.innerHTML = html;
    });
}

function editMaterial(m) {
    if(!form) form = document.getElementById('materialForm');
    form.reset();
    document.getElementById('mat-id').value = m.id;
    document.getElementById('mat-action').value = 'edit_material';
    document.getElementById('mat-course').value = m.course_id;
    document.getElementById('mat-title').value = m.title;
    document.getElementById('mat-desc').value = m.description;
    document.getElementById('mat-status').value = m.status;
    
    loadSubjects(m.course_id, m.subject_id);
    document.getElementById('matModalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>Edit Resource';
    modalMat.show();
}

function resetForm() {
    form.reset();
    document.getElementById('mat-id').value = '';
    document.getElementById('mat-action').value = 'add_material';
    document.getElementById('matModalTitle').innerHTML = '<i class="fas fa-cloud-upload-alt me-2"></i>Upload Resource';
    document.getElementById('mat-subject').innerHTML = '<option value="">All Subjects / General</option>';
}

function viewMaterial(id) {
    document.getElementById('view-mat-body').innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>';
    new bootstrap.Modal(document.getElementById('modalViewMat')).show();
    
    fetch(`${HANDLER}?action=view_material&id=${id}`)
    .then(r => r.json()).then(res => {
        if(!res.success) {
            document.getElementById('view-mat-body').innerHTML = `<p class="text-danger p-3">${res.message}</p>`;
            return;
        }
        const d = res.data;
        document.getElementById('view-mat-body').innerHTML = `
            <div class="enquiry-view-premium p-1">
                <div class="view-section mb-3">
                    <div class="view-section-header">MATERIAL INFORMATION</div>
                    <div class="row g-3 px-3 py-2">
                        <div class="col-12">
                            <label class="view-label">Title</label>
                            <div class="view-value text-primary fw-bold fs-5">${d.title}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="view-label">Course</label>
                            <div class="view-value">${d.course_name}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="view-label">Subject</label>
                            <div class="view-value">${d.subject_name || 'General'}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="view-label">Status</label>
                            <div class="view-value">
                                <span class="badge bg-${d.status == 1 ? 'success' : 'danger'} rounded-pill">
                                    ${d.status == 1 ? 'Active' : 'Inactive'}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="view-label">Added On</label>
                            <div class="view-value">${new Date(d.created_at).toLocaleDateString('en-GB', {day:'2-digit', month:'short', year:'numeric'})}</div>
                        </div>
                        <div class="col-12">
                            <label class="view-label">Description</label>
                            <div class="view-value">${d.description || 'No description provided.'}</div>
                        </div>
                        ${d.file_path ? `
                        <div class="col-12 mt-2">
                            <a href="${HANDLER.replace('ajax/course_handler.php', 'media/materials/')}${d.file_path}" target="_blank" class="btn btn-outline-primary w-100">
                                <i class="fas fa-download me-2"></i>Download Resource
                            </a>
                        </div>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;
    });
}

function deleteMaterial(id, name) {
    window.confirmDelete({
        target: name,
        onConfirm: function(modal, btn) {
            const fd = new FormData();
            fd.append('action', 'delete_material');
            fd.append('id', id);
            fetch(HANDLER, { method: 'POST', body: fd })
            .then(r => r.json()).then(res => {
                modal.hide();
                if(res.success) { 
                    Swal.fire({ icon: 'success', title: 'Deleted!', text: res.message, timer: 1500, showConfirmButton: false })
                    .then(() => location.reload()); 
                }
                else Swal.fire('Error', res.message, 'error');
            });
        }
    });
}
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>
