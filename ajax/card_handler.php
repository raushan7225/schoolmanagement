<?php
/**
 * ajax/card_handler.php
 * Handles Card Template and Generation CRUD
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

    case 'save_template':
        $id = (int)($_POST['id'] ?? 0);
        $type = $_POST['type'] ?? '';
        $name = trim($_POST['name'] ?? '');
        $status = (int)($_POST['status'] ?? 1);
        
        if (!$type || !$name) {
            echo json_encode(['success' => false, 'message' => 'Type and Name are required.']);
            exit();
        }

        // Background Image handling
        $bg_image = "";
        if (isset($_FILES['background_image']) && $_FILES['background_image']['error'] === UPLOAD_ERR_OK) {
            $dir = "../media/templates/";
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            $ext = pathinfo($_FILES['background_image']['name'], PATHINFO_EXTENSION);
            $bg_image = "TEMP_" . time() . "_" . rand(100,999) . "." . $ext;
            move_uploaded_file($_FILES['background_image']['tmp_name'], $dir . $bg_image);
        }

        try {
            if ($id === 0) {
                $stmt = $pdo->prepare("INSERT INTO document_templates (type, name, background_image, status) VALUES (?, ?, ?, ?)");
                $stmt->execute([$type, $name, $bg_image, $status]);
            } else {
                $params = [$type, $name, $status];
                $sql = "UPDATE document_templates SET type=?, name=?, status=?";
                if ($bg_image) { $sql .= ", background_image=?"; $params[] = $bg_image; }
                $sql .= " WHERE id=?"; $params[] = $id;
                $pdo->prepare($sql)->execute($params);
            }
            echo json_encode(['success' => true, 'message' => 'Template saved successfully.']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'delete_template':
        $id = (int)($_POST['id'] ?? 0);
        try {
            $pdo->prepare("DELETE FROM document_templates WHERE id = ?")->execute([$id]);
            echo json_encode(['success' => true, 'message' => 'Template deleted.']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'save_download_window':
        $id = (int)($_POST['id'] ?? 0);
        $exam_id = (int)($_POST['exam_id'] ?? 0);
        $start_date = $_POST['start_date'] ?? '';
        $end_date = $_POST['end_date'] ?? '';
        $status = (int)($_POST['status'] ?? 1);

        if (!$exam_id || !$start_date || !$end_date) {
            echo json_encode(['success' => false, 'message' => 'All fields are required.']);
            exit();
        }

        try {
            if ($id === 0) {
                $stmt = $pdo->prepare("INSERT INTO admit_download_settings (exam_id, start_date, end_date, status) VALUES (?, ?, ?, ?)");
                $stmt->execute([$exam_id, $start_date, $end_date, $status]);
            } else {
                $pdo->prepare("UPDATE admit_download_settings SET exam_id=?, start_date=?, end_date=?, status=? WHERE id=?")
                    ->execute([$exam_id, $start_date, $end_date, $status, $id]);
            }
            echo json_encode(['success' => true, 'message' => 'Download window saved.']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'delete_download_window':
        $id = (int)($_POST['id'] ?? 0);
        try {
            $pdo->prepare("DELETE FROM admit_download_settings WHERE id = ?")->execute([$id]);
            echo json_encode(['success' => true, 'message' => 'Window deleted.']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'issue_certificate':
        $admission_id = (int)($_POST['admission_id'] ?? 0);
        $template_id = (int)($_POST['template_id'] ?? 0);
        $unique_id = trim($_POST['unique_id'] ?? '');
        $issued_at = $_POST['issued_at'] ?? date('Y-m-d');

        if (!$admission_id || !$template_id || !$unique_id) {
            echo json_encode(['success' => false, 'message' => 'All fields are required.']);
            exit();
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO issued_documents (admission_id, template_id, document_type, unique_id, issued_at) VALUES (?, ?, 'certificate', ?, ?)");
            $stmt->execute([$admission_id, $template_id, $unique_id, $issued_at]);
            echo json_encode(['success' => true, 'message' => 'Certificate issued successfully.']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'search_student':
        $reg_no = trim($_POST['reg_no'] ?? '');
        if (!$reg_no) {
            echo json_encode(['success' => false, 'message' => 'Registration No. required.']);
            exit();
        }
        try {
            $stmt = $pdo->prepare("
                SELECT a.*, c.name as course_name, cnt.name as center_name, cnt.code as center_code
                FROM admissions a
                LEFT JOIN courses c ON a.course_id = c.id
                LEFT JOIN centers cnt ON a.center_id = cnt.id
                WHERE a.roll_number = ? OR a.mobile = ?
                LIMIT 1
            ");
            $stmt->execute([$reg_no, $reg_no]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($student) {
                echo json_encode(['success' => true, 'data' => $student]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Student not found.']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action.']);
}
