<?php
// admin/course-duration.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Fetch existing durations
$durations = $pdo->query("SELECT * FROM course_durations ORDER BY years ASC, months ASC, days ASC, duration_label ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>Course Duration</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Course Management</li>
            <li class="breadcrumb-item active">Duration Units</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <!-- Duration List -->
        <div class="col-12">
            <div class="card">
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle datatable-premium"
                               data-add-btn='{"text":"Add New Duration","onclick":"openAddDuration()","icon":"fas fa-plus"}'>
                            <thead class="table-light">
                                <tr>
                                    <th width="80">S.No.</th>
                                    <th>Duration Name</th>
                                    <th>Pattern (Y-M-D)</th>
                                    <th data-no-sort>Status</th>
                                    <th class="text-end" data-no-sort>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $sn=1; foreach($durations as $d): ?>
                                <tr id="durrow-<?php echo $d['id']; ?>">
                                    <td><?php echo $sn++; ?></td>
                                    <td><div class="fw-bold text-dark"><?php echo htmlspecialchars($d['duration_label']); ?></div></td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <span class="badge bg-primary-light text-primary border px-2"><?php echo $d['years']; ?> Y</span>
                                            <span class="badge bg-info-light text-info border px-2"><?php echo $d['months']; ?> M</span>
                                            <span class="badge bg-success-light text-success border px-2"><?php echo $d['days']; ?> D</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $d['status'] == 1 ? 'success' : 'danger'; ?> rounded-pill">
                                            <?php echo $d['status'] == 1 ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group shadow-sm border rounded">
                                            <button class="btn btn-sm btn-outline-info border-0 px-2" title="View" onclick="viewDuration(<?php echo $d['id']; ?>)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-warning border-0 px-2" title="Edit" onclick='editDuration(<?php echo json_encode($d); ?>)'>
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger border-0 px-2" title="Delete" onclick="deleteDuration(<?php echo $d['id']; ?>, '<?php echo addslashes($d['duration_label']); ?>')">
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

<!-- Add/Edit Modal -->
<div class="modal fade" id="modalDur" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="durModalTitle"><i class="fas fa-clock me-2"></i>Add Duration</h5>
            <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                <i class="<?php echo v('theme_modal_close_icon', 'fas fa-times'); ?>"></i>
            </button>
        </div>
        <form id="durationForm" novalidate>
            <input type="hidden" name="action" id="dur-action" value="add_duration">
            <input type="hidden" name="id" id="dur-id" value="">
            <div class="modal-body px-3 py-4">
                
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Years</label>
                        <input type="number" class="form-control dur-input" name="years" id="dur-years" value="0" min="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Months</label>
                        <input type="number" class="form-control dur-input" name="months" id="dur-months" value="0" min="0" max="11">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Days</label>
                        <input type="number" class="form-control dur-input" name="days" id="dur-days" value="0" min="0" max="30">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Duration Label <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="duration_label" id="dur-label" placeholder="e.g. 1 Year 6 Months" required>
                    <small class="text-muted">This will be auto-generated but you can customize it.</small>
                    <div class="invalid-feedback">Duration label is required.</div>
                </div>

                <div class="mb-0">
                    <label class="form-label fw-bold">Status</label>
                    <select class="form-select" name="status" id="dur-status">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer bg-light border-top py-2">
                <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">CANCEL</button>
                <button type="submit" class="btn btn-primary px-4 fw-bold" id="btn-save">
                    <i class="fas fa-save me-2"></i>SAVE DURATION
                </button>
            </div>
        </form>
    </div>
  </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="modalViewDur" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title"><i class="fas fa-eye me-2"></i>Duration Details</h5>
            <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                <i class="<?php echo v('theme_modal_close_icon', 'fas fa-times'); ?>"></i>
            </button>
        </div>
        <div class="modal-body px-0 py-0" id="view-dur-body">
            <div class="text-center py-5"><div class="spinner-border text-primary"></div></div>
        </div>
    </div>
  </div>
</div>

<script>
const HANDLER = '<?php echo BASE_URL; ?>ajax/course_handler.php';
let modalDur, form;

document.addEventListener('DOMContentLoaded', function() {
    modalDur = new bootstrap.Modal(document.getElementById('modalDur'));
    form = document.getElementById('durationForm');

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!form.checkValidity()) { form.classList.add('was-validated'); return; }
        
        const btn = document.getElementById('btn-save');
        btn.disabled = true;
        
        fetch(HANDLER, { method: 'POST', body: new FormData(form) })
        .then(r => r.json()).then(res => {
            btn.disabled = false;
            if(res.success) { 
                Swal.fire({ icon: 'success', title: 'Success', text: res.message, timer: 1500, showConfirmButton: false })
                .then(() => location.reload()); 
            }
            else Swal.fire('Error', res.message, 'error');
        });
    });

    // Auto-label generation
    document.querySelectorAll('.dur-input').forEach(input => {
        input.addEventListener('input', generateLabel);
    });

    document.getElementById('modalDur').addEventListener('hidden.bs.modal', resetForm);
});

