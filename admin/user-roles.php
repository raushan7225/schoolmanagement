<?php
// admin/user-roles.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Fetch all roles with user count and permission count
$roles = $pdo->query("
    SELECT 
        r.*,
        (SELECT COUNT(*) FROM users WHERE role_id = r.id) as users_count,
        (SELECT COUNT(*) FROM role_permissions WHERE role_id = r.id) as perm_count
    FROM user_roles r
    ORDER BY r.level DESC, r.name ASC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>Role Management</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Settings</li>
            <li class="breadcrumb-item active">User Roles</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <!-- Role List Card -->
        <div class="col-12">
            <div class="card">
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle datatable-premium"
                               data-add-btn='{"text":"Create New Role","onclick":"openAddRole()","icon":"fas fa-shield-halved"}'>
                            <thead class="table-light">
                                <tr>
                                    <th width="80">Level</th>
                                    <th>Role Name</th>
                                    <th>Description</th>
                                    <th class="text-center" data-no-sort>Permissions</th>
                                    <th class="text-center" data-no-sort>Active Users</th>
                                    <th class="text-end" data-no-sort>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($roles as $r): ?>
                                <tr id="rolerow-<?php echo $r['id']; ?>">
                                    <td>
                                        <div class="badge bg-dark rounded-pill px-3">LVL <?php echo $r['level']; ?></div>
                                    </td>
                                    <td><div class="fw-bold text-dark fs-6"><?php echo htmlspecialchars($r['name']); ?></div></td>
                                    <td><small class="text-muted"><?php echo htmlspecialchars($r['description']); ?></small></td>
                                    <td class="text-center">
                                        <a href="access-control.php?role_id=<?php echo $r['id']; ?>" class="badge bg-primary-light text-primary border border-primary text-decoration-none px-3">
                                            <i class="fas fa-lock me-1"></i> <?php echo $r['perm_count']; ?> Rules
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info-light text-info border border-info rounded-pill px-3"><?php echo $r['users_count']; ?> Users</span>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-warning btn-icon me-1" title="Edit Role" onclick='editRole(<?php echo json_encode($r); ?>)'>
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger btn-icon" title="Delete Role" onclick="deleteRole(<?php echo $r['id']; ?>, '<?php echo addslashes($r['name']); ?>')">
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
<div class="modal fade" id="modalRole" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="modalTitle"><i class="fas fa-shield-halved me-2"></i>Create New Role</h5>
            <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="roleForm" novalidate>
            <input type="hidden" name="action" value="save_role">
            <input type="hidden" name="id" id="role-id" value="">
            <div class="modal-body px-3 py-4">
                <div class="mb-3">
                    <label class="form-label fw-bold">Role Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" id="role-name" placeholder="e.g. Academic Manager" required>
                    <div class="invalid-feedback">Please enter a role name.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Hierarchy Level <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="level" id="role-level" value="1" min="1" max="99" required title="Higher level has more priority">
                    <div class="form-text">Higher numbers indicate higher authority.</div>
                </div>
                <div class="mb-0">
                    <label class="form-label fw-bold">Description</label>
                    <textarea class="form-control" name="description" id="role-desc" rows="3" placeholder="Brief description of responsibilities..."></textarea>
                </div>
            </div>
            <div class="modal-footer bg-light border-top py-2">
                <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">CANCEL</button>
                <button type="submit" class="btn btn-primary px-4 fw-bold" id="btn-save">
                    <i class="fas fa-save me-2"></i>SAVE ROLE
                </button>
            </div>
        </form>
    </div>
  </div>
</div>

<script>
'use strict';
const HANDLER = '<?php echo BASE_URL; ?>ajax/user_handler.php';
let modalRole;

document.addEventListener('DOMContentLoaded', function() {
    modalRole = new bootstrap.Modal(document.getElementById('modalRole'));
    const form = document.getElementById('roleForm');

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!form.checkValidity()) { form.classList.add('was-validated'); return; }
        
        const btn = document.getElementById('btn-save');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>SAVING...';
        
        fetch(HANDLER, { method: 'POST', body: new FormData(form) })
        .then(r => r.json()).then(res => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-2"></i>SAVE ROLE';
            if(res.success) { location.reload(); }
            else alert(res.message);
        });
    });
});

function openAddRole() {
    resetRoleForm();
    modalRole.show();
}

function editRole(r) {
    resetRoleForm();
    document.getElementById('role-id').value = r.id;
    document.getElementById('role-name').value = r.name;
    document.getElementById('role-level').value = r.level;
    document.getElementById('role-desc').value = r.description;
    
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>Edit User Role';
    modalRole.show();
}

function resetRoleForm() {
    const form = document.getElementById('roleForm');
    form.reset();
    form.classList.remove('was-validated');
    document.getElementById('role-id').value = '';
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-shield-halved me-2"></i>Create New Role';
}

function deleteRole(id, name) {
    if(confirm(`Are you sure you want to delete the "${name}" role? This cannot be undone.`)) {
        const fd = new FormData();
        fd.append('action', 'delete_role');
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
