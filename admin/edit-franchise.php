<?php
// admin/edit-franchise.php
require_once('../common/config.php');

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header("Location: franchise-list.php");
    exit();
}

// Fetch franchise data
$stmt = $pdo->prepare("SELECT * FROM franchises WHERE id = ?");
$stmt->execute([$id]);
$f = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$f) {
    header("Location: franchise-list.php");
    exit();
}

// Fetch active partners
$partners = $pdo->query("SELECT id, full_name FROM partners WHERE status = 1 ORDER BY full_name ASC")->fetchAll(PDO::FETCH_ASSOC);

include(__DIR__ . "/includes/header.php");
?>

<div class="pagetitle">
    <h1>Edit Franchise — <?php echo htmlspecialchars($f['center_name']); ?></h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Franchise Management</li>
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>franchise-list.php">Franchise List</a></li>
            <li class="breadcrumb-item active">Edit Franchise</li>
        </ol>
    </nav>
</div>

<section class="section">
<div class="row justify-content-center">
<div class="col-lg-12">

<!-- Alert container -->
<div id="franchise-alert" class="d-none mb-3"></div>

<form id="edit-franchise-form" novalidate enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $f['id']; ?>">
<!-- ══════════════════════════════════════════════════════════
     SECTION 1 — Center Information
