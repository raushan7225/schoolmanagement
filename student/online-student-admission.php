<?php
require_once('../common/config.php');

$user_id = $_SESSION['user_id'] ?? null;
$existing_data = null;
$can_apply_new = true;
$is_edit_mode = false;

if ($user_id) {
    // Fetch latest admission
    $stmt = $pdo->prepare("SELECT a.*, c.name as course_name, f.center_name 
                          FROM admissions a 
                          LEFT JOIN courses c ON a.course_id = c.id 
                          LEFT JOIN franchises f ON a.center_id = f.id 
                          WHERE a.user_id = ? ORDER BY a.id DESC LIMIT 1");
    $stmt->execute([$user_id]);
    $existing_data = $stmt->fetch();

    if ($existing_data) {
        if ($existing_data['approval_status'] !== 'completed') {
            $can_apply_new = false;
            $is_edit_mode = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Online Admission Portal | NEBOOSASE</title>
    <?php include("../common/meta.php"); ?>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <!-- Dynamic Theme Colors from General Settings -->
    <link id="dynamic-theme-css" rel="stylesheet" href="<?php echo BASE_URL; ?>theme.php">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/student.css">
</head>

<body class="bg-light">
    <?php include("../common/header.php"); ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div id="responseAlert"></div>

                <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-5">
                    <div class="card-header bg-primary-theme p-4 text-white">
                        <h4 class="mb-1 fw-bold"><i class="fas fa-edit me-2"></i><?php echo $is_edit_mode ? 'Update Admission Data' : 'Official Admission Form'; ?></h4>
                        <p class="mb-0 small opacity-75"><?php echo $is_edit_mode ? 'Modify your existing application details.' : 'All fields marked with (*) are mandatory for registration.'; ?></p>
                    </div>

                    <div class="card-body p-4 p-md-5 bg-white">
                        <form id="finalAdmissionForm" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="<?php echo $is_edit_mode ? 'edit' : 'add'; ?>">
                            <?php if ($is_edit_mode): ?>
                                <input type="hidden" name="id" value="<?php echo $existing_data['id']; ?>">
                                <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                                <input type="hidden" name="admission_date" value="<?php echo $existing_data['admission_date']; ?>">
                            <?php endif; ?>

                            <!-- Section: Account Setup -->
                            <?php if (!$user_id): ?>
                                <div class="alert alert-primary border-0 rounded-4 p-4 mb-5">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="small fw-bold">Username *</label>
                                            <input type="text" class="form-control" name="username" placeholder="Username" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="small fw-bold">Password *</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" name="password" id="reg-password" placeholder="Password" required>
                                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('reg-password', this)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Section 1: Academic Choice -->
                            <div class="form-section-title">Academic Selection</div>
                            <div class="row g-3 mb-5">
                                <div class="col-md-4">
                                    <label class="small fw-bold">Academic Session *</label>
                                    <select class="form-select" name="session_id" required>
                                        <option value="">Select Session</option>
                                        <?php
                                        $sessStmt = $pdo->query("SELECT id, session_label FROM admission_sessions WHERE status = 1");
                                        while ($s = $sessStmt->fetch()) {
                                            $sel = (isset($existing_data['session_id']) && $existing_data['session_id'] == $s['id']) ? 'selected' : '';
                                            echo "<option value='{$s['id']}' $sel>{$s['session_label']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-4"><label class="small fw-bold">Category *</label><select class="form-select" name="course_category" id="adm_category" required>
                                        <option value="">Select Category</option>
                                    </select></div>
                                <div class="col-md-4"><label class="small fw-bold">Course *</label><select class="form-select" name="course_id" id="adm_course" required disabled>
                                        <option value="">Select Category First</option>
                                    </select></div>

                                <div class="col-md-4"><label class="small fw-bold">State *</label><select class="form-select" id="adm_state" required>
                                        <option value="">Select State</option>
                                    </select></div>
                                <div class="col-md-4"><label class="small fw-bold">District *</label><select class="form-select" id="adm_district" required disabled>
                                        <option value="">Select State First</option>
                                    </select></div>
                                <div class="col-md-4"><label class="small fw-bold">Center *</label><select class="form-select" name="center_id" id="adm_center" required disabled>
                                        <option value="">Select District First</option>
                                    </select></div>

                                <div class="col-md-4"><label class="small fw-bold">Course Duration</label><input type="text" class="form-control bg-light" id="display_duration" readonly placeholder="Duration"></div>
                                <div class="col-md-4"><label class="small fw-bold">Eligibility</label><input type="text" class="form-control bg-light" id="display_eligibility" readonly placeholder="Eligibility"></div>

                            </div>

                            <!-- Section 2: Student Profile -->
                            <div class="form-section-title">Student Personal Information</div>
                            <div class="row g-3 mb-5">
                                <div class="col-md-6"><label class="small fw-bold">Full Name *</label><input type="text" class="form-control" name="full_name" value="<?php echo $existing_data['full_name'] ?? ''; ?>" required></div>
                                <div class="col-md-3"><label class="small fw-bold">DOB *</label><input type="date" class="form-control" name="dob" value="<?php echo $existing_data['dob'] ?? ''; ?>" required></div>
                                <div class="col-md-3"><label class="small fw-bold">Gender *</label><select class="form-select" name="gender" required>
                                        <option value="male" <?php echo ($existing_data['gender'] ?? '') == 'male' ? 'selected' : ''; ?>>Male</option>
                                        <option value="female" <?php echo ($existing_data['gender'] ?? '') == 'female' ? 'selected' : ''; ?>>Female</option>
                                    </select></div>

                                <div class="col-md-4">
                                    <label class="small fw-bold">Religion *</label>
                                    <select class="form-select" name="religion" required>
                                        <option value="">Select Religion</option>
                                        <option value="Hindu" <?php echo ($existing_data['religion'] ?? '') == 'Hindu' ? 'selected' : ''; ?>>Hindu</option>
                                        <option value="Muslim" <?php echo ($existing_data['religion'] ?? '') == 'Muslim' ? 'selected' : ''; ?>>Muslim</option>
                                        <option value="Sikh" <?php echo ($existing_data['religion'] ?? '') == 'Sikh' ? 'selected' : ''; ?>>Sikh</option>
                                        <option value="Christian" <?php echo ($existing_data['religion'] ?? '') == 'Christian' ? 'selected' : ''; ?>>Christian</option>
                                        <option value="Buddhist" <?php echo ($existing_data['religion'] ?? '') == 'Buddhist' ? 'selected' : ''; ?>>Buddhist</option>
                                        <option value="Jain" <?php echo ($existing_data['religion'] ?? '') == 'Jain' ? 'selected' : ''; ?>>Jain</option>
                                        <option value="Other" <?php echo ($existing_data['religion'] ?? '') == 'Other' ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="small fw-bold">Caste *</label>
                                    <select class="form-select" name="caste" required>
                                        <option value="">Select Caste</option>
                                        <option value="General" <?php echo ($existing_data['caste'] ?? '') == 'General' ? 'selected' : ''; ?>>General</option>
                                        <option value="OBC" <?php echo ($existing_data['caste'] ?? '') == 'OBC' ? 'selected' : ''; ?>>OBC</option>
                                        <option value="SC" <?php echo ($existing_data['caste'] ?? '') == 'SC' ? 'selected' : ''; ?>>SC</option>
                                        <option value="ST" <?php echo ($existing_data['caste'] ?? '') == 'ST' ? 'selected' : ''; ?>>ST</option>
                                        <option value="Other" <?php echo ($existing_data['caste'] ?? '') == 'Other' ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="small fw-bold">Blood Group *</label>
                                    <select class="form-select" name="blood_group" required>
                                        <option value="">Select Blood Group</option>
                                        <option value="A+" <?php echo ($existing_data['blood_group'] ?? '') == 'A+' ? 'selected' : ''; ?>>A+</option>
                                        <option value="A-" <?php echo ($existing_data['blood_group'] ?? '') == 'A-' ? 'selected' : ''; ?>>A-</option>
                                        <option value="B+" <?php echo ($existing_data['blood_group'] ?? '') == 'B+' ? 'selected' : ''; ?>>B+</option>
                                        <option value="B-" <?php echo ($existing_data['blood_group'] ?? '') == 'B-' ? 'selected' : ''; ?>>B-</option>
                                        <option value="O+" <?php echo ($existing_data['blood_group'] ?? '') == 'O+' ? 'selected' : ''; ?>>O+</option>
                                        <option value="O-" <?php echo ($existing_data['blood_group'] ?? '') == 'O-' ? 'selected' : ''; ?>>O-</option>
                                        <option value="AB+" <?php echo ($existing_data['blood_group'] ?? '') == 'AB+' ? 'selected' : ''; ?>>AB+</option>
                                        <option value="AB-" <?php echo ($existing_data['blood_group'] ?? '') == 'AB-' ? 'selected' : ''; ?>>AB-</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="small fw-bold">Last Qualification *</label>
                                    <select class="form-select" name="qualification" required>
                                        <option value="">Select Qualification</option>
                                        <?php
                                        $quals = ['10th', '12th', 'Graduation', 'Diploma', 'Others'];
                                        foreach ($quals as $q):
                                            $sel = (isset($existing_data['qualification']) && $existing_data['qualification'] == $q) ? 'selected' : '';
                                            echo "<option value='$q' $sel>$q</option>";
                                        endforeach;
                                        ?>
                                    </select>
                                </div>

                                <!-- Section 3: Address -->
                                <div class="form-section-title">Contact & Address</div>
                                <div class="row g-3 mb-5">
                                    <div class="col-md-6"><label class="small fw-bold">Email *</label><input type="email" class="form-control" name="email" value="<?php echo $existing_data['email'] ?? ''; ?>" required></div>
                                    <div class="col-md-6"><label class="small fw-bold">Mobile *</label><input type="tel" class="form-control" name="mobile" value="<?php echo $existing_data['mobile'] ?? ''; ?>" required></div>
                                    <div class="col-md-12"><label class="small fw-bold">Full Address *</label><textarea class="form-control" name="address" rows="2" required><?php echo $existing_data['address'] ?? ''; ?></textarea></div>

                                    <div class="col-md-4"><label class="small fw-bold">Country *</label><select class="form-select" name="country_id" id="addr_country" required>
                                            <option value="">Select Country</option>
                                        </select></div>
                                    <div class="col-md-4"><label class="small fw-bold">State *</label><select class="form-select" name="state_id" id="addr_state" required disabled>
                                            <option value="">Select Country First</option>
                                        </select></div>
                                    <div class="col-md-4"><label class="small fw-bold">District *</label><select class="form-select" name="district_id" id="addr_district" required disabled>
                                            <option value="">Select State First</option>
                                        </select></div>
                                    <div class="col-md-4"><label class="small fw-bold">City *</label><select class="form-select" name="city_id" id="addr_city" required disabled>
                                            <option value="">Select District First</option>
                                        </select></div>

                                    <div class="col-md-4 mt-3"><label class="small fw-bold">Pin Code *</label><input type="text" class="form-control" name="pincode" value="<?php echo $existing_data['pincode'] ?? ''; ?>" required pattern="[0-9]+" title="Only numbers allowed"></div>
                                </div>

                                <!-- Section 4: Family Details -->
                                <div class="form-section-title">Family Details</div>
                                <div class="row g-3 mb-5">
                                    <?php if (!$is_edit_mode): ?>
                                        <input type="hidden" name="admission_date" value="<?php echo date('Y-m-d'); ?>">
                                    <?php endif; ?>
                                    <div class="col-md-4"><label class="small fw-bold">Father's Name *</label><input type="text" class="form-control" name="father_name" value="<?php echo $existing_data['father_name'] ?? ''; ?>" required></div>
                                    <div class="col-md-4"><label class="small fw-bold">Mother's Name *</label><input type="text" class="form-control" name="mother_name" value="<?php echo $existing_data['mother_name'] ?? ''; ?>" required></div>
                                    <div class="col-md-4">
                                        <label class="small fw-bold">Guardian Mobile</label>
                                        <input type="tel" class="form-control" name="guardian_phone" value="<?php echo $existing_data['guardian_phone'] ?? ''; ?>">
                                    </div>
                                </div>

                                <!-- Section 5: Document Uploads -->
                                <div class="form-section-title"><i class="fas fa-cloud-upload-alt me-2"></i>UPLOAD REQUIRED DOCUMENTS</div>
                                <div class="row g-4 mb-4">
                                    <!-- Row 1 -->
                                    <div class="col-md-6 col-lg-3">
                                        <div class="p-3 border rounded text-center h-100 bg-white shadow-sm">
                                            <i class="fas fa-user-circle fs-3 text-primary mb-2"></i>
                                            <label class="form-label d-block small fw-bold">Student Photo <span class="text-danger">*</span></label>
                                            <input type="file" class="form-control form-control-sm" name="student_photo" accept="image/*" <?php echo !$existing_data ? 'required' : ''; ?>>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-3">
                                        <div class="p-3 border rounded text-center h-100 bg-white shadow-sm">
                                            <i class="fas fa-signature fs-3 text-primary mb-2"></i>
                                            <label class="form-label d-block small fw-bold">Student Signature <span class="text-danger">*</span></label>
                                            <input type="file" class="form-control form-control-sm" name="signature" accept="image/*" <?php echo !$existing_data ? 'required' : ''; ?>>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-3">
                                        <div class="p-3 border rounded text-center h-100 bg-white shadow-sm">
                                            <i class="fas fa-id-card fs-3 text-primary mb-2"></i>
                                            <label class="form-label d-block small fw-bold">Aadhar (Front) <span class="text-danger">*</span></label>
                                            <input type="file" class="form-control form-control-sm" name="aadhar_front" accept="image/*,application/pdf" <?php echo !$existing_data ? 'required' : ''; ?>>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-3">
                                        <div class="p-3 border rounded text-center h-100 bg-white shadow-sm">
                                            <i class="fas fa-id-card fs-3 text-primary mb-2"></i>
                                            <label class="form-label d-block small fw-bold">Aadhar (Back) <span class="text-danger">*</span></label>
                                            <input type="file" class="form-control form-control-sm" name="aadhar_back" accept="image/*,application/pdf" <?php echo !$existing_data ? 'required' : ''; ?>>
                                        </div>
                                    </div>

                                    <!-- Row 2 -->
                                    <div class="col-md-6 col-lg-3">
                                        <div class="p-3 border rounded text-center h-100 bg-white shadow-sm">
                                            <i class="fas fa-file-invoice fs-3 text-success mb-2"></i>
                                            <label class="form-label d-block small fw-bold">10th Marksheet</label>
                                            <input type="file" class="form-control form-control-sm" name="marksheet_10th" accept="image/*,application/pdf">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-3">
                                        <div class="p-3 border rounded text-center h-100 bg-white shadow-sm">
                                            <i class="fas fa-file-invoice fs-3 text-success mb-2"></i>
                                            <label class="form-label d-block small fw-bold">12th Marksheet</label>
                                            <input type="file" class="form-control form-control-sm" name="marksheet_12th" accept="image/*,application/pdf">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-3">
                                        <div class="p-3 border rounded text-center h-100 bg-white shadow-sm">
                                            <i class="fas fa-id-badge fs-3 text-info mb-2"></i>
                                            <label class="form-label d-block small fw-bold">Parent Aadhar (F)</label>
                                            <input type="file" class="form-control form-control-sm" name="parent_aadhar_front" accept="image/*,application/pdf">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-3">
                                        <div class="p-3 border rounded text-center h-100 bg-white shadow-sm">
                                            <i class="fas fa-id-badge fs-3 text-info mb-2"></i>
                                            <label class="form-label d-block small fw-bold">Parent Aadhar (B)</label>
                                            <input type="file" class="form-control form-control-sm" name="parent_aadhar_back" accept="image/*,application/pdf">
                                        </div>
                                    </div>

                                    <!-- Row 3 -->
                                    <div class="col-md-6 col-lg-3">
                                        <div class="p-3 border rounded text-center h-100 bg-white shadow-sm">
                                            <i class="fas fa-passport fs-3 text-warning mb-2"></i>
                                            <label class="form-label d-block small fw-bold">Student ID Proof <span class="text-danger">*</span></label>
                                            <input type="file" class="form-control form-control-sm" name="id_proof" accept="image/*,application/pdf" <?php echo !$existing_data ? 'required' : ''; ?>>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-3">
                                        <div class="p-3 border rounded text-center h-100 bg-white shadow-sm">
                                            <i class="fas fa-user-shield fs-3 text-warning mb-2"></i>
                                            <label class="form-label d-block small fw-bold">Guardian ID Proof <span class="text-danger">*</span></label>
                                            <input type="file" class="form-control form-control-sm" name="guardian_doc" accept="image/*,application/pdf" <?php echo !$existing_data ? 'required' : ''; ?>>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-12 text-center mt-5">
                                    <button type="submit" id="submitBtn" class="btn btn-primary-theme px-5 py-3 rounded-pill fw-bold shadow-lg">
                                        <i class="fas fa-paper-plane me-2"></i> <?php echo $is_edit_mode ? 'UPDATE MY APPLICATION' : 'SUBMIT FINAL APPLICATION'; ?>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include("../common/footer.php"); ?>
    <?php include("../common/requirejs.php"); ?>

    <script>
        const PREFILL_DATA = <?php echo $is_edit_mode ? json_encode([
                                    'course_category' => $existing_data['course_category'] ?? '',
                                    'course_id' => $existing_data['course_id'],
                                    'state' => $existing_data['center_state'] ?? '',
                                    'district' => $existing_data['center_district'] ?? '',
                                    'center_id' => $existing_data['center_id'],
                                    'country_id' => $existing_data['country_id'],
                                    'state_id' => $existing_data['state_id'],
                                    'district_id' => $existing_data['district_id'],
                                    'city_id' => $existing_data['city_id']
                                ]) : 'null'; ?>;

        document.addEventListener('DOMContentLoaded', function() {
            // Dropdown Initializations (State/Category)
            fetch('../ajax/get_locations.php?type=center_states').then(r => r.json()).then(data => {
                data.forEach(s => document.getElementById('adm_state').innerHTML += `<option value="${s}">${s}</option>`);
            });
            fetch('../ajax/get_courses.php?type=categories').then(r => r.json()).then(data => {
                data.forEach(c => document.getElementById('adm_category').innerHTML += `<option value="${c}">${c.toUpperCase()}</option>`);
            });

            // Dependent Dropdown Logic (Academic)
            document.getElementById('adm_state').addEventListener('change', function() {
                const dist = document.getElementById('adm_district');
                dist.disabled = !this.value;
                dist.innerHTML = '<option value="">Loading...</option>';
                if (this.value) fetch(`../ajax/get_locations.php?type=center_districts&state=${this.value}`)
                    .then(r => r.json()).then(data => {
                        dist.innerHTML = '<option value="">Select District</option>';
                        data.forEach(d => dist.innerHTML += `<option value="${d}">${d}</option>`);
                        if (PREFILL_DATA && PREFILL_DATA.district) dist.value = PREFILL_DATA.district;
                    });
            });

            document.getElementById('adm_district').addEventListener('change', function() {
                const ctr = document.getElementById('adm_center');
                ctr.disabled = !this.value;
                ctr.innerHTML = '<option value="">Loading...</option>';
                if (this.value) fetch(`../ajax/get_locations.php?type=centers&district_name=${this.value}`)
                    .then(r => r.json()).then(data => {
                        ctr.innerHTML = '<option value="">Select Center</option>';
                        data.forEach(c => ctr.innerHTML += `<option value="${c.id}">${c.name}</option>`);
                        if (PREFILL_DATA && PREFILL_DATA.center_id) ctr.value = PREFILL_DATA.center_id;
                    });
            });

            // Update Session Display


            let loadedCourses = [];
            document.getElementById('adm_category').addEventListener('change', function() {
                const crs = document.getElementById('adm_course');
                crs.disabled = !this.value;
                crs.innerHTML = '<option value="">Loading...</option>';
                if (this.value) fetch(`../ajax/get_courses.php?type=courses&category=${this.value}`)
                    .then(r => r.json()).then(data => {
                        loadedCourses = data;
                        crs.innerHTML = '<option value="">Select Course</option>';
                        data.forEach(c => crs.innerHTML += `<option value="${c.id}">${c.name}</option>`);
                        if (PREFILL_DATA && PREFILL_DATA.course_id) {
                            crs.value = PREFILL_DATA.course_id;
                            crs.dispatchEvent(new Event('change'));
                        }
                    });
            });

            document.getElementById('adm_course').addEventListener('change', function() {
                const course = loadedCourses.find(c => c.id == this.value);
                if (course) {
                    document.getElementById('display_duration').value = (course.duration_months || 'N/A') + ' Months';
                    document.getElementById('display_eligibility').value = course.eligibility || 'N/A';
                } else {
                    document.getElementById('display_duration').value = '';
                    document.getElementById('display_eligibility').value = '';
                }
            });

            // Dependent Dropdown Logic (Address)
            fetch('../ajax/get_locations.php?type=countries').then(r => r.json()).then(data => {
                data.forEach(c => document.getElementById('addr_country').innerHTML += `<option value="${c.id}">${c.name}</option>`);
            });

            document.getElementById('addr_country').addEventListener('change', function() {
                const st = document.getElementById('addr_state');
                st.disabled = !this.value;
                st.innerHTML = '<option value="">Loading...</option>';
                document.getElementById('addr_district').disabled = true;
                document.getElementById('addr_district').innerHTML = '<option value="">Select State First</option>';
                document.getElementById('addr_city').disabled = true;
                document.getElementById('addr_city').innerHTML = '<option value="">Select District First</option>';
                if (this.value) fetch(`../ajax/get_locations.php?type=states&country_id=${this.value}`)
                    .then(r => r.json()).then(data => {
                        st.innerHTML = '<option value="">Select State</option>';
                        data.forEach(d => st.innerHTML += `<option value="${d.id}">${d.name}</option>`);
                        if (PREFILL_DATA && PREFILL_DATA.state_id) st.value = PREFILL_DATA.state_id;
                    });
            });

            document.getElementById('addr_state').addEventListener('change', function() {
                const dist = document.getElementById('addr_district');
                dist.disabled = !this.value;
                dist.innerHTML = '<option value="">Loading...</option>';
                document.getElementById('addr_city').disabled = true;
                document.getElementById('addr_city').innerHTML = '<option value="">Select District First</option>';
                if (this.value) fetch(`../ajax/get_locations.php?type=districts&state_id=${this.value}`)
                    .then(r => r.json()).then(data => {
                        dist.innerHTML = '<option value="">Select District</option>';
                        data.forEach(d => dist.innerHTML += `<option value="${d.id}">${d.name}</option>`);
                        if (PREFILL_DATA && PREFILL_DATA.district_id) dist.value = PREFILL_DATA.district_id;
                    });
            });

            document.getElementById('addr_district').addEventListener('change', function() {
                const city = document.getElementById('addr_city');
                city.disabled = !this.value;
                city.innerHTML = '<option value="">Loading...</option>';
                if (this.value) fetch(`../ajax/get_locations.php?type=cities&district_id=${this.value}`)
                    .then(r => r.json()).then(data => {
                        city.innerHTML = '<option value="">Select City</option>';
                        data.forEach(c => city.innerHTML += `<option value="${c.id}">${c.name}</option>`);
                        if (PREFILL_DATA && PREFILL_DATA.city_id) city.value = PREFILL_DATA.city_id;
                    });
            });

            // --- Auto-trigger Chain for Edit Mode ---
            if (PREFILL_DATA) {
                // 1. Academic Chain
                setTimeout(() => {
                    if (PREFILL_DATA.course_category) {
                        document.getElementById('adm_category').value = PREFILL_DATA.course_category;
                        document.getElementById('adm_category').dispatchEvent(new Event('change'));
                    }
                    if (PREFILL_DATA.state) {
                        document.getElementById('adm_state').value = PREFILL_DATA.state;
                        document.getElementById('adm_state').dispatchEvent(new Event('change'));
                    }
                }, 500);

                // 2. Address Chain
                setTimeout(() => {
                    if (PREFILL_DATA.country_id) {
                        document.getElementById('addr_country').value = PREFILL_DATA.country_id;
                        document.getElementById('addr_country').dispatchEvent(new Event('change'));
                    }
                }, 800);

                // Sub-levels need more time for chain reactions
                setTimeout(() => {
                    if (PREFILL_DATA.course_id) document.getElementById('adm_course').value = PREFILL_DATA.course_id;
                    if (PREFILL_DATA.district) document.getElementById('adm_district').dispatchEvent(new Event('change'));
                    if (PREFILL_DATA.state_id) document.getElementById('addr_state').dispatchEvent(new Event('change'));
                }, 1200);

                setTimeout(() => {
                    if (PREFILL_DATA.center_id) document.getElementById('adm_center').value = PREFILL_DATA.center_id;
                    if (PREFILL_DATA.district_id) document.getElementById('addr_district').dispatchEvent(new Event('change'));
                }, 1800);

                setTimeout(() => {
                    if (PREFILL_DATA.city_id) document.getElementById('addr_city').value = PREFILL_DATA.city_id;
                }, 2500);
            }

            // Form Submission with Files
            document.getElementById('finalAdmissionForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const btn = document.getElementById('submitBtn');
                btn.disabled = true;
                btn.innerHTML = 'Uploading Files & Saving...';

                fetch('../ajax/admission_handler.php', {
                        method: 'POST',
                        body: new FormData(this)
                    })
                    .then(r => r.json()).then(data => {
                        const alert = document.getElementById('responseAlert');
                        if (data.status === 'success') {
                            alert.innerHTML = `<div class="alert alert-success shadow-sm rounded-4 mb-4">${data.message}</div>`;
                            setTimeout(() => window.location.href = data.redirect, 2000);
                        } else {
                            alert.innerHTML = `<div class="alert alert-danger shadow-sm rounded-4 mb-4">${data.message}</div>`;
                            btn.disabled = false;
                            btn.innerHTML = '<i class="fas fa-cloud-upload-alt me-2"></i> SUBMIT FINAL APPLICATION';
                        }
                    });
            });
        });

        function togglePassword(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
</body>

</html>