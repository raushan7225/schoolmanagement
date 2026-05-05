<?php
// admin/access-control.php
include_once(__DIR__ . "/includes/config.php");

// Defined System Modules & Actions
$modules = [
    'Enquiry Management' => ['view', 'add', 'edit', 'delete'],
    'Partner Management' => ['view', 'add', 'edit', 'delete'],
    'Franchise Management' => ['view', 'add', 'edit', 'delete'],
    'Student Management' => ['view', 'add', 'edit', 'delete'],
    'Course Management' => ['view', 'add', 'edit', 'delete'],
    'Card Management' => ['view', 'add', 'edit', 'delete'],
    'Accounting' => ['view', 'add', 'edit', 'delete'],
    'Exam Management' => ['view', 'add', 'edit', 'delete'],
    'Frontend Management' => ['view', 'add', 'edit', 'delete'],
    'Report Management' => ['view'],
    'Locations' => ['view', 'add', 'edit', 'delete'],
    'Settings' => ['view', 'edit']
];

// Fetch all roles for the dropdown
$roles = $pdo->query("SELECT id, name FROM user_roles ORDER BY level DESC, name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Determine active role
$role_id = isset($_GET['role_id']) ? (int)$_GET['role_id'] : ($roles[0]['id'] ?? 0);
$role_name = "Unknown Role";
foreach ($roles as $r) {
    if ($r['id'] == $role_id) {
        $role_name = $r['name'];
        break;
    }
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_permissions'])) {
    $post_role_id = (int)$_POST['role_id'];
    if ($post_role_id > 0) {
        // Clear old permissions
        $pdo->prepare("DELETE FROM role_permissions WHERE role_id = ?")->execute([$post_role_id]);
        
        // Insert new permissions
        if (isset($_POST['permissions']) && is_array($_POST['permissions'])) {
            $stmt = $pdo->prepare("INSERT INTO role_permissions (role_id, module, action) VALUES (?, ?, ?)");
            foreach ($_POST['permissions'] as $module => $actions) {
                foreach ($actions as $action => $val) {
                    $stmt->execute([$post_role_id, $module, $action]);
                }
            }
        }
        $_SESSION['success'] = "Permissions updated successfully.";
    } else {
        $_SESSION['error'] = "Invalid Role.";
    }
    header("Location: access-control.php?role_id=" . $post_role_id);
    exit;
}

// Fetch existing permissions for the selected role
$existingPerms = [];
if ($role_id > 0) {
    $stmt = $pdo->prepare("SELECT module, action FROM role_permissions WHERE role_id = ?");
    $stmt->execute([$role_id]);
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $existingPerms[$row['module']][] = $row['action'];
    }
}

include(__DIR__ . "/includes/header.php");
?>

<div class="pagetitle">
    <h1>Access Control</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Settings</li>
            <li class="breadcrumb-item active">Access Control</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-lock text-white me-2"></i>
                        <h5 class="card-title text-white mb-0">Permission Matrix: <?php echo $role_name; ?></h5>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select form-select-sm border-0 shadow-sm" onchange="window.location.href='?role_id='+this.value">
                            <?php if(empty($roles)): ?>
                                <option value="">No Roles Found</option>
                            <?php else: ?>
                                <?php foreach($roles as $r): ?>
                                    <option value="<?php echo $r['id']; ?>" <?php echo ($r['id'] == $role_id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($r['name']); ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div class="card-body pt-4">
                    <div class="alert alert-info border-0 shadow-sm d-flex align-items-center">
                        <i class="fas fa-info-circle fs-4 me-3"></i>
                        <div>
                            <strong>Note:</strong> Permissions marked with <i class="fas fa-check-circle text-success mx-1"></i> are currently enabled for this role. Use the toggles below to adjust access levels.
                        </div>
                    </div>

                    <form method="POST" action="access-control.php">
                        <input type="hidden" name="role_id" value="<?php echo $role_id; ?>">
                        <input type="hidden" name="save_permissions" value="1">
                        <div class="table-responsive mt-4">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th class="text-start" width="300">System Modules / Features</th>
                                        <th class="text-center" width="120">VIEW</th>
                                        <th class="text-center" width="120">ADD</th>
                                        <th class="text-center" width="120">EDIT</th>
                                        <th class="text-center" width="120">DELETE</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($role_id > 0): ?>
                                        <?php foreach($modules as $module => $actions): ?>
                                        <tr>
                                            <td class="text-start fw-bold text-dark p-3">
                                                <i class="fas fa-folder-open text-primary me-2 opacity-50"></i> <?php echo htmlspecialchars($module); ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if(in_array('view', $actions)): ?>
                                                    <div class="form-check form-switch d-inline-block">
                                                        <input class="form-check-input" type="checkbox" name="permissions[<?php echo $module; ?>][view]" value="1" <?php echo (isset($existingPerms[$module]) && in_array('view', $existingPerms[$module])) ? 'checked' : ''; ?>>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-muted small">N/A</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if(in_array('add', $actions)): ?>
                                                    <div class="form-check form-switch d-inline-block">
                                                        <input class="form-check-input" type="checkbox" name="permissions[<?php echo $module; ?>][add]" value="1" <?php echo (isset($existingPerms[$module]) && in_array('add', $existingPerms[$module])) ? 'checked' : ''; ?>>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-muted small">N/A</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if(in_array('edit', $actions)): ?>
                                                    <div class="form-check form-switch d-inline-block">
                                                        <input class="form-check-input" type="checkbox" name="permissions[<?php echo $module; ?>][edit]" value="1" <?php echo (isset($existingPerms[$module]) && in_array('edit', $existingPerms[$module])) ? 'checked' : ''; ?>>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-muted small">N/A</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if(in_array('delete', $actions)): ?>
                                                    <div class="form-check form-switch d-inline-block">
                                                        <input class="form-check-input" type="checkbox" name="permissions[<?php echo $module; ?>][delete]" value="1" <?php echo (isset($existingPerms[$module]) && in_array('delete', $existingPerms[$module])) ? 'checked' : ''; ?>>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-muted small">N/A</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">Please define a User Role first to manage permissions.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top py-3 text-end">
                        <button type="submit" class="btn btn-primary px-5 fw-bold" <?php echo ($role_id == 0) ? 'disabled' : ''; ?>>
                            <i class="fas fa-save me-2"></i>SAVE PERMISSIONS
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<style>
.form-check-input:checked {
    background-color: #198754;
    border-color: #198754;
}
.table-bordered th, .table-bordered td {
    border: 1px solid #dee2e6 !important;
}
</style>

<?php include(__DIR__ . "/includes/footer.php"); ?>
