<?php
// admin/partner-list.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Search / Filter logic maintained but DataTables will handle search locally mostly
$where = "WHERE p.status != -1"; 
$params = [];

// Fetch partners with franchise counts
$stmt = $pdo->prepare("
    SELECT p.*, (SELECT COUNT(*) FROM franchises f WHERE f.partner_id = p.id AND f.status=1) as franchise_count
    FROM partners p
    $where 
    ORDER BY p.created_at DESC
");
$stmt->execute($params);
$partner_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>Partner List</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Partner Managements</li>
            <li class="breadcrumb-item active">Partner List</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-12">
            <div id="partner-alert" class="d-none mb-3"></div>
            <div class="card">
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle datatable-premium"
                               data-add-btn='{"text":"Add New Partner","onclick":"openAddPartner()","icon":"fas fa-user-tie"}'>
                            <thead class="table-light">
                                <tr>
                                    <th width="60">S.No.</th>
                                    <th>Partner Info</th>
                                    <th>Franchise Count</th>
                                    <th>Wallet Balance</th>
                                    <th data-no-sort>Status</th>
                                    <th class="text-end" data-no-sort>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $sn=1; foreach($partner_list as $p): ?>
                                <tr id="prow-<?php echo $p['id']; ?>">
                                    <td><?php echo $sn++; ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-3 border shadow-sm" style="width:45px;height:45px;overflow:hidden;">
                                                <img src="<?php echo $p['profile_image'] ? BASE_URL.'media/partners/'.$p['profile_image'] : BASE_URL.'media/users/avatar.png'; ?>" 
                                                     alt="Partner" style="width:100%;height:100%;object-fit:cover;">
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark fs-6"><?php echo strtoupper($p['full_name']); ?></div>
                                                <div class="small text-muted">
                                                    <i class="fas fa-envelope me-1 text-primary"></i><?php echo $p['email']; ?> &nbsp;
                                                    <i class="fas fa-phone-alt me-1 text-success"></i><?php echo $p['phone']; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="badge bg-info-light text-info border border-info px-3"><?php echo str_pad($p['franchise_count'], 2, '0', STR_PAD_LEFT); ?> Centers</div>
                                        <div class="mt-1"><a href="franchise-list.php?partner_id=<?php echo $p['id']; ?>" class="small text-decoration-underline fw-bold">Manage Centers</a></div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="fw-bold text-success fs-5">₹ <span id="pbal-<?php echo $p['id']; ?>"><?php echo number_format($p['wallet_balance'], 2); ?></span></span>
                                            <div class="btn-group shadow-sm border rounded">
                                                <button class="btn btn-xs btn-outline-success border-0 px-2" title="Credit Balance"
                                                        onclick="openPWalletModal('Credit', <?php echo $p['id']; ?>, '<?php echo addslashes($p['full_name']); ?>')">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                                <button class="btn btn-xs btn-outline-danger border-0 px-2" title="Debit Balance"
                                                        onclick="openPWalletModal('Debit', <?php echo $p['id']; ?>, '<?php echo addslashes($p['full_name']); ?>')">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $p['status'] == 1 ? 'success' : 'danger'; ?> rounded-pill px-3">
                                            <?php echo $p['status'] == 1 ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-info btn-icon me-1" title="View Profile" onclick="viewPartner(<?php echo $p['id']; ?>)"><i class="fas fa-eye"></i></button>
                                        <button class="btn btn-sm btn-outline-warning btn-icon me-1" title="Edit Account" onclick="editPartner(<?php echo $p['id']; ?>)"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-outline-danger btn-icon" title="Delete Account" onclick="deletePartner(<?php echo $p['id']; ?>, '<?php echo addslashes($p['full_name']); ?>')"><i class="fas fa-trash"></i></button>
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

<!-- Add / Edit Partner Modal -->
<div class="modal fade" id="addPartner" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-tie me-2"></i><span id="p-modal-title">Register New Partner</span></h5>
                <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="form-partner" enctype="multipart/form-data" novalidate>
                <input type="hidden" name="action" id="p-action" value="add">
                <input type="hidden" name="id" id="p-id" value="">
                <div class="modal-body px-3 py-4">
                    <div class="row g-3">
                        <div class="col-12"><div class="form-section-header">1. BASIC PROFILE</div></div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" id="p-full_name" class="form-control" placeholder="Partner Full Name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Email (Username) <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="p-email" class="form-control" placeholder="partner@example.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Phone Number <span class="text-danger">*</span></label>
                            <input type="text" name="phone" id="p-phone" class="form-control" placeholder="10 Digit Mobile" maxlength="10" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Account Status</label>
                            <select name="status" id="p-status" class="form-select">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                        <div class="col-12 mt-4"><div class="form-section-header">2. ACCESS & IDENTITY</div></div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Password <span class="text-danger" id="pass-req">*</span></label>
                            <input type="password" name="password" id="p-password" class="form-control" placeholder="Set Access Password">
                            <small class="text-muted d-none" id="pass-help">Leave blank to keep current password</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Profile Image</label>
                            <input type="file" name="profile_image" class="form-control" accept="image/*">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top py-2">
                    <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">CANCEL</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold" id="p-save-btn"><i class="fas fa-save me-2"></i>SAVE ACCOUNT</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Partner Modal -->
<div class="modal fade" id="viewPartner" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-eye me-2"></i>Partner Details</h5>
                <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body p-0" id="viewPartnerBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Wallet Adjustment Modal -->
<div class="modal fade" id="walletAdjust" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="adjustTitle"><i class="fas fa-wallet me-2"></i>Adjust Wallet Balance</h5>
                <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="form-pwallet" novalidate>
                <input type="hidden" id="pAdjustId" name="partner_id">
                <input type="hidden" id="pAdjustType" name="type">
                <div class="modal-body px-3 py-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Amount (₹) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="amount" class="form-control" placeholder="0.00" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold">Remarks / Note</label>
                        <textarea class="form-control" name="description" rows="3" placeholder="e.g. Manual Credit for referral"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top py-2">
                    <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">CANCEL</button>
                    <button type="submit" class="btn btn-success px-4 fw-bold" id="pAdjustBtn">
                        <i class="fas fa-check me-1"></i>CONFIRM
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const PHANDLER = '<?php echo BASE_URL; ?>ajax/partner_handler.php';
let modalPartner, modalWallet;

document.addEventListener('DOMContentLoaded', () => {
    modalPartner = new bootstrap.Modal(document.getElementById('addPartner'));
    modalWallet = new bootstrap.Modal(document.getElementById('walletAdjust'));

    const pForm = document.getElementById('form-partner');
    pForm.onsubmit = function(e) {
        e.preventDefault();
        if(!pForm.checkValidity()) { pForm.classList.add('was-validated'); return; }
        const btn = document.getElementById('p-save-btn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>SAVING...';
        fetch(PHANDLER, { method: 'POST', body: new FormData(this) })
        .then(r => r.json()).then(res => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-2"></i>SAVE ACCOUNT';
            if(res.success) { location.reload(); }
            else alert(res.message);
        });
    };

    const wForm = document.getElementById('form-pwallet');
    wForm.onsubmit = function(e) {
        e.preventDefault();
        if(!wForm.checkValidity()) { wForm.classList.add('was-validated'); return; }
        const fd = new FormData(this);
        fd.append('action', 'adjust_wallet');
        const btn = document.getElementById('pAdjustBtn');
        btn.disabled = true;
        fetch(PHANDLER, { method: 'POST', body: fd })
        .then(r => r.json()).then(res => {
            btn.disabled = false;
            if(res.success) { location.reload(); }
            else alert(res.message);
        });
    };
});

function openAddPartner() {
    resetPartnerForm();
    modalPartner.show();
}

function resetPartnerForm() {
    const f = document.getElementById('form-partner');
    f.reset();
    f.classList.remove('was-validated');
    document.getElementById('p-id').value = '';
    document.getElementById('p-action').value = 'add';
    document.getElementById('p-modal-title').textContent = 'Register New Partner';
    document.getElementById('p-password').required = true;
    document.getElementById('pass-req').classList.remove('d-none');
    document.getElementById('pass-help').classList.add('d-none');
}

function openPWalletModal(type, id, name) {
    document.getElementById('adjustTitle').innerHTML = `<i class="fas fa-wallet me-2"></i>${type} Wallet Balance (${name})`;
    document.getElementById('pAdjustId').value = id;
    document.getElementById('pAdjustType').value = type.toLowerCase();
    
    const btn = document.getElementById('pAdjustBtn');
    if (type === 'Credit') {
        btn.className = 'btn btn-success px-4 fw-bold';
        btn.innerHTML = '<i class="fas fa-plus me-1"></i>CREDIT BALANCE';
    } else {
        btn.className = 'btn btn-danger px-4 fw-bold';
        btn.innerHTML = '<i class="fas fa-minus me-1"></i>DEBIT BALANCE';
    }
    modalWallet.show();
}

function viewPartner(id) {
    const body = document.getElementById('viewPartnerBody');
    body.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>';
    new bootstrap.Modal(document.getElementById('viewPartner')).show();
    
    fetch(PHANDLER + '?action=get&id=' + id)
    .then(r => r.json()).then(res => {
        if(!res.success) { body.innerHTML = `<div class="p-4 text-danger">${res.message}</div>`; return; }
        const p = res.data;
        body.innerHTML = `
            <div class="enquiry-view-premium p-1">
                <div class="view-section mb-0">
                    <div class="view-section-header">1. Partner Profile</div>
                    <div class="row g-3 align-items-center px-3 py-4">
                        <div class="col-md-4 text-center">
                            <div class="p-2 bg-white rounded-circle shadow-sm d-inline-block border">
                                <img src="${p.profile_image ? '<?php echo BASE_URL; ?>media/partners/' + p.profile_image : '<?php echo BASE_URL; ?>media/users/avatar.png'}" 
                                     class="img-fluid rounded-circle" style="width:140px;height:140px;object-fit:cover;">
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="row g-3">
                                <div class="col-md-12"><label class="view-label">Full Name</label><div class="view-value fw-bold text-dark fs-5">${p.full_name.toUpperCase()}</div></div>
                                <div class="col-md-6"><label class="view-label">Account Status</label><div class="view-value"><span class="badge bg-${p.status == 1 ? 'success' : 'danger'} rounded-pill px-3">${p.status == 1 ? 'ACTIVE' : 'INACTIVE'}</span></div></div>
                                <div class="col-md-6"><label class="view-label">Email Address</label><div class="view-value text-primary fw-bold">${p.email}</div></div>
                                <div class="col-md-6"><label class="view-label">Phone Number</label><div class="view-value fw-bold text-dark">${p.phone}</div></div>
                                <div class="col-md-6"><label class="view-label">Reg. Date</label><div class="view-value small">${new Date(p.created_at).toLocaleDateString('en-GB', {day: '2-digit', month: 'short', year: 'numeric'})}</div></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="view-section mb-0 border-top">
                    <div class="view-section-header">2. Financial Overview</div>
                    <div class="row g-3 px-3 py-4">
                        <div class="col-md-12">
                            <div class="p-4 bg-success bg-opacity-10 border border-success border-opacity-25 rounded-3 text-center">
                                <label class="view-label text-success mb-2 fs-6">Available Wallet Balance</label>
                                <div class="display-6 fw-bold text-success mb-0">₹ ${parseFloat(p.wallet_balance).toLocaleString('en-IN', {minimumFractionDigits: 2})}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
}

function deletePartner(id, name) {
    if(confirm(`Are you sure you want to delete the partner account for "${name}"?`)) {
        const fd = new FormData();
        fd.append('action', 'delete');
        fd.append('id', id);
        fetch(PHANDLER, { method: 'POST', body: fd })
        .then(r => r.json()).then(res => {
            if(res.success) { location.reload(); }
            else alert(res.message);
        });
    }
}

function editPartner(id) {
    fetch(PHANDLER + '?action=get&id=' + id)
    .then(r => r.json()).then(res => {
        if(res.success) {
            const p = res.data;
            document.getElementById('p-id').value = p.id;
            document.getElementById('p-action').value = 'edit';
            document.getElementById('p-modal-title').textContent = 'Edit Partner — ' + p.full_name;
            document.getElementById('p-full_name').value = p.full_name;
            document.getElementById('p-email').value = p.email;
            document.getElementById('p-phone').value = p.phone;
            document.getElementById('p-status').value = p.status;
            document.getElementById('p-password').required = false;
            document.getElementById('pass-req').classList.add('d-none');
            document.getElementById('pass-help').classList.remove('d-none');
            modalPartner.show();
        }
    });
}
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>
