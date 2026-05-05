<?php
// admin/online-franchise-application.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

$search = trim($_GET['search'] ?? '');
$where  = "WHERE e.status=1 AND e.source='online'"; $params = [];
if ($search) { 
    $where .= " AND (e.director_name LIKE ? OR e.phone LIKE ? OR e.center_name LIKE ? OR e.email LIKE ?)"; 
    $params = ["%$search%","%$search%","%$search%","%$search%"]; 
}

$stmt = $pdo->prepare("
    SELECT e.*, s.name as state_name, d.name as district_name, c.name as city_name 
    FROM franchise_enquiries e 
    LEFT JOIN states s ON e.state_id = s.id 
    LEFT JOIN districts d ON e.district_id = d.id 
    LEFT JOIN cities c ON e.city_id = c.id
    $where 
    ORDER BY e.created_at DESC
");
$stmt->execute($params);
$leads = $stmt->fetchAll(PDO::FETCH_ASSOC);

// For Edit Modal
$states = $pdo->query("SELECT id, name FROM states ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>Online Franchise Application</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Enquiry Management</li>
            <li class="breadcrumb-item active">Franchise Applications</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-12">
            <div id="ofa-alert" class="d-none mb-3"></div>
            <div class="card">
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle datatable-premium">
                            <thead class="table-light">
                                <tr>
                                    <th width="60">S.No.</th>
                                    <th>Apply Date</th>
                                    <th>Applicant Name</th>
                                    <th>Center Name</th>
                                    <th>Contact</th>
                                    <th>Location</th>
                                    <th data-no-sort>Status</th>
                                    <th class="text-end" data-no-sort>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $sn=1; foreach($leads as $l): ?>
                                <tr id="row-<?php echo $l['id']; ?>">
                                    <td><?php echo $sn++; ?></td>
                                    <td><div class="small fw-bold text-dark"><?php echo date('d M Y', strtotime($l['created_at'])); ?></div></td>
                                    <td><div class="fw-bold text-dark"><?php echo strtoupper(htmlspecialchars($l['director_name'])); ?></div></td>
                                    <td><div class="small fw-semibold"><?php echo htmlspecialchars($l['center_name'] ?: 'N/A'); ?></div></td>
                                    <td>
                                        <div class="small"><i class="fas fa-phone-alt text-muted me-1 small"></i><?php echo $l['phone']; ?></div>
                                        <div class="small text-muted"><i class="fas fa-envelope text-muted me-1 small"></i><?php echo $l['email'] ?: '—'; ?></div>
                                    </td>
                                    <td>
                                        <div class="small"><i class="fas fa-map-marker-alt text-danger me-1 small"></i><?php echo $l['city_name']; ?>, <?php echo $l['state_name']; ?></div>
                                    </td>
                                    <td>
                                        <select class="form-select form-select-sm status-select ofa-status" style="width:145px;" data-id="<?php echo $l['id']; ?>" onchange="updateApplicationStatus(this)">
                                            <option value="new"       <?php echo $l['approval_status']=='new'?'selected':''; ?>>🟡 New</option>
                                            <option value="reviewed"  <?php echo $l['approval_status']=='reviewed'?'selected':''; ?>>🔵 Reviewed</option>
                                            <option value="approved"  <?php echo $l['approval_status']=='approved'?'selected':''; ?>>🟢 Approved</option>
                                            <option value="rejected"  <?php echo $l['approval_status']=='rejected'?'selected':''; ?>>🔴 Rejected</option>
                                        </select>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group shadow-sm border rounded">
                                            <button class="btn btn-sm btn-outline-info border-0 px-2" title="View Details" onclick="viewApplication(<?php echo $l['id']; ?>)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-warning border-0 px-2" title="Edit Application" onclick="editApplication(<?php echo $l['id']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger border-0 px-2" title="Delete" onclick="deleteApplication(<?php echo $l['id']; ?>, '<?php echo addslashes($l['director_name']); ?>')">
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

<!-- View Modal -->
<div class="modal fade" id="modalViewApplication" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-eye me-2"></i>Franchise Application Details</h5>
                <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                    <i class="<?php echo v('theme_modal_close_icon', 'fas fa-times'); ?>"></i>
                </button>
            </div>
            <div class="modal-body p-0" id="view-body">
                <div class="text-center py-5"><div class="spinner-border text-primary"></div></div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="modalEditApplication" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Online Application</h5>
                <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                    <i class="<?php echo v('theme_modal_close_icon', 'fas fa-times'); ?>"></i>
                </button>
            </div>
            <form id="form-ofa" novalidate>
                <input type="hidden" name="id" id="ofa-id">
                <input type="hidden" name="action" value="update_lead">
                <div class="modal-body px-3 py-4">
                    <div class="row g-3">
                        <div class="col-12"><div class="form-section-header">1. APPLICANT & CENTER INFO</div></div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Director Name <span class="text-danger">*</span></label>
                            <input type="text" name="director_name" id="ofa-director_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Proposed Center Name</label>
                            <input type="text" name="center_name" id="ofa-center_name" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Mobile Number <span class="text-danger">*</span></label>
                            <input type="text" name="phone" id="ofa-phone" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Email Address</label>
                            <input type="email" name="email" id="ofa-email" class="form-control">
                        </div>

                        <div class="col-12 mt-4"><div class="form-section-header">2. LOCATION DETAILS</div></div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">State <span class="text-danger">*</span></label>
                            <select name="state_id" id="ofa-state" class="form-select" required>
                                <option value="">Select State</option>
                                <?php foreach($states as $s): ?>
                                    <option value="<?php echo $s['id']; ?>"><?php echo $s['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">District <span class="text-danger">*</span></label>
                            <select name="district_id" id="ofa-district" class="form-select" required disabled>
                                <option value="">Select State First</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">City/Block <span class="text-danger">*</span></label>
                            <select name="city_id" id="ofa-city" class="form-select" required disabled>
                                <option value="">Select District First</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small">Complete Address</label>
                            <textarea name="address" id="ofa-address" class="form-control" rows="2"></textarea>
                        </div>

                        <div class="col-12 mt-4"><div class="form-section-header">3. ADDITIONAL DETAILS</div></div>
                        <div class="col-12">
                            <label class="form-label fw-bold small">Message / Note</label>
                            <textarea name="comments" id="ofa-comments" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Application Status</label>
                            <select name="approval_status" id="ofa-approval_status" class="form-select">
                                <option value="new">New</option>
                                <option value="reviewed">Reviewed</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top py-2">
                    <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">CANCEL</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold" id="ofa-save-btn"><i class="fas fa-save me-2"></i>UPDATE APPLICATION</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const HANDLER = '<?php echo BASE_URL; ?>ajax/franchise_handler.php';
let modalView, modalEdit;

document.addEventListener('DOMContentLoaded', () => {
    modalView = new bootstrap.Modal(document.getElementById('modalViewApplication'));
    modalEdit = new bootstrap.Modal(document.getElementById('modalEditApplication'));

    const ofaForm = document.getElementById('form-ofa');
    ofaForm.onsubmit = function(e) {
        e.preventDefault();
        if(!this.checkValidity()) { this.classList.add('was-validated'); return; }
        const btn = document.getElementById('ofa-save-btn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>UPDATING...';
        fetch(HANDLER, { method: 'POST', body: new FormData(this) })
        .then(r => r.json()).then(res => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-2"></i>UPDATE APPLICATION';
            if(res.success) { location.reload(); }
            else alert(res.message);
        });
    };

    // Location Cascade
    const stateSel = document.getElementById('ofa-state');
    const distSel = document.getElementById('ofa-district');
    const citySel = document.getElementById('ofa-city');

    stateSel.onchange = function() {
        distSel.innerHTML = '<option value="">Loading...</option>';
        distSel.disabled = true;
        citySel.innerHTML = '<option value="">Select District First</option>';
        citySel.disabled = true;
        if(!this.value) return;
        fetch(`<?php echo BASE_URL; ?>ajax/location_handler.php?action=get_districts&state_id=${this.value}`)
        .then(r => r.json()).then(res => {
            distSel.innerHTML = '<option value="">Select District</option>';
            res.data.forEach(d => distSel.innerHTML += `<option value="${d.id}">${d.name}</option>`);
            distSel.disabled = false;
        });
    };

    distSel.onchange = function() {
        citySel.innerHTML = '<option value="">Loading...</option>';
        citySel.disabled = true;
        if(!this.value) return;
        fetch(`<?php echo BASE_URL; ?>ajax/location_handler.php?action=get_cities&district_id=${this.value}`)
        .then(r => r.json()).then(res => {
            citySel.innerHTML = '<option value="">Select City</option>';
            res.data.forEach(c => citySel.innerHTML += `<option value="${c.id}">${c.name}</option>`);
            citySel.disabled = false;
        });
    };
});

function updateApplicationStatus(sel) {
    const id = sel.dataset.id;
    const status = sel.value;
    const fd = new FormData();
    fd.append('action', 'update_lead_status');
    fd.append('id', id);
    fd.append('status', status);
    
    fetch(HANDLER, { method: 'POST', body: fd })
    .then(r => r.json()).then(res => {
        if(!res.success) alert(res.message);
    });
}

function viewApplication(id) {
    const body = document.getElementById('view-body');
    body.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>';
    modalView.show();
    
    fetch(`${HANDLER}?action=get_lead&id=${id}`)
    .then(r => r.json()).then(res => {
        if(!res.success) { body.innerHTML = `<p class="text-danger p-3">${res.message}</p>`; return; }
        const d = res.data;
        const badge = {'new':'warning','reviewed':'info','approved':'success','rejected':'danger'}[d.approval_status] || 'secondary';
        
        body.innerHTML = `
            <div class="enquiry-view-premium p-1">
                <div class="view-section mb-0">
                    <div class="view-section-header">1. DIRECTOR & CENTER INFO</div>
                    <div class="row g-3 px-3 py-4 align-items-center">
                        <div class="col-md-12">
                            <div class="row g-3">
                                <div class="col-md-6"><label class="view-label">Director Name</label><div class="view-value fw-bold text-dark fs-5">${d.director_name.toUpperCase()}</div></div>
                                <div class="col-md-6"><label class="view-label">Proposed Center Name</label><div class="view-value text-primary fw-bold fs-5">${d.center_name ? d.center_name.toUpperCase() : 'N/A'}</div></div>
                                <div class="col-md-4"><label class="view-label">Mobile Number</label><div class="view-value fw-bold">${d.phone}</div></div>
                                <div class="col-md-4"><label class="view-label">Email Address</label><div class="view-value text-lowercase">${d.email || '—'}</div></div>
                                <div class="col-md-4"><label class="view-label">Application Date</label><div class="view-value small">${new Date(d.created_at).toLocaleDateString('en-GB', {day: '2-digit', month: 'short', year: 'numeric'})}</div></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="view-section mb-0 border-top">
                    <div class="view-section-header">2. LOCATION & INFRASTRUCTURE</div>
                    <div class="row g-3 px-3 py-4">
                        <div class="col-md-4"><label class="view-label">State</label><div class="view-value">${d.state_name}</div></div>
                        <div class="col-md-4"><label class="view-label">District</label><div class="view-value">${d.district_name}</div></div>
                        <div class="col-md-4"><label class="view-label">City/Block</label><div class="view-value">${d.city_name}</div></div>
                        <div class="col-12"><label class="view-label">Complete Address</label><div class="view-value bg-light p-2 rounded">${d.address || 'No address provided.'}</div></div>
                    </div>
                </div>
                <div class="view-section mb-0 border-top">
                    <div class="view-section-header">3. STATUS & REMARKS</div>
                    <div class="row g-3 px-3 py-4">
                        <div class="col-md-4"><label class="view-label">Current Status</label><div class="view-value"><span class="badge bg-${badge} rounded-pill px-3">${d.approval_status.toUpperCase()}</span></div></div>
                        <div class="col-md-8"><label class="view-label">Director's Message / Internal Notes</label><div class="view-value p-3 bg-light rounded border-start border-4 border-primary small text-muted">${d.message || d.comments || 'No additional details provided.'}</div></div>
                    </div>
                </div>
                <div class="text-end p-2 bg-light border-top small text-muted">Lead ID: #FL-${id.toString().padStart(5, '0')} | Source: Online Portal</div>
            </div>
        `;
    });
}

function editApplication(id) {
    fetch(`${HANDLER}?action=get_lead&id=${id}`)
    .then(r => r.json()).then(res => {
        if(!res.success) return alert(res.message);
        const d = res.data;
        document.getElementById('ofa-id').value = d.id;
        document.getElementById('ofa-director_name').value = d.director_name;
        document.getElementById('ofa-center_name').value = d.center_name || '';
        document.getElementById('ofa-phone').value = d.phone;
        document.getElementById('ofa-email').value = d.email || '';
        document.getElementById('ofa-address').value = d.address || '';
        document.getElementById('ofa-comments').value = d.comments || d.message || '';
        document.getElementById('ofa-approval_status').value = d.approval_status;
        
        const stateSel = document.getElementById('ofa-state');
        stateSel.value = d.state_id;
        
        // Trigger cascades manually
        const fdDist = new FormData();
        fetch(`<?php echo BASE_URL; ?>ajax/location_handler.php?action=get_districts&state_id=${d.state_id}`)
        .then(r => r.json()).then(resDist => {
            const distSel = document.getElementById('ofa-district');
            distSel.innerHTML = '<option value="">Select District</option>';
            resDist.data.forEach(item => distSel.innerHTML += `<option value="${item.id}">${item.name}</option>`);
            distSel.value = d.district_id;
            distSel.disabled = false;
            
            return fetch(`<?php echo BASE_URL; ?>ajax/location_handler.php?action=get_cities&district_id=${d.district_id}`);
        })
        .then(r => r.json()).then(resCity => {
            const citySel = document.getElementById('ofa-city');
            citySel.innerHTML = '<option value="">Select City</option>';
            resCity.data.forEach(item => citySel.innerHTML += `<option value="${item.id}">${item.name}</option>`);
            citySel.value = d.city_id;
            citySel.disabled = false;
        });

        modalEdit.show();
    });
}

function deleteApplication(id, name) {
    if(confirm(`Are you sure you want to delete application from "${name}"?`)) {
        const fd = new FormData();
        fd.append('action', 'delete_lead');
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
