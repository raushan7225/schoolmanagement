<?php
// admin/frontend-affiliation-logo.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Fetch existing affiliations
$stmt = $pdo->query("SELECT * FROM frontend_affiliations ORDER BY created_at DESC");
$logos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>Affiliations & Partner Logos</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Frontend</li>
            <li class="breadcrumb-item active">Partner Logos</li>
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
                               data-add-btn='{"text":"Add New Logo","onclick":"openAddModal()","icon":"fas fa-plus-circle"}'>
                            <thead class="table-light">
                                <tr>
                                    <th width="60">S.No.</th>
                                    <th>Partner / Brand</th>
                                    <th>Status</th>
                                    <th class="text-end" data-no-sort>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($logos)): $sn=1; foreach($logos as $l): ?>
                                <tr id="logorow-<?php echo $l['id']; ?>">
                                    <td><?php echo $sn++; ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light p-1 rounded border me-2" style="width: 80px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                                <img src="<?php echo BASE_URL; ?>media/frontend/<?php echo $l['logo']; ?>" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                            </div>
                                            <div>
                                                <div class="fw-bold text-primary small"><?php echo htmlspecialchars($l['name']); ?></div>
                                                <?php if($l['link']): ?>
                                                    <a href="<?php echo $l['link']; ?>" target="_blank" class="x-small text-info text-decoration-none"><i class="fas fa-external-link-alt me-1"></i>Visit</a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $l['status'] == 1 ? 'success' : 'secondary'; ?> rounded-pill px-3">
                                            <?php echo $l['status'] == 1 ? 'Active' : 'Hidden'; ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-warning btn-icon me-1" title="Edit" onclick="editLogo(<?php echo $l['id']; ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger btn-icon" title="Delete" onclick="deleteLogo(<?php echo $l['id']; ?>, '<?php echo addslashes($l['name']); ?>')">
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
<div class="modal fade" id="modalLogo" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle"><i class="fas fa-handshake me-2"></i>Partner Detail</h5>
                <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="logoForm" novalidate enctype="multipart/form-data">
                <input type="hidden" name="action" value="save_affiliation">
                <input type="hidden" name="id" id="l-id" value="">
                <div class="modal-body px-3 py-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Partner Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="l-name" placeholder="e.g. ISO Certified" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Website Link (Optional)</label>
                        <input type="url" class="form-control" name="link" id="l-link" placeholder="https://...">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Status</label>
                        <select class="form-select" name="status" id="l-status">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-bold">Logo Image <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="logo" id="l-logo" accept="image/*">
                        <div class="mt-2 d-none text-center" id="img-preview-box">
                            <img src="" id="img-preview" class="img-fluid rounded border p-2 bg-light shadow-sm" style="max-height: 100px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top py-2">
                    <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">CANCEL</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold" id="btn-save">
                        <i class="fas fa-save me-2"></i>SAVE LOGO
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
'use strict';
const HANDLER = '<?php echo BASE_URL; ?>ajax/frontend_handler.php';
const ALL_LOGOS = <?php echo json_encode($logos); ?>;
let modalLogo;

document.addEventListener('DOMContentLoaded', function() {
    modalLogo = new bootstrap.Modal(document.getElementById('modalLogo'));
    const form = document.getElementById('logoForm');

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!form.checkValidity()) { form.classList.add('was-validated'); return; }
        
        const btn = document.getElementById('btn-save');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>SAVING...';
        
        fetch(HANDLER, { method: 'POST', body: new FormData(form) })
        .then(r => r.json()).then(res => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-2"></i>SAVE LOGO';
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
    modalLogo.show();
}

function editLogo(id) {
    const l = ALL_LOGOS.find(x => x.id == id);
    if (!l) return;
    
    resetForm();
    document.getElementById('l-id').value = l.id;
    document.getElementById('l-name').value = l.name;
    document.getElementById('l-link').value = l.link;
    document.getElementById('l-status').value = l.status;
    
    if(l.logo) {
        document.getElementById('img-preview-box').classList.remove('d-none');
        document.getElementById('img-preview').src = `<?php echo BASE_URL; ?>media/frontend/${l.logo}`;
    }
    
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>Edit Partner Logo';
    modalLogo.show();
}

function resetForm() {
    const form = document.getElementById('logoForm');
    form.reset();
    form.classList.remove('was-validated');
    document.getElementById('l-id').value = '';
    document.getElementById('img-preview-box').classList.add('d-none');
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-handshake me-2"></i>Partner Detail';
}

function deleteLogo(id, name) {
    window.confirmDelete({
        target: name,
        onConfirm: function(cModal, btn) {
            const fd = new FormData();
            fd.append('action', 'delete_affiliation');
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
