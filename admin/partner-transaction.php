<?php
// admin/partner-transaction.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Search / Filter logic
$where = "WHERE 1=1";
$params = [];

$filter_partner = (int)($_GET['partner_id'] ?? 0);
$filter_type = $_GET['type'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';

if ($filter_partner) { $where .= " AND l.partner_id = ?"; $params[] = $filter_partner; }
if ($filter_type) { $where .= " AND l.type = ?"; $params[] = $filter_type; }
if ($date_from) { $where .= " AND l.created_at >= ?"; $params[] = $date_from . " 00:00:00"; }
if ($date_to) { $where .= " AND l.created_at <= ?"; $params[] = $date_to . " 23:59:59"; }

// Fetch Transactions
$stmt = $pdo->prepare("
    SELECT l.*, p.full_name as partner_name
    FROM partner_wallet_ledger l
    JOIN partners p ON l.partner_id = p.id
    $where
    ORDER BY l.created_at DESC
");
$stmt->execute($params);
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Stats
$total_balance = $pdo->query("SELECT SUM(wallet_balance) FROM partners WHERE status=1")->fetchColumn() ?: 0;
$total_credits = $pdo->query("SELECT SUM(amount) FROM partner_wallet_ledger WHERE type='credit'")->fetchColumn() ?: 0;
$total_debits = $pdo->query("SELECT SUM(amount) FROM partner_wallet_ledger WHERE type='debit'")->fetchColumn() ?: 0;

// Fetch partners for dropdown
$partners_dropdown = $pdo->query("SELECT id, full_name FROM partners WHERE status != -1 ORDER BY full_name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>Partner Transactions</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Partner Managements</li>
            <li class="breadcrumb-item active">Transactions</li>
        </ol>
    </nav>
</div>

<section class="section">
    <!-- Summary Stat Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-none border-start border-success border-4 mb-0">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small fw-bold">TOTAL PARTNER BALANCE</div>
                            <div class="h4 mb-0 fw-bold text-success">₹ <?php echo number_format($total_balance, 2); ?></div>
                        </div>
                        <div class="bg-success-light p-2 rounded text-success"><i class="fas fa-wallet fa-lg"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-none border-start border-primary border-4 mb-0">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small fw-bold">TOTAL CREDITS</div>
                            <div class="h4 mb-0 fw-bold text-primary">₹ <?php echo number_format($total_credits, 2); ?></div>
                        </div>
                        <div class="bg-primary-light p-2 rounded text-primary"><i class="fas fa-arrow-up fa-lg"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-none border-start border-danger border-4 mb-0">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small fw-bold">TOTAL DEBITS</div>
                            <div class="h4 mb-0 fw-bold text-danger">₹ <?php echo number_format($total_debits, 2); ?></div>
                        </div>
                        <div class="bg-danger-light p-2 rounded text-danger"><i class="fas fa-arrow-down fa-lg"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body pt-3 pb-2">
            <form class="row g-2 align-items-end" method="GET">
                <div class="col-md-3">
                    <label class="form-label fw-bold small">SELECT PARTNER</label>
                    <select name="partner_id" class="form-select">
                        <option value="">All Partners...</option>
                        <?php foreach($partners_dropdown as $p): ?>
                            <option value="<?php echo $p['id']; ?>" <?php echo ($filter_partner == $p['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($p['full_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold small">TXN TYPE</label>
                    <select name="type" class="form-select">
                        <option value="">All Types</option>
                        <option value="credit" <?php echo ($filter_type == 'credit') ? 'selected' : ''; ?>>Credit</option>
                        <option value="debit" <?php echo ($filter_type == 'debit') ? 'selected' : ''; ?>>Debit</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold small">FROM DATE</label>
                    <input type="date" name="date_from" class="form-control" value="<?php echo htmlspecialchars($date_from); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold small">TO DATE</label>
                    <input type="date" name="date_to" class="form-control" value="<?php echo htmlspecialchars($date_to); ?>">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button class="btn btn-primary flex-grow-1 fw-bold" type="submit"><i class="fas fa-filter me-2"></i>APPLY</button>
                    <a href="partner-transaction.php" class="btn btn-outline-secondary fw-bold">RESET</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-body pt-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle datatable-premium">
                    <thead class="table-light">
                        <tr>
                            <th width="60">S.No.</th>
                            <th>Partner Name</th>
                            <th>TXN ID</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th data-no-sort>Type</th>
                            <th data-no-sort>Remarks</th>
                            <th class="text-end" data-no-sort>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $sn=1; foreach($transactions as $t): ?>
                        <tr>
                            <td><?php echo $sn++; ?></td>
                            <td><div class="fw-bold text-dark"><?php echo strtoupper($t['partner_name']); ?></div></td>
                            <td><code class="text-primary fw-bold">#PTRX-<?php echo str_pad($t['id'], 5, '0', STR_PAD_LEFT); ?></code></td>
                            <td><div class="small fw-bold text-dark"><?php echo date('d M Y', strtotime($t['created_at'])); ?></div></td>
                            <td><span class="fw-bold fs-6 text-<?php echo ($t['type'] == 'credit') ? 'success' : 'danger'; ?>">
                                ₹ <?php echo number_format($t['amount'], 2); ?>
                            </span></td>
                            <td>
                                <span class="badge bg-<?php echo ($t['type'] == 'credit') ? 'success' : 'danger'; ?> rounded-pill px-3">
                                    <?php echo strtoupper($t['type']); ?>
                                </span>
                            </td>
                            <td><small class="text-muted text-truncate d-inline-block" style="max-width:200px;"><?php echo htmlspecialchars($t['description']); ?></small></td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-info btn-icon" title="View Details"
                                        onclick="viewPartnerTxn(<?php echo $t['id']; ?>)">
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
    <div class="modal-dialog modal-dialog-centered">
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
function viewPartnerTxn(id) {
    document.getElementById('txnViewBody').innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>';
    new bootstrap.Modal(document.getElementById('viewTxnModal')).show();
    
    const fd = new FormData();
    fd.append('action', 'get_txn');
    fd.append('id', id);
    
    fetch('<?php echo BASE_URL; ?>ajax/partner_handler.php', { method: 'POST', body: fd })
    .then(r => r.json()).then(res => {
        if(res.success) {
            const t = res.data;
            const amtClass = t.type === 'credit' ? 'text-success' : 'text-danger';
            const badgeClass = t.type === 'credit' ? 'bg-success' : 'bg-danger';
            
            document.getElementById('txnViewBody').innerHTML = `
                <div class="enquiry-view-premium p-1">
                    <div class="view-section mb-0">
                        <div class="view-section-header">1. TRANSACTION DETAILS</div>
                        <div class="row g-3 px-3 py-3">
                            <div class="col-md-6"><label class="view-label">TXN ID</label><div class="view-value fw-bold text-primary">#PTRX-${id.toString().padStart(5, '0')}</div></div>
                            <div class="col-md-6"><label class="view-label">Date & Time</label><div class="view-value small text-dark">${t.created_at}</div></div>
                            <div class="col-md-6"><label class="view-label">Amount</label><div class="view-value fw-bold fs-4 ${amtClass}">₹ ${parseFloat(t.amount).toLocaleString('en-IN', {minimumFractionDigits: 2})}</div></div>
                            <div class="col-md-6"><label class="view-label">Type</label><div class="view-value"><span class="badge ${badgeClass} rounded-pill px-3">${t.type.toUpperCase()}</span></div></div>
                        </div>
                    </div>
                    <div class="view-section mb-0 border-top">
                        <div class="view-section-header">2. ASSOCIATED PARTNER</div>
                        <div class="row g-3 px-3 py-3">
                            <div class="col-md-12"><label class="view-label">Partner Name</label><div class="view-value fw-bold text-dark fs-5">${t.partner_name.toUpperCase()}</div></div>
                        </div>
                    </div>
                    <div class="view-section mb-0 border-top">
                        <div class="view-section-header">3. REMARKS / DESCRIPTION</div>
                        <div class="px-3 py-3">
                            <div class="p-3 bg-light rounded border-start border-4 border-primary shadow-sm text-muted">
                                ${t.description || 'No additional remarks available.'}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        } else {
            document.getElementById('txnViewBody').innerHTML = `<p class="text-danger p-3">${res.message}</p>`;
        }
    });
}
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>
