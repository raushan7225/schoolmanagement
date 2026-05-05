<?php
// admin/frontend-testimonial.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Fetch existing testimonials
$stmt = $pdo->query("SELECT * FROM frontend_testimonials ORDER BY created_at DESC");
$testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>Testimonial Management</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Frontend</li>
            <li class="breadcrumb-item active">Testimonials</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle datatable-premium"
                               data-add-btn='{"text":"Add New Testimonial","onclick":"openAddModal()","icon":"fas fa-plus-circle"}'>
                            <thead class="table-light">
                                <tr>
                                    <th width="60">S.No.</th>
                                    <th>Identity</th>
                                    <th>Review</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-end" data-no-sort>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($testimonials)): $sn=1; foreach($testimonials as $t): ?>
                                <tr id="testirow-<?php echo $t['id']; ?>">
                                    <td><?php echo $sn++; ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo $t['photo'] ? BASE_URL . 'media/frontend/' . $t['photo'] : 'https://placehold.co/50'; ?>" class="rounded-circle border me-2" width="40" height="40" style="object-fit: cover;">
                                            <div>
                                                <div class="fw-bold text-dark"><?php echo htmlspecialchars($t['name']); ?></div>
                                                <small class="text-muted"><?php echo htmlspecialchars($t['designation']); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><small class="text-muted italic">"<?php echo htmlspecialchars($t['quote']); ?>"</small></td>
                                    <td class="text-center">
                                        <span class="badge bg-<?php echo $t['status'] == 1 ? 'success' : 'danger'; ?> rounded-pill px-3">
                                            <?php echo $t['status'] == 1 ? 'Active' : 'Hidden'; ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-warning btn-icon me-1" title="Edit" onclick="editTesti(<?php echo $t['id']; ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger btn-icon" title="Delete" onclick="deleteTesti(<?php echo $t['id']; ?>, '<?php echo addslashes($t['name']); ?>')">
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

<!-- Add/Edit Modal -->
<div class="modal fade" id="modalTesti" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle"><i class="fas fa-comment-dots me-2"></i>Configure Review</h5>
                <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="testimonialForm" novalidate enctype="multipart/form-data">
                <input type="hidden" name="action" value="save_testimonial">
                <input type="hidden" name="id" id="t-id" value="">
                <div class="modal-body px-3 py-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Reviewer Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" id="t-name" placeholder="e.g. Rahul Sharma" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Designation / Course</label>
                            <input type="text" class="form-control" name="designation" id="t-desig" placeholder="e.g. Alumnus, DCA 2024">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Status</label>
                            <select class="form-select" name="status" id="t-status">
                                <option value="1">Active</option>
                                <option value="0">Hidden</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Photo</label>
                            <input type="file" class="form-control" name="photo" id="t-photo" accept="image/*">
                        </div>
                        <div class="col-12 text-center d-none" id="img-preview-box">
                            <img src="" id="img-preview" class="rounded-circle border shadow-sm" style="width: 80px; height: 80px; object-fit: cover;">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Review Message <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="quote" id="t-quote" rows="4" placeholder="Enter feedback..." required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top py-2">
                    <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">CANCEL</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold" id="btn-save">
                        <i class="fas fa-save me-2"></i>SAVE REVIEW
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
'use strict';
const HANDLER = '<?php echo BASE_URL; ?>ajax/frontend_handler.php';
const ALL_TESTIS = <?php echo json_encode($testimonials); ?>;
let modalTesti;

document.addEventListener('DOMContentLoaded', function() {
    modalTesti = new bootstrap.Modal(document.getElementById('modalTesti'));
    const form = document.getElementById('testimonialForm');

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!form.checkValidity()) { form.classList.add('was-validated'); return; }
        
        const btn = document.getElementById('btn-save');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>SAVING...';
        
        fetch(HANDLER, { method: 'POST', body: new FormData(form) })
        .then(r => r.json()).then(res => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-2"></i>SAVE REVIEW';
            if(res.success) { 
                Swal.fire({ icon: 'success', title: 'Success', text: res.message, timer: 1500, showConfirmButton: false })
                .then(() => location.reload()); 
            }
            else Swal.fire('Error', res.message, 'error');
        });
    });
});

function openAddModal() {
    resetForm();
    modalTesti.show();
}

function editTesti(id) {
    const t = ALL_TESTIS.find(x => x.id == id);
    if (!t) return;
    
    resetForm();
    document.getElementById('t-id').value = t.id;
    document.getElementById('t-name').value = t.name;
    document.getElementById('t-desig').value = t.designation;
    document.getElementById('t-quote').value = t.quote;
    document.getElementById('t-status').value = t.status;
    
    if(t.photo) {
        document.getElementById('img-preview-box').classList.remove('d-none');
        document.getElementById('img-preview').src = `<?php echo BASE_URL; ?>media/frontend/${t.photo}`;
    }
    
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>Edit Testimonial';
    modalTesti.show();
}

function resetForm() {
    const form = document.getElementById('testimonialForm');
    form.reset();
    form.classList.remove('was-validated');
    document.getElementById('t-id').value = '';
    document.getElementById('img-preview-box').classList.add('d-none');
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-comment-dots me-2"></i>Configure Review';
}

function deleteTesti(id, name) {
    window.confirmDelete({
        target: name,
        onConfirm: function(cModal, btn) {
            const fd = new FormData();
            fd.append('action', 'delete_testimonial');
            fd.append('id', id);
            fetch(HANDLER, { method: 'POST', body: fd })
            .then(r => r.json()).then(res => {
                cModal.hide();
                if(res.success) { location.reload(); }
                else Swal.fire('Error', res.message, 'error');
            });
        }
    });
}
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>
