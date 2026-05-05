<?php
// admin/all-years.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Fetch existing years
$years = $pdo->query("SELECT * FROM academic_years ORDER BY year_label ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>Academic Infrastructure</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Course Management</li>
            <li class="breadcrumb-item active">All Years / Semesters</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <!-- Years List -->
        <div class="col-12">
            <div class="card">
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle datatable-premium"
                               data-add-btn='{"text":"Add Year / Semester","onclick":"openAddYear()","icon":"fas fa-plus"}'>
                            <thead class="table-light">
                                <tr>
                                    <th width="80">S.No.</th>
                                    <th>Year / Semester Name</th>
                                    <th>Type</th>
                                    <th data-no-sort>Status</th>
                                    <th class="text-end" data-no-sort>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $sn=1; foreach($years as $y): ?>
                                <tr id="yearrow-<?php echo $y['id']; ?>">
                                    <td><?php echo $sn++; ?></td>
                                    <td><div class="fw-bold text-dark"><?php echo htmlspecialchars($y['year_label']); ?></div></td>
                                    <td><span class="badge bg-secondary-light text-secondary"><?php echo $y['year_type']; ?></span></td>
                                    <td>
                                        <span class="badge bg-<?php echo $y['status'] == 1 ? 'success' : 'danger'; ?> rounded-pill">
                                            <?php echo $y['status'] == 1 ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-info btn-icon me-1" title="View" onclick="viewYear(<?php echo $y['id']; ?>)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning btn-icon me-1" title="Edit" onclick='editYear(<?php echo json_encode($y); ?>)'>
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger btn-icon" title="Delete" onclick="deleteYear(<?php echo $y['id']; ?>, '<?php echo addslashes($y['year_label']); ?>')">
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
<div class="modal fade" id="modalYear" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="yearModalTitle"><i class="fas fa-calendar-plus me-2"></i>Add Year / Semester</h5>
            <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                <i class="<?php echo v('theme_modal_close_icon', 'fas fa-times'); ?>"></i>
            </button>
        </div>
        <form id="yearForm" novalidate>
            <input type="hidden" name="action" id="year-action" value="add_year">
            <input type="hidden" name="id" id="year-id" value="">
            <div class="modal-body px-3 py-4">
                <div class="mb-3">
                    <label class="form-label fw-bold">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="year_label" id="year-label" placeholder="e.g. 1st Year" required>
                    <div class="invalid-feedback">Year/Semester name is required.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Type <span class="text-danger">*</span></label>
                    <select class="form-select" name="year_type" id="year-type" required>
                        <option value="Year">Year</option>
                        <option value="Semester">Semester</option>
                    </select>
                </div>
                <div class="mb-0">
                    <label class="form-label fw-bold">Status</label>
                    <select class="form-select" name="status" id="year-status">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer bg-light border-top py-2">
                <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">CANCEL</button>
                <button type="submit" class="btn btn-primary px-4 fw-bold" id="btn-save">
                    <i class="fas fa-save me-2"></i>SAVE YEAR
                </button>
            </div>
        </form>
    </div>
  </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="modalViewYear" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title"><i class="fas fa-eye me-2"></i>Academic Unit Details</h5>
            <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                <i class="<?php echo v('theme_modal_close_icon', 'fas fa-times'); ?>"></i>
            </button>
        </div>
        <div class="modal-body p-0" id="view-year-body">
            <div class="text-center py-5"><div class="spinner-border text-primary"></div></div>
        </div>
    </div>
  </div>
</div>

<script>
const HANDLER = '<?php echo BASE_URL; ?>ajax/course_handler.php';
let modalYear, form;

document.addEventListener('DOMContentLoaded', function() {
    modalYear = new bootstrap.Modal(document.getElementById('modalYear'));
    form = document.getElementById('yearForm');

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!form.checkValidity()) { form.classList.add('was-validated'); return; }
        
        const btn = document.getElementById('btn-save');
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
});

function openAddYear() {
    resetYearForm();
    modalYear.show();
}

function editYear(y) {
    if(!form) form = document.getElementById('yearForm');
    form.reset();
    document.getElementById('year-id').value = y.id;
    document.getElementById('year-action').value = 'edit_year';
    document.getElementById('year-label').value = y.year_label;
    document.getElementById('year-type').value = y.year_type || 'Year';
    document.getElementById('year-status').value = y.status;
    document.getElementById('yearModalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>Edit Year / Semester';
    modalYear.show();
}

function resetYearForm() {
    if(!form) form = document.getElementById('yearForm');
    form.reset();
    form.classList.remove('was-validated');
    document.getElementById('year-id').value = '';
    document.getElementById('year-action').value = 'add_year';
    document.getElementById('yearModalTitle').innerHTML = '<i class="fas fa-calendar-plus me-2"></i>Add Year / Semester';
}

function viewYear(id) {
    document.getElementById('view-year-body').innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>';
    new bootstrap.Modal(document.getElementById('modalViewYear')).show();
    
    fetch(`${HANDLER}?action=view_year&id=${id}`)
    .then(r => r.json()).then(res => {
        if(!res.success) {
            document.getElementById('view-year-body').innerHTML = `<p class="text-danger p-3">${res.message}</p>`;
            return;
        }
        const d = res.data;
        document.getElementById('view-year-body').innerHTML = `
            <div class="enquiry-view-premium p-1">
                <div class="view-section mb-0">
                    <div class="view-section-header">ACADEMIC UNIT INFO</div>
                    <div class="row g-3 px-3 py-3">
                        <div class="col-md-6">
                            <label class="view-label">Year / Semester Name</label>
                            <div class="view-value text-primary fw-bold fs-5">${d.year_label}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="view-label">Type</label>
                            <div class="view-value"><span class="badge bg-secondary-light text-secondary">${d.year_type}</span></div>
                        </div>
                        <div class="col-12">
                            <label class="view-label">Status</label>
                            <div class="view-value">
                                <span class="badge bg-${d.status == 1 ? 'success' : 'danger'} rounded-pill px-3">
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

function deleteYear(id, name) {
    window.confirmDelete({
        target: name,
        onConfirm: function(modal, btn) {
            const fd = new FormData();
            fd.append('action', 'delete_year');
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
