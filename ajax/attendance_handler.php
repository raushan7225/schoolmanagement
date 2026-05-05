<?php
/**
 * ajax/attendance_handler.php
 * Handles QR Attendance marking
 */
require_once(__DIR__ . '/../common/config.php');
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'franchise'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {

    case 'mark_attendance':
        $reg_no = trim($_POST['reg_no'] ?? '');
        if (!$reg_no) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid QR Code data.']);
            exit();
        }

        try {
            // Find student
            $stmt = $pdo->prepare("SELECT id, full_name as student_name, center_id as franchise_id FROM admissions WHERE roll_number = ? LIMIT 1");
            $stmt->execute([$reg_no]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$student) {
                echo json_encode(['status' => 'error', 'message' => 'Student not found.']);
                exit();
            }

            // Check if already marked today in student_attendance
            $today = date('Y-m-d');
            $check = $pdo->prepare("SELECT id FROM student_attendance WHERE admission_id = ? AND attendance_date = ?");
            $check->execute([$student['id'], $today]);
            if ($check->fetch()) {
                echo json_encode(['status' => 'error', 'message' => 'Attendance already marked for today.']);
                exit();
            }

            $pdo->beginTransaction();

            // 1. Log detailed QR scan
            $insQr = $pdo->prepare("INSERT INTO qr_attendance (student_id, franchise_id, check_in_time, status) VALUES (?, ?, NOW(), 'present')");
            $insQr->execute([$student['id'], $student['franchise_id']]);

            // 2. Mark in main attendance table
            $insMain = $pdo->prepare("INSERT INTO student_attendance (admission_id, center_id, attendance_date, status, remarks) VALUES (?, ?, ?, 'present', 'QR Scan')");
            $insMain->execute([$student['id'], $student['franchise_id'], $today]);

            $pdo->commit();

            echo json_encode([
                'status' => 'success', 
                'message' => 'Attendance marked for ' . $student['student_name']
            ]);

        } catch (Exception $e) { 
            if($pdo->inTransaction()) $pdo->rollBack();
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]); 
        }
        break;

    case 'save_attendance':
        $att_date = $_POST['att_date'] ?? date('Y-m-d');
        $attendance = $_POST['attendance'] ?? []; // [student_id => status]
        $remarks = $_POST['remarks'] ?? []; // [student_id => remark]

        if (empty($attendance)) {
            echo json_encode(['success' => false, 'message' => 'No attendance data received.']);
            exit();
        }

        try {
            $pdo->beginTransaction();
            
            foreach ($attendance as $student_id => $status) {
                $remark = trim($remarks[$student_id] ?? '');
                
                // Check if already exists for this student and date
                $check = $pdo->prepare("SELECT id FROM student_attendance WHERE admission_id = ? AND attendance_date = ?");
                $check->execute([$student_id, $att_date]);
                $existing = $check->fetch();

                if ($existing) {
                    $upd = $pdo->prepare("UPDATE student_attendance SET status = ?, remarks = ? WHERE id = ?");
                    $upd->execute([$status, $remark, $existing['id']]);
                } else {
                    // Get center_id for this student
                    $getCenter = $pdo->prepare("SELECT center_id FROM admissions WHERE id = ?");
                    $getCenter->execute([$student_id]);
                    $center = $getCenter->fetchColumn();

                    $ins = $pdo->prepare("INSERT INTO student_attendance (admission_id, center_id, attendance_date, status, remarks) VALUES (?, ?, ?, ?, ?)");
                    $ins->execute([$student_id, $center, $att_date, $status, $remark]);
                }
            }

            $pdo->commit();
            echo json_encode(['status' => 'success', 'message' => 'Attendance saved successfully.']);
        } catch (Exception $e) {
            $pdo->rollBack();
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action.']);
}
