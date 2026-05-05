<?php
// admin/online-admission-list.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Fetch courses for filter
$course_list = $pdo->query("SELECT id, name FROM courses WHERE status = 1 ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Search / Filter
$where = "WHERE a.approval_status = 'pending'";
$params = [];
$filter_center = (int)($_GET['center_id'] ?? 0);
$filter_course = (int)($_GET['course_id'] ?? 0);

if ($filter_center) {
    $where .= " AND a.center_id = ?";
    $params[] = $filter_center;
}
if ($filter_course) {
    $where .= " AND a.course_id = ?";
    $params[] = $filter_course;
}

$stmt = $pdo->prepare("
    SELECT a.*, c.name as course_name, f.center_name, f.center_code,
           st.name as state_name, dt.name as district_name, ct.name as city_name
    FROM admissions a
    LEFT JOIN courses c ON a.course_id = c.id
    LEFT JOIN franchises f ON a.center_id = f.id
    LEFT JOIN states st ON a.state_id = st.id
    LEFT JOIN districts dt ON a.district_id = dt.id
    LEFT JOIN cities ct ON a.city_id = ct.id
    $where
    ORDER BY a.created_at DESC
");
$stmt->execute($params);
$admissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch franchises for filter
$franchises = $pdo->query("SELECT id, center_name, center_code FROM franchises WHERE status = 1 ORDER BY center_name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>Online Admission List</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Student Management</li>
            <li class="breadcrumb-item active">Online Admission</li>
        </ol>
    </nav>
</div>

<section class="section">
    <!-- Filter Bar -->
    <div class="card mb-3">
        <div class="card-body py-2">
            <form class="row g-2 align-items-center" method="GET">
                <div class="col-md-5">
                    <select name="center_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Centers...</option>
                        <?php foreach($franchises as $fr): ?>
                            <option value="<?php echo $fr['id']; ?>" <?php echo $filter_center == $fr['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($fr['center_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <select name="course_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Courses...</option>
                        <?php foreach($course_list as $cl): ?>
                            <option value="<?php echo $cl['id']; ?>" <?php echo $filter_course == $cl['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($cl['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary flex-grow-1 fw-bold"><i class="fas fa-filter me-2"></i>APPLY</button>
                    <a href="online-admission-list.php" class="btn btn-sm btn-outline-secondary fw-bold">RESET</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-user-graduate text-primary me-2"></i>
                        <h5 class="card-title text-dark mb-0 fw-bold">Pending Admission Applications</h5>
                    </div>
                </div>
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle datatable-premium">
                            <thead class="table-light">
                                <tr>
                                    <th width="60">S.No.</th>
                                    <th>Date</th>
                                    <th>Student Info</th>
                                    <th>Parent Info</th>
                                    <th>Franchise / Center</th>
                                    <th>Course</th>
                                    <th>Contact</th>
                                    <th>Location</th>
                                    <th class="text-end" data-no-sort>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $sn = 1; foreach($admissions as $a): ?>
                                <tr>
                                    <td><?php echo $sn++; ?></td>
                                    <td><div class="fw-bold text-dark"><?php echo date('d M Y', strtotime($a['created_at'])); ?></div></td>
                                    <td>
                                        <div class="fw-bold"><?php echo htmlspecialchars($a['full_name']); ?></div>
                                        <div class="small text-muted">Session: <?php echo htmlspecialchars($a['session_name'] ?: 'N/A'); ?></div>
                                    </td>
                                    <td><small class="fw-semibold">Father: <?php echo htmlspecialchars($a['father_name']); ?></small></td>
                                    <td>
                                        <div class="fw-bold"><?php echo htmlspecialchars($a['center_name'] ?: 'N/A'); ?></div>
                                        <small class="text-muted"><?php echo htmlspecialchars($a['center_code'] ?: ''); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($a['course_name'] ?: 'N/A'); ?></td>
                                    <td><div class="fw-bold"><?php echo htmlspecialchars($a['mobile']); ?></div></td>
                                    <td><small><?php echo $a['city_name'] ? htmlspecialchars($a['city_name'].', '.$a['state_name']) : 'N/A'; ?></small></td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-1">
                                            <a href="admit-student.php?id=<?php echo $a['id']; ?>" class="btn btn-sm btn-outline-success btn-icon" title="Verify & Admit"><i class="fas fa-check"></i></a>
                                            <button class="btn btn-sm btn-outline-info btn-icon" title="View Details" onclick="viewStudent(<?php echo $a['id']; ?>)"><i class="fas fa-eye"></i></button>
                                            <button class="btn btn-sm btn-outline-danger btn-icon" title="Reject" onclick="rejectAdmission(<?php echo $a['id']; ?>, '<?php echo addslashes($a['full_name']); ?>')">
                                                <i class="fas fa-times"></i>
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

<!-- View Modal -->
<div class="modal fade" id="modalViewStudent" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title"><i class="fas fa-user-graduate me-2"></i>Application Details</h5>
            <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                <i class="<?php echo v('theme_modal_close_icon', 'fas fa-times'); ?>"></i>
            </button>
        </div>
        <div class="modal-body p-0" id="view-student-body">
            <div class="text-center py-5"><div class="spinner-border text-primary"></div></div>
        </div>
    </div>
  </div>
</div>

<script>
const HANDLER = '<?php echo BASE_URL; ?>ajax/student_handler.php';

function viewStudent(id) {
    document.getElementById('view-student-body').innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>';
    new bootstrap.Modal(document.getElementById('modalViewStudent')).show();
    
    fetch(`${HANDLER}?action=view_admission_details&id=${id}`)
    .then(r => r.json()).then(res => {
        if(!res.success) {
            document.getElementById('view-student-body').innerHTML = `<p class="text-danger p-3">${res.message}</p>`;
            return;
        }
        const d = res.data;
        // Construct visual details here (simplified for now)
        document.getElementById('view-student-body').innerHTML = `<div class="p-4"><h5>${d.full_name}</h5><hr/><p>More details will be rendered here based on the full schema.</p></div>`;
    });
}

function rejectAdmission(id, name) {
    if(confirm(`Are you sure you want to REJECT the application of "${name}"? This cannot be undone.`)) {
        const fd = new FormData();
        fd.append('action', 'reject_admission');
        fd.append('id', id);
        fetch(HANDLER, { method: 'POST', body: fd })
            .then(r => r.json()).then(res => {
                if(res.success) { location.reload(); }
                else alert(res.message);
            });
    }
}
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>
