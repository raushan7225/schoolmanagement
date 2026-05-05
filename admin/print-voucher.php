<?php
// admin/print-voucher.php
require_once('../common/config.php');

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("
    SELECT ot.*, vh.name as head_name
    FROM office_transactions ot
    JOIN voucher_heads vh ON ot.voucher_head_id = vh.id
    WHERE ot.id = ?
");
$stmt->execute([$id]);
$v = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$v) {
    die("Voucher not found.");
}

// Fetch School Name for header (from general settings)
$school_name = "ICSTIR (NEB)"; // Fallback
$school_logo = BASE_URL . "media/general/favicon.png";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Voucher - <?php echo 1000 + $v['id']; ?></title>
    <link href="<?php echo BASE_URL; ?>assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f0f2f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .voucher-container { background: #fff; max-width: 800px; margin: 50px auto; padding: 40px; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); border-top: 5px solid <?php echo $v['type']=='deposit' ? '#198754' : '#dc3545'; ?>; position: relative; overflow: hidden; }
        .voucher-header { border-bottom: 2px solid #eee; padding-bottom: 20px; margin-bottom: 30px; }
        .voucher-title { text-transform: uppercase; letter-spacing: 2px; font-weight: 800; color: <?php echo $v['type']=='deposit' ? '#198754' : '#dc3545'; ?>; }
        .voucher-id { font-size: 14px; color: #666; }
        .amount-box { background: #f8f9fa; border: 1px dashed #ccc; padding: 15px; border-radius: 5px; font-size: 24px; font-weight: 800; color: #333; }
        .signature-line { border-top: 1px solid #333; margin-top: 60px; padding-top: 10px; font-weight: bold; width: 200px; text-align: center; }
        .watermark { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-45deg); font-size: 80px; color: rgba(0,0,0,0.03); font-weight: 900; pointer-events: none; text-transform: uppercase; white-space: nowrap; }
        @media print {
            body { background: #fff; margin: 0; }
            .voucher-container { box-shadow: none; margin: 0; border: 1px solid #eee; border-top: 5px solid <?php echo $v['type']=='deposit' ? '#198754' : '#dc3545'; ?>; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

<div class="no-print text-center mt-3">
    <button class="btn btn-primary" onclick="window.print()">Print Now</button>
    <button class="btn btn-secondary" onclick="window.close()">Close</button>
</div>

<div class="voucher-container">
    <div class="watermark"><?php echo $v['type']; ?></div>
    
    <div class="voucher-header d-flex justify-content-between align-items-center">
        <div>
            <img src="<?php echo $school_logo; ?>" height="50" class="mb-2">
            <h4 class="mb-0 fw-bold"><?php echo $school_name; ?></h4>
            <small class="text-muted">Account Management System</small>
        </div>
        <div class="text-end">
            <h2 class="voucher-title mb-0"><?php echo $v['type'] == 'deposit' ? 'Income' : 'Expense'; ?> Voucher</h2>
            <div class="voucher-id">V-<?php echo strtoupper(substr($v['type'], 0, 3)); ?>-<?php echo 1000 + $v['id']; ?></div>
            <div class="fw-bold mt-2">Date: <?php echo date('d-M-Y', strtotime($v['date'])); ?></div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-8">
            <p class="text-muted small text-uppercase mb-1 fw-bold">Particulars / Head</p>
            <h5 class="fw-bold text-dark"><?php echo htmlspecialchars($v['head_name']); ?></h5>
            <p class="mt-3 text-muted"><?php echo htmlspecialchars($v['description'] ?: 'No description provided.'); ?></p>
        </div>
        <div class="col-4 text-end">
            <p class="text-muted small text-uppercase mb-1 fw-bold">Payment Method</p>
            <div class="badge bg-light text-dark border p-2 mb-3"><?php echo strtoupper($v['payment_mode']); ?></div>
            <?php if($v['transaction_id']): ?>
                <p class="small mb-0"><strong>Ref ID:</strong> <?php echo $v['transaction_id']; ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="row align-items-center">
        <div class="col-6">
            <div class="amount-box">
                <small class="d-block text-muted" style="font-size:12px;">TOTAL AMOUNT</small>
                ₹ <?php echo number_format($v['amount'], 2); ?>
            </div>
        </div>
        <div class="col-6">
            <div class="d-flex flex-column align-items-end">
                <div class="signature-line">Authorized Signatory</div>
            </div>
        </div>
    </div>

    <div class="mt-5 text-center text-muted small">
        <hr>
        This is a computer-generated voucher and does not require a physical signature for digital validation.
    </div>
</div>

</body>
</html>
