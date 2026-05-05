<?php
// admin/franchise-wallet-ledger.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Filter logic
$where = "WHERE 1=1";
$params = [];
$filter_center = (int)($_GET['center'] ?? 0);
$filter_type = $_GET['type'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';

if ($filter_center) { $where .= " AND l.franchise_id = ?"; $params[] = $filter_center; }
if ($filter_type) { $where .= " AND l.type = ?"; $params[] = $filter_type; }
if ($date_from) { $where .= " AND DATE(l.created_at) >= ?"; $params[] = $date_from; }
if ($date_to) { $where .= " AND DATE(l.created_at) <= ?"; $params[] = $date_to; }

$stmt = $pdo->prepare("
    SELECT l.*, f.center_name, f.center_code 
    FROM franchise_wallet_ledger l
    JOIN franchises f ON l.franchise_id = f.id
    $where
    ORDER BY l.created_at DESC
    LIMIT 1000
");
$stmt->execute($params);
$ledger = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Stats
$totalFranchiseBalance = $pdo->query("SELECT IFNULL(SUM(wallet_balance), 0) FROM franchises")->fetchColumn();
$totalCredits = $pdo->query("SELECT IFNULL(SUM(amount), 0) FROM franchise_wallet_ledger WHERE type = 'credit'")->fetchColumn();
$totalDebits = $pdo->query("SELECT IFNULL(SUM(amount), 0) FROM franchise_wallet_ledger WHERE type = 'debit'")->fetchColumn();

// Centers for dropdown
$centers = $pdo->query("SELECT id, center_name, center_code FROM franchises ORDER BY center_name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>Wallet Balance Ledger</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Franchise Management</li>
            <li class="breadcrumb-item active">Wallet Ledger</li>
        </ol>
    </nav>
</div>

<section class="section">
    <!-- Summary Stat Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-none border-start border-primary border-4 mb-0 h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small fw-bold">TOTAL FRANCHISE BALANCE</div>
                            <div class="h4 mb-0 fw-bold text-primary">₹ <?php echo number_format($totalFranchiseBalance, 2); ?></div>
                        </div>
                        <div class="bg-primary-light p-2 rounded text-primary"><i class="fas fa-coins fa-lg"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-none border-start border-success border-4 mb-0 h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small fw-bold">TOTAL CREDITS</div>
                            <div class="h4 mb-0 fw-bold text-success">₹ <?php echo number_format($totalCredits, 2); ?></div>
                        </div>
                        <div class="bg-success-light p-2 rounded text-success"><i class="fas fa-arrow-up fa-lg"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-none border-start border-danger border-4 mb-0 h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small fw-bold">TOTAL DEBITS</div>
                            <div class="h4 mb-0 fw-bold text-danger">₹ <?php echo number_format($totalDebits, 2); ?></div>
                        </div>
                        <div class="bg-danger-light p-2 rounded text-danger"><i class="fas fa-arrow-down fa-lg"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card mb-4">
        <div class="card-body pt-3 pb-2">
            <form class="row g-2 align-items-end" method="GET">
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted">SELECT CENTER</label>
                    <select name="center" class="form-select">
                        <option value="">All Centers...</option>
                        <?php foreach($centers as $c): ?>
                            <option value="<?php echo $c['id']; ?>" <?php echo $filter_center == $c['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($c['center_name']); ?> (<?php echo $c['center_code']; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold small text-muted">TYPE</label>
                    <select name="type" class="form-select">
                        <option value="">All Types</option>
                        <option value="credit" <?php echo $filter_type == 'credit' ? 'selected' : ''; ?>>Credit (+)</option>
                        <option value="debit" <?php echo $filter_type == 'debit' ? 'selected' : ''; ?>>Debit (-)</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold small text-muted">FROM DATE</label>
                    <input type="date" name="date_from" class="form-control" value="<?php echo $date_from; ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold small text-muted">TO DATE</label>
                    <input type="date" name="date_to" class="form-control" value="<?php echo $date_to; ?>">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button class="btn btn-primary flex-grow-1 fw-bold" type="submit">APPLY FILTERS</button>
                    <a href="franchise-wallet-ledger.php" class="btn btn-outline-secondary fw-bold">RESET</a>
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
                            <th>Center (Franchise)</th>
                            <th>Date &amp; Time</th>
                            <th>Amount</th>
                            <th data-no-sort>Type</th>
                            <th data-no-sort>Remarks / Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $sn=1; foreach($ledger as $l): ?>
                        <tr>
                            <td><?php echo $sn++; ?></td>
                            <td>
                                <div class="fw-bold text-dark"><?php echo htmlspecialchars($l['center_name']); ?></div>
                                <div class="small text-muted">Code: <?php echo htmlspecialchars($l['center_code']); ?></div>
                            </td>
                            <td><div class="small fw-bold text-dark"><?php echo date('d M Y, h:i A', strtotime($l['created_at'])); ?></div></td>
                            <td>
                                <div class="fw-bold fs-6 text-<?php echo $l['type']=='credit'?'success':'danger'; ?>">
                                    <?php echo $l['type']=='credit'?'+':'-'; ?> ₹ <?php echo number_format($l['amount'], 2); ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge rounded-pill bg-<?php echo $l['type']=='credit'?'success':'danger'; ?> px-3">
                                    <?php echo strtoupper($l['type']); ?>
                                </span>
                            </td>
                            <td><small class="text-muted text-truncate d-inline-block" style="max-width:300px;"><?php echo htmlspecialchars($l['description']); ?></small></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<?php include(__DIR__ . "/includes/footer.php"); ?>
