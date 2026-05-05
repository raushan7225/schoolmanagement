<?php
// admin/frontend-student-achievement.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Fetch existing achievements
$stmt = $pdo->query("SELECT * FROM frontend_achievements ORDER BY created_at DESC");
$achievements = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>Student Achievers</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Frontend</li>
            <li class="breadcrumb-item active">Achievements</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle datatable-premium"
                               data-add-btn='{"text":"Add New Achiever","onclick":"openAddModal()","icon":"fas fa-trophy"}'>
                            <thead class="table-light">
                                <tr>
                                    <th width="60">S.No.</th>
                                    <th>Identity</th>
                                    <th>Achievement</th>
                                    <th>Status</th>
                                    <th class="text-end" data-no-sort>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($achievements)): $sn=1; foreach($achievements as $a): ?>
                                <tr id="achiverow-<?php echo $a['id']; ?>">
                                    <td><?php echo $sn++; ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo $a['photo'] ? BASE_URL . 'media/frontend/' . $a['photo'] : 'https://placehold.co/60'; ?>" class="rounded-circle border me-2" width="40" height="40" style="object-fit: cover;">
                                            <div class="fw-bold text-dark small"><?php echo htmlspecialchars($a['student_name']); ?></div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-primary small"><?php echo htmlspecialchars($a['title']); ?></div>
                                        <div class="x-small text-muted italic text-truncate" style="max-width: 200px;"><?php echo htmlspecialchars($a['description']); ?></div>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $a['status'] == 1 ? 'success' : 'secondary'; ?> rounded-pill px-3">
                                            <?php echo $a['status'] == 1 ? 'Active' : 'Off'; ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-warning btn-icon me-1" title="Edit" onclick="editAchive(<?php echo $a['id']; ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger btn-icon" title="Delete" onclick="deleteAchive(<?php echo $a['id']; ?>, '<?php echo addslashes($a['student_name']); ?>')">
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
<div class="modal fade" id="modalAchive" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle"><i class="fas fa-medal me-2"></i>Achiever Form</h5>
                <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="achievementForm" novalidate enctype="multipart/form-data">
                <input type="hidden" name="action" value="save_achievement">
                <input type="hidden" name="id" id="a-id" value="">
                <div class="modal-body px-3 py-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Student Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="student_name" id="a-name" placeholder="e.g. Rahul Kumar" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Achievement Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="title" id="a-title" placeholder="e.g. State Level Topper" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Status</label>
                            <select class="form-select" name="status" id="a-status">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Student Photo</label>
                            <input type="file" class="form-control" name="photo" id="a-photo" accept="image/*">
                        </div>
                        <div class="col-12 text-center d-none" id="img-preview-box">
                            <img src="" id="img-preview" class="rounded-circle border p-1 bg-light shadow-sm" style="width: 80px; height: 80px; object-fit: cover;">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Description / Bio</label>
                            <textarea class="form-control" name="description" id="a-desc" rows="3" placeholder="Brief details..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top py-2">
                    <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">CANCEL</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold" id="btn-save">
                        <i class="fas fa-trophy me-2"></i>SHOWCASE NOW
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
'use strict';
const HANDLER = '<?php echo BASE_URL; ?>ajax/frontend_handler.php';
const ALL_ACHIVES = <?php echo json_encode($achievements); ?>;
let modalAchive;

document.addEventListener('DOMContentLoaded', function() {
    modalAchive = new bootstrap.Modal(document.getElementById('modalAchive'));
    const form = document.getElementById('achievementForm');

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!form.checkValidity()) { form.classList.add('was-validated'); return; }
        
        const btn = document.getElementById('btn-save');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>SAVING...';
        
        fetch(HANDLER, { method: 'POST', body: new FormData(form) })
        .then(r => r.json()).then(res => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-trophy me-2"></i>SHOWCASE NOW';
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
    modalAchive.show();
}

function editAchive(id) {
    const a = ALL_ACHIVES.find(x => x.id == id);
    if (!a) return;
    
    resetForm();
    document.getElementById('a-id').value = a.id;
    document.getElementById('a-name').value = a.student_name;
    document.getElementById('a-title').value = a.title;
    document.getElementById('a-desc').value = a.description;
    document.getElementById('a-status').value = a.status;
    
    if(a.photo) {
        document.getElementById('img-preview-box').classList.remove('d-none');
        document.getElementById('img-preview').src = `<?php echo BASE_URL; ?>media/frontend/${a.photo}`;
    }
    
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>Edit Achiever';
    modalAchive.show();
}

function resetForm() {
    const form = document.getElementById('achievementForm');
    form.reset();
    form.classList.remove('was-validated');
    document.getElementById('a-id').value = '';
    document.getElementById('img-preview-box').classList.add('d-none');
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-medal me-2"></i>Achiever Form';
}

function deleteAchive(id, name) {
    window.confirmDelete({
        target: name,
        onConfirm: function(cModal, btn) {
            const fd = new FormData();
            fd.append('action', 'delete_achievement');
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
