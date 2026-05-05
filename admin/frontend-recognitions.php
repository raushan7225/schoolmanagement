<?php
// admin/frontend-recognitions.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Fetch existing recognitions
$stmt = $pdo->query("SELECT * FROM frontend_recognitions ORDER BY sort_order ASC, created_at DESC");
$recognitions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>Recognitions & Approvals</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Frontend</li>
            <li class="breadcrumb-item active">Recognitions</li>
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
                               data-add-btn='{"text":"Add Recognition","onclick":"openAddModal()","icon":"fas fa-plus-circle"}'>
                            <thead class="table-light">
                                <tr>
                                    <th width="60">Sort</th>
                                    <th>Recognition Title</th>
                                    <th>Document</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-end" data-no-sort>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($recognitions)): foreach($recognitions as $r): ?>
                                <tr id="recrow-<?php echo $r['id']; ?>">
                                    <td><?php echo $r['sort_order']; ?></td>
                                    <td class="fw-bold text-primary small"><?php echo htmlspecialchars($r['title']); ?></td>
                                    <td>
                                        <a href="<?php echo BASE_URL; ?>media/frontend/<?php echo $r['file_path']; ?>" target="_blank" class="badge bg-light text-info border border-info px-3 text-decoration-none small">
                                            <i class="fas <?php echo str_ends_with($r['file_path'], '.pdf') ? 'fa-file-pdf' : 'fa-image'; ?> me-1"></i> View Doc
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-<?php echo $r['status'] == 1 ? 'success' : 'secondary'; ?> rounded-pill px-3">
                                            <?php echo $r['status'] == 1 ? 'Active' : 'Hidden'; ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-warning btn-icon me-1" title="Edit" onclick="editRec(<?php echo $r['id']; ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger btn-icon" title="Delete" onclick="deleteRec(<?php echo $r['id']; ?>, '<?php echo addslashes($r['title']); ?>')">
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
<div class="modal fade" id="modalRec" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle"><i class="fas fa-award me-2"></i>Recognition Form</h5>
                <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="recognitionForm" novalidate enctype="multipart/form-data">
                <input type="hidden" name="action" value="save_recognition">
                <input type="hidden" name="id" id="r-id" value="">
                <div class="modal-body px-3 py-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Board / Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="title" id="r-title" placeholder="e.g. NIOS Approval Letter" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Sort Order</label>
                            <input type="number" class="form-control" name="sort_order" id="r-sort" value="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Status</label>
                            <select class="form-select" name="status" id="r-status">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-bold">Certificate (PDF/Image) <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="file" id="r-file" accept=".pdf,image/*">
                        <div class="mt-2 d-none" id="file-link-box">
                            <a href="" id="file-link" target="_blank" class="btn btn-sm btn-light border text-primary w-100">
                                <i class="fas fa-external-link-alt me-1"></i>View Current File
                            </a>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top py-2">
                    <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">CANCEL</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold" id="btn-save">
                        <i class="fas fa-save me-2"></i>SAVE RECORD
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
'use strict';
const HANDLER = '<?php echo BASE_URL; ?>ajax/frontend_handler.php';
const ALL_RECS = <?php echo json_encode($recognitions); ?>;
let modalRec;

document.addEventListener('DOMContentLoaded', function() {
    modalRec = new bootstrap.Modal(document.getElementById('modalRec'));
    const form = document.getElementById('recognitionForm');

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!form.checkValidity()) { form.classList.add('was-validated'); return; }
        
        const btn = document.getElementById('btn-save');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>SAVING...';
        
        fetch(HANDLER, { method: 'POST', body: new FormData(form) })
        .then(r => r.json()).then(res => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-2"></i>SAVE RECORD';
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
    modalRec.show();
}

function editRec(id) {
    const r = ALL_RECS.find(x => x.id == id);
    if (!r) return;
    
    resetForm();
    document.getElementById('r-id').value = r.id;
    document.getElementById('r-title').value = r.title;
    document.getElementById('r-sort').value = r.sort_order;
    document.getElementById('r-status').value = r.status;
    
    if(r.file_path) {
        document.getElementById('file-link-box').classList.remove('d-none');
        document.getElementById('file-link').href = `<?php echo BASE_URL; ?>media/frontend/${r.file_path}`;
    }
    
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>Edit Recognition';
    modalRec.show();
}

function resetForm() {
    const form = document.getElementById('recognitionForm');
    form.reset();
    form.classList.remove('was-validated');
    document.getElementById('r-id').value = '';
    document.getElementById('file-link-box').classList.add('d-none');
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-award me-2"></i>Recognition Form';
}

function deleteRec(id, name) {
    window.confirmDelete({
        target: name,
        onConfirm: function(cModal, btn) {
            const fd = new FormData();
            fd.append('action', 'delete_recognition');
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
