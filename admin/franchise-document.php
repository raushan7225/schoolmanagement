<?php
// admin/franchise-document.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Filter logic maintained for initial load if needed, but DataTables will handle search locally mostly
$where = "WHERE 1=1";
$params = [];
$filter_center = (int)($_GET['franchise_id'] ?? $_GET['id'] ?? 0);
$filter_type = $_GET['doc_type'] ?? '';

if ($filter_center) { $where .= " AND d.franchise_id = ?"; $params[] = $filter_center; }
if ($filter_type) { $where .= " AND d.type = ?"; $params[] = $filter_type; }

$stmt = $pdo->prepare("
    SELECT d.*, f.center_name, f.center_code 
    FROM franchise_documents d
    JOIN franchises f ON d.franchise_id = f.id
    $where
    ORDER BY d.created_at DESC
");
$stmt->execute($params);
$documents = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Centers for dropdown
$centers = $pdo->query("SELECT id, center_name, center_code FROM franchises ORDER BY center_name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>Franchise Documents</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Franchise Management</li>
            <li class="breadcrumb-item active">Documents</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle datatable-premium"
                               data-add-btn='{"text":"Upload Document","onclick":"openUploadModal()","icon":"fas fa-upload"}'>
                            <thead class="table-light">
                                <tr>
                                    <th width="60">S.No.</th>
                                    <th>Center Name</th>
                                    <th>Document Details</th>
                                    <th>Upload Date</th>
                                    <th class="text-end" data-no-sort>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $sn=1; foreach($documents as $d): ?>
                                <tr id="docrow-<?php echo $d['id']; ?>">
                                    <td><?php echo $sn++; ?></td>
                                    <td>
                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($d['center_name']); ?></div>
                                        <div class="small text-muted">Code: <?php echo htmlspecialchars($d['center_code']); ?></div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark fs-6"><?php echo htmlspecialchars($d['title']); ?></div>
                                        <span class="badge bg-info-light text-info border border-info px-3"><?php echo strtoupper($d['type']); ?></span>
                                    </td>
                                    <td><div class="small fw-bold text-dark"><?php echo date('d M Y', strtotime($d['created_at'])); ?></div></td>
                                    <td class="text-end">
                                        <div class="btn-group shadow-sm border rounded">
                                            <a href="<?php echo BASE_URL . 'media/franchise/documents/' . $d['file_name']; ?>" target="_blank" class="btn btn-sm btn-outline-info border-0 px-2" title="View / Download">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline-danger border-0 px-2" title="Delete" onclick="deleteDoc(<?php echo $d['id']; ?>, '<?php echo addslashes($d['title']); ?>')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
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

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-upload me-2"></i>Upload Center Document</h5>
                <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                    <i class="<?php echo v('theme_modal_close_icon', 'fas fa-times'); ?>"></i>
                </button>
            </div>
            <form id="doc-upload-form" novalidate enctype="multipart/form-data">
                <input type="hidden" name="action" value="upload_document">
                <div class="modal-body px-3 py-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Select Center <span class="text-danger">*</span></label>
                        <select class="form-select select2-basic" name="franchise_id" required>
                            <option value="">Search Center...</option>
                            <?php foreach($centers as $c): ?>
                                <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['center_name']); ?> (<?php echo $c['center_code']; ?>)</option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Please select a center.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Document Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="doc_title" placeholder="e.g. Agreement Copy 2024" required>
                        <div class="invalid-feedback">Document title is required.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Document Type</label>
                        <select class="form-select" name="doc_type">
                            <option>Agreement Paper</option>
                            <option>Registration Certificate</option>
                            <option>Director ID Proof</option>
                            <option>Center Photos</option>
                            <option>Other</option>
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold text-dark">Select File <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="doc_file" accept=".pdf,.jpg,.jpeg,.png" required>
                        <div class="invalid-feedback">Please select a valid file.</div>
                        <small class="text-muted">Allowed: PDF, JPG, PNG (Max 5MB)</small>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top py-2">
                    <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">CANCEL</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold" id="btn-upload">
                        <i class="fas fa-cloud-upload-alt me-2"></i>START UPLOAD
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const HANDLER = '<?php echo BASE_URL; ?>ajax/franchise_handler.php';
let uploadModal;

document.addEventListener('DOMContentLoaded', () => {
    uploadModal = new bootstrap.Modal(document.getElementById('uploadModal'));
    const form = document.getElementById('doc-upload-form');
    
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!form.checkValidity()) { form.classList.add('was-validated'); return; }
        
        const btn = document.getElementById('btn-upload');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>UPLOADING...';
        
        fetch(HANDLER, { method: 'POST', body: new FormData(form) })
        .then(r => r.json()).then(res => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-cloud-upload-alt me-2"></i>START UPLOAD';
            if(res.success) { location.reload(); }
            else alert(res.message);
        });
    });
});

function openUploadModal() {
    const f = document.getElementById('doc-upload-form');
    f.reset();
    f.classList.remove('was-validated');
    uploadModal.show();
}

function deleteDoc(id, title) {
    if(confirm(`Are you sure you want to delete document "${title}"?`)) {
        const fd = new FormData();
        fd.append('action', 'delete_document');
        fd.append('id', id);
        fetch(HANDLER, { method: 'POST', body: fd })
        .then(r => r.json()).then(res => {
            if(res.success) { location.reload(); }
            else alert(res.message);
        });
    }
}
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>
