<?php
// admin/online-admission-enquiry.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

$search = trim($_GET['search'] ?? '');
$where  = "WHERE e.status=1 AND e.source='online'"; $params = [];
if ($search) { $where .= " AND (e.full_name LIKE ? OR e.mobile LIKE ? OR e.email LIKE ?)"; $params = ["%$search%","%$search%","%$search%"]; }

$stmt = $pdo->prepare("SELECT e.*, c.name as course_name FROM enquiries e LEFT JOIN courses c ON e.course_id=c.id $where ORDER BY e.created_at DESC");
$stmt->execute($params);
$enquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);

// For Edit Modal Dropdowns
$sessList = $pdo->query("SELECT id, session_label FROM admission_sessions WHERE status=1 ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
$states   = $pdo->query("SELECT id, name FROM states ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>Online Admission Enquiry</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Enquiry Management</li>
            <li class="breadcrumb-item active">Online Enquiry</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-12">
            <!-- Search & Filter Bar -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-body py-3">
                    <form class="row g-3 align-items-center" method="GET">
                        <div class="col-md-8">
                            <div class="input-group border rounded-pill px-3 py-1 bg-light">
                                <span class="input-group-text bg-transparent border-0 text-muted"><i class="fas fa-search"></i></span>
                                <input type="text" name="search" class="form-control bg-transparent border-0 shadow-none" 
                                       placeholder="Search by student name, mobile, or email..." value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary fw-bold flex-grow-1 rounded-pill">
                                    <i class="fas fa-filter me-2"></i>FILTER
                                </button>
                                <a href="online-admission-enquiry.php" class="btn btn-outline-secondary fw-bold flex-grow-1 rounded-pill">
                                    <i class="fas fa-undo me-2"></i>RESET
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div id="oe-alert" class="d-none mb-3"></div>
            <div class="card">
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle datatable-premium">
                            <thead class="table-light">
                                <tr>
                                    <th width="60">S.No.</th>
                                    <th>Date</th>
                                    <th>Student Name</th>
                                    <th>Phone / Email</th>
                                    <th>Course</th>
                                    <th data-no-sort>Message</th>
                                    <th data-no-sort>Status</th>
                                    <th class="text-end" data-no-sort>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $sn=1; foreach($enquiries as $e): ?>
                                <tr id="orow-<?php echo $e['id']; ?>">
                                    <td><?php echo $sn++; ?></td>
                                    <td><div class="small fw-bold text-dark"><?php echo date('d M Y', strtotime($e['created_at'])); ?></div></td>
                                    <td><div class="fw-bold text-dark"><?php echo strtoupper(htmlspecialchars($e['full_name'])); ?></div></td>
                                    <td>
                                        <div class="small"><i class="fas fa-phone-alt text-muted me-1 small"></i><?php echo $e['mobile']; ?></div>
                                        <div class="small text-muted"><i class="fas fa-envelope text-muted me-1 small"></i><?php echo $e['email'] ?: '—'; ?></div>
                                    </td>
                                    <td><span class="badge bg-primary-light text-primary border border-primary px-2"><?php echo $e['course_name'] ?: '—'; ?></span></td>
                                    <td><small class="text-muted d-inline-block text-truncate" style="max-width: 200px;"><?php echo htmlspecialchars($e['message'] ?? ''); ?></small></td>
                                    <td>
                                        <select class="form-select form-select-sm status-select" style="width:145px;" data-id="<?php echo $e['id']; ?>" onchange="updateOStatus(this)">
                                            <option value="new"       <?php echo $e['approval_status']=='new'?'selected':''; ?>>🟡 New</option>
                                            <option value="contacted" <?php echo $e['approval_status']=='contacted'?'selected':''; ?>>🔵 Contacted</option>
                                            <option value="closed"    <?php echo $e['approval_status']=='closed'?'selected':''; ?>>🔴 Closed</option>
                                        </select>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group shadow-sm border rounded">
                                            <button class="btn btn-sm btn-outline-info border-0 px-2" title="View Details" onclick="viewOEnquiry(<?php echo $e['id']; ?>)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-warning border-0 px-2" title="Edit Enquiry" onclick="editOEnquiry(<?php echo $e['id']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger border-0 px-2" title="Delete" onclick="deleteOEnquiry(<?php echo $e['id']; ?>, '<?php echo addslashes($e['full_name']); ?>')">
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
<div class="modal fade" id="modalViewOEnquiry" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-eye me-2"></i>Online Enquiry Details</h5>
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
<div class="modal fade" id="modalEditOEnquiry" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Online Enquiry</h5>
                <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                    <i class="<?php echo v('theme_modal_close_icon', 'fas fa-times'); ?>"></i>
                </button>
            </div>
            <form id="form-oe" novalidate>
                <input type="hidden" name="id" id="oe-id">
                <input type="hidden" name="action" value="edit">
                <div class="modal-body px-3 py-4">
                    <div class="row g-3">
                        <div class="col-12"><div class="form-section-header">1. PERSONAL INFORMATION</div></div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Student Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" id="oe-full_name" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Mobile Number <span class="text-danger">*</span></label>
                            <input type="text" name="mobile" id="oe-mobile" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Email Address</label>
                            <input type="email" name="email" id="oe-email" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Gender</label>
                            <select name="gender" id="oe-gender" class="form-select">
                                <option value="">Select</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Qualification</label>
                            <select name="qualification" id="oe-qualification" class="form-select">
                                <option value="">Select</option>
                                <option value="10th">10th</option>
                                <option value="12th">12th</option>
                                <option value="Graduation">Graduation</option>
                                <option value="Diploma">Diploma</option>
                            </select>
                        </div>

                        <div class="col-12 mt-4"><div class="form-section-header">2. ACADEMIC & LOCATION</div></div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Academic Session</label>
                            <select name="session_id" id="oe-session_id" class="form-select">
                                <option value="">Select Session</option>
                                <?php foreach($sessList as $s): ?>
                                    <option value="<?php echo $s['id']; ?>"><?php echo $s['session_label']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-bold small">Street Address</label>
                            <textarea name="address" id="oe-address" class="form-control" rows="1"></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">State</label>
                            <select name="state_id" id="oe-state_id" class="form-select">
                                <option value="">Select State</option>
                                <?php foreach($states as $s): ?>
                                    <option value="<?php echo $s['id']; ?>"><?php echo $s['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">District</label>
                            <select name="district_id" id="oe-district_id" class="form-select" disabled>
                                <option value="">Select State First</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">City</label>
                            <select name="city_id" id="oe-city_id" class="form-select" disabled>
                                <option value="">Select District First</option>
                            </select>
                        </div>

                        <div class="col-12 mt-4"><div class="form-section-header">3. STATUS & QUERY</div></div>
                        <div class="col-12">
                            <label class="form-label fw-bold small">Student Query / Message</label>
                            <textarea name="message" id="oe-message" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Current Status</label>
                            <select name="approval_status" id="oe-approval_status" class="form-select">
                                <option value="new">New</option>
                                <option value="contacted">Contacted</option>
                                <option value="closed">Closed</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top py-2">
                    <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">CANCEL</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold" id="oe-save-btn"><i class="fas fa-save me-2"></i>UPDATE ENQUIRY</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const HANDLER = '<?php echo BASE_URL; ?>ajax/enquiry_handler.php';
let modalView, modalEdit;

document.addEventListener('DOMContentLoaded', () => {
    modalView = new bootstrap.Modal(document.getElementById('modalViewOEnquiry'));
    modalEdit = new bootstrap.Modal(document.getElementById('modalEditOEnquiry'));

    const oeForm = document.getElementById('form-oe');
    oeForm.onsubmit = function(e) {
        e.preventDefault();
        if(!this.checkValidity()) { this.classList.add('was-validated'); return; }
        const btn = document.getElementById('oe-save-btn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>UPDATING...';
        fetch(HANDLER, { method: 'POST', body: new FormData(this) })
        .then(r => r.json()).then(res => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-2"></i>UPDATE ENQUIRY';
            if(res.status === 'success') { location.reload(); }
            else alert(res.message);
        });
    };

    // Location Cascade
    const stateSel = document.getElementById('oe-state_id');
    const distSel = document.getElementById('oe-district_id');
    const citySel = document.getElementById('oe-city_id');

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

function updateOStatus(sel) {
    const fd = new FormData();
    fd.append('action', 'change_status');
    fd.append('id', sel.dataset.id);
    fd.append('approval_status', sel.value);
    fetch(HANDLER, { method: 'POST', body: fd }).then(r => r.json()).then(res => {
        if(res.status !== 'success') alert(res.message);
    });
}

function viewOEnquiry(id) {
    const body = document.getElementById('view-body');
    body.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>';
    modalView.show();
    
    fetch(`${HANDLER}?action=view&id=${id}`)
    .then(r => r.json()).then(res => {
        if(res.status !== 'success') { body.innerHTML = `<p class="text-danger p-3">${res.message}</p>`; return; }
        const d = res.data;
        const badge = {'new':'warning','contacted':'info','closed':'danger'}[d.approval_status] || 'secondary';
        
        body.innerHTML = `
            <div class="enquiry-view-premium p-1">
                <div class="view-section mb-0">
                    <div class="view-section-header">1. BASIC INFORMATION</div>
                    <div class="row g-3 px-3 py-4">
                        <div class="col-md-6"><label class="view-label">Full Name</label><div class="view-value fw-bold text-dark fs-5">${d.full_name.toUpperCase()}</div></div>
                        <div class="col-md-6"><label class="view-label">Mobile Number</label><div class="view-value fw-bold">${d.mobile}</div></div>
                        <div class="col-md-6"><label class="view-label">Email Address</label><div class="view-value text-primary fw-bold text-lowercase">${d.email || '—'}</div></div>
                        <div class="col-md-3"><label class="view-label">Gender</label><div class="view-value">${(d.gender || '—').toUpperCase()}</div></div>
                        <div class="col-md-3"><label class="view-label">Qualification</label><div class="view-value text-success fw-bold">${d.qualification || '—'}</div></div>
                    </div>
                </div>
                <div class="view-section mb-0 border-top">
                    <div class="view-section-header">2. ACADEMIC & LOCATION</div>
                    <div class="row g-3 px-3 py-4">
                        <div class="col-md-4"><label class="view-label">Target Course</label><div class="view-value text-primary fw-bold">${d.course_name || '—'}</div></div>
                        <div class="col-md-4"><label class="view-label">Academic Session</label><div class="view-value">${d.session_label || '—'}</div></div>
                        <div class="col-md-4"><label class="view-label">Admission Center</label><div class="view-value small">${d.center_name || 'N/A'}</div></div>
                        <div class="col-md-12"><label class="view-label">Location Address</label><div class="view-value small text-muted">${d.city_name ? d.city_name + ', ' : ''}${d.district_name ? d.district_name + ', ' : ''}${d.state_name || ''} ${d.address ? ' — ' + d.address : ''}</div></div>
                    </div>
                </div>
                <div class="view-section mb-0 border-top">
                    <div class="view-section-header">3. MESSAGE & STATUS</div>
                    <div class="row g-3 px-3 py-4">
                        <div class="col-md-4"><label class="view-label">Current Status</label><div class="view-value"><span class="badge bg-${badge} rounded-pill px-3">${d.approval_status.toUpperCase()}</span></div></div>
                        <div class="col-md-8"><label class="view-label">Student Message / Inquiry Detail</label><div class="view-value p-3 bg-light rounded border-start border-4 border-primary small text-muted">${d.message || 'No additional query provided.'}</div></div>
                    </div>
                </div>
                <div class="text-end p-2 bg-light border-top small text-muted">Enquiry ID: #OEQ-${id.toString().padStart(5, '0')} | Added: ${d.created_at}</div>
            </div>
        `;
    });
}

function editOEnquiry(id) {
    fetch(`${HANDLER}?action=view&id=${id}`)
    .then(r => r.json()).then(res => {
        if(res.status !== 'success') return alert(res.message);
        const d = res.data;
        document.getElementById('oe-id').value = d.id;
        document.getElementById('oe-full_name').value = d.full_name;
        document.getElementById('oe-mobile').value = d.mobile;
        document.getElementById('oe-email').value = d.email || '';
        document.getElementById('oe-gender').value = d.gender || '';
        document.getElementById('oe-qualification').value = d.qualification || '';
        document.getElementById('oe-session_id').value = d.session_id || '';
        document.getElementById('oe-address').value = d.address || '';
        document.getElementById('oe-message').value = d.message || '';
        document.getElementById('oe-approval_status').value = d.approval_status;
        
        const stateSel = document.getElementById('oe-state_id');
        stateSel.value = d.state_id;
        
        // Trigger cascades
        if(d.state_id) {
            fetch(`<?php echo BASE_URL; ?>ajax/location_handler.php?action=get_districts&state_id=${d.state_id}`)
            .then(r => r.json()).then(resDist => {
                const distSel = document.getElementById('oe-district_id');
                distSel.innerHTML = '<option value="">Select District</option>';
                resDist.data.forEach(item => distSel.innerHTML += `<option value="${item.id}">${item.name}</option>`);
                distSel.value = d.district_id;
                distSel.disabled = false;
                
                return fetch(`<?php echo BASE_URL; ?>ajax/location_handler.php?action=get_cities&district_id=${d.district_id}`);
            })
            .then(r => r.json()).then(resCity => {
                const citySel = document.getElementById('oe-city_id');
                citySel.innerHTML = '<option value="">Select City</option>';
                resCity.data.forEach(item => citySel.innerHTML += `<option value="${item.id}">${item.name}</option>`);
                citySel.value = d.city_id;
                citySel.disabled = false;
            });
        }

        modalEdit.show();
    });
}

function deleteOEnquiry(id, name) {
    if(confirm(`Are you sure you want to delete enquiry from "${name}"?`)) {
        const fd = new FormData();
        fd.append('action', 'delete');
        fd.append('id', id);
        fetch(HANDLER, { method: 'POST', body: fd }).then(r => r.json()).then(res => {
            if(res.status === 'success') { location.reload(); }
            else alert(res.message);
        });
    }
}
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>
