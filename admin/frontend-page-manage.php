<?php
// admin/frontend-page-manage.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Fetch existing pages
$stmt = $pdo->query("SELECT * FROM frontend_pages ORDER BY title ASC");
$pages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>Custom Page Management</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Frontend</li>
            <li class="breadcrumb-item active">Page Manage</li>
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
                               data-add-btn='{"text":"Create New Page","onclick":"openAddModal()","icon":"fas fa-file-circle-plus"}'>
                            <thead class="table-light">
                                <tr>
                                    <th width="60">S.No.</th>
                                    <th>Page Title</th>
                                    <th>Slug / Route</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-end" data-no-sort>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($pages)): $sn=1; foreach($pages as $p): ?>
                                <tr id="pagerow-<?php echo $p['id']; ?>">
                                    <td><?php echo $sn++; ?></td>
                                    <td class="fw-bold text-dark"><?php echo htmlspecialchars($p['title']); ?></td>
                                    <td><code class="text-primary"><?php echo htmlspecialchars($p['slug']); ?></code></td>
                                    <td class="text-center">
                                        <span class="badge bg-<?php echo $p['status'] == 1 ? 'success' : 'danger'; ?> rounded-pill px-3">
                                            <?php echo $p['status'] == 1 ? 'Active' : 'Hidden'; ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-warning btn-icon me-1" title="Edit Meta" onclick="editPage(<?php echo $p['id']; ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger btn-icon" title="Delete" onclick="deletePage(<?php echo $p['id']; ?>, '<?php echo addslashes($p['title']); ?>')">
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
<div class="modal fade" id="modalPage" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle"><i class="fas fa-file-circle-plus me-2"></i>Create New Page</h5>
                <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="pageForm" novalidate enctype="multipart/form-data">
                <input type="hidden" name="action" value="save_page">
                <input type="hidden" name="id" id="p-id" value="">
                <div class="modal-body px-3 py-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Page Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="title" id="p-title" placeholder="e.g. Terms & Conditions" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Page Slug / Route <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="slug" id="p-slug" placeholder="e.g. terms-conditions" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Page Content / UI Blocks</label>
                            <textarea class="form-control" name="content" id="p-content" rows="4" placeholder="Enter content or comma separated UI Block keys..."></textarea>
                            <small class="text-muted">You can enter raw content or a list of UI Block keys (e.g. ABOUT_HERO, ABOUT_INFO).</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Meta Title</label>
                            <input type="text" class="form-control" name="meta_title" id="p-meta-title">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Status</label>
                            <select class="form-select" name="status" id="p-status">
                                <option value="1">Active</option>
                                <option value="0">Hidden</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Meta Description</label>
                            <textarea class="form-control" name="meta_description" id="p-meta-desc" rows="2"></textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Featured Image</label>
                            <input type="file" class="form-control" name="featured_image" id="p-image" accept="image/*">
                            <div class="mt-2 d-none text-center" id="img-preview-box">
                                <img src="" id="img-preview" class="img-fluid rounded border" style="max-height: 100px;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top py-2">
                    <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">CANCEL</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold" id="btn-save">
                        <i class="fas fa-save me-2"></i>SAVE PAGE
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
'use strict';
const HANDLER = '<?php echo BASE_URL; ?>ajax/frontend_handler.php';
const ALL_PAGES = <?php echo json_encode($pages); ?>;
let modalPage;

document.addEventListener('DOMContentLoaded', function() {
    modalPage = new bootstrap.Modal(document.getElementById('modalPage'));
    const form = document.getElementById('pageForm');

    // Auto-slug generation
    document.getElementById('p-title').addEventListener('input', function() {
        if(document.getElementById('p-id').value === '') {
            document.getElementById('p-slug').value = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
        }
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!form.checkValidity()) { form.classList.add('was-validated'); return; }
        
        const btn = document.getElementById('btn-save');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>SAVING...';
        
        fetch(HANDLER, { method: 'POST', body: new FormData(form) })
        .then(r => r.json()).then(res => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-2"></i>SAVE PAGE';
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
    modalPage.show(); 
}

function editPage(id) {
    const p = ALL_PAGES.find(x => x.id == id);
    if (!p) return;
    
    resetForm();
    document.getElementById('p-id').value = p.id;
    document.getElementById('p-title').value = p.title;
    document.getElementById('p-slug').value = p.slug;
    document.getElementById('p-content').value = p.content;
    document.getElementById('p-meta-title').value = p.meta_title;
    document.getElementById('p-meta-desc').value = p.meta_description;
    document.getElementById('p-status').value = p.status;
    
    if(p.featured_image) {
        document.getElementById('img-preview-box').classList.remove('d-none');
        document.getElementById('img-preview').src = `<?php echo BASE_URL; ?>media/frontend/${p.featured_image}`;
    }
    
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>Edit Page Meta';
    modalPage.show();
}

function resetForm() {
    const form = document.getElementById('pageForm');
    form.reset();
    form.classList.remove('was-validated');
    document.getElementById('p-id').value = '';
    document.getElementById('img-preview-box').classList.add('d-none');
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-file-circle-plus me-2"></i>Create New Page';
}

function deletePage(id, name) {
    window.confirmDelete({
        target: name,
        onConfirm: function(cModal, btn) {
            const fd = new FormData();
            fd.append('action', 'delete_page');
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
