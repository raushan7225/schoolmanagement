<?php
// admin/franchise-list.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Fetch partners for filter
$partners = $pdo->query("SELECT id, full_name FROM partners WHERE status != -1 ORDER BY full_name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Search / Filter
$where = "WHERE f.status=1";
$params = [];
$search = trim($_GET['search'] ?? '');
$filter_partner = (int)($_GET['partner_id'] ?? 0);

if ($search) {
    $where .= " AND (f.center_code LIKE ? OR f.center_name LIKE ? OR f.director_name LIKE ?)";
    $params[] = "%$search%"; $params[] = "%$search%"; $params[] = "%$search%";
}
if ($filter_partner) {
    $where .= " AND f.partner_id = ?";
    $params[] = $filter_partner;
}

$stmt = $pdo->prepare("
    SELECT f.*, u.username as partner_name, p.full_name as partner_real_name
    FROM franchises f 
    LEFT JOIN users u ON f.user_id = u.id 
    LEFT JOIN partners p ON f.partner_id = p.id
    $where 
    ORDER BY f.created_at DESC
");
$stmt->execute($params);
$franchise_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>Franchise / Center List</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Franchise Management</li>
            <li class="breadcrumb-item active">Franchise List</li>
        </ol>
    </nav>
</div>
<style>
.view-section-header {
    background: #f0f2ff;
    border-left: 5px solid #012970;
    padding: 10px 15px;
    font-weight: 700;
    font-size: 0.85rem;
    color: #012970;
    letter-spacing: 0.5px;
    margin-bottom: 20px;
    border-radius: 0 6px 6px 0;
    text-transform: uppercase;
}
.view-label {
    display: block;
    font-size: 0.75rem;
    font-weight: 700;
    color: #4154f1;
    margin-bottom: 5px;
    text-transform: uppercase;
}
.view-value {
    font-size: 0.95rem;
    color: #444;
    min-height: 24px;
    padding-bottom: 8px;
}
</style>

<section class="section">
    <!-- Filter Bar -->
    <div class="card mb-3">
        <div class="card-body py-2">
            <form class="row g-2 align-items-center" method="GET">
                <div class="col-md-9">
                    <select name="partner_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Partners...</option>
                        <?php foreach($partners as $p): ?>
                            <option value="<?php echo $p['id']; ?>" <?php echo $filter_partner == $p['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($p['full_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary flex-grow-1 fw-bold"><i class="fas fa-filter me-2"></i>APPLY</button>
                    <a href="franchise-list.php" class="btn btn-sm btn-outline-secondary fw-bold">RESET</a>
                </div>
            </form>
        </div>
    </div>


<!-- Table -->
<div class="card">
    <div class="card-body pt-3">

        <div class="table-responsive">
            <table class="table table-hover align-middle datatable-premium"
                   data-add-btn='{"text":"Add New Franchise","onclick":"new bootstrap.Modal(document.getElementById(\"modalAddFranchise\")).show()","icon":"fas fa-plus"}'>
                <thead class="table-light">
                    <tr>
                        <th>S.No.</th>
                        <th>Center Info</th>
                        <th>Director / Contact</th>
                        <th>Partner</th>
                        <th>Wallet Balance</th>
                        <th data-no-sort>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                            <tbody>
                                <?php if(!empty($franchise_list)): $sn=1; foreach($franchise_list as $f): ?>
                                <tr id="frow-<?php echo $f['id']; ?>">
                                    <td><?php echo $sn++; ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded p-2 me-2 border">
                                                <i class="fas fa-building text-primary"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-primary-theme"><?php echo strtoupper($f['center_name']); ?></div>
                                                <div class="text-muted small">Code: <?php echo $f['center_code']; ?> || Joined: <?php echo date('d M Y', strtotime($f['created_at'])); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold"><?php echo strtoupper($f['director_name']); ?></div>
                                        <small class="text-muted"><i class="fas fa-phone-alt me-1"></i><?php echo $f['phone']; ?></small>
                                    </td>
                                    <td><span class="badge bg-light text-dark border"><?php echo $f['partner_name'] ?: 'SYSTEM'; ?></span></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="fw-bold text-success me-2">&#8377; <span id="balance-<?php echo $f['id']; ?>"><?php echo number_format($f['wallet_balance'], 2); ?></span></span>
                                            <button class="btn btn-xs btn-success me-1 py-1 px-1" title="Credit Balance" onclick="openWalletModal('Credit', <?php echo $f['id']; ?>, '<?php echo $f['center_code']; ?>')"><i class="fas fa-plus" style="font-size: 10px;"></i></button>
                                            <button class="btn btn-xs btn-danger py-1 px-1" title="Debit Balance" onclick="openWalletModal('Debit', <?php echo $f['id']; ?>, '<?php echo $f['center_code']; ?>')"><i class="fas fa-minus" style="font-size: 10px;"></i></button>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $f['status'] == 1 ? 'success' : 'danger'; ?> rounded-pill">
                                            <?php echo $f['status'] == 1 ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-info btn-icon me-1" title="View Details" onclick="viewFranchise(<?php echo $f['id']; ?>)"><i class="fas fa-eye"></i></button>
                                        <a href="franchise-document.php?id=<?php echo $f['id']; ?>" class="btn btn-sm btn-outline-primary btn-icon me-1" title="Documents"><i class="fas fa-file-alt"></i></a>
                                        <a href="edit-franchise.php?id=<?php echo $f['id']; ?>" class="btn btn-sm btn-outline-warning btn-icon me-1" title="Edit"><i class="fas fa-edit"></i></a>
                                        <button class="btn btn-sm btn-outline-danger btn-icon" title="Delete" onclick="deleteFranchise(<?php echo $f['id']; ?>, '<?php echo addslashes($f['center_name']); ?>')"><i class="fas fa-trash"></i></button>
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

<!-- Wallet Adjustment Modal -->
<div class="modal fade" id="walletAdjust" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="adjustTitle"><i class="fas fa-wallet me-2"></i>Adjust Franchise Balance</h5>
                <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                    <i class="<?php echo v('theme_modal_close_icon', 'fas fa-times'); ?>"></i>
                </button>
            </div>
            <form id="form-wallet">
                <input type="hidden" id="adjustFranchiseId" name="franchise_id">
                <input type="hidden" id="adjustType" name="type">
                <div class="modal-body">
                    <div class="enquiry-view-premium p-2">
                        <div class="view-section mb-4">
                            <div class="view-section-header">1. ADJUSTMENT DETAILS</div>
                            <div class="row g-3 px-3">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold small text-muted">AMOUNT (&#8377;) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light fw-bold">&#8377;</span>
                                        <input type="number" step="0.01" class="form-control form-control-lg fw-bold text-dark" name="amount" placeholder="0.00" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="view-section">
                            <div class="view-section-header">2. REMARKS / DOCUMENTATION</div>
                            <div class="px-3">
                                <label class="form-label fw-bold small text-muted">REASON FOR ADJUSTMENT</label>
                                <textarea class="form-control" name="description" rows="3" placeholder="e.g. Manual fund addition by admin for referral bonus..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" id="adjustBtn">Confirm Adjustment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Franchise Modal -->
<div class="modal fade" id="modalView" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-building me-2"></i>Franchise Details</h5>
                <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                    <i class="<?php echo v('theme_modal_close_icon', 'fas fa-times'); ?>"></i>
                </button>
            </div>
            <div class="modal-body px-3 py-2" id="view-body" style="max-height: 80vh; overflow-y: auto; overflow-x: hidden;">
                <div class="text-center py-5"><div class="spinner-border text-primary"></div></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<!-- Add Franchise Modal -->
<div class="modal fade" id="modalAddFranchise" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Add New Franchise</h5>
                <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                    <i class="<?php echo v('theme_modal_close_icon', 'fas fa-times'); ?>"></i>
                </button>
            </div>
            <form id="add-franchise-form" novalidate enctype="multipart/form-data">
                <div class="modal-body px-4 py-4" style="max-height: 80vh; overflow-y: auto; overflow-x: hidden;">

                <div class="row g-3">
                            <div class="col-12"><div class="form-section-header"><i class="fas fa-building me-2"></i>1. CENTER INFORMATION</div></div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark">Center Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="center_name" placeholder="e.g. Modern Tech Institute" required>
                                <div class="invalid-feedback">Center name is required.</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark">Center Code <span class="text-muted small">(Auto-generated)</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-hashtag"></i></span>
                                    <input type="text" class="form-control bg-light fw-bold" name="center_code" value="ICSTIR<?php echo rand(1000, 9999); ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark">Master Partner</label>
                                <select class="form-select select2-basic" name="partner_id">
                                    <option value="">Select Partner (Optional)</option>
                                    <?php foreach($partners as $p): ?>
                                        <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['full_name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark">Center Phone <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-phone-alt"></i></span>
                                    <input type="text" class="form-control" name="center_phone" placeholder="10 Digit Mobile" maxlength="10" required>
                                </div>
                                <div class="invalid-feedback">Center phone is required.</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark">Center Email <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control" name="center_email" placeholder="center@example.com" required>
                                </div>
                                <div class="invalid-feedback">A valid email is required.</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark">Alternate Phone</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-phone"></i></span>
                                    <input type="text" class="form-control" name="phone_alt" placeholder="Optional Mobile" maxlength="10">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark">Director Qualification</label>
                                <input type="text" class="form-control" name="qualification" placeholder="e.g. Graduate/MCA">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark">Establishment Date</label>
                                <input type="date" class="form-control" name="estd_date" value="<?php echo date('Y-m-d'); ?>">
                            </div>
                        <div class="col-12"><div class="form-section-header mt-4"><i class="fas fa-user-tie me-2"></i>2. DIRECTOR DETAILS</div></div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark">Director Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="director_name" placeholder="Full Name" required>
                                <div class="invalid-feedback">Director name is required.</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark">Director Mobile <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-mobile-alt"></i></span>
                                    <input type="text" class="form-control" name="director_mobile" placeholder="10 Digit Mobile" maxlength="10" required>
                                </div>
                                <div class="invalid-feedback">Director mobile is required.</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark">Aadhar Number</label>
                                <input type="text" class="form-control" name="aadhar_no" placeholder="12 Digit Aadhar" maxlength="12">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark">Director Photo</label>
                                <input type="file" class="form-control" name="director_photo" accept="image/*">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark">Director Signature</label>
                                <input type="file" class="form-control" name="signature" accept="image/*">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark">Aadhar Card (Front)</label>
                                <input type="file" class="form-control" name="aadhar_front" accept="image/*,application/pdf">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark">Aadhar Card (Back)</label>
                                <input type="file" class="form-control" name="aadhar_back" accept="image/*,application/pdf">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark">Center Approval Doc</label>
                                <input type="file" class="form-control" name="approval_doc">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark">Other ID Proof</label>
                                <input type="file" class="form-control" name="id_proof">
                            </div>
                        <div class="col-12"><div class="form-section-header mt-4"><i class="fas fa-map-marker-alt me-2"></i>3. LOCATION & ADDRESS</div></div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold text-dark">Full Address <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="address" rows="2" placeholder="Building No., Street, Landmark..." required></textarea>
                                <div class="invalid-feedback">Address is required.</div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-dark">State <span class="text-danger">*</span></label>
                                <select class="form-select" id="franchise_state" name="state_id" required>
                                    <option value="">Loading...</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-dark">District <span class="text-danger">*</span></label>
                                <select class="form-select" id="franchise_district" name="district_id" disabled required>
                                    <option value="">Select State</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-dark">City / Town</label>
                                <select class="form-select" id="franchise_city" name="city_id" disabled>
                                    <option value="">Select District</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-dark">Pincode</label>
                                <input type="text" class="form-control" name="pincode" maxlength="6">
                            </div>
                        <div class="col-12"><div class="form-section-header mt-4"><i class="fas fa-laptop-house me-2"></i>4. INFRASTRUCTURE</div></div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-dark">Computers Count</label>
                                <input type="number" class="form-control" name="computers" value="5">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-dark">Staff Count</label>
                                <input type="number" class="form-control" name="teachers" value="2">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-dark">Total Rooms</label>
                                <input type="number" class="form-control" name="rooms" value="1">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-dark">Area (Sq. Ft.)</label>
                                <input type="number" class="form-control" name="area_sqft" value="500">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark">Center Front Photo</label>
                                <input type="file" class="form-control" name="photo_front">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark">Lab Photo</label>
                                <input type="file" class="form-control" name="photo_lab">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark">Reception Photo</label>
                                <input type="file" class="form-control" name="photo_office">
                            </div>
                        <div class="col-12"><div class="form-section-header mt-4"><i class="fas fa-lock me-2"></i>5. ACCESS CREDENTIALS</div></div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark">Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-key"></i></span>
                                    <input type="password" class="form-control" name="password" id="p-pass" placeholder="Set Access Password" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePass('p-pass')"><i class="fas fa-eye"></i></button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark">Account Status</label>
                                <select class="form-select" name="status">
                                    <option value="1">Active</option>
                                    <option value="0">Pending Approval</option>
                                </select>
                            </div>
                        </div>
                </div>
                <div class="modal-footer bg-light border-top py-2">
                    <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">CANCEL</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold" id="btn-submit">
                        <i class="fas fa-save me-2"></i>REGISTER FRANCHISE
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const HANDLER = '<?php echo BASE_URL; ?>ajax/franchise_handler.php';

function togglePass(id) {
    const el = document.getElementById(id);
    el.type = el.type === "password" ? "text" : "password";
}

document.addEventListener("DOMContentLoaded", function () {
    if(typeof initLocationCascade === "function") {
        initLocationCascade({
            stateEl   : "#franchise_state",
            districtEl: "#franchise_district",
            cityEl    : "#franchise_city"
        });
    }

    const formAdd = document.getElementById("add-franchise-form");
    if(formAdd) {
        formAdd.addEventListener("submit", function (e) {
            e.preventDefault();
            if (!formAdd.checkValidity()) { formAdd.classList.add("was-validated"); return; }

            const btn = document.getElementById("btn-submit");
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>REGISTERING...';

            const fd = new FormData(formAdd);
            fd.append("action", "add_franchise");
            
            fetch(HANDLER, { method: "POST", body: fd })
            .then(r => r.json()).then(res => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save me-2"></i>REGISTER FRANCHISE';
                if(res.success) {
                    if(typeof Swal !== "undefined") {
                        Swal.fire({ icon: "success", title: "Success", text: res.message, timer: 1500, showConfirmButton: false }).then(() => location.reload());
                    } else {
                        alert(res.message); location.reload();
                    }
                } else {
                    if(typeof Swal !== "undefined") {
                        Swal.fire("Error", res.message, "error");
                    } else {
                        alert(res.message);
                    }
                }
            });
        });
    }
});


function viewFranchise(id) {
    document.getElementById('view-body').innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>';
    new bootstrap.Modal(document.getElementById('modalView')).show();
    
    fetch(HANDLER + '?action=get&id=' + id)
    .then(r => r.json())
    .then(res => {
        if (!res.success) { 
            document.getElementById('view-body').innerHTML = '<p class="text-danger">' + res.message + '</p>'; 
            return; 
        }
        const d = res.data;
        const formatVal = (v) => v || '—';
        const docLink = (f, dir) => d[f] ? `<a href="<?php echo BASE_URL; ?>media/franchise/${dir}/${d[f]}" target="_blank" class="badge bg-light text-primary border text-decoration-none d-flex align-items-center"><i class="fas fa-file me-1"></i>View File</a>` : '—';

        document.getElementById('view-body').innerHTML = `
            <div class="enquiry-view-premium p-2">
                <div class="view-section mb-4">
                    <div class="view-section-header">1. DIRECTOR & CENTER INFORMATION</div>
                    <div class="row g-3 px-3">
                        <div class="col-md-4"><label class="view-label">Center Name</label><div class="view-value fw-bold text-primary">${d.center_name}</div></div>
                        <div class="col-md-4"><label class="view-label">Center Code</label><div class="view-value fw-bold">${d.center_code}</div></div>
                        <div class="col-md-4"><label class="view-label">Director Name</label><div class="view-value fw-bold">${d.director_name}</div></div>
                        <div class="col-md-4"><label class="view-label">Master Partner</label><div class="view-value"><span class="badge bg-info-light text-info border">${formatVal(d.partner_full_name)}</span></div></div>
                        <div class="col-md-4"><label class="view-label">Qualification</label><div class="view-value">${formatVal(d.qualification)}</div></div>
                        <div class="col-md-4"><label class="view-label">Estd. Date</label><div class="view-value">${formatVal(d.estd_date)}</div></div>
                    </div>
                </div>

                <div class="view-section mb-4">
                    <div class="view-section-header">2. CONTACT & IDENTITY</div>
                    <div class="row g-3 px-3">
                        <div class="col-md-3"><label class="view-label">Phone</label><div class="view-value">${d.phone}</div></div>
                        <div class="col-md-3"><label class="view-label">Alt Phone</label><div class="view-value">${formatVal(d.phone_alt)}</div></div>
                        <div class="col-md-3"><label class="view-label">Email</label><div class="view-value">${d.email}</div></div>
                        <div class="col-md-3"><label class="view-label">Aadhar No.</label><div class="view-value">${formatVal(d.aadhar_no)}</div></div>
                    </div>
                </div>

                <div class="view-section mb-4">
                    <div class="view-section-header">3. LOCATION DETAILS</div>
                    <div class="row g-3 px-3">
                        <div class="col-12"><label class="view-label">Full Address</label><div class="view-value">${formatVal(d.address)}</div></div>
                        <div class="col-md-3"><label class="view-label">City</label><div class="view-value">${formatVal(d.city_name)}</div></div>
                        <div class="col-md-3"><label class="view-label">District</label><div class="view-value">${formatVal(d.district_name)}</div></div>
                        <div class="col-md-3"><label class="view-label">State</label><div class="view-value">${formatVal(d.state_name)}</div></div>
                        <div class="col-md-3"><label class="view-label">Pincode</label><div class="view-value">${formatVal(d.pincode)}</div></div>
                    </div>
                </div>

                <div class="view-section mb-4">
                    <div class="view-section-header">4. INFRASTRUCTURE & FACILITIES</div>
                    <div class="row g-3 px-3">
                        <div class="col-md-3"><label class="view-label">Total Computers</label><div class="view-value">${d.computers} Units</div></div>
                        <div class="col-md-3"><label class="view-label">No. of Teachers</label><div class="view-value">${d.teachers} Staff</div></div>
                        <div class="col-md-3"><label class="view-label">Total Rooms</label><div class="view-value">${d.rooms} Rooms</div></div>
                        <div class="col-md-3"><label class="view-label">Area (Sq. Ft)</label><div class="view-value">${d.area_sqft} Sq. Ft.</div></div>
                        <div class="col-md-3"><label class="view-label">Internet Type</label><div class="view-value">${formatVal(d.internet_type)}</div></div>
                        <div class="col-md-3"><label class="view-label">Wallet Balance</label><div class="view-value text-success fw-bold">₹ ${parseFloat(d.wallet_balance).toLocaleString('en-IN', {minimumFractionDigits: 2})}</div></div>
                    </div>
                </div>

                <div class="view-section mb-4">
                    <div class="view-section-header">5. UPLOADED DOCUMENTS & PHOTOS</div>
                    <div class="row g-4 px-3 text-center">
                        <div class="col-md-3"><label class="view-label">Director Photo</label><div class="view-value d-flex justify-content-center">${docLink('director_photo', 'directors')}</div></div>
                        <div class="col-md-3"><label class="view-label">Signature</label><div class="view-value d-flex justify-content-center">${docLink('signature', 'documents')}</div></div>
                        <div class="col-md-3"><label class="view-label">Aadhar Front</label><div class="view-value d-flex justify-content-center">${docLink('aadhar_front', 'documents')}</div></div>
                        <div class="col-md-3"><label class="view-label">Aadhar Back</label><div class="view-value d-flex justify-content-center">${docLink('aadhar_back', 'documents')}</div></div>
                        <div class="col-md-3"><label class="view-label">ID Proof</label><div class="view-value d-flex justify-content-center">${docLink('id_proof', 'documents')}</div></div>
                        <div class="col-md-3"><label class="view-label">Approval Doc</label><div class="view-value d-flex justify-content-center">${docLink('approval_doc', 'documents')}</div></div>
                        <div class="col-md-3"><label class="view-label">Center Front</label><div class="view-value d-flex justify-content-center">${docLink('photo_front', 'centers')}</div></div>
                        <div class="col-md-3"><label class="view-label">Lab Photo</label><div class="view-value d-flex justify-content-center">${docLink('photo_lab', 'centers')}</div></div>
                        <div class="col-md-3"><label class="view-label">Office Photo</label><div class="view-value d-flex justify-content-center">${docLink('photo_office', 'centers')}</div></div>
                    </div>
                </div>
            </div>`;
    }).catch(() => {
        document.getElementById('view-body').innerHTML = '<p class="text-danger">Failed to fetch details. Please try again.</p>';
    });
}

function openWalletModal(type, id, code) {
    document.getElementById('adjustTitle').innerHTML = '<i class="fas fa-wallet me-2"></i>' + type + ' Wallet Balance (' + code + ')';
    document.getElementById('adjustFranchiseId').value = id;
    document.getElementById('adjustType').value = type.toLowerCase();
    
    const btn = document.getElementById('adjustBtn');
    if(type === 'Credit') {
        btn.className = 'btn btn-success px-4';
        btn.innerHTML = '<i class="fas fa-plus me-1"></i>Credit Balance';
    } else {
        btn.className = 'btn btn-danger px-4';
        btn.innerHTML = '<i class="fas fa-minus me-1"></i>Debit Balance';
    }
    
    new bootstrap.Modal(document.getElementById('walletAdjust')).show();
}

document.getElementById('form-wallet').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('adjustBtn');
    btn.disabled = true;
    
    const fd = new FormData(this);
    fd.append('action', 'adjust_wallet');

    fetch(HANDLER, { method: 'POST', body: fd })
        .then(r => r.json())
        .then(res => {
            btn.disabled = false;
            if(res.success) {
                alert(res.message);
                location.reload();
            } else {
                alert(res.message);
            }
        });
});

function deleteFranchise(id, name) {
    window.confirmDelete({
        target: name,
        onConfirm: function(modal, btn) {
            const fd = new FormData();
            fd.append('action', 'delete');
            fd.append('id', id);

            fetch(HANDLER, { method: 'POST', body: fd })
                .then(r => r.json())
                .then(res => {
                    modal.hide();
                    if(res.success) {
                        location.reload();
                    } else {
                        alert(res.message);
                    }
                });
        }
    });
}
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>