═══════════════════════════════════════════════════════════════ -->
<div class="card mb-4">
    <div class="card-header d-flex align-items-center">
        <i class="fas fa-building me-2"></i>
        <h5 class="card-title mb-0">Center Information</h5>
    </div>
    <div class="card-body pt-4">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-bold">Center Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="center_name"
                       value="<?php echo htmlspecialchars($f['center_name']); ?>" required>
                <div class="invalid-feedback">Center name is required.</div>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Center Code</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                    <input type="text" class="form-control bg-light" name="center_code"
                           value="<?php echo htmlspecialchars($f['center_code']); ?>" readonly>
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Master Partner</label>
                <select class="form-select" name="partner_id">
                    <option value="">Select Partner (Optional)</option>
                    <?php foreach($partners as $p): ?>
                        <option value="<?php echo $p['id']; ?>" <?php echo $f['partner_id'] == $p['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($p['full_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Center Phone <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-phone-alt"></i></span>
                    <input type="text" class="form-control" name="center_phone"
                           value="<?php echo htmlspecialchars($f['phone']); ?>" maxlength="10" required>
                </div>
                <div class="invalid-feedback">Center phone is required.</div>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Center Email <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" class="form-control" name="center_email"
                           value="<?php echo htmlspecialchars($f['email']); ?>" required>
                </div>
                <div class="invalid-feedback">A valid email is required.</div>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Alternate Phone</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                    <input type="text" class="form-control" name="phone_alt"
                           value="<?php echo htmlspecialchars($f['phone_alt'] ?? ''); ?>" maxlength="10">
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Qualification of Director</label>
                <input type="text" class="form-control" name="qualification" value="<?php echo htmlspecialchars($f['qualification'] ?? ''); ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Establishment Date</label>
                <input type="date" class="form-control" name="estd_date"
                       value="<?php echo $f['estd_date'] ?: date('Y-m-d'); ?>">
            </div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════
     SECTION 2 — Director Details
═══════════════════════════════════════════════════════════════ -->
<div class="card mb-4">
    <div class="card-header d-flex align-items-center">
        <i class="fas fa-user-tie me-2"></i>
        <h5 class="card-title mb-0">Director Details</h5>
    </div>
    <div class="card-body pt-4">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-bold">Director Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="director_name"
                       value="<?php echo htmlspecialchars($f['director_name']); ?>" required>
                <div class="invalid-feedback">Director name is required.</div>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Director Mobile <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-mobile-alt"></i></span>
                    <input type="text" class="form-control" name="director_mobile"
                           value="<?php echo htmlspecialchars($f['director_mobile']); ?>" maxlength="10" required>
                </div>
                <div class="invalid-feedback">Director mobile is required.</div>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Director Aadhar No.</label>
                <input type="text" class="form-control" name="aadhar_no"
                       value="<?php echo htmlspecialchars($f['aadhar_no'] ?? ''); ?>" maxlength="12">
            </div>
            
            <div class="col-md-4">
                <label class="form-label fw-bold">Director Photo</label>
                <input type="file" class="form-control" name="director_photo" accept="image/*">
                <?php if($f['director_photo']): ?>
                    <small class="text-success"><i class="fas fa-check-circle me-1"></i>Existing: <a href="<?php echo BASE_URL; ?>media/franchise/directors/<?php echo $f['director_photo']; ?>" target="_blank">View</a></small>
                <?php endif; ?>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Director Signature</label>
                <input type="file" class="form-control" name="signature" accept="image/*">
                <?php if($f['signature']): ?>
                    <small class="text-success"><i class="fas fa-check-circle me-1"></i>Existing: <a href="<?php echo BASE_URL; ?>media/franchise/documents/<?php echo $f['signature']; ?>" target="_blank">View</a></small>
                <?php endif; ?>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Aadhar Card (Front)</label>
                <input type="file" class="form-control" name="aadhar_front" accept=".pdf,image/*">
                <?php if($f['aadhar_front']): ?>
                    <small class="text-success"><i class="fas fa-check-circle me-1"></i>Existing: <a href="<?php echo BASE_URL; ?>media/franchise/documents/<?php echo $f['aadhar_front']; ?>" target="_blank">View</a></small>
                <?php endif; ?>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Aadhar Card (Back)</label>
                <input type="file" class="form-control" name="aadhar_back" accept=".pdf,image/*">
                <?php if($f['aadhar_back']): ?>
                    <small class="text-success"><i class="fas fa-check-circle me-1"></i>Existing: <a href="<?php echo BASE_URL; ?>media/franchise/documents/<?php echo $f['aadhar_back']; ?>" target="_blank">View</a></small>
                <?php endif; ?>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Center Approval Doc</label>
                <input type="file" class="form-control" name="approval_doc" accept=".pdf,image/*">
                <?php if($f['approval_doc']): ?>
                    <small class="text-success"><i class="fas fa-check-circle me-1"></i>Existing: <a href="<?php echo BASE_URL; ?>media/franchise/documents/<?php echo $f['approval_doc']; ?>" target="_blank">View</a></small>
                <?php endif; ?>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Other ID Proof</label>
                <input type="file" class="form-control" name="id_proof" accept=".pdf,image/*">
                <?php if($f['id_proof']): ?>
                    <small class="text-success"><i class="fas fa-check-circle me-1"></i>Existing: <a href="<?php echo BASE_URL; ?>media/franchise/documents/<?php echo $f['id_proof']; ?>" target="_blank">View</a></small>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════
     SECTION 3 — Location Details
═══════════════════════════════════════════════════════════════ -->
<div class="card mb-4">
    <div class="card-header d-flex align-items-center">
        <i class="fas fa-map-marker-alt me-2"></i>
        <h5 class="card-title mb-0">Location Details</h5>
    </div>
    <div class="card-body pt-4">
        <div class="row g-3">
            <div class="col-md-12">
                <label class="form-label fw-bold">Full Address <span class="text-danger">*</span></label>
                <textarea class="form-control" name="address" rows="2" required><?php echo htmlspecialchars($f['address']); ?></textarea>
                <div class="invalid-feedback">Address is required.</div>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">State <span class="text-danger">*</span></label>
                <select class="form-select" id="franchise_state" name="state_id" required>
                    <option value="">Loading states&hellip;</option>
                </select>
                <div class="invalid-feedback">Please select a state.</div>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">District <span class="text-danger">*</span></label>
                <select class="form-select" id="franchise_district" name="district_id" disabled required>
                    <option value="">Select State first</option>
                </select>
                <div class="invalid-feedback">Please select a district.</div>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">City / Town</label>
                <select class="form-select" id="franchise_city" name="city_id" disabled>
                    <option value="">Select District first</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Pincode</label>
                <input type="text" class="form-control" name="pincode"
                       value="<?php echo htmlspecialchars($f['pincode']); ?>" maxlength="6" pattern="[0-9]{6}">
            </div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════
     SECTION 4 — Infrastructure
═══════════════════════════════════════════════════════════════ -->
<div class="card mb-4">
    <div class="card-header d-flex align-items-center">
        <i class="fas fa-laptop-house me-2"></i>
        <h5 class="card-title mb-0">Infrastructure Details</h5>
    </div>
    <div class="card-body pt-4">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label fw-bold">Total Computers</label>
                <input type="number" class="form-control" name="computers" value="<?php echo $f['computers']; ?>" min="0">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">No. of Teachers</label>
                <input type="number" class="form-control" name="teachers" value="<?php echo $f['teachers']; ?>" min="0">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Total Rooms / Labs</label>
                <input type="number" class="form-control" name="rooms" value="<?php echo $f['rooms']; ?>" min="0">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Total Area (Sq. Ft.)</label>
                <input type="number" class="form-control" name="area_sqft" value="<?php echo $f['area_sqft']; ?>" min="0">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Internet Connection</label>
                <select class="form-select" name="internet_type">
                    <option <?php echo $f['internet_type'] == 'Broadband / Fiber' ? 'selected' : ''; ?>>Broadband / Fiber</option>
                    <option <?php echo $f['internet_type'] == 'Mobile Hotspot' ? 'selected' : ''; ?>>Mobile Hotspot</option>
                    <option <?php echo $f['internet_type'] == 'Leased Line' ? 'selected' : ''; ?>>Leased Line</option>
                    <option <?php echo $f['internet_type'] == 'None' ? 'selected' : ''; ?>>None</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Center Front Photo</label>
                <input type="file" class="form-control" name="photo_front" accept="image/*">
                <?php if($f['photo_front']): ?>
                    <small class="text-success"><i class="fas fa-check-circle me-1"></i>Existing: <a href="<?php echo BASE_URL; ?>media/franchise/centers/<?php echo $f['photo_front']; ?>" target="_blank">View</a></small>
                <?php endif; ?>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Computer Lab Photo</label>
                <input type="file" class="form-control" name="photo_lab" accept="image/*">
                <?php if($f['photo_lab']): ?>
                    <small class="text-success"><i class="fas fa-check-circle me-1"></i>Existing: <a href="<?php echo BASE_URL; ?>media/franchise/centers/<?php echo $f['photo_lab']; ?>" target="_blank">View</a></small>
                <?php endif; ?>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Office / Reception Photo</label>
                <input type="file" class="form-control" name="photo_office" accept="image/*">
                <?php if($f['photo_office']): ?>
                    <small class="text-success"><i class="fas fa-check-circle me-1"></i>Existing: <a href="<?php echo BASE_URL; ?>media/franchise/centers/<?php echo $f['photo_office']; ?>" target="_blank">View</a></small>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════
     SECTION 5 — Account Status
═══════════════════════════════════════════════════════════════ -->
<div class="card mb-4">
    <div class="card-header d-flex align-items-center">
        <i class="fas fa-shield-alt me-2"></i>
        <h5 class="card-title mb-0">Account Status</h5>
    </div>
    <div class="card-body pt-4">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-bold">Account Status</label>
                <select class="form-select" name="status">
                    <option value="1" <?php echo $f['status'] == 1 ? 'selected' : ''; ?>>Active</option>
                    <option value="0" <?php echo $f['status'] == 0 ? 'selected' : ''; ?>>Inactive / Pending</option>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- ══ Form Actions ══════════════════════════════════════════ -->
<div class="card mb-4">
    <div class="card-body py-3">
        <div class="d-flex justify-content-between align-items-center">
            <a href="<?php echo ADMIN_BASE_URL; ?>franchise-list.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-5">
                    <i class="fas fa-save me-2"></i>Update Franchise
                </button>
            </div>
        </div>
    </div>
</div>

</form>
</div>
</div>
</section>

<script>
// Bootstrap client-side validation and AJAX submission
(function () {
    'use strict';
    const form = document.getElementById('edit-franchise-form');
    const alertBox = document.getElementById('franchise-alert');

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        form.classList.add('was-validated');

        if (!form.checkValidity()) return;

        const btn = form.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Updating...';

        const fd = new FormData(form);
        fd.append('action', 'edit_franchise');
        
        fetch('<?php echo BASE_URL; ?>ajax/franchise_handler.php', {
            method: 'POST',
            body: fd
        })
        .then(r => r.json())
        .then(res => {
            btn.disabled = false;
            btn.innerHTML = originalText;

            if(res.success) {
                alertBox.className = 'alert alert-success mb-3';
                alertBox.innerHTML = '<i class="fas fa-check-circle me-2"></i>' + res.message;
                alertBox.classList.remove('d-none');
                window.scrollTo({ top: 0, behavior: 'smooth' });
                setTimeout(() => { window.location.href = 'franchise-list.php'; }, 2000);
            } else {
                alertBox.className = 'alert alert-danger mb-3';
                alertBox.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i>' + res.message;
                alertBox.classList.remove('d-none');
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = originalText;
            console.error(err);
            alert('An unexpected error occurred. Please try again.');
        });
    });
})();

// Cascading location dropdowns
document.addEventListener('DOMContentLoaded', function () {
    initLocationCascade({
        stateEl   : '#franchise_state',
        districtEl: '#franchise_district',
        cityEl    : '#franchise_city'
    }).then(() => {
        // Set initial values for location
        const stateEl = document.querySelector('#franchise_state');
        stateEl.value = "<?php echo $f['state_id']; ?>";
        stateEl.dispatchEvent(new Event('change'));
        
        setTimeout(() => {
            const distEl = document.querySelector('#franchise_district');
            distEl.value = "<?php echo $f['district_id']; ?>";
            distEl.dispatchEvent(new Event('change'));
            
            setTimeout(() => {
                const cityEl = document.querySelector('#franchise_city');
                cityEl.value = "<?php echo $f['city_id']; ?>";
            }, 500);
        }, 500);
    });
});
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>
