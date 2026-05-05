<?php
// admin/payment-qr.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

$setStmt = $pdo->query("SELECT setting_key, setting_value FROM site_settings");
$settings = $setStmt->fetchAll(PDO::FETCH_KEY_PAIR);
function s($settings, $key, $default = '') {
    return htmlspecialchars($settings[$key] ?? $default);
}
?>

<div class="pagetitle">
    <h1>Direct UPI QR</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">System Settings</li>
            <li class="breadcrumb-item active">Payment QR</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center">
                    <i class="fas fa-qrcode text-white me-2"></i>
                    <h5 class="card-title text-white mb-0">UPI Configuration</h5>
                </div>
                <div class="card-body pt-4">
                    <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center mb-4">
                        <i class="fas fa-triangle-exclamation fs-4 me-3"></i>
                        <div class="small">
                            <strong>Note:</strong> This QR code is used for direct manual payments. Ensure the UPI ID linked to the QR is active and verified.
                        </div>
                    </div>

                    <form id="qrUpdateForm" class="was-validated">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Display Name</label>
                                <input type="text" name="payment_qr_name" class="form-control" value="<?php echo s($settings, 'payment_qr_name'); ?>" placeholder="e.g. School Management System" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">UPI ID / VPA</label>
                                <input type="text" name="payment_qr_upi" class="form-control" value="<?php echo s($settings, 'payment_qr_upi'); ?>" placeholder="e.g. merchant@upi" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Upload QR Code Image <span class="text-danger">*</span></label>
                                <div id="qr-dropzone" class="dropzone p-5 border-dashed rounded text-center bg-light mb-2">
                                    <i class="fas fa-camera fs-2 text-primary mb-2"></i>
                                    <div class="dz-message mb-0 small">Click to upload or drag & drop QR image</div>
                                </div>
                                <div class="form-text small"><i class="fas fa-info-circle me-1"></i> Recommended: Clear square image (JPG/PNG).</div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer bg-white border-top py-3 text-end">
                    <button type="submit" form="qrUpdateForm" class="btn btn-primary px-5 fw-bold">
                        <i class="fas fa-upload me-2"></i>UPDATE QR CODE
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <!-- Mobile Preview Mockup -->
            <div class="card h-100 bg-light border-0 shadow-sm">
                <div class="card-body p-4 text-center">
                    <h6 class="fw-bold mb-4 text-primary"><i class="fas fa-mobile-screen-button me-2"></i>Mobile Payment Preview</h6>
                    
                    <div class="iphone-mockup mx-auto shadow-lg position-relative" style="width: 250px; height: 500px; border: 8px solid #333; border-radius: 35px; background: #fff; overflow: hidden;">
                        <!-- iPhone Notch -->
                        <div class="position-absolute top-0 start-50 translate-middle-x bg-dark" style="width: 100px; height: 18px; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; z-index: 10;"></div>
                        
                        <!-- Screen Content -->
                        <div class="p-3 pt-5">
                            <div class="text-start mb-3">
                                <i class="fas fa-arrow-left text-muted me-2"></i>
                                <span class="fw-bold small">Payment Checkout</span>
                            </div>
                            
                            <div class="payment-card rounded-4 p-3 text-white mb-4" style="background: linear-gradient(135deg, #1b1260 0%, #4c40a5 100%);">
                                <div class="small opacity-75 mb-1">Total Payable</div>
                                <div class="h4 fw-bold mb-0">₹ 5,500.00</div>
                            </div>

                            <div class="qr-container border rounded-4 p-3 shadow-sm bg-white mb-4">
                                <?php 
                                $qr_img = s($settings, 'payment_qr_image');
                                $img_src = $qr_img ? $BASE_URL . $qr_img : "https://placehold.co/300x300/f8f9fa/1b1260?text=SCAN+QR";
                                ?>
                                <img src="<?php echo $img_src; ?>" class="img-fluid rounded" alt="QR Preview">
                                <div class="mt-2 fw-bold small text-dark">Scan & Pay via UPI</div>
                                <div class="text-muted" style="font-size: 10px;">Powered by Secure Gateway</div>
                            </div>

                            <div class="row g-2 px-2">
                                <div class="col-4 text-center opacity-50"><i class="fab fa-google-pay fa-2x"></i></div>
                                <div class="col-4 text-center opacity-50"><i class="fas fa-phone-flip fa-lg mt-2"></i><div style="font-size: 8px;">PhonePe</div></div>
                                <div class="col-4 text-center opacity-50"><i class="fab fa-cc-amazon-pay fa-2x"></i></div>
                            </div>
                        </div>
                    </div>
                    <p class="mt-4 small text-muted px-4">This mockup shows how franchises will see the QR code on their mobile devices during checkout.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.iphone-mockup {
    border: 8px solid #222 !important;
}
.payment-card {
    box-shadow: 0 5px 15px rgba(27, 18, 96, 0.3);
}
</style>

<script>
// Save QR settings
document.getElementById('qrUpdateForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.querySelector('button[form="qrUpdateForm"]');
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>UPDATING...';
    btn.disabled = true;
    
    const data = new FormData(this);
    data.append('group', 'payment_qr');
    
    fetch('<?php echo BASE_URL; ?>ajax/save_settings.php', { method: 'POST', body: data })
    .then(r => r.json())
    .then(res => {
        if(res.success) {
            location.reload();
        } else {
            alert(res.message);
            btn.innerHTML = '<i class="fas fa-upload me-2"></i>UPDATE QR CODE';
            btn.disabled = false;
        }
    });
});

// Dropzone for QR Upload
Dropzone.autoDiscover = false;
if (document.getElementById('qr-dropzone')) {
    new Dropzone("#qr-dropzone", {
        url: '<?php echo BASE_URL; ?>ajax/upload_handler.php',
        acceptedFiles: 'image/*',
        params: { target: 'payment_qr', db_key: 'payment_qr_image' },
        success: function(file, response) {
            location.reload();
        }
    });
}
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>
