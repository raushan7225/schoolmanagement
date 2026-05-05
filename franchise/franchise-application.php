<?php 
require_once(__DIR__ . "/../common/config.php"); 

$msg = "";
$msgType = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $data = [
            'center_name' => trim($_POST['center_name'] ?? ''),
            'director_name' => trim($_POST['director_name'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'qualification' => trim($_POST['qualification'] ?? ''),
            'state_id' => (int)($_POST['state'] ?? 0),
            'district_id' => (int)($_POST['district'] ?? 0),
            'city_id' => 0, // We'll store city name in comments if city_id is not available as int
            'pincode' => trim($_POST['pincode'] ?? ''),
            'computers' => (int)($_POST['computers'] ?? 0),
            'teachers' => (int)($_POST['teachers'] ?? 0),
            'rooms' => (int)($_POST['rooms'] ?? 0),
            'area_sqft' => (int)($_POST['area_sqft'] ?? 0),
            'comments' => "City: " . trim($_POST['city'] ?? ''),
            'source' => 'online',
            'approval_status' => 'new',
            'status' => 1
        ];

        // Basic validation
        if (!$data['center_name'] || !$data['director_name'] || !$data['phone']) {
            throw new Exception("Please fill all required fields.");
        }

        // Handle File Uploads
        $uploadDir = __DIR__ . "/../media/franchise/applications/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $files = [
            'dir_photo' => 'PHOTO',
            'dir_sig' => 'SIG',
            'aadhar_front' => 'AF',
            'aadhar_back' => 'AB',
            'labs_photo' => 'LAB',
            'approval_doc' => 'APPROV',
            'center_photo' => 'CENTER'
        ];
        
        $dbFieldMap = [
            'dir_photo' => 'dir_photo',
            'dir_sig' => 'dir_sig',
            'aadhar_front' => 'aadhar_front',
            'aadhar_back' => 'aadhar_back',
            'labs_photo' => 'labs_photo',
            'approval_doc' => 'approval_doc',
            'center_photo' => 'center_photo'
        ];

        foreach ($files as $key => $prefix) {
            if (isset($_FILES[$key]) && $_FILES[$key]['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES[$key]['name'], PATHINFO_EXTENSION);
                $newName = $prefix . "_" . time() . "_" . rand(1000, 9999) . "." . $ext;
                if (move_uploaded_file($_FILES[$key]['tmp_name'], $uploadDir . $newName)) {
                    $data[$dbFieldMap[$key]] = $newName;
                }
            }
        }

        // Insert into DB
        $cols = implode(", ", array_keys($data));
        $placeholders = ":" . implode(", :", array_keys($data));
        $stmt = $pdo->prepare("INSERT INTO franchise_enquiries ($cols) VALUES ($placeholders)");
        $stmt->execute($data);

        $msg = "Success! Your franchise application has been submitted successfully. Our team will contact you soon.";
        $msgType = "success";

    } catch (Exception $e) {
        $msg = "Error: " . $e->getMessage();
        $msgType = "danger";
    }
}
?>
<?php include(__DIR__ . "/../common/meta.php"); ?>
<?php include(__DIR__ . "/../common/header.php"); ?>
<!-- Page Header -->
<div class="page-header text-center" style="background: linear-gradient(rgba(11, 28, 61, 0.9), rgba(11, 28, 61, 0.9)), url('<?php echo BASE_URL; ?>media/banners/about-us.png') center center;">
    <div class="container">
        <h1 class="display-4 fw-bold text-white mb-3">Franchise Application</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php" class="text-white text-decoration-none">Home</a></li>
                <li class="breadcrumb-item active text-white" aria-current="page">Franchise Application</li>
            </ol>
        </nav>
    </div>
</div>

