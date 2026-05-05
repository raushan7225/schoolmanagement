<?php
// admin/franchise-enquiry.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Fetch states for filter
$states_list = $pdo->query("SELECT id, name FROM states ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Search / Filter
$where = "WHERE e.status=1 AND e.source='manual'";
$params = [];
$search = trim($_GET['search'] ?? '');
$filter_status = $_GET['approval_status'] ?? '';
$filter_state = (int)($_GET['state_id'] ?? 0);

if ($search) { 
    $where .= " AND (e.director_name LIKE ? OR e.phone LIKE ? OR e.center_name LIKE ?)"; 
    $params[] = "%$search%"; $params[] = "%$search%"; $params[] = "%$search%"; 
}
if ($filter_status) { 
    $where .= " AND e.approval_status=?"; 
    $params[] = $filter_status; 
}
if ($filter_state) {
    $where .= " AND e.state_id=?";
    $params[] = $filter_state;
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
?>

<div class="pagetitle">
    <h1>Franchise Enquiry</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Enquiry Management</li>
            <li class="breadcrumb-item active">Franchise Enquiry</li>
        </ol>
    </nav>
</div>

<style>
.doc-upload-box {
    border: 1px dashed #d1d1d1;
    padding: 15px;
    border-radius: 10px;
    text-align: center;
    background: #fcfcfc;
    transition: all 0.3s;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}
.doc-upload-box:hover {
    border-color: #4154f1;
    background: #f0f2ff;
}
.doc-upload-box i {
    font-size: 1.8rem;
    color: #999;
    margin-bottom: 8px;
    display: block;
    width: 100%;
    text-align: center;
}
.modal-body {
    padding-bottom: 50px !important;
    overflow-y: auto;
    max-height: calc(100vh - 200px);
}
@media (max-width: 768px) {
    .modal-body {
        max-height: calc(100vh - 150px);
    }
}
</style>

<section class="section">
    <!-- Filter Bar -->
    <div class="card mb-3">
        <div class="card-body py-2">
            <form class="row g-2 align-items-center" method="GET">
                <div class="col-md-5">
                    <select name="state_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All States...</option>
                        <?php foreach($states_list as $sl): ?>
                            <option value="<?php echo $sl['id']; ?>" <?php echo $filter_state == $sl['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($sl['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <select name="approval_status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Status...</option>
                        <option value="new" <?php echo $filter_status == 'new' ? 'selected' : ''; ?>>🟡 New</option>
                        <option value="contacted" <?php echo $filter_status == 'contacted' ? 'selected' : ''; ?>>🔵 Contacted</option>
                        <option value="closed" <?php echo $filter_status == 'closed' ? 'selected' : ''; ?>>🔴 Closed</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary flex-grow-1 fw-bold"><i class="fas fa-filter me-2"></i>APPLY</button>
                    <a href="franchise-enquiry.php" class="btn btn-sm btn-outline-secondary fw-bold">RESET</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Alert -->
    <div id="feq-alert" class="d-none mb-3"></div>

<!-- Table -->
<div class="card">
    <div class="card-body pt-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle datatable-premium"
                   data-add-btn='{"text":"Add New Lead","onclick":"openAddLead()","icon":"fas fa-plus"}'>
                <thead class="table-light">
                    <tr>
                        <th>S.No.</th>
                        <th>Director / Center</th>
                        <th>Contact Info</th>
                        <th>Location</th>
                        <th>Follow-up</th>
                        <th data-no-sort>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($leads)): $sn=1; foreach($leads as $l): ?>
                    <tr id="frow-<?php echo $l['id']; ?>">
                        <td><?php echo $sn++; ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars($l['director_name']); ?></strong><br>
                            <small class="text-muted">Center: <?php echo htmlspecialchars($l['center_name'] ?: 'N/A'); ?></small>
                        </td>
                        <td>
                            <div class="small"><i class="fas fa-phone me-1 text-muted"></i><?php echo $l['phone']; ?></div>
                            <small class="text-primary"><?php echo $l['email'] ?: '—'; ?></small>
                        </td>
                        <td><small><?php echo $l['city_name'] ?: ($l['district_name'] ?: $l['state_name']); ?><?php echo $l['pincode'] ? " ({$l['pincode']})" : ''; ?></small></td>
                        <td><small><?php echo $l['followup_date'] ? date('d M Y', strtotime($l['followup_date'])) : '—'; ?></small></td>
                        <td>
                            <?php 
                                $badge = ['new'=>'warning','interested'=>'info','converted'=>'success','closed'=>'danger'][$l['approval_status']] ?? 'secondary';
                            ?>
                            <span class="badge bg-<?php echo $badge; ?> rounded-pill"><?php echo ucfirst($l['approval_status']); ?></span>
                        </td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-info btn-icon me-1" title="View" onclick="viewLead(<?php echo $l['id']; ?>)"><i class="fas fa-eye"></i></button>
                            <button class="btn btn-sm btn-outline-warning btn-icon me-1" title="Edit" onclick="editLead(<?php echo $l['id']; ?>)"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-sm btn-outline-danger btn-icon" title="Delete" onclick="deleteLead(<?php echo $l['id']; ?>, '<?php echo addslashes($l['director_name']); ?>')"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</div></div>
</section>

<!-- Add / Edit Franchise Enquiry Modal -->
<div class="modal fade" id="addFranchiseEnquiry" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-building me-2"></i><span id="feq-modal-title">New Franchise Enquiry</span></h5>
                <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                    <i class="<?php echo v('theme_modal_close_icon', 'fas fa-times'); ?>"></i>
                </button>
            </div>
            <form id="form-feq" enctype="multipart/form-data">
                <input type="hidden" name="id" id="feq-id">
                <input type="hidden" name="action" id="feq-action" value="add">
                <div class="modal-body px-3 py-2" style="max-height: 80vh; overflow-y: auto; overflow-x: hidden;">
                    <div class="row g-3 pb-5">
                        <!-- Section 1 -->
                        <div class="col-12"><div class="form-section-header">1. Personal & Center Details</div></div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Director Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="director_name" id="feq-director_name" placeholder="Owner Name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Email Address</label>
                            <input type="email" class="form-control" name="email" id="feq-email" placeholder="email@example.com">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Center Name</label>
                            <input type="text" class="form-control" name="center_name" id="feq-center_name" placeholder="Proposed Center Name">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Primary Phone <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="phone" id="feq-phone" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Qualification</label>
                            <input type="text" class="form-control" name="qualification" id="feq-qualification" placeholder="e.g. Graduate">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Alternate Phone</label>
                            <input type="text" class="form-control" name="phone_alt" id="feq-phone_alt">
                        </div>

                        <!-- Section 2 -->
                        <div class="col-12 mt-4"><div class="form-section-header">2. Location Details</div></div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Full Address</label>
                            <input type="text" class="form-control" name="address" id="feq-address">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">State <span class="text-danger">*</span></label>
                            <select class="form-select" id="enq_state" name="state_id" required>
                                <option value="">Select State</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">District <span class="text-danger">*</span></label>
                            <select class="form-select" id="enq_district" name="district_id" required>
                                <option value="">Select State first</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">City</label>
                            <select class="form-select" id="enq_city" name="city_id">
                                <option value="">Select District first</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Pincode</label>
                            <input type="text" class="form-control" name="pincode" id="feq-pincode"
                                   placeholder="6 Digit Pincode" maxlength="6" pattern="[0-9]{6}">
                        </div>

                        <!-- Section 3 -->
                        <div class="col-12 mt-4"><div class="form-section-header">3. Infrastructure Details</div></div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">No. of Computers</label>
                            <input type="number" class="form-control" name="computers" id="feq-computers" value="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Teachers</label>
                            <input type="number" class="form-control" name="teachers" id="feq-teachers" value="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Rooms</label>
                            <input type="number" class="form-control" name="rooms" id="feq-rooms" value="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Square Feet</label>
                            <input type="number" class="form-control" name="area_sqft" id="feq-area_sqft" value="0">
                        </div>

                        <!-- Section 5: Documents Gallery -->
                        <div class="col-12 mt-4"><div class="form-section-header">5. Documents Gallery</div></div>
                        <div class="col-md-4">
                            <div class="doc-upload-box">
                                <i class="fas fa-user-circle"></i>
                                <label class="form-label fw-bold">Director Photo</label>
                                <input type="file" class="form-control form-control-sm" name="dir_photo">
                                <div id="cur-dir_photo" class="mt-2"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="doc-upload-box">
                                <i class="fas fa-signature"></i>
                                <label class="form-label fw-bold">Signature</label>
                                <input type="file" class="form-control form-control-sm" name="dir_sig">
                                <div id="cur-dir_sig" class="mt-2"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="doc-upload-box">
                                <i class="fas fa-id-card"></i>
                                <label class="form-label fw-bold">Aadhar Card (Front)</label>
                                <input type="file" class="form-control form-control-sm" name="aadhar_front">
                                <div id="cur-aadhar_front" class="mt-2"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="doc-upload-box">
                                <i class="fas fa-id-card"></i>
                                <label class="form-label fw-bold">Aadhar Card (Back)</label>
                                <input type="file" class="form-control form-control-sm" name="aadhar_back">
                                <div id="cur-aadhar_back" class="mt-2"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="doc-upload-box">
                                <i class="fas fa-flask"></i>
                                <label class="form-label fw-bold">Labs Front Photo</label>
                                <input type="file" class="form-control form-control-sm" name="labs_photo">
                                <div id="cur-labs_photo" class="mt-2"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="doc-upload-box">
                                <i class="fas fa-file-contract"></i>
                                <label class="form-label fw-bold">Approval Doc</label>
                                <input type="file" class="form-control form-control-sm" name="approval_doc">
                                <div id="cur-approval_doc" class="mt-2"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="doc-upload-box">
                                <i class="fas fa-camera"></i>
                                <label class="form-label fw-bold">Center Front Photo</label>
                                <input type="file" class="form-control form-control-sm" name="center_photo">
                                <div id="cur-center_photo" class="mt-2"></div>
                            </div>
                        </div>

                        <!-- Section 4 -->
                        <div class="col-12 mt-4"><div class="form-section-header">4. Follow-up & Notes</div></div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Follow-up Date</label>
                            <input type="date" class="form-control" name="followup_date" id="feq-followup_date">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Estimate Fees</label>
                            <input type="number" class="form-control" name="estimate_fees" id="feq-estimate_fees" value="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Present Students</label>
                            <input type="number" class="form-control" name="present_students" id="feq-present_students" value="0">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Comments / Status Note</label>
                            <textarea class="form-control" name="comments" id="feq-comments" rows="2" placeholder="Describe infrastructure or other notes..."></textarea>
                        </div>
                        <div class="col-md-4" id="feq-status-wrap">
                            <label class="form-label fw-bold">Enquiry Status</label>
                            <select class="form-select" name="approval_status" id="feq-approval_status">
                                <option value="new">New</option>
                                <option value="interested">Interested</option>
                                <option value="converted">Converted</option>
                                <option value="closed">Closed</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary px-4 py-1" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 py-1" id="btn-save-feq"><i class="fas fa-save me-1"></i>Save Lead</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="modalView" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-eye me-2"></i>Lead Details</h5>
                <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                    <i class="<?php echo v('theme_modal_close_icon', 'fas fa-times'); ?>"></i>
                </button>
            </div>
            <div class="modal-body px-3 py-2" id="view-body" style="max-height: 80vh; overflow-y: auto; overflow-x: hidden;"></div>
        </div>
    </div>
</div>



<script>
document.addEventListener('DOMContentLoaded', () => {
    const HANDLER = '<?php echo BASE_URL; ?>ajax/franchise_enquiry_handler.php';

    function showAlert(msg, type='success') {
        const el = document.getElementById('feq-alert');
        el.className = `alert alert-${type} mb-3`; el.textContent = msg;
        el.scrollIntoView({behavior:'smooth'}); setTimeout(()=>el.className='d-none', 4000);
    }

    // Add button logic
    window.openAddLead = function() {
        document.getElementById('form-feq').reset();
        document.getElementById('feq-id').value = '';
        document.getElementById('feq-action').value = 'add';
        document.getElementById('feq-modal-title').textContent = 'New Franchise Enquiry';
        ['dir_photo','dir_sig','aadhar_front','aadhar_back','labs_photo','approval_doc','center_photo'].forEach(f => {
            const curEl = document.getElementById('cur-'+f);
            if(curEl) curEl.innerHTML = '';
        });
        initFEQLocations();
        bootstrap.Modal.getOrCreateInstance(document.getElementById('addFranchiseEnquiry')).show();
    };

    // Location Cascade
    let _enqLocationInited = false;
    function initFEQLocations() {
        if (!_enqLocationInited && typeof initLocationCascade === 'function') {
            const p = initLocationCascade({ stateEl: '#enq_state', districtEl: '#enq_district', cityEl: '#enq_city' });
            _enqLocationInited = true;
            return p;
        }
        return Promise.resolve();
    }

    window.editLead = function(id) {
        document.getElementById('form-feq').reset();
        initFEQLocations();
        fetch(HANDLER + '?action=view&id=' + id).then(r=>r.json()).then(res => {
            if(!res.success) return showAlert(res.message, 'danger');
            const d = res.data;
            document.getElementById('feq-id').value = d.id;
            document.getElementById('feq-action').value = 'edit';
            document.getElementById('feq-modal-title').textContent = 'Edit Lead — ' + d.director_name;
            document.getElementById('feq-director_name').value = d.director_name;
            document.getElementById('feq-email').value = d.email || '';
            document.getElementById('feq-center_name').value = d.center_name || '';
            document.getElementById('feq-phone').value = d.phone;
            document.getElementById('feq-phone_alt').value = d.phone_alt || '';
            document.getElementById('feq-qualification').value = d.qualification || '';
            document.getElementById('feq-address').value = d.address || '';
            document.getElementById('feq-pincode').value = d.pincode || '';
            document.getElementById('feq-computers').value = d.computers;
            document.getElementById('feq-teachers').value = d.teachers;
            document.getElementById('feq-rooms').value = d.rooms;
            document.getElementById('feq-area_sqft').value = d.area_sqft;
            document.getElementById('feq-followup_date').value = d.followup_date || '';
            document.getElementById('feq-estimate_fees').value = d.estimate_fees;
            document.getElementById('feq-present_students').value = d.present_students;
            document.getElementById('feq-comments').value = d.comments || '';
            document.getElementById('feq-approval_status').value = d.approval_status;
            
            // Show current files
            ['dir_photo','dir_sig','aadhar_front','aadhar_back','labs_photo','approval_doc','center_photo'].forEach(f => {
                const el = document.getElementById('cur-'+f);
                if(el) {
                    el.className = "mt-2 d-flex justify-content-center w-100";
                    if(d[f]) el.innerHTML = `<span class="badge bg-light text-primary border d-flex align-items-center"><i class="fas fa-check-circle me-1"></i>File Exists</span>`;
                    else el.innerHTML = `<span class="badge bg-light text-muted border d-flex align-items-center">No File</span>`;
                }
            });

            // Handle Location Cascade Select
            initFEQLocations().then(() => {
                const stateSelect = document.getElementById('enq_state');
                stateSelect.value = d.state_id;
                stateSelect.dispatchEvent(new Event('change'));
                
                setTimeout(() => {
                    const distSelect = document.getElementById('enq_district');
                    if(distSelect) {
                        distSelect.value = d.district_id;
                        distSelect.dispatchEvent(new Event('change'));
                        setTimeout(() => {
                            const citySelect = document.getElementById('enq_city');
                            if(citySelect) citySelect.value = d.city_id;
                        }, 500);
                    }
                }, 500);
            });

            bootstrap.Modal.getOrCreateInstance(document.getElementById('addFranchiseEnquiry')).show();
        });
    }

    window.viewLead = function(id) {
        const body = document.getElementById('view-body');
        body.innerHTML = '<div class="text-center p-5"><div class="spinner-border text-primary"></div></div>';
        bootstrap.Modal.getOrCreateInstance(document.getElementById('modalView')).show();
        fetch(HANDLER + '?action=view&id=' + id).then(r=>r.json()).then(res => {
            if(!res.success) return body.innerHTML = res.message;
            const d = res.data;
            const badge = {'new':'warning','interested':'info','converted':'success','closed':'danger'}[d.approval_status] || 'secondary';
            
            body.innerHTML = `
                <div class="enquiry-view-premium">
                    <div class="view-section mb-4">
                        <div class="view-section-header">1. Director & Center Info</div>
                        <div class="row g-3 px-3">
                            <div class="col-md-6"><label class="view-label">Director Name</label><div class="view-value fw-bold">${d.director_name}</div></div>
                            <div class="col-md-6"><label class="view-label">Qualification</label><div class="view-value text-success fw-bold">${d.qualification || '—'}</div></div>
                            <div class="col-md-6"><label class="view-label">Center Name</label><div class="view-value text-primary">${d.center_name || '—'}</div></div>
                            <div class="col-md-3"><label class="view-label">Phone</label><div class="view-value">${d.phone}</div></div>
                            <div class="col-md-3"><label class="view-label">Email</label><div class="view-value">${d.email || '—'}</div></div>
                        </div>
                    </div>
                    <div class="view-section mb-4">
                        <div class="view-section-header">2. Location Details</div>
                        <div class="row g-3 px-3">
                            <div class="col-12"><label class="view-label">Full Address</label><div class="view-value">${d.address || '—'}</div></div>
                            <div class="col-md-4"><label class="view-label">City</label><div class="view-value">${d.city_name || '—'}</div></div>
                            <div class="col-md-4"><label class="view-label">District</label><div class="view-value">${d.district_name || '—'}</div></div>
                            <div class="col-md-4"><label class="view-label">State</label><div class="view-value">${d.state_name || '—'}</div></div>
                        </div>
                    </div>
                    <div class="view-section mb-4">
                        <div class="view-section-header">3. Documents Gallery</div>
                        <div class="row g-3 px-3">
                            ${['dir_photo','dir_sig','aadhar_front','aadhar_back','labs_photo','approval_doc','center_photo'].map(f => `
                                <div class="col-md-4 text-center">
                                    <label class="view-label">${f.replace(/_/g, ' ').toUpperCase()}</label>
                                    <div class="view-value d-flex align-items-center justify-content-center">${d[f] ? `<a href="<?php echo BASE_URL; ?>media/franchise/applications/${d[f]}" target="_blank" class="badge bg-light text-primary border text-decoration-none d-flex align-items-center"><i class="fas fa-file me-1"></i>View File</a>` : '—'}</div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                    <div class="view-section mb-4">
                        <div class="view-section-header">4. Infrastructure</div>
                        <div class="row g-3 px-3">
                            <div class="col-md-3"><label class="view-label">Computers</label><div class="view-value">${d.computers} Units</div></div>
                            <div class="col-md-3"><label class="view-label">Teachers</label><div class="view-value">${d.teachers} Staff</div></div>
                            <div class="col-md-3"><label class="view-label">Rooms</label><div class="view-value">${d.rooms} Rooms</div></div>
                            <div class="col-md-3"><label class="view-label">Area (Sqft)</label><div class="view-value">${d.area_sqft} Sq. Ft.</div></div>
                        </div>
                    </div>
                    <div class="view-section">
                        <div class="view-section-header">5. Follow-up & Status</div>
                        <div class="row g-3 px-3">
                            <div class="col-md-4"><label class="view-label">Follow-up Date</label><div class="view-value">${d.followup_date || '—'}</div></div>
                            <div class="col-md-4"><label class="view-label">Est. Fees</label><div class="view-value">₹ ${d.estimate_fees}</div></div>
                            <div class="col-md-4"><label class="view-label">Status</label><div class="view-value"><span class="badge bg-${badge}">${d.approval_status.toUpperCase()}</span></div></div>
                            <div class="col-12"><label class="view-label">Notes</label><div class="view-value bg-light p-3 rounded border-start border-4 border-primary">${d.comments || '—'}</div></div>
                        </div>
                    </div>
                    <div class="text-end text-muted small mt-4 pt-2 border-top">Added On: ${d.created_at}</div>
                </div>`;
        });
    }

    window.deleteLead = function(id, name) {
        window.confirmDelete({
            target: name,
            onConfirm: function(modal, btn) {
                const fd = new FormData(); fd.append('action', 'delete'); fd.append('id', id);
                fetch(HANDLER, {method:'POST', body:fd}).then(r=>r.json()).then(res => {
                    modal.hide();
                    if(res.success) { location.reload(); showAlert(res.message); }
                    else showAlert(res.message, 'danger');
                });
            }
        });
    }

    document.getElementById('form-feq').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('btn-save-feq');
        btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving...';
        fetch(HANDLER, {method:'POST', body:new FormData(this)}).then(r=>r.json()).then(res => {
            btn.disabled = false; btn.innerHTML = '<i class="fas fa-save me-1"></i>Save Lead';
            if(res.success) { showAlert(res.message); setTimeout(()=>location.reload(), 1000); }
            else showAlert(res.message, 'danger');
        });
    });
});
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>
