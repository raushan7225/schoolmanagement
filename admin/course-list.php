<?php
// admin/course-list.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Search / Filter logic
$where = "WHERE 1=1";
$params = [];

$filter_cat = (int)($_GET['category_id'] ?? 0);
$filter_status = $_GET['status'] ?? '';
$search = trim($_GET['search'] ?? '');

if ($filter_cat) {
    $where .= " AND c.category_id = ?";
    $params[] = $filter_cat;
}
if ($filter_status !== '') {
    $where .= " AND c.status = ?";
    $params[] = (int)$filter_status;
}
if ($search) {
    $where .= " AND (c.name LIKE ? OR c.code LIKE ?)";
    $params[] = "%$search%"; $params[] = "%$search%";
}

// Fetch Courses
$stmt = $pdo->prepare("
    SELECT c.*, cat.name as category_name 
    FROM courses c
    LEFT JOIN course_categories cat ON c.category_id = cat.id
    $where
    ORDER BY c.name ASC
");
$stmt->execute($params);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Stats
$totalCourses = $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
$activeCourses = $pdo->query("SELECT COUNT(*) FROM courses WHERE status = 1")->fetchColumn();
$inactiveCourses = $pdo->query("SELECT COUNT(*) FROM courses WHERE status = 0")->fetchColumn();

// Categories for dropdown
$categories = $pdo->query("SELECT id, name FROM course_categories WHERE status = 1 ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>Course Management</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Course Management</li>
            <li class="breadcrumb-item active">Course List</li>
        </ol>
    </nav>
</div>

<section class="section">
<div class="row">

<!-- ── Stat Cards ─────────────────────────────────────────── -->
<div class="col-md-4 col-sm-6 mb-3">
    <div class="card h-100">
        <div class="card-body d-flex align-items-center gap-3 py-3">
            <div class="flex-shrink-0 rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10" style="width:48px;height:48px;">
                <i class="fas fa-book text-primary fs-5"></i>
            </div>
            <div>
                <div class="text-muted small">Total Courses</div>
                <div class="fw-bold fs-5"><?php echo $totalCourses; ?></div>
            </div>
        </div>
    </div>
</div>
<div class="col-md-4 col-sm-6 mb-3">
    <div class="card h-100">
        <div class="card-body d-flex align-items-center gap-3 py-3">
            <div class="flex-shrink-0 rounded-circle d-flex align-items-center justify-content-center bg-success bg-opacity-10" style="width:48px;height:48px;">
                <i class="fas fa-circle-check text-success fs-5"></i>
            </div>
            <div>
                <div class="text-muted small">Active Courses</div>
                <div class="fw-bold fs-5 text-success"><?php echo $activeCourses; ?></div>
            </div>
        </div>
    </div>
</div>
<div class="col-md-4 col-sm-6 mb-3">
    <div class="card h-100">
        <div class="card-body d-flex align-items-center gap-3 py-3">
            <div class="flex-shrink-0 rounded-circle d-flex align-items-center justify-content-center bg-danger bg-opacity-10" style="width:48px;height:48px;">
                <i class="fas fa-circle-xmark text-danger fs-5"></i>
            </div>
            <div>
                <div class="text-muted small">Inactive Courses</div>
                <div class="fw-bold fs-5 text-danger"><?php echo $inactiveCourses; ?></div>
            </div>
        </div>
    </div>
</div>

<!-- ── Full Width ─────────────────────────────────────────── -->
<div class="col-12">

<!-- Filter Bar -->
<div class="card mb-3">
    <div class="card-body py-2">
        <form class="row g-2 align-items-center" method="GET">
            <div class="col-md-3">
                <select name="category_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Categories...</option>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo $filter_cat == $cat['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Status...</option>
                    <option value="1" <?php echo $filter_status === '1' ? 'selected' : ''; ?>>Active</option>
                    <option value="0" <?php echo $filter_status === '0' ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search course name or code..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary flex-grow-1 fw-bold"><i class="fas fa-filter me-2"></i>APPLY</button>
                <a href="course-list.php" class="btn btn-sm btn-outline-secondary fw-bold">RESET</a>
            </div>
        </form>
    </div>
</div>

<!-- Table Card -->
<div class="card">
    <div class="card-body pt-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle datatable-premium"
                   data-add-btn='{"text":"Add New Course","onclick":"new bootstrap.Modal(document.getElementById(\"courseModal\")).show()","icon":"fas fa-plus"}'>
                <thead class="table-light">
                    <tr>
                        <th>S.No.</th>
                        <th>Thumbnail</th>
                        <th>Course Info</th>
                        <th>Fees (Reg / Course)</th>
                        <th>Exam Fee</th>
                        <th>Duration</th>
                        <th data-no-sort>On Web</th>
                        <th data-no-sort>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($courses)): $sn=1; foreach($courses as $c): ?>
                    <tr id="courserow-<?php echo $c['id']; ?>">
                        <td><?php echo $sn++; ?></td>
                        <td>
                            <?php 
                            $thumb = $c['thumbnail'] ? BASE_URL . 'media/courses/' . $c['thumbnail'] : 'https://placehold.co/60x40/e8ecfc/1b1260?text='.urlencode($c['code']);
                            ?>
                            <img src="<?php echo $thumb; ?>" alt="<?php echo $c['name']; ?>" class="rounded border" style="width:60px; height:40px; object-fit: cover;">
                        </td>
                        <td>
                            <div class="fw-bold text-primary-theme"><?php echo htmlspecialchars($c['name']); ?></div>
                            <div class="text-muted small">
                                Code: <strong><?php echo htmlspecialchars($c['code']); ?></strong> &nbsp;|&nbsp;
                                Category: <span class="badge bg-info-light text-info"><?php echo htmlspecialchars($c['category_name'] ?? 'N/A'); ?></span>
                            </div>
                        </td>
                        <td>
                            <div class="small fw-bold text-dark">Reg: &#8377; <?php echo number_format($c['registration_fee'], 0); ?></div>
                            <div class="text-success small fw-bold">Course: &#8377; <?php echo number_format($c['course_fee'], 0); ?></div>
                        </td>
                        <td>
                            <div class="text-primary small fw-bold">&#8377; <?php echo number_format($c['exam_fee'], 0); ?></div>
                        </td>
                        <td>
                            <div class="d-flex flex-column gap-1">
                                <span class="badge border text-dark rounded-pill small">
                                    <?php 
                                    if($c['duration_semesters'] > 0) echo $c['duration_semesters'] . ' Semester(s)';
                                    elseif($c['duration_years'] > 0) echo $c['duration_years'] . ' Year(s)';
                                    elseif($c['duration_months'] > 0) echo $c['duration_months'] . ' Month(s)';
                                    elseif($c['duration_days'] > 0) echo $c['duration_days'] . ' Day(s)';
                                    else echo '0';
                                    ?>
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input toggle-web" type="checkbox" data-id="<?php echo $c['id']; ?>" <?php echo $c['status'] == 1 ? 'checked' : ''; ?>>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-<?php echo $c['status'] == 1 ? 'success' : 'danger'; ?> rounded-pill">
                                <?php echo $c['status'] == 1 ? 'Active' : 'Inactive'; ?>
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="btn-group shadow-sm border rounded">
                                <button class="btn btn-sm btn-outline-info border-0 px-2" title="View" onclick="viewCourse(<?php echo $c['id']; ?>)">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-warning border-0 px-2" title="Edit" onclick='editCourse(<?php echo json_encode($c); ?>)'>
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger border-0 px-2" title="Delete" onclick="deleteCourse(<?php echo $c['id']; ?>, '<?php echo addslashes($c['name']); ?>')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</div><!-- /.col-12 -->
</div><!-- /.row -->
</section>

<!-- ══ Add/Edit Course Modal ══════════════════════════════════════════════════════ -->
<div class="modal fade" id="courseModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="courseModalTitle">
                    <i class="fas fa-plus-circle me-2"></i>Create New Course
                </h5>
                <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                    <i class="<?php echo v('theme_modal_close_icon', 'fas fa-times'); ?>"></i>
                </button>
            </div>
            <form id="courseForm" novalidate enctype="multipart/form-data">
                <input type="hidden" name="action" id="course-action" value="add_course">
                <input type="hidden" name="id" id="course-id" value="">
                <div class="modal-body px-3 py-4" style="max-height: 80vh; overflow-y: auto; overflow-x: hidden;">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Category <span class="text-danger">*</span></label>
                            <select class="form-select" name="category_id" id="f-category" required>
                                <option value="">Select Category…</option>
                                <?php foreach($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Please select a category.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Course Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" id="f-name"
                                   placeholder="Full Course Title" required>
                            <div class="invalid-feedback">Course name is required.</div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold small">Course Code</label>
                            <input type="text" class="form-control" name="code" id="f-code" placeholder="e.g. DCA01">
                        </div>
                        
                        <div class="col-12"><div class="form-section-header">COURSE DURATION</div></div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Duration Type</label>
                            <select class="form-select" name="duration_type" id="f-duration-type">
                                <option value="years">Years</option>
                                <option value="months">Months</option>
                                <option value="days">Days</option>
                                <option value="semesters">Semesters</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Value</label>
                            <input type="number" class="form-control" name="duration_value" id="f-duration-value" value="0" min="0">
                        </div>

                        <div class="col-12"><div class="form-section-header">ACADEMIC & FEES</div></div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Eligibility</label>
                            <input type="text" class="form-control" name="eligibility" id="f-eligibility" placeholder="e.g. 10th Pass">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Reg. Fee (&#8377;)</label>
                            <input type="number" class="form-control" name="registration_fee" id="f-reg-fee" value="0" min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Course Fee (&#8377;)</label>
                            <input type="number" class="form-control" name="course_fee" id="f-course-fee" value="0" min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Exam Fee (&#8377;)</label>
                            <input type="number" class="form-control" name="exam_fee" id="f-exam-fee" value="0" min="0">
                        </div>
                        
                        <div class="col-12"><div class="form-section-header">FRANCHISE & PARTNER SETTINGS</div></div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Franchise Fee (&#8377;)</label>
                            <input type="number" class="form-control" name="franchise_fee" id="f-fran-fee" value="0" min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Partner Commission (&#8377;)</label>
                            <input type="number" class="form-control" name="partner_commission" id="f-part-comm" value="0" min="0">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold small">Status</label>
                            <select class="form-select" name="status" id="f-status">
                                <option value="1" selected>Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold small">Short Description</label>
                            <textarea class="form-control" name="description" id="f-description" rows="2"
                                      placeholder="Brief summary for list view…"></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold small">Course Thumbnail</label>
                            <input type="file" class="form-control" name="thumbnail" accept="image/*">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top py-2">
                    <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">CANCEL</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold" id="btn-save-course">
                        <i class="fas fa-save me-2"></i>SAVE COURSE
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ══ View Course Modal ════════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalViewCourse" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-eye me-2"></i>Course Details</h5>
                <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                    <i class="<?php echo v('theme_modal_close_icon', 'fas fa-times'); ?>"></i>
                </button>
            </div>
            <div class="modal-body p-0" id="view-course-body">
                <div class="text-center py-5"><div class="spinner-border text-primary"></div></div>
            </div>
        </div>
    </div>
</div>

<script>
const HANDLER = '<?php echo BASE_URL; ?>ajax/course_handler.php';
let modalEl, modalObj, form;

document.addEventListener('DOMContentLoaded', function() {
    modalEl = document.getElementById('courseModal');
    modalObj = new bootstrap.Modal(modalEl);
    form = document.getElementById('courseForm');

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!form.checkValidity()) { form.classList.add('was-validated'); return; }
        
        const btn = document.getElementById('btn-save-course');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>SAVING...';
        
        fetch(HANDLER, { method: 'POST', body: new FormData(form) })
        .then(r => r.json()).then(res => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-2"></i>SAVE COURSE';
            if(res.success) { 
                Swal.fire({ icon: 'success', title: 'Success', text: res.message, timer: 1500, showConfirmButton: false })
                .then(() => location.reload()); 
            }
            else Swal.fire('Error', res.message, 'error');
        });
    });

    modalEl.addEventListener('hidden.bs.modal', resetCourseForm);

    // Toggle Web Status
    document.querySelectorAll('.toggle-web').forEach(chk => {
        chk.addEventListener('change', function() {
            const id = this.getAttribute('data-id');
            const status = this.checked ? 1 : 0;
            const fd = new FormData();
            fd.append('action', 'toggle_course_status');
            fd.append('id', id);
            fd.append('status', status);
            
            fetch(HANDLER, { method: 'POST', body: fd })
            .then(r => r.json()).then(res => {
                if(!res.success) {
                    Swal.fire('Error', res.message, 'error');
                    this.checked = !this.checked;
                }
            });
        });
    });
});

function editCourse(c) {
    if(!form) form = document.getElementById('courseForm');
    form.reset();
    document.getElementById('course-id').value = c.id;
    document.getElementById('course-action').value = 'edit_course';
    document.getElementById('f-category').value = c.category_id;
    document.getElementById('f-name').value = c.name;
    document.getElementById('f-code').value = c.code;
    let durType = 'years';
    let durVal = 0;
    if (c.duration_semesters > 0) { durType = 'semesters'; durVal = c.duration_semesters; }
    else if (c.duration_years > 0) { durType = 'years'; durVal = c.duration_years; }
    else if (c.duration_months > 0) { durType = 'months'; durVal = c.duration_months; }
    else if (c.duration_days > 0) { durType = 'days'; durVal = c.duration_days; }

    document.getElementById('f-duration-type').value = durType;
    document.getElementById('f-duration-value').value = durVal;
    document.getElementById('f-eligibility').value = c.eligibility;
    document.getElementById('f-reg-fee').value = c.registration_fee;
    document.getElementById('f-course-fee').value = c.course_fee;
    document.getElementById('f-fran-fee').value = c.franchise_fee;
    document.getElementById('f-part-comm').value = c.partner_commission;
    document.getElementById('f-exam-fee').value = c.exam_fee;
    document.getElementById('f-status').value = c.status;
    document.getElementById('f-description').value = c.description;
    
    document.getElementById('courseModalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>Edit Course';
    modalObj.show();
}

function resetCourseForm() {
    if(!form) form = document.getElementById('courseForm');
    form.reset();
    document.getElementById('course-id').value = '';
    document.getElementById('course-action').value = 'add_course';
    document.getElementById('courseModalTitle').innerHTML = '<i class="fas fa-plus-circle me-2"></i>Create New Course';
    form.classList.remove('was-validated');
}

function viewCourse(id) {
    document.getElementById('view-course-body').innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>';
    const viewModal = new bootstrap.Modal(document.getElementById('modalViewCourse'));
    viewModal.show();
    
    const fd = new FormData();
    fd.append('action', 'view_course');
    fd.append('id', id);
    
    fetch(HANDLER, { method: 'POST', body: fd })
    .then(r => r.json()).then(res => {
        if(!res.success) {
            document.getElementById('view-course-body').innerHTML = `<p class="text-danger text-center py-4">${res.message}</p>`;
            return;
        }
        const d = res.data;
        const thumb = d.thumbnail ? '<?php echo BASE_URL; ?>media/courses/' + d.thumbnail : 'https://placehold.co/600x300/e8ecfc/1b1260?text='+encodeURIComponent(d.code);
        
        let finalDur = '0';
        if(d.duration_semesters > 0) finalDur = d.duration_semesters + ' Semester(s)';
        else if(d.duration_years > 0) finalDur = d.duration_years + ' Year(s)';
        else if(d.duration_months > 0) finalDur = d.duration_months + ' Month(s)';
        else if(d.duration_days > 0) finalDur = d.duration_days + ' Day(s)';

        document.getElementById('view-course-body').innerHTML = `
            <div class="enquiry-view-premium p-1">
                <div class="view-section mb-0">
                    <div class="view-section-header">1. BASIC INFORMATION</div>
                    <div class="row g-3 px-3 py-4">
                        <div class="col-md-8">
                            <label class="view-label">Course Name</label>
                            <div class="view-value text-primary fw-bold fs-5">${d.name}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="view-label">Course Code</label>
                            <div class="view-value badge bg-primary-light text-primary fs-6">${d.code}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="view-label">Category</label>
                            <div class="view-value fw-bold">${d.category_name || 'N/A'}</div>
                        </div>
                        <div class="col-md-3">
                            <label class="view-label">Duration</label>
                            <div class="view-value fw-bold text-success">${finalDur}</div>
                        </div>
                        <div class="col-md-3">
                            <label class="view-label">Eligibility</label>
                            <div class="view-value fw-bold">${d.eligibility || '—'}</div>
                        </div>
                    </div>
                </div>

                <div class="view-section mb-0 border-top">
                    <div class="view-section-header">2. FEE STRUCTURE & COMMISSIONS</div>
                    <div class="row g-3 px-3 py-4">
                        <div class="col-md-3"><label class="view-label">Registration Fee</label><div class="view-value fw-bold text-dark">&#8377; ${parseFloat(d.registration_fee).toLocaleString()}</div></div>
                        <div class="col-md-3"><label class="view-label">Course Fee</label><div class="view-value text-success fw-bold">&#8377; ${parseFloat(d.course_fee).toLocaleString()}</div></div>
                        <div class="col-md-3"><label class="view-label">Exam Fee</label><div class="view-value text-primary fw-bold">&#8377; ${parseFloat(d.exam_fee).toLocaleString()}</div></div>
                        <div class="col-md-3"><label class="view-label">Franchise Fee</label><div class="view-value text-warning fw-bold">&#8377; ${parseFloat(d.franchise_fee).toLocaleString()}</div></div>
                    </div>
                </div>

                <div class="view-section mb-0 border-top">
                    <div class="view-section-header">3. ADDITIONAL DETAILS</div>
                    <div class="row g-3 px-3 py-4">
                        <div class="col-12">
                            <label class="view-label">Short Description</label>
                            <div class="view-value text-muted small p-3 bg-light rounded border-start border-4 border-primary">${d.description || 'No description provided.'}</div>
                        </div>
                        <div class="col-12 text-center mt-3">
                            <label class="view-label mb-2">Course Thumbnail</label>
                            <img src="${thumb}" class="img-fluid rounded border shadow-sm" style="max-height: 200px; width: 100%; object-fit: cover;">
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
}

function deleteCourse(id, name) {
    window.confirmDelete({
        target: name,
        onConfirm: function(modal, btn) {
            const fd = new FormData();
            fd.append('action', 'delete_course');
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
