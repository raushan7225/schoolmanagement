<?php
// admin/frontend-banners.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Fetch existing banners
$stmt = $pdo->query("SELECT * FROM frontend_banners ORDER BY created_at DESC");
$banners = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>Hero Banner Management</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Frontend</li>
            <li class="breadcrumb-item active">Banners</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle datatable-premium"
                               data-add-btn='{"text":"Add New Banner","onclick":"openAddModal()","icon":"fas fa-plus-circle"}'>
                            <thead class="table-light">
                                <tr>
                                    <th width="60">S.No.</th>
                                    <th>Banner Image</th>
                                    <th>Overlay Content</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-end" data-no-sort>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($banners)): $sn=1; foreach($banners as $b): ?>
                                <tr id="bannerrow-<?php echo $b['id']; ?>">
                                    <td><?php echo $sn++; ?></td>
                                    <td>
                                        <img src="<?php echo BASE_URL; ?>media/frontend/<?php echo $b['image']; ?>" class="rounded border shadow-sm" style="width: 120px; height: 60px; object-fit: cover;">
                                    </td>
                                    <td>
                                        <div class="fw-bold text-primary"><?php echo htmlspecialchars($b['title']); ?></div>
                                        <small class="text-muted d-block text-truncate" style="max-width: 250px;"><?php echo htmlspecialchars($b['subtitle']); ?></small>
                                        <?php if($b['link']): ?>
                                            <span class="badge bg-light text-dark border mt-1">Link: <?php echo htmlspecialchars($b['link']); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-<?php echo $b['status'] == 1 ? 'success' : 'danger'; ?> rounded-pill px-3">
                                            <?php echo $b['status'] == 1 ? 'Active' : 'Hidden'; ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-warning btn-icon me-1" title="Edit" onclick="editBanner(<?php echo $b['id']; ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger btn-icon" title="Delete" onclick="deleteBanner(<?php echo $b['id']; ?>, '<?php echo addslashes($b['title']); ?>')">
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
<div class="modal fade" id="modalBanner" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle"><i class="fas fa-plus-circle me-2"></i>Add New Banner</h5>
                <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="bannerForm" novalidate enctype="multipart/form-data">
                <input type="hidden" name="action" value="save_banner">
                <input type="hidden" name="id" id="b-id" value="">
                <div class="modal-body px-3 py-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Banner Title (Large Text)</label>
                        <input type="text" class="form-control" name="title" id="b-title" placeholder="e.g. Welcome to Our School">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Subtitle (Small Text)</label>
                        <input type="text" class="form-control" name="subtitle" id="b-subtitle" placeholder="e.g. Nurturing Future Leaders">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label fw-bold">Link URL</label>
                            <input type="text" class="form-control" name="link" id="b-link" placeholder="e.g. /about-us">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Status</label>
                            <select class="form-select" name="status" id="b-status">
                                <option value="1">Active</option>
                                <option value="0">Hidden</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small"><i class="fas fa-desktop me-1"></i>Desktop Banner <span class="text-danger small">*</span> <span class="text-muted fw-normal">(1920x600)</span></label>
                            <input type="file" class="form-control form-control-sm" name="image" id="b-image" accept="image/*">
                            <div class="mt-2 d-none text-center" id="img-preview-box">
                                <img src="" id="img-preview" class="img-fluid rounded border" style="max-height: 80px;">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small"><i class="fas fa-laptop me-1"></i>Laptop Banner <span class="text-muted fw-normal">(1366x500)</span></label>
                            <input type="file" class="form-control form-control-sm" name="laptop_image" id="b-laptop" accept="image/*">
                            <div class="mt-2 d-none text-center" id="laptop-preview-box">
                                <img src="" id="laptop-preview" class="img-fluid rounded border" style="max-height: 80px;">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small"><i class="fas fa-tablet-alt me-1"></i>Tablet Banner <span class="text-muted fw-normal">(1024x500)</span></label>
                            <input type="file" class="form-control form-control-sm" name="tablet_image" id="b-tablet" accept="image/*">
                            <div class="mt-2 d-none text-center" id="tablet-preview-box">
                                <img src="" id="tablet-preview" class="img-fluid rounded border" style="max-height: 80px;">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small"><i class="fas fa-mobile-alt me-1"></i>Mobile Banner <span class="text-muted fw-normal">(600x400)</span></label>
                            <input type="file" class="form-control form-control-sm" name="mobile_image" id="b-mobile" accept="image/*">
                            <div class="mt-2 d-none text-center" id="mobile-preview-box">
                                <img src="" id="mobile-preview" class="img-fluid rounded border" style="max-height: 80px;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top py-2">
                    <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">CANCEL</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold" id="btn-save">
                        <i class="fas fa-save me-2"></i>SAVE BANNER
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
'use strict';
const HANDLER = '<?php echo BASE_URL; ?>ajax/frontend_handler.php';
const ALL_BANNERS = <?php echo json_encode($banners); ?>;
let modalBanner;

document.addEventListener('DOMContentLoaded', function() {
    modalBanner = new bootstrap.Modal(document.getElementById('modalBanner'));
    const form = document.getElementById('bannerForm');

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!form.checkValidity()) { form.classList.add('was-validated'); return; }
        
        const btn = document.getElementById('btn-save');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>SAVING...';
        
        fetch(HANDLER, { method: 'POST', body: new FormData(form) })
        .then(r => r.json()).then(res => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-2"></i>SAVE BANNER';
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
    modalBanner.show(); 
}

function editBanner(id) {
    const b = ALL_BANNERS.find(x => x.id == id);
    if (!b) return;
    
    resetForm();
    document.getElementById('b-id').value = b.id;
    document.getElementById('b-title').value = b.title;
    document.getElementById('b-subtitle').value = b.subtitle;
    document.getElementById('b-link').value = b.link;
    document.getElementById('b-status').value = b.status;
    
    if(b.image) {
        document.getElementById('img-preview-box').classList.remove('d-none');
        document.getElementById('img-preview').src = `<?php echo BASE_URL; ?>media/frontend/${b.image}`;
    }
    if(b.laptop_image) {
        document.getElementById('laptop-preview-box').classList.remove('d-none');
        document.getElementById('laptop-preview').src = `<?php echo BASE_URL; ?>media/frontend/${b.laptop_image}`;
    }
    if(b.tablet_image) {
        document.getElementById('tablet-preview-box').classList.remove('d-none');
        document.getElementById('tablet-preview').src = `<?php echo BASE_URL; ?>media/frontend/${b.tablet_image}`;
    }
    if(b.mobile_image) {
        document.getElementById('mobile-preview-box').classList.remove('d-none');
        document.getElementById('mobile-preview').src = `<?php echo BASE_URL; ?>media/frontend/${b.mobile_image}`;
    }
    
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>Edit Banner';
    modalBanner.show();
}

function resetForm() {
    const form = document.getElementById('bannerForm');
    form.reset();
    form.classList.remove('was-validated');
    document.getElementById('b-id').value = '';
    document.getElementById('img-preview-box').classList.add('d-none');
    document.getElementById('laptop-preview-box').classList.add('d-none');
    document.getElementById('tablet-preview-box').classList.add('d-none');
    document.getElementById('mobile-preview-box').classList.add('d-none');
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-plus-circle me-2"></i>Add New Banner';
}

function deleteBanner(id, name) {
    window.confirmDelete({
        target: name,
        onConfirm: function(cModal, btn) {
            const fd = new FormData();
            fd.append('action', 'delete_banner');
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
