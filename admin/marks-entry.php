<?php
// admin/marks-entry.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Fetch filters
$exams = $pdo->query("SELECT id, exam_name FROM exams WHERE status = 1 ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
$categories = $pdo->query("SELECT id, name FROM course_categories WHERE status = 1 ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
$courses = $pdo->query("SELECT id, name FROM courses WHERE status = 1 ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>Marks Entry</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Exam Management</li>
            <li class="breadcrumb-item active">Marks Entry</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <!-- Filter Section -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
                    <i class="fas fa-filter text-primary me-2"></i>
                    <h5 class="card-title text-dark mb-0 fw-bold">Filter Criteria for Marks Entry</h5>
                </div>
                <div class="card-body pt-4">
                    <form class="row g-3" id="marksFilterForm" novalidate>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Exam <span class="text-danger">*</span></label>
                            <select class="form-select" name="exam_id" id="f-exam" required>
                                <option value="">Select Exam...</option>
                                <?php foreach($exams as $ex): ?>
                                    <option value="<?php echo $ex['id']; ?>"><?php echo htmlspecialchars($ex['exam_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Course <span class="text-danger">*</span></label>
                            <select class="form-select" name="course_id" id="f-course" required>
                                <option value="">Select Course...</option>
                                <?php foreach($courses as $co): ?>
                                    <option value="<?php echo $co['id']; ?>"><?php echo htmlspecialchars($co['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Subject <span class="text-danger">*</span></label>
                            <select class="form-select" name="subject_id" id="f-subject" required disabled>
                                <option value="">Select Subject...</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100 fw-bold" id="btn-fetch">
                                <i class="fas fa-search me-2"></i>FETCH STUDENTS
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Data Entry Section -->
        <div class="col-lg-12 d-none" id="entrySection">
            <div class="card">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-edit text-primary me-2"></i>
                        <h5 class="card-title text-dark mb-0 fw-bold">Students List for Marks Entry</h5>
                    </div>
                    <span class="badge bg-primary-light text-primary rounded-pill px-3 py-2 fw-bold" id="subject-info">
                        <i class="fas fa-book-open me-1"></i> Subject: - (Max: 100)
                    </span>
                </div>
                <div class="card-body pt-4">
                    <form id="marksEntryForm" novalidate>
                        <input type="hidden" name="action" value="save_marks">
                        <input type="hidden" name="exam_id" id="entry-exam-id">
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle" id="marksTable">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th width="60">S.No.</th>
                                        <th class="text-start">Roll No.</th>
                                        <th class="text-start">Student Name</th>
                                        <th width="200">Marks Obtained</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Dynamic content -->
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>
                <div class="card-footer bg-white border-top py-3">
                    <div class="text-end">
                        <button type="submit" form="marksEntryForm" class="btn btn-success px-5 fw-bold" id="btn-save">
                            <i class="fas fa-save me-2"></i>SAVE ALL MARKS
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
'use strict';
const EXAM_HANDLER = '<?php echo BASE_URL; ?>ajax/exam_handler.php';
const COURSE_HANDLER = '<?php echo BASE_URL; ?>ajax/course_handler.php';

document.getElementById('f-course').addEventListener('change', function() {
    const courseId = this.value;
    const subSelect = document.getElementById('f-subject');
    subSelect.innerHTML = '<option value="">Select Subject...</option>';
    subSelect.disabled = true;

    if (courseId) {
        fetch(`${COURSE_HANDLER}?action=get_subjects&course_id=${courseId}`)
        .then(r => r.json()).then(res => {
            if (res.success && res.data.length > 0) {
                res.data.forEach(s => {
                    subSelect.innerHTML += `<option value="${s.id}">${s.subject_name}</option>`;
                });
                subSelect.disabled = false;
            }
        });
    }
});

document.getElementById('marksFilterForm').addEventListener('submit', function(e) {
    e.preventDefault();
    if (!this.checkValidity()) { this.classList.add('was-validated'); return; }
    
    const btn = document.getElementById('btn-fetch');
    btn.disabled = true;
    
    const fd = new FormData(this);
    fd.append('action', 'get_students_for_marks');
    
    fetch(EXAM_HANDLER, { method: 'POST', body: fd })
    .then(r => r.json()).then(res => {
        btn.disabled = false;
        if (res.success) {
            renderEntryTable(res.data);
            document.getElementById('entrySection').classList.remove('d-none');
            document.getElementById('entry-exam-id').value = document.getElementById('f-exam').value;
            document.getElementById('subject-info').innerHTML = `<i class="fas fa-book-open me-1"></i> Subject: ${document.getElementById('f-subject').options[document.getElementById('f-subject').selectedIndex].text}`;
            window.scrollTo({top: document.getElementById('entrySection').offsetTop - 20, behavior: 'smooth'});
        } else {
            Swal.fire('No Data', res.message, 'info');
        }
    });
});

function renderEntryTable(students) {
    const tbody = document.querySelector('#marksTable tbody');
    tbody.innerHTML = '';
    const subjectId = document.getElementById('f-subject').value;

    if (students.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center py-4 text-muted">No students found for this course.</td></tr>';
        return;
    }

    students.forEach((s, index) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="text-center">${index + 1}</td>
            <td><span class="fw-bold text-primary">${s.roll_number || 'PENDING'}</span></td>
            <td class="fw-bold">${s.full_name}</td>
            <td>
                <input type="number" step="0.01" class="form-control text-center fw-bold" name="marks[${s.id}][${subjectId}]" value="${s.current_marks || ''}" placeholder="Enter Marks" required>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

document.getElementById('marksEntryForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('btn-save');
    btn.disabled = true;
    
    fetch(EXAM_HANDLER, { method: 'POST', body: new FormData(this) })
    .then(r => r.json()).then(res => {
        btn.disabled = false;
        if (res.success) {
            Swal.fire('Success', res.message, 'success');
        } else {
            Swal.fire('Error', res.message, 'error');
        }
    });
});
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>
