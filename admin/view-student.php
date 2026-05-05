<?php
// admin/view-student.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("
    SELECT a.*, c.name as course_name, f.center_name, f.center_code,
           st.name as state_name, dt.name as district_name, ct.name as city_name,
           u.username, u.email as user_email, u.status as user_status
    FROM admissions a
    LEFT JOIN courses c ON a.course_id = c.id
    LEFT JOIN franchises f ON a.center_id = f.id
    LEFT JOIN states st ON a.state_id = st.id
    LEFT JOIN districts dt ON a.district_id = dt.id
    LEFT JOIN cities ct ON a.city_id = ct.id
    LEFT JOIN users u ON a.user_id = u.id
    WHERE a.id = ?
");
$stmt->execute([$id]);
$s = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$s) {
    echo "<div class='alert alert-danger'>Student not found.</div>";
    include(__DIR__ . "/includes/footer.php");
    exit();
}

// Profile Picture Logic
$photoPath = !empty($s['photo']) ? __DIR__ . '/../media/students/' . $s['photo'] : '';
$profileImg = (file_exists($photoPath) && !empty($s['photo'])) 
              ? BASE_URL . 'media/students/' . $s['photo'] 
              : BASE_URL . 'media/general/default-avatar.png';
?>

<div class="pagetitle">
    <h1>Student Profile</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item">Student Management</li>
            <li class="breadcrumb-item active">View Profile</li>
        </ol>
    </nav>
</div>

