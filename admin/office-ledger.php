<?php
// admin/office-ledger.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Filter logic
$date_from = $_GET['date_from'] ?? date('Y-m-01'); // Default to start of month
$date_to = $_GET['date_to'] ?? date('Y-m-d');
$filter_type = $_GET['type'] ?? '';
$filter_head = (int)($_GET['head_id'] ?? 0);

$where = "WHERE ot.date BETWEEN ? AND ?";
$params = [$date_from, $date_to];

if ($filter_type) {
    $where .= " AND ot.type = ?";
    $params[] = $filter_type;
}
if ($filter_head) {
    $where .= " AND ot.voucher_head_id = ?";
    $params[] = $filter_head;
}

// Fetch transactions
$stmt = $pdo->prepare("
    SELECT ot.*, vh.name as head_name
    FROM office_transactions ot
    JOIN voucher_heads vh ON ot.voucher_head_id = vh.id
    $where
    ORDER BY ot.date DESC, ot.created_at DESC
");
$stmt->execute($params);
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Summary Stats for the filtered range
$stats_stmt = $pdo->prepare("
    SELECT 
        SUM(CASE WHEN type = 'deposit' THEN amount ELSE 0 END) as total_income,
        SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as total_expense
    FROM office_transactions ot
    $where
");
$stats_stmt->execute($params);
$summary = $stats_stmt->fetch(PDO::FETCH_ASSOC);

$total_income = $summary['total_income'] ?? 0;
$total_expense = $summary['total_expense'] ?? 0;
$net_balance = $total_income - $total_expense;

// Fetch Heads for filter
$heads = $pdo->query("SELECT id, name, type FROM voucher_heads WHERE status = 1 ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>Office Accounting Ledger</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Office Accounting</li>
            <li class="breadcrumb-item active">Ledger</li>
        </ol>
    </nav>
</div>

<section class="section">
    <!-- ══ Summary Cards ══════════════════════════════════════════ -->
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-none border-start border-success border-4">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="flex-shrink-0 rounded-circle d-flex align-items-center justify-content-center bg-success bg-opacity-10" style="width:52px;height:52px;">
                        <i class="fas fa-arrow-down text-success fs-5"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold">TOTAL INCOME</div>
                        <div class="fw-bold fs-5 text-success">₹ <?php echo number_format($total_income, 2); ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-none border-start border-danger border-4">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="flex-shrink-0 rounded-circle d-flex align-items-center justify-content-center bg-danger bg-opacity-10" style="width:52px;height:52px;">
                        <i class="fas fa-arrow-up text-danger fs-5"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold">TOTAL EXPENSE</div>
                        <div class="fw-bold fs-5 text-danger">₹ <?php echo number_format($total_expense, 2); ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-none border-start border-primary border-4">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="flex-shrink-0 rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10" style="width:52px;height:52px;">
                        <i class="fas fa-wallet text-primary fs-5"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold">NET BALANCE</div>
                        <div class="fw-bold fs-5 <?php echo $net_balance >= 0 ? 'text-primary' : 'text-danger'; ?>">
                            ₹ <?php echo number_format($net_balance, 2); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- ══ Filter Bar ══════════════════════════════════════════ -->
        <div class="col-lg-12">
            <div class="card mb-3">
                <div class="card-body py-3">
                    <form class="row g-2 align-items-end" method="GET">
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">From Date</label>
                            <input type="date" name="date_from" class="form-control form-control-sm" value="<?php echo $date_from; ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">To Date</label>
                            <input type="date" name="date_to" class="form-control form-control-sm" value="<?php echo $date_to; ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Type</label>
                            <select name="type" class="form-select form-select-sm">
                                <option value="">All Types</option>
                                <option value="deposit" <?php echo $filter_type == 'deposit' ? 'selected' : ''; ?>>Income / Deposit</option>
                                <option value="expense" <?php echo $filter_type == 'expense' ? 'selected' : ''; ?>>Expense</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Voucher Head</label>
                            <select name="head_id" class="form-select form-select-sm">
                                <option value="">All Heads</option>
                                <?php foreach($heads as $h): ?>
                                    <option value="<?php echo $h['id']; ?>" <?php echo $filter_head == $h['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($h['name']); ?> (<?php echo ucfirst($h['type']); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold">
                                <i class="fas fa-filter me-1"></i>Apply Filter
                            </button>
                        </div>
                        <div class="col-md-1">
                            <a href="office-ledger.php" class="btn btn-outline-secondary btn-sm w-100">Reset</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ══ Ledger Table ════════════════════════════════════════ -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-book text-white me-2"></i>
                        <h5 class="card-title text-white mb-0">Unified Statement</h5>
                    </div>
                    <button class="btn btn-sm btn-light fw-bold" onclick="window.print()">
                        <i class="fas fa-print me-1"></i>Print Report
                    </button>
                </div>
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle datatable-premium">
                            <thead class="table-light">
                                <tr>
                                    <th width="60">S.No.</th>
                                    <th>Date</th>
                                    <th>Voucher ID</th>
                                    <th>Head / Description</th>
                                    <th>Mode</th>
                                    <th class="text-end">Income</th>
                                    <th class="text-end">Expense</th>
                                    <th class="text-end" data-no-sort>Print</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($transactions)): $sn=1; foreach($transactions as $t): ?>
                                <tr>
                                    <td><?php echo $sn++; ?></td>
                                    <td><small class="fw-bold"><?php echo date('d M Y', strtotime($t['date'])); ?></small></td>
                                    <td>
                                        <span class="badge bg-light text-dark border rounded-pill px-2">
                                            V-<?php echo strtoupper(substr($t['type'], 0, 3)); ?>-<?php echo 1000 + $t['id']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold <?php echo $t['type'] == 'deposit' ? 'text-success' : 'text-danger'; ?>">
                                            <?php echo htmlspecialchars($t['head_name']); ?>
                                        </div>
                                        <small class="text-muted"><?php echo htmlspecialchars($t['description']); ?></small>
                                    </td>
                                    <td><small class="badge border bg-white text-dark"><?php echo $t['payment_mode']; ?></small></td>
                                    <td class="text-end fw-bold text-success">
                                        <?php echo $t['type'] == 'deposit' ? '₹ ' . number_format($t['amount'], 2) : '-'; ?>
                                    </td>
                                    <td class="text-end fw-bold text-danger">
                                        <?php echo $t['type'] == 'expense' ? '₹ ' . number_format($t['amount'], 2) : '-'; ?>
                                    </td>
                                    <td class="text-end">
                                        <a href="print-voucher.php?id=<?php echo $t['id']; ?>" target="_blank" class="btn btn-sm btn-outline-info btn-icon" title="Print Voucher">
                                            <i class="fas fa-print"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr class="fw-bold">
                                    <td colspan="5" class="text-end">TOTALS:</td>
                                    <td class="text-end text-success">₹ <?php echo number_format($total_income, 2); ?></td>
                                    <td class="text-end text-danger">₹ <?php echo number_format($total_expense, 2); ?></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include(__DIR__ . "/includes/footer.php"); ?>
