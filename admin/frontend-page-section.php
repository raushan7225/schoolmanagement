<?php
// admin/frontend-page-section.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Fetch existing sections
$stmt = $pdo->query("SELECT * FROM frontend_sections ORDER BY section_key ASC");
$sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>UI Block Designer</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Frontend</li>
            <li class="breadcrumb-item active">Page Section</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <!-- Add/Edit Section Sidebar -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 sticky-top" style="top: 100px;">
                <div class="card-header bg-light py-3 border-bottom">
                    <h5 class="card-title mb-0" id="form-title"><i class="fas fa-cubes me-2"></i>Configure UI Block</h5>
                </div>
                <div class="card-body pt-4">
                    <form id="sectionForm" class="row g-3" novalidate enctype="multipart/form-data">
                        <input type="hidden" name="action" value="save_section">
                        <input type="hidden" name="id" id="s-id" value="">
                        
                        <div class="col-12">
                            <label class="form-label fw-bold">Block Unique Key <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="section_key" id="s-key" placeholder="e.g. HOME_HERO_TITLE" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Block Title (Internal)</label>
                            <input type="text" class="form-control" name="title" id="s-title" placeholder="e.g. Home Welcome Text">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold">Section Content / Value <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="content" id="s-content" rows="6" placeholder="Text or HTML content..." required></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Page Slug Reference</label>
                            <input type="text" class="form-control" name="page_slug" id="s-page-slug" placeholder="e.g. about-us">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Status</label>
                            <select class="form-select" name="status" id="s-status">
                                <option value="1">Active</option>
                                <option value="0">Hidden</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Block Image (Optional)</label>
                            <input type="file" class="form-control" name="image" id="s-image" accept="image/*">
                            <div class="mt-2 d-none text-center" id="img-preview-box">
                                <img src="" id="img-preview" class="img-fluid rounded border" style="max-height: 80px;">
                            </div>
                        </div>
                        
                        <div class="col-12 pt-2 text-end border-top mt-4">
                            <button type="button" class="btn btn-light px-4 me-2 d-none" id="btn-cancel" onclick="resetForm()">Cancel</button>
                            <button type="submit" class="btn btn-primary px-5 fw-bold" id="btn-save">
                                <i class="fas fa-save me-2"></i>SAVE BLOCK
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Section List -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle datatable-premium">
                            <thead class="table-light">
                                <tr>
                                    <th width="60">S.No.</th>
                                    <th>Block Identifier</th>
                                    <th>Content Preview</th>
                                    <th class="text-end" data-no-sort>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($sections)): $sn=1; foreach($sections as $s): ?>
                                <tr id="sectionrow-<?php echo $s['id']; ?>">
                                    <td><?php echo $sn++; ?></td>
                                    <td>
                                        <code class="text-primary fw-bold"><?php echo htmlspecialchars($s['section_key']); ?></code>
                                        <div class="small text-muted"><?php echo htmlspecialchars($s['title']); ?></div>
                                    </td>
                                    <td>
                                        <div class="small text-muted text-truncate" style="max-width: 300px;">
                                            <?php echo htmlspecialchars(strip_tags($s['content'])); ?>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-warning btn-icon me-1" title="Edit" onclick="editSection(<?php echo $s['id']; ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger btn-icon" title="Delete" onclick="deleteSection(<?php echo $s['id']; ?>, '<?php echo addslashes($s['section_key']); ?>')">
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
const ALL_SECTIONS = <?php echo json_encode($sections); ?>;

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('sectionForm');

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!form.checkValidity()) { form.classList.add('was-validated'); return; }
        
        const btn = document.getElementById('btn-save');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>SAVING...';
        
        fetch(HANDLER, { method: 'POST', body: new FormData(form) })
        .then(r => r.json()).then(res => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-2"></i>SAVE BLOCK';
            if(res.success) { 
                Swal.fire({ icon: 'success', title: 'Success', text: res.message, timer: 1500, showConfirmButton: false })
                .then(() => location.reload()); 
            }
            else Swal.fire('Error', res.message, 'error');
        });
    });
});

function editSection(id) {
    const s = ALL_SECTIONS.find(x => x.id == id);
    if (!s) return;
    
    resetForm();
    document.getElementById('s-id').value = s.id;
    document.getElementById('s-key').value = s.section_key;
    document.getElementById('s-title').value = s.title;
    document.getElementById('s-content').value = s.content;
    document.getElementById('s-page-slug').value = s.page_slug;
    document.getElementById('s-status').value = s.status;
    
    if(s.image) {
        document.getElementById('img-preview-box').classList.remove('d-none');
        document.getElementById('img-preview').src = `<?php echo BASE_URL; ?>media/frontend/${s.image}`;
    }
    
    document.getElementById('form-title').innerHTML = '<i class="fas fa-edit me-2"></i>Edit UI Block';
    document.getElementById('btn-cancel').classList.remove('d-none');
    window.scrollTo({top: 0, behavior: 'smooth'});
}

function resetForm() {
    const form = document.getElementById('sectionForm');
    form.reset();
    form.classList.remove('was-validated');
    document.getElementById('s-id').value = '';
    document.getElementById('img-preview-box').classList.add('d-none');
    document.getElementById('form-title').innerHTML = '<i class="fas fa-cubes me-2"></i>Configure UI Block';
    document.getElementById('btn-cancel').classList.add('d-none');
}

function deleteSection(id, name) {
    window.confirmDelete({
        target: name,
        onConfirm: function(cModal, btn) {
            const fd = new FormData();
            fd.append('action', 'delete_section');
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