<section class="section profile">
    <div class="row">
        <div class="col-xl-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mb-3 shadow-sm" style="width:150px;height:150px; overflow:hidden; border: 4px solid #fff;">
                        <img src="<?php echo $profileImg; ?>" alt="Profile" style="width:100%;height:100%;object-fit:cover;">
                    </div>
                    <h2 class="text-center fw-bold text-dark mb-1"><?php echo strtoupper($s['full_name']); ?></h2>
                    <h3 class="text-primary small mb-2"><?php echo $s['course_name']; ?></h3>
                    <div class="badge bg-primary rounded-pill px-3 py-2">NEB/REG/<?php echo str_pad($s['id'], 6, '0', STR_PAD_LEFT); ?></div>
                    
                    <?php if($s['roll_number']): ?>
                        <div class="mt-2 small text-muted fw-bold">Roll No: <?php echo $s['roll_number']; ?></div>
                    <?php endif; ?>
                </div>

                <div class="card-body pt-0 px-4">
                    <hr class="my-3 opacity-50">
                    
                    <h5 class="card-title small text-uppercase fw-bold text-primary mb-3">
                        <i class="fas fa-graduation-cap me-2"></i>Academic Info
                    </h5>
                    
                    <div class="mb-3">
                        <small class="text-muted d-block small-caps">Franchise / Center</small>
                        <span class="fw-bold text-dark"><?php echo $s['center_name']; ?></span>
                        <small class="d-block text-muted small">(Code: <?php echo $s['center_code']; ?>)</small>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <small class="text-muted d-block small-caps">Admission Date</small>
                            <span class="fw-bold"><?php echo $s['admission_date'] ? date('d M Y', strtotime($s['admission_date'])) : 'N/A'; ?></span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block small-caps">Session</small>
                            <span class="fw-bold"><?php echo $s['session_name'] ?: 'N/A'; ?></span>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <small class="text-muted d-block small-caps">Source</small>
                            <span class="badge bg-secondary rounded-pill"><?php echo ucfirst($s['source']); ?></span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block small-caps">Approval Status</small>
                            <span class="badge bg-<?php 
                                echo match($s['approval_status']) {
                                    'approved' => 'success',
                                    'pending' => 'warning',
                                    'rejected' => 'danger',
                                    'completed' => 'info',
                                    default => 'secondary'
                                }; 
                            ?> rounded-pill"><?php echo ucfirst($s['approval_status']); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="card shadow-sm h-100">
                <div class="card-body pt-3 px-4">
                    <!-- Bordered Tabs -->
                    <ul class="nav nav-tabs nav-tabs-bordered mb-3">
                        <li class="nav-item">
                            <button class="nav-link active fw-bold" data-bs-toggle="tab" data-bs-target="#profile-overview">
                                <i class="fas fa-id-card me-2"></i>Overview
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-bold" data-bs-toggle="tab" data-bs-target="#profile-docs">
                                <i class="fas fa-file-alt me-2"></i>Documents
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content pt-2">
                        <div class="tab-pane fade show active profile-overview" id="profile-overview">
                            
                            <!-- Personal Details Section -->
                            <div class="view-section mb-4">
                                <div class="view-section-header"><i class="fas fa-user me-2"></i>Personal Details</div>
                                <div class="row g-3 px-3">
                                    <div class="col-md-6 mb-2">
                                        <label class="view-label">Father's Name</label>
                                        <div class="view-value"><?php echo $s['father_name']; ?></div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="view-label">Mother's Name</label>
                                        <div class="view-value"><?php echo $s['mother_name'] ?: 'N/A'; ?></div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="view-label">Date of Birth</label>
                                        <div class="view-value"><?php echo date('d M Y', strtotime($s['dob'])); ?></div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="view-label">Gender</label>
                                        <div class="view-value"><?php echo ucfirst($s['gender']); ?></div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="view-label">Blood Group</label>
                                        <div class="view-value text-danger fw-bold"><?php echo $s['blood_group'] ?: '—'; ?></div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="view-label">Religion</label>
                                        <div class="view-value"><?php echo $s['religion'] ?: '—'; ?></div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="view-label">Caste / Category</label>
                                        <div class="view-value"><?php echo $s['caste'] ?: '—'; ?></div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="view-label">Qualification</label>
                                        <div class="view-value fw-bold text-success"><?php echo $s['qualification'] ?: '—'; ?></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Information Section -->
                            <div class="view-section mb-4">
                                <div class="view-section-header"><i class="fas fa-phone-alt me-2"></i>Contact Information</div>
                                <div class="row g-3 px-3">
                                    <div class="col-md-6 mb-2">
                                        <label class="view-label">Student Phone</label>
                                        <div class="view-value"><?php echo $s['mobile']; ?></div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="view-label">Guardian Phone</label>
                                        <div class="view-value"><?php echo $s['guardian_phone'] ?: '—'; ?></div>
                                    </div>
                                    <div class="col-12 mb-2">
                                        <label class="view-label">Email Address</label>
                                        <div class="view-value"><?php echo $s['email'] ?: '—'; ?></div>
                                    </div>
                                    <div class="col-12 mb-2">
                                        <label class="view-label">Current Address</label>
                                        <div class="view-value"><?php echo $s['address']; ?>, <?php echo $s['city_name']; ?>, <?php echo $s['district_name']; ?>, <?php echo $s['state_name']; ?> - <?php echo $s['pincode']; ?></div>
                                    </div>
                                    <?php if($s['guardian_address']): ?>
                                    <div class="col-12 mb-2">
                                        <label class="view-label">Guardian Address</label>
                                        <div class="view-value"><?php echo $s['guardian_address']; ?></div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Account Information Section -->
                            <div class="view-section">
                                <div class="view-section-header"><i class="fas fa-shield-alt me-2"></i>Account Information</div>
                                <div class="row g-3 px-3">
                                    <div class="col-md-6 mb-2">
                                        <label class="view-label">Username</label>
                                        <div class="view-value fw-bold text-primary"><?php echo $s['username']; ?></div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="view-label">Account Status</label>
                                        <div class="view-value">
                                            <?php if($s['user_status'] == 1): ?>
                                                <span class="badge bg-success rounded-pill px-3">Active</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger rounded-pill px-3">Inactive</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="tab-pane fade profile-docs pt-3" id="profile-docs">
                            <div class="row g-3">
                                <?php 
                                $docs = [
                                    ['label' => 'ID Proof', 'file' => $s['id_proof'], 'icon' => 'fa-image'],
                                    ['label' => 'Signature', 'file' => $s['signature'], 'icon' => 'fa-signature'],
                                    ['label' => 'Aadhar Front', 'file' => $s['aadhar_front'], 'icon' => 'fa-id-card'],
                                    ['label' => 'Aadhar Back', 'file' => $s['aadhar_back'], 'icon' => 'fa-id-card'],
                                    ['label' => '10th Marksheet', 'file' => $s['marksheet_10th'], 'icon' => 'fa-file-invoice'],
                                    ['label' => '12th Marksheet', 'file' => $s['marksheet_12th'], 'icon' => 'fa-file-invoice'],
                                    ['label' => 'Parent Aadhar (F)', 'file' => $s['parent_aadhar_front'], 'icon' => 'fa-user-shield'],
                                    ['label' => 'Parent Aadhar (B)', 'file' => $s['parent_aadhar_back'], 'icon' => 'fa-user-shield'],
                                    ['label' => 'Guardian Doc', 'file' => $s['guardian_doc'], 'icon' => 'fa-file-contract'],
                                ];

                                foreach($docs as $doc):
                                ?>
                                <div class="col-md-4 col-lg-3">
                                    <div class="card h-100 shadow-none border text-center p-3 hover-shadow-sm transition-smooth">
                                        <i class="fas <?php echo $doc['icon']; ?> fa-2x text-muted mb-3"></i>
                                        <div class="small fw-bold text-uppercase opacity-75"><?php echo $doc['label']; ?></div>
                                        <div class="mt-auto pt-3">
                                            <?php if($doc['file']): ?>
                                                <a href="<?php echo BASE_URL; ?>media/students/<?php echo $doc['file']; ?>" target="_blank" class="btn btn-sm btn-primary w-100">
                                                    <i class="fas fa-eye me-1"></i>View
                                                </a>
                                            <?php else: ?>
                                                <span class="badge bg-light text-danger w-100 py-2">Not Uploaded</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div><!-- End Bordered Tabs -->
                </div>
            </div>
        </div>
    </div>
</section>

<?php include(__DIR__ . "/includes/footer.php"); ?>
