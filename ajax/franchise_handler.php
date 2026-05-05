<?php
/**
 * ajax/franchise_handler.php
 * Handles wallet adjustments and center management
 */
require_once('../common/config.php');
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// --- MIGRATION BLOCK (ENSURE TABLES) ---
$pdo->exec("CREATE TABLE IF NOT EXISTS support_tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    franchise_id INT NOT NULL,
    subject VARCHAR(255) NOT NULL,
    department VARCHAR(100),
    message TEXT NOT NULL,
    status ENUM('open', 'in-progress', 'resolved', 'closed') DEFAULT 'open',
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)");

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {

    case 'add_student_lead':
        try {
            $stmt = $pdo->prepare("SELECT id FROM franchises WHERE user_id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $fid = $stmt->fetchColumn();
            if (!$fid) throw new Exception("Franchise not found.");

            $name = trim($_POST['director_name'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $course = trim($_POST['course'] ?? '');

            if (!$name || !$phone) throw new Exception("Name and Mobile are required.");

            $stmt = $pdo->prepare("INSERT INTO enquiries (center_id, full_name, mobile, message, source, approval_status, status) VALUES (?, ?, ?, ?, 'manual', 'new', 1)");
            $stmt->execute([$fid, $name, $phone, "Interested in: " . $course]);

            echo json_encode(['success' => true, 'message' => 'Lead saved successfully!']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'open_support_ticket':
        try {
            $stmt = $pdo->prepare("SELECT id FROM franchises WHERE user_id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $fid = $stmt->fetchColumn();
            if (!$fid) throw new Exception("Franchise not found.");

            $subject = trim($_POST['subject'] ?? '');
            $dept = trim($_POST['department'] ?? '');
            $msg = trim($_POST['message'] ?? '');

            if (!$subject || !$msg) throw new Exception("Subject and Message are required.");

            $stmt = $pdo->prepare("INSERT INTO support_tickets (franchise_id, subject, department, message) VALUES (?, ?, ?, ?)");
            $stmt->execute([$fid, $subject, $dept, $msg]);

            echo json_encode(['success' => true, 'message' => 'Ticket opened successfully! Reference: #' . $pdo->lastInsertId()]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'get_student_detail':
        try {
            $sid = (int)($_GET['id'] ?? 0);
            if (!$sid) throw new Exception("Invalid Student ID.");

            // Fetch student with course and franchise check
            $stmt = $pdo->prepare("
                SELECT a.*, c.name as course_name, f.center_name, f.center_code 
                FROM admissions a 
                JOIN courses c ON a.course_id = c.id 
                JOIN franchises f ON a.center_id = f.id 
                WHERE a.id = ?
            ");
            $stmt->execute([$sid]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$student) throw new Exception("Student not found.");

            // Basic check: is this franchise authorized to view this student?
            $stmt_f = $pdo->prepare("SELECT id FROM franchises WHERE user_id = ?");
            $stmt_f->execute([$_SESSION['user_id']]);
            $fid = $stmt_f->fetchColumn();
            
            if ($_SESSION['role'] !== 'admin') {
                if (!$fid || $student['center_id'] != $fid) {
                    throw new Exception("Unauthorized access to this student profile.");
                }
            }

            echo json_encode(['success' => true, 'data' => $student]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'upload_center_doc':
        try {
            $stmt = $pdo->prepare("SELECT id FROM franchises WHERE user_id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $fid = $stmt->fetchColumn();
            if (!$fid) throw new Exception("Franchise profile not found.");

            $title = trim($_POST['doc_title'] ?? '');
            $type = trim($_POST['doc_type'] ?? 'Other');

            if (!$title || !isset($_FILES['doc_file'])) throw new Exception("Title and File are required.");

            $uploadDir = "../media/franchise/documents/";
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            $ext = pathinfo($_FILES['doc_file']['name'], PATHINFO_EXTENSION);
            $fname = "CENTER_DOC_" . time() . "_" . rand(100,999) . "." . $ext;
            
            if (move_uploaded_file($_FILES['doc_file']['tmp_name'], $uploadDir . $fname)) {
                $stmt = $pdo->prepare("INSERT INTO franchise_documents (franchise_id, title, type, file_name) VALUES (?, ?, ?, ?)");
                $stmt->execute([$fid, $title, $type, $fname]);
                echo json_encode(['success' => true, 'message' => 'Document uploaded successfully!']);
            } else {
                throw new Exception("Failed to upload file.");
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'get':
        if ($_SESSION['role'] !== 'admin') { echo json_encode(['success' => false, 'message' => 'Admin access required']); exit(); }
        $id = (int)($_GET['id'] ?? 0);
        $stmt = $pdo->prepare("
            SELECT f.*, s.name as state_name, d.name as district_name, c.name as city_name, u.full_name as partner_full_name
            FROM franchises f 
            LEFT JOIN states s ON f.state_id = s.id 
            LEFT JOIN districts d ON f.district_id = d.id 
            LEFT JOIN cities c ON f.city_id = c.id
            LEFT JOIN partners u ON f.partner_id = u.id
            WHERE f.id = ?
        ");
        $stmt->execute([$id]);
        $franchise = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($franchise) {
            echo json_encode(['success' => true, 'data' => $franchise]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Franchise not found.']);
        }
        break;

    case 'add_franchise':
        if ($_SESSION['role'] !== 'admin') { echo json_encode(['success' => false, 'message' => 'Admin access required']); exit(); }
        $pdo->beginTransaction();
        try {
            $center_name = trim($_POST['center_name'] ?? '');
            $center_email = trim($_POST['center_email'] ?? '');
            $password = $_POST['password'] ?? '';
            
            // Check email uniqueness in users
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$center_email]);
            if ($stmt->fetch()) throw new Exception("Email already registered with another account.");

            // 1. Create User
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, status) VALUES (?, ?, ?, 'franchise', ?)");
            $stmt->execute([$center_email, $center_email, password_hash($password, PASSWORD_DEFAULT), (int)($_POST['status'] ?? 1)]);
            $user_id = $pdo->lastInsertId();

            // 2. Handle File Uploads
            $upload_map = [
                'director_photo' => 'media/franchise/directors/',
                'id_proof'       => 'media/franchise/documents/',
                'aadhar_front'   => 'media/franchise/documents/',
                'aadhar_back'    => 'media/franchise/documents/',
                'approval_doc'   => 'media/franchise/documents/',
                'signature'      => 'media/franchise/documents/',
                'photo_front'    => 'media/franchise/centers/',
                'photo_lab'      => 'media/franchise/centers/',
                'photo_office'   => 'media/franchise/centers/'
            ];
            $saved_files = [];
            foreach ($upload_map as $key => $dir) {
                if (isset($_FILES[$key]) && $_FILES[$key]['error'] === UPLOAD_ERR_OK) {
                    if (!is_dir("../" . $dir)) mkdir("../" . $dir, 0777, true);
                    $ext = pathinfo($_FILES[$key]['name'], PATHINFO_EXTENSION);
                    $fname = strtoupper($key) . "_" . time() . "_" . rand(100,999) . "." . $ext;
                    move_uploaded_file($_FILES[$key]['tmp_name'], "../" . $dir . $fname);
                    $saved_files[$key] = $fname;
                } else {
                    $saved_files[$key] = "";
                }
            }

            // 3. Create Franchise Profile
            $sql = "INSERT INTO franchises (
                user_id, partner_id, center_code, center_name, director_name, director_mobile, aadhar_no, id_proof, aadhar_front, aadhar_back, approval_doc, director_photo, signature,
                phone, phone_alt, email, qualification, estd_date, address, state_id, district_id, city_id, pincode, 
                computers, teachers, rooms, area_sqft, internet_type, photo_front, photo_lab, photo_office, status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $user_id,
                !empty($_POST['partner_id']) ? $_POST['partner_id'] : null,
                $_POST['center_code'] ?? '',
                $center_name,
                $_POST['director_name'] ?? '',
                $_POST['director_mobile'] ?? '',
                $_POST['aadhar_no'] ?? '',
                $saved_files['id_proof'],
                $saved_files['aadhar_front'],
                $saved_files['aadhar_back'],
                $saved_files['approval_doc'],
                $saved_files['director_photo'],
                $saved_files['signature'],
                $_POST['center_phone'] ?? '',
                $_POST['phone_alt'] ?? '',
                $center_email,
                $_POST['qualification'] ?? '',
                $_POST['estd_date'] ?? null,
                $_POST['address'] ?? '',
                $_POST['state_id'] ?? null,
                $_POST['district_id'] ?? null,
                $_POST['city_id'] ?? null,
                $_POST['pincode'] ?? '',
                (int)($_POST['computers'] ?? 0),
                (int)($_POST['teachers'] ?? 0),
                (int)($_POST['rooms'] ?? 0),
                (int)($_POST['area_sqft'] ?? 0),
                $_POST['internet_type'] ?? '',
                $saved_files['photo_front'],
                $saved_files['photo_lab'],
                $saved_files['photo_office'],
                (int)($_POST['status'] ?? 1)
            ]);

            $pdo->commit();
            echo json_encode(['success' => true, 'message' => 'Franchise registered successfully! User account created.', 'id' => $pdo->lastInsertId()]);
        } catch (Exception $e) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'edit_franchise':
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) { echo json_encode(['success' => false, 'message' => 'Invalid ID']); exit; }

        $center_name = trim($_POST['center_name'] ?? '');
        $center_email = trim($_POST['center_email'] ?? '');

        if (!$center_name || !$center_email) {
            echo json_encode(['success' => false, 'message' => 'Center name and email are required.']);
            exit;
        }

        // 1. Fetch current data to preserve files
        $curr = $pdo->prepare("SELECT * FROM franchises WHERE id = ?");
        $curr->execute([$id]);
        $existing = $curr->fetch(PDO::FETCH_ASSOC);
        if (!$existing) { echo json_encode(['success' => false, 'message' => 'Franchise not found']); exit; }

        // 2. Handle File Uploads
        $upload_map = [
            'director_photo' => 'media/franchise/directors/',
            'id_proof'       => 'media/franchise/documents/',
            'aadhar_front'   => 'media/franchise/documents/',
            'aadhar_back'    => 'media/franchise/documents/',
            'approval_doc'   => 'media/franchise/documents/',
            'signature'      => 'media/franchise/documents/',
            'photo_front'    => 'media/franchise/centers/',
            'photo_lab'      => 'media/franchise/centers/',
            'photo_office'   => 'media/franchise/centers/'
        ];

        $saved_files = [];
        foreach ($upload_map as $key => $dir) {
            if (isset($_FILES[$key]) && $_FILES[$key]['error'] === UPLOAD_ERR_OK) {
                if (!is_dir("../" . $dir)) mkdir("../" . $dir, 0777, true);
                $ext = pathinfo($_FILES[$key]['name'], PATHINFO_EXTENSION);
                $fname = strtoupper($key) . "_" . time() . "_" . rand(100, 999) . "." . $ext;
                move_uploaded_file($_FILES[$key]['tmp_name'], "../" . $dir . $fname);
                $saved_files[$key] = $fname;
            } else {
                $saved_files[$key] = $existing[$key] ?? "";
            }
        }

        // 3. Update Profile
        $sql = "UPDATE franchises SET 
                partner_id = ?, center_name = ?, director_name = ?, director_mobile = ?, aadhar_no = ?, 
                id_proof = ?, aadhar_front = ?, aadhar_back = ?, approval_doc = ?, director_photo = ?, signature = ?,
                phone = ?, phone_alt = ?, email = ?, qualification = ?, estd_date = ?, address = ?, 
                state_id = ?, district_id = ?, city_id = ?, pincode = ?, 
                computers = ?, teachers = ?, rooms = ?, area_sqft = ?, internet_type = ?, 
                photo_front = ?, photo_lab = ?, photo_office = ?, status = ?
                WHERE id = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            !empty($_POST['partner_id']) ? $_POST['partner_id'] : null,
            $center_name,
            $_POST['director_name'] ?? '',
            $_POST['director_mobile'] ?? '',
            $_POST['aadhar_no'] ?? '',
            $saved_files['id_proof'],
            $saved_files['aadhar_front'],
            $saved_files['aadhar_back'],
            $saved_files['approval_doc'],
            $saved_files['director_photo'],
            $saved_files['signature'],
            $_POST['center_phone'] ?? '',
            $_POST['phone_alt'] ?? '',
            $center_email,
            $_POST['qualification'] ?? '',
            $_POST['estd_date'] ?? null,
            $_POST['address'] ?? '',
            $_POST['state_id'] ?? null,
            $_POST['district_id'] ?? null,
            $_POST['city_id'] ?? null,
            $_POST['pincode'] ?? '',
            (int)($_POST['computers'] ?? 0),
            (int)($_POST['teachers'] ?? 0),
            (int)($_POST['rooms'] ?? 0),
            (int)($_POST['area_sqft'] ?? 0),
            $_POST['internet_type'] ?? '',
            $saved_files['photo_front'],
            $saved_files['photo_lab'],
            $saved_files['photo_office'],
            (int)($_POST['status'] ?? 1),
            $id
        ]);

        echo json_encode(['success' => true, 'message' => 'Franchise updated successfully!']);
        break;

    case 'request_wallet_topup':
        try {
            // 1. Resolve franchise_id from user session
            $stmt = $pdo->prepare("SELECT id FROM franchises WHERE user_id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $fid = $stmt->fetchColumn();
            if (!$fid) throw new Exception("Franchise profile not found for this account.");

            $amount = (float)($_POST['amount'] ?? 0);
            $method = trim($_POST['payment_method'] ?? '');
            if ($amount <= 0) throw new Exception("Invalid amount.");

            // 2. Handle Proof Upload
            $proof_file = "";
            if (isset($_FILES['proof_file']) && $_FILES['proof_file']['error'] === UPLOAD_ERR_OK) {
                $dir = "../media/franchise/wallets/";
                if (!is_dir($dir)) mkdir($dir, 0777, true);
                
                $ext = pathinfo($_FILES['proof_file']['name'], PATHINFO_EXTENSION);
                $proof_file = "PROOF_" . time() . "_" . rand(100,999) . "." . $ext;
                move_uploaded_file($_FILES['proof_file']['tmp_name'], $dir . $proof_file);
            }

            // 3. Insert Request
            $stmt = $pdo->prepare("INSERT INTO franchise_wallet_requests (franchise_id, amount, payment_method, proof_file, status) VALUES (?, ?, ?, ?, 'pending')");
            $stmt->execute([$fid, $amount, $method, $proof_file]);

            echo json_encode(['success' => true, 'message' => 'Top-up request submitted successfully! Pending approval.']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'approve_wallet':
        if ($_SESSION['role'] !== 'admin') { echo json_encode(['success' => false, 'message' => 'Admin access required']); exit(); }
        $id = (int)($_POST['id'] ?? 0);
        $admin_remarks = trim($_POST['admin_remarks'] ?? '');
        
        try {
            $pdo->beginTransaction();

            // 1. Fetch request details
            $stmt = $pdo->prepare("SELECT * FROM franchise_wallet_requests WHERE id = ? AND status = 'pending'");
            $stmt->execute([$id]);
            $req = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$req) throw new Exception("Pending request not found.");

            // 2. Update request status
            $stmt = $pdo->prepare("UPDATE franchise_wallet_requests SET status = 'approved', admin_remarks = ? WHERE id = ?");
            $stmt->execute([$admin_remarks, $id]);

            // 3. Add to franchise wallet
            $stmt = $pdo->prepare("UPDATE franchises SET wallet_balance = wallet_balance + ? WHERE id = ?");
            $stmt->execute([$req['amount'], $req['franchise_id']]);

            // 4. Log in ledger
            $desc = "Wallet Top-up Approved (Req #$id). " . ($admin_remarks ? "Remarks: $admin_remarks" : "");
            $stmt = $pdo->prepare("INSERT INTO franchise_wallet_ledger (franchise_id, amount, type, description, status) VALUES (?, ?, 'credit', ?, 'success')");
            $stmt->execute([$req['franchise_id'], $req['amount'], $desc]);

            $pdo->commit();
            echo json_encode(['success' => true, 'message' => 'Wallet request approved and balance added!']);
        } catch (Exception $e) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'reject_wallet':
        if ($_SESSION['role'] !== 'admin') { echo json_encode(['success' => false, 'message' => 'Admin access required']); exit(); }
        $id = (int)($_POST['id'] ?? 0);
        $admin_remarks = trim($_POST['admin_remarks'] ?? '');
        if (!$admin_remarks) {
            echo json_encode(['success' => false, 'message' => 'Rejection reason is required.']);
            exit();
        }

        try {
            $stmt = $pdo->prepare("UPDATE franchise_wallet_requests SET status = 'rejected', admin_remarks = ? WHERE id = ? AND status = 'pending'");
            $stmt->execute([$admin_remarks, $id]);
            
            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => true, 'message' => 'Request rejected successfully.']);
            } else {
                throw new Exception("Request not found or already processed.");
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'upload_document':
        if ($_SESSION['role'] !== 'admin') { echo json_encode(['success' => false, 'message' => 'Admin access required']); exit(); }
        $fid = (int)($_POST['franchise_id'] ?? 0);
        $title = trim($_POST['doc_title'] ?? '');
        $type = $_POST['doc_type'] ?? 'Other';
        
        if (!$fid || !$title || !isset($_FILES['doc_file'])) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
            exit();
        }

        try {
            $dir = "../media/franchise/documents/";
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            
            $ext = pathinfo($_FILES['doc_file']['name'], PATHINFO_EXTENSION);
            $fname = "DOC_" . time() . "_" . rand(100,999) . "." . $ext;
            
            if (move_uploaded_file($_FILES['doc_file']['tmp_name'], $dir . $fname)) {
                $stmt = $pdo->prepare("INSERT INTO franchise_documents (franchise_id, title, type, file_name) VALUES (?, ?, ?, ?)");
                $stmt->execute([$fid, $title, $type, $fname]);
                echo json_encode(['success' => true, 'message' => 'Document uploaded successfully!']);
            } else {
                throw new Exception("Failed to move uploaded file.");
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'delete_document':
        if ($_SESSION['role'] !== 'admin') { echo json_encode(['success' => false, 'message' => 'Admin access required']); exit(); }
        $id = (int)($_POST['id'] ?? 0);
        try {
            // Optional: delete actual file from disk
            $stmt = $pdo->prepare("DELETE FROM franchise_documents WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true, 'message' => 'Document deleted.']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'send_notice':
        if ($_SESSION['role'] !== 'admin') { echo json_encode(['success' => false, 'message' => 'Admin access required']); exit(); }
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');
        
        if (!$subject || !$message) {
            echo json_encode(['success' => false, 'message' => 'Subject and Message are required.']);
            exit();
        }

        try {
            $attachment = "";
            if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
                $dir = "../media/franchise/notices/";
                if (!is_dir($dir)) mkdir($dir, 0777, true);
                
                $ext = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
                $attachment = "NOTICE_" . time() . "_" . rand(100,999) . "." . $ext;
                move_uploaded_file($_FILES['attachment']['tmp_name'], $dir . $attachment);
            }

            $stmt = $pdo->prepare("INSERT INTO franchise_notices (subject, message, attachment) VALUES (?, ?, ?)");
            $stmt->execute([$subject, $message, $attachment]);
            echo json_encode(['success' => true, 'message' => 'Notice broadcasted to all franchises!']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'delete_notice':
        if ($_SESSION['role'] !== 'admin') { echo json_encode(['success' => false, 'message' => 'Admin access required']); exit(); }
        $id = (int)($_POST['id'] ?? 0);
        try {
            $stmt = $pdo->prepare("DELETE FROM franchise_notices WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true, 'message' => 'Notice deleted.']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'adjust_wallet':
        if ($_SESSION['role'] !== 'admin') { echo json_encode(['success' => false, 'message' => 'Admin access required']); exit(); }
        $id = (int)($_POST['franchise_id'] ?? 0);
        $amount = (float)($_POST['amount'] ?? 0);
        $type = $_POST['type'] ?? ''; // credit or debit
        $desc = trim($_POST['description'] ?? 'Manual adjustment by admin');

        if (!$id || $amount <= 0 || !in_array($type, ['credit', 'debit'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid data.']);
            exit();
        }

        try {
            $pdo->beginTransaction();

            // 1. Log transaction
            $stmt = $pdo->prepare("INSERT INTO franchise_wallet_ledger (franchise_id, amount, type, description, status) VALUES (?, ?, ?, ?, 'success')");
            $stmt->execute([$id, $amount, $type, $desc]);

            // 2. Update franchise balance
            $sign = ($type === 'credit') ? '+' : '-';
            $stmt = $pdo->prepare("UPDATE franchises SET wallet_balance = wallet_balance $sign ? WHERE id = ?");
            $stmt->execute([$amount, $id]);

            $pdo->commit();
            echo json_encode(['success' => true, 'message' => 'Wallet adjusted successfully!']);
        } catch (Exception $e) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        break;

    case 'delete':
        if ($_SESSION['role'] !== 'admin') { echo json_encode(['success' => false, 'message' => 'Admin access required']); exit(); }
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) { echo json_encode(['success' => false, 'message' => 'Invalid ID.']); exit(); }
        
        $pdo->prepare("UPDATE franchises SET status = 0 WHERE id = ?")->execute([$id]);
        echo json_encode(['success' => true, 'message' => 'Center deleted successfully.']);
        break;

    case 'add_franchise_gateway':
        if ($_SESSION['role'] !== 'admin') { echo json_encode(['success' => false, 'message' => 'Admin access required']); exit(); }
        $franchise_id = (int)($_POST['franchise_id'] ?? 0);
        $provider = trim($_POST['gateway_provider'] ?? '');
        $status = (int)($_POST['status'] ?? 1);
        $key_id = trim($_POST['key_id'] ?? '');
        $secret_key = trim($_POST['secret_key'] ?? '');

        if (!$franchise_id || !$provider || !$key_id || !$secret_key) {
            echo json_encode(['success' => false, 'message' => 'All fields are required.']);
            exit();
        }

        try {
            // Check if gateway already exists for this franchise
            $stmt = $pdo->prepare("SELECT id FROM franchise_gateways WHERE franchise_id = ? AND gateway_provider = ?");
            $stmt->execute([$franchise_id, $provider]);
            if ($stmt->fetch()) {
                $stmt = $pdo->prepare("UPDATE franchise_gateways SET key_id = ?, secret_key = ?, status = ? WHERE franchise_id = ? AND gateway_provider = ?");
                $stmt->execute([$key_id, $secret_key, $status, $franchise_id, $provider]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO franchise_gateways (franchise_id, gateway_provider, key_id, secret_key, status) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$franchise_id, $provider, $key_id, $secret_key, $status]);
            }
            echo json_encode(['success' => true, 'message' => 'Gateway connection saved successfully!']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action.']);
}
