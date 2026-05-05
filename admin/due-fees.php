<?php
// admin/due-fees.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Fetch filters
$centers = $pdo->query("SELECT id, center_code, center_name FROM franchises WHERE status = 1 ORDER BY center_name ASC")->fetchAll(PDO::FETCH_ASSOC);
$courses = $pdo->query("SELECT id, name FROM courses WHERE status = 1 ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
$fee_types = $pdo->query("SELECT id, name FROM fee_types WHERE status = 1 ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Filter logic
$f_center = (int)($_GET['center_id'] ?? 0);
$f_course = (int)($_GET['course_id'] ?? 0);
$f_type = (int)($_GET['fee_type_id'] ?? 0);

$where = "WHERE a.status = 1 AND a.approval_status = 'approved'";
$params = [];

if ($f_center) { $where .= " AND a.center_id = ?"; $params[] = $f_center; }
if ($f_course) { $where .= " AND a.course_id = ?"; $params[] = $f_course; }

// Main Query to find students with balances
$sql = "
    SELECT a.id, a.roll_number, a.full_name, c.name as course_name, f.center_code,
    SUM(fgi.amount) as total_allocated,
    (SELECT SUM(amount_paid) FROM fee_collections WHERE admission_id = a.id) as total_paid
    FROM admissions a
    JOIN fee_allocations fa ON a.id = fa.admission_id
    JOIN fee_groups fg ON fa.fee_group_id = fg.id
    JOIN fee_group_items fgi ON fg.id = fgi.fee_group_id
    LEFT JOIN courses c ON a.course_id = c.id
    LEFT JOIN franchises f ON a.center_id = f.id
    $where
    GROUP BY a.id
    HAVING (total_allocated - IFNULL(total_paid, 0)) > 0
    ORDER BY a.full_name ASC
";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$defaulters = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>Due Fees List</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Student Accounting</li>
            <li class="breadcrumb-item active">Due Fees</li>
        </ol>
    </nav>
</div>

<section class="section">
    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body pt-3 pb-2">
            <form class="row g-2" method="GET">
                <div class="col-md-3">
                    <select class="form-select" name="center_id">
                        <option value="">All Centers...</option>
                        <?php foreach($centers as $ct): ?>
                            <option value="<?php echo $ct['id']; ?>" <?php echo $f_center == $ct['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($ct['center_code'] . ' - ' . $ct['center_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="course_id">
                        <option value="">All Courses...</option>
                        <?php foreach($courses as $co): ?>
                            <option value="<?php echo $co['id']; ?>" <?php echo $f_course == $co['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($co['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="fee_type_id">
                        <option value="">All Due Fees...</option>
                        <?php foreach($fee_types as $ft): ?>
                            <option value="<?php echo $ft['id']; ?>" <?php echo $f_type == $ft['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($ft['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-danger w-100 fw-bold">
                        <i class="fas fa-search me-2"></i>FETCH DEFAULTERS
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Defaulters List -->
    <div class="card">
        <div class="card-body pt-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle datatable-premium">
                    <thead class="table-light">
                        <tr>
                            <th width="60">S.No.</th>
                            <th>Roll Number</th>
                            <th>Student Name</th>
                            <th>Franchise</th>
                            <th>Course</th>
                            <th>Total Fee</th>
                            <th>Paid Amount</th>
                            <th>Balance</th>
                            <th class="text-end" data-no-sort>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $sn=1; foreach($defaulters as $d): 
                            $balance = $d['total_allocated'] - ($d['total_paid'] ?? 0);
                        ?>
                        <tr>
                            <td><?php echo $sn++; ?></td>
                            <td><code class="text-primary fw-bold"><?php echo htmlspecialchars($d['roll_number'] ?: 'PENDING'); ?></code></td>
                            <td><div class="fw-bold text-dark"><?php echo strtoupper(htmlspecialchars($d['full_name'])); ?></div></td>
                            <td><span class="badge bg-light text-dark border px-2"><?php echo htmlspecialchars($d['center_code']); ?></span></td>
                            <td><small class="fw-semibold"><?php echo htmlspecialchars($d['course_name']); ?></small></td>
                            <td>₹ <?php echo number_format($d['total_allocated'], 2); ?></td>
                            <td><span class="text-success fw-bold">₹ <?php echo number_format($d['total_paid'] ?? 0, 2); ?></span></td>
                            <td><span class="fw-bold text-danger fs-6">₹ <?php echo number_format($balance, 2); ?></span></td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-warning btn-icon me-1" title="Send SMS Reminder"><i class="fas fa-sms"></i></button>
                                <a href="fees-collection.php" class="btn btn-sm btn-outline-success btn-icon" title="Collect Fee"><i class="fas fa-rupee-sign"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<?php include(__DIR__ . "/includes/footer.php"); ?>
