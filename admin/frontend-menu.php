<?php
// admin/frontend-menu.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Fetch existing menus
$stmt = $pdo->query("SELECT * FROM frontend_menus ORDER BY parent_id ASC, sort_order ASC");
$menus = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Helper for parent mapping
$menuMap = [];
foreach($menus as $m) { $menuMap[$m['id']] = $m; }
?>

<div class="pagetitle">
    <h1>Navigation Menu Designer</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Frontend</li>
            <li class="breadcrumb-item active">Front Menu</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <!-- Sidebar Form -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 sticky-top" style="top: 100px;">
                <div class="card-header bg-light py-3 border-bottom">
                    <h5 class="card-title mb-0" id="form-title"><i class="fas fa-list-ul me-2"></i>Configure Link</h5>
                </div>
                <div class="card-body pt-4">
                    <form id="menuForm" class="row g-3" novalidate>
                        <input type="hidden" name="action" value="save_menu">
                        <input type="hidden" name="id" id="m-id" value="">
                        
                        <div class="col-12">
                            <label class="form-label fw-bold">Link Label <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="title" id="m-title" placeholder="e.g. Courses" required>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label fw-bold">URL / Link <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="link" id="m-link" placeholder="e.g. /courses or #id" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Parent Menu (Optional)</label>
                            <select class="form-select" name="parent_id" id="m-parent">
                                <option value="0">--- ROOT LEVEL ---</option>
                                <?php foreach($menus as $m): if($m['parent_id'] == 0): ?>
                                    <option value="<?php echo $m['id']; ?>"><?php echo htmlspecialchars($m['title']); ?></option>
                                <?php endif; endforeach; ?>
                            </select>
                            <small class="text-muted">Select a parent to create a dropdown.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Sort Order</label>
                            <input type="number" class="form-control" name="sort_order" id="m-sort" value="1">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Status</label>
                            <select class="form-select" name="status" id="m-status">
                                <option value="1">Active</option>
                                <option value="0">Hidden</option>
                            </select>
                        </div>
                        
                        <div class="col-12 text-end pt-2 border-top mt-4">
                            <button type="button" class="btn btn-light px-4 me-2 d-none" id="btn-cancel" onclick="resetForm()">Cancel</button>
                            <button type="submit" class="btn btn-primary px-5 fw-bold" id="btn-save">
                                <i class="fas fa-save me-2"></i>SAVE LINK
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tree View List -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle datatable-premium">
                            <thead class="table-light">
                                <tr>
                                    <th width="60">Sort</th>
                                    <th>Menu Structure</th>
                                    <th>Route</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-end" data-no-sort>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($menus)): foreach($menus as $m): ?>
                                <tr id="menurow-<?php echo $m['id']; ?>">
                                    <td><?php echo $m['sort_order']; ?></td>
                                    <td>
                                        <?php if($m['parent_id'] > 0): ?>
                                            <span class="text-muted ms-3"><i class="fas fa-level-up-alt fa-rotate-90 me-2"></i></span>
                                            <span class="text-dark small"><?php echo htmlspecialchars($m['title']); ?></span>
                                            <small class="text-muted ms-1">(Sub of <?php echo htmlspecialchars($menuMap[$m['parent_id']]['title']); ?>)</small>
                                        <?php else: ?>
                                            <span class="fw-bold text-primary"><i class="fas fa-folder me-2"></i><?php echo htmlspecialchars($m['title']); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><code class="small"><?php echo htmlspecialchars($m['link']); ?></code></td>
                                    <td class="text-center">
                                        <span class="badge bg-<?php echo $m['status'] == 1 ? 'success' : 'danger'; ?> rounded-pill px-3">
                                            <?php echo $m['status'] == 1 ? 'Active' : 'Hidden'; ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-warning btn-icon me-1" title="Edit" onclick="editMenu(<?php echo $m['id']; ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger btn-icon" title="Delete" onclick="deleteMenu(<?php echo $m['id']; ?>, '<?php echo addslashes($m['title']); ?>')">
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
    </div>
</section>

<script>
'use strict';
const HANDLER = '<?php echo BASE_URL; ?>ajax/frontend_handler.php';
const ALL_MENUS = <?php echo json_encode($menus); ?>;

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('menuForm');
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!form.checkValidity()) { form.classList.add('was-validated'); return; }
        
        const btn = document.getElementById('btn-save');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>SAVING...';
        
        fetch(HANDLER, { method: 'POST', body: new FormData(form) })
        .then(r => r.json()).then(res => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-2"></i>SAVE LINK';
            if(res.success) { 
                Swal.fire({ icon: 'success', title: 'Success', text: res.message, timer: 1500, showConfirmButton: false })
                .then(() => location.reload()); 
            }
            else Swal.fire('Error', res.message, 'error');
        });
    });
});

function editMenu(id) {
    const m = ALL_MENUS.find(x => x.id == id);
    if (!m) return;
    
    resetForm();
    document.getElementById('m-id').value = m.id;
    document.getElementById('m-title').value = m.title;
    document.getElementById('m-link').value = m.link;
    document.getElementById('m-parent').value = m.parent_id;
    document.getElementById('m-sort').value = m.sort_order;
    document.getElementById('m-status').value = m.status;
    
    document.getElementById('form-title').innerHTML = '<i class="fas fa-edit me-2"></i>Edit Menu Item';
    document.getElementById('btn-cancel').classList.remove('d-none');
    window.scrollTo({top: 0, behavior: 'smooth'});
}

function resetForm() {
    document.getElementById('menuForm').reset();
    document.getElementById('m-id').value = '';
    document.getElementById('form-title').innerHTML = '<i class="fas fa-list-ul me-2"></i>Configure Link';
    document.getElementById('btn-cancel').classList.add('d-none');
}

function deleteMenu(id, name) {
    window.confirmDelete({
        target: name,
        onConfirm: function(cModal, btn) {
            const fd = new FormData();
            fd.append('action', 'delete_menu');
            fd.append('id', id);
            fetch(HANDLER, { method: 'POST', body: fd })
            .then(r => r.json()).then(res => {
                cModal.hide();
                if(res.success) { location.reload(); }
                else Swal.fire('Error', res.message, 'error');
            });
        }
    });
}
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>
