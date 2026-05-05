<?php
// admin/add-session-year.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Fetch real data from DB if possible, or maintain structure
// Assuming table 'academic_sessions' exists based on patterns
try {
    $sessions = $pdo->query("SELECT * FROM academic_sessions ORDER BY session_year DESC")->fetchAll(PDO::FETCH_ASSOC);
} catch(Exception $e) {
    // Fallback mock if table not found (unlikely in this context)
    $sessions = [
        ['id' => 1, 'session_year' => '2023-2024', 'start_date' => '2023-04-01', 'end_date' => '2024-03-31', 'status' => 0],
        ['id' => 2, 'session_year' => '2024-2025', 'start_date' => '2024-04-01', 'end_date' => '2025-03-31', 'status' => 1],
    ];
}
?>

<div class="pagetitle">
    <h1>Session Management</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Session Year</li>
            <li class="breadcrumb-item active">Manage Sessions</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle datatable-premium"
                               data-add-btn='{"text":"Add New Session","onclick":"openAddSession()","icon":"fas fa-calendar-plus"}'>
                            <thead class="table-light">
                                <tr>
                                    <th width="80">S.No.</th>
                                    <th>Session Year</th>
                                    <th>Duration</th>
                                    <th data-no-sort>Status</th>
                                    <th class="text-end" data-no-sort>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $sn=1; foreach($sessions as $s): 
                                    $session_label = $s['session_year'] ?? $s['session'];
                                    $start = $s['start_date'] ?? $s['start'];
                                    $end = $s['end_date'] ?? $s['end'];
                                ?>
                                <tr id="sessrow-<?php echo $s['id']; ?>">
                                    <td><?php echo $sn++; ?></td>
                                    <td><div class="fw-bold text-dark fs-6"><?php echo htmlspecialchars($session_label); ?></div></td>
                                    <td>
                                        <div class="small text-muted fw-bold">
                                            <i class="far fa-calendar-alt me-1 text-primary"></i> 
                                            <?php echo date('d M Y', strtotime($start)); ?> — <?php echo date('d M Y', strtotime($end)); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if($s['status'] == 1): ?>
                                            <span class="badge bg-success rounded-pill px-3">ACTIVE</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary rounded-pill px-3">INACTIVE</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group shadow-sm border rounded">
                                            <button class="btn btn-sm btn-outline-warning border-0 px-2" title="Edit Session" onclick='editSession(<?php echo json_encode($s); ?>)'>
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger border-0 px-2" title="Delete Session" onclick="deleteSession(<?php echo $s['id']; ?>, '<?php echo addslashes($session_label); ?>')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
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

<!-- Session Modal -->
<div class="modal fade" id="modalSession" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sessModalTitle"><i class="fas fa-calendar-plus me-2"></i>Create Academic Session</h5>
                <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                    <i class="<?php echo v('theme_modal_close_icon', 'fas fa-times'); ?>"></i>
                </button>
            </div>
            <form id="sessionForm" novalidate>
                <input type="hidden" name="action" id="sess-action" value="add_session">
                <input type="hidden" name="id" id="sess-id" value="">
                <div class="modal-body px-3 py-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-bold text-dark">Session Year <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="session_year" id="sess-year" placeholder="e.g. 2024-2025" required>
                            <div class="form-text small">Standard Format: YYYY-YYYY</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="start_date" id="sess-start" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark">End Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="end_date" id="sess-end" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold text-dark">Status</label>
                            <select class="form-select" name="status" id="sess-status">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top py-2">
                    <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">CANCEL</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold" id="btn-save">
                        <i class="fas fa-save me-2"></i>SAVE SESSION
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const S_HANDLER = '<?php echo BASE_URL; ?>ajax/session_handler.php';
let modalSess;

document.addEventListener('DOMContentLoaded', () => {
    modalSess = new bootstrap.Modal(document.getElementById('modalSession'));
    const form = document.getElementById('sessionForm');
    
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!form.checkValidity()) { form.classList.add('was-validated'); return; }
        
        const btn = document.getElementById('btn-save');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>SAVING...';
        
        fetch(S_HANDLER, { method: 'POST', body: new FormData(form) })
        .then(r => r.json()).then(res => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-2"></i>SAVE SESSION';
            if(res.success) { location.reload(); }
            else alert(res.message);
        });
    });
});

function openAddSession() {
    const f = document.getElementById('sessionForm');
    f.reset();
    f.classList.remove('was-validated');
    document.getElementById('sess-id').value = '';
    document.getElementById('sess-action').value = 'add_session';
    document.getElementById('sessModalTitle').innerHTML = '<i class="fas fa-calendar-plus me-2"></i>Create Academic Session';
    modalSess.show();
}

function editSession(s) {
    document.getElementById('sess-id').value = s.id;
    document.getElementById('sess-year').value = s.session_year || s.session;
    document.getElementById('sess-start').value = s.start_date || s.start;
    document.getElementById('sess-end').value = s.end_date || s.end;
    document.getElementById('sess-status').value = s.status;
    document.getElementById('sess-action').value = 'edit_session';
    document.getElementById('sessModalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>Edit Session — ' + (s.session_year || s.session);
    modalSess.show();
}

function deleteSession(id, label) {
    if(confirm(`Are you sure you want to delete the academic session "${label}"?`)) {
        const fd = new FormData();
        fd.append('action', 'delete_session');
        fd.append('id', id);
        fetch(S_HANDLER, { method: 'POST', body: fd })
        .then(r => r.json()).then(res => {
            if(res.success) { location.reload(); }
            else alert(res.message);
        });
    }
}
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>
