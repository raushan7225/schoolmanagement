<?php
// admin/franchise-notice.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Fetch sent notices
$notices = $pdo->query("SELECT * FROM franchise_notices ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

// Count total franchises for metadata
$totalCenters = $pdo->query("SELECT COUNT(*) FROM franchises")->fetchColumn();
?>

<div class="pagetitle">
    <h1>Franchise Notice</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Franchise Management</li>
            <li class="breadcrumb-item active">Franchise Notice</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <!-- Sent Notices Table -->
        <div class="col-12">
            <div class="card">
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle datatable-premium"
                               data-add-btn='{"text":"Broadcast Notice","onclick":"openAddNotice()","icon":"fas fa-bullhorn"}'>
                            <thead class="table-light">
                                <tr>
                                    <th width="60">S.No.</th>
                                    <th>Broadcast Date</th>
                                    <th>Subject</th>
                                    <th data-no-sort>Attachment</th>
                                    <th class="text-end" data-no-sort>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $sn=1; foreach($notices as $n): ?>
                                <tr id="noticerow-<?php echo $n['id']; ?>">
                                    <td><?php echo $sn++; ?></td>
                                    <td><div class="small fw-bold text-dark"><?php echo date('d M Y', strtotime($n['created_at'])); ?></div></td>
                                    <td>
                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($n['subject']); ?></div>
                                        <small class="text-muted">Broadcasted to all centers</small>
                                    </td>
                                    <td>
                                        <?php if($n['attachment']): ?>
                                        <a href="<?php echo BASE_URL . 'media/franchise/notices/' . $n['attachment']; ?>" target="_blank" class="btn btn-xs btn-outline-info rounded-pill px-3">
                                            <i class="fas fa-paperclip me-1"></i>View File
                                        </a>
                                        <?php else: ?>
                                        <span class="text-muted small">None</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-danger btn-icon" title="Delete" onclick="deleteNotice(<?php echo $n['id']; ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
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

<!-- Add Modal -->
<div class="modal fade" id="modalNotice" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title"><i class="fas fa-bullhorn me-2"></i>Broadcast Notice to Centers</h5>
            <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                <i class="<?php echo v('theme_modal_close_icon', 'fas fa-times'); ?>"></i>
            </button>
        </div>
        <form id="notice-form" novalidate>
            <input type="hidden" name="action" value="send_notice">
            <div class="modal-body px-3 py-4">
                <div class="mb-3">
                    <label class="form-label fw-bold">Subject <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="subject" placeholder="Notice subject..." required>
                    <div class="invalid-feedback">Subject is required.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Message <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="message" rows="5" placeholder="Write notice content here..." required></textarea>
                    <div class="invalid-feedback">Message is required.</div>
                </div>
                <div class="mb-0">
                    <label class="form-label fw-bold">Attachment <span class="text-muted">(Optional)</span></label>
                    <input type="file" class="form-control" name="attachment" accept=".pdf,.jpg,.png,.docx">
                    <small class="text-muted">Allowed: PDF, JPG, PNG, DOCX</small>
                </div>
            </div>
            <div class="modal-footer bg-light border-top py-2">
                <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">CANCEL</button>
                <button type="submit" class="btn btn-primary px-4 fw-bold" id="btn-send">
                    <i class="fas fa-paper-plane me-2"></i>BROADCAST NOW
                </button>
            </div>
        </form>
    </div>
  </div>
</div>

<script>
const HANDLER = '<?php echo BASE_URL; ?>ajax/franchise_handler.php';
let modalNotice;

document.addEventListener('DOMContentLoaded', function() {
    modalNotice = new bootstrap.Modal(document.getElementById('modalNotice'));
    const form = document.getElementById('notice-form');

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!form.checkValidity()) { form.classList.add('was-validated'); return; }
        
        const btn = document.getElementById('btn-send');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>BROADCASTING...';
        
        const fd = new FormData(form);
        fetch(HANDLER, { method: 'POST', body: fd })
        .then(r => r.json()).then(res => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>BROADCAST NOW';
            if(res.success) { location.reload(); }
            else alert(res.message);
        });
    });
});

function openAddNotice() {
    document.getElementById('notice-form').reset();
    document.getElementById('notice-form').classList.remove('was-validated');
    modalNotice.show();
}

function deleteNotice(id) {
    window.confirmDelete({
        target: 'this notice',
        onConfirm: function(modal, btn) {
            const fd = new FormData();
            fd.append('action', 'delete_notice');
            fd.append('id', id);
            fetch(HANDLER, { method: 'POST', body: fd })
            .then(r => r.json()).then(res => {
                modal.hide();
                if(res.success) { 
                    location.reload();
                    Swal.fire('Deleted!', 'Notice has been removed.', 'success');
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            });
        }
    });
}
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>
