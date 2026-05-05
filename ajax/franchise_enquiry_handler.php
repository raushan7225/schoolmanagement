<?php
/**
 * ajax/franchise_enquiry_handler.php
 * Handles CRUD for franchise_enquiries table
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

    case 'add':
    case 'edit':
        $id = (int)($_POST['id'] ?? 0);
        $director_name = trim($_POST['director_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        
        if (!$director_name || !$phone) {
            echo json_encode(['success' => false, 'message' => 'Director name and phone are required.']);
            exit();
        }

        $data = [
            'director_name' => $director_name,
            'email' => trim($_POST['email'] ?? ''),
            'center_name' => trim($_POST['center_name'] ?? ''),
            'phone' => $phone,
            'phone_alt' => trim($_POST['phone_alt'] ?? ''),
            'qualification' => trim($_POST['qualification'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'state_id' => (int)$_POST['state_id'],
            'district_id' => (int)$_POST['district_id'],
            'city_id' => (int)$_POST['city_id'],
            'pincode' => trim($_POST['pincode'] ?? ''),
            'computers' => (int)$_POST['computers'],
            'teachers' => (int)$_POST['teachers'],
            'rooms' => (int)$_POST['rooms'],
            'area_sqft' => (int)$_POST['area_sqft'],
            'followup_date' => $_POST['followup_date'] ?: null,
            'estimate_fees' => (float)$_POST['estimate_fees'],
            'present_students' => (int)$_POST['present_students'],
            'comments' => trim($_POST['comments'] ?? ''),
            'approval_status' => $_POST['approval_status'] ?? 'new'
        ];

        if ($action === 'add') {
            $data['source'] = 'manual';
            $data['status'] = 1;
        }

        // Handle File Uploads
        $uploadDir = __DIR__ . "/../media/franchise/applications/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $files = [
            'dir_photo' => 'PHOTO',
            'dir_sig' => 'SIG',
            'aadhar_front' => 'AF',
            'aadhar_back' => 'AB',
            'labs_photo' => 'LAB',
            'approval_doc' => 'APPROV',
            'center_photo' => 'CENTER'
        ];

        foreach ($files as $key => $prefix) {
            if (isset($_FILES[$key]) && $_FILES[$key]['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES[$key]['name'], PATHINFO_EXTENSION);
                $newName = $prefix . "_" . time() . "_" . rand(1000, 9999) . "." . $ext;
                if (move_uploaded_file($_FILES[$key]['tmp_name'], $uploadDir . $newName)) {
                    $data[$key] = $newName;
                }
            }
        }

        if ($action === 'add') {
            $cols = implode(", ", array_keys($data));
            $placeholders = ":" . implode(", :", array_keys($data));
            $stmt = $pdo->prepare("INSERT INTO franchise_enquiries ($cols) VALUES ($placeholders)");
            $stmt->execute($data);
            $msg = "Franchise enquiry added successfully!";
        } else {
            $sets = [];
            foreach ($data as $key => $val) { $sets[] = "$key = :$key"; }
            $data['id'] = $id;
            $stmt = $pdo->prepare("UPDATE franchise_enquiries SET " . implode(", ", $sets) . " WHERE id = :id");
            $stmt->execute($data);
            $msg = "Franchise enquiry updated successfully!";
        }
        echo json_encode(['success' => true, 'message' => $msg]);
        break;

    case 'delete':
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) { echo json_encode(['success' => false, 'message' => 'Invalid ID.']); exit(); }
        $pdo->prepare("UPDATE franchise_enquiries SET status = 0 WHERE id = ?")->execute([$id]);
        echo json_encode(['success' => true, 'message' => 'Enquiry deleted successfully!']);
        break;

    case 'view':
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) { echo json_encode(['success' => false, 'message' => 'Invalid ID.']); exit(); }
        $stmt = $pdo->prepare("
            SELECT e.*, s.name as state_name, d.name as district_name, c.name as city_name 
            FROM franchise_enquiries e 
            LEFT JOIN states s ON e.state_id = s.id 
            LEFT JOIN districts d ON e.district_id = d.id 
            LEFT JOIN cities c ON e.city_id = c.id
            WHERE e.id = ?
        ");
        $stmt->execute([$id]);
        $lead = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($lead) echo json_encode(['success' => true, 'data' => $lead]);
        else echo json_encode(['success' => false, 'message' => 'Lead not found.']);
        break;

    case 'change_status':
        $id = (int)($_POST['id'] ?? 0);
        $status = $_POST['approval_status'] ?? '';
        if (!$id || !$status) { echo json_encode(['success' => false, 'message' => 'Invalid data.']); exit(); }
        $pdo->prepare("UPDATE franchise_enquiries SET approval_status = ? WHERE id = ?")->execute([$status, $id]);
        echo json_encode(['success' => true, 'message' => 'Status updated successfully!']);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action.']);
}
