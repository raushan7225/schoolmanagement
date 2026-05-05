<?php
// admin/voucher-head.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Fetch existing voucher heads
$where = "WHERE 1=1";
$params = [];
$filter_type = $_GET['type'] ?? '';
$search = trim($_GET['search'] ?? '');

if ($filter_type) {
    $where .= " AND type = ?";
    $params[] = $filter_type;
}

if ($search) {
    $where .= " AND name LIKE ?";
    $params[] = "%$search%";
}

$stmt = $pdo->prepare("SELECT * FROM voucher_heads $where ORDER BY name ASC");
$stmt->execute($params);
$heads = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>Voucher Heads Configuration</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Office Accounting</li>
            <li class="breadcrumb-item active">Voucher Head</li>
        </ol>
    </nav>
</div>

<section class="section">
    <!-- Filter Bar -->
    <div class="card mb-3">
        <div class="card-body py-2">
            <form class="row g-2 align-items-center" method="GET">
                <div class="col-md-5">
                    <select name="type" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Types...</option>
                        <option value="deposit" <?php echo $filter_type == 'deposit' ? 'selected' : ''; ?>>Income (Deposit)</option>
                        <option value="expense" <?php echo $filter_type == 'expense' ? 'selected' : ''; ?>>Expense (Voucher)</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search Head Name..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary flex-grow-1 fw-bold"><i class="fas fa-filter me-2"></i>APPLY</button>
                    <a href="voucher-head.php" class="btn btn-sm btn-outline-secondary fw-bold">RESET</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <!-- Head List -->
        <div class="col-12">
            <div class="card">
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle datatable-premium"
                               data-add-btn='{"text":"Add Accounting Head","onclick":"openAddHead()","icon":"fas fa-plus-circle"}'>
                            <thead class="table-light">
                                <tr>
                                    <th width="60">S.No.</th>
                                    <th>Head Name</th>
                                    <th>Type</th>
                                    <th>Description</th>
                                    <th data-no-sort>Status</th>
                                    <th class="text-end" data-no-sort>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $sn=1; foreach($heads as $h): ?>
                                <tr id="headrow-<?php echo $h['id']; ?>">
                                    <td><?php echo $sn++; ?></td>
                                    <td class="fw-bold"><?php echo htmlspecialchars($h['name']); ?></td>
                                    <td>
                                        <?php if($h['type'] == 'deposit'): ?>
                                            <span class="badge bg-success-light text-success border border-success px-3 rounded-pill"><i class="fas fa-arrow-down me-1"></i>Income</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger-light text-danger border border-danger px-3 rounded-pill"><i class="fas fa-arrow-up me-1"></i>Expense</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><small class="text-muted"><?php echo htmlspecialchars($h['description'] ?: '—'); ?></small></td>
                                    <td>
                                        <?php if($h['status'] == 1): ?>
                                            <span class="badge bg-success rounded-pill">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary rounded-pill">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-warning btn-icon me-1" title="Edit" onclick='editHead(<?php echo json_encode($h); ?>)'><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-outline-danger btn-icon" title="Delete" onclick="deleteHead(<?php echo $h['id']; ?>, '<?php echo addslashes($h['name']); ?>')"><i class="fas fa-trash"></i></button>
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
<div class="modal fade" id="modalHead" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="modalTitle"><i class="fas fa-tags me-2"></i>Add Accounting Head</h5>
            <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                <i class="<?php echo v('theme_modal_close_icon', 'fas fa-times'); ?>"></i>
            </button>
        </div>
        <form id="voucherHeadForm" novalidate>
            <input type="hidden" name="action" value="save_voucher_head">
            <input type="hidden" name="id" id="head-id" value="">
            <div class="modal-body px-3 py-4">
                <div class="mb-3">
                    <label class="form-label fw-bold">Head Type <span class="text-danger">*</span></label>
                    <select class="form-select" name="type" id="head-type" required>
                        <option value="">Select Type...</option>
                        <option value="deposit">Income (Deposit)</option>
                        <option value="expense">Expense</option>
                    </select>
                    <div class="invalid-feedback">Please select a head type.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Head Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" id="head-name" placeholder="e.g. Office Rent, Salary..." required>
                    <div class="invalid-feedback">Please enter a head name.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Description</label>
                    <textarea class="form-control" name="description" id="head-desc" rows="2" placeholder="Brief details..."></textarea>
                </div>
                <div class="mb-0">
                    <label class="form-label fw-bold">Status</label>
                    <select class="form-select" name="status" id="head-status">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer bg-light border-top py-2">
                <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">CANCEL</button>
                <button type="submit" class="btn btn-primary px-4 fw-bold" id="btn-save">
                    <i class="fas fa-save me-2"></i>SAVE HEAD
                </button>
            </div>
        </form>
    </div>
  </div>
</div>

<script>
const HANDLER = 'handlers/voucher_handler.php';
let modalHead;

document.addEventListener('DOMContentLoaded', function() {
    modalHead = new bootstrap.Modal(document.getElementById('modalHead'));
    const form = document.getElementById('voucherHeadForm');

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        if (!form.checkValidity()) { form.classList.add('was-validated'); return; }

        const btn = document.getElementById('btn-save');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>SAVING...';

        fetch(HANDLER, { method: 'POST', body: new FormData(form) })
            .then(r => r.json())
            .then(res => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save me-2"></i>SAVE HEAD';
                if(res.success) { location.reload(); }
                else alert(res.message);
            });
    });
});

function openAddHead() {
    document.getElementById('voucherHeadForm').reset();
    document.getElementById('head-id').value = '';
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-plus-circle me-2"></i>Add Accounting Head';
    document.getElementById('voucherHeadForm').classList.remove('was-validated');
    modalHead.show();
}

function editHead(h) {
    document.getElementById('voucherHeadForm').reset();
    document.getElementById('head-id').value = h.id;
    document.getElementById('head-type').value = h.type;
    document.getElementById('head-name').value = h.name;
    document.getElementById('head-desc').value = h.description;
    document.getElementById('head-status').value = h.status;
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>Edit Accounting Head';
    document.getElementById('voucherHeadForm').classList.remove('was-validated');
    modalHead.show();
}

function deleteHead(id, name) {
    if(confirm(`Are you sure you want to delete "${name}"?`)) {
        const fd = new FormData();
        fd.append('action', 'delete_voucher_head');
        fd.append('id', id);
        fetch(HANDLER, { method: 'POST', body: fd })
            .then(r => r.json())
            .then(res => {
                if(res.success) { location.reload(); }
                else alert(res.message);
            });
    }
}
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>
