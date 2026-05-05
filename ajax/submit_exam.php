<?php
/**
 * ajax/submit_exam.php
 * Evaluates student MCQ answers and stores results
 */
require_once(__DIR__ . '/../common/config.php');
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$exam_id = (int)($_POST['exam_id'] ?? 0);
$answers = $_POST['answer'] ?? []; // Array of [question_id => selected_option]

try {
    // 1. Get Admission/Student ID
    $admStmt = $pdo->prepare("SELECT id, course_id FROM admissions WHERE user_id = ?");
    $admStmt->execute([$user_id]);
    $student = $admStmt->fetch(PDO::FETCH_ASSOC);
    if (!$student) throw new Exception("Student enrollment not found.");
    $student_id = $student['id'];

    // 2. Double check if already submitted
    $chk = $pdo->prepare("SELECT id FROM online_exam_results WHERE student_id = ? AND exam_id = ?");
    $chk->execute([$student_id, $exam_id]);
    if ($chk->fetch()) throw new Exception("Already submitted.");

    // 3. Fetch Exam Settings
    $exStmt = $pdo->prepare("SELECT * FROM online_exams WHERE id = ?");
    $exStmt->execute([$exam_id]);
    $exam = $exStmt->fetch(PDO::FETCH_ASSOC);
    if (!$exam) throw new Exception("Exam not found.");

    // 4. Fetch Correct Answers
    $qStmt = $pdo->prepare("SELECT id, correct_answer, marks FROM online_exam_questions WHERE exam_id = ?");
    $qStmt->execute([$exam_id]);
    $questions = $qStmt->fetchAll(PDO::FETCH_ASSOC);

    $totalQ = count($questions);
    $correctCount = 0;
    $obtainedMarks = 0;
    $totalPossibleMarks = 0;

    foreach ($questions as $q) {
        $totalPossibleMarks += $q['marks'];
        $submittedAns = $answers[$q['id']] ?? null;
        if ($submittedAns === $q['correct_answer']) {
            $correctCount++;
            $obtainedMarks += $q['marks'];
        }
    }

    $percentage = ($totalPossibleMarks > 0) ? ($obtainedMarks / $totalPossibleMarks) * 100 : 0;
    $resultStatus = ($percentage >= $exam['pass_percentage']) ? 'pass' : 'fail';

    // 5. Save Result
    $ins = $pdo->prepare("INSERT INTO online_exam_results (student_id, exam_id, total_questions, correct_answers, obtained_marks, percentage, result_status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $ins->execute([$student_id, $exam_id, $totalQ, $correctCount, $obtainedMarks, $percentage, $resultStatus]);
    $result_id = $pdo->lastInsertId();

    echo json_encode([
        'success' => true, 
        'message' => 'Exam submitted successfully.',
        'result_id' => $result_id
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
