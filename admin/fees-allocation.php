<?php
// admin/fees-allocation.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Fetch active fee groups
$fee_groups = $pdo->query("SELECT id, name FROM fee_groups WHERE status = 1 ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
$categories = $pdo->query("SELECT id, name FROM course_categories WHERE status = 1 ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
$courses = $pdo->query("SELECT id, name FROM courses WHERE status = 1 ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Filter logic
$f_group = (int)($_GET['fee_group_id'] ?? 0);
$f_cat = (int)($_GET['category_id'] ?? 0);
$f_course = (int)($_GET['course_id'] ?? 0);

$students = [];
if ($f_course) {
    $stmt = $pdo->prepare("
        SELECT a.id, a.roll_number, a.full_name, f.center_name, f.center_code,
        (SELECT fg.name FROM fee_allocations fa JOIN fee_groups fg ON fa.fee_group_id = fg.id WHERE fa.admission_id = a.id LIMIT 1) as current_group
        FROM admissions a
        LEFT JOIN franchises f ON a.center_id = f.id
        WHERE a.course_id = ? AND a.status = 1 AND a.approval_status = 'approved'
        ORDER BY a.full_name ASC
    ");
    $stmt->execute([$f_course]);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="pagetitle">
    <h1>Fees Allocation</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Student Accounting</li>
            <li class="breadcrumb-item active">Fees Allocation</li>
        </ol>
    </nav>
</div>

<section class="section">
    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body pt-3 pb-2">
            <form class="row g-2" method="GET">
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-danger">ASSIGN FEE GROUP *</label>
                    <select class="form-select" name="fee_group_id" required>
                        <option value="">Select Group...</option>
                        <?php foreach($fee_groups as $fg): ?>
                            <option value="<?php echo $fg['id']; ?>" <?php echo $f_group == $fg['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($fg['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small">COURSE CATEGORY</label>
                    <select class="form-select" name="category_id">
                        <option value="">All Categories...</option>
                        <?php foreach($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo $f_cat == $cat['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold small text-danger">COURSE / PROGRAM *</label>
                    <select class="form-select" name="course_id" required>
                        <option value="">Select Course...</option>
                        <?php foreach($courses as $co): ?>
                            <option value="<?php echo $co['id']; ?>" <?php echo $f_course == $co['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($co['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search me-1"></i>Fetch</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Student List -->
    <div class="card">
        <div class="card-body pt-3">
            <div id="allocation-alert" class="d-none mb-3"></div>
            <form id="allocationForm">
                <input type="hidden" name="action" value="allocate_fees">
                <input type="hidden" name="fee_group_id" value="<?php echo $f_group; ?>">
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle datatable-premium">
                        <thead class="table-light">
                            <tr>
                                <th width="40" data-no-sort>
                                    <input type="checkbox" class="form-check-input" id="checkAll" onchange="toggleAllRows(this)">
                                </th>
                                <th width="60">S.No.</th>
                                <th>Roll Number</th>
                                <th>Student Name</th>
                                <th>Franchise / Center</th>
                                <th data-no-sort>Current Allocated Group</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $sn=1; foreach($students as $s): ?>
                            <tr>
                                <td><input type="checkbox" class="form-check-input row-check" name="student_ids[]" value="<?php echo $s['id']; ?>"></td>
                                <td><?php echo $sn++; ?></td>
                                <td><code class="text-primary fw-bold"><?php echo htmlspecialchars($s['roll_number']); ?></code></td>
                                <td><div class="fw-bold text-dark"><?php echo strtoupper(htmlspecialchars($s['full_name'])); ?></div></td>
                                <td>
                                    <div class="small fw-semibold"><?php echo htmlspecialchars($s['center_name']); ?></div>
                                    <code class="text-muted small"><?php echo $s['center_code']; ?></code>
                                </td>
                                <td>
                                    <?php if($s['current_group']): ?>
                                        <span class="badge bg-info-light text-info border border-info px-3"><?php echo htmlspecialchars($s['current_group']); ?></span>
                                    <?php else: ?>
                                        <span class="text-muted small">Not Allocated</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
        <?php if(!empty($students)): ?>
        <div class="card-footer bg-light border-top py-3 text-end">
            <button type="button" class="btn btn-primary px-5 fw-bold" onclick="submitAllocation()">
                <i class="fas fa-check-circle me-2"></i>CONFIRM BULK ALLOCATION
            </button>
        </div>
        <?php endif; ?>
    </div>
</section>

<script>
function toggleAllRows(chk) {
    document.querySelectorAll('.row-check').forEach(c => c.checked = chk.checked);
}

function submitAllocation() {
    const selected = document.querySelectorAll('.row-check:checked');
    if(selected.length === 0) {
        alert('Please select at least one student.');
        return;
    }
    
    const groupId = document.querySelector('input[name="fee_group_id"]').value;
    if(!groupId) {
        alert('Please select a Fee Group in the filter above.');
        return;
    }

    if(confirm(`Are you sure you want to allocate the selected fee group to ${selected.length} students?`)) {
        const btn = event.target;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>ALLOCATING...';
        
        const fd = new FormData(document.getElementById('allocationForm'));
        fetch('<?php echo BASE_URL; ?>ajax/accounting_handler.php', { method: 'POST', body: fd })
        .then(r => r.json()).then(res => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check-circle me-2"></i>CONFIRM BULK ALLOCATION';
            if(res.success) { 
                alert(res.message);
                location.reload(); 
            } else {
                alert(res.message);
            }
        });
    }
}
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>
