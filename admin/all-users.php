<?php
// admin/all-users.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Fetch Stats
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$activeUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE status = 1")->fetchColumn();
$suspendedUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE status = 0")->fetchColumn();
$adminCount = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();

// Search / Filter
$filter_role = $_GET['role'] ?? '';
$filter_status = $_GET['status'] ?? '';

$where = "WHERE 1=1";
$params = [];

if ($filter_role) {
    $where .= " AND u.role = ?";
    $params[] = $filter_role;
}
if ($filter_status !== '') {
    $where .= " AND u.status = ?";
    $params[] = (int)$filter_status;
}

// Fetch Users List
$stmt = $pdo->prepare("
    SELECT u.*, r.name as custom_role_name 
    FROM users u 
    LEFT JOIN user_roles r ON u.role_id = r.id 
    $where
    ORDER BY u.created_at DESC
");
$stmt->execute($params);
$dbUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch custom roles
$customRoles = $pdo->query("SELECT id, name FROM user_roles ORDER BY level DESC, name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>User Management</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Settings</li>
            <li class="breadcrumb-item active">All Users</li>
        </ol>
    </nav>
</div>

<section class="section">
    <!-- User Stats Dashboard -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-none border-start border-primary border-4 mb-0">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small fw-bold">TOTAL USERS</div>
                            <div class="h4 mb-0 fw-bold text-dark"><?php echo $totalUsers; ?></div>
                        </div>
                        <div class="bg-primary-light p-2 rounded text-primary"><i class="fas fa-users fa-lg"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-none border-start border-success border-4 mb-0">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small fw-bold">ACTIVE NOW</div>
                            <div class="h4 mb-0 fw-bold text-success"><?php echo $activeUsers; ?></div>
                        </div>
                        <div class="bg-success-light p-2 rounded text-success pulsate"><i class="fas fa-circle fa-sm"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-none border-start border-danger border-4 mb-0">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small fw-bold">SUSPENDED</div>
                            <div class="h4 mb-0 fw-bold text-danger"><?php echo $suspendedUsers; ?></div>
                        </div>
                        <div class="bg-danger-light p-2 rounded text-danger"><i class="fas fa-user-slash fa-lg"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-none border-start border-warning border-4 mb-0">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small fw-bold">ADMINISTRATORS</div>
                            <div class="h4 mb-0 fw-bold text-warning"><?php echo $adminCount; ?></div>
                        </div>
                        <div class="bg-warning-light p-2 rounded text-warning"><i class="fas fa-user-shield fa-lg"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Filter Bar -->
        <div class="col-12 mb-3">
            <div class="card">
                <div class="card-body py-2">
                    <form class="row g-2 align-items-center" method="GET">
                        <div class="col-md-5">
                            <select name="role" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">All Roles...</option>
                                <option value="admin" <?php echo $filter_role == 'admin' ? 'selected' : ''; ?>>Super Admin</option>
                                <option value="staff" <?php echo $filter_role == 'staff' ? 'selected' : ''; ?>>Staff Member</option>
                                <option value="franchise" <?php echo $filter_role == 'franchise' ? 'selected' : ''; ?>>Franchise User</option>
                                <option value="partner" <?php echo $filter_role == 'partner' ? 'selected' : ''; ?>>Partner User</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">All Status...</option>
                                <option value="1" <?php echo $filter_status === '1' ? 'selected' : ''; ?>>✅ Active</option>
                                <option value="0" <?php echo $filter_status === '0' ? 'selected' : ''; ?>>🔴 Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-sm btn-primary flex-grow-1 fw-bold"><i class="fas fa-filter me-2"></i>APPLY</button>
                            <a href="all-users.php" class="btn btn-sm btn-outline-secondary fw-bold">RESET</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- User List Card -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle datatable-premium"
                               data-add-btn='{"text":"Add New User","onclick":"openAddUser()","icon":"fas fa-user-plus"}'>
                            <thead class="table-light">
                                <tr>
                                    <th width="60">S.No.</th>
                                    <th>User Info</th>
                                    <th>Role</th>
                                    <th>Activity Pulse</th>
                                    <th>Security Score</th>
                                    <th>Status</th>
                                    <th class="text-end" data-no-sort>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $sn=1; foreach($dbUsers as $user): ?>
                                <tr id="user-row-<?php echo $user['id']; ?>">
                                    <td><?php echo $sn++; ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm rounded-circle border me-3 d-flex align-items-center justify-content-center bg-light text-primary fw-bold" style="width:40px; height:40px;">
                                                <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark"><?php echo htmlspecialchars($user['username']); ?></div>
                                                <div class="small text-muted"><?php echo htmlspecialchars($user['email']); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php 
                                            $roleLabels = [
                                                'franchise' => 'Center Manager',
                                                'student' => 'Student',
                                                'partner' => 'Regional Partner'
                                            ];
                                            $roleClass = 'secondary';
                                            if ($user['role'] == 'admin') {
                                                $roleClass = 'danger';
                                                $label = $user['custom_role_name'] ? htmlspecialchars($user['custom_role_name']) : 'Super Admin';
                                            } else {
                                                $roleClass = ($user['role'] == 'franchise') ? 'primary' : 'success';
                                                $label = $roleLabels[$user['role']] ?? ucfirst($user['role']);
                                            }
                                        ?>
                                        <span class="badge bg-<?php echo $roleClass; ?>-light text-<?php echo $roleClass; ?> rounded-pill px-3">
                                            <?php echo $label; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="status-pulse <?php echo ($user['status'] == 1) ? 'bg-success' : 'bg-secondary'; ?> me-2"></div>
                                            <span class="small text-muted"><?php echo date('d M Y', strtotime($user['created_at'])); ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small text-muted">Created On</div>
                                        <div class="fw-bold small"><?php echo date('H:i', strtotime($user['created_at'])); ?></div>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input status-toggle" type="checkbox" data-id="<?php echo $user['id']; ?>" <?php echo ($user['status'] == 1) ? 'checked' : ''; ?>>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <?php 
                                            // Determine role_id for the permissions link
                                            $link_role_id = $user['role_id'];
                                            if (!$link_role_id) {
                                                foreach($customRoles as $cr) {
                                                    if(strtolower($cr['name']) == strtolower($user['role'])) {
                                                        $link_role_id = $cr['id'];
                                                        break;
                                                    }
                                                }
                                            }
                                        ?>
                                        <button class="btn btn-sm btn-outline-primary btn-icon me-1" title="Security Logs" onclick="alert('Security logs feature is under development.')"><i class="fas fa-shield-halved"></i></button>
                                        <a href="access-control.php?role_id=<?php echo $link_role_id; ?>" class="btn btn-sm btn-outline-warning btn-icon me-1" title="Edit Permissions"><i class="fas fa-key"></i></a>
                                        <button class="btn btn-sm btn-outline-danger btn-icon" title="Delete Account" onclick="deleteUser(<?php echo $user['id']; ?>, '<?php echo addslashes($user['username']); ?>')"><i class="fas fa-trash"></i></button>
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

<!-- Add User Modal (Standardized) -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Create New User Account</h5>
                <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                    <i class="<?php echo v('theme_modal_close_icon', 'fas fa-times'); ?>"></i>
                </button>
            </div>
            <form id="addUserForm" class="was-validated">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Full Name (Username) <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control" required placeholder="John Doe">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required placeholder="john@school.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Initial Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" required minlength="8">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Assign Role <span class="text-danger">*</span></label>
                            <select name="role" class="form-select" required>
                                <option value="">Select Role...</option>
                                <optgroup label="System Roles">
                                    <option value="admin">Super Admin</option>
                                    <option value="franchise">Center Manager</option>
                                    <option value="student">Student</option>
                                    <option value="partner">Regional Partner</option>
                                </optgroup>
                                <?php if(count($customRoles) > 0): ?>
                                <optgroup label="Custom Admin Roles">
                                    <?php foreach($customRoles as $cr): ?>
                                        <option value="admin_<?php echo $cr['id']; ?>"><?php echo htmlspecialchars($cr['name']); ?></option>
                                    <?php endforeach; ?>
                                </optgroup>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <div class="alert alert-info border-0 shadow-sm small mb-0">
                                <i class="fas fa-info-circle me-2"></i> An invitation email will be sent to the user to verify their account.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">CANCEL</button>
                    <button type="submit" class="btn btn-primary px-5 fw-bold">CREATE ACCOUNT</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.status-pulse {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    display: inline-block;
}
.pulsate {
    animation: pulse-animation 2s infinite;
}
@keyframes pulse-animation {
    0% { opacity: 1; }
    50% { opacity: 0.3; }
    100% { opacity: 1; }
}
.avatar-sm:hover {
    transform: scale(1.1);
    transition: transform 0.2s;
    cursor: pointer;
}
</style>

<?php include(__DIR__ . "/includes/footer.php"); ?>

<script>
const USER_HANDLER = '<?php echo BASE_URL; ?>ajax/user_handler.php';

// --- Toggle User Status ---
document.querySelectorAll('.status-toggle').forEach(el => {
    el.addEventListener('change', function() {
        const id = this.dataset.id;
        const status = this.checked ? 1 : 0;
        const fd = new FormData();
        fd.append('action', 'toggle_status');
        fd.append('id', id);
        fd.append('status', status);

        fetch(USER_HANDLER, { method: 'POST', body: fd })
            .then(r => r.json())
            .then(res => {
                if(!res.success) {
                    alert(res.message);
                    this.checked = !this.checked;
                }
            });
    });
});

// --- Delete User ---
function deleteUser(id, name) {
    window.confirmDelete({
        target: name,
        onConfirm: function(modal, btn) {
            const fd = new FormData();
            fd.append('action', 'delete');
            fd.append('id', id);

            fetch(USER_HANDLER, { method: 'POST', body: fd })
                .then(r => r.json())
                .then(res => {
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

function openAddUser() {
    bootstrap.Modal.getOrCreateInstance(document.getElementById('addUserModal')).show();
}

// --- Add New User ---
document.getElementById('addUserForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = this.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.innerHTML = 'CREATING...';

    const fd = new FormData(this);
    fd.append('action', 'add');

    fetch(USER_HANDLER, { method: 'POST', body: fd })
        .then(r => r.json())
        .then(res => {
            btn.disabled = false;
            btn.innerHTML = 'CREATE ACCOUNT';
            if(res.success) {
                location.reload();
            } else {
                alert(res.message);
            }
        });
});
</script>
