<?php
// admin/online-class-list.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Fetch courses for dropdown
$courses = $pdo->query("SELECT id, name FROM courses WHERE status = 1 ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch existing classes
$classes = $pdo->query("SELECT cl.*, c.name as course_name FROM online_classes cl JOIN courses c ON cl.course_id = c.id ORDER BY cl.class_date DESC, cl.created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>Online Class <span class="badge bg-danger ms-2" style="font-size: 0.6rem; vertical-align: middle;">PRO</span></h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Online Class</li>
            <li class="breadcrumb-item active">Class List</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <!-- Add/Edit Class Form -->
        <div class="col-lg-12 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex align-items-center border-bottom">
                    <i class="fas fa-video text-primary me-2"></i>
                    <h5 class="card-title text-dark mb-0 fw-bold" id="form-title">Schedule Live/Recorded Class</h5>
                </div>
                <div class="card-body pt-4">
                    <form id="classForm" class="row g-3" novalidate>
                        <input type="hidden" name="action" value="save_class">
                        <input type="hidden" name="id" id="class-id" value="">
                        
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Class Topic <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="title" id="class-title" placeholder="e.g. Introduction to HTML" required>
                            <div class="invalid-feedback">Please enter a topic.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Select Course <span class="text-danger">*</span></label>
                            <select class="form-select" name="course_id" id="class-course" required>
                                <option value="">Select Course...</option>
                                <?php foreach($courses as $c): ?>
                                    <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Class Type</label>
                            <select class="form-select" name="class_type" id="class-type" onchange="toggleType(this.value)">
                                <option value="live">Live Session (Zoom/Meet)</option>
                                <option value="recorded">Recorded Video (YouTube)</option>
                            </select>
                        </div>
                        
                        <div class="col-md-8" id="live-box">
                            <label class="form-label fw-bold">Meeting Link <span class="text-danger">*</span></label>
                            <input type="url" class="form-control" name="live_link" id="class-link" placeholder="https://zoom.us/j/...">
                        </div>
                        
                        <div class="col-md-8 d-none" id="recorded-box">
                            <label class="form-label fw-bold">Video ID/URL (YouTube) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="video_url" id="class-video" placeholder="e.g. dQw4w9WgXcQ">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Scheduled Date</label>
                            <input type="date" class="form-control" name="class_date" id="class-date" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        
                        <div class="col-12 text-end">
                            <button type="button" class="btn btn-outline-secondary d-none" id="btn-cancel" onclick="resetForm()">Cancel</button>
                            <button type="submit" class="btn btn-primary px-5 fw-bold" id="btn-save">
                                <i class="fas fa-save me-2"></i>SAVE CLASS
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Class List -->
        <div class="col-lg-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex align-items-center border-bottom">
                    <i class="fas fa-list-ul text-primary me-2"></i>
                    <h5 class="card-title text-dark mb-0 fw-bold">Managed Online Classes</h5>
                </div>
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle datatable-premium">
                            <thead class="table-light">
                                <tr>
                                    <th width="60">S.No.</th>
                                    <th>Type</th>
                                    <th>Topic & Course</th>
                                    <th>Link / Resource</th>
                                    <th>Date</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($classes)): $sn=1; foreach($classes as $cl): ?>
                                <tr>
                                    <td><?php echo $sn++; ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $cl['class_type'] == 'live' ? 'danger' : 'info'; ?> rounded-pill px-2">
                                            <i class="fas <?php echo $cl['class_type'] == 'live' ? 'fa-broadcast-tower' : 'fa-play-circle'; ?> me-1"></i>
                                            <?php echo ucfirst($cl['class_type']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($cl['title']); ?></div>
                                        <span class="badge bg-primary-light text-primary rounded-pill px-2" style="font-size: 0.7rem;"><?php echo htmlspecialchars($cl['course_name']); ?></span>
                                    </td>
                                    <td>
                                        <?php if($cl['class_type'] == 'live'): ?>
                                            <a href="<?php echo $cl['live_link']; ?>" target="_blank" class="text-truncate d-inline-block text-primary small" style="max-width: 200px;"><?php echo $cl['live_link']; ?></a>
                                        <?php else: ?>
                                            <span class="text-muted small"><i class="fab fa-youtube text-danger me-1"></i> <?php echo $cl['video_url']; ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><div class="small text-muted"><?php echo $cl['class_date'] ? date('d M Y', strtotime($cl['class_date'])) : 'N/A'; ?></div></td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-warning btn-icon me-1" title="Edit" onclick='editClass(<?php echo json_encode($cl); ?>)'><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-outline-danger btn-icon" title="Delete" onclick="deleteClass(<?php echo $cl['id']; ?>)"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
'use strict';
const HANDLER = '<?php echo BASE_URL; ?>ajax/class_handler.php';

function toggleType(type) {
    if(type === 'live') {
        document.getElementById('live-box').classList.remove('d-none');
        document.getElementById('recorded-box').classList.add('d-none');
    } else {
        document.getElementById('live-box').classList.add('d-none');
        document.getElementById('recorded-box').classList.remove('d-none');
    }
}

(function () {
    const form = document.getElementById('classForm');
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!form.checkValidity()) { form.classList.add('was-validated'); return; }
        
        const btn = document.getElementById('btn-save');
        btn.disabled = true;
        
        fetch(HANDLER, { method: 'POST', body: new FormData(form) })
        .then(r => r.json()).then(res => {
            btn.disabled = false;
            if(res.success) { location.reload(); }
            else alert(res.message);
        });
    });
})();

function editClass(cl) {
    document.getElementById('class-id').value = cl.id;
    document.getElementById('class-title').value = cl.title;
    document.getElementById('class-course').value = cl.course_id;
    document.getElementById('class-type').value = cl.class_type;
    toggleType(cl.class_type);
    document.getElementById('class-link').value = cl.live_link || '';
    document.getElementById('class-video').value = cl.video_url || '';
    document.getElementById('class-date').value = cl.class_date;
    
    document.getElementById('form-title').textContent = 'Edit Class Details';
    document.getElementById('btn-save').innerHTML = '<i class="fas fa-save me-2"></i>UPDATE CLASS';
    document.getElementById('btn-cancel').classList.remove('d-none');
    window.scrollTo({top: 0, behavior: 'smooth'});
}

function resetForm() {
    document.getElementById('classForm').reset();
    document.getElementById('class-id').value = '';
    document.getElementById('btn-save').innerHTML = '<i class="fas fa-save me-2"></i>SAVE CLASS';
    document.getElementById('btn-cancel').classList.add('d-none');
    toggleType('live');
}

function deleteClass(id) {
    if(!confirm("Are you sure?")) return;
    fetch(HANDLER, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=delete_class&id=${id}`
    }).then(r => r.json()).then(res => {
        if(res.success) { location.reload(); }
        else alert(res.message);
    });
}
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>
