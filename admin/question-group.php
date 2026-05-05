<?php
// admin/question-group.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Fetch existing groups
$groups = $pdo->query("SELECT * FROM question_groups ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>Question Groups <span class="badge bg-danger ms-2" style="font-size: 0.6rem; vertical-align: middle;">PRO</span></h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Online Exam</li>
            <li class="breadcrumb-item active">Question Group</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <!-- Group List -->
        <div class="col-12">
            <div class="card">
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle datatable-premium"
                               data-add-btn='{"text":"Add Question Group","onclick":"openAddGroup()","icon":"fas fa-folder-plus"}'>
                            <thead class="table-light">
                                <tr>
                                    <th width="60">S.No.</th>
                                    <th>Group Name</th>
                                    <th>Description</th>
                                    <th>Created At</th>
                                    <th class="text-end" data-no-sort>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $sn=1; foreach($groups as $g): ?>
                                <tr id="grouprow-<?php echo $g['id']; ?>">
                                    <td><?php echo $sn++; ?></td>
                                    <td><div class="fw-bold text-dark"><?php echo htmlspecialchars($g['name']); ?></div></td>
                                    <td><small class="text-muted"><?php echo htmlspecialchars($g['description']); ?></small></td>
                                    <td><div class="small fw-bold text-dark"><?php echo date('d M Y', strtotime($g['created_at'])); ?></div></td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-warning btn-icon me-1" title="Edit" onclick='editGroup(<?php echo json_encode($g); ?>)'>
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger btn-icon" title="Delete" onclick="deleteGroup(<?php echo $g['id']; ?>, '<?php echo addslashes($g['name']); ?>')">
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

<!-- Add/Edit Modal -->
<div class="modal fade" id="modalGroup" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="modalTitle"><i class="fas fa-folder-plus me-2"></i>Add Question Group</h5>
            <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="addGroupForm" novalidate>
            <input type="hidden" name="action" value="save_question_group">
            <input type="hidden" name="id" id="group-id" value="">
            <div class="modal-body px-3 py-4">
                <div class="mb-3">
                    <label class="form-label fw-bold">Group Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" id="group-name" placeholder="e.g. Basic Computer Fundamentals" required>
                    <div class="invalid-feedback">Please enter a group name.</div>
                </div>
                <div class="mb-0">
                    <label class="form-label fw-bold">Description</label>
                    <textarea class="form-control" name="description" id="group-desc" rows="4" placeholder="Brief details about this question bank..."></textarea>
                </div>
            </div>
            <div class="modal-footer bg-light border-top py-2">
                <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">CANCEL</button>
                <button type="submit" class="btn btn-primary px-4 fw-bold" id="btn-save">
                    <i class="fas fa-save me-2"></i>SAVE GROUP
                </button>
            </div>
        </form>
    </div>
  </div>
</div>

<script>
'use strict';
const HANDLER = '<?php echo BASE_URL; ?>ajax/exam_handler.php';
let modalGroup;

document.addEventListener('DOMContentLoaded', function() {
    modalGroup = new bootstrap.Modal(document.getElementById('modalGroup'));
    const form = document.getElementById('addGroupForm');

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!form.checkValidity()) { form.classList.add('was-validated'); return; }
        
        const btn = document.getElementById('btn-save');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>SAVING...';
        
        fetch(HANDLER, { method: 'POST', body: new FormData(form) })
        .then(r => r.json()).then(res => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-2"></i>SAVE GROUP';
            if(res.success) { location.reload(); }
            else alert(res.message);
        });
    });
});

function openAddGroup() {
    resetForm();
    modalGroup.show();
}

function editGroup(g) {
    resetForm();
    document.getElementById('group-id').value = g.id;
    document.getElementById('group-name').value = g.name;
    document.getElementById('group-desc').value = g.description;
    
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>Edit Question Group';
    modalGroup.show();
}

function resetForm() {
    const form = document.getElementById('addGroupForm');
    form.reset();
    form.classList.remove('was-validated');
    document.getElementById('group-id').value = '';
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-folder-plus me-2"></i>Add Question Group';
}

function deleteGroup(id, name) {
    if(confirm(`Are you sure you want to delete "${name}"?`)) {
        const fd = new FormData();
        fd.append('action', 'delete_question_group');
        fd.append('id', id);
        fetch(HANDLER, { method: 'POST', body: fd })
            .then(r => r.json()).then(res => {
                if(res.success) { location.reload(); }
                else alert(res.message);
            });
    }
}
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>
