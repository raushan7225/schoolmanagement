<?php
/**
 * student/take-exam.php
 * Computer Based Test (CBT) Interface for Students
 */
require_once('../common/config.php');
checkRole('student');

$exam_id = (int)($_GET['id'] ?? 0);
if (!$exam_id) { header("Location: index.php"); exit(); }

// Fetch exam details
$stmt = $pdo->prepare("SELECT e.*, c.name as course_name FROM online_exams e JOIN courses c ON e.course_id = c.id WHERE e.id = ? AND e.status = 1");
$stmt->execute([$exam_id]);
$exam = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$exam) { die("Exam not found or inactive."); }

// Check if student is already taken this exam
$user_id = $_SESSION['user_id'];
// Get student admission ID
$admStmt = $pdo->prepare("SELECT id FROM admissions WHERE user_id = ?");
$admStmt->execute([$user_id]);
$student = $admStmt->fetch(PDO::FETCH_ASSOC);
$student_id = $student['id'];

$resStmt = $pdo->prepare("SELECT id FROM online_exam_results WHERE student_id = ? AND exam_id = ?");
$resStmt->execute([$student_id, $exam_id]);
if ($resStmt->fetch()) {
    die("<div style='text-align:center; padding:50px;'><h2>You have already submitted this exam.</h2><a href='index.php'>Back to Dashboard</a></div>");
}

