<?php
// admin/frontend-notices.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Fetch notices
$stmt = $pdo->query("SELECT * FROM frontend_notices ORDER BY notice_date DESC, created_at DESC");
$notices = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>Notice Board Management</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Frontend</li>
            <li class="breadcrumb-item active">Notices</li>
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
                               data-add-btn='{"text":"Add New Notice","onclick":"openAddModal()","icon":"fas fa-bullhorn"}'>
                            <thead class="table-light">
                                <tr>
                                    <th width="60">S.No.</th>
                                    <th>Notice Information</th>
                                    <th>Date</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-end" data-no-sort>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($notices)): $sn=1; foreach($notices as $n): ?>
                                <tr id="noticerow-<?php echo $n['id']; ?>">
                                    <td><?php echo $sn++; ?></td>
                                    <td>
                                        <div class="fw-bold text-primary"><?php echo htmlspecialchars($n['title']); ?></div>
                                        <div class="small text-muted text-truncate" style="max-width: 300px;"><?php echo htmlspecialchars($n['content']); ?></div>
                                        <?php if($n['file_path']): ?>
                                            <a href="<?php echo BASE_URL; ?>media/frontend/<?php echo $n['file_path']; ?>" target="_blank" class="badge bg-light text-info border border-info mt-1 text-decoration-none">
                                                <i class="fas fa-paperclip me-1"></i> Attachment
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('d M Y', strtotime($n['notice_date'])); ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-<?php echo $n['status'] == 1 ? 'success' : 'danger'; ?> rounded-pill px-3">
                                            <?php echo $n['status'] == 1 ? 'Active' : 'Hidden'; ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-warning btn-icon me-1" title="Edit" onclick="editNotice(<?php echo $n['id']; ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger btn-icon" title="Delete" onclick="deleteNotice(<?php echo $n['id']; ?>, '<?php echo addslashes($n['title']); ?>')">
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
    </div>
</section>

<!-- Add/Edit Modal -->
<div class="modal fade" id="modalNotice" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle"><i class="fas fa-bullhorn me-2"></i>Add New Notice</h5>
                <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="noticeForm" novalidate enctype="multipart/form-data">
                <input type="hidden" name="action" value="save_notice">
                <input type="hidden" name="id" id="n-id" value="">
                <div class="modal-body px-3 py-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Notice Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="title" id="n-title" placeholder="e.g. Admission Open 2024-25" required>
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Notice Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="notice_date" id="n-date" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Status</label>
                            <select class="form-select" name="status" id="n-status">
                                <option value="1">Active</option>
                                <option value="0">Hidden</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Attachment (PDF/Image)</label>
                        <input type="file" class="form-control" name="file" id="n-file" accept=".pdf,image/*">
                        <div class="mt-2 d-none" id="file-link-box">
                            <a href="" id="file-link" target="_blank" class="btn btn-sm btn-light border text-primary">View Current File</a>
                        </div>
                    </div>
                    
                    <div class="mb-0">
                        <label class="form-label fw-bold">Notice Content <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="content" id="n-content" rows="4" placeholder="Detailed message..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top py-2">
                    <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">CANCEL</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold" id="btn-save">
                        <i class="fas fa-save me-2"></i>SAVE NOTICE
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
'use strict';
const HANDLER = '<?php echo BASE_URL; ?>ajax/frontend_handler.php';
const ALL_NOTICES = <?php echo json_encode($notices); ?>;
let modalNotice;

document.addEventListener('DOMContentLoaded', function() {
    modalNotice = new bootstrap.Modal(document.getElementById('modalNotice'));
    const form = document.getElementById('noticeForm');

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!form.checkValidity()) { form.classList.add('was-validated'); return; }
        
        const btn = document.getElementById('btn-save');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>SAVING...';
        
        fetch(HANDLER, { method: 'POST', body: new FormData(form) })
        .then(r => r.json()).then(res => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-2"></i>SAVE NOTICE';
            if(res.success) { 
                Swal.fire({ icon: 'success', title: 'Success', text: res.message, timer: 1500, showConfirmButton: false })
                .then(() => location.reload()); 
            }
            else Swal.fire('Error', res.message, 'error');
        });
    });
});

function openAddModal() { 
    resetForm(); 
    modalNotice.show(); 
}

function editNotice(id) {
    const n = ALL_NOTICES.find(x => x.id == id);
    if (!n) return;
    
    resetForm();
    document.getElementById('n-id').value = n.id;
    document.getElementById('n-title').value = n.title;
    document.getElementById('n-date').value = n.notice_date;
    document.getElementById('n-content').value = n.content;
    document.getElementById('n-status').value = n.status;
    
    if(n.file_path) {
        document.getElementById('file-link-box').classList.remove('d-none');
        document.getElementById('file-link').href = `<?php echo BASE_URL; ?>media/frontend/${n.file_path}`;
    }
    
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>Edit Notice';
    modalNotice.show();
}

function resetForm() {
    const form = document.getElementById('noticeForm');
    form.reset();
    form.classList.remove('was-validated');
    document.getElementById('n-id').value = '';
    document.getElementById('file-link-box').classList.add('d-none');
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-bullhorn me-2"></i>Add New Notice';
}

function deleteNotice(id, name) {
    window.confirmDelete({
        target: name,
        onConfirm: function(cModal, btn) {
            const fd = new FormData();
            fd.append('action', 'delete_notice');
            fd.append('id', id);
            fetch(HANDLER, { method: 'POST', body: fd })
            .then(r => r.json()).then(res => {
                cModal.hide();
                if(res.success) { location.reload(); }
                else Swal.fire('Error', res.message, 'error');
            });
        }
    });
}
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>
