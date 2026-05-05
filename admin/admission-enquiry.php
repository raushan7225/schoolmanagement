<?php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

$courses   = $pdo->query("SELECT id, name FROM courses WHERE status=1 ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
$franchises= $pdo->query("SELECT id, center_name as name FROM franchises WHERE status=1 ORDER BY center_name ASC")->fetchAll(PDO::FETCH_ASSOC);
$sessList  = $pdo->query("SELECT id, session_label FROM admission_sessions WHERE status=1 ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

// Search / Filter
$where = "WHERE e.status=1 AND e.source='manual'";
$params = [];
$search = trim($_GET['search'] ?? '');
$filter_center = (int)($_GET['center_id'] ?? 0);
$filter_status = $_GET['approval_status'] ?? '';
if ($search) { $where .= " AND (e.full_name LIKE ? OR e.mobile LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }
if ($filter_center) { $where .= " AND e.center_id=?"; $params[] = $filter_center; }
if ($filter_status) { $where .= " AND e.approval_status=?"; $params[] = $filter_status; }

$stmt = $pdo->prepare("
    SELECT e.*, c.name as course_name, ctr.center_name, sess.session_label 
    FROM enquiries e 
    LEFT JOIN courses c ON e.course_id = c.id 
    LEFT JOIN franchises ctr ON e.center_id = ctr.id 
    LEFT JOIN admission_sessions sess ON e.session_id = sess.id
    $where 
    ORDER BY e.created_at DESC
");
$stmt->execute($params);
$enquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>Admission Enquiry</h1>
    <nav><ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
        <li class="breadcrumb-item">Enquiry Management</li>
        <li class="breadcrumb-item active">Admission Enquiry</li>
    </ol></nav>
</div>



<section class="section">
<!-- Filter Bar -->
<div class="card mb-3">
    <div class="card-body py-2">
        <form class="row g-2 align-items-center" method="GET">
            <div class="col-md-5">
                <select name="center_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Centers...</option>
                    <?php foreach($franchises as $fr): ?>
                        <option value="<?php echo $fr['id']; ?>" <?php echo $filter_center == $fr['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($fr['name']); ?></option>
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
                <a href="admission-enquiry.php" class="btn btn-sm btn-outline-secondary fw-bold">RESET</a>
            </div>
        </form>
    </div>
</div>

<!-- Alert -->
<div id="eq-alert" class="d-none mb-3"></div>

<!-- Table -->
<div class="card">
    <div class="card-body pt-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle datatable-premium" data-add-btn='{"text":"Add New Enquiry","onclick":"openAdd()","icon":"fas fa-plus"}'>
                <thead class="table-light">
                    <tr>
                        <th>S.No.</th>
                        <th>Student Info</th>
                        <th>Course</th>
                        <th>Franchise</th>
                        <th>Prob. Date</th>
                        <th>Added On</th>
                        <th data-no-sort>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if(!empty($enquiries)): $sn=1; foreach($enquiries as $e): ?>
                <tr id="row-<?php echo $e['id']; ?>">
                    <td><?php echo $sn++; ?></td>
                    <td>
                        <strong><?php echo htmlspecialchars($e['full_name']); ?></strong><br>
                        <small class="text-muted"><i class="fas fa-phone-alt me-1"></i><?php echo $e['mobile']; ?></small>
                    </td>
                    <td>
                        <?php echo $e['course_name'] ?: '<span class="text-muted">N/A</span>'; ?><br>
                        <small class="text-muted">Session: <?php echo $e['session_label'] ?: '—'; ?></small>
                    </td>
                    <td><small><?php echo $e['center_name'] ?: 'N/A'; ?></small></td>
                    <td>
                        <small><?php echo $e['prob_admission_date'] ? date('d M Y', strtotime($e['prob_admission_date'])) : '<span class="text-muted">—</span>'; ?></small>
                    </td>
                    <td>
                        <small class="text-muted"><?php echo date('d M Y', strtotime($e['created_at'])); ?></small>
                    </td>
                    <td>
                        <select class="form-select form-select-sm status-select" style="width:145px;" data-id="<?php echo $e['id']; ?>">
                            <option value="new"       <?php echo $e['approval_status']=='new'?'selected':''; ?>>🟡 New</option>
                            <option value="contacted" <?php echo $e['approval_status']=='contacted'?'selected':''; ?>>🔵 Contacted</option>
                            <option value="closed"    <?php echo $e['approval_status']=='closed'?'selected':''; ?>>🔴 Closed</option>
                        </select>
                    </td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-outline-info btn-icon me-1" title="View" onclick="viewEnquiry(<?php echo $e['id']; ?>)"><i class="fas fa-eye"></i></button>
                        <button class="btn btn-sm btn-outline-warning btn-icon me-1" title="Edit" onclick="editEnquiry(<?php echo $e['id']; ?>)"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-outline-danger btn-icon" title="Delete" onclick="deleteEnquiry(<?php echo $e['id']; ?>, '<?php echo addslashes($e['full_name']); ?>')"><i class="fas fa-trash"></i></button>
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

<!-- ══ ADD / EDIT Modal ═══════════════════════════════════════════════════ -->
<div class="modal fade" id="modalEnquiry" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i><span id="modal-title-text">New Admission Enquiry</span></h5>
            <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                <i class="<?php echo v('theme_modal_close_icon', 'fas fa-times'); ?>"></i>
            </button>
        </div>
        <form id="form-enquiry" novalidate>
            <input type="hidden" id="eq-id" name="id" value="">
            <input type="hidden" name="action" id="eq-action" value="add">
            <input type="hidden" name="source" value="manual">
            <div class="modal-body px-3 py-2" style="max-height: 80vh; overflow-y: auto; overflow-x: hidden;">
                
                <!-- Section 1: Academic Interest -->
                <div class="row g-3">
                    <div class="col-12"><div class="form-section-header">1. Academic Interest</div></div>
                    
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Preferred State <span class="text-danger">*</span></label>
                        <select class="form-select" name="state" id="eq-state" required>
                            <option value="">Select State</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Preferred District <span class="text-danger">*</span></label>
                        <select class="form-select" name="district" id="eq-district" required disabled>
                            <option value="">Select State First</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Center / Franchise <span class="text-danger">*</span></label>
                        <select class="form-select" name="center_id" id="eq-center_id" required disabled>
                            <option value="">Select District First</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Session <span class="text-danger">*</span></label>
                        <select class="form-select" name="session_id" id="eq-session_id" required>
                            <option value="">Select Session</option>
                            <?php foreach($sessList as $s) echo "<option value='{$s['id']}'>".htmlspecialchars($s['session_label'])."</option>"; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Course Category <span class="text-danger">*</span></label>
                        <select class="form-select" name="course_category" id="eq-course_category" required>
                            <option value="">Select Category</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Target Course <span class="text-danger">*</span></label>
                        <select class="form-select" name="course_id" id="eq-course_id" required disabled>
                            <option value="">Select Category First</option>
                        </select>
                    </div>

                    <!-- Section 2: Personal Information -->
                    <div class="col-12 mt-4"><div class="form-section-header">2. Personal Information</div></div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Student Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="full_name" id="eq-full_name" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold small">Mobile <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="mobile" id="eq-mobile" maxlength="10" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold small">Email</label>
                        <input type="email" class="form-control" name="email" id="eq-email">
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label fw-bold small">Gender</label>
                        <select class="form-select" name="gender" id="eq-gender">
                            <option value="">Select</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold small">Date of Birth</label>
                        <input type="date" class="form-control" name="dob" id="eq-dob">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold small">Qualification <span class="text-danger">*</span></label>
                        <select class="form-select" name="qualification" id="eq-qualification" required>
                            <option value="">Select</option>
                            <option value="10th">10th</option>
                            <option value="12th">12th</option>
                            <option value="Graduation">Graduation</option>
                            <option value="Diploma">Diploma</option>
                            <option value="Others">Others</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold small">Probable Adm. Date</label>
                        <input type="date" class="form-control" name="prob_admission_date" id="eq-pad">
                    </div>

                    <!-- Section 3: Address & Message -->
                    <div class="col-12 mt-4"><div class="form-section-header">3. Address & Message</div></div>
                    
                    <div class="col-md-12">
                        <label class="form-label fw-bold small">Street Address <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="address" id="eq-address" rows="1" required></textarea>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label fw-bold small">Country <span class="text-danger">*</span></label>
                        <select class="form-select" name="country_id" id="eq-country_id" required>
                            <option value="">Select Country</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold small">State <span class="text-danger">*</span></label>
                        <select class="form-select" name="state_id" id="eq-state_id" required disabled>
                            <option value="">Select State</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold small">District <span class="text-danger">*</span></label>
                        <select class="form-select" name="district_id" id="eq-district_id" required disabled>
                            <option value="">Select District</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold small">City <span class="text-danger">*</span></label>
                        <select class="form-select" name="city_id" id="eq-city_id" required disabled>
                            <option value="">Select City</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label fw-bold small">Pin Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="pincode" id="eq-pincode" maxlength="6" required>
                    </div>

                    <div class="col-md-9">
                        <label class="form-label fw-bold small">Query / Note</label>
                        <textarea class="form-control" name="message" id="eq-message" rows="1" placeholder="Admin note or student's query..."></textarea>
                    </div>

                    <!-- Only show in Edit mode -->
                    <div class="col-md-12 d-none" id="status-field-wrap">
                        <label class="form-label fw-bold small">Enquiry Status</label>
                        <select class="form-select" name="approval_status" id="eq-approval_status">
                            <option value="new">New</option>
                            <option value="contacted">Contacted</option>
                            <option value="closed">Closed</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary px-4" id="btn-save-enquiry">
                    <i class="fas fa-save me-1"></i>Save Enquiry
                </button>
            </div>
        </form>
    </div>
  </div>
</div>

<!-- ══ VIEW Modal ════════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalView" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title"><i class="fas fa-user me-2"></i>Enquiry Details</h5>
            <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                <i class="<?php echo v('theme_modal_close_icon', 'fas fa-times'); ?>"></i>
            </button>
        </div>
        <div class="modal-body px-3 py-2" id="view-body" style="max-height: 80vh; overflow-y: auto; overflow-x: hidden;">
            <div class="text-center py-5"><div class="spinner-border text-primary"></div></div>
        </div>
    </div>
  </div>
</div>



<script>
const HANDLER = '<?php echo BASE_URL; ?>ajax/enquiry_handler.php';

// ─── Alert helper ─────────────────────────────────────────────────────────
function showAlert(msg, type='success') {
    const el = document.getElementById('eq-alert');
    el.className = `mb-3 alert alert-${type}`;
    el.textContent = msg;
    el.scrollIntoView({behavior:'smooth', block:'nearest'});
    setTimeout(()=>{ el.className='d-none'; }, 4000);
}

// ─── Open Add Modal ───────────────────────────────────────────────────────
function openAdd() {
    document.getElementById('form-enquiry').reset();
    document.getElementById('form-enquiry').classList.remove('was-validated');
    document.getElementById('eq-id').value = '';
    document.getElementById('eq-action').value = 'add';
    document.getElementById('modal-title-text').textContent = 'New Admission Enquiry';
    document.getElementById('status-field-wrap').classList.add('d-none');
    
    // Reset dependent dropdowns
    ['eq-district','eq-center_id','eq-course_id','eq-state_id','eq-district_id','eq-city_id'].forEach(id => {
        const el = document.getElementById(id);
        if(el) { el.disabled = true; el.innerHTML = '<option value="">Select Parent First</option>'; }
    });

    // Explicitly show modal
    const modalEl = document.getElementById('modalEnquiry');
    bootstrap.Modal.getOrCreateInstance(modalEl).show();
}

// ─── Location & Course Logic (Modal) ──────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    // Load Countries
    fetch('../ajax/get_locations.php?type=countries').then(r => r.json()).then(data => {
        const el = document.getElementById('eq-country_id');
        if(el) data.forEach(c => el.innerHTML += `<option value="${c.id}">${c.name}</option>`);
    });

    // Load Academic States
    fetch('../ajax/get_locations.php?type=center_states').then(r => r.json()).then(data => {
        const el = document.getElementById('eq-state');
        if(el) data.forEach(s => el.innerHTML += `<option value="${s}">${s}</option>`);
    });

    // Load Categories
    fetch('../ajax/get_courses.php?type=categories').then(r => r.json()).then(data => {
        const el = document.getElementById('eq-course_category');
        if(el) data.forEach(c => el.innerHTML += `<option value="${c}">${c.toUpperCase()}</option>`);
    });

    // Academic Cascades
    document.getElementById('eq-state').addEventListener('change', function() {
        const dist = document.getElementById('eq-district');
        dist.disabled = !this.value; dist.innerHTML = '<option value="">Loading...</option>';
        if(this.value) fetch(`../ajax/get_locations.php?type=center_districts&state=${this.value}`)
            .then(r => r.json()).then(data => {
                dist.innerHTML = '<option value="">Select District</option>';
                data.forEach(d => dist.innerHTML += `<option value="${d}">${d}</option>`);
            });
    });
    document.getElementById('eq-district').addEventListener('change', function() {
        const ctr = document.getElementById('eq-center_id');
        ctr.disabled = !this.value; ctr.innerHTML = '<option value="">Loading...</option>';
        if(this.value) fetch(`../ajax/get_locations.php?type=centers&district_name=${this.value}`)
            .then(r => r.json()).then(data => {
                ctr.innerHTML = '<option value="">Select Center</option>';
                data.forEach(c => ctr.innerHTML += `<option value="${c.id}">${c.name}</option>`);
            });
    });
    document.getElementById('eq-course_category').addEventListener('change', function() {
        const crs = document.getElementById('eq-course_id');
        crs.disabled = !this.value; crs.innerHTML = '<option value="">Loading...</option>';
        if(this.value) fetch(`../ajax/get_courses.php?type=courses&category=${this.value}`)
            .then(r => r.json()).then(data => {
                crs.innerHTML = '<option value="">Select Course</option>';
                data.forEach(c => crs.innerHTML += `<option value="${c.id}">${c.name}</option>`);
            });
    });

    // Address Cascades
    document.getElementById('eq-country_id').addEventListener('change', function() {
        const st = document.getElementById('eq-state_id');
        st.disabled = !this.value; st.innerHTML = '<option value="">Loading...</option>';
        if(this.value) fetch(`../ajax/get_locations.php?type=states&country_id=${this.value}`)
            .then(r => r.json()).then(data => {
                st.innerHTML = '<option value="">Select State</option>';
                data.forEach(d => st.innerHTML += `<option value="${d.id}">${d.name}</option>`);
            });
    });
    document.getElementById('eq-state_id').addEventListener('change', function() {
        const dist = document.getElementById('eq-district_id');
        dist.disabled = !this.value; dist.innerHTML = '<option value="">Loading...</option>';
        if(this.value) fetch(`../ajax/get_locations.php?type=districts&state_id=${this.value}`)
            .then(r => r.json()).then(data => {
                dist.innerHTML = '<option value="">Select District</option>';
                data.forEach(d => dist.innerHTML += `<option value="${d.id}">${d.name}</option>`);
            });
    });
    document.getElementById('eq-district_id').addEventListener('change', function() {
        const city = document.getElementById('eq-city_id');
        city.disabled = !this.value; city.innerHTML = '<option value="">Loading...</option>';
        if(this.value) fetch(`../ajax/get_locations.php?type=cities&district_id=${this.value}`)
            .then(r => r.json()).then(data => {
                city.innerHTML = '<option value="">Select City</option>';
                data.forEach(c => city.innerHTML += `<option value="${c.id}">${c.name}</option>`);
            });
    });
});

// ─── Edit ─────────────────────────────────────────────────────────────────
async function editEnquiry(id) {
    const r = await fetch(HANDLER + '?action=view&id=' + id);
    const res = await r.json();
    if (res.status !== 'success') { showAlert(res.message, 'danger'); return; }
    const d = res.data;

    document.getElementById('form-enquiry').reset();
    document.getElementById('form-enquiry').classList.remove('was-validated');
    document.getElementById('eq-id').value       = d.id;
    document.getElementById('eq-action').value   = 'edit';
    document.getElementById('modal-title-text').textContent = 'Edit Enquiry — ' + d.full_name;
    
    // Simple Fields
    document.getElementById('eq-full_name').value  = d.full_name;
    document.getElementById('eq-mobile').value     = d.mobile;
    document.getElementById('eq-email').value      = d.email || '';
    document.getElementById('eq-gender').value     = d.gender || '';
    document.getElementById('eq-dob').value        = d.dob || '';
    document.getElementById('eq-qualification').value = d.qualification || '';
    document.getElementById('eq-pad').value        = d.prob_admission_date || '';
    document.getElementById('eq-session_id').value = d.session_id || '';
    document.getElementById('eq-address').value    = d.address || '';
    document.getElementById('eq-pincode').value    = d.pincode || '';
    document.getElementById('eq-message').value    = d.message || '';
    document.getElementById('eq-approval_status').value = d.approval_status;
    document.getElementById('status-field-wrap').classList.remove('d-none');

    // Cascading Dropdowns (Academic)
    (async () => {
        try {
            // Load States first if they aren't loaded
            const stateEl = document.getElementById('eq-state');
            if (stateEl.options.length <= 1) {
                const rState = await fetch('../ajax/get_locations.php?type=center_states');
                const states = await rState.json();
                stateEl.innerHTML = '<option value="">Select State</option>' + states.map(s => `<option value="${s}">${s}</option>`).join('');
            }
            
            if (d.state) {
                stateEl.value = d.state;
                // Load Districts
                const distEl = document.getElementById('eq-district');
                const rDist = await fetch(`../ajax/get_locations.php?type=center_districts&state=${encodeURIComponent(d.state)}`);
                const districts = await rDist.json();
                distEl.innerHTML = '<option value="">Select District</option>' + districts.map(dst => `<option value="${dst}">${dst}</option>`).join('');
                distEl.disabled = false;
                distEl.value = d.district;
                
                // Load Centers
                if (d.district) {
                    const centerEl = document.getElementById('eq-center_id');
                    const rCenter = await fetch(`../ajax/get_locations.php?type=centers&district_name=${encodeURIComponent(d.district)}`);
                    const centers = await rCenter.json();
                    centerEl.innerHTML = '<option value="">Select Center</option>' + centers.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
                    centerEl.disabled = false;
                    centerEl.value = d.center_id;
                }
            }

            // Course Category
            const catEl = document.getElementById('eq-course_category');
            if (catEl.options.length <= 1) {
                const rCat = await fetch(`../ajax/get_courses.php?type=categories`);
                const cats = await rCat.json();
                catEl.innerHTML = '<option value="">Select Category</option>' + cats.map(c => `<option value="${c}">${c.toUpperCase()}</option>`).join('');
            }
            
            if (d.course_category) {
                catEl.value = d.course_category;
                const courseEl = document.getElementById('eq-course_id');
                const rCourse = await fetch(`../ajax/get_courses.php?type=courses&category=${encodeURIComponent(d.course_category)}`);
                const courses = await rCourse.json();
                courseEl.innerHTML = '<option value="">Select Course</option>' + courses.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
                courseEl.disabled = false;
                courseEl.value = d.course_id;
            }

            // Address Locations (Country/State/District/City)
            const countryEl = document.getElementById('eq-country_id');
            if (countryEl.options.length <= 1) {
                const rCountry = await fetch('../ajax/get_locations.php?type=countries');
                const countries = await rCountry.json();
                countryEl.innerHTML = '<option value="">Select Country</option>' + countries.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
            }
            countryEl.value = d.country_id || 1;

            if (d.country_id || 1) {
                const stEl = document.getElementById('eq-state_id');
                const rSt = await fetch(`../ajax/get_locations.php?type=states&country_id=${d.country_id || 1}`);
                const states = await rSt.json();
                stEl.innerHTML = '<option value="">Select State</option>' + states.map(s => `<option value="${s.id}">${s.name}</option>`).join('');
                stEl.disabled = false;
                stEl.value = d.state_id;

                if (d.state_id) {
                    const dstEl = document.getElementById('eq-district_id');
                    const rDst = await fetch(`../ajax/get_locations.php?type=districts&state_id=${d.state_id}`);
                    const districts = await rDst.json();
                    dstEl.innerHTML = '<option value="">Select District</option>' + districts.map(dst => `<option value="${dst.id}">${dst.name}</option>`).join('');
                    dstEl.disabled = false;
                    dstEl.value = d.district_id;

                    if (d.district_id) {
                        const cityEl = document.getElementById('eq-city_id');
                        const rCity = await fetch(`../ajax/get_locations.php?type=cities&district_id=${d.district_id}`);
                        const cities = await rCity.json();
                        cityEl.innerHTML = '<option value="">Select City</option>' + cities.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
                        cityEl.disabled = false;
                        cityEl.value = d.city_id;
                    }
                }
            }
        } catch (err) {
            console.error("Error in edit modal population:", err);
        }
    })();

    new bootstrap.Modal(document.getElementById('modalEnquiry')).show();
}

// ─── View ─────────────────────────────────────────────────────────────────
function viewEnquiry(id) {
    document.getElementById('view-body').innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>';
    new bootstrap.Modal(document.getElementById('modalView')).show();
    fetch(HANDLER + '?action=view&id=' + id)
    .then(r => r.json()).then(res => {
        if (res.status !== 'success') { document.getElementById('view-body').innerHTML = '<p class="text-danger">'+res.message+'</p>'; return; }
        const d = res.data;
        const badge = {'new':'warning','contacted':'info','closed':'danger'}[d.approval_status] || 'secondary';
        
        document.getElementById('view-body').innerHTML = `
        <div class="enquiry-view-premium p-2">
            
            <!-- Section 1: Academic Interest -->
            <div class="view-section mb-4">
                <div class="view-section-header"><i class="fas fa-map-marker-alt me-2"></i>1. PREFERRED LOCATION & COURSE</div>
                <div class="row g-3 px-3 py-2">
                    <div class="col-md-4">
                        <label class="view-label">State</label>
                        <div class="view-value">${d.state || '—'}</div>
                    </div>
                    <div class="col-md-4">
                        <label class="view-label">District</label>
                        <div class="view-value">${d.district || '—'}</div>
                    </div>
                    <div class="col-md-4">
                        <label class="view-label">Franchise / Center</label>
                        <div class="view-value">${d.center_name || '—'}</div>
                    </div>
                    <div class="col-md-4">
                        <label class="view-label">Academic Session</label>
                        <div class="view-value">${d.session_label || '—'}</div>
                    </div>
                    <div class="col-md-4">
                        <label class="view-label">Course Category</label>
                        <div class="view-value">${(d.course_category || '—').toUpperCase()}</div>
                    </div>
                    <div class="col-md-4">
                        <label class="view-label">Target Course</label>
                        <div class="view-value text-primary fw-bold">${d.course_name || '—'}</div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Personal Details -->
            <div class="view-section mb-4">
                <div class="view-section-header"><i class="fas fa-user me-2"></i>2. PERSONAL DETAILS</div>
                <div class="row g-3 px-3 py-2">
                    <div class="col-md-6">
                        <label class="view-label">Full Name</label>
                        <div class="view-value fw-bold">${d.full_name}</div>
                    </div>
                    <div class="col-md-3">
                        <label class="view-label">Date of Birth</label>
                        <div class="view-value">${d.dob || '—'}</div>
                    </div>
                    <div class="col-md-3">
                        <label class="view-label">Gender</label>
                        <div class="view-value">${(d.gender || '—').toUpperCase()}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="view-label">Email Address</label>
                        <div class="view-value">${d.email || '—'}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="view-label">Phone Number</label>
                        <div class="view-value">${d.mobile}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="view-label">Current Qualification</label>
                        <div class="view-value text-success fw-bold">${d.qualification || '—'}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="view-label">Probable Admission Date</label>
                        <div class="view-value">${d.prob_admission_date || '—'}</div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Address & Query -->
            <div class="view-section">
                <div class="view-section-header"><i class="fas fa-home me-2"></i>3. ADDRESS & QUERY</div>
                <div class="row g-3 px-3 py-2">
                    <div class="col-12">
                        <label class="view-label">Street Address</label>
                        <div class="view-value">${d.address || '—'}</div>
                    </div>
                    <div class="col-md-3">
                        <label class="view-label">Country</label>
                        <div class="view-value">India</div>
                    </div>
                    <div class="col-md-3">
                        <label class="view-label">State</label>
                        <div class="view-value">${d.state_name || '—'}</div>
                    </div>
                    <div class="col-md-3">
                        <label class="view-label">District</label>
                        <div class="view-value">${d.district_name || '—'}</div>
                    </div>
                    <div class="col-md-3">
                        <label class="view-label">City</label>
                        <div class="view-value">${d.city_name || '—'}</div>
                    </div>
                    <div class="col-md-3">
                        <label class="view-label">Pin Code</label>
                        <div class="view-value">${d.pincode || '—'}</div>
                    </div>
                    <div class="col-md-9">
                        <label class="view-label">Status</label>
                        <div class="view-value"><span class="badge bg-${badge}">${d.approval_status.toUpperCase()}</span></div>
                    </div>
                    <div class="col-12">
                        <label class="view-label">Your Query / Message</label>
                        <div class="view-value bg-light p-3 rounded-3 border-start border-4 border-primary">
                            ${d.message || '<span class="text-muted italic">No query provided.</span>'}
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-end text-muted small mt-4 pt-2 border-top">
                Source: <strong>${d.source.toUpperCase()}</strong> | Added On: <strong>${d.created_at}</strong>
            </div>
        </div>`;
    });
}

function deleteEnquiry(id, name) {
    window.confirmDelete({
        target: name,
        onConfirm: function(modal, btn) {
            const fd = new FormData(); 
            fd.append('action','delete'); 
            fd.append('id', id);
            fetch(HANDLER, {method:'POST', body:fd})
            .then(r=>r.json()).then(res => {
                modal.hide();
                if (res.status === 'success') {
                    Swal.fire({ icon: 'success', title: 'Deleted!', text: res.message, timer: 1500, showConfirmButton: false })
                    .then(() => location.reload());
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            });
        }
    });
}

// ─── Inline Status Change ─────────────────────────────────────────────────
document.querySelectorAll('.status-select').forEach(function(sel) {
    sel.addEventListener('change', function() {
        const fd = new FormData();
        fd.append('action','change_status');
        fd.append('id', this.dataset.id);
        fd.append('approval_status', this.value);
        fetch(HANDLER, {method:'POST', body:fd})
        .then(r=>r.json()).then(res => {
            showAlert(res.message, res.status === 'success' ? 'success' : 'danger');
        });
    });
});

// ─── Form Submit (Add / Edit) with client-side validation ─────────────────
document.getElementById('form-enquiry').addEventListener('submit', function(e) {
    e.preventDefault();
    this.classList.add('was-validated');
    if (!this.checkValidity()) {
        showAlert('Please fix the highlighted errors.', 'danger');
        return;
    }
    const btn = document.getElementById('btn-save-enquiry');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving...';

    fetch(HANDLER, {method:'POST', body: new FormData(this)})
    .then(r=>r.json()).then(res => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save me-1"></i>Save Enquiry';
        if (res.status === 'success') {
            bootstrap.Modal.getInstance(document.getElementById('modalEnquiry')).hide();
            showAlert(res.message, 'success');
            setTimeout(()=>location.reload(), 1200);
        } else {
            showAlert(res.message, 'danger');
        }
    }).catch(()=>{
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save me-1"></i>Save Enquiry';
        showAlert('Network error. Please try again.', 'danger');
    });
});
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>
