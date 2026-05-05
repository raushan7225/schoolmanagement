<?php
require_once('../common/config.php');

$user_id = $_SESSION['user_id'] ?? null;
$profile = null;
if ($user_id) {
    $stmt = $pdo->prepare("SELECT * FROM admissions WHERE user_id = ? ORDER BY id DESC LIMIT 1");
    $stmt->execute([$user_id]);
    $profile = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Academic Portal | Online Enquiry</title>
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
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="card-header bg-primary-theme p-4 text-white">
                        <h4 class="mb-1 fw-bold"><i class="fas fa-question-circle me-2"></i>Online Student Enquiry</h4>
                        <p class="mb-0 small opacity-75">Tell us about your interests and our advisors will help you choose the right path.</p>
                    </div>
                    <div class="card-body p-4 p-md-5 bg-white">
                        <form id="enhancedEnquiryForm" class="row g-4">
                            <input type="hidden" name="action" value="add">
                            <input type="hidden" name="source" value="online">
                            
                            <!-- Section 1: Location & Course -->
                            <div class="col-12">
                                <div class="form-section-title mb-0">1. Preferred Location & Course</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Select State *</label>
                                <select class="form-select" name="state" id="enq_state" required>
                                    <option value="">Select State</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Select District *</label>
                                <select class="form-select" name="district" id="enq_district" required disabled>
                                    <option value="">Select State First</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Select Franchise / Center *</label>
                                <select class="form-select" name="center_id" id="enq_center" required disabled>
                                    <option value="">Select District First</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Academic Session *</label>
                                <select class="form-select" name="session_id" required>
                                    <option value="">Select Session</option>
                                    <?php
                                    $sessStmt = $pdo->query("SELECT id, session_label FROM admission_sessions WHERE status = 1");
                                    while($s = $sessStmt->fetch()) {
                                        echo "<option value='{$s['id']}'>{$s['session_label']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Course Category *</label>
                                <select class="form-select" name="course_category" id="enq_category" required>
                                    <option value="">Select Category</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Target Course *</label>
                                <select class="form-select" name="course_id" id="enq_course" required disabled>
                                    <option value="">Select Category First</option>
                                </select>
                            </div>

                            <!-- Section 2: Personal Profile -->
                            <div class="col-12 mt-5">
                                <div class="form-section-title mb-0">2. Personal Details</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Full Name *</label>
                                <input type="text" class="form-control" name="full_name" value="<?php echo $profile['full_name'] ?? ''; ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Date of Birth *</label>
                                <input type="date" class="form-control" name="dob" value="<?php echo $profile['dob'] ?? ''; ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Gender *</label>
                                <select class="form-select" name="gender" required>
                                    <option value="male" <?php echo ($profile['gender'] ?? '') == 'male' ? 'selected' : ''; ?>>Male</option>
                                    <option value="female" <?php echo ($profile['gender'] ?? '') == 'female' ? 'selected' : ''; ?>>Female</option>
                                    <option value="other" <?php echo ($profile['gender'] ?? '') == 'other' ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Email Address</label>
                                <input type="email" class="form-control" name="email" value="<?php echo $profile['email'] ?? ''; ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Phone Number *</label>
                                <input type="tel" class="form-control" name="mobile" value="<?php echo $profile['mobile'] ?? ''; ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Current Qualification *</label>
                                <select class="form-select" name="qualification" required>
                                    <option value="">Select Qualification</option>
                                    <?php 
                                    $quals = ['10th', '12th', 'Graduation', 'Diploma', 'Others'];
                                    foreach($quals as $q):
                                        echo "<option value='$q'>$q</option>";
                                    endforeach;
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Probable Admission Date</label>
                                <input type="date" class="form-control" name="prob_admission_date">
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-bold">Street Address *</label>
                                <textarea class="form-control" name="address" rows="2" required></textarea>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Country *</label>
                                <select class="form-select" name="country_id" id="enq_country" required><option value="">Select Country</option></select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">State *</label>
                                <select class="form-select" name="state_id" id="enq_addr_state" required disabled><option value="">Select Country First</option></select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">District *</label>
                                <select class="form-select" name="district_id" id="enq_addr_district" required disabled><option value="">Select State First</option></select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">City *</label>
                                <select class="form-select" name="city_id" id="enq_addr_city" required disabled><option value="">Select District First</option></select>
                            </div>
                            <div class="col-md-12 mt-2">
                                <label class="form-label small fw-bold">Pin Code *</label>
                                <input type="text" class="form-control" name="pincode" placeholder="6-digit Pin Code" required pattern="[0-9]{6}" title="6 digit pin code">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold">Your Query / Message *</label>
                                <textarea class="form-control" name="message" rows="3" required></textarea>
                            </div>

                            <div class="col-12 text-center mt-5">
                                <button type="submit" id="submitBtn" class="btn btn-primary-theme px-5 py-3 rounded-pill fw-bold shadow">
                                    <i class="fas fa-paper-plane me-2"></i> SUBMIT ENQUIRY
                                </button>
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
    const PREFILL_DATA = <?php echo $profile ? json_encode([
        'address' => $profile['address'],
        'country_id' => $profile['country_id'],
        'state_id' => $profile['state_id'],
        'district_id' => $profile['district_id'],
        'city_id' => $profile['city_id'],
        'center_id' => $profile['center_id']
    ]) : 'null'; ?>;

    document.addEventListener('DOMContentLoaded', function() {
        // Pre-fill Address if available
        if(PREFILL_DATA && PREFILL_DATA.address) {
            document.querySelector('textarea[name="address"]').value = PREFILL_DATA.address;
        }

        // Load Initial States & Categories
        fetch('../ajax/get_locations.php?type=center_states').then(r => r.json()).then(data => {
            data.forEach(s => document.getElementById('enq_state').innerHTML += `<option value="${s}">${s}</option>`);
        });
        fetch('../ajax/get_courses.php?type=categories').then(r => r.json()).then(data => {
            data.forEach(c => document.getElementById('enq_category').innerHTML += `<option value="${c}">${c.toUpperCase()}</option>`);
        });

        // Dependent Dropdowns (Academic)
        document.getElementById('enq_state').addEventListener('change', function() {
            const dist = document.getElementById('enq_district');
            dist.disabled = !this.value; dist.innerHTML = '<option value="">Loading...</option>';
            if(this.value) fetch(`../ajax/get_locations.php?type=center_districts&state=${this.value}`)
                .then(r => r.json()).then(data => {
                    dist.innerHTML = '<option value="">Select District</option>';
                    data.forEach(d => dist.innerHTML += `<option value="${d}">${d}</option>`);
                });
        });

        document.getElementById('enq_district').addEventListener('change', function() {
            const ctr = document.getElementById('enq_center');
            ctr.disabled = !this.value; ctr.innerHTML = '<option value="">Loading...</option>';
            if(this.value) fetch(`../ajax/get_locations.php?type=centers&district_name=${this.value}`)
                .then(r => r.json()).then(data => {
                    ctr.innerHTML = '<option value="">Select Center</option>';
                    data.forEach(c => ctr.innerHTML += `<option value="${c.id}">${c.name}</option>`);
                    if(PREFILL_DATA && PREFILL_DATA.center_id) ctr.value = PREFILL_DATA.center_id;
                });
        });

        document.getElementById('enq_category').addEventListener('change', function() {
            const crs = document.getElementById('enq_course');
            crs.disabled = !this.value; crs.innerHTML = '<option value="">Loading...</option>';
            if(this.value) fetch(`../ajax/get_courses.php?type=courses&category=${this.value}`)
                .then(r => r.json()).then(data => {
                    crs.innerHTML = '<option value="">Select Course</option>';
                    data.forEach(c => crs.innerHTML += `<option value="${c.id}">${c.name}</option>`);
                });
        });

        // Dependent Dropdown Logic (Address)
        fetch('../ajax/get_locations.php?type=countries').then(r => r.json()).then(data => {
            const el = document.getElementById('enq_country');
            data.forEach(c => el.innerHTML += `<option value="${c.id}">${c.name}</option>`);
            if(PREFILL_DATA && PREFILL_DATA.country_id) {
                el.value = PREFILL_DATA.country_id;
                el.dispatchEvent(new Event('change'));
            }
        });

        document.getElementById('enq_country').addEventListener('change', function() {
            const st = document.getElementById('enq_addr_state');
            st.disabled = !this.value; st.innerHTML = '<option value="">Loading...</option>';
            document.getElementById('enq_addr_district').disabled = true; document.getElementById('enq_addr_district').innerHTML = '<option value="">Select State First</option>';
            document.getElementById('enq_addr_city').disabled = true; document.getElementById('enq_addr_city').innerHTML = '<option value="">Select District First</option>';
            if(this.value) fetch(`../ajax/get_locations.php?type=states&country_id=${this.value}`)
                .then(r => r.json()).then(data => {
                    st.innerHTML = '<option value="">Select State</option>';
                    data.forEach(d => st.innerHTML += `<option value="${d.id}">${d.name}</option>`);
                    if(PREFILL_DATA && PREFILL_DATA.state_id) {
                        st.value = PREFILL_DATA.state_id;
                        st.dispatchEvent(new Event('change'));
                    }
                });
        });

        document.getElementById('enq_addr_state').addEventListener('change', function() {
            const dist = document.getElementById('enq_addr_district');
            dist.disabled = !this.value; dist.innerHTML = '<option value="">Loading...</option>';
            document.getElementById('enq_addr_city').disabled = true; document.getElementById('enq_addr_city').innerHTML = '<option value="">Select District First</option>';
            if(this.value) fetch(`../ajax/get_locations.php?type=districts&state_id=${this.value}`)
                .then(r => r.json()).then(data => {
                    dist.innerHTML = '<option value="">Select District</option>';
                    data.forEach(d => dist.innerHTML += `<option value="${d.id}">${d.name}</option>`);
                    if(PREFILL_DATA && PREFILL_DATA.district_id) {
                        dist.value = PREFILL_DATA.district_id;
                        dist.dispatchEvent(new Event('change'));
                    }
                });
        });

        document.getElementById('enq_addr_district').addEventListener('change', function() {
            const city = document.getElementById('enq_addr_city');
            city.disabled = !this.value; city.innerHTML = '<option value="">Loading...</option>';
            if(this.value) fetch(`../ajax/get_locations.php?type=cities&district_id=${this.value}`)
                .then(r => r.json()).then(data => {
                    city.innerHTML = '<option value="">Select City</option>';
                    data.forEach(c => city.innerHTML += `<option value="${c.id}">${c.name}</option>`);
                    if(PREFILL_DATA && PREFILL_DATA.city_id) city.value = PREFILL_DATA.city_id;
                });
        });

        // Form Submission
        document.getElementById('enhancedEnquiryForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('submitBtn');
            btn.disabled = true; btn.innerHTML = 'Sending...';
            
            fetch('../ajax/enquiry_handler.php', { method: 'POST', body: new FormData(this) })
            .then(r => r.json()).then(data => {
                const alert = document.getElementById('responseAlert');
                if(data.status === 'success') {
                    alert.innerHTML = `<div class="alert alert-success shadow-sm rounded-4 mb-4">
                        <strong>Success!</strong> ${data.message}<br>
                        <small class="text-muted">Redirecting you in 5 seconds...</small>
                    </div>`;
                    this.reset();
                    setTimeout(() => {
                        window.location.href = PREFILL_DATA ? 'index.php' : '../index.php';
                    }, 5000);
                } else {
                    alert.innerHTML = `<div class="alert alert-danger shadow-sm rounded-4 mb-4">${data.message}</div>`;
                    btn.disabled = false; btn.innerHTML = '<i class="fas fa-paper-plane me-2"></i> SUBMIT ENQUIRY';
                }
            });
        });
    });
    </script>
</body>
</html>