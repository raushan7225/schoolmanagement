<?php
// admin/frontend-center-certificates.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Fetch existing certificates
$stmt = $pdo->query("SELECT * FROM frontend_certificates ORDER BY sort_order ASC, created_at DESC");
$certs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>Center Certificates</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Frontend</li>
            <li class="breadcrumb-item active">Center Certificates</li>
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
                               data-add-btn='{"text":"Add New Certificate","onclick":"openAddModal()","icon":"fas fa-award"}'>
                            <thead class="table-light">
                                <tr>
                                    <th width="60">S.No.</th>
                                    <th>Certificate</th>
                                    <th>Center Details</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-end" data-no-sort>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($certs)): $sn=1; foreach($certs as $c): ?>
                                <tr id="certrow-<?php echo $c['id']; ?>">
                                    <td><?php echo $sn++; ?></td>
                                    <td>
                                        <img src="<?php echo BASE_URL; ?>media/frontend/<?php echo $c['image']; ?>" class="rounded border p-1" style="width: 80px; height: 50px; object-fit: contain;">
                                    </td>
                                    <td><div class="fw-bold text-primary"><?php echo htmlspecialchars($c['center_name']); ?></div></td>
                                    <td class="text-center">
                                        <span class="badge bg-<?php echo $c['status'] == 1 ? 'success' : 'danger'; ?> rounded-pill px-3">
                                            <?php echo $c['status'] == 1 ? 'Active' : 'Hidden'; ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-warning btn-icon me-1" title="Edit" onclick="editCert(<?php echo $c['id']; ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger btn-icon" title="Delete" onclick="deleteCert(<?php echo $c['id']; ?>, '<?php echo addslashes($c['center_name']); ?>')">
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
<div class="modal fade" id="modalCert" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle"><i class="fas fa-award me-2"></i>Showcase Form</h5>
                <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="certForm" novalidate enctype="multipart/form-data">
                <input type="hidden" name="action" value="save_certificate">
                <input type="hidden" name="id" id="c-id" value="">
                <div class="modal-body px-3 py-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Center Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="center_name" id="c-name" placeholder="e.g. Laxmi Institute" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Display Order</label>
                            <input type="number" class="form-control" name="sort_order" id="c-sort" value="1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Status</label>
                            <select class="form-select" name="status" id="c-status">
                                <option value="1">Active</option>
                                <option value="0">Hidden</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-bold">Certificate Image <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="image" id="c-image" accept="image/*">
                        <div class="mt-2 d-none text-center" id="img-preview-box">
                            <img src="" id="img-preview" class="img-fluid rounded border shadow-sm" style="max-height: 120px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top py-2">
                    <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">CANCEL</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold" id="btn-save">
                        <i class="fas fa-upload me-2"></i>PUBLISH NOW
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
'use strict';
const HANDLER = '<?php echo BASE_URL; ?>ajax/frontend_handler.php';
const ALL_CERTS = <?php echo json_encode($certs); ?>;
let modalCert;

document.addEventListener('DOMContentLoaded', function() {
    modalCert = new bootstrap.Modal(document.getElementById('modalCert'));
    const form = document.getElementById('certForm');

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!form.checkValidity()) { form.classList.add('was-validated'); return; }
        
        const btn = document.getElementById('btn-save');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>SAVING...';
        
        fetch(HANDLER, { method: 'POST', body: new FormData(form) })
        .then(r => r.json()).then(res => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-upload me-2"></i>PUBLISH NOW';
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
    modalCert.show();
}

function editCert(id) {
    const c = ALL_CERTS.find(x => x.id == id);
    if (!c) return;
    
    resetForm();
    document.getElementById('c-id').value = c.id;
    document.getElementById('c-name').value = c.center_name;
    document.getElementById('c-sort').value = c.sort_order;
    document.getElementById('c-status').value = c.status;
    
    if(c.image) {
        document.getElementById('img-preview-box').classList.remove('d-none');
        document.getElementById('img-preview').src = `<?php echo BASE_URL; ?>media/frontend/${c.image}`;
    }
    
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>Edit Certificate';
    modalCert.show();
}

function resetForm() {
    const form = document.getElementById('certForm');
    form.reset();
    form.classList.remove('was-validated');
    document.getElementById('c-id').value = '';
    document.getElementById('img-preview-box').classList.add('d-none');
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-award me-2"></i>Showcase Form';
}

function deleteCert(id, name) {
    window.confirmDelete({
        target: name,
        onConfirm: function(cModal, btn) {
            const fd = new FormData();
            fd.append('action', 'delete_certificate');
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
