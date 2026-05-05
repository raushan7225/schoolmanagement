<?php
/**
 * ajax/exam_handler.php
 * Handles CRUD for Online Exams & Questions
 */
require_once(__DIR__ . '/../common/config.php');
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {

    case 'save_exam':
        $id           = (int)($_POST['id'] ?? 0);
        $exam_name    = trim($_POST['exam_name'] ?? '');
        $session_id   = (int)($_POST['session_year_id'] ?? 0);
        $category_id  = (int)($_POST['category_id'] ?? 0);
        $start_date   = $_POST['start_date'] ?: null;
        $end_date     = $_POST['end_date'] ?: null;
        $description  = trim($_POST['description'] ?? '');
        $status       = (int)($_POST['status'] ?? 1);

        if (!$exam_name || !$session_id || !$category_id) {
            echo json_encode(['success' => false, 'message' => 'Exam Name, Session Year, and Course Category are required.']);
            exit();
        }

        try {
            if ($id === 0) {
                $stmt = $pdo->prepare("INSERT INTO exams (session_year_id, category_id, exam_name, start_date, end_date, description, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$session_id, $category_id, $exam_name, $start_date, $end_date, $description, $status]);
            } else {
                $stmt = $pdo->prepare("UPDATE exams SET session_year_id=?, category_id=?, exam_name=?, start_date=?, end_date=?, description=?, status=? WHERE id=?");
                $stmt->execute([$session_id, $category_id, $exam_name, $start_date, $end_date, $description, $status, $id]);
            }
            echo json_encode(['success' => true, 'message' => 'Exam saved successfully.']);
        } catch (Exception $e) { echo json_encode(['success' => false, 'message' => $e->getMessage()]); }
        break;

    case 'delete_exam':
        $id = (int)($_POST['id'] ?? 0);
        $pdo->prepare("DELETE FROM exams WHERE id = ?")->execute([$id]);
        echo json_encode(['success' => true]);
        break;

    case 'save_question':
        $id = (int)($_POST['id'] ?? 0);
        $exam_id = (int)($_POST['exam_id'] ?? 0);
        $question = trim($_POST['question'] ?? '');
        $a = trim($_POST['option_a'] ?? '');
        $b = trim($_POST['option_b'] ?? '');
        $c = trim($_POST['option_c'] ?? '');
        $d = trim($_POST['option_d'] ?? '');
        $correct = $_POST['correct_answer'] ?? '';
        $marks = (int)($_POST['marks'] ?? 1);

        if (!$exam_id || !$question || !$a || !$b || !$correct) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
            exit();
        }

        try {
            if ($id === 0) {
                $stmt = $pdo->prepare("INSERT INTO online_exam_questions (exam_id, question, option_a, option_b, option_c, option_d, correct_answer, marks) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$exam_id, $question, $a, $b, $c, $d, $correct, $marks]);
            } else {
                $stmt = $pdo->prepare("UPDATE online_exam_questions SET question=?, option_a=?, option_b=?, option_c=?, option_d=?, correct_answer=?, marks=? WHERE id=?");
                $stmt->execute([$question, $a, $b, $c, $d, $correct, $marks, $id]);
            }
            echo json_encode(['success' => true, 'message' => 'Question saved.']);
        } catch (Exception $e) { echo json_encode(['success' => false, 'message' => $e->getMessage()]); }
        break;

    case 'delete_question':
        $id = (int)($_POST['id'] ?? 0);
        $pdo->prepare("DELETE FROM online_exam_questions WHERE id = ?")->execute([$id]);
        echo json_encode(['success' => true]);
        break;

    case 'save_question_group':
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $desc = trim($_POST['description'] ?? '');

        if (!$name) {
            echo json_encode(['success' => false, 'message' => 'Group name is required.']);
            exit();
        }

        try {
            if ($id === 0) {
                $stmt = $pdo->prepare("INSERT INTO question_groups (name, description) VALUES (?, ?)");
                $stmt->execute([$name, $desc]);
            } else {
                $stmt = $pdo->prepare("UPDATE question_groups SET name=?, description=? WHERE id=?");
                $stmt->execute([$name, $desc, $id]);
            }
            echo json_encode(['success' => true]);
        } catch (Exception $e) { echo json_encode(['success' => false, 'message' => $e->getMessage()]); }
        break;

    case 'delete_question_group':
        $id = (int)($_POST['id'] ?? 0);
        $pdo->prepare("DELETE FROM question_groups WHERE id = ?")->execute([$id]);
        echo json_encode(['success' => true]);
        break;

    case 'save_grade_range':
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['grade_name'] ?? '');
        $min = (float)($_POST['min_percentage'] ?? 0);
        $max = (float)($_POST['max_percentage'] ?? 0);
        $point = (float)($_POST['grade_point'] ?? 0);
        $remark = trim($_POST['remark'] ?? '');
        $status = (int)($_POST['status'] ?? 1);

        if (!$name || $min < 0 || $max < 0) {
            echo json_encode(['success' => false, 'message' => 'Please fill all required fields correctly.']);
            exit();
        }

        try {
            if ($id === 0) {
                $stmt = $pdo->prepare("INSERT INTO grade_ranges (grade_name, min_percentage, max_percentage, grade_point, remark, status) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$name, $min, $max, $point, $remark, $status]);
            } else {
                $stmt = $pdo->prepare("UPDATE grade_ranges SET grade_name=?, min_percentage=?, max_percentage=?, grade_point=?, remark=?, status=? WHERE id=?");
                $stmt->execute([$name, $min, $max, $point, $remark, $status, $id]);
            }
            echo json_encode(['success' => true]);
        } catch (Exception $e) { echo json_encode(['success' => false, 'message' => $e->getMessage()]); }
        break;

    case 'delete_grade_range':
        $id = (int)($_POST['id'] ?? 0);
        $pdo->prepare("DELETE FROM grade_ranges WHERE id = ?")->execute([$id]);
        echo json_encode(['success' => true]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action.']);
}