function generateLabel() {
    const y = parseInt(document.getElementById('dur-years').value) || 0;
    const m = parseInt(document.getElementById('dur-months').value) || 0;
    const d = parseInt(document.getElementById('dur-days').value) || 0;
    
    let parts = [];
    if(y > 0) parts.push(y + (y > 1 ? ' Years' : ' Year'));
    if(m > 0) parts.push(m + (m > 1 ? ' Months' : ' Month'));
    if(d > 0) parts.push(d + (d > 1 ? ' Days' : ' Day'));
    
    document.getElementById('dur-label').value = parts.join(' ');
}

function openAddDuration() {
    resetForm();
    modalDur.show();
}

function editDuration(d) {
    if(!form) form = document.getElementById('durationForm');
    form.reset();
    document.getElementById('dur-id').value = d.id;
    document.getElementById('dur-action').value = 'edit_duration';
    document.getElementById('dur-label').value = d.duration_label;
    document.getElementById('dur-years').value = d.years || 0;
    document.getElementById('dur-months').value = d.months || 0;
    document.getElementById('dur-days').value = d.days || 0;
    document.getElementById('dur-status').value = d.status;
    document.getElementById('durModalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>Edit Duration';
    modalDur.show();
}

function resetForm() {
    if(!form) form = document.getElementById('durationForm');
    form.reset();
    document.getElementById('dur-id').value = '';
    document.getElementById('dur-action').value = 'add_duration';
    document.getElementById('dur-years').value = '0';
    document.getElementById('dur-months').value = '0';
    document.getElementById('dur-days').value = '0';
    document.getElementById('durModalTitle').innerHTML = '<i class="fas fa-clock me-2"></i>Add Duration';
    form.classList.remove('was-validated');
}

function viewDuration(id) {
    document.getElementById('view-dur-body').innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>';
    new bootstrap.Modal(document.getElementById('modalViewDur')).show();
    
    fetch(`${HANDLER}?action=view_duration&id=${id}`)
    .then(r => r.json()).then(res => {
        if(!res.success) {
            document.getElementById('view-dur-body').innerHTML = `<p class="text-danger p-3">${res.message}</p>`;
            return;
        }
        const d = res.data;
        document.getElementById('view-dur-body').innerHTML = `
            <div class="enquiry-view-premium p-1">
                <div class="view-section mb-0">
                    <div class="view-section-header">DURATION INFORMATION</div>
                    <div class="row g-3 px-3 py-3">
                        <div class="col-12">
                            <label class="view-label">Duration Name</label>
                            <div class="view-value text-primary fw-bold fs-5">${d.duration_label}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="view-label">Years</label>
                            <div class="view-value fw-bold">${d.years}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="view-label">Months</label>
                            <div class="view-value fw-bold">${d.months}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="view-label">Days</label>
                            <div class="view-value fw-bold">${d.days}</div>
                        </div>
                        <div class="col-md-12">
                            <label class="view-label">Account Status</label>
                            <div class="view-value">
                                <span class="badge bg-${d.status == 1 ? 'success' : 'danger'} rounded-pill px-3">
                                    ${d.status == 1 ? 'Active' : 'Inactive'}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
}

function deleteDuration(id, name) {
    window.confirmDelete({
        target: name,
        onConfirm: function(modal, btn) {
            const fd = new FormData();
            fd.append('action', 'delete_duration');
            fd.append('id', id);
            fetch(HANDLER, { method: 'POST', body: fd })
            .then(r => r.json()).then(res => {
                modal.hide();
                if(res.success) { 
                    Swal.fire({ icon: 'success', title: 'Deleted!', text: res.message, timer: 1500, showConfirmButton: false })
                    .then(() => location.reload()); 
                }
                else Swal.fire('Error', res.message, 'error');
            });
        }
    });
}
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>
