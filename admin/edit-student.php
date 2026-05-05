<?php
// admin/edit-student.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    die("Invalid student ID.");
}

$stmt = $pdo->prepare("SELECT a.*, u.username FROM admissions a JOIN users u ON a.user_id = u.id WHERE a.id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    die("Student not found.");
}

// Fetch real franchises (formerly centers)
$franchises = $pdo->query("SELECT id, center_name, center_code FROM franchises WHERE status = 1 ORDER BY center_name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch real courses
$courses = $pdo->query("SELECT id, name as course_name FROM courses WHERE status = 1 ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch course categories from course_categories table
$categories = $pdo->query("SELECT id, name as category_name FROM course_categories WHERE status = 1 ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>Edit Student</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Student Management</li>
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>student-list.php">Student List</a></li>
            <li class="breadcrumb-item active">Edit Student</li>
        </ol>
    </nav>
</div>

<section class="section">
<div class="row justify-content-center">
<div class="col-lg-12">

<div id="student-alert" class="d-none mb-3"></div>

<form id="add-student-form" novalidate>
<input type="hidden" name="action" value="edit">
<input type="hidden" name="id" value="<?php echo $id; ?>">
<input type="hidden" name="user_id" value="<?php echo $student['user_id']; ?>">

<!-- ══ SECTION 1 — Academic Details ════════════════════════ -->
<div class="card mb-4">
    <div class="card-header d-flex align-items-center"
        >
        <i class="fas fa-graduation-cap me-2"></i>
        <h5 class="card-title mb-0">Academic Details</h5>
    </div>
    <div class="card-body pt-4">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-bold">Franchise / Center <span class="text-danger">*</span></label>
                <select class="form-select" name="center_id" required>
                    <option value="">Select Center...</option>
                    <?php foreach($franchises as $ctr): ?>
                        <option value="<?php echo $ctr['id']; ?>" <?php echo $ctr['id'] == $student['center_id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($ctr['center_name']); ?> (<?php echo $ctr['center_code']; ?>)</option>
                    <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">Please select a franchise/center.</div>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Registration No. <span class="text-muted small">(Auto-generated)</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                    <input type="text" class="form-control bg-light" name="reg_no"
                           value="NEB/REG/<?php echo str_pad($student['id'], 6, '0', STR_PAD_LEFT); ?>" readonly>
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Admission Date</label>
                <input type="date" class="form-control" name="admission_date"
                       value="<?php echo htmlspecialchars($student['admission_date']); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Academic Session <span class="text-danger">*</span></label>
                <select class="form-select" name="session_id" required>
                    <option value="">Select Session...</option>
                    <?php
                    $sessions = $pdo->query("SELECT id, session_label FROM admission_sessions WHERE status = 1")->fetchAll(PDO::FETCH_ASSOC);
                    foreach($sessions as $s) {
                        $sel = ($student['session_id'] == $s['id']) ? 'selected' : '';
                        echo "<option value='{$s['id']}' $sel>".htmlspecialchars($s['session_label'])."</option>";
                    }
                    ?>
                </select>
                <div class="invalid-feedback">Please select a session.</div>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Course Category <span class="text-danger">*</span></label>
                <select class="form-select" name="course_category" id="course_category" required>
                    <option value="">Select Category...</option>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo $cat['id'] == $student['course_category'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['category_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">Please select a course category.</div>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Course <span class="text-danger">*</span></label>
                <select class="form-select" name="course_id" required>
                    <option value="">Select Course...</option>
                    <?php foreach($courses as $c): ?>
                        <option value="<?php echo $c['id']; ?>" <?php echo $c['id'] == $student['course_id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['course_name']); ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">Please select a course.</div>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Year / Semester</label>
                <select class="form-select" name="year_sem">
                    <option>1st Year</option>
                    <option>2nd Year</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Fees Group</label>
                <select class="form-select" name="fees_group">
                    <option>Standard Fees</option>
                    <option>Scholarship Batch</option>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- ══ SECTION 2 — Personal Details ════════════════════════ -->
<div class="card mb-4">
    <div class="card-header d-flex align-items-center"
        >
        <i class="fas fa-user me-2"></i>
        <h5 class="card-title mb-0">Personal Details</h5>
    </div>
    <div class="card-body pt-4">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-bold">Student Full Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="full_name" value="<?php echo htmlspecialchars($student['full_name']); ?>"
                       placeholder="Full Name as on Certificate" required>
                <div class="invalid-feedback">Student name is required.</div>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Father's Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="father_name" value="<?php echo htmlspecialchars($student['father_name']); ?>"
                       placeholder="Father's Full Name" required>
                <div class="invalid-feedback">Father's name is required.</div>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Mother's Name</label>
                <input type="text" class="form-control" name="mother_name" value="<?php echo htmlspecialchars($student['mother_name']); ?>"
                       placeholder="Mother's Full Name">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Last Qualification <span class="text-danger">*</span></label>
                <select class="form-select" name="qualification" required>
                    <option value="">Select Qualification</option>
                    <option value="10th" <?php echo $student['qualification'] == '10th' ? 'selected' : ''; ?>>10th</option>
                    <option value="12th" <?php echo $student['qualification'] == '12th' ? 'selected' : ''; ?>>12th</option>
                    <option value="Graduation" <?php echo $student['qualification'] == 'Graduation' ? 'selected' : ''; ?>>Graduation</option>
                    <option value="Diploma" <?php echo $student['qualification'] == 'Diploma' ? 'selected' : ''; ?>>Diploma</option>
                    <option value="Others" <?php echo $student['qualification'] == 'Others' ? 'selected' : ''; ?>>Others</option>
                </select>
                <div class="invalid-feedback">Qualification is required.</div>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Date of Birth <span class="text-danger">*</span></label>
                <input type="date" class="form-control" name="dob" value="<?php echo htmlspecialchars($student['dob']); ?>" required>
                <div class="invalid-feedback">Date of birth is required.</div>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Gender</label>
                <select class="form-select" name="gender">
                    <option <?php echo $student['gender'] === 'Male' ? 'selected' : ''; ?>>Male</option>
                    <option <?php echo $student['gender'] === 'Female' ? 'selected' : ''; ?>>Female</option>
                    <option <?php echo $student['gender'] === 'Other' ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Category / Caste</label>
                <select class="form-select" name="category">
                    <option>General</option>
                    <option>OBC</option>
                    <option>SC</option>
                    <option>ST</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Religion</label>
                <select class="form-select" name="religion">
                    <option>Hindu</option>
                    <option>Muslim</option>
                    <option>Christian</option>
                    <option>Sikh</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Mobile Number <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-phone-alt"></i></span>
                    <input type="text" class="form-control" name="mobile" value="<?php echo htmlspecialchars($student['mobile']); ?>"
                           placeholder="10 Digit Mobile" maxlength="10" required>
                </div>
                <div class="invalid-feedback">Mobile number is required.</div>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Email ID</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($student['email']); ?>"
                           placeholder="email@example.com">
                </div>
            </div>
            <div class="col-md-12">
                <label class="form-label fw-bold">Full Address <span class="text-danger">*</span></label>
                <textarea class="form-control" name="address" rows="2"
                          placeholder="House No., Street, Village, Landmark..." required><?php echo htmlspecialchars($student['address']); ?></textarea>
                <div class="invalid-feedback">Address is required.</div>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">State <span class="text-danger">*</span></label>
                <select class="form-select" id="student_state" name="state_id" required>
                    <option value="">Loading states&hellip;</option>
                </select>
                <div class="invalid-feedback">Please select a state.</div>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">District <span class="text-danger">*</span></label>
                <select class="form-select" id="student_district" name="district_id" disabled required>
                    <option value="">Select State first</option>
                </select>
                <div class="invalid-feedback">Please select a district.</div>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">City / Block</label>
                <select class="form-select" id="student_city" name="city_id" disabled>
                    <option value="">Select District first</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Pincode</label>
                <input type="text" class="form-control" name="pincode" value="<?php echo htmlspecialchars($student['pincode']); ?>"
                       placeholder="6 Digit Pincode" maxlength="6" pattern="[0-9]{6}">
            </div>
        </div>
    </div>
</div>

<!-- ══ SECTION 3 — Login Credentials ════════════════════════ -->
<div class="card mb-4">
    <div class="card-header d-flex align-items-center">
        <i class="fas fa-key me-2"></i>
        <h5 class="card-title mb-0">Login Credentials</h5>
    </div>
    <div class="card-body pt-4">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-bold">Username <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="username" value="<?php echo htmlspecialchars($student['username']); ?>" placeholder="Unique username" required>
                <small class="text-muted">Used for student portal login</small>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Password</label>
                <input type="password" class="form-control" name="password" placeholder="Leave blank to keep unchanged" minlength="8">
            </div>
        </div>
    </div>
</div>

<!-- ══ SECTION 4 — Documents & Attachments ══════════════════ -->
<div class="card mb-4">
    <div class="card-header d-flex align-items-center"
        >
        <i class="fas fa-file-upload me-2"></i>
        <h5 class="card-title mb-0">Documents &amp; Attachments</h5>
    </div>
    <div class="card-body pt-4">
        <div class="row g-4">
            <!-- Row 1 -->
            <div class="col-md-3">
                <div class="p-3 border rounded text-center h-100 bg-light">
                    <i class="fas fa-user-circle fs-3 text-primary mb-2"></i>
                    <label class="form-label d-block small fw-bold">Student Photo</label>
                    <input type="file" class="form-control form-control-sm" name="student_photo" accept="image/*">
                    <?php if($student['photo']): ?><div class="mt-1 small text-success"><i class="fas fa-check-circle me-1"></i>Exits</div><?php endif; ?>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 border rounded text-center h-100 bg-light">
                    <i class="fas fa-signature fs-3 text-primary mb-2"></i>
                    <label class="form-label d-block small fw-bold">Student Signature</label>
                    <input type="file" class="form-control form-control-sm" name="signature" accept="image/*">
                    <?php if($student['signature']): ?><div class="mt-1 small text-success"><i class="fas fa-check-circle me-1"></i>Exits</div><?php endif; ?>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 border rounded text-center h-100 bg-light">
                    <i class="fas fa-id-card fs-3 text-primary mb-2"></i>
                    <label class="form-label d-block small fw-bold">Aadhar (Front)</label>
                    <input type="file" class="form-control form-control-sm" name="aadhar_front" accept="image/*,application/pdf">
                    <?php if($student['aadhar_front']): ?><div class="mt-1 small text-success"><i class="fas fa-check-circle me-1"></i>Exits</div><?php endif; ?>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 border rounded text-center h-100 bg-light">
                    <i class="fas fa-id-card fs-3 text-primary mb-2"></i>
                    <label class="form-label d-block small fw-bold">Aadhar (Back)</label>
                    <input type="file" class="form-control form-control-sm" name="aadhar_back" accept="image/*,application/pdf">
                    <?php if($student['aadhar_back']): ?><div class="mt-1 small text-success"><i class="fas fa-check-circle me-1"></i>Exits</div><?php endif; ?>
                </div>
            </div>

            <!-- Row 2 -->
            <div class="col-md-3">
                <div class="p-3 border rounded text-center h-100 bg-light">
                    <i class="fas fa-file-invoice fs-3 text-success mb-2"></i>
                    <label class="form-label d-block small fw-bold">10th Marksheet</label>
                    <input type="file" class="form-control form-control-sm" name="marksheet_10th" accept="image/*,application/pdf">
                    <?php if($student['marksheet_10th']): ?><div class="mt-1 small text-success"><i class="fas fa-check-circle me-1"></i>Exits</div><?php endif; ?>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 border rounded text-center h-100 bg-light">
                    <i class="fas fa-file-invoice fs-3 text-success mb-2"></i>
                    <label class="form-label d-block small fw-bold">12th Marksheet</label>
                    <input type="file" class="form-control form-control-sm" name="marksheet_12th" accept="image/*,application/pdf">
                    <?php if($student['marksheet_12th']): ?><div class="mt-1 small text-success"><i class="fas fa-check-circle me-1"></i>Exits</div><?php endif; ?>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 border rounded text-center h-100 bg-light">
                    <i class="fas fa-id-badge fs-3 text-info mb-2"></i>
                    <label class="form-label d-block small fw-bold">Parent Aadhar (F)</label>
                    <input type="file" class="form-control form-control-sm" name="parent_aadhar_front" accept="image/*,application/pdf">
                    <?php if($student['parent_aadhar_front']): ?><div class="mt-1 small text-success"><i class="fas fa-check-circle me-1"></i>Exits</div><?php endif; ?>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 border rounded text-center h-100 bg-light">
                    <i class="fas fa-id-badge fs-3 text-info mb-2"></i>
                    <label class="form-label d-block small fw-bold">Parent Aadhar (B)</label>
                    <input type="file" class="form-control form-control-sm" name="parent_aadhar_back" accept="image/*,application/pdf">
                    <?php if($student['parent_aadhar_back']): ?><div class="mt-1 small text-success"><i class="fas fa-check-circle me-1"></i>Exits</div><?php endif; ?>
                </div>
            </div>

            <!-- Row 3 -->
            <div class="col-md-3">
                <div class="p-3 border rounded text-center h-100 bg-light">
                    <i class="fas fa-passport fs-3 text-warning mb-2"></i>
                    <label class="form-label d-block small fw-bold">Student ID Proof</label>
                    <input type="file" class="form-control form-control-sm" name="id_proof" accept="image/*,application/pdf">
                    <?php if($student['id_proof']): ?><div class="mt-1 small text-success"><i class="fas fa-check-circle me-1"></i>Exits</div><?php endif; ?>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 border rounded text-center h-100 bg-light">
                    <i class="fas fa-user-shield fs-3 text-warning mb-2"></i>
                    <label class="form-label d-block small fw-bold">Guardian ID Proof</label>
                    <input type="file" class="form-control form-control-sm" name="guardian_doc" accept="image/*,application/pdf">
                    <?php if($student['guardian_doc']): ?><div class="mt-1 small text-success"><i class="fas fa-check-circle me-1"></i>Exits</div><?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ══ Form Actions ══════════════════════════════════════════ -->
<div class="card mb-4">
    <div class="card-body py-3">
        <div class="d-flex justify-content-between align-items-center">
            <a href="<?php echo ADMIN_BASE_URL; ?>student-list.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
            <div class="d-flex gap-2">
                <button type="reset" class="btn btn-outline-warning">
                    <i class="fas fa-undo me-2"></i>Reset Form
                </button>
                <button type="submit" class="btn btn-primary px-5">
                    <i class="fas fa-save me-2"></i>Update Student
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
(function () {
    'use strict';
    const form = document.getElementById('add-student-form');
    const alertBox = document.getElementById('student-alert');

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        form.classList.add('was-validated');

        if (!form.checkValidity()) return;

        const btn = form.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Processing...';

        const fd = new FormData(form);
        
        fetch('<?php echo BASE_URL; ?>ajax/admission_handler.php', {
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
                setTimeout(() => { window.location.href = 'student-list.php'; }, 2000);
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
            alert('An error occurred. Please check console.');
            console.error(err);
        });
    });
})();

// Cascading location dropdowns
document.addEventListener('DOMContentLoaded', function () {
    if(typeof initLocationCascade === 'function') {
        initLocationCascade({
            stateEl   : '#student_state',
            districtEl: '#student_district',
            cityEl    : '#student_city'
        }).then(() => {
            // Set existing values
            const stateEl = document.getElementById('student_state');
            stateEl.value = "<?php echo $student['state_id']; ?>";
            stateEl.dispatchEvent(new Event('change'));

            // Wait for districts to load, then set district
            setTimeout(() => {
                const distEl = document.getElementById('student_district');
                distEl.value = "<?php echo $student['district_id']; ?>";
                distEl.dispatchEvent(new Event('change'));

                // Wait for cities to load, then set city
                setTimeout(() => {
                    const cityEl = document.getElementById('student_city');
                    if(cityEl) cityEl.value = "<?php echo $student['city_id']; ?>";
                }, 500);
            }, 500);
        });
    }

    // Course Category Cascade
    const categoryEl = document.getElementById('course_category');
    const courseEl = document.getElementById('course_id');
    if(categoryEl && courseEl) {
        categoryEl.addEventListener('change', function() {
            const catId = this.value;
            courseEl.disabled = true;
            courseEl.innerHTML = '<option value="">Loading courses...</option>';
            if(!catId) { courseEl.innerHTML = '<option value="">Select Category first...</option>'; return; }
            
            fetch(`<?php echo BASE_URL; ?>ajax/get_courses.php?type=courses&category_id=${catId}`)
                .then(r => r.json()).then(data => {
                    courseEl.innerHTML = '<option value="">Select Course...</option>';
                    if(data && data.length > 0) {
                        data.forEach(c => { courseEl.innerHTML += `<option value="${c.id}">${c.name}</option>`; });
                        courseEl.disabled = false;
                    } else { courseEl.innerHTML = '<option value="">No courses found</option>'; }
                });
        });
    }
});
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>
