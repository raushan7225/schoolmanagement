<?php
/**
 * ajax/enquiry_handler.php
 * Handles all CRUD operations for the enquiries table.
 * Actions: add, edit, delete, view, change_status
 */
require_once('../common/config.php');
header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Only enforce admin check for non-public actions
if ($action !== 'add' && (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin')) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// ─── Helper: validate required fields ─────────────────────────────────────
function validate_enquiry($data) {
    $errors = [];
    if (empty(trim($data['full_name'] ?? '')))   $errors[] = 'Student name is required.';
    if (empty(trim($data['mobile'] ?? '')))       $errors[] = 'Mobile number is required.';
    if (!preg_match('/^[6-9]\d{9}$/', trim($data['mobile'] ?? '')))
                                                  $errors[] = 'Mobile must be a valid 10-digit Indian number.';
    if (empty($data['center_id']))                $errors[] = 'Franchise/Center is required.';
    if (empty($data['course_id']))                $errors[] = 'Course is required.';
    if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL))
                                                  $errors[] = 'Invalid email address.';
    return $errors;
}

switch ($action) {

    // ─── ADD ──────────────────────────────────────────────────────────────
    case 'add':
        $errors = validate_enquiry($_POST);
        if ($errors) { echo json_encode(['status' => 'error', 'message' => implode(' ', $errors)]); exit(); }

        // Allow multiple enquiries. We can add spam protection later if needed.

        $user_id = $_SESSION['user_id'] ?? null;
        if ($_SESSION['role'] !== 'student') $user_id = null; // Only link for student role

        $stmt = $pdo->prepare("
            INSERT INTO enquiries 
            (user_id, full_name, mobile, email, gender, dob, qualification, course_id, course_category,
             center_id, state, district, address, country_id, state_id, district_id, city_id, pincode,
             prob_admission_date, message, session_id, source, approval_status, status)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,'new',1)
        ");
        $stmt->execute([
            $user_id,
            trim($_POST['full_name']),
            trim($_POST['mobile']),
            trim($_POST['email'] ?? ''),
            $_POST['gender'] ?? '',
            $_POST['dob'] ?: null,
            trim($_POST['qualification'] ?? ''),
            (int)$_POST['course_id'],
            trim($_POST['course_category'] ?? ''),
            (int)$_POST['center_id'],
            trim($_POST['state'] ?? ''),
            trim($_POST['district'] ?? ''),
            trim($_POST['address'] ?? ''),
            (int)($_POST['country_id'] ?? 1),
            (int)($_POST['state_id'] ?? 0),
            (int)($_POST['district_id'] ?? 0),
            (int)($_POST['city_id'] ?? 0),
            trim($_POST['pincode'] ?? ''),
            $_POST['prob_admission_date'] ?: null,
            trim($_POST['message'] ?? ''),
            (int)($_POST['session_id'] ?? 0),
            $_POST['source'] ?? 'manual'
        ]);
        echo json_encode(['status' => 'success', 'message' => 'Enquiry added successfully!']);
        break;

    // ─── EDIT ─────────────────────────────────────────────────────────────
    case 'edit':
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) { echo json_encode(['status' => 'error', 'message' => 'Invalid enquiry ID.']); exit(); }

        $errors = validate_enquiry($_POST);
        if ($errors) { echo json_encode(['status' => 'error', 'message' => implode(' ', $errors)]); exit(); }

        $stmt = $pdo->prepare("
            UPDATE enquiries SET
                full_name=?, mobile=?, email=?, gender=?, dob=?, qualification=?,
                course_id=?, course_category=?, center_id=?, state=?, district=?, address=?,
                country_id=?, state_id=?, district_id=?, city_id=?, pincode=?,
                prob_admission_date=?, message=?, session_id=?, approval_status=?
            WHERE id=?
        ");
        $stmt->execute([
            trim($_POST['full_name']),
            trim($_POST['mobile']),
            trim($_POST['email'] ?? ''),
            $_POST['gender'] ?? '',
            $_POST['dob'] ?: null,
            trim($_POST['qualification'] ?? ''),
            (int)$_POST['course_id'],
            trim($_POST['course_category'] ?? ''),
            (int)$_POST['center_id'],
            trim($_POST['state'] ?? ''),
            trim($_POST['district'] ?? ''),
            trim($_POST['address'] ?? ''),
            (int)($_POST['country_id'] ?? 1),
            (int)($_POST['state_id'] ?? 0),
            (int)($_POST['district_id'] ?? 0),
            (int)($_POST['city_id'] ?? 0),
            trim($_POST['pincode'] ?? ''),
            $_POST['prob_admission_date'] ?: null,
            trim($_POST['message'] ?? ''),
            (int)($_POST['session_id'] ?? 0),
            $_POST['approval_status'] ?? 'new',
            $id,
        ]);
        echo json_encode(['status' => 'success', 'message' => 'Enquiry updated successfully!']);
        break;

    // ─── DELETE ───────────────────────────────────────────────────────────
    case 'delete':
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) { echo json_encode(['status' => 'error', 'message' => 'Invalid ID.']); exit(); }
        $pdo->prepare("DELETE FROM enquiries WHERE id = ?")->execute([$id]);
        echo json_encode(['status' => 'success', 'message' => 'Enquiry deleted.']);
        break;

    // ─── VIEW (GET) ───────────────────────────────────────────────────────
    case 'view':
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) { echo json_encode(['status' => 'error', 'message' => 'Invalid ID.']); exit(); }
        $stmt = $pdo->prepare("
            SELECT e.*, c.name as course_name, f.center_name, f.center_code,
                   st.name as state_name, dt.name as district_name, ct.name as city_name,
                   sess.session_label
            FROM enquiries e
            LEFT JOIN courses c ON e.course_id = c.id
            LEFT JOIN franchises f ON e.center_id = f.id
            LEFT JOIN states st ON e.state_id = st.id
            LEFT JOIN districts dt ON e.district_id = dt.id
            LEFT JOIN cities ct ON e.city_id = ct.id
            LEFT JOIN admission_sessions sess ON e.session_id = sess.id
            WHERE e.id = ?
        ");
        $stmt->execute([$id]);
        $enquiry = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($enquiry) {
            echo json_encode(['status' => 'success', 'data' => $enquiry]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Not found.']);
        }
        break;

    // ─── CHANGE STATUS ────────────────────────────────────────────────────
    case 'change_status':
        $id     = (int)($_POST['id'] ?? 0);
        $status = $_POST['approval_status'] ?? '';
        $allowed = ['new', 'contacted', 'closed'];
        if (!$id || !in_array($status, $allowed)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid data.']); exit();
        }
        $pdo->prepare("UPDATE enquiries SET approval_status=? WHERE id=?")->execute([$status, $id]);
        echo json_encode(['status' => 'success', 'message' => 'Status updated.']);
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Unknown action.']);
}
?>
