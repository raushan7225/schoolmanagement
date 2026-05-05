<?php
// admin/print-session.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Fetch existing sessions
$sessions = $pdo->query("SELECT * FROM admission_sessions ORDER BY session_label DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>Manage Academic Sessions</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Course Management</li>
            <li class="breadcrumb-item active">Manage Sessions</li>
        </ol>
    </nav>
</div>

<section class="section">
<div class="row">

    <!-- ══ Left: Add Form ══════════════════════════════════════ -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <i class="fas fa-print text-white me-2"></i>
                <h5 class="card-title text-white mb-0" id="form-title">Add New Session</h5>
            </div>
            <div class="card-body pt-4">
                <form id="sessionForm" novalidate>
                    <input type="hidden" name="action" id="ses-action" value="add_session">
                    <input type="hidden" name="id" id="ses-id" value="">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Session Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="session_label" id="ses-label"
                               placeholder="e.g. 2024-25" required>
                        <div class="invalid-feedback">Session name is required.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Status</label>
                        <select class="form-select" name="status" id="ses-status">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary" id="btn-save">
                            <i class="fas fa-save me-2"></i>Save Session
                        </button>
                        <button type="button" class="btn btn-outline-secondary d-none" id="btn-cancel" onclick="resetForm()">
                            Cancel Edit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ══ Right: Session Audit List ═══════════════════════════ -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <i class="fas fa-calendar-alt text-white me-2"></i>
                <h5 class="card-title text-white mb-0">Session History</h5>
            </div>
            <div class="card-body pt-3">
                <div class="table-responsive">
                    <table class="table table-hover align-middle datatable-premium">
                        <thead class="table-light">
                            <tr>
                                <th>S.No.</th>
                                <th>Session Label</th>
                                <th data-no-sort>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($sessions)): $sn=1; foreach($sessions as $s): ?>
                            <tr id="sesrow-<?php echo $s['id']; ?>">
                                <td><?php echo $sn++; ?></td>
                                <td><strong><?php echo htmlspecialchars($s['session_label']); ?></strong></td>
                                <td>
                                    <span class="badge bg-<?php echo $s['status'] == 1 ? 'success' : 'danger'; ?> rounded-pill">
                                        <?php echo $s['status'] == 1 ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-warning btn-icon me-1" title="Edit" onclick='editSession(<?php echo json_encode($s); ?>)'>
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger btn-icon" title="Delete" onclick="deleteSession(<?php echo $s['id']; ?>, '<?php echo addslashes($s['session_label']); ?>')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

</div><!-- /.row -->
</section>

<script>
const HANDLER = '<?php echo BASE_URL; ?>ajax/course_handler.php';

(function () {
    'use strict';
    const form = document.getElementById('sessionForm');
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!form.checkValidity()) { form.classList.add('was-validated'); return; }
        
        const btn = document.getElementById('btn-save');
        btn.disabled = true;
        
        fetch(HANDLER, { method: 'POST', body: new FormData(form) })
        .then(r => r.json()).then(res => {
            btn.disabled = false;
            if(res.success) { location.reload(); }
            else alert(res.message);
        });
    });
})();

function editSession(s) {
    document.getElementById('ses-id').value = s.id;
    document.getElementById('ses-action').value = 'edit_session';
    document.getElementById('ses-label').value = s.session_label;
    document.getElementById('ses-status').value = s.status;
    
    document.getElementById('form-title').textContent = 'Edit Academic Session';
    document.getElementById('btn-cancel').classList.remove('d-none');
    window.scrollTo({top: 0, behavior: 'smooth'});
}

function resetForm() {
    document.getElementById('sessionForm').reset();
    document.getElementById('ses-id').value = '';
    document.getElementById('ses-action').value = 'add_session';
    document.getElementById('form-title').textContent = 'Add New Session';
    document.getElementById('btn-cancel').classList.add('d-none');
}

function deleteSession(id, name) {
    window.confirmDelete({
        target: name,
        onConfirm: function(modal, btn) {
            const fd = new FormData();
            fd.append('action', 'delete_session');
            fd.append('id', id);
            fetch(HANDLER, { method: 'POST', body: fd })
            .then(r => r.json()).then(res => {
                modal.hide();
                if(res.success) {
                    Swal.fire({ icon: 'success', title: 'Deleted!', text: res.message, timer: 1500, showConfirmButton: false })
                    .then(() => location.reload());
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            });
        }
    });
}
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>
