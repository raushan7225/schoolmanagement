<?php
// admin/manage-countries.php
include_once(__DIR__ . "/includes/config.php");

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM countries WHERE id = ?");
    if ($stmt->execute([$id])) {
        $_SESSION['success'] = "Country deleted successfully.";
    } else {
        $_SESSION['error'] = "Failed to delete country. It might be linked to other records.";
    }
    header("Location: manage-countries.php");
    exit;
}

// Handle Add / Edit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name'])) {
    $name = trim($_POST['name']);
    $status = (int)$_POST['status'];
    $edit_id = isset($_POST['edit_id']) ? (int)$_POST['edit_id'] : 0;

    if (!empty($name)) {
        if ($edit_id > 0) {
            $stmt = $pdo->prepare("UPDATE countries SET name = ?, status = ? WHERE id = ?");
            if ($stmt->execute([$name, $status, $edit_id])) {
                $_SESSION['success'] = "Country updated successfully.";
            } else {
                $_SESSION['error'] = "Failed to update country.";
            }
        } else {
            $stmt = $pdo->prepare("INSERT INTO countries (name, status) VALUES (?, ?)");
            if ($stmt->execute([$name, $status])) {
                $_SESSION['success'] = "Country added successfully.";
            } else {
                $_SESSION['error'] = "Failed to add country.";
            }
        }
    } else {
        $_SESSION['error'] = "Country name is required.";
    }
    header("Location: manage-countries.php");
    exit;
}

// Fetch countries
$countries = $pdo->query("SELECT * FROM countries ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

include(__DIR__ . "/includes/header.php");
?>

<div class="pagetitle">
    <h1>Manage Countries</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Locations</li>
            <li class="breadcrumb-item active">Countries</li>
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
    <div class="row">
        <!-- Country List -->
        <div class="col-12">
            <div class="card">
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle datatable-premium"
                               data-add-btn='{"text":"Add New Country","onclick":"openAddCountry()","icon":"fas fa-plus"}'>
                            <thead class="table-light">
                                <tr>
                                    <th width="80">S.No.</th>
                                    <th>Country Name</th>
                                    <th data-no-sort>Status</th>
                                    <th class="text-end" data-no-sort>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; foreach ($countries as $c): ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td><div class="fw-bold text-dark"><?php echo htmlspecialchars($c['name']); ?></div></td>
                                    <td>
                                        <?php if($c['status'] == 1): ?>
                                            <span class="badge bg-success-light text-success border border-success rounded-pill px-3">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger-light text-danger border border-danger rounded-pill px-3">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-warning btn-icon me-1" title="Edit" onclick='editCountry(<?php echo json_encode($c); ?>)'><i class="fas fa-edit"></i></button>
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
<div class="modal fade" id="modalCountry" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="modalTitle"><i class="fas fa-globe-americas me-2"></i>Add New Country</h5>
            <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                <i class="<?php echo v('theme_modal_close_icon', 'fas fa-times'); ?>"></i>
            </button>
        </div>
        <form id="countryForm" method="POST" novalidate>
            <input type="hidden" name="edit_id" id="edit-id" value="">
            <div class="modal-body px-3 py-4">
                <div class="mb-3">
                    <label class="form-label fw-bold">Country Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" id="country-name" placeholder="e.g. India" required>
                    <div class="invalid-feedback">Please enter country name.</div>
                </div>
                <div class="mb-0">
                    <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                    <select class="form-select" name="status" id="country-status" required>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer bg-light border-top py-2">
                <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">CANCEL</button>
                <button type="submit" class="btn btn-primary px-4 fw-bold" id="btn-save">
                    <i class="fas fa-save me-2"></i>SAVE COUNTRY
                </button>
            </div>
        </form>
    </div>
  </div>
</div>

<script>
let modalCountry;
document.addEventListener('DOMContentLoaded', function() {
    modalCountry = new bootstrap.Modal(document.getElementById('modalCountry'));
    
    const form = document.getElementById('countryForm');
    form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
            form.classList.add('was-validated');
        }
    }, false);
});

function openAddCountry() {
    document.getElementById('countryForm').reset();
    document.getElementById('edit-id').value = '';
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-globe-americas me-2"></i>Add New Country';
    document.getElementById('btn-save').innerHTML = '<i class="fas fa-save me-2"></i>SAVE COUNTRY';
    document.getElementById('countryForm').classList.remove('was-validated');
    modalCountry.show();
}

function editCountry(c) {
    document.getElementById('countryForm').reset();
    document.getElementById('edit-id').value = c.id;
    document.getElementById('country-name').value = c.name;
    document.getElementById('country-status').value = c.status;
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>Edit Country';
    document.getElementById('btn-save').innerHTML = '<i class="fas fa-save me-2"></i>UPDATE COUNTRY';
    document.getElementById('countryForm').classList.remove('was-validated');
    modalCountry.show();
}
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>
