<?php
// admin/manage-cities.php
include_once(__DIR__ . "/includes/config.php");

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM cities WHERE id = ?");
    if ($stmt->execute([$id])) {
        $_SESSION['success'] = "City deleted successfully.";
    } else {
        $_SESSION['error'] = "Failed to delete city. It might be linked to other records.";
    }
    header("Location: manage-cities.php");
    exit;
}

// Handle Add / Edit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name'])) {
    $name = trim($_POST['name']);
    $district_id = (int)$_POST['district_id'];
    $status = (int)$_POST['status'];
    $edit_id = isset($_POST['edit_id']) ? (int)$_POST['edit_id'] : 0;

    if (!empty($name) && $district_id > 0) {
        if ($edit_id > 0) {
            $stmt = $pdo->prepare("UPDATE cities SET district_id = ?, name = ?, status = ? WHERE id = ?");
            if ($stmt->execute([$district_id, $name, $status, $edit_id])) {
                $_SESSION['success'] = "City updated successfully.";
            } else {
                $_SESSION['error'] = "Failed to update city.";
            }
        } else {
            $stmt = $pdo->prepare("INSERT INTO cities (district_id, name, status) VALUES (?, ?, ?)");
            if ($stmt->execute([$district_id, $name, $status])) {
                $_SESSION['success'] = "City added successfully.";
            } else {
                $_SESSION['error'] = "Failed to add city.";
            }
        }
    } else {
        $_SESSION['error'] = "City name and District are required.";
    }
    header("Location: manage-cities.php");
    exit;
}

// Fetch cities and districts
$filter_district = (int)($_GET['district_id'] ?? 0);
$where = "";
$params = [];

if ($filter_district) {
    $where = "WHERE c.district_id = ?";
    $params[] = $filter_district;
}

$stmt = $pdo->prepare("SELECT c.*, d.name as district_name FROM cities c LEFT JOIN districts d ON c.district_id = d.id $where ORDER BY c.name ASC");
$stmt->execute($params);
$cities = $stmt->fetchAll(PDO::FETCH_ASSOC);

$districts = $pdo->query("SELECT id, name FROM districts WHERE status = 1 ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

include(__DIR__ . "/includes/header.php");
?>

<div class="pagetitle">
    <h1>Manage Cities</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Locations</li>
            <li class="breadcrumb-item active">Cities</li>
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
                    <select name="district_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Districts...</option>
                        <?php foreach($districts as $d): ?>
                            <option value="<?php echo $d['id']; ?>" <?php echo $filter_district == $d['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($d['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary flex-grow-1 fw-bold"><i class="fas fa-filter me-2"></i>APPLY</button>
                    <a href="manage-cities.php" class="btn btn-sm btn-outline-secondary fw-bold">RESET</a>
                </div>
            </form>
        </div>
    </div>

        <!-- City List -->
        <div class="col-12">
            <div class="card">
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle datatable-premium"
                               data-add-btn='{"text":"Add New City","onclick":"openAddCity()","icon":"fas fa-plus"}'>
                            <thead class="table-light">
                                <tr>
                                    <th width="80">S.No.</th>
                                    <th>City Name</th>
                                    <th>District</th>
                                    <th data-no-sort>Status</th>
                                    <th class="text-end" data-no-sort>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; foreach ($cities as $c): ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td><div class="fw-bold text-dark"><?php echo htmlspecialchars($c['name']); ?></div></td>
                                    <td><span class="badge bg-primary-light text-primary rounded-pill px-3"><?php echo htmlspecialchars($c['district_name']); ?></span></td>
                                    <td>
                                        <?php if($c['status'] == 1): ?>
                                            <span class="badge bg-success-light text-success border border-success rounded-pill px-3">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger-light text-danger border border-danger rounded-pill px-3">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-warning btn-icon me-1" title="Edit" onclick='editCity(<?php echo json_encode($c); ?>)'><i class="fas fa-edit"></i></button>
                                        <a href="?delete=<?php echo $c['id']; ?>" class="btn btn-sm btn-outline-danger btn-icon btn-delete" title="Delete" data-name="<?php echo addslashes($c['name']); ?>"><i class="fas fa-trash"></i></a>
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
<div class="modal fade" id="modalCity" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="modalTitle"><i class="fas fa-city me-2"></i>Add New City</h5>
            <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                <i class="<?php echo v('theme_modal_close_icon', 'fas fa-times'); ?>"></i>
            </button>
        </div>
        <form id="cityForm" method="POST" novalidate>
            <input type="hidden" name="edit_id" id="edit-id" value="">
            <div class="modal-body px-3 py-4">
                <div class="mb-3">
                    <label class="form-label fw-bold">Select District <span class="text-danger">*</span></label>
                    <select class="form-select" name="district_id" id="district-id" required>
                        <option value="">Select District...</option>
                        <?php foreach($districts as $d): ?>
                            <option value="<?php echo $d['id']; ?>"><?php echo htmlspecialchars($d['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">Please select a district.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">City Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" id="city-name" placeholder="e.g. Pune City" required>
                    <div class="invalid-feedback">Please enter city name.</div>
                </div>
                <div class="mb-0">
                    <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                    <select class="form-select" name="status" id="city-status" required>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer bg-light border-top py-2">
                <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">CANCEL</button>
                <button type="submit" class="btn btn-primary px-4 fw-bold" id="btn-save">
                    <i class="fas fa-save me-2"></i>SAVE CITY
                </button>
            </div>
        </form>
    </div>
  </div>
</div>

<script>
let modalCity;
document.addEventListener('DOMContentLoaded', function() {
    modalCity = new bootstrap.Modal(document.getElementById('modalCity'));
    
    const form = document.getElementById('cityForm');
    form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
            form.classList.add('was-validated');
        }
    }, false);
});

function openAddCity() {
    document.getElementById('cityForm').reset();
    document.getElementById('edit-id').value = '';
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-city me-2"></i>Add New City';
    document.getElementById('btn-save').innerHTML = '<i class="fas fa-save me-2"></i>SAVE CITY';
    document.getElementById('cityForm').classList.remove('was-validated');
    modalCity.show();
}

function editCity(c) {
    document.getElementById('cityForm').reset();
    document.getElementById('edit-id').value = c.id;
    document.getElementById('district-id').value = c.district_id;
    document.getElementById('city-name').value = c.name;
    document.getElementById('city-status').value = c.status;
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>Edit City';
    document.getElementById('btn-save').innerHTML = '<i class="fas fa-save me-2"></i>UPDATE CITY';
    document.getElementById('cityForm').classList.remove('was-validated');
    modalCity.show();
}
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>
