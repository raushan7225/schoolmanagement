<?php
// admin/franchise-gateway.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Fetch Configured Gateways from DB
$gatewaysStmt = $pdo->query("
    SELECT fg.*, f.center_name, f.center_code 
    FROM franchise_gateways fg 
    JOIN franchises f ON fg.franchise_id = f.id
");
$gateways = $gatewaysStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch active franchises for dropdown
$franchisesStmt = $pdo->query("SELECT id, center_name, center_code FROM franchises WHERE status = 1");
$allFranchises = $franchisesStmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate stats
$activeCount = 0;
$offlineCount = 0;
foreach($gateways as $g) {
    if($g['status'] == 1) $activeCount++;
    else $offlineCount++;
}
?>

<div class="pagetitle">
    <h1>Franchise Gateways</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Franchise Management</li>
            <li class="breadcrumb-item active">Gateway Settings</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <!-- Quick Stats -->
        <div class="col-12 mb-4">
            <div class="card bg-primary-light border-0">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary rounded p-2 me-3"><i class="fas fa-network-wired fa-lg"></i></div>
                        <div>
                            <h6 class="mb-0 fw-bold">Gateway Audit</h6>
                            <p class="mb-0 text-muted small">Manage payment connections for all partner centers.</p>
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-success-light text-success px-3 py-2 border border-success rounded-pill me-2"><?php echo $activeCount; ?> Active</span>
                        <span class="badge bg-warning-light text-warning px-3 py-2 border border-warning rounded-pill"><?php echo $offlineCount; ?> Offline</span>
                    </div>
                </div>
            </div>
        </div>


        <!-- Gateway List -->
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <i class="fas fa-list me-2"></i>
                    <h5 class="card-title mb-0">Configured Franchise Gateways</h5>
                </div>
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle datatable-premium"
                               data-add-btn='{"text":"Add New Gateway","onclick":"new bootstrap.Modal(document.getElementById(\"addGatewayModal\")).show()","icon":"fas fa-plus"}'>
                            <thead class="table-light">
                                <tr>
                                    <th width="80">S.No.</th>
                                    <th>Franchise / Center Detail</th>
                                    <th>Gateway Engine</th>
                                    <th>Currency</th>
                                    <th>Status</th>
                                    <th class="text-end" data-no-sort>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i=1; foreach($gateways as $g): ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td>
                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($g['center_name']); ?></div>
                                        <div class="small text-muted"><i class="fas fa-fingerprint me-1"></i> <?php echo htmlspecialchars($g['center_code']); ?></div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-credit-card text-primary me-2"></i>
                                            <span><?php echo htmlspecialchars($g['gateway_provider']); ?></span>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-light text-dark border px-3 rounded-pill"><?php echo $g['currency']; ?></span></td>
                                    <td>
                                        <?php if($g['status'] == 1): ?>
                                            <span class="badge bg-success-light text-success border border-success rounded-pill px-3">Enabled</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger-light text-danger border border-danger rounded-pill px-3">Disabled</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-warning btn-icon me-1" title="Edit Configuration"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-outline-danger btn-icon" title="Delete Connection"><i class="fas fa-trash"></i></button>
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

<!-- Add Gateway Modal (Standardized) -->
<div class="modal fade" id="addGatewayModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Configure New Center Gateway</h5>
                <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                    <i class="<?php echo v('theme_modal_close_icon', 'fas fa-times'); ?>"></i>
                </button>
            </div>
            <form id="addGatewayForm" class="was-validated">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Target Franchise Center <span class="text-danger">*</span></label>
                            <select class="form-select" name="franchise_id" required>
                                <option value="">Select Center...</option>
                                <?php foreach($allFranchises as $f): ?>
                                    <option value="<?php echo $f['id']; ?>"><?php echo htmlspecialchars($f['center_name'] . ' (' . $f['center_code'] . ')'); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Gateway Provider</label>
                            <select class="form-select" name="gateway_provider" required>
                                <option value="Razorpay">Razorpay</option>
                                <option value="PayU">PayU Money</option>
                                <option value="PayPal">PayPal</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Status</label>
                            <select class="form-select" name="status">
                                <option value="1">Active / Enabled</option>
                                <option value="0">Inactive / Disabled</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Merchant Key ID <span class="text-danger">*</span></label>
                            <input type="text" name="key_id" class="form-control" required placeholder="rzp_live_xxxxxxx">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Merchant Secret <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="secret_key" class="form-control" required placeholder="••••••••••••">
                                <button class="btn btn-outline-secondary toggle-pass" type="button"><i class="fas fa-eye"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">CANCEL</button>
                    <button type="submit" class="btn btn-primary px-5 fw-bold">SAVE CONNECTION</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Password toggle
document.querySelectorAll('.toggle-pass').forEach(btn => {
    btn.addEventListener('click', function() {
        const input = this.previousElementSibling;
        const type = input.type === 'password' ? 'text' : 'password';
        input.type = type;
        this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
    });
});

// Add Gateway Handler
document.getElementById('addGatewayForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = this.querySelector('button[type="submit"]');
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>SAVING...';
    btn.disabled = true;

    const data = new FormData(this);
    data.append('action', 'add_franchise_gateway');

    fetch('<?php echo BASE_URL; ?>ajax/franchise_handler.php', { method: 'POST', body: data })
    .then(r => r.json())
    .then(res => {
        if(res.success) {
            location.reload();
        } else {
            alert(res.message);
            btn.innerHTML = 'SAVE CONNECTION';
            btn.disabled = false;
        }
    });
});
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>
