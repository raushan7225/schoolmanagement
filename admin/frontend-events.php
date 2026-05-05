<?php
// admin/frontend-events.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Fetch existing events
$stmt = $pdo->query("SELECT * FROM frontend_events ORDER BY event_date DESC, created_at DESC");
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>Events Management</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Frontend</li>
            <li class="breadcrumb-item active">Events</li>
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
                               data-add-btn='{"text":"Add New Event","onclick":"openAddModal()","icon":"fas fa-calendar-plus"}'>
                            <thead class="table-light">
                                <tr>
                                    <th width="60">S.No.</th>
                                    <th>Event Details</th>
                                    <th>Date & Venue</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-end" data-no-sort>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($events)): $sn=1; foreach($events as $e): ?>
                                <tr id="eventrow-<?php echo $e['id']; ?>">
                                    <td><?php echo $sn++; ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if($e['image']): ?>
                                                <img src="<?php echo BASE_URL; ?>media/frontend/<?php echo $e['image']; ?>" class="rounded me-2 border" style="width: 50px; height: 50px; object-fit: cover;">
                                            <?php endif; ?>
                                            <div>
                                                <div class="fw-bold text-primary"><?php echo htmlspecialchars($e['title']); ?></div>
                                                <small class="text-muted text-truncate d-inline-block" style="max-width: 250px;"><?php echo htmlspecialchars($e['description']); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="badge bg-info-light text-info border border-info rounded-pill px-3 mb-1">
                                            <i class="far fa-calendar-alt me-1"></i> <?php echo date('d M Y', strtotime($e['event_date'])); ?>
                                        </div>
                                        <div class="small text-muted"><i class="fas fa-map-marker-alt me-1 text-danger"></i> <?php echo htmlspecialchars($e['location']); ?></div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-<?php echo $e['status'] == 1 ? 'success' : 'danger'; ?> rounded-pill px-3">
                                            <?php echo $e['status'] == 1 ? 'Active' : 'Hidden'; ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-warning btn-icon me-1" title="Edit" onclick="editEvent(<?php echo $e['id']; ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger btn-icon" title="Delete" onclick="deleteEvent(<?php echo $e['id']; ?>, '<?php echo addslashes($e['title']); ?>')">
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
<div class="modal fade" id="modalEvent" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle"><i class="fas fa-calendar-plus me-2"></i>Add New Event</h5>
                <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="eventForm" novalidate enctype="multipart/form-data">
                <input type="hidden" name="action" value="save_event">
                <input type="hidden" name="id" id="e-id" value="">
                <div class="modal-body px-3 py-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Event Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="title" id="e-title" placeholder="e.g. Annual Sports Meet" required>
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Event Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="event_date" id="e-date" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Status</label>
                            <select class="form-select" name="status" id="e-status">
                                <option value="1">Active</option>
                                <option value="0">Hidden</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Location / Venue</label>
                        <input type="text" class="form-control" name="location" id="e-loc" placeholder="e.g. Main Campus Ground">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Cover Image</label>
                        <input type="file" class="form-control" name="image" id="e-image" accept="image/*">
                        <div class="mt-2 d-none text-center" id="img-preview-box">
                            <img src="" id="img-preview" class="img-fluid rounded border" style="max-height: 100px;">
                        </div>
                    </div>
                    
                    <div class="mb-0">
                        <label class="form-label fw-bold">Description</label>
                        <textarea class="form-control" name="description" id="e-desc" rows="4" placeholder="Brief details..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top py-2">
                    <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">CANCEL</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold" id="btn-save">
                        <i class="fas fa-save me-2"></i>SAVE EVENT
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
'use strict';
const HANDLER = '<?php echo BASE_URL; ?>ajax/frontend_handler.php';
const ALL_EVENTS = <?php echo json_encode($events); ?>;
let modalEvent;

document.addEventListener('DOMContentLoaded', function() {
    modalEvent = new bootstrap.Modal(document.getElementById('modalEvent'));
    const form = document.getElementById('eventForm');

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!form.checkValidity()) { form.classList.add('was-validated'); return; }
        
        const btn = document.getElementById('btn-save');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>SAVING...';
        
        fetch(HANDLER, { method: 'POST', body: new FormData(form) })
        .then(r => r.json()).then(res => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-2"></i>SAVE EVENT';
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
    modalEvent.show(); 
}

function editEvent(id) {
    const e = ALL_EVENTS.find(x => x.id == id);
    if (!e) return;
    
    resetForm();
    document.getElementById('e-id').value = e.id;
    document.getElementById('e-title').value = e.title;
    document.getElementById('e-date').value = e.event_date;
    document.getElementById('e-loc').value = e.location;
    document.getElementById('e-desc').value = e.description;
    document.getElementById('e-status').value = e.status;
    
    if(e.image) {
        document.getElementById('img-preview-box').classList.remove('d-none');
        document.getElementById('img-preview').src = `<?php echo BASE_URL; ?>media/frontend/${e.image}`;
    }
    
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>Edit Event';
    modalEvent.show();
}

function resetForm() {
    const form = document.getElementById('eventForm');
    form.reset();
    form.classList.remove('was-validated');
    document.getElementById('e-id').value = '';
    document.getElementById('img-preview-box').classList.add('d-none');
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-calendar-plus me-2"></i>Add New Event';
}

function deleteEvent(id, name) {
    window.confirmDelete({
        target: name,
        onConfirm: function(cModal, btn) {
            const fd = new FormData();
            fd.append('action', 'delete_event');
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
