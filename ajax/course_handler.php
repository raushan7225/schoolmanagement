<?php
/**
 * ajax/course_handler.php
 * Handles Course and Category CRUD
 */
require_once('../common/config.php');
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {

    case 'add_category':
    case 'edit_category':
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $fee = (float)($_POST['franchise_fee'] ?? 0);
        $status = (int)($_POST['status'] ?? 1);

        if (!$name) {
            echo json_encode(['success' => false, 'message' => 'Category name is required.']);
            exit();
        }

        try {
            if ($action === 'add_category') {
                $stmt = $pdo->prepare("INSERT INTO course_categories (name, franchise_fee, status) VALUES (?, ?, ?)");
                $stmt->execute([$name, $fee, $status]);
            } else {
                $stmt = $pdo->prepare("UPDATE course_categories SET name = ?, franchise_fee = ?, status = ? WHERE id = ?");
                $stmt->execute([$name, $fee, $status, $id]);
            }
            echo json_encode(['success' => true, 'message' => 'Category saved successfully.']);
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                echo json_encode(['success' => false, 'message' => 'Category name already exists.']);
            } else {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
        }
        break;

    case 'add_course':
    case 'edit_course':
        $id = (int)($_POST['id'] ?? 0);
        $cat_id = (int)($_POST['category_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $code = trim($_POST['code'] ?? '');
        $dur_type = $_POST['duration_type'] ?? 'years';
        $dur_val = (int)($_POST['duration_value'] ?? 0);
        
        $years = ($dur_type === 'years') ? $dur_val : 0;
        $months = ($dur_type === 'months') ? $dur_val : 0;
        $days = ($dur_type === 'days') ? $dur_val : 0;
        $semesters = ($dur_type === 'semesters') ? $dur_val : 0;
        $eligibility = trim($_POST['eligibility'] ?? '');
        $reg_fee = (float)($_POST['registration_fee'] ?? 0);
        $course_fee = (float)($_POST['course_fee'] ?? 0);
        $fran_fee = (float)($_POST['franchise_fee'] ?? 0);
        $part_comm = (float)($_POST['partner_commission'] ?? 0);
        $exam_fee = (float)($_POST['exam_fee'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $status = (int)($_POST['status'] ?? 1);

        if (!$name || !$cat_id) {
            echo json_encode(['success' => false, 'message' => 'Name and Category are required.']);
            exit();
        }

        // Thumbnail handling
        $thumb_name = "";
        if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
            $dir = "../media/courses/";
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            $ext = pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION);
            $thumb_name = "COURSE_" . time() . "_" . rand(100,999) . "." . $ext;
            move_uploaded_file($_FILES['thumbnail']['tmp_name'], $dir . $thumb_name);
        }

        try {
            if ($action === 'add_course') {
                $sql = "INSERT INTO courses (category_id, name, code, duration_years, duration_months, duration_days, duration_semesters, registration_fee, course_fee, franchise_fee, partner_commission, exam_fee, eligibility, description, thumbnail, status) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$cat_id, $name, $code, $years, $months, $days, $semesters, $reg_fee, $course_fee, $fran_fee, $part_comm, $exam_fee, $eligibility, $description, $thumb_name, $status]);
            } else {
                $params = [$cat_id, $name, $code, $years, $months, $days, $semesters, $reg_fee, $course_fee, $fran_fee, $part_comm, $exam_fee, $eligibility, $description, $status];
                $sql = "UPDATE courses SET category_id=?, name=?, code=?, duration_years=?, duration_months=?, duration_days=?, duration_semesters=?, registration_fee=?, course_fee=?, franchise_fee=?, partner_commission=?, exam_fee=?, eligibility=?, description=?, status=?";
                
                if ($thumb_name) {
                    $sql .= ", thumbnail=?";
                    $params[] = $thumb_name;
                }
                
                $sql .= " WHERE id=?";
                $params[] = $id;
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
            }
            echo json_encode(['success' => true, 'message' => 'Course saved successfully.']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'delete_course':
        $id = (int)($_POST['id'] ?? 0);
        try {
            // Check if used in admissions
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM admissions WHERE course_id = ?");
            $stmt->execute([$id]);
            if ($stmt->fetchColumn() > 0) {
                throw new Exception("Cannot delete course as it is linked to active student admissions.");
            }
            
            $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true, 'message' => 'Course deleted.']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'delete_category':
        $id = (int)($_POST['id'] ?? 0);
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM courses WHERE category_id = ?");
            $stmt->execute([$id]);
            if ($stmt->fetchColumn() > 0) {
                throw new Exception("Cannot delete category as it is linked to active courses.");
            }
            
            $stmt = $pdo->prepare("DELETE FROM course_categories WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true, 'message' => 'Category deleted.']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'add_subject':
    case 'edit_subject':
        $id = (int)($_POST['id'] ?? 0);
        $course_id = (int)($_POST['course_id'] ?? 0);
        $year_sem = trim($_POST['year_sem'] ?? '');
        $subject_name = trim($_POST['subject_name'] ?? '');
        $subject_code = trim($_POST['subject_code'] ?? '');
        $subject_type = $_POST['subject_type'] ?? 'Theory';
        $total_lessons = (int)($_POST['total_lessons'] ?? 0);
        $status = (int)($_POST['status'] ?? 1);

        if (!$course_id || !$subject_name) {
            echo json_encode(['success' => false, 'message' => 'Course and Subject Name are required.']);
            exit();
        }

        try {
            if ($action === 'add_subject') {
                $stmt = $pdo->prepare("INSERT INTO subjects (course_id, year_sem, subject_name, subject_code, subject_type, total_lessons, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$course_id, $year_sem, $subject_name, $subject_code, $subject_type, $total_lessons, $status]);
            } else {
                $stmt = $pdo->prepare("UPDATE subjects SET course_id=?, year_sem=?, subject_name=?, subject_code=?, subject_type=?, total_lessons=?, status=? WHERE id=?");
                $stmt->execute([$course_id, $year_sem, $subject_name, $subject_code, $subject_type, $total_lessons, $status, $id]);
            }
            echo json_encode(['success' => true, 'message' => 'Subject saved successfully.']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'delete_subject':
        $id = (int)($_POST['id'] ?? 0);
        try {
            $stmt = $pdo->prepare("DELETE FROM subjects WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true, 'message' => 'Subject deleted.']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'add_year':
    case 'edit_year':
        $id = (int)($_POST['id'] ?? 0);
        $label = trim($_POST['year_label'] ?? '');
        $type = $_POST['year_type'] ?? 'Year';
        $status = (int)($_POST['status'] ?? 1);
        if (!$label) { echo json_encode(['success' => false, 'message' => 'Label required']); exit(); }
        try {
            if ($action === 'add_year') {
                $pdo->prepare("INSERT INTO academic_years (year_label, year_type, status) VALUES (?,?,?)")->execute([$label, $type, $status]);
            } else {
                $pdo->prepare("UPDATE academic_years SET year_label=?, year_type=?, status=? WHERE id=?")->execute([$label, $type, $status, $id]);
            }
            echo json_encode(['success' => true, 'message' => 'Year saved.']);
        } catch (PDOException $e) { echo json_encode(['success' => false, 'message' => $e->getMessage()]); }
        break;

    case 'delete_year':
        $id = (int)($_POST['id'] ?? 0);
        $pdo->prepare("DELETE FROM academic_years WHERE id=?")->execute([$id]);
        echo json_encode(['success' => true]);
        break;

    case 'add_duration':
    case 'edit_duration':
        $id = (int)($_POST['id'] ?? 0);
        $label = trim($_POST['duration_label'] ?? '');
        $years = (int)($_POST['years'] ?? 0);
        $months = (int)($_POST['months'] ?? 0);
        $days = (int)($_POST['days'] ?? 0);
        $status = (int)($_POST['status'] ?? 1);
        
        if (!$label) { echo json_encode(['success' => false, 'message' => 'Label required']); exit(); }
        try {
            if ($action === 'add_duration') {
                $pdo->prepare("INSERT INTO course_durations (duration_label, years, months, days, status) VALUES (?,?,?,?,?)")->execute([$label, $years, $months, $days, $status]);
            } else {
                $pdo->prepare("UPDATE course_durations SET duration_label=?, years=?, months=?, days=?, status=? WHERE id=?")->execute([$label, $years, $months, $days, $status, $id]);
            }
            echo json_encode(['success' => true, 'message' => 'Duration saved.']);
        } catch (PDOException $e) { echo json_encode(['success' => false, 'message' => $e->getMessage()]); }
        break;

    case 'delete_duration':
        $id = (int)($_POST['id'] ?? 0);
        $pdo->prepare("DELETE FROM course_durations WHERE id=?")->execute([$id]);
        echo json_encode(['success' => true]);
        break;

    case 'add_session':
    case 'edit_session':
        $id = (int)($_POST['id'] ?? 0);
        $label = trim($_POST['session_label'] ?? '');
        $status = (int)($_POST['status'] ?? 1);
        if (!$label) { echo json_encode(['success' => false, 'message' => 'Label required']); exit(); }
        try {
            if ($action === 'add_session') {
                $pdo->prepare("INSERT INTO admission_sessions (session_label, status) VALUES (?,?)")->execute([$label, $status]);
            } else {
                $pdo->prepare("UPDATE admission_sessions SET session_label=?, status=? WHERE id=?")->execute([$label, $status, $id]);
            }
            echo json_encode(['success' => true, 'message' => 'Session saved.']);
        } catch (PDOException $e) { echo json_encode(['success' => false, 'message' => $e->getMessage()]); }
        break;

    case 'delete_session':
        $id = (int)($_POST['id'] ?? 0);
        $pdo->prepare("DELETE FROM admission_sessions WHERE id=?")->execute([$id]);
        echo json_encode(['success' => true]);
        break;

    case 'add_material':
    case 'edit_material':
        $id = (int)($_POST['id'] ?? 0);
        $course_id = (int)($_POST['course_id'] ?? 0);
        $subject_id = (int)($_POST['subject_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $status = (int)($_POST['status'] ?? 1);

        if (!$course_id || !$title) {
            echo json_encode(['success' => false, 'message' => 'Course and Title required']);
            exit();
        }

        $file_name = "";
        if (isset($_FILES['material_file']) && $_FILES['material_file']['error'] === UPLOAD_ERR_OK) {
            $dir = "../media/materials/";
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            $ext = pathinfo($_FILES['material_file']['name'], PATHINFO_EXTENSION);
            $file_name = "MAT_" . time() . "_" . rand(100,999) . "." . $ext;
            move_uploaded_file($_FILES['material_file']['tmp_name'], $dir . $file_name);
        }

        try {
            if ($action === 'add_material') {
                $stmt = $pdo->prepare("INSERT INTO study_materials (course_id, subject_id, title, description, file_path, status) VALUES (?,?,?,?,?,?)");
                $stmt->execute([$course_id, $subject_id ?: null, $title, $description, $file_name, $status]);
            } else {
                $params = [$course_id, $subject_id ?: null, $title, $description, $status];
                $sql = "UPDATE study_materials SET course_id=?, subject_id=?, title=?, description=?, status=?";
                if ($file_name) { $sql .= ", file_path=?"; $params[] = $file_name; }
                $sql .= " WHERE id=?"; $params[] = $id;
                $pdo->prepare($sql)->execute($params);
            }
            echo json_encode(['success' => true, 'message' => 'Material saved.']);
        } catch (PDOException $e) { echo json_encode(['success' => false, 'message' => $e->getMessage()]); }
        break;

    case 'delete_material':
        $id = (int)($_POST['id'] ?? 0);
        $pdo->prepare("DELETE FROM study_materials WHERE id=?")->execute([$id]);
        echo json_encode(['success' => true]);
        break;

    case 'get_subjects':
        $course_id = (int)($_GET['course_id'] ?? 0);
        $stmt = $pdo->prepare("SELECT id, subject_name FROM subjects WHERE course_id = ? AND status = 1 ORDER BY subject_name ASC");
        $stmt->execute([$course_id]);
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        break;

    case 'view_category':
        $id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
        $stmt = $pdo->prepare("SELECT * FROM course_categories WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode(['success' => !!$data, 'data' => $data, 'message' => $data ? '' : 'Category not found.']);
        break;

    case 'view_course':
        $id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
        $stmt = $pdo->prepare("
            SELECT c.*, cat.name as category_name 
            FROM courses c
            LEFT JOIN course_categories cat ON c.category_id = cat.id
            WHERE c.id = ?
        ");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode(['success' => !!$data, 'data' => $data, 'message' => $data ? '' : 'Course not found.']);
        break;

    case 'view_subject':
        $id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
        $stmt = $pdo->prepare("
            SELECT s.*, c.name as course_name 
            FROM subjects s
            LEFT JOIN courses c ON s.course_id = c.id
            WHERE s.id = ?
        ");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode(['success' => !!$data, 'data' => $data, 'message' => $data ? '' : 'Subject not found.']);
        break;

    case 'view_material':
        $id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
        $stmt = $pdo->prepare("
            SELECT m.*, c.name as course_name, s.subject_name 
            FROM study_materials m
            LEFT JOIN courses c ON m.course_id = c.id
            LEFT JOIN subjects s ON m.subject_id = s.id
            WHERE m.id = ?
        ");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode(['success' => !!$data, 'data' => $data, 'message' => $data ? '' : 'Material not found.']);
        break;

    case 'view_year':
        $id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
        $stmt = $pdo->prepare("SELECT * FROM academic_years WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode(['success' => !!$data, 'data' => $data, 'message' => $data ? '' : 'Year not found.']);
        break;

    case 'view_duration':
        $id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
        $stmt = $pdo->prepare("SELECT * FROM course_durations WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode(['success' => !!$data, 'data' => $data, 'message' => $data ? '' : 'Duration not found.']);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action.']);
}
