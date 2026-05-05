<?php
// admin/manage-states.php
include_once(__DIR__ . "/includes/config.php");

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM states WHERE id = ?");
    if ($stmt->execute([$id])) {
        $_SESSION['success'] = "State deleted successfully.";
    } else {
        $_SESSION['error'] = "Failed to delete state. It might be linked to other records.";
    }
    header("Location: manage-states.php");
    exit;
}

// Handle Add / Edit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name'])) {
    $name = trim($_POST['name']);
    $country_id = (int)$_POST['country_id'];
    $status = (int)$_POST['status'];
    $edit_id = isset($_POST['edit_id']) ? (int)$_POST['edit_id'] : 0;

    if (!empty($name) && $country_id > 0) {
        if ($edit_id > 0) {
            $stmt = $pdo->prepare("UPDATE states SET country_id = ?, name = ?, status = ? WHERE id = ?");
            if ($stmt->execute([$country_id, $name, $status, $edit_id])) {
                $_SESSION['success'] = "State updated successfully.";
            } else {
                $_SESSION['error'] = "Failed to update state.";
            }
        } else {
            $stmt = $pdo->prepare("INSERT INTO states (country_id, name, status) VALUES (?, ?, ?)");
            if ($stmt->execute([$country_id, $name, $status])) {
                $_SESSION['success'] = "State added successfully.";
            } else {
                $_SESSION['error'] = "Failed to add state.";
            }
        }
    } else {
        $_SESSION['error'] = "State name and Country are required.";
    }
    header("Location: manage-states.php");
    exit;
}

// Fetch states and countries
$filter_country = (int)($_GET['country_id'] ?? 0);
$where = "";
$params = [];

if ($filter_country) {
    $where = "WHERE s.country_id = ?";
    $params[] = $filter_country;
}

$stmt = $pdo->prepare("SELECT s.*, c.name as country_name FROM states s LEFT JOIN countries c ON s.country_id = c.id $where ORDER BY s.name ASC");
$stmt->execute($params);
$states = $stmt->fetchAll(PDO::FETCH_ASSOC);

$countries = $pdo->query("SELECT id, name FROM countries WHERE status = 1 ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

include(__DIR__ . "/includes/header.php");
?>

<div class="pagetitle">
    <h1>Manage States</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Locations</li>
            <li class="breadcrumb-item active">States</li>
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
                    <select name="country_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Countries...</option>
                        <?php foreach($countries as $c): ?>
                            <option value="<?php echo $c['id']; ?>" <?php echo $filter_country == $c['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary flex-grow-1 fw-bold"><i class="fas fa-filter me-2"></i>APPLY</button>
                    <a href="manage-states.php" class="btn btn-sm btn-outline-secondary fw-bold">RESET</a>
                </div>
            </form>
        </div>
    </div>

        <!-- State List -->
        <div class="col-12">
            <div class="card">
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle datatable-premium"
                               data-add-btn='{"text":"Add New State","onclick":"openAddState()","icon":"fas fa-plus"}'>
                            <thead class="table-light">
                                <tr>
                                    <th width="80">S.No.</th>
                                    <th>State Name</th>
                                    <th>Country</th>
                                    <th data-no-sort>Status</th>
                                    <th class="text-end" data-no-sort>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; foreach ($states as $s): ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td><div class="fw-bold text-dark"><?php echo htmlspecialchars($s['name']); ?></div></td>
                                    <td><span class="badge bg-primary-light text-primary rounded-pill px-3"><?php echo htmlspecialchars($s['country_name']); ?></span></td>
                                    <td>
                                        <?php if($s['status'] == 1): ?>
                                            <span class="badge bg-success-light text-success border border-success rounded-pill px-3">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger-light text-danger border border-danger rounded-pill px-3">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-warning btn-icon me-1" title="Edit" onclick='editState(<?php echo json_encode($s); ?>)'><i class="fas fa-edit"></i></button>
                                        <a href="?delete=<?php echo $s['id']; ?>" class="btn btn-sm btn-outline-danger btn-icon btn-delete" title="Delete" data-name="<?php echo addslashes($s['name']); ?>"><i class="fas fa-trash"></i></a>
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
<div class="modal fade" id="modalState" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="modalTitle"><i class="fas fa-map-marked-alt me-2"></i>Add New State</h5>
            <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                <i class="<?php echo v('theme_modal_close_icon', 'fas fa-times'); ?>"></i>
            </button>
        </div>
        <form id="stateForm" method="POST" novalidate>
            <input type="hidden" name="edit_id" id="edit-id" value="">
            <div class="modal-body px-3 py-4">
                <div class="mb-3">
                    <label class="form-label fw-bold">Select Country <span class="text-danger">*</span></label>
                    <select class="form-select" name="country_id" id="country-id" required>
                        <option value="">Select Country...</option>
                        <?php foreach($countries as $c): ?>
                            <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">Please select a country.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">State Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" id="state-name" placeholder="e.g. Maharashtra" required>
                    <div class="invalid-feedback">Please enter state name.</div>
                </div>
                <div class="mb-0">
                    <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                    <select class="form-select" name="status" id="state-status" required>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer bg-light border-top py-2">
                <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">CANCEL</button>
                <button type="submit" class="btn btn-primary px-4 fw-bold" id="btn-save">
                    <i class="fas fa-save me-2"></i>SAVE STATE
                </button>
            </div>
        </form>
    </div>
  </div>
</div>

<script>
let modalState;
document.addEventListener('DOMContentLoaded', function() {
    modalState = new bootstrap.Modal(document.getElementById('modalState'));
    
    const form = document.getElementById('stateForm');
    form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
            form.classList.add('was-validated');
        }
    }, false);
});

function openAddState() {
    document.getElementById('stateForm').reset();
    document.getElementById('edit-id').value = '';
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-map-marked-alt me-2"></i>Add New State';
    document.getElementById('btn-save').innerHTML = '<i class="fas fa-save me-2"></i>SAVE STATE';
    document.getElementById('stateForm').classList.remove('was-validated');
    modalState.show();
}

function editState(s) {
    document.getElementById('stateForm').reset();
    document.getElementById('edit-id').value = s.id;
    document.getElementById('country-id').value = s.country_id;
    document.getElementById('state-name').value = s.name;
    document.getElementById('state-status').value = s.status;
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>Edit State';
    document.getElementById('btn-save').innerHTML = '<i class="fas fa-save me-2"></i>UPDATE STATE';
    document.getElementById('stateForm').classList.remove('was-validated');
    modalState.show();
}
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>