<section class="section-padding bg-light">
    <div class="container">
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-5">
            <div class="card-header-theme" style="background-color: #0b1c3d;">
                <h4 class="mb-0 fw-bold text-white"><i class="fas fa-building-columns me-2"></i>New Center Application</h4>
                <p class="mb-0 small opacity-75">Apply to join the International Council network as an Authorized Study Center.</p>
            </div>
            <div class="card-body p-4 p-md-5 bg-white">
                
                <?php if ($msg): ?>
                    <div class="alert alert-<?php echo $msgType; ?> alert-dismissible fade show mb-4" role="alert">
                        <?php echo $msg; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form action="#" method="POST" enctype="multipart/form-data">
                    
                    <!-- Section 1: Center & Director Info -->
                    <h5 class="form-section-title">1. Center & Director Information</h5>
                    <div class="row g-4 mb-5">
                        <div class="col-md-6">
                            <label class="form-label">Proposed Center Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="center_name" placeholder="Enter Institution Name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Director / Owner Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="director_name" placeholder="Full Name of Applicant" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Contact Phone <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" name="phone" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Qualification of Director</label>
                            <input type="text" class="form-control" name="qualification">
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label">State <span class="text-danger">*</span></label>
                            <select class="form-select" name="state" id="state-select" required>
                                <option value="">Select State</option>
                                <?php
                                $stmt = $pdo->query("SELECT id, name FROM states WHERE country_id = 1 AND status = 1 ORDER BY name ASC");
                                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<option value='{$row['id']}'>{$row['name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">District <span class="text-danger">*</span></label>
                            <select class="form-select" name="district" id="district-select" required>
                                <option value="">Select State First</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">City <span class="text-danger">*</span></label>
                            <select class="form-select" name="city" id="city-select" required>
                                <option value="">Select District First</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">PIN Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="pincode" maxlength="6" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Full Center Address <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="address" rows="2" required></textarea>
                        </div>
                    </div>

                    <!-- Section 2: Infrastructure Details -->
                    <h5 class="form-section-title">2. Infrastructure & Facilities</h5>
                    <div class="row g-3 mb-5 p-3 bg-light rounded-4">
                        <div class="col-6 col-md-3">
                            <label class="form-label">Total Computers <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="computers" min="1" required>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label">No. of Teachers <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="teachers" min="1" required>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label">Total Rooms <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="rooms" min="1" required>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label">Space (Sq. Ft) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="area_sqft" placeholder="e.g. 500" required>
                        </div>
                    </div>

                    <!-- Section 3: Document Gallery -->
                    <h5 class="form-section-title">3. Upload Required Documents</h5>
                    <div class="row g-4">
                        <div class="col-md-6 col-lg-4">
                            <div class="p-3 border rounded text-center">
                                <i class="fas fa-user-circle fs-3 text-muted mb-2"></i>
                                <label class="form-label d-block small fw-bold">Director Photo <span class="text-danger">*</span></label>
                                <input type="file" class="form-control form-control-sm" name="dir_photo" accept="image/*" required>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="p-3 border rounded text-center">
                                <i class="fas fa-signature fs-3 text-muted mb-2"></i>
                                <label class="form-label d-block small fw-bold">Signature <span class="text-danger">*</span></label>
                                <input type="file" class="form-control form-control-sm" name="dir_sig" accept="image/*" required>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="p-3 border rounded text-center">
                                <i class="fas fa-id-card fs-3 text-muted mb-2"></i>
                                <label class="form-label d-block small fw-bold">Aadhar Card (Front) <span class="text-danger">*</span></label>
                                <input type="file" class="form-control form-control-sm" name="aadhar_front" accept="image/*,application/pdf" required>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="p-3 border rounded text-center">
                                <i class="fas fa-id-card fs-3 text-muted mb-2"></i>
                                <label class="form-label d-block small fw-bold">Aadhar Card (Back) <span class="text-danger">*</span></label>
                                <input type="file" class="form-control form-control-sm" name="aadhar_back" accept="image/*,application/pdf" required>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="p-3 border rounded text-center">
                                <i class="fas fa-flask fs-3 text-muted mb-2"></i>
                                <label class="form-label d-block small fw-bold">Labs Front Photo <span class="text-danger">*</span></label>
                                <input type="file" class="form-control form-control-sm" name="labs_photo" accept="image/*" required>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="p-3 border rounded text-center">
                                <i class="fas fa-file-contract fs-3 text-muted mb-2"></i>
                                <label class="form-label d-block small fw-bold">Center Approval Document <span class="text-danger">*</span></label>
                                <input type="file" class="form-control form-control-sm" name="approval_doc" accept="image/*,application/pdf" required>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="p-3 border rounded text-center">
                                <i class="fas fa-image fs-3 text-muted mb-2"></i>
                                <label class="form-label d-block small fw-bold">Center Front Photo <span class="text-danger">*</span></label>
                                <input type="file" class="form-control form-control-sm" name="center_photo" accept="image/*" required>
                            </div>
                        </div>
                        
                        <div class="col-12 mt-4 text-center">
                            <p class="text-muted small mb-4">By clicking submit, you agree to the terms and conditions of NEBOOSASE Franchise Program.</p>
                            <button type="submit" class="btn btn-form-submit px-5 py-3">SUBMIT CENTER APPLICATION</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</section>

<?php include(__DIR__ . "/../common/footer.php"); ?>
<?php include(__DIR__ . "/../common/requirejs.php"); ?>

<script>
// State -> District Cascading
document.getElementById('state-select').addEventListener('change', function() {
    const stateId = this.value;
    const districtSelect = document.getElementById('district-select');
    const citySelect = document.getElementById('city-select');
    
    // Reset following dropdowns
    districtSelect.innerHTML = '<option value="">Loading...</option>';
    citySelect.innerHTML = '<option value="">Select District First</option>';
    
    if(!stateId) {
        districtSelect.innerHTML = '<option value="">Select State First</option>';
        return;
    }
    
    fetch(`<?php echo BASE_URL; ?>ajax/get_locations.php?type=districts&state_id=${stateId}`)
    .then(response => response.json())
    .then(data => {
        districtSelect.innerHTML = '<option value="">Select District</option>';
        if(data.length > 0) {
            data.forEach(item => {
                districtSelect.innerHTML += `<option value="${item.id}">${item.name}</option>`;
            });
        } else {
            districtSelect.innerHTML = '<option value="">No Districts Found</option>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        districtSelect.innerHTML = '<option value="">Error Loading</option>';
    });
});

// District -> City Cascading
document.getElementById('district-select').addEventListener('change', function() {
    const districtId = this.value;
    const citySelect = document.getElementById('city-select');
    
    citySelect.innerHTML = '<option value="">Loading...</option>';
    
    if(!districtId) {
        citySelect.innerHTML = '<option value="">Select District First</option>';
        return;
    }
    
    fetch(`<?php echo BASE_URL; ?>ajax/get_locations.php?type=cities&district_id=${districtId}`)
    .then(response => response.json())
    .then(data => {
        citySelect.innerHTML = '<option value="">Select City</option>';
        if(data.length > 0) {
            data.forEach(item => {
                citySelect.innerHTML += `<option value="${item.name}">${item.name}</option>`;
            });
        } else {
            citySelect.innerHTML = '<option value="">No Cities Found</option>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        citySelect.innerHTML = '<option value="">Error Loading</option>';
    });
});
</script>
</body>
</html>
