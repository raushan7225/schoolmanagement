<?php
// admin/student-registration-transaction.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

$where = "WHERE 1=1";
$params = [];

$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';
$filter_status = $_GET['status'] ?? '';
$filter_method = $_GET['method'] ?? '';
$search = trim($_GET['search'] ?? '');

if ($date_from) {
    $where .= " AND DATE(t.created_at) >= ?";
    $params[] = $date_from;
}
if ($date_to) {
    $where .= " AND DATE(t.created_at) <= ?";
    $params[] = $date_to;
}
if ($filter_status) {
    $where .= " AND t.payment_status = ?";
    $params[] = $filter_status;
}
if ($filter_method) {
    $where .= " AND t.payment_method = ?";
    $params[] = $filter_method;
}
if ($search) {
    $where .= " AND (t.txn_id LIKE ? OR a.full_name LIKE ? OR a.roll_number LIKE ?)";
    $p = "%$search%";
    $params[] = $p; $params[] = $p; $params[] = $p;
}

$stmt = $pdo->prepare("
    SELECT t.*, a.full_name, a.roll_number, f.center_name, f.center_code
    FROM student_registration_transactions t
    JOIN admissions a ON t.admission_id = a.id
    JOIN franchises f ON t.center_id = f.id
    $where
    ORDER BY t.created_at DESC
");
$stmt->execute($params);
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Stats
$totalCollected = $pdo->query("SELECT IFNULL(SUM(amount), 0) FROM student_registration_transactions WHERE payment_status = 'success'")->fetchColumn();
$totalPending = $pdo->query("SELECT IFNULL(SUM(amount), 0) FROM student_registration_transactions WHERE payment_status = 'pending'")->fetchColumn();
$totalFailed = $pdo->query("SELECT IFNULL(SUM(amount), 0) FROM student_registration_transactions WHERE payment_status = 'failed'")->fetchColumn();
?>

<div class="pagetitle">
    <h1>Registration Transactions</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Student Management</li>
            <li class="breadcrumb-item active">Transactions</li>
        </ol>
    </nav>
</div>

<section class="section">
    <!-- Summary Stat Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-none border-start border-success border-4 mb-0">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="bg-success-light p-2 rounded text-success"><i class="fas fa-rupee-sign fa-lg"></i></div>
                    <div>
                        <div class="text-muted small fw-bold">TOTAL COLLECTED</div>
                        <div class="h5 mb-0 fw-bold text-success">₹ <?php echo number_format($totalCollected, 2); ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-none border-start border-warning border-4 mb-0">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="bg-warning-light p-2 rounded text-warning"><i class="fas fa-clock fa-lg"></i></div>
                    <div>
                        <div class="text-muted small fw-bold">PENDING</div>
                        <div class="h5 mb-0 fw-bold text-warning">₹ <?php echo number_format($totalPending, 2); ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-none border-start border-danger border-4 mb-0">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="bg-danger-light p-2 rounded text-danger"><i class="fas fa-times-circle fa-lg"></i></div>
                    <div>
                        <div class="text-muted small fw-bold">FAILED</div>
                        <div class="h5 mb-0 fw-bold text-danger">₹ <?php echo number_format($totalFailed, 2); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body pt-3 pb-2">
            <form class="row g-2" method="GET">
                <div class="col-md-2">
                    <label class="form-label fw-bold small">From Date</label>
                    <input type="date" name="date_from" class="form-control" value="<?php echo $date_from; ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold small">To Date</label>
                    <input type="date" name="date_to" class="form-control" value="<?php echo $date_to; ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold small">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="success" <?php echo $filter_status == 'success' ? 'selected' : ''; ?>>Success</option>
                        <option value="pending" <?php echo $filter_status == 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="failed" <?php echo $filter_status == 'failed' ? 'selected' : ''; ?>>Failed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold small">Method</label>
                    <select name="method" class="form-select">
                        <option value="">All Methods</option>
                        <option value="razorpay" <?php echo $filter_method == 'razorpay' ? 'selected' : ''; ?>>Razorpay</option>
                        <option value="upi" <?php echo $filter_method == 'upi' ? 'selected' : ''; ?>>UPI</option>
                        <option value="offline" <?php echo $filter_method == 'offline' ? 'selected' : ''; ?>>Offline</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter me-2"></i>Apply</button>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <a href="student-registration-transaction.php" class="btn btn-outline-secondary w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Transaction Table -->
    <div class="card">
        <div class="card-body pt-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle datatable-premium">
                    <thead class="table-light">
                        <tr>
                            <th width="60">S.No.</th>
                            <th>TXN ID</th>
                            <th>Date</th>
                            <th>Student / Reg No</th>
                            <th>Franchise</th>
                            <th>Amount</th>
                            <th data-no-sort>Method</th>
                            <th data-no-sort>Status</th>
                            <th class="text-end" data-no-sort>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $sn=1; foreach($transactions as $t): ?>
                        <tr>
                            <td><?php echo $sn++; ?></td>
                            <td><code class="text-primary fw-bold">#<?php echo $t['txn_id']; ?></code></td>
                            <td><div class="small fw-bold text-dark"><?php echo date('d M Y', strtotime($t['created_at'])); ?></div></td>
                            <td>
                                <div class="fw-bold"><?php echo strtoupper(htmlspecialchars($t['full_name'])); ?></div>
                                <small class="text-muted">REG: NEB/REG/<?php echo str_pad($t['admission_id'], 6, '0', STR_PAD_LEFT); ?></small>
                            </td>
                            <td>
                                <div class="fw-bold small text-dark"><?php echo htmlspecialchars($t['center_name']); ?></div>
                                <code class="text-muted small"><?php echo $t['center_code']; ?></code>
                            </td>
                            <td><span class="fw-bold text-<?php echo $t['payment_status']=='success'?'success':'danger'; ?>">₹ <?php echo number_format($t['amount'], 2); ?></span></td>
                            <td><small><i class="fas fa-credit-card me-1 text-muted"></i><?php echo ucfirst($t['payment_method']); ?></small></td>
                            <td>
                                <span class="badge rounded-pill bg-<?php echo ($t['payment_status']=='success'?'success':($t['payment_status']=='pending'?'warning':'danger')); ?>-light text-<?php echo ($t['payment_status']=='success'?'success':($t['payment_status']=='pending'?'warning':'danger')); ?> border border-<?php echo ($t['payment_status']=='success'?'success':($t['payment_status']=='pending'?'warning':'danger')); ?> px-3">
                                    <?php echo strtoupper($t['payment_status']); ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-info btn-icon" title="View Details"
                                        onclick="viewTxn(<?php echo htmlspecialchars(json_encode($t)); ?>)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- View Transaction Modal -->
<div class="modal fade" id="viewTxnModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-receipt me-2"></i>Transaction Details</h5>
                <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body p-0" id="txnViewBody">
                <div class="text-center py-5"><div class="spinner-border text-primary"></div></div>
            </div>
        </div>
    </div>
</div>

<script>
function viewTxn(t) {
    const statusBadge = t.payment_status=='success'?'success':(t.payment_status=='pending'?'warning':'danger');
    const body = `
        <div class="enquiry-view-premium p-1">
            <div class="view-section mb-0">
                <div class="view-section-header">TRANSACTION INFORMATION</div>
                <div class="row g-3 px-3 py-3">
                    <div class="col-md-6"><label class="view-label">TXN ID</label><div class="view-value fw-bold text-primary">#${t.txn_id}</div></div>
                    <div class="col-md-6"><label class="view-label">Payment Date</label><div class="view-value">${new Date(t.created_at).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })}</div></div>
                    <div class="col-md-6"><label class="view-label">Amount Paid</label><div class="view-value fw-bold text-success fs-5">₹ ${parseFloat(t.amount).toFixed(2)}</div></div>
                    <div class="col-md-3"><label class="view-label">Method</label><div class="view-value text-uppercase">${t.payment_method}</div></div>
                    <div class="col-md-3"><label class="view-label">Status</label><div class="view-value"><span class="badge bg-${statusBadge} rounded-pill px-3">${t.payment_status.toUpperCase()}</span></div></div>
                </div>
            </div>
            
            <div class="view-section mb-0 border-top">
                <div class="view-section-header">STUDENT & FRANCHISE</div>
                <div class="row g-3 px-3 py-3">
                    <div class="col-md-8"><label class="view-label">Student Name</label><div class="view-value fw-bold text-dark">${t.full_name.toUpperCase()}</div></div>
                    <div class="col-md-4"><label class="view-label">Reg. Number</label><div class="view-value text-muted small">NEB/REG/${t.admission_id.toString().padStart(6, '0')}</div></div>
                    <div class="col-md-12"><label class="view-label">Center / Franchise</label><div class="view-value text-primary fw-bold">${t.center_name} (${t.center_code})</div></div>
                </div>
            </div>

            <div class="view-section mb-0 border-top">
                <div class="view-section-header">REMARKS</div>
                <div class="px-3 py-3">
                    <div class="bg-light p-3 rounded border-start border-4 border-primary small text-muted">
                        ${t.description || 'No remarks provided.'}
                    </div>
                </div>
            </div>
        </div>
    `;
    document.getElementById('txnViewBody').innerHTML = body;
    new bootstrap.Modal(document.getElementById('viewTxnModal')).show();
}
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>
