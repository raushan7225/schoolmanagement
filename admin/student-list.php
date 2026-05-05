<?php
// admin/student-list.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Search / Filter logic
$where = "WHERE a.approval_status = 'approved' AND a.status = 1";
$params = [];

$filter_center = (int)($_GET['center_id'] ?? 0);
$filter_course = (int)($_GET['course_id'] ?? 0);
$filter_status = $_GET['status'] ?? '';
$search = trim($_GET['search'] ?? '');

if ($filter_center) {
    $where .= " AND a.center_id = ?";
    $params[] = $filter_center;
}
if ($filter_course) {
    $where .= " AND a.course_id = ?";
    $params[] = $filter_course;
}
if ($filter_status !== '') {
    $where .= " AND u.status = ?";
    $params[] = (int)$filter_status;
}
if ($search) {
    $where .= " AND (a.full_name LIKE ? OR a.mobile LIKE ? OR a.id LIKE ? OR a.roll_number LIKE ?)";
    $p = "%$search%";
    $params[] = $p; $params[] = $p; $params[] = $p; $params[] = $p;
}

$stmt = $pdo->prepare("
    SELECT a.*, c.name as course_name, f.center_name, f.center_code,
           u.status as user_status
    FROM admissions a
    LEFT JOIN courses c ON a.course_id = c.id
    LEFT JOIN franchises f ON a.center_id = f.id
    LEFT JOIN users u ON a.user_id = u.id
    $where
    ORDER BY a.id DESC
");
$stmt->execute($params);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch franchises for filter
$franchises = $pdo->query("SELECT id, center_name, center_code FROM franchises WHERE status = 1 ORDER BY center_name ASC")->fetchAll(PDO::FETCH_ASSOC);
$courseList = $pdo->query("SELECT id, name FROM courses WHERE status = 1 ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
$categories = $pdo->query("SELECT id, name as category_name FROM course_categories WHERE status = 1 ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>Student Management</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Student Management</li>
            <li class="breadcrumb-item active">Student List</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <!-- Summary Stats -->
        <div class="col-md-3">
            <div class="card info-card sales-card">
                <div class="card-body py-3 d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-primary-light text-primary">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div class="ps-3">
                        <h6 class="mb-0 fs-5"><?php echo count($students); ?></h6>
                        <span class="text-muted small">Total Students</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card info-card revenue-card">
                <div class="card-body py-3 d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-success-light text-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="ps-3">
                        <h6 class="mb-0 fs-5 text-success"><?php echo count(array_filter($students, function($s) { return $s['user_status'] == 1; })); ?></h6>
                        <span class="text-muted small">Active Accounts</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-body py-2">
                    <form class="row g-2 align-items-center" method="GET">
                        <div class="col-md-3">
                            <select name="course_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">All Courses</option>
                                <?php foreach($courseList as $cl): ?>
                                    <option value="<?php echo $cl['id']; ?>" <?php echo $filter_course == $cl['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($cl['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select name="center_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">All Centers</option>
                                <?php foreach($franchises as $fr): ?>
                                    <option value="<?php echo $fr['id']; ?>" <?php echo $filter_center == $fr['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($fr['center_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">All Status</option>
                                <option value="1" <?php echo $filter_status === '1' ? 'selected' : ''; ?>>Active</option>
                                <option value="0" <?php echo $filter_status === '0' ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-sm btn-primary flex-grow-1 fw-bold"><i class="fas fa-filter me-2"></i>APPLY</button>
                            <a href="student-list.php" class="btn btn-sm btn-outline-secondary fw-bold">RESET</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle datatable-premium"
                               data-add-btn='{"text":"Add Student","onclick":"new bootstrap.Modal(document.getElementById(\"modalAddStudent\")).show()","icon":"fas fa-user-plus"}'>
                            <thead class="table-light">
                                <tr>
                                    <th width="60">S.No.</th>
                                    <th>Student Info</th>
                                    <th>Reg / Roll No</th>
                                    <th>Center</th>
                                    <th>Course</th>
                                    <th data-no-sort>Status</th>
                                    <th class="text-end" data-no-sort>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $sn = 1; foreach($students as $s): ?>
                                <tr id="studentrow-<?php echo $s['id']; ?>">
                                    <td><?php echo $sn++; ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-2 border flex-shrink-0" style="width:42px;height:42px;overflow:hidden;">
                                                <?php 
                                                    $pImg = !empty($s['photo']) && file_exists(__DIR__ . '/../media/students/' . $s['photo'])
                                                            ? BASE_URL . 'media/students/' . $s['photo'] 
                                                            : BASE_URL . 'media/general/default-avatar.png';
                                                ?>
                                                <img src="<?php echo $pImg; ?>" style="width:100%;height:100%;object-fit:cover;" onerror="this.src='<?php echo BASE_URL; ?>media/general/default-avatar.png'">
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark"><?php echo htmlspecialchars($s['full_name']); ?></div>
                                                <div class="small text-muted"><i class="fas fa-phone-alt me-1 small"></i><?php echo $s['mobile']; ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="badge bg-light text-dark border mb-1 small fw-bold">REG: <?php echo str_pad($s['id'], 6, '0', STR_PAD_LEFT); ?></div>
                                        <?php if($s['roll_number']): ?>
                                            <div class="small text-primary fw-bold">ROLL: <?php echo $s['roll_number']; ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="fw-bold small text-dark"><?php echo htmlspecialchars($s['center_name'] ?: 'N/A'); ?></div>
                                        <small class="text-muted"><?php echo $s['center_code'] ?: ''; ?></small>
                                    </td>
                                    <td><span class="badge bg-primary-light text-primary border border-primary px-2"><?php echo $s['course_name'] ?: 'N/A'; ?></span></td>
                                    <td>
                                        <span class="badge bg-<?php echo ($s['user_status'] == 1) ? 'success' : 'danger'; ?> rounded-pill px-3">
                                            <?php echo ($s['user_status'] == 1) ? 'Active Account' : 'No Account'; ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group shadow-sm border rounded">
                                            <a href="view-student.php?id=<?php echo $s['id']; ?>" class="btn btn-sm btn-outline-info border-0 px-2" title="View Profile">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="edit-student.php?id=<?php echo $s['id']; ?>" class="btn btn-sm btn-outline-warning border-0 px-2" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="print-admission.php?id=<?php echo $s['id']; ?>" target="_blank" class="btn btn-sm btn-outline-success border-0 px-2" title="Print Admission">
                                                <i class="fas fa-print"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline-danger border-0 px-2" title="Delete" onclick="deleteStudent(<?php echo $s['id']; ?>, '<?php echo addslashes($s['full_name']); ?>')">
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


<!-- Add Student Modal -->
<div class="modal fade" id="modalAddStudent" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Add New Student</h5>
                <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                    <i class="<?php echo v('theme_modal_close_icon', 'fas fa-times'); ?>"></i>
                </button>
            </div>
            <form id="add-student-form" novalidate enctype="multipart/form-data">
                <div class="modal-body px-4 py-4" style="max-height: 80vh; overflow-y: auto; overflow-x: hidden;">

<div class="row g-3">
            <div class="col-12"><div class="form-section-header"><i class="fas fa-graduation-cap me-2"></i>Academic Details</div></div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Franchise / Center <span class="text-danger">*</span></label>
                <select class="form-select" name="center_id" required>
                    <option value="">Select Center...</option>
                    <?php foreach($franchises as $ctr): ?>
                        <option value="<?php echo $ctr['id']; ?>"><?php echo htmlspecialchars($ctr['center_name']); ?> (<?php echo $ctr['center_code']; ?>)</option>
                    <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">Please select a franchise/center.</div>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Registration No. <span class="text-muted small">(Auto-generated)</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                    <input type="text" class="form-control bg-light" name="reg_no"
                           value="ICSTIR/2024/<?php echo rand(1000, 9999); ?>" readonly>
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Admission Date</label>
                <input type="date" class="form-control" name="admission_date"
                       value="<?php echo date('Y-m-d'); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Academic Session <span class="text-danger">*</span></label>
                <select class="form-select" name="session_id" required>
                    <option value="">Select Session...</option>
                    <?php
                    $sessions = $pdo->query("SELECT id, session_label FROM admission_sessions WHERE status = 1")->fetchAll(PDO::FETCH_ASSOC);
                    foreach($sessions as $s) {
                        echo "<option value='{$s['id']}'>".htmlspecialchars($s['session_label'])."</option>";
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
                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">Please select a course category.</div>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Course <span class="text-danger">*</span></label>
                <select class="form-select" name="course_id" id="course_id" required disabled>
                    <option value="">Select Category first...</option>
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
        <div class="col-12"><div class="form-section-header mt-4"><i class="fas fa-user me-2"></i>Personal Details</div></div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Student Full Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="full_name"
                       placeholder="Full Name as on Certificate" required>
                <div class="invalid-feedback">Student name is required.</div>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Father's Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="father_name"
                       placeholder="Father's Full Name" required>
                <div class="invalid-feedback">Father's name is required.</div>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Mother's Name</label>
                <input type="text" class="form-control" name="mother_name"
                       placeholder="Mother's Full Name">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Last Qualification <span class="text-danger">*</span></label>
                <select class="form-select" name="qualification" required>
                    <option value="">Select Qualification</option>
                    <option value="10th">10th</option>
                    <option value="12th">12th</option>
                    <option value="Graduation">Graduation</option>
                    <option value="Diploma">Diploma</option>
                    <option value="Others">Others</option>
                </select>
                <div class="invalid-feedback">Qualification is required.</div>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Date of Birth <span class="text-danger">*</span></label>
                <input type="date" class="form-control" name="dob" required>
                <div class="invalid-feedback">Date of birth is required.</div>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Gender</label>
                <select class="form-select" name="gender">
                    <option>Male</option>
                    <option>Female</option>
                    <option>Other</option>
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
                    <input type="text" class="form-control" name="mobile"
                           placeholder="10 Digit Mobile" maxlength="10" required>
                </div>
                <div class="invalid-feedback">Mobile number is required.</div>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Email ID</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" class="form-control" name="email"
                           placeholder="email@example.com">
                </div>
            </div>
            <div class="col-md-12">
                <label class="form-label fw-bold">Full Address <span class="text-danger">*</span></label>
                <textarea class="form-control" name="address" rows="2"
                          placeholder="House No., Street, Village, Landmark..." required></textarea>
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
                <input type="text" class="form-control" name="pincode"
                       placeholder="6 Digit Pincode" maxlength="6" pattern="[0-9]{6}">
            </div>
        <div class="col-12"><div class="form-section-header mt-4"><i class="fas fa-key me-2"></i>Login Credentials</div></div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Username <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="username" placeholder="Unique username" required>
                <small class="text-muted">Used for student portal login</small>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Password <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="password" class="form-control" id="student_password" name="password" placeholder="Initial password" required minlength="8">
                    <button class="btn btn-outline-secondary" type="button" onclick="const p = document.getElementById('student_password'); p.type = p.type === 'password' ? 'text' : 'password'; this.querySelector('i').classList.toggle('fa-eye'); this.querySelector('i').classList.toggle('fa-eye-slash');">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
        <div class="col-12"><div class="form-section-header mt-4"><i class="fas fa-file-upload me-2"></i>Documents &amp; Attachments</div></div>
            <!-- Row 1 -->
            <div class="col-md-3">
                <div class="p-3 border rounded text-center h-100 bg-light">
                    <i class="fas fa-user-circle fs-3 text-primary mb-2"></i>
                    <label class="form-label d-block small fw-bold">Student Photo <span class="text-danger">*</span></label>
                    <input type="file" class="form-control form-control-sm" name="student_photo" accept="image/*" required>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 border rounded text-center h-100 bg-light">
                    <i class="fas fa-signature fs-3 text-primary mb-2"></i>
                    <label class="form-label d-block small fw-bold">Student Signature <span class="text-danger">*</span></label>
                    <input type="file" class="form-control form-control-sm" name="signature" accept="image/*" required>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 border rounded text-center h-100 bg-light">
                    <i class="fas fa-id-card fs-3 text-primary mb-2"></i>
                    <label class="form-label d-block small fw-bold">Aadhar (Front) <span class="text-danger">*</span></label>
                    <input type="file" class="form-control form-control-sm" name="aadhar_front" accept="image/*,application/pdf" required>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 border rounded text-center h-100 bg-light">
                    <i class="fas fa-id-card fs-3 text-primary mb-2"></i>
                    <label class="form-label d-block small fw-bold">Aadhar (Back) <span class="text-danger">*</span></label>
                    <input type="file" class="form-control form-control-sm" name="aadhar_back" accept="image/*,application/pdf" required>
                </div>
            </div>

            <!-- Row 2 -->
            <div class="col-md-3">
                <div class="p-3 border rounded text-center h-100 bg-light">
                    <i class="fas fa-file-invoice fs-3 text-success mb-2"></i>
                    <label class="form-label d-block small fw-bold">10th Marksheet</label>
                    <input type="file" class="form-control form-control-sm" name="marksheet_10th" accept="image/*,application/pdf">
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 border rounded text-center h-100 bg-light">
                    <i class="fas fa-file-invoice fs-3 text-success mb-2"></i>
                    <label class="form-label d-block small fw-bold">12th Marksheet</label>
                    <input type="file" class="form-control form-control-sm" name="marksheet_12th" accept="image/*,application/pdf">
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 border rounded text-center h-100 bg-light">
                    <i class="fas fa-id-badge fs-3 text-info mb-2"></i>
                    <label class="form-label d-block small fw-bold">Parent Aadhar (F)</label>
                    <input type="file" class="form-control form-control-sm" name="parent_aadhar_front" accept="image/*,application/pdf">
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 border rounded text-center h-100 bg-light">
                    <i class="fas fa-id-badge fs-3 text-info mb-2"></i>
                    <label class="form-label d-block small fw-bold">Parent Aadhar (B)</label>
                    <input type="file" class="form-control form-control-sm" name="parent_aadhar_back" accept="image/*,application/pdf">
                </div>
            </div>

            <!-- Row 3 -->
            <div class="col-md-3">
                <div class="p-3 border rounded text-center h-100 bg-light">
                    <i class="fas fa-passport fs-3 text-warning mb-2"></i>
                    <label class="form-label d-block small fw-bold">Student ID Proof <span class="text-danger">*</span></label>
                    <input type="file" class="form-control form-control-sm" name="id_proof" accept="image/*,application/pdf" required>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 border rounded text-center h-100 bg-light">
                    <i class="fas fa-user-shield fs-3 text-warning mb-2"></i>
                    <label class="form-label d-block small fw-bold">Guardian ID Proof <span class="text-danger">*</span></label>
                    <input type="file" class="form-control form-control-sm" name="guardian_doc" accept="image/*,application/pdf" required>
                </div>
                </div>
            </div> <!-- Close row g-3 -->
        </div> <!-- Close modal-body -->
        <div class="modal-footer bg-light border-top py-2">
                    <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">CANCEL</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold" id="btn-submit">
                        <i class="fas fa-user-plus me-2"></i>REGISTER STUDENT
                    </button>
                </div>
            </form>
        </div> <!-- Close modal-content -->
    </div> <!-- Close modal-dialog -->
</div> <!-- Close modal fade -->

<script>

document.addEventListener("DOMContentLoaded", function () {
    if(typeof initLocationCascade === "function") {
        initLocationCascade({
            stateEl   : "#student_state",
            districtEl: "#student_district",
            cityEl    : "#student_city"
        });
    }

    const categoryEl = document.getElementById("course_category");
    const courseEl = document.getElementById("course_id");
    
    if(categoryEl && courseEl) {
        categoryEl.addEventListener("change", function() {
            const catId = this.value;
            courseEl.disabled = true;
            courseEl.innerHTML = '<option value="">Loading courses...</option>';
            
            if(!catId) {
                courseEl.innerHTML = '<option value="">Select Category first...</option>';
                return;
            }
            
            fetch(`<?php echo BASE_URL; ?>ajax/get_courses.php?type=courses&category_id=${catId}`)
                .then(response => response.json())
                .then(data => {
                    courseEl.innerHTML = '<option value="">Select Course...</option>';
                    if(data && data.length > 0) {
                        data.forEach(c => {
                            courseEl.innerHTML += `<option value="${c.id}">${c.name}</option>`;
                        });
                        courseEl.disabled = false;
                    } else {
                        courseEl.innerHTML = '<option value="">No courses found</option>';
                    }
                })
                .catch(err => {
                    console.error("Error fetching courses:", err);
                    courseEl.innerHTML = '<option value="">Error loading courses</option>';
                });
        });
    }

    const form = document.getElementById("add-student-form");
    if(form) {
        form.addEventListener("submit", function (e) {
            e.preventDefault();
            form.classList.add("was-validated");

            if (!form.checkValidity()) return;

            const btn = document.getElementById("btn-submit");
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Processing...';

            const fd = new FormData(form);
            
            fetch("<?php echo BASE_URL; ?>ajax/admission_handler.php", {
                method: "POST",
                body: fd
            })
            .then(r => r.json())
            .then(res => {
                btn.disabled = false;
                btn.innerHTML = originalText;

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
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = originalText;
                alert("An error occurred. Please check console.");
                console.error(err);
            });
        });
    }
});

function deleteStudent(id, name) {
    if(confirm(`Are you sure you want to delete student "${name}"?\nThis will remove their admission record.`)) {
        const fd = new FormData();
        fd.append('action', 'delete_student');
        fd.append('id', id);
        fetch('<?php echo BASE_URL; ?>ajax/admission_handler.php', { method: 'POST', body: fd })
        .then(r => r.json()).then(res => {
            if(res.status === 'success') { location.reload(); }
            else alert(res.message);
        });
    }
}
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>
