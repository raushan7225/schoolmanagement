<?php
// admin/add-question.php
require_once('../common/config.php');
$exam_id = (int)($_GET['exam_id'] ?? 0);
if ($exam_id === 0) {
    header("Location: online-exam.php");
    exit();
}

// Fetch exam details
$stmt = $pdo->prepare("SELECT e.*, c.name as course_name FROM online_exams e JOIN courses c ON e.course_id = c.id WHERE e.id = ?");
$stmt->execute([$exam_id]);
$exam = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$exam) {
    header("Location: online-exam.php");
    exit();
}

include(__DIR__ . "/includes/header.php");

// Fetch existing questions
$qStmt = $pdo->prepare("SELECT * FROM online_exam_questions WHERE exam_id = ? ORDER BY id ASC");
$qStmt->execute([$exam_id]);
$questions = $qStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pagetitle">
    <h1>Manage Questions: <span class="text-primary"><?php echo htmlspecialchars($exam['title']); ?></span></h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item"><a href="online-exam.php">Online Exam</a></li>
            <li class="breadcrumb-item active">Manage Questions</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <!-- Add Question Form -->
        <div class="col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex align-items-center">
                    <i class="fas fa-plus-square text-white me-2"></i>
                    <h5 class="card-title text-white mb-0" id="form-title">Add Multiple Choice Question</h5>
                </div>
                <div class="card-body pt-4">
                    <form id="questionForm" novalidate>
                        <input type="hidden" name="action" value="save_question">
                        <input type="hidden" name="exam_id" value="<?php echo $exam_id; ?>">
                        <input type="hidden" name="id" id="q-id" value="">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Question Text <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="question" id="q-text" rows="3" required placeholder="Type the question here..."></textarea>
                            <div class="invalid-feedback">Please enter the question text.</div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold small text-primary">Option A <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" name="option_a" id="q-a" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold small text-primary">Option B <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" name="option_b" id="q-b" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold small text-primary">Option C</label>
                                <input type="text" class="form-control form-control-sm" name="option_c" id="q-c">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold small text-primary">Option D</label>
                                <input type="text" class="form-control form-control-sm" name="option_d" id="q-d">
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <label class="form-label fw-bold small text-success">Correct Answer <span class="text-danger">*</span></label>
                                <select class="form-select form-select-sm border-success" name="correct_answer" id="q-correct" required>
                                    <option value="">Select...</option>
                                    <option value="A">Option A</option>
                                    <option value="B">Option B</option>
                                    <option value="C">Option C</option>
                                    <option value="D">Option D</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold small">Marks</label>
                                <input type="number" class="form-control form-control-sm" name="marks" id="q-marks" value="1" required>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary" id="btn-save">
                                <i class="fas fa-save me-2"></i>SAVE QUESTION
                            </button>
                            <button type="button" class="btn btn-outline-secondary d-none" id="btn-cancel" onclick="resetForm()">Cancel Edit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Questions List -->
        <div class="col-lg-7">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex align-items-center">
                    <i class="fas fa-list-ol text-white me-2"></i>
                    <h5 class="card-title text-white mb-0">Question Bank (<?php echo count($questions); ?>)</h5>
                </div>
                <div class="card-body pt-3">
                    <?php if(empty($questions)): ?>
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-folder-open fs-1 mb-3 opacity-25"></i>
                            <p>No questions added to this exam yet.</p>
                        </div>
                    <?php else: $sn=1; foreach($questions as $q): ?>
                        <div class="question-item border-bottom pb-3 mb-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="fw-bold text-dark mb-2">Q<?php echo $sn++; ?>. <?php echo htmlspecialchars($q['question']); ?></div>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-warning btn-icon-only" onclick='editQuestion(<?php echo json_encode($q); ?>)'><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-outline-danger btn-icon-only" onclick="deleteQuestion(<?php echo $q['id']; ?>)"><i class="fas fa-trash"></i></button>
                                </div>
                            </div>
                            <div class="row g-2 small">
                                <div class="col-6"><span class="fw-bold <?php echo $q['correct_answer'] == 'A' ? 'text-success' : ''; ?>">A:</span> <?php echo htmlspecialchars($q['option_a']); ?></div>
                                <div class="col-6"><span class="fw-bold <?php echo $q['correct_answer'] == 'B' ? 'text-success' : ''; ?>">B:</span> <?php echo htmlspecialchars($q['option_b']); ?></div>
                                <div class="col-6"><span class="fw-bold <?php echo $q['correct_answer'] == 'C' ? 'text-success' : ''; ?>">C:</span> <?php echo htmlspecialchars($q['option_c']); ?></div>
                                <div class="col-6"><span class="fw-bold <?php echo $q['correct_answer'] == 'D' ? 'text-success' : ''; ?>">D:</span> <?php echo htmlspecialchars($q['option_d']); ?></div>
                            </div>
                            <div class="mt-2">
                                <span class="badge bg-success-light text-success border border-success rounded-pill px-2">Correct: Option <?php echo $q['correct_answer']; ?></span>
                                <span class="badge bg-secondary-light text-secondary border border-secondary rounded-pill px-2">Marks: <?php echo $q['marks']; ?></span>
                            </div>
                        </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
'use strict';
const HANDLER = '<?php echo BASE_URL; ?>ajax/exam_handler.php';

(function () {
    const form = document.getElementById('questionForm');
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

function editQuestion(q) {
    document.getElementById('q-id').value = q.id;
    document.getElementById('q-text').value = q.question;
    document.getElementById('q-a').value = q.option_a;
    document.getElementById('q-b').value = q.option_b;
    document.getElementById('q-c').value = q.option_c;
    document.getElementById('q-d').value = q.option_d;
    document.getElementById('q-correct').value = q.correct_answer;
    document.getElementById('q-marks').value = q.marks;
    
    document.getElementById('form-title').textContent = 'Edit Question';
    document.getElementById('btn-save').innerHTML = '<i class="fas fa-save me-2"></i>UPDATE QUESTION';
    document.getElementById('btn-cancel').classList.remove('d-none');
    window.scrollTo({top: 0, behavior: 'smooth'});
}

function resetForm() {
    document.getElementById('questionForm').reset();
    document.getElementById('q-id').value = '';
    document.getElementById('form-title').textContent = 'Add Multiple Choice Question';
    document.getElementById('btn-save').innerHTML = '<i class="fas fa-save me-2"></i>SAVE QUESTION';
    document.getElementById('btn-cancel').classList.add('d-none');
}

function deleteQuestion(id) {
    if(!confirm("Are you sure?")) return;
    fetch(HANDLER, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=delete_question&id=${id}`
    }).then(r => r.json()).then(res => {
        if(res.success) { location.reload(); }
        else alert(res.message);
    });
}
</script>

<style>
.btn-icon-only {
    width: 28px;
    height: 28px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}
</style>

<?php include(__DIR__ . "/includes/footer.php"); ?>
