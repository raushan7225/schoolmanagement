<?php
// admin/voucher-expense.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Fetch expense heads
$heads = $pdo->query("SELECT id, name FROM voucher_heads WHERE type = 'expense' AND status = 1 ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Search / Filter
$filter_head = (int)($_GET['head_id'] ?? 0);
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';

$where = "WHERE ot.type = 'expense'";
$params = [];

if ($filter_head) {
    $where .= " AND ot.voucher_head_id = ?";
    $params[] = $filter_head;
}
if ($date_from) {
    $where .= " AND ot.date >= ?";
    $params[] = $date_from;
}
if ($date_to) {
    $where .= " AND ot.date <= ?";
    $params[] = $date_to;
}

// Fetch existing expense transactions
$stmt = $pdo->prepare("
    SELECT ot.*, vh.name as head_name
    FROM office_transactions ot
    JOIN voucher_heads vh ON ot.voucher_head_id = vh.id
    $where
    ORDER BY ot.date DESC, ot.created_at DESC
");
$stmt->execute($params);
$expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>Expense Vouchers</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Office Accounting</li>
            <li class="breadcrumb-item active">Voucher Expense</li>
        </ol>
    </nav>
</div>

<section class="section">
    <!-- Filter Bar -->
    <div class="card mb-3">
        <div class="card-body py-2">
            <form class="row g-2 align-items-center" method="GET">
                <div class="col-md-4">
                    <select name="head_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Heads...</option>
                        <?php foreach($heads as $h): ?>
                            <option value="<?php echo $h['id']; ?>" <?php echo $filter_head == $h['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($h['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_from" class="form-control form-control-sm" value="<?php echo $date_from; ?>">
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_to" class="form-control form-control-sm" value="<?php echo $date_to; ?>">
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary flex-grow-1 fw-bold"><i class="fas fa-filter me-2"></i>APPLY</button>
                    <a href="voucher-expense.php" class="btn btn-sm btn-outline-secondary fw-bold">RESET</a>
                </div>
            </form>
        </div>
    </div>

        <!-- Expense List -->
        <div class="col-12">
            <div class="card">
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle datatable-premium"
                               data-add-btn='{"text":"New Expense Voucher","onclick":"openAddTxn()","icon":"fas fa-plus-circle"}'>
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Voucher No</th>
                                    <th>Expense Head</th>
                                    <th>Reference</th>
                                    <th>Amount</th>
                                    <th data-no-sort>Mode</th>
                                    <th class="text-end" data-no-sort>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($expenses as $e): ?>
                                <tr id="txrow-<?php echo $e['id']; ?>">
                                    <td><div class="fw-bold text-dark"><?php echo date('d M, Y', strtotime($e['date'])); ?></div></td>
                                    <td><code class="text-danger fw-bold">#<?php echo $e['id']; ?></code></td>
                                    <td>
                                        <div class="fw-bold"><?php echo htmlspecialchars($e['head_name']); ?></div>
                                        <small class="text-muted"><?php echo htmlspecialchars($e['description'] ?: ''); ?></small>
                                    </td>
                                    <td><small><?php echo htmlspecialchars($e['transaction_id'] ?: '—'); ?></small></td>
                                    <td><span class="fw-bold text-danger">₹ <?php echo number_format($e['amount'], 2); ?></span></td>
                                    <td><span class="badge bg-light text-dark border"><?php echo $e['payment_mode']; ?></span></td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-warning btn-icon me-1" title="Edit" onclick='editTxn(<?php echo json_encode($e); ?>)'><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-outline-danger btn-icon" title="Delete" onclick="deleteTxn(<?php echo $e['id']; ?>)"><i class="fas fa-trash"></i></button>
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
<div class="modal fade" id="modalTxn" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="modalTitle"><i class="fas fa-arrow-up me-2"></i>New Expense Voucher</h5>
            <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                <i class="<?php echo v('theme_modal_close_icon', 'fas fa-times'); ?>"></i>
            </button>
        </div>
        <form id="expenseForm" novalidate>
            <input type="hidden" name="action" value="save_transaction">
            <input type="hidden" name="id" id="txn-id" value="">
            <input type="hidden" name="type" value="expense">
            
            <div class="modal-body px-3 py-4">
                <div class="mb-3">
                    <label class="form-label fw-bold">Expense Head <span class="text-danger">*</span></label>
                    <select class="form-select" name="voucher_head_id" id="t-head" required>
                        <option value="">Select Head...</option>
                        <?php foreach($heads as $h): ?>
                            <option value="<?php echo $h['id']; ?>"><?php echo htmlspecialchars($h['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">Please select an expense head.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Amount (₹) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">₹</span>
                        <input type="number" class="form-control fw-bold" name="amount" id="t-amount" placeholder="0.00" step="0.01" required>
                        <div class="invalid-feedback">Please enter the amount.</div>
                    </div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-bold">Payment Mode</label>
                        <select class="form-select" name="payment_mode" id="t-mode" required>
                            <option value="Cash">Cash</option>
                            <option value="Online">Online / UPI</option>
                            <option value="Bank">Bank Transfer</option>
                            <option value="Cheque">Cheque</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold">Date</label>
                        <input type="date" class="form-control" name="date" id="t-date" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Reference (Optional)</label>
                    <input type="text" class="form-control" name="transaction_id" id="t-ref" placeholder="Txn ID / Cheque No">
                </div>
                <div class="mb-0">
                    <label class="form-label fw-bold">Description / Remarks</label>
                    <textarea class="form-control" name="description" id="t-desc" rows="2" placeholder="Optional details..."></textarea>
                </div>
            </div>
            <div class="modal-footer bg-light border-top py-2">
                <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">CANCEL</button>
                <button type="submit" class="btn btn-danger px-4 fw-bold" id="btn-save">
                    <i class="fas fa-save me-2"></i>SAVE VOUCHER
                </button>
            </div>
        </form>
    </div>
  </div>
</div>

<script>
const HANDLER = 'handlers/voucher_handler.php';
let modalTxn;

document.addEventListener('DOMContentLoaded', function() {
    modalTxn = new bootstrap.Modal(document.getElementById('modalTxn'));
    const form = document.getElementById('expenseForm');

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
                btn.innerHTML = '<i class="fas fa-save me-2"></i>SAVE VOUCHER';
                if(res.success) { 
                    Swal.fire({ icon: 'success', title: 'Success', text: res.message, timer: 1500, showConfirmButton: false })
                    .then(() => location.reload()); 
                }
                else Swal.fire('Error', res.message, 'error');
            });
    });
});

function openAddTxn() {
    document.getElementById('expenseForm').reset();
    document.getElementById('txn-id').value = '';
    document.getElementById('t-date').value = '<?php echo date('Y-m-d'); ?>';
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-arrow-up me-2"></i>New Expense Voucher';
    document.getElementById('expenseForm').classList.remove('was-validated');
    modalTxn.show();
}

function editTxn(d) {
    document.getElementById('expenseForm').reset();
    document.getElementById('txn-id').value = d.id;
    document.getElementById('t-head').value = d.voucher_head_id;
    document.getElementById('t-amount').value = d.amount;
    document.getElementById('t-mode').value = d.payment_mode;
    document.getElementById('t-date').value = d.date;
    document.getElementById('t-ref').value = d.transaction_id;
    document.getElementById('t-desc').value = d.description;
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>Edit Expense Voucher';
    document.getElementById('expenseForm').classList.remove('was-validated');
    modalTxn.show();
}

function deleteTxn(id) {
    window.confirmDelete({
        target: 'this transaction',
        onConfirm: function(modal, btn) {
            const fd = new FormData();
            fd.append('action', 'delete_transaction');
            fd.append('id', id);
            fetch(HANDLER, { method: 'POST', body: fd })
            .then(r => r.json()).then(res => {
                modal.hide();
                if(res.success) { 
                    Swal.fire({ icon: 'success', title: 'Deleted!', text: res.message, timer: 1500, showConfirmButton: false })
                    .then(() => location.reload()); 
                }
                else Swal.fire('Error', res.message, 'error');
            });
        }
    });
}
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>
