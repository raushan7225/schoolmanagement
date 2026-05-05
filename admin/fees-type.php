<?php
// admin/fees-type.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Fetch existing fee types
$stmt = $pdo->query("SELECT * FROM fee_types ORDER BY created_at DESC");
$fee_types = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>Fees Type Configuration</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Student Accounting</li>
            <li class="breadcrumb-item active">Fees Type</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <!-- Fee Types List -->
        <div class="col-12">
            <div class="card">
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle datatable-premium"
                               data-add-btn='{"text":"Add Fees Type","onclick":"openAddFee()","icon":"fas fa-plus-circle"}'>
                            <thead class="table-light">
                                <tr>
                                    <th width="60">S.No.</th>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Description</th>
                                    <th data-no-sort>Status</th>
                                    <th class="text-end" data-no-sort>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $sn=1; foreach($fee_types as $ft): ?>
                                <tr id="feerow-<?php echo $ft['id']; ?>">
                                    <td><?php echo $sn++; ?></td>
                                    <td><div class="fw-bold text-dark"><?php echo htmlspecialchars($ft['name']); ?></div></td>
                                    <td><span class="badge bg-light text-dark border px-3"><?php echo htmlspecialchars($ft['fee_code']); ?></span></td>
                                    <td><small class="text-muted"><?php echo htmlspecialchars($ft['description']); ?></small></td>
                                    <td>
                                        <span class="badge bg-<?php echo $ft['status'] == 1 ? 'success' : 'danger'; ?> rounded-pill">
                                            <?php echo $ft['status'] == 1 ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-warning btn-icon me-1" title="Edit" onclick='editFee(<?php echo json_encode($ft); ?>)'>
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger btn-icon" title="Delete" onclick="deleteFee(<?php echo $ft['id']; ?>, '<?php echo addslashes($ft['name']); ?>')">
                                            <i class="fas fa-trash"></i>
                                        </button>
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
<div class="modal fade" id="modalFee" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="modalTitle"><i class="fas fa-plus-circle me-2"></i>Add Fees Type</h5>
            <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                <i class="<?php echo v('theme_modal_close_icon', 'fas fa-times'); ?>"></i>
            </button>
        </div>
        <form id="feeTypeForm" novalidate>
            <input type="hidden" name="action" value="save_fee_type">
            <input type="hidden" name="id" id="fee-id" value="">
            <div class="modal-body px-3 py-4">
                <div class="mb-3">
                    <label class="form-label fw-bold">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" id="fee-name" placeholder="e.g. Admission Fee" required>
                    <div class="invalid-feedback">Please enter the fee name.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Fees Code</label>
                    <input type="text" class="form-control" name="fee_code" id="fee-code" placeholder="e.g. ADM_FEE">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Description</label>
                    <textarea class="form-control" name="description" id="fee-desc" rows="2" placeholder="Brief description..."></textarea>
                </div>
                <div class="mb-0">
                    <label class="form-label fw-bold">Status</label>
                    <select class="form-select" name="status" id="fee-status">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer bg-light border-top py-2">
                <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">CANCEL</button>
                <button type="submit" class="btn btn-primary px-4 fw-bold" id="btn-save">
                    <i class="fas fa-save me-2"></i>SAVE FEES TYPE
                </button>
            </div>
        </form>
    </div>
  </div>
</div>

<script>
'use strict';
const HANDLER = '<?php echo BASE_URL; ?>ajax/accounting_handler.php';
let modalFee;

document.addEventListener('DOMContentLoaded', function() {
    modalFee = new bootstrap.Modal(document.getElementById('modalFee'));
    const form = document.getElementById('feeTypeForm');

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!form.checkValidity()) { form.classList.add('was-validated'); return; }
        
        const btn = document.getElementById('btn-save');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>SAVING...';
        
        fetch(HANDLER, { method: 'POST', body: new FormData(form) })
        .then(r => r.json()).then(res => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-2"></i>SAVE FEES TYPE';
            if(res.success) { location.reload(); }
            else alert(res.message);
        });
    });
});

function openAddFee() {
    resetForm();
    modalFee.show();
}

function editFee(f) {
    resetForm();
    document.getElementById('fee-id').value = f.id;
    document.getElementById('fee-name').value = f.name;
    document.getElementById('fee-code').value = f.fee_code;
    document.getElementById('fee-desc').value = f.description;
    document.getElementById('fee-status').value = f.status;
    
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>Edit Fees Type';
    modalFee.show();
}

function resetForm() {
    const form = document.getElementById('feeTypeForm');
    form.reset();
    form.classList.remove('was-validated');
    document.getElementById('fee-id').value = '';
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-plus-circle me-2"></i>Add Fees Type';
}

function deleteFee(id, name) {
    window.confirmDelete({
        target: name,
        onConfirm: function(modal, btn) {
            const fd = new FormData();
            fd.append('action', 'delete_fee_type');
            fd.append('id', id);
            fetch(HANDLER, { method: 'POST', body: fd })
            .then(r => r.json()).then(res => {
                modal.hide();
                if(res.success) { 
                    location.reload();
                    Swal.fire('Deleted!', 'Fee type has been removed.', 'success');
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            });
        }
    });
}
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>
