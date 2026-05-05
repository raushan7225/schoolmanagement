<?php
/**
 * ajax/class_handler.php
 * Handles CRUD for Online Classes
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

    case 'save_class':
        $id = (int)($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $course_id = (int)($_POST['course_id'] ?? 0);
        $type = $_POST['class_type'] ?? 'live';
        $link = trim($_POST['live_link'] ?? '');
        $video = trim($_POST['video_url'] ?? '');
        $date = $_POST['class_date'] ?? date('Y-m-d');

        if (!$title || !$course_id) {
            echo json_encode(['success' => false, 'message' => 'Title and Course are required.']);
            exit();
        }

        try {
            if ($id === 0) {
                $stmt = $pdo->prepare("INSERT INTO online_classes (title, course_id, class_type, live_link, video_url, class_date) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$title, $course_id, $type, $link, $video, $date]);
            } else {
                $stmt = $pdo->prepare("UPDATE online_classes SET title=?, course_id=?, class_type=?, live_link=?, video_url=?, class_date=? WHERE id=?");
                $stmt->execute([$title, $course_id, $type, $link, $video, $date, $id]);
            }
            echo json_encode(['success' => true, 'message' => 'Class saved.']);
        } catch (Exception $e) { echo json_encode(['success' => false, 'message' => $e->getMessage()]); }
        break;

    case 'delete_class':
        $id = (int)($_POST['id'] ?? 0);
        $pdo->prepare("DELETE FROM online_classes WHERE id = ?")->execute([$id]);
        echo json_encode(['success' => true]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action.']);
}
