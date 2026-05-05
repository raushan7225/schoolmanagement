<?php
// admin/manage-districts.php
include_once(__DIR__ . "/includes/config.php");

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM districts WHERE id = ?");
    if ($stmt->execute([$id])) {
        $_SESSION['success'] = "District deleted successfully.";
    } else {
        $_SESSION['error'] = "Failed to delete district. It might be linked to other records.";
    }
    header("Location: manage-districts.php");
    exit;
}

// Handle Add / Edit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name'])) {
    $name = trim($_POST['name']);
    $state_id = (int)$_POST['state_id'];
    $status = (int)$_POST['status'];
    $edit_id = isset($_POST['edit_id']) ? (int)$_POST['edit_id'] : 0;

    if (!empty($name) && $state_id > 0) {
        if ($edit_id > 0) {
            $stmt = $pdo->prepare("UPDATE districts SET state_id = ?, name = ?, status = ? WHERE id = ?");
            if ($stmt->execute([$state_id, $name, $status, $edit_id])) {
                $_SESSION['success'] = "District updated successfully.";
            } else {
                $_SESSION['error'] = "Failed to update district.";
            }
        } else {
            $stmt = $pdo->prepare("INSERT INTO districts (state_id, name, status) VALUES (?, ?, ?)");
            if ($stmt->execute([$state_id, $name, $status])) {
                $_SESSION['success'] = "District added successfully.";
            } else {
                $_SESSION['error'] = "Failed to add district.";
            }
        }
    } else {
        $_SESSION['error'] = "District name and State are required.";
    }
    header("Location: manage-districts.php");
    exit;
}

// Fetch districts and states
$filter_state = (int)($_GET['state_id'] ?? 0);
$where = "";
$params = [];

if ($filter_state) {
    $where = "WHERE d.state_id = ?";
    $params[] = $filter_state;
}

$stmt = $pdo->prepare("SELECT d.*, s.name as state_name FROM districts d LEFT JOIN states s ON d.state_id = s.id $where ORDER BY d.name ASC");
$stmt->execute($params);
$districts = $stmt->fetchAll(PDO::FETCH_ASSOC);

$states = $pdo->query("SELECT id, name FROM states WHERE status = 1 ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

include(__DIR__ . "/includes/header.php");
?>

<div class="pagetitle">
    <h1>Manage Districts</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Locations</li>
            <li class="breadcrumb-item active">Districts</li>
        </ol>
    </nav>
</div>

<?php if(isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
        <i class="fas fa-check-circle me-2"></i><strong>Success!</strong> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if(isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i><strong>Error!</strong> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<section class="section">
    <!-- Filter Bar -->
    <div class="card mb-3">
        <div class="card-body py-2">
            <form class="row g-2 align-items-center" method="GET">
                <div class="col-md-9">
                    <select name="state_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All States...</option>
                        <?php foreach($states as $s): ?>
                            <option value="<?php echo $s['id']; ?>" <?php echo $filter_state == $s['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($s['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary flex-grow-1 fw-bold"><i class="fas fa-filter me-2"></i>APPLY</button>
                    <a href="manage-districts.php" class="btn btn-sm btn-outline-secondary fw-bold">RESET</a>
                </div>
            </form>
        </div>
    </div>

        <!-- District List -->
        <div class="col-12">
            <div class="card">
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle datatable-premium"
                               data-add-btn='{"text":"Add New District","onclick":"openAddDistrict()","icon":"fas fa-plus"}'>
                            <thead class="table-light">
                                <tr>
                                    <th width="80">S.No.</th>
                                    <th>District Name</th>
                                    <th>State</th>
                                    <th data-no-sort>Status</th>
                                    <th class="text-end" data-no-sort>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; foreach ($districts as $d): ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td><div class="fw-bold text-dark"><?php echo htmlspecialchars($d['name']); ?></div></td>
                                    <td><span class="badge bg-primary-light text-primary rounded-pill px-3"><?php echo htmlspecialchars($d['state_name']); ?></span></td>
                                    <td>
                                        <?php if($d['status'] == 1): ?>
                                            <span class="badge bg-success-light text-success border border-success rounded-pill px-3">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger-light text-danger border border-danger rounded-pill px-3">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-warning btn-icon me-1" title="Edit" onclick='editDistrict(<?php echo json_encode($d); ?>)'><i class="fas fa-edit"></i></button>
                                        <a href="?delete=<?php echo $d['id']; ?>" class="btn btn-sm btn-outline-danger btn-icon btn-delete" title="Delete" data-name="<?php echo addslashes($d['name']); ?>"><i class="fas fa-trash"></i></a>
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
<div class="modal fade" id="modalDistrict" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="modalTitle"><i class="fas fa-map-location me-2"></i>Add New District</h5>
            <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                <i class="<?php echo v('theme_modal_close_icon', 'fas fa-times'); ?>"></i>
            </button>
        </div>
        <form id="districtForm" method="POST" novalidate>
            <input type="hidden" name="edit_id" id="edit-id" value="">
            <div class="modal-body px-3 py-4">
                <div class="mb-3">
                    <label class="form-label fw-bold">Select State <span class="text-danger">*</span></label>
                    <select class="form-select" name="state_id" id="state-id" required>
                        <option value="">Select State...</option>
                        <?php foreach($states as $s): ?>
                            <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">Please select a state.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">District Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" id="district-name" placeholder="e.g. Pune" required>
                    <div class="invalid-feedback">Please enter district name.</div>
                </div>
                <div class="mb-0">
                    <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                    <select class="form-select" name="status" id="district-status" required>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer bg-light border-top py-2">
                <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">CANCEL</button>
                <button type="submit" class="btn btn-primary px-4 fw-bold" id="btn-save">
                    <i class="fas fa-save me-2"></i>SAVE DISTRICT
                </button>
            </div>
        </form>
    </div>
  </div>
</div>

<script>
let modalDistrict;
document.addEventListener('DOMContentLoaded', function() {
    modalDistrict = new bootstrap.Modal(document.getElementById('modalDistrict'));
    
    const form = document.getElementById('districtForm');
    form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
            form.classList.add('was-validated');
        }
    }, false);
});

function openAddDistrict() {
    document.getElementById('districtForm').reset();
    document.getElementById('edit-id').value = '';
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-map-location me-2"></i>Add New District';
    document.getElementById('btn-save').innerHTML = '<i class="fas fa-save me-2"></i>SAVE DISTRICT';
    document.getElementById('districtForm').classList.remove('was-validated');
    modalDistrict.show();
}

function editDistrict(d) {
    document.getElementById('districtForm').reset();
    document.getElementById('edit-id').value = d.id;
    document.getElementById('state-id').value = d.state_id;
    document.getElementById('district-name').value = d.name;
    document.getElementById('district-status').value = d.status;
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>Edit District';
    document.getElementById('btn-save').innerHTML = '<i class="fas fa-save me-2"></i>UPDATE DISTRICT';
    document.getElementById('districtForm').classList.remove('was-validated');
    modalDistrict.show();
}
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>
