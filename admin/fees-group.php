<?php
// admin/fees-group.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Fetch active fee types for the selection
$fee_types = $pdo->query("SELECT * FROM fee_types WHERE status = 1 ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch existing fee groups with their totals and items
$stmt = $pdo->query("
    SELECT fg.*, 
    (SELECT SUM(amount) FROM fee_group_items WHERE fee_group_id = fg.id) as total_amount
    FROM fee_groups fg 
    ORDER BY fg.created_at DESC
");
$fee_groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Helper to fetch items for a specific group (used in JS for editing)
function getGroupItems($pdo, $group_id) {
    $stmt = $pdo->prepare("SELECT fee_type_id, amount FROM fee_group_items WHERE fee_group_id = ?");
    $stmt->execute([$group_id]);
    return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
}

// Pre-load all group items for fast editing
$all_group_items = [];
foreach ($fee_groups as $fg) {
    $all_group_items[$fg['id']] = getGroupItems($pdo, $fg['id']);
}
?>

<div class="pagetitle">
    <h1>Fees Group Configuration</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Student Accounting</li>
            <li class="breadcrumb-item active">Fees Group</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <!-- Fee Groups List -->
        <div class="col-12">
            <div class="card">
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle datatable-premium"
                               data-add-btn='{"text":"Create Fees Group","onclick":"openAddGroup()","icon":"fas fa-layer-group"}'>
                            <thead class="table-light">
                                <tr>
                                    <th width="60">S.No.</th>
                                    <th>Group Name</th>
                                    <th>Total Amount</th>
                                    <th data-no-sort>Status</th>
                                    <th class="text-end" data-no-sort>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $sn=1; foreach($fee_groups as $fg): ?>
                                <tr id="grouprow-<?php echo $fg['id']; ?>">
                                    <td><?php echo $sn++; ?></td>
                                    <td>
                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($fg['name']); ?></div>
                                        <div class="text-muted small"><?php echo htmlspecialchars($fg['description']); ?></div>
                                    </td>
                                    <td><span class="fw-bold fs-6 text-primary">₹ <?php echo number_format($fg['total_amount'], 2); ?></span></td>
                                    <td>
                                        <span class="badge bg-<?php echo $fg['status'] == 1 ? 'success' : 'danger'; ?> rounded-pill">
                                            <?php echo $fg['status'] == 1 ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-warning btn-icon me-1" title="Edit" onclick='editGroup(<?php echo $fg['id']; ?>, "<?php echo addslashes($fg['name']); ?>", "<?php echo addslashes($fg['description']); ?>", <?php echo $fg['status']; ?>)'>
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger btn-icon" title="Delete" onclick="deleteGroup(<?php echo $fg['id']; ?>, '<?php echo addslashes($fg['name']); ?>')">
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
<div class="modal fade" id="modalGroup" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="modalTitle"><i class="fas fa-layer-group me-2"></i>Create Fees Group</h5>
            <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                <i class="<?php echo v('theme_modal_close_icon', 'fas fa-times'); ?>"></i>
            </button>
        </div>
        <form id="feeGroupForm" novalidate>
            <input type="hidden" name="action" value="save_fee_group">
            <input type="hidden" name="id" id="group-id" value="">
            <div class="modal-body px-3 py-4">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Group Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" id="group-name" placeholder="e.g. IT Bundle" required>
                            <div class="invalid-feedback">Please enter a group name.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Description</label>
                            <textarea class="form-control" name="description" id="group-desc" rows="3" placeholder="Brief description..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Status</label>
                            <select class="form-select" name="status" id="group-status">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 border-start">
                        <h6 class="fw-bold mb-3 text-primary">
                            <i class="fas fa-check-square me-2"></i>Assign Fee Types
                        </h6>
                        <div class="bg-light rounded p-3" style="max-height: 250px; overflow-y: auto;">
                            <?php if(empty($fee_types)): ?>
                                <p class="text-muted small mb-0 text-center">Please add Fee Types first.</p>
                            <?php else: foreach($fee_types as $ft): ?>
                            <div class="row mb-2 align-items-center fee-item">
                                <div class="col-7">
                                    <div class="form-check">
                                        <input class="form-check-input fee-checkbox" type="checkbox" id="fee-<?php echo $ft['id']; ?>" data-id="<?php echo $ft['id']; ?>">
                                        <label class="form-check-label fw-semibold small" for="fee-<?php echo $ft['id']; ?>">
                                            <?php echo htmlspecialchars($ft['name']); ?>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-5">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">₹</span>
                                        <input type="number" class="form-control fw-bold fee-amount" name="items[<?php echo $ft['id']; ?>]" id="amount-<?php echo $ft['id']; ?>" placeholder="0" value="0" disabled>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top py-2">
                <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">CANCEL</button>
                <button type="submit" class="btn btn-primary px-4 fw-bold" id="btn-save">
                    <i class="fas fa-save me-2"></i>SAVE GROUP
                </button>
            </div>
        </form>
    </div>
  </div>
</div>

<script>
'use strict';
const HANDLER = '<?php echo BASE_URL; ?>ajax/accounting_handler.php';
const ALL_ITEMS = <?php echo json_encode($all_group_items); ?>;
let modalGroup;

document.addEventListener('DOMContentLoaded', function() {
    modalGroup = new bootstrap.Modal(document.getElementById('modalGroup'));
    const form = document.getElementById('feeGroupForm');
    
    document.querySelectorAll('.fee-checkbox').forEach(cb => {
        cb.addEventListener('change', function() {
            const amountInput = document.getElementById('amount-' + this.dataset.id);
            amountInput.disabled = !this.checked;
            if(!this.checked) amountInput.value = 0;
        });
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
            btn.innerHTML = '<i class="fas fa-save me-2"></i>SAVE GROUP';
            if(res.success) { location.reload(); }
            else alert(res.message);
        });
    });
});

function openAddGroup() {
    resetForm();
    modalGroup.show();
}

function editGroup(id, name, desc, status) {
    resetForm();
    document.getElementById('group-id').value = id;
    document.getElementById('group-name').value = name;
    document.getElementById('group-desc').value = desc;
    document.getElementById('group-status').value = status;
    
    const items = ALL_ITEMS[id] || {};
    for (const typeId in items) {
        const cb = document.getElementById('fee-' + typeId);
        const amt = document.getElementById('amount-' + typeId);
        if (cb && amt) {
            cb.checked = true;
            amt.disabled = false;
            amt.value = items[typeId];
        }
    }

    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>Edit Fees Group';
    modalGroup.show();
}

function resetForm() {
    const form = document.getElementById('feeGroupForm');
    form.reset();
    form.classList.remove('was-validated');
    document.getElementById('group-id').value = '';
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-layer-group me-2"></i>Create Fees Group';
    document.querySelectorAll('.fee-amount').forEach(inp => inp.disabled = true);
}

function deleteGroup(id, name) {
    window.confirmDelete({
        target: name,
        onConfirm: function(modal, btn) {
            const fd = new FormData();
            fd.append('action', 'delete_fee_group');
            fd.append('id', id);
            fetch(HANDLER, { method: 'POST', body: fd })
            .then(r => r.json()).then(res => {
                modal.hide();
                if(res.success) { 
                    location.reload();
                    Swal.fire('Deleted!', 'Fee group has been removed.', 'success');
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            });
        }
    });
}
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>
