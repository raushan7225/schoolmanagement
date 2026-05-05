<?php
// admin/frontend-gallery.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Fetch existing media
$stmt = $pdo->query("SELECT * FROM frontend_gallery ORDER BY created_at DESC");
$media = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch categories for dropdown
$categories = $pdo->query("SELECT * FROM frontend_gallery_categories WHERE status = 1 ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
$catMap = [];
foreach($categories as $c) { $catMap[$c['id']] = $c['name']; }
?>

<div class="pagetitle">
    <h1>Photo Gallery Management</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Frontend</li>
            <li class="breadcrumb-item active">Gallery</li>
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
                               data-add-btn='{"text":"Upload to Gallery","onclick":"openAddModal()","icon":"fas fa-images"}'>
                            <thead class="table-light">
                                <tr>
                                    <th width="60">S.No.</th>
                                    <th>Image</th>
                                    <th>Title & Category</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-end" data-no-sort>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($media)): $sn=1; foreach($media as $m): ?>
                                <tr id="mediarow-<?php echo $m['id']; ?>">
                                    <td><?php echo $sn++; ?></td>
                                    <td>
                                        <img src="<?php echo BASE_URL; ?>media/frontend/<?php echo $m['media_file']; ?>" class="rounded border shadow-sm" style="width: 80px; height: 50px; object-fit: cover;">
                                    </td>
                                    <td>
                                        <div class="fw-bold text-primary"><?php echo htmlspecialchars($m['title']); ?></div>
                                        <span class="badge bg-light text-secondary border small mt-1"><?php echo isset($catMap[$m['category_id']]) ? htmlspecialchars($catMap[$m['category_id']]) : 'Uncategorized'; ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-<?php echo $m['status'] == 1 ? 'success' : 'danger'; ?> rounded-pill px-3">
                                            <?php echo $m['status'] == 1 ? 'Active' : 'Hidden'; ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-warning btn-icon me-1" title="Edit" onclick="editMedia(<?php echo $m['id']; ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger btn-icon" title="Delete" onclick="deleteMedia(<?php echo $m['id']; ?>, '<?php echo addslashes($m['title']); ?>')">
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
<div class="modal fade" id="modalGallery" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle"><i class="fas fa-images me-2"></i>Upload to Gallery</h5>
                <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="galleryForm" novalidate enctype="multipart/form-data">
                <input type="hidden" name="action" value="save_gallery">
                <input type="hidden" name="id" id="m-id" value="">
                <div class="modal-body px-3 py-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Title / Caption <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="title" id="m-title" placeholder="e.g. Science Fair 2024" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                        <select class="form-select" name="category_id" id="m-cat" required>
                            <option value="">Select Category...</option>
                            <?php foreach($categories as $c): ?>
                                <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Gallery Image <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="media_file" id="m-file" accept="image/*">
                        <div class="mt-2 d-none text-center" id="img-preview-box">
                            <img src="" id="img-preview" class="img-fluid rounded border shadow-sm" style="max-height: 120px;">
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-bold">Status</label>
                        <select class="form-select" name="status" id="m-status">
                            <option value="1">Active</option>
                            <option value="0">Hidden</option>
                        </select>
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
const ALL_MEDIA = <?php echo json_encode($media); ?>;
let modalGallery;

document.addEventListener('DOMContentLoaded', function() {
    modalGallery = new bootstrap.Modal(document.getElementById('modalGallery'));
    const form = document.getElementById('galleryForm');

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
    modalGallery.show();
}

function editMedia(id) {
    const m = ALL_MEDIA.find(x => x.id == id);
    if (!m) return;
    
    resetForm();
    document.getElementById('m-id').value = m.id;
    document.getElementById('m-title').value = m.title;
    document.getElementById('m-cat').value = m.category_id;
    document.getElementById('m-status').value = m.status;
    
    if(m.media_file) {
        document.getElementById('img-preview-box').classList.remove('d-none');
        document.getElementById('img-preview').src = `<?php echo BASE_URL; ?>media/frontend/${m.media_file}`;
    }
    
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>Edit Media';
    modalGallery.show();
}

function resetForm() {
    const form = document.getElementById('galleryForm');
    form.reset();
    form.classList.remove('was-validated');
    document.getElementById('m-id').value = '';
    document.getElementById('img-preview-box').classList.add('d-none');
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-images me-2"></i>Upload to Gallery';
}

function deleteMedia(id, name) {
    window.confirmDelete({
        target: name,
        onConfirm: function(cModal, btn) {
            const fd = new FormData();
            fd.append('action', 'delete_gallery');
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
