<?php
// admin/change-session-year.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Fetch active sessions
$sessions = $pdo->query("SELECT * FROM academic_sessions WHERE status = 1 ORDER BY session_year DESC")->fetchAll(PDO::FETCH_ASSOC);

// Current active session (usually from settings or global config)
$current_session = "2024-2025"; // Placeholder, usually from a settings table
?>

<div class="pagetitle">
    <h1>Active Session Control</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Session Year</li>
            <li class="breadcrumb-item active">Switch Global Session</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <!-- Change Session Form -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-primary py-3">
                    <h5 class="card-title mb-0 fw-bold"><i class="fas fa-sync-alt me-2"></i>GLOBAL SESSION SWITCH</h5>
                </div>
                <div class="card-body pt-4">
                    <form id="changeSessionForm" novalidate>
                        <input type="hidden" name="action" value="change_global_session">
                        
                        <div class="mb-4 text-center py-4 bg-light rounded-4 border border-primary border-opacity-25 shadow-sm">
                            <div class="text-muted small fw-bold mb-1">CURRENT ACTIVE ACADEMIC YEAR</div>
                            <div class="display-5 fw-bold text-primary mb-0"><?php echo $current_session; ?></div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark fs-6">Select Target Session <span class="text-danger">*</span></label>
                            <select class="form-select form-select-lg border-primary border-2 shadow-sm fw-bold text-primary" name="target_session" required>
                                <option value="">CHOOSE NEW SESSION...</option>
                                <?php foreach($sessions as $s): ?>
                                    <option value="<?php echo $s['session_year']; ?>" <?php echo ($current_session == $s['session_year']) ? 'selected' : ''; ?>>
                                        <?php echo $s['session_year']; ?> (<?php echo ($current_session == $s['session_year']) ? 'ACTIVE' : 'READY'; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text mt-3 text-muted">
                                <i class="fas fa-info-circle me-1 text-info"></i> 
                                Switching the session will update all data filters across the entire administrative portal.
                            </div>
                        </div>

                        <div class="alert bg-danger bg-opacity-10 border border-danger border-opacity-25 d-flex align-items-start mb-0 rounded-4 p-4 shadow-sm">
                            <div class="bg-danger text-white rounded-circle p-2 me-3 shadow-sm">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div>
                                <strong class="text-danger d-block mb-1 fs-6">CRITICAL TRANSITION WARNING</strong>
                                <p class="mb-0 small text-danger text-opacity-75">
                                    This operation will instantly filter all student lists, fee structures, exam marks, and dashboard analytics to the selected session year. Verify all data migrations before proceeding.
                                </p>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer bg-light border-top py-3 text-end">
                    <button type="submit" form="changeSessionForm" class="btn btn-danger btn-lg px-5 fw-bold shadow-sm" id="btn-switch">
                        <i class="fas fa-check-double me-2"></i>CONFIRM SYSTEM SWITCH
                    </button>
                </div>
            </div>
        </div>

        <!-- Impact Analysis Card -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100 bg-white shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4 pb-2 border-bottom">
                        <div class="bg-info text-white p-2 rounded me-3"><i class="fas fa-project-diagram"></i></div>
                        <h6 class="fw-bold text-dark mb-0 fs-5">SYSTEM-WIDE IMPACT ANALYSIS</h6>
                    </div>
                    
                    <div class="impact-list">
                        <div class="d-flex align-items-center mb-4 p-3 bg-light rounded-4 border border-white shadow-sm hover-elevate">
                            <div class="bg-white rounded-circle shadow-sm p-3 me-3 text-info"><i class="fas fa-users-viewfinder fa-lg"></i></div>
                            <div>
                                <div class="fw-bold text-dark">Student Management</div>
                                <div class="small text-muted">Only records associated with the selected session will be visible.</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-4 p-3 bg-light rounded-4 border border-white shadow-sm hover-elevate">
                            <div class="bg-white rounded-circle shadow-sm p-3 me-3 text-success"><i class="fas fa-wallet fa-lg"></i></div>
                            <div>
                                <div class="fw-bold text-dark">Fee Collection & Dues</div>
                                <div class="small text-muted">Ledgers will calculate balances based on the active session's fee schedule.</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-4 p-3 bg-light rounded-4 border border-white shadow-sm hover-elevate">
                            <div class="bg-white rounded-circle shadow-sm p-3 me-3 text-warning"><i class="fas fa-chart-pie fa-lg"></i></div>
                            <div>
                                <div class="fw-bold text-dark">Analytical Insights</div>
                                <div class="small text-muted">All dashboard statistics and growth charts will reflect current year metrics.</div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 text-center p-4 bg-success bg-opacity-5 rounded-4 border border-success border-dashed">
                        <div class="display-6 text-success mb-2"><i class="fas fa-shield-check"></i></div>
                        <p class="mb-0 small fw-bold text-success">This operation is secure and fully reversible through this panel.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.getElementById('changeSessionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    if(!confirm('Are you absolutely sure you want to switch the global academic session? This will affect all users.')) return;
    
    const btn = document.getElementById('btn-switch');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>SWITCHING SYSTEM...';
    
    // Simulate AJAX call
    setTimeout(() => {
        alert('Global session updated successfully!');
        location.reload();
    }, 1500);
});
</script>

<style>
.hover-elevate { transition: all 0.3s ease; }
.hover-elevate:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important; }
</style>

<?php include(__DIR__ . "/includes/footer.php"); ?>