// Fetch questions
$qStmt = $pdo->prepare("SELECT id, question, option_a, option_b, option_c, option_d, marks FROM online_exam_questions WHERE exam_id = ? ORDER BY RAND()");
$qStmt->execute([$exam_id]);
$questions = $qStmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($questions)) { die("No questions found for this exam."); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CBT: <?php echo htmlspecialchars($exam['title']); ?></title>
    <?php include("../common/meta.php"); ?>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <link id="dynamic-theme-css" rel="stylesheet" href="<?php echo BASE_URL; ?>theme.php">
    <style>
        body { background: #f0f2f5; }
        .exam-header { background: #fff; border-bottom: 1px solid #ddd; padding: 15px 0; position: sticky; top: 0; z-index: 100; }
        .timer-box { font-size: 1.5rem; font-weight: bold; color: #dc3545; }
        .question-card { display: none; }
        .question-card.active { display: block; }
        .option-label { cursor: pointer; transition: 0.2s; border: 2px solid #eee; border-radius: 10px; padding: 15px; margin-bottom: 10px; display: block; }
        .option-label:hover { border-color: var(--theme-primary-color); background: #f8f9ff; }
        input[type="radio"]:checked + .option-label { border-color: var(--theme-primary-color); background: #eef1ff; font-weight: bold; }
        .q-nav-btn { width: 40px; height: 40px; margin: 3px; border-radius: 5px; font-weight: bold; }
        .q-nav-btn.answered { background: #198754; color: #fff; border-color: #198754; }
        .q-nav-btn.current { border: 2px solid #000; }
    </style>
</head>
<body>

<header class="exam-header shadow-sm">
    <div class="container-fluid px-5">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="mb-0 fw-bold"><?php echo htmlspecialchars($exam['title']); ?></h5>
                <span class="small text-muted"><?php echo htmlspecialchars($exam['course_name']); ?> | Total Questions: <?php echo count($questions); ?></span>
            </div>
            <div class="col-md-6 text-end">
                <div class="d-inline-block text-start me-4">
                    <div class="small text-muted text-uppercase" style="font-size: 0.7rem;">Time Remaining</div>
                    <div class="timer-box" id="timer">--:--</div>
                </div>
                <button class="btn btn-danger px-4 rounded-pill fw-bold" onclick="finishExam()">FINISH EXAM</button>
            </div>
        </div>
    </div>
</header>

<main class="container-fluid px-5 py-4">
    <div class="row g-4">
        <!-- Question Area -->
        <div class="col-lg-8">
            <form id="examForm">
                <input type="hidden" name="action" value="submit_exam">
                <input type="hidden" name="exam_id" value="<?php echo $exam_id; ?>">
                
                <?php foreach($questions as $index => $q): ?>
                <div class="card border-0 shadow-sm rounded-4 p-4 question-card <?php echo $index === 0 ? 'active' : ''; ?>" id="q-card-<?php echo $index; ?>">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="badge bg-primary-light text-primary rounded-pill px-3">Question <?php echo $index + 1; ?> of <?php echo count($questions); ?></span>
                        <span class="text-muted small">Marks: <?php echo $q['marks']; ?></span>
                    </div>
                    <h4 class="fw-bold mb-4"><?php echo htmlspecialchars($q['question']); ?></h4>
                    
                    <div class="options-list">
                        <input type="radio" class="btn-check" name="answer[<?php echo $q['id']; ?>]" id="q<?php echo $q['id']; ?>a" value="A" autocomplete="off" onchange="markAnswered(<?php echo $index; ?>)">
                        <label class="option-label" for="q<?php echo $q['id']; ?>a"><strong>A.</strong> <?php echo htmlspecialchars($q['option_a']); ?></label>

                        <input type="radio" class="btn-check" name="answer[<?php echo $q['id']; ?>]" id="q<?php echo $q['id']; ?>b" value="B" autocomplete="off" onchange="markAnswered(<?php echo $index; ?>)">
                        <label class="option-label" for="q<?php echo $q['id']; ?>b"><strong>B.</strong> <?php echo htmlspecialchars($q['option_b']); ?></label>

                        <?php if($q['option_c']): ?>
                        <input type="radio" class="btn-check" name="answer[<?php echo $q['id']; ?>]" id="q<?php echo $q['id']; ?>c" value="C" autocomplete="off" onchange="markAnswered(<?php echo $index; ?>)">
                        <label class="option-label" for="q<?php echo $q['id']; ?>c"><strong>C.</strong> <?php echo htmlspecialchars($q['option_c']); ?></label>
                        <?php endif; ?>

                        <?php if($q['option_d']): ?>
                        <input type="radio" class="btn-check" name="answer[<?php echo $q['id']; ?>]" id="q<?php echo $q['id']; ?>d" value="D" autocomplete="off" onchange="markAnswered(<?php echo $index; ?>)">
                        <label class="option-label" for="q<?php echo $q['id']; ?>d"><strong>D.</strong> <?php echo htmlspecialchars($q['option_d']); ?></label>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>

                <div class="mt-4 d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary px-4 rounded-pill" id="prevBtn" onclick="changeQuestion(-1)" disabled>Previous</button>
                    <button type="button" class="btn btn-primary-theme px-5 rounded-pill" id="nextBtn" onclick="changeQuestion(1)">Next Question</button>
                </div>
            </form>
        </div>

        <!-- Palette Area -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h6 class="fw-bold mb-3">Question Palette</h6>
                <div class="d-flex flex-wrap mb-4">
                    <?php foreach($questions as $index => $q): ?>
                    <button class="btn btn-outline-secondary q-nav-btn <?php echo $index === 0 ? 'current' : ''; ?>" id="q-nav-<?php echo $index; ?>" onclick="goToQuestion(<?php echo $index; ?>)"><?php echo $index + 1; ?></button>
                    <?php endforeach; ?>
                </div>
                <hr>
                <div class="small">
                    <div class="mb-2"><span class="badge bg-success" style="width:20px; height:20px; display:inline-block; vertical-align:middle;"></span> Answered</div>
                    <div class="mb-2"><span class="badge bg-outline-secondary border" style="width:20px; height:20px; display:inline-block; vertical-align:middle;"></span> Not Answered</div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
let currentIdx = 0;
const totalQ = <?php echo count($questions); ?>;
let timeLeft = <?php echo $exam['duration_mins'] * 60; ?>;

function updateTimer() {
    let minutes = Math.floor(timeLeft / 60);
    let seconds = timeLeft % 60;
    document.getElementById('timer').innerText = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    if (timeLeft <= 0) { finishExam(); }
    else { timeLeft--; setTimeout(updateTimer, 1000); }
}

function goToQuestion(idx) {
    document.querySelector('.question-card.active').classList.remove('active');
    document.querySelector('.q-nav-btn.current').classList.remove('current');
    
    document.getElementById(`q-card-${idx}`).classList.add('active');
    document.getElementById(`q-nav-${idx}`).classList.add('current');
    
    currentIdx = idx;
    document.getElementById('prevBtn').disabled = (currentIdx === 0);
    document.getElementById('nextBtn').innerText = (currentIdx === totalQ - 1) ? 'Finish' : 'Next Question';
}

function changeQuestion(step) {
    let newIdx = currentIdx + step;
    if (newIdx >= 0 && newIdx < totalQ) { goToQuestion(newIdx); }
    else if (newIdx === totalQ) { finishExam(); }
}

function markAnswered(idx) {
    document.getElementById(`q-nav-${idx}`).classList.add('answered');
}

function finishExam() {
    if(!confirm("Are you sure you want to submit your answers?")) return;
    const form = document.getElementById('examForm');
    const formData = new FormData(form);
    
    document.body.innerHTML = "<div style='text-align:center; padding-top:100px;'><h3>Submitting your exam... Please wait.</h3><div class='spinner-border text-primary'></div></div>";

    fetch('../ajax/submit_exam.php', { method: 'POST', body: formData })
    .then(r => r.json()).then(res => {
        if(res.success) {
            window.location.href = `exam-result.php?id=${res.result_id}`;
        } else {
            alert(res.message);
            location.reload();
        }
    });
}

document.addEventListener('DOMContentLoaded', updateTimer);
</script>
</body>
</html>
