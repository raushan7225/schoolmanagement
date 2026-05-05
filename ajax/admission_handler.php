<?php
/**
 * ajax/admission_handler.php
 * Handles student admission from public portal and admin panel.
 */
require_once('../common/config.php');
header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? 'add';

// Auth Check
if ($action === 'edit' || $action === 'view') {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        exit();
    }
    if ($_SESSION['role'] === 'student') {
        $target_id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        $checkStmt = $pdo->prepare("SELECT user_id FROM admissions WHERE id = ?");
        $checkStmt->execute([$target_id]);
        $owner_id = $checkStmt->fetchColumn();
        if ($owner_id != $_SESSION['user_id']) {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
            exit();
        }
    } elseif ($_SESSION['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $action !== 'view') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit();
}

try {
    // --- VIEW ACTION ---
    if ($action === 'view') {
        $id = (int)($_GET['id'] ?? 0);
        $stmt = $pdo->prepare("
            SELECT a.*, c.name as course_name, f.center_name, f.center_code,
                   st.name as state_name, dt.name as district_name, ct.name as city_name,
                   sess.session_label
            FROM admissions a
            LEFT JOIN courses c ON a.course_id = c.id
            LEFT JOIN franchises f ON a.center_id = f.id
            LEFT JOIN states st ON a.state_id = st.id
            LEFT JOIN districts dt ON a.district_id = dt.id
            LEFT JOIN cities ct ON a.city_id = ct.id
            LEFT JOIN admission_sessions sess ON a.session_id = sess.id
            WHERE a.id = ?
        ");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            echo json_encode(['status' => 'success', 'data' => $data]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Record not found.']);
        }
        exit();
    }

    $pdo->beginTransaction();

    // 1. Handle User Account Creation/Link
    if ($action === 'add') {
        $full_name = strtoupper(trim($_POST['full_name'] ?? ''));
        $email = trim($_POST['email'] ?? '');
        $mobile = trim($_POST['mobile'] ?? '');

        // Check existing active application
        $checkStmt = $pdo->prepare("SELECT id FROM admissions WHERE user_id = ? AND course_id = ? AND approval_status != 'completed'");
        $checkStmt->execute([$_SESSION['user_id'] ?? 0, (int)$_POST['course_id']]);
        if ($checkStmt->fetch()) throw new Exception("You already have an active application for this course.");

        $username = trim($_POST['username'] ?? $mobile);
        $password = $_POST['password'] ?? str_replace('-', '', $_POST['dob'] ?? '123456');

        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR (email != '' AND email = ?)");
        $stmt->execute([$username, $email]);
        $existing_user = $stmt->fetch();
        if ($existing_user) {
            $user_id = $existing_user['id'];
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, status) VALUES (?, ?, ?, 'student', 1)");
            $stmt->execute([$username, $email, $hashed_password]);
            $user_id = $pdo->lastInsertId();
        }
    } elseif ($action === 'edit') {
        $id = (int)$_POST['id'];
        $user_id = (int)$_POST['user_id'];
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $stmt = $pdo->prepare("SELECT id FROM users WHERE (username = ? OR (email != '' AND email = ?)) AND id != ?");
        $stmt->execute([$username, $email, $user_id]);
        if ($stmt->fetch()) throw new Exception("Username or email already exists.");

        if ($password) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET username=?, email=?, password=? WHERE id=?");
            $stmt->execute([$username, $email, $hashed_password, $user_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET username=?, email=? WHERE id=?");
            $stmt->execute([$username, $email, $user_id]);
        }
    }

    // 2. Prepare Admission Data
    $full_name = strtoupper(trim($_POST['full_name']));
    $father_name = strtoupper(trim($_POST['father_name']));
    $mother_name = strtoupper(trim($_POST['mother_name'] ?? ''));
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $mobile = trim($_POST['mobile']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $state_id = (int)$_POST['state_id'];
    $district_id = (int)$_POST['district_id'];
    $city_id = (int)$_POST['city_id'];
    $pincode = trim($_POST['pincode']);
    $center_id = (int)$_POST['center_id'];
    $course_id = (int)$_POST['course_id'];
    $session_id = (int)$_POST['session_id'];
    $admission_date = $_POST['admission_date'] ?? date('Y-m-d');
    $religion = trim($_POST['religion'] ?? '');
    $caste = trim($_POST['caste'] ?? '');
    $blood_group = trim($_POST['blood_group'] ?? '');
    $qualification = trim($_POST['qualification'] ?? '');
    $guardian_phone = trim($_POST['guardian_phone'] ?? '');

    // Handle File Uploads
    $upload_dir = "../media/students/";
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
    function upload($file, $pfx, $dir) {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) return "";
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $name = $pfx . "_" . time() . "_" . rand(100,999) . "." . $ext;
        move_uploaded_file($file['tmp_name'], $dir . $name);
        return $name;
    }
    $photo = upload($_FILES['student_photo'] ?? null, 'IMG', $upload_dir);
    $sig = upload($_FILES['signature'] ?? null, 'SIG', $upload_dir);
    $id_proof = upload($_FILES['id_proof'] ?? null, 'DOC', $upload_dir);
    $guardian_doc = upload($_FILES['guardian_doc'] ?? null, 'GDOC', $upload_dir);
    
    $aadhar_front = upload($_FILES['aadhar_front'] ?? null, 'AF', $upload_dir);
    $aadhar_back = upload($_FILES['aadhar_back'] ?? null, 'AB', $upload_dir);
    $m10 = upload($_FILES['marksheet_10th'] ?? null, 'M10', $upload_dir);
    $m12 = upload($_FILES['marksheet_12th'] ?? null, 'M12', $upload_dir);
    $p_af = upload($_FILES['parent_aadhar_front'] ?? null, 'PAF', $upload_dir);
    $p_ab = upload($_FILES['parent_aadhar_back'] ?? null, 'PAB', $upload_dir);

    if ($action === 'add') {
        $status = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') ? 'approved' : 'pending';
        $source = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') ? 'manual' : 'online';

        $sql = "INSERT INTO admissions (
            user_id, center_id, course_id, full_name, father_name, mother_name, 
            dob, gender, mobile, email, address, state_id, district_id, city_id, 
            pincode, photo, signature, id_proof, session_id, admission_date, approval_status,
            religion, caste, blood_group, qualification, guardian_phone, guardian_doc, country_id,
            aadhar_front, aadhar_back, marksheet_10th, marksheet_12th, parent_aadhar_front, parent_aadhar_back,
            source
        ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,'$status',?,?,?,?,?,?,?,?,?,?,?,?,?,?,'$source')";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $user_id, $center_id, $course_id, $full_name, $father_name, $mother_name,
            $dob, $gender, $mobile, $email, $address, $state_id, $district_id, $city_id,
            $pincode, $photo, $sig, $id_proof, $session_id, $admission_date,
            $religion, $caste, $blood_group, $qualification, $guardian_phone, $guardian_doc,
            (int)($_POST['country_id'] ?? 1),
            $aadhar_front, $aadhar_back, $m10, $m12, $p_af, $p_ab
        ]);
        $admission_id = $pdo->lastInsertId();
        
        if ($status === 'approved') {
            $year = date('Y', strtotime($admission_date));
            $roll = $year . str_pad($admission_id, 6, '0', STR_PAD_LEFT);
            $pdo->prepare("UPDATE admissions SET roll_number = ? WHERE id = ?")->execute([$roll, $admission_id]);
        }
        $msg = ($status === 'approved') ? 'Student admitted successfully!' : 'Application submitted successfully!';
    } elseif ($action === 'edit') {
        $id = (int)$_POST['id'];
        $st = $pdo->prepare("SELECT photo, signature, id_proof, guardian_doc, aadhar_front, aadhar_back, marksheet_10th, marksheet_12th, parent_aadhar_front, parent_aadhar_back FROM admissions WHERE id=?");
        $st->execute([$id]);
        $old = $st->fetch();
        
        $f_photo = $photo ?: $old['photo'];
        $f_sig = $sig ?: $old['signature'];
        $f_id = $id_proof ?: $old['id_proof'];
        $f_gdoc = $guardian_doc ?: $old['guardian_doc'];
        
        $f_af = $aadhar_front ?: $old['aadhar_front'];
        $f_ab = $aadhar_back ?: $old['aadhar_back'];
        $f_m10 = $m10 ?: $old['marksheet_10th'];
        $f_m12 = $m12 ?: $old['marksheet_12th'];
        $f_paf = $p_af ?: $old['parent_aadhar_front'];
        $f_pab = $p_ab ?: $old['parent_aadhar_back'];

        $status = $_POST['approval_status'] ?? 'pending';

        $sql = "UPDATE admissions SET 
            center_id=?, course_id=?, full_name=?, father_name=?, mother_name=?, 
            dob=?, gender=?, mobile=?, email=?, address=?, state_id=?, district_id=?, city_id=?, 
            pincode=?, photo=?, signature=?, id_proof=?, guardian_doc=?, admission_date=?, approval_status=?,
            religion=?, caste=?, blood_group=?, qualification=?, guardian_phone=?, session_id=?, country_id=?,
            aadhar_front=?, aadhar_back=?, marksheet_10th=?, marksheet_12th=?, parent_aadhar_front=?, parent_aadhar_back=?
            WHERE id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $center_id, $course_id, $full_name, $father_name, $mother_name,
            $dob, $gender, $mobile, $email, $address, $state_id, $district_id, $city_id,
            $pincode, $f_photo, $f_sig, $f_id, $f_gdoc, $admission_date, $status,
            $religion, $caste, $blood_group, $qualification, $guardian_phone, $session_id, (int)($_POST['country_id'] ?? 1),
            $f_af, $f_ab, $f_m10, $f_m12, $f_paf, $f_pab,
            $id
        ]);

        if ($status === 'approved') {
            $check = $pdo->prepare("SELECT roll_number FROM admissions WHERE id=?");
            $check->execute([$id]);
            if (!$check->fetchColumn()) {
                $year = date('Y', strtotime($admission_date));
                $roll = $year . str_pad($id, 6, '0', STR_PAD_LEFT);
                $pdo->prepare("UPDATE admissions SET roll_number=? WHERE id=?")->execute([$roll, $id]);
            }
        }
        $msg = 'Record updated successfully!';
    }

    $pdo->commit();
    echo json_encode(['status' => 'success', 'message' => $msg]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
