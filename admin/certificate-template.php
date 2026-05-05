<?php
// admin/certificate-template.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Fetch existing Certificate templates
$stmt = $pdo->prepare("SELECT * FROM document_templates WHERE type = 'certificate' ORDER BY created_at DESC");
$stmt->execute();
$templates = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>Certificate Template</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Card Management</li>
            <li class="breadcrumb-item active">Certificate Template</li>
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
                               data-add-btn='{"text":"Add Template","onclick":"openAddTemplate()","icon":"fas fa-certificate"}'>
                            <thead class="table-light">
                                <tr>
                                    <th width="60">S.No.</th>
                                    <th width="120">Preview</th>
                                    <th>Template Name</th>
                                    <th data-no-sort>Status</th>
                                    <th class="text-end" data-no-sort>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $sn=1; foreach($templates as $t): ?>
                                <tr id="temprow-<?php echo $t['id']; ?>">
                                    <td><?php echo $sn++; ?></td>
                                    <td>
                                        <?php if($t['background_image']): ?>
                                            <div class="p-1 bg-white border rounded shadow-sm d-inline-block">
                                                <img src="<?php echo BASE_URL . 'media/templates/' . $t['background_image']; ?>" 
                                                     class="rounded" style="width: 100px; height: 65px; object-fit: cover;">
                                            </div>
                                        <?php else: ?>
                                            <div class="bg-light d-flex align-items-center justify-content-center border rounded shadow-sm" style="width: 100px; height: 65px;">
                                                <i class="fas fa-image text-muted fs-4"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark fs-6"><?php echo htmlspecialchars($t['name']); ?></div>
                                        <div class="small text-muted">Type: Certificate Background</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $t['status'] == 1 ? 'success' : 'danger'; ?> rounded-pill px-3">
                                            <?php echo $t['status'] == 1 ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group shadow-sm border rounded">
                                            <button class="btn btn-sm btn-outline-warning border-0 px-2" title="Edit Template" onclick='editTemplate(<?php echo json_encode($t); ?>)'>
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger border-0 px-2" title="Delete Template" onclick="deleteTemplate(<?php echo $t['id']; ?>, '<?php echo addslashes($t['name']); ?>')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
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
<div class="modal fade" id="modalTemplate" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle"><i class="fas fa-certificate me-2"></i>Add Certificate Template</h5>
                <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="templateForm" novalidate enctype="multipart/form-data">
                <input type="hidden" name="action" value="save_template">
                <input type="hidden" name="type" value="certificate">
                <input type="hidden" name="id" id="temp-id" value="">
                <div class="modal-body px-3 py-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Template Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="temp-name" placeholder="e.g. Standard Landscape Certificate" required>
                        <div class="invalid-feedback">Template name is required.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Background Image <span class="text-danger" id="bg-req">*</span></label>
                        <input type="file" class="form-control" name="background_image" id="temp-bg" accept="image/*" required>
                        <small class="text-muted">Recommended: A4 Landscape Resolution (3508 x 2480 px)</small>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold text-dark">Status</label>
                        <select class="form-select" name="status" id="temp-status">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top py-2">
                    <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">CANCEL</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold" id="btn-save">
                        <i class="fas fa-save me-2"></i>SAVE TEMPLATE
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const HANDLER = '<?php echo BASE_URL; ?>ajax/card_handler.php';
let modalTemplate;

document.addEventListener('DOMContentLoaded', () => {
    modalTemplate = new bootstrap.Modal(document.getElementById('modalTemplate'));
    const form = document.getElementById('templateForm');
    
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!form.checkValidity()) { form.classList.add('was-validated'); return; }
        
        const btn = document.getElementById('btn-save');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>SAVING...';
        
        fetch(HANDLER, { method: 'POST', body: new FormData(form) })
        .then(r => r.json()).then(res => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-2"></i>SAVE TEMPLATE';
            if(res.success) { location.reload(); }
            else alert(res.message);
        });
    });
});

function openAddTemplate() {
    resetTemplateForm();
    modalTemplate.show();
}

function resetTemplateForm() {
    const f = document.getElementById('templateForm');
    f.reset();
    f.classList.remove('was-validated');
    document.getElementById('temp-id').value = '';
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-certificate me-2"></i>Add Certificate Template';
    document.getElementById('temp-bg').setAttribute('required', '');
    document.getElementById('bg-req').classList.remove('d-none');
}

function editTemplate(t) {
    resetTemplateForm();
    document.getElementById('temp-id').value = t.id;
    document.getElementById('temp-name').value = t.name;
    document.getElementById('temp-status').value = t.status;
    
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>Edit Template — ' + t.name;
    document.getElementById('temp-bg').removeAttribute('required');
    document.getElementById('bg-req').classList.add('d-none');
    
    modalTemplate.show();
}

function deleteTemplate(id, name) {
    if(confirm(`Are you sure you want to delete the template "${name}"?`)) {
        const fd = new FormData();
        fd.append('action', 'delete_template');
        fd.append('id', id);
        fetch(HANDLER, { method: 'POST', body: fd })
        .then(r => r.json()).then(res => {
            if(res.success) { location.reload(); }
            else alert(res.message);
        });
    }
}
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>
