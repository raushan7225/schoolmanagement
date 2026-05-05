<?php
// admin/qr-attendance.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Fetch today's scans
$today = date('Y-m-d');
$stmt = $pdo->prepare("
    SELECT a.*, adm.full_name as student_name, adm.roll_number as registration_no, adm.photo 
    FROM qr_attendance a 
    JOIN admissions adm ON a.student_id = adm.id 
    WHERE DATE(a.check_in_time) = ? 
    ORDER BY a.check_in_time DESC 
    LIMIT 20
");
$stmt->execute([$today]);
$scans = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>QR Attendance Scanner <span class="badge bg-danger ms-2" style="font-size: 0.6rem; vertical-align: middle;">PRO</span></h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">QR Attendance</li>
            <li class="breadcrumb-item active">Scanner</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <!-- Live Scanner Interface -->
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header d-flex align-items-center">
                    <i class="fas fa-qrcode text-white me-2"></i>
                    <h5 class="card-title text-white mb-0">Live QR Scanner</h5>
                </div>
                <div class="card-body pt-5 text-center">
                    <div id="reader" class="mx-auto mb-4" style="width: 320px; height: 320px; border: 4px solid var(--theme-primary-color); border-radius: 20px; overflow: hidden;"></div>
                    <div id="scan-result" class="alert d-none mt-3"></div>
                </div>
                <div class="card-footer bg-white border-top py-3 text-center">
                    <button class="btn btn-primary btn-lg shadow-sm px-5" id="start-btn">
                        <i class="fas fa-camera me-2"></i>START SCANNING
                    </button>
                    <div class="mt-2">
                        <p class="text-muted small">Scan Student ID Card QR to mark attendance</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Latest Scans / Feed -->
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-clock-rotate-left text-white me-2"></i>
                        <h5 class="card-title text-white mb-0">Recent Activity Feed</h5>
                    </div>
                    <span class="badge bg-white text-primary fw-bold">Live Feed</span>
                </div>
                <div class="card-body pt-3">
                    <div class="activity-feed" id="attendance-feed">
                        <?php if(empty($scans)): ?>
                            <div class="text-center py-5 text-muted">
                                <i class="fas fa-barcode fs-1 mb-3 opacity-25"></i>
                                <p>No scans recorded today yet.</p>
                            </div>
                        <?php else: foreach($scans as $s): ?>
                        <div class="d-flex align-items-center p-3 mb-3 border rounded bg-light shadow-sm">
                            <img src="<?php echo $s['photo'] ? BASE_URL.'media/students/'.$s['photo'] : 'https://placehold.co/50'; ?>" class="rounded-circle border me-3" width="50" height="50" style="object-fit: cover;">
                            <div class="flex-grow-1">
                                <div class="fw-bold text-dark"><?php echo htmlspecialchars($s['student_name']); ?></div>
                                <div class="small text-muted">ID: <?php echo htmlspecialchars($s['registration_no']); ?></div>
                                <div class="badge bg-success-light text-success border border-success rounded-pill px-2 mt-1" style="font-size: 0.65rem;">
                                    <i class="fas fa-check-circle me-1"></i> Marked Present
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-primary"><?php echo date('h:i A', strtotime($s['check_in_time'])); ?></div>
                                <div class="small text-muted">Today</div>
                            </div>
                        </div>
                        <?php endforeach; endif; ?>
                    </div>
                </div>
                <div class="card-footer bg-white text-center py-2">
                    <a href="qr-attendance-report.php" class="small fw-bold text-decoration-none">View All Today's Attendance <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://unpkg.com/html5-qrcode"></script>
<script>
'use strict';
const HANDLER = '<?php echo BASE_URL; ?>ajax/attendance_handler.php';

function onScanSuccess(decodedText, decodedResult) {
    // Expecting registration_no in QR
    console.log(`Code scanned = ${decodedText}`, decodedResult);
    
    // Stop scanner briefly to prevent multiple scans
    html5QrcodeScanner.pause(true);
    
    const resultDiv = document.getElementById('scan-result');
    resultDiv.classList.remove('d-none', 'alert-success', 'alert-danger');
    resultDiv.classList.add('alert-info');
    resultDiv.textContent = "Processing scan...";

    fetch(HANDLER, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=mark_attendance&reg_no=${encodeURIComponent(decodedText)}`
    })
    .then(r => r.json())
    .then(res => {
        resultDiv.classList.remove('alert-info');
        if(res.status === 'success') {
            resultDiv.classList.add('alert-success');
            resultDiv.innerHTML = `<i class="fas fa-check-circle me-2"></i> ${res.message}`;
            // Refresh feed
            setTimeout(() => location.reload(), 1500);
        } else {
            resultDiv.classList.add('alert-danger');
            resultDiv.innerHTML = `<i class="fas fa-times-circle me-2"></i> ${res.message}`;
            // Resume after 2 seconds
            setTimeout(() => html5QrcodeScanner.resume(), 2000);
        }
    });
}

let html5QrcodeScanner;
document.getElementById('start-btn').addEventListener('click', function() {
    this.disabled = true;
    html5QrcodeScanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: 250 });
    html5QrcodeScanner.render(onScanSuccess);
});
</script>

<style>
#reader__dashboard_section_csr button {
    display: inline-block;
    padding: 5px 15px;
    background: var(--theme-secondary-color);
    color: white;
    border: none;
    border-radius: 5px;
    font-weight: bold;
    margin: 5px;
}
#reader { border: none !important; }
</style>

<?php include(__DIR__ . "/includes/footer.php"); ?>
