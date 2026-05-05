<?php
// admin/course-category.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Fetch existing categories
$stmt = $pdo->query("SELECT * FROM course_categories ORDER BY name ASC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>Course Category</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Course Management</li>
            <li class="breadcrumb-item active">Category</li>
        </ol>
    </nav>
</div>

<section class="section">
<div class="row">

    <!-- ══ Right: Category List ════════════════════════════════ -->
    <div class="col-12">
        <div class="card">
            <div class="card-body pt-3">
                <div class="table-responsive">
                    <table class="table table-hover align-middle datatable-premium"
                           data-add-btn='{"text":"Add New Category","onclick":"openAddCategory()","icon":"fas fa-plus"}'>
                        <thead class="table-light">
                            <tr>
                                <th>S.No.</th>
                                <th>Category Name</th>
                                <th>Franchise Fee</th>
                                <th data-no-sort>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($categories)): $sn=1; foreach($categories as $cat): ?>
                            <tr id="catrow-<?php echo $cat['id']; ?>">
                                <td><?php echo $sn++; ?></td>
                                <td><strong><?php echo htmlspecialchars($cat['name']); ?></strong></td>
                                <td><span class="fw-bold text-success">&#8377; <?php echo number_format($cat['franchise_fee'], 2); ?></span></td>
                                <td>
                                    <span class="badge bg-<?php echo $cat['status'] == 1 ? 'success' : 'danger'; ?> rounded-pill">
                                        <?php echo $cat['status'] == 1 ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-info btn-icon me-1" title="View" onclick="viewCategory(<?php echo $cat['id']; ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-warning btn-icon me-1" title="Edit" onclick='editCategory(<?php echo json_encode($cat); ?>)'>
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger btn-icon" title="Delete" onclick="deleteCategory(<?php echo $cat['id']; ?>, '<?php echo addslashes($cat['name']); ?>')">
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

</div><!-- /.row -->

<!-- Add/Edit Modal -->
<div class="modal fade" id="modalCat" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="catModalTitle"><i class="fas fa-tag me-2"></i>Add New Category</h5>
            <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                <i class="<?php echo v('theme_modal_close_icon', 'fas fa-times'); ?>"></i>
            </button>
        </div>
        <form id="catForm" novalidate>
            <input type="hidden" name="action" id="cat-action" value="add_category">
            <input type="hidden" name="id" id="cat-id" value="">
            <div class="modal-body px-3 py-4">
                <div class="mb-3">
                    <label class="form-label fw-bold">Category Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" id="cat-name" placeholder="e.g. Vocational" required>
                    <div class="invalid-feedback">Category name is required.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Franchise Fee (&#8377;)</label>
                    <input type="number" step="0.01" class="form-control" name="franchise_fee" id="cat-fee" placeholder="0.00" min="0">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Status</label>
                    <select class="form-select" name="status" id="cat-status">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary px-4" id="btn-save-cat">
                    <i class="fas fa-save me-1"></i>Save Category
                </button>
            </div>
        </form>
    </div>
  </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="modalViewCat" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title"><i class="fas fa-eye me-2"></i>Category Details</h5>
            <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                <i class="<?php echo v('theme_modal_close_icon', 'fas fa-times'); ?>"></i>
            </button>
        </div>
        <div class="modal-body px-3 py-2" id="view-cat-body">
            <div class="text-center py-5"><div class="spinner-border text-primary"></div></div>
        </div>
    </div>
  </div>
</div>

</section>

<script>
const HANDLER = '<?php echo BASE_URL; ?>ajax/course_handler.php';
let modalCat, form;

document.addEventListener('DOMContentLoaded', function() {
    modalCat = new bootstrap.Modal(document.getElementById('modalCat'));
    form = document.getElementById('catForm');

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!form.checkValidity()) { form.classList.add('was-validated'); return; }
        
        const btn = document.getElementById('btn-save-cat');
        btn.disabled = true;
        
        fetch(HANDLER, { method: 'POST', body: new FormData(form) })
        .then(r => r.json()).then(res => {
            btn.disabled = false;
            if(res.success) { 
                Swal.fire({ icon: 'success', title: 'Success', text: res.message, timer: 1500, showConfirmButton: false })
                .then(() => location.reload()); 
            }
            else Swal.fire('Error', res.message, 'error');
        });
    });

    document.getElementById('modalCat').addEventListener('hidden.bs.modal', resetCatForm);
});

function editCategory(cat) {
    if(!form) form = document.getElementById('catForm');
    form.reset();
    document.getElementById('cat-id').value = cat.id;
    document.getElementById('cat-action').value = 'edit_category';
    document.getElementById('cat-name').value = cat.name;
    document.getElementById('cat-fee').value = cat.franchise_fee;
    document.getElementById('cat-status').value = cat.status;
    document.getElementById('catModalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>Edit Category';
    modalCat.show();
}

function openAddCategory() {
    resetCatForm();
    modalCat.show();
}

function resetCatForm() {
    form.reset();
    document.getElementById('cat-id').value = '';
    document.getElementById('cat-action').value = 'add_category';
    document.getElementById('catModalTitle').innerHTML = '<i class="fas fa-tag me-2"></i>Add New Category';
}

function viewCategory(id) {
    document.getElementById('view-cat-body').innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>';
    new bootstrap.Modal(document.getElementById('modalViewCat')).show();
    
    fetch(`${HANDLER}?action=view_category&id=${id}`)
    .then(r => r.json()).then(res => {
        if(!res.success) {
            document.getElementById('view-cat-body').innerHTML = `<p class="text-danger p-3">${res.message}</p>`;
            return;
        }
        const d = res.data;
        document.getElementById('view-cat-body').innerHTML = `
            <div class="enquiry-view-premium p-1">
                <div class="view-section mb-3">
                    <div class="view-section-header"><i class="fas fa-tag me-2"></i>CATEGORY INFORMATION</div>
                    <div class="row g-3 px-3 py-2">
                        <div class="col-12">
                            <label class="view-label">Category Name</label>
                            <div class="view-value text-primary fw-bold fs-5">${d.name}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="view-label">Franchise Fee</label>
                            <div class="view-value text-success fw-bold">&#8377; ${parseFloat(d.franchise_fee).toLocaleString()}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="view-label">Status</label>
                            <div class="view-value">
                                <span class="badge bg-${d.status == 1 ? 'success' : 'danger'} rounded-pill">
                                    ${d.status == 1 ? 'Active' : 'Inactive'}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
}

function deleteCategory(id, name) {
    window.confirmDelete({
        target: name,
        onConfirm: function(modal, btn) {
            const fd = new FormData();
            fd.append('action', 'delete_category');
            fd.append('id', id);
            fetch(HANDLER, { method: 'POST', body: fd })
            .then(r => r.json()).then(res => {
                modal.hide();
                if(res.success) { 
                    Swal.fire({ icon: 'success', title: 'Deleted!', text: res.message, timer: 1500, showConfirmButton: false })
                    .then(() => location.reload());
                }
                else Swal.fire('Error', res.message, 'error');
            });
        }
    });
}
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>
