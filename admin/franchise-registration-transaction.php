<?php
// admin/franchise-registration-transaction.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Filter logic
$where = "WHERE 1=1";
$params = [];
$filter_center = (int)($_GET['center_id'] ?? 0);
$filter_status = $_GET['status'] ?? '';
$filter_method = $_GET['method'] ?? '';
$search = trim($_GET['search'] ?? '');

if ($filter_center) {
    $where .= " AND t.franchise_id = ?";
    $params[] = $filter_center;
}
if ($filter_status) {
    $where .= " AND t.status = ?";
    $params[] = $filter_status;
}
if ($filter_method) {
    $where .= " AND t.method = ?";
    $params[] = $filter_method;
}
if ($search) {
    $where .= " AND (t.txn_id LIKE ? OR f.center_name LIKE ? OR f.center_code LIKE ?)";
    $p = "%$search%";
    $params[] = $p; $params[] = $p; $params[] = $p;
}

$stmt = $pdo->prepare("
    SELECT t.*, f.center_name, f.center_code 
    FROM franchise_registration_transactions t
    JOIN franchises f ON t.franchise_id = f.id
    $where
    ORDER BY t.created_at DESC
    LIMIT 500
");
$stmt->execute($params);
$txns = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Stats
$totalCollected = $pdo->query("SELECT IFNULL(SUM(amount), 0) FROM franchise_registration_transactions WHERE status = 'success'")->fetchColumn();
$totalPending = $pdo->query("SELECT IFNULL(SUM(amount), 0) FROM franchise_registration_transactions WHERE status = 'pending'")->fetchColumn();
$totalFailed = $pdo->query("SELECT IFNULL(SUM(amount), 0) FROM franchise_registration_transactions WHERE status = 'failed'")->fetchColumn();

// Centers for filter
$centers = $pdo->query("SELECT id, center_name, center_code FROM franchises ORDER BY center_name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>Franchise Registration Transactions</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Franchise Management</li>
            <li class="breadcrumb-item active">Registration Transactions</li>
        </ol>
    </nav>
</div>

<section class="section">
<div class="row">

<!-- ── Summary Stat Cards ─────────────────────────────────── -->
<div class="col-md-4 col-sm-6 mb-4">
    <div class="card h-100 shadow-none border-start border-success border-4">
        <div class="card-body d-flex align-items-center gap-3">
            <div class="flex-shrink-0 rounded-circle d-flex align-items-center justify-content-center bg-success bg-opacity-10"
                 style="width:52px;height:52px;">
                <i class="fas fa-rupee-sign text-success fs-5"></i>
            </div>
            <div>
                <div class="text-muted small fw-bold">TOTAL COLLECTED</div>
                <div class="fw-bold fs-5 text-success">₹ <?php echo number_format($totalCollected, 2); ?></div>
                <div class="text-muted" style="font-size:12px;">All successful onboarding payments</div>
            </div>
        </div>
    </div>
</div>

<div class="col-md-4 col-sm-6 mb-4">
    <div class="card h-100 shadow-none border-start border-warning border-4">
        <div class="card-body d-flex align-items-center gap-3">
            <div class="flex-shrink-0 rounded-circle d-flex align-items-center justify-content-center bg-warning bg-opacity-10"
                 style="width:52px;height:52px;">
                <i class="fas fa-clock text-warning fs-5"></i>
            </div>
            <div>
                <div class="text-muted small fw-bold">PENDING</div>
                <div class="fw-bold fs-5 text-warning">₹ <?php echo number_format($totalPending, 2); ?></div>
                <div class="text-muted" style="font-size:12px;">Awaiting confirmation</div>
            </div>
        </div>
    </div>
</div>

<div class="col-md-4 col-sm-6 mb-4">
    <div class="card h-100 shadow-none border-start border-danger border-4">
        <div class="card-body d-flex align-items-center gap-3">
            <div class="flex-shrink-0 rounded-circle d-flex align-items-center justify-content-center bg-danger bg-opacity-10"
                 style="width:52px;height:52px;">
                <i class="fas fa-times-circle text-danger fs-5"></i>
            </div>
            <div>
                <div class="text-muted small fw-bold">FAILED</div>
                <div class="fw-bold fs-5 text-danger">₹ <?php echo number_format($totalFailed, 2); ?></div>
                <div class="text-muted" style="font-size:12px;">Failed / cancelled payments</div>
            </div>
        </div>
    </div>
</div>

<!-- ── Full Width Table ──────────────────────────────────── -->
<div class="col-12">

<!-- Alert -->
<div id="reg-txn-alert" class="d-none mb-3"></div>

<!-- Filter Bar -->
<div class="filter-bar">
<div class="card mb-3">
    <div class="card-body py-3">
        <form class="row g-2 align-items-end" method="GET">
            <div class="col-md-3">
                <select name="center_id" class="form-select form-select-sm">
                    <option value="">All Franchises</option>
                    <?php foreach($centers as $c): ?>
                        <option value="<?php echo $c['id']; ?>" <?php echo $filter_center == $c['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($c['center_name']); ?> (<?php echo $c['center_code']; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    <option value="success" <?php echo $filter_status == 'success' ? 'selected' : ''; ?>>Success</option>
                    <option value="pending" <?php echo $filter_status == 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="failed" <?php echo $filter_status == 'failed' ? 'selected' : ''; ?>>Failed</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="method" class="form-select form-select-sm">
                    <option value="">All Methods</option>
                    <option value="razorpay" <?php echo $filter_method == 'razorpay' ? 'selected' : ''; ?>>Razorpay</option>
                    <option value="upi" <?php echo $filter_method == 'upi' ? 'selected' : ''; ?>>UPI</option>
                    <option value="bank" <?php echo $filter_method == 'bank' ? 'selected' : ''; ?>>Bank Transfer</option>
                    <option value="offline" <?php echo $filter_method == 'offline' ? 'selected' : ''; ?>>Offline</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="date_from" class="form-control form-control-sm" title="From Date" value="<?php echo $_GET['date_from'] ?? ''; ?>">
            </div>
            <div class="col-md-3">
                <div class="input-group input-group-sm">
                    <input type="text" name="search" class="form-control" placeholder="TXN ID / Franchise..." value="<?php echo htmlspecialchars($search); ?>">
                    <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                    <a href="franchise-registration-transaction.php" class="btn btn-outline-secondary">Clear</a>
                </div>
            </div>
        </form>
    </div>
</div>
</div><!-- /.filter-bar -->

<!-- Table -->
<div class="card">
    <div class="card-body pt-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle datatable-premium">
                <thead class="table-light">
                    <tr>
                        <th>S.No.</th>
                        <th>TXN ID</th>
                        <th>Franchise Name</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th data-no-sort>Method</th>
                        <th data-no-sort>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($txns)): $sn=1; foreach($txns as $t): ?>
                    <tr>
                        <td><?php echo $sn++; ?></td>
                        <td><span class="text-primary fw-bold"><?php echo htmlspecialchars($t['txn_id']); ?></span></td>
                        <td>
                            <strong><?php echo htmlspecialchars($t['center_name']); ?></strong><br>
                            <small class="text-muted"><?php echo htmlspecialchars($t['center_code']); ?></small>
                        </td>
                        <td><small><?php echo date('d M Y', strtotime($t['created_at'])); ?></small></td>
                        <td><span class="fw-bold text-dark">₹ <?php echo number_format($t['amount'], 2); ?></span></td>
                        <td><small><i class="fas fa-university me-1 text-muted"></i><?php echo ucfirst($t['method']); ?></small></td>
                        <td>
                            <span class="badge rounded-pill bg-<?php echo $t['status']=='success'?'success':($t['status']=='pending'?'warning':'danger'); ?>">
                                <?php echo ucfirst($t['status']); ?>
                            </span>
                        </td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-info" title="View Details"
                                    onclick='viewTxn(<?php echo json_encode($t); ?>)'>
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

</div><!-- /.col-12 -->
</div><!-- /.row -->
</section>

<!-- ══ View Transaction Modal ════════════════════════════════════════════════ -->
<div class="modal fade" id="viewTxnModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-receipt me-2"></i>Transaction Details</h5>
                <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                    <i class="<?php echo v('theme_modal_close_icon', 'fas fa-times'); ?>"></i>
                </button>
            </div>
            <div class="modal-body px-3 py-2" id="v-body" style="max-height: 80vh; overflow-y: auto; overflow-x: hidden;">
                <div class="enquiry-view-premium">
                    <div class="view-section mb-4">
                        <div class="view-section-header">1. TRANSACTION DETAILS</div>
                        <div class="row g-3 px-3">
                            <div class="col-md-6"><label class="view-label">TXN ID</label><div class="view-value fw-bold text-primary" id="v-txn-id"></div></div>
                            <div class="col-md-6"><label class="view-label">Date</label><div class="view-value" id="v-date"></div></div>
                            <div class="col-md-6"><label class="view-label">Amount Paid</label><div class="view-value fw-bold text-success" id="v-amount"></div></div>
                            <div class="col-md-6"><label class="view-label">Payment Method</label><div class="view-value fw-bold text-dark" id="v-method"></div></div>
                            <div class="col-md-6"><label class="view-label">Payment Status</label><div class="view-value" id="v-status"></div></div>
                        </div>
                    </div>
                    <div class="view-section mb-4">
                        <div class="view-section-header">2. FRANCHISE / CENTER INFO</div>
                        <div class="row g-3 px-3">
                            <div class="col-md-8"><label class="view-label">Center Name</label><div class="view-value fw-bold text-dark" id="v-center"></div></div>
                            <div class="col-md-4"><label class="view-label">Center Code</label><div class="view-value fw-bold text-muted" id="v-code"></div></div>
                        </div>
                    </div>
                    <div class="view-section">
                        <div class="view-section-header">3. ADDITIONAL REMARKS</div>
                        <div class="px-3">
                            <div class="p-3 bg-light rounded border-start border-4 border-primary shadow-sm" id="v-remarks">
                                No additional remarks available.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-outline-primary">
                    <i class="fas fa-print me-1"></i>Print Receipt
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const txnModal = new bootstrap.Modal(document.getElementById('viewTxnModal'));

function viewTxn(t) {
    document.getElementById('v-txn-id').textContent = t.txn_id;
    document.getElementById('v-center').textContent = t.center_name;
    document.getElementById('v-code').textContent = t.center_code;
    document.getElementById('v-date').textContent = new Date(t.created_at).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
    document.getElementById('v-amount').textContent = '₹ ' + parseFloat(t.amount).toLocaleString('en-IN', { minimumFractionDigits: 2 });
    document.getElementById('v-method').textContent = t.method.toUpperCase();
    document.getElementById('v-status').innerHTML = `<span class="badge rounded-pill bg-${t.status=='success'?'success':(t.status=='pending'?'warning':'danger')}">${t.status.toUpperCase()}</span>`;
    document.getElementById('v-remarks').textContent = t.remarks || 'No remarks available.';
    txnModal.show();
}
</script>
<?php include(__DIR__ . "/includes/footer.php"); ?>
