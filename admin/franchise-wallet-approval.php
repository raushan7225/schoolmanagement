<?php
// admin/franchise-wallet-approval.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Fetch Pending Requests
$stmt = $pdo->query("
    SELECT r.*, f.center_name, f.center_code 
    FROM franchise_wallet_requests r
    JOIN franchises f ON r.franchise_id = f.id
    WHERE r.status = 'pending'
    ORDER BY r.created_at DESC
");
$pendingRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Monthly Stats
$approvedThisMonth = $pdo->query("
    SELECT IFNULL(SUM(amount), 0) FROM franchise_wallet_requests 
    WHERE status = 'approved' AND MONTH(updated_at) = MONTH(CURRENT_DATE()) AND YEAR(updated_at) = YEAR(CURRENT_DATE())
")->fetchColumn();

$rejectedThisMonth = $pdo->query("
    SELECT IFNULL(SUM(amount), 0) FROM franchise_wallet_requests 
    WHERE status = 'rejected' AND MONTH(updated_at) = MONTH(CURRENT_DATE()) AND YEAR(updated_at) = YEAR(CURRENT_DATE())
")->fetchColumn();

// History (Filters)
$where = "WHERE r.status != 'pending'";
$params = [];
$filter_center = (int)($_GET['center_id'] ?? 0);
$filter_status = $_GET['status'] ?? '';

if ($filter_center) { $where .= " AND r.franchise_id = ?"; $params[] = $filter_center; }
if ($filter_status) { $where .= " AND r.status = ?"; $params[] = $filter_status; }

$historyStmt = $pdo->prepare("
    SELECT r.*, f.center_name, f.center_code 
    FROM franchise_wallet_requests r
    JOIN franchises f ON r.franchise_id = f.id
    $where
    ORDER BY r.updated_at DESC
");
$historyStmt->execute($params);
$history = $historyStmt->fetchAll(PDO::FETCH_ASSOC);

// Centers for filter
$centers = $pdo->query("SELECT id, center_name, center_code FROM franchises ORDER BY center_name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>Wallet Approval</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Franchise Management</li>
            <li class="breadcrumb-item active">Wallet Approval</li>
        </ol>
    </nav>
</div>

<section class="section">
    <!-- Summary Stat Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-none border-start border-warning border-4 mb-0">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small fw-bold">PENDING REQUESTS</div>
                            <div class="h4 mb-0 fw-bold text-dark"><?php echo count($pendingRequests); ?></div>
                        </div>
                        <div class="bg-warning-light p-2 rounded text-warning"><i class="fas fa-clock fa-lg"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-none border-start border-success border-4 mb-0">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small fw-bold">APPROVED (MONTH)</div>
                            <div class="h4 mb-0 fw-bold text-success">₹ <?php echo number_format($approvedThisMonth, 2); ?></div>
                        </div>
                        <div class="bg-success-light p-2 rounded text-success"><i class="fas fa-check-circle fa-lg"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-none border-start border-danger border-4 mb-0">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small fw-bold">REJECTED (MONTH)</div>
                            <div class="h4 mb-0 fw-bold text-danger">₹ <?php echo number_format($rejectedThisMonth, 2); ?></div>
                        </div>
                        <div class="bg-danger-light p-2 rounded text-danger"><i class="fas fa-times-circle fa-lg"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header border-bottom-0 pb-0 bg-transparent">
            <ul class="nav nav-tabs nav-tabs-bordered" id="walletApprovalTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending-req" type="button" role="tab">
                        <i class="fas fa-clock me-2"></i>PENDING REQUESTS
                        <?php if(count($pendingRequests) > 0): ?>
                            <span class="badge bg-danger ms-1"><?php echo count($pendingRequests); ?></span>
                        <?php endif; ?>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold" id="history-tab" data-bs-toggle="tab" data-bs-target="#history-req" type="button" role="tab">
                        <i class="fas fa-history me-2"></i>APPROVAL HISTORY
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body pt-3">
            <div class="tab-content" id="walletApprovalTabContent">

                <!-- Pending Requests Tab -->
                <div class="tab-pane fade show active" id="pending-req" role="tabpanel">
                    <div class="table-responsive mt-2">
                        <table class="table table-hover align-middle datatable-premium">
                            <thead class="table-light">
                                <tr>
                                    <th width="60">S.No.</th>
                                    <th>Date</th>
                                    <th>Req. ID</th>
                                    <th>Center (Franchise)</th>
                                    <th>Amount Requested</th>
                                    <th>Payment Info</th>
                                    <th data-no-sort>Proof</th>
                                    <th class="text-end" data-no-sort>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $sn=1; foreach($pendingRequests as $r): ?>
                                <tr id="reqrow-<?php echo $r['id']; ?>">
                                    <td><?php echo $sn++; ?></td>
                                    <td><div class="small fw-bold text-dark"><?php echo date('d M Y', strtotime($r['created_at'])); ?></div></td>
                                    <td><code class="text-primary fw-bold">#REQ-<?php echo $r['id']; ?></code></td>
                                    <td>
                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($r['center_name']); ?></div>
                                        <div class="small text-muted">Code: <?php echo htmlspecialchars($r['center_code']); ?></div>
                                    </td>
                                    <td><span class="fw-bold fs-6 text-success">₹ <?php echo number_format($r['amount'], 2); ?></span></td>
                                    <td><div class="small text-muted"><i class="fas fa-wallet me-1"></i><?php echo htmlspecialchars($r['payment_method']); ?></div></td>
                                    <td>
                                        <?php if($r['proof_file']): ?>
                                        <a href="<?php echo BASE_URL . 'media/franchise/wallets/' . $r['proof_file']; ?>" target="_blank" class="btn btn-xs btn-outline-info rounded-pill px-3">
                                            <i class="fas fa-file-image me-1"></i>View
                                        </a>
                                        <?php else: ?>
                                        <span class="text-muted small">No Proof</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group shadow-sm rounded">
                                            <button class="btn btn-sm btn-outline-success border-0 px-3" title="Approve Request"
                                                    onclick='openApproveModal(<?php echo json_encode($r); ?>)'>
                                                <i class="fas fa-check me-1"></i>Approve
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger border-0 px-3" title="Reject Request"
                                                    onclick='openRejectModal(<?php echo json_encode($r); ?>)'>
                                                <i class="fas fa-times me-1"></i>Reject
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- History Tab -->
                <div class="tab-pane fade" id="history-req" role="tabpanel">
            <div class="card">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Approval History</h5>
                </div>
                <div class="card-body pt-3">
                    <!-- Filter Bar -->
                    <div class="card bg-light border shadow-none mb-3">
                        <div class="card-body py-2">
                            <form class="row g-2 align-items-center" method="GET">
                                <div class="col-md-5">
                                    <select name="center_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                        <option value="">All Centers...</option>
                                        <?php foreach($centers as $c): ?>
                                            <option value="<?php echo $c['id']; ?>" <?php echo $filter_center == $c['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['center_name']); ?> (<?php echo $c['center_code']; ?>)</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                        <option value="">All Status...</option>
                                        <option value="approved" <?php echo $filter_status == 'approved' ? 'selected' : ''; ?>>✅ Approved</option>
                                        <option value="rejected" <?php echo $filter_status == 'rejected' ? 'selected' : ''; ?>>❌ Rejected</option>
                                    </select>
                                </div>
                                <div class="col-md-3 d-flex align-items-end gap-2">
                                    <button type="submit" class="btn btn-sm btn-primary flex-grow-1 fw-bold"><i class="fas fa-filter me-2"></i>APPLY</button>
                                    <a href="franchise-wallet-approval.php" class="btn btn-sm btn-outline-secondary fw-bold">RESET</a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle datatable-premium">
                            <thead class="table-light">
                                <tr>
                                    <th width="60">S.No.</th>
                                    <th>Updated Date</th>
                                    <th>Req. ID</th>
                                    <th>Center (Franchise)</th>
                                    <th>Amount</th>
                                    <th data-no-sort>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $sn=1; foreach($history as $h): ?>
                                <tr>
                                    <td><?php echo $sn++; ?></td>
                                    <td><div class="small fw-bold text-dark"><?php echo date('d M Y', strtotime($h['updated_at'])); ?></div></td>
                                    <td><code class="text-muted fw-bold">#REQ-<?php echo $h['id']; ?></code></td>
                                    <td>
                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($h['center_name']); ?></div>
                                        <div class="small text-muted">Code: <?php echo htmlspecialchars($h['center_code']); ?></div>
                                    </td>
                                    <td><span class="fw-bold text-<?php echo $h['status']=='approved'?'success':'danger'; ?>">₹ <?php echo number_format($h['amount'], 2); ?></span></td>
                                    <td>
                                        <span class="badge bg-<?php echo $h['status']=='approved'?'success':'danger'; ?> rounded-pill px-3">
                                            <i class="fas fa-<?php echo $h['status']=='approved'?'check':'times'; ?>-circle me-1"></i>
                                            <?php echo strtoupper($h['status']); ?>
                                        </span>
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

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-check-circle me-2"></i>Confirm Wallet Approval</h5>
                <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                    <i class="<?php echo v('theme_modal_close_icon', 'fas fa-times'); ?>"></i>
                </button>
            </div>
            <form id="approveForm" novalidate>
                <input type="hidden" name="action" value="approve_wallet">
                <input type="hidden" name="id" id="approve-id">
                <div class="modal-body px-3 py-4">
                    <div class="enquiry-view-premium p-1">
                        <div class="view-section mb-4">
                            <div class="view-section-header">REQUEST OVERVIEW</div>
                            <div class="row g-3 px-3 py-3">
                                <div class="col-md-12">
                                    <div class="alert alert-info border-0 rounded-3 mb-0 shadow-sm">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Confirming this will add <strong id="approve-amount" class="text-dark">₹ 0.00</strong> to 
                                        <strong id="approve-center" class="text-dark">Center Name</strong> wallet balance.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="view-section">
                            <div class="view-section-header">APPROVAL REMARKS</div>
                            <div class="px-3 py-3">
                                <label class="form-label fw-bold small text-muted">ADMIN NOTE (OPTIONAL)</label>
                                <textarea class="form-control" name="admin_remarks" rows="3" placeholder="e.g. Payment verified via bank statement."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top py-2">
                    <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">CANCEL</button>
                    <button type="submit" class="btn btn-success px-4 fw-bold" id="btn-confirm-approve">
                        <i class="fas fa-check me-2"></i>APPROVE & ADD BALANCE
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-times-circle me-2"></i>Reject Wallet Request</h5>
                <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                    <i class="<?php echo v('theme_modal_close_icon', 'fas fa-times'); ?>"></i>
                </button>
            </div>
            <form id="rejectForm" novalidate>
                <input type="hidden" name="action" value="reject_wallet">
                <input type="hidden" name="id" id="reject-id">
                <div class="modal-body px-3 py-4">
                    <div class="enquiry-view-premium p-1">
                        <div class="view-section mb-4">
                            <div class="view-section-header text-danger border-danger">REJECTION OVERVIEW</div>
                            <div class="row g-3 px-3 py-3">
                                <div class="col-md-12">
                                    <div class="alert alert-danger border-0 rounded-3 mb-0 shadow-sm">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        Rejecting <strong id="reject-req-id" class="text-dark">#REQ-0</strong> from 
                                        <strong id="reject-center" class="text-dark">Center Name</strong>.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="view-section">
                            <div class="view-section-header text-danger border-danger">REJECTION REASON</div>
                            <div class="px-3 py-3">
                                <label class="form-label fw-bold small text-muted">ADMIN NOTE (REQUIRED) <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="admin_remarks" rows="3" placeholder="e.g. Payment proof unclear / not matching." required></textarea>
                                <div class="invalid-feedback">Please provide a reason for rejection.</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top py-2">
                    <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">CANCEL</button>
                    <button type="submit" class="btn btn-danger px-4 fw-bold" id="btn-confirm-reject">
                        <i class="fas fa-times me-2"></i>REJECT REQUEST
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const HANDLER = '<?php echo BASE_URL; ?>ajax/franchise_handler.php';
const appModal = new bootstrap.Modal(document.getElementById('approveModal'));
const rejModal = new bootstrap.Modal(document.getElementById('rejectModal'));

function openApproveModal(r) {
    document.getElementById('approve-id').value = r.id;
    document.getElementById('approve-amount').textContent = '₹ ' + parseFloat(r.amount).toLocaleString('en-IN', {minimumFractionDigits:2});
    document.getElementById('approve-center').textContent = r.center_name + ' (' + r.center_code + ')';
    appModal.show();
}

function openRejectModal(r) {
    document.getElementById('reject-id').value = r.id;
    document.getElementById('reject-req-id').textContent = '#REQ-' + r.id;
    document.getElementById('reject-center').textContent = r.center_name;
    rejModal.show();
}

document.getElementById('approveForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('btn-confirm-approve');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>PROCESSING...';
    fetch(HANDLER, { method: 'POST', body: new FormData(this) })
    .then(r => r.json()).then(res => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check me-2"></i>APPROVE & ADD BALANCE';
        if(res.success) location.reload();
        else alert(res.message);
    });
});

document.getElementById('rejectForm').addEventListener('submit', function(e) {
    e.preventDefault();
    if(!this.checkValidity()){ this.classList.add('was-validated'); return; }
    const btn = document.getElementById('btn-confirm-reject');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>PROCESSING...';
    fetch(HANDLER, { method: 'POST', body: new FormData(this) })
    .then(r => r.json()).then(res => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-times me-2"></i>REJECT REQUEST';
        if(res.success) location.reload();
        else alert(res.message);
    });
});
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>
