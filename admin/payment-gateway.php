<?php
// admin/payment-gateway.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Fetch settings
$setStmt = $pdo->query("SELECT setting_key, setting_value FROM site_settings");
$settings = $setStmt->fetchAll(PDO::FETCH_KEY_PAIR);
function s($settings, $key, $default = '') {
    return htmlspecialchars($settings[$key] ?? $default);
}

// Map Dynamic Gateways
$gateways = [
    [
        'id' => 'razorpay', 
        'name' => 'Razorpay', 
        'logo' => 'https://razorpay.com/favicon.png', 
        'status' => s($settings, 'pg_razorpay_status', '0'), 
        'currency' => s($settings, 'pg_razorpay_currency', 'INR'), 
        'key_id' => s($settings, 'pg_razorpay_key', ''),
        'secret' => s($settings, 'pg_razorpay_secret', '')
    ],
    [
        'id' => 'payu', 
        'name' => 'PayU Money', 
        'logo' => 'https://www.payumoney.com/favicon.ico', 
        'status' => s($settings, 'pg_payu_status', '0'), 
        'currency' => s($settings, 'pg_payu_currency', 'INR'), 
        'key_id' => s($settings, 'pg_payu_key', ''),
        'secret' => s($settings, 'pg_payu_secret', '')
    ],
    [
        'id' => 'paypal', 
        'name' => 'PayPal', 
        'logo' => 'https://www.paypalobjects.com/webstatic/mktg/logo/pp_cc_mark_37x23.jpg', 
        'status' => s($settings, 'pg_paypal_status', '0'), 
        'currency' => s($settings, 'pg_paypal_currency', 'USD'), 
        'key_id' => s($settings, 'pg_paypal_key', ''),
        'secret' => s($settings, 'pg_paypal_secret', '')
    ],
];
?>

<div class="pagetitle">
    <h1>Payment Gateway</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">System Settings</li>
            <li class="breadcrumb-item active">Payment Gateway</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row g-4">
        <?php foreach($gateways as $g): ?>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm gateway-card <?php echo ($g['status'] == 1) ? 'active-gateway' : ''; ?>">
                <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <img src="<?php echo $g['logo']; ?>" alt="<?php echo $g['name']; ?>" style="height: 30px; filter: grayscale(<?php echo ($g['status'] == 0 ? '100%' : '0'); ?>);">
                    <?php if($g['status'] == 1): ?>
                        <span class="badge bg-success-light text-success rounded-pill px-3"><i class="fas fa-circle fa-xs me-1 pulsate"></i> Connected</span>
                    <?php else: ?>
                        <span class="badge bg-secondary-light text-secondary rounded-pill px-3">Disconnected</span>
                    <?php endif; ?>
                </div>
                <div class="card-body px-4 py-3">
                    <h5 class="fw-bold text-dark mb-1"><?php echo $g['name']; ?></h5>
                    <p class="text-muted small mb-3">Accept payments via Cards, Netbanking, and UPI.</p>
                    
                    <?php if($g['status'] == 1): ?>
                        <div class="bg-light p-2 rounded small border border-dashed mb-3">
                            <div class="text-muted small">Merchant Key ID:</div>
                            <div class="fw-bold text-truncate"><?php echo $g['key_id']; ?></div>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4 bg-light rounded-3 mb-3 border border-dashed">
                            <i class="fas fa-plug text-muted mb-2"></i>
                            <div class="small text-muted">Gateway not configured</div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-white border-0 px-4 pb-4">
                    <button class="btn btn-<?php echo ($g['status'] == 1 ? 'primary' : 'outline-primary'); ?> w-100 fw-bold" data-bs-toggle="modal" data-bs-target="#configGateway_<?php echo $g['id']; ?>">
                        <i class="fas fa-cog me-2"></i><?php echo ($g['status'] == 1 ? 'RECONFIGURE' : 'CONNECT GATEWAY'); ?>
                    </button>
                </div>
            </div>
        </div>

        <!-- Gateway Config Modal -->
        <div class="modal fade" id="configGateway_<?php echo $g['id']; ?>" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content border-0">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-link me-2"></i>Configure <?php echo $g['name']; ?></h5>
                        <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                            <i class="<?php echo v('theme_modal_close_icon', 'fas fa-times'); ?>"></i>
                        </button>
                    </div>
                    <form class="gateway-form" data-gateway="<?php echo $g['id']; ?>">
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Gateway Status</label>
                                    <select class="form-select" name="pg_<?php echo $g['id']; ?>_status">
                                        <option value="1" <?php echo ($g['status'] == '1' ? 'selected' : ''); ?>>Enabled</option>
                                        <option value="0" <?php echo ($g['status'] == '0' ? 'selected' : ''); ?>>Disabled</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Transaction Currency</label>
                                    <select class="form-select" name="pg_<?php echo $g['id']; ?>_currency">
                                        <option value="INR" <?php echo ($g['currency'] == 'INR' ? 'selected' : ''); ?>>INR - Indian Rupee</option>
                                        <option value="USD" <?php echo ($g['currency'] == 'USD' ? 'selected' : ''); ?>>USD - US Dollar</option>
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">API Key / Merchant ID <span class="text-danger">*</span></label>
                                    <input type="text" name="pg_<?php echo $g['id']; ?>_key" class="form-control" value="<?php echo $g['key_id']; ?>" placeholder="Enter Key ID" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">API Secret / Merchant Salt <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" name="pg_<?php echo $g['id']; ?>_secret" class="form-control" placeholder="Enter Secret Key" value="<?php echo $g['secret']; ?>" required>
                                        <button class="btn btn-outline-secondary toggle-pass" type="button"><i class="fas fa-eye"></i></button>
                                    </div>
                                    <div class="form-text small"><i class="fas fa-info-circle me-1"></i> This secret key is encrypted and never shown in plain text.</div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">CANCEL</button>
                            <button type="submit" class="btn btn-primary px-5 fw-bold">UPDATE CONNECTION</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<style>
.gateway-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border-radius: 15px;
}
.gateway-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
}
.active-gateway {
    border: 2px solid var(--theme-primary-color) !important;
}
.pulsate {
    animation: pulse-dot 1.5s infinite;
}
@keyframes pulse-dot {
    0% { opacity: 1; }
    50% { opacity: 0.3; }
    100% { opacity: 1; }
}
</style>

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

// Save Gateway config
document.querySelectorAll('.gateway-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = this.querySelector('button[type="submit"]');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>SAVING...';
        btn.disabled = true;
        
        const data = new FormData(this);
        data.append('group', 'payment_' + this.dataset.gateway);
        
        fetch('<?php echo BASE_URL; ?>ajax/save_settings.php', { method: 'POST', body: data })
        .then(r => r.json())
        .then(res => {
            if(res.success) {
                location.reload();
            } else {
                alert(res.message);
                btn.innerHTML = 'UPDATE CONNECTION';
                btn.disabled = false;
            }
        });
    });
});
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>
