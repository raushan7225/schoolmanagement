<?php
/**
 * ajax/accounting_handler.php
 * Handles Student Accounting CRUD (Fee Types, Groups, Allocations, etc.)
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

    case 'save_fee_type':
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $fee_code = trim($_POST['fee_code'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $status = (int)($_POST['status'] ?? 1);

        if (!$name) {
            echo json_encode(['success' => false, 'message' => 'Fee Type Name is required.']);
            exit();
        }

        try {
            if ($id === 0) {
                $stmt = $pdo->prepare("INSERT INTO fee_types (name, fee_code, description, status) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $fee_code, $description, $status]);
            } else {
                $pdo->prepare("UPDATE fee_types SET name=?, fee_code=?, description=?, status=? WHERE id=?")
                    ->execute([$name, $fee_code, $description, $status, $id]);
            }
            echo json_encode(['success' => true, 'message' => 'Fee type saved.']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'delete_fee_type':
        $id = (int)($_POST['id'] ?? 0);
        try {
            $pdo->prepare("DELETE FROM fee_types WHERE id = ?")->execute([$id]);
            echo json_encode(['success' => true, 'message' => 'Fee type deleted.']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'save_fee_group':
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $status = (int)($_POST['status'] ?? 1);
        $items = $_POST['items'] ?? []; // Array of fee_type_id => amount

        if (!$name) {
            echo json_encode(['success' => false, 'message' => 'Fee Group Name is required.']);
            exit();
        }

        try {
            $pdo->beginTransaction();
            if ($id === 0) {
                $stmt = $pdo->prepare("INSERT INTO fee_groups (name, description, status) VALUES (?, ?, ?)");
                $stmt->execute([$name, $description, $status]);
                $id = $pdo->lastInsertId();
            } else {
                $pdo->prepare("UPDATE fee_groups SET name=?, description=?, status=? WHERE id=?")
                    ->execute([$name, $description, $status, $id]);
                // Clear existing items
                $pdo->prepare("DELETE FROM fee_group_items WHERE fee_group_id = ?")->execute([$id]);
            }

            // Insert new items
            $stmt = $pdo->prepare("INSERT INTO fee_group_items (fee_group_id, fee_type_id, amount) VALUES (?, ?, ?)");
            foreach ($items as $type_id => $amount) {
                if ($amount > 0) {
                    $stmt->execute([$id, $type_id, $amount]);
                }
            }
            $pdo->commit();
            echo json_encode(['success' => true, 'message' => 'Fee group saved.']);
        } catch (PDOException $e) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'delete_fee_group':
        $id = (int)($_POST['id'] ?? 0);
        try {
            $pdo->beginTransaction();
            $pdo->prepare("DELETE FROM fee_group_items WHERE fee_group_id = ?")->execute([$id]);
            $pdo->prepare("DELETE FROM fee_groups WHERE id = ?")->execute([$id]);
            $pdo->commit();
            echo json_encode(['success' => true, 'message' => 'Fee group deleted.']);
        } catch (PDOException $e) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'allocate_fees':
        $fee_group_id = (int)($_POST['fee_group_id'] ?? 0);
        $student_ids = $_POST['student_ids'] ?? []; // Array of admission IDs

        if (!$fee_group_id || empty($student_ids)) {
            echo json_encode(['success' => false, 'message' => 'Fee Group and Students are required.']);
            exit();
        }

        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("INSERT INTO fee_allocations (admission_id, fee_group_id) VALUES (?, ?)");
            foreach ($student_ids as $sid) {
                // Check if already allocated this group
                $check = $pdo->prepare("SELECT id FROM fee_allocations WHERE admission_id = ? AND fee_group_id = ?");
                $check->execute([$sid, $fee_group_id]);
                if (!$check->fetch()) {
                    $stmt->execute([$sid, $fee_group_id]);
                }
            }
            $pdo->commit();
            echo json_encode(['success' => true, 'message' => 'Fees allocated successfully.']);
        } catch (PDOException $e) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'get_student_ledger':
        $search = trim($_POST['search'] ?? '');
        if (!$search) {
            echo json_encode(['success' => false, 'message' => 'Search term required.']);
            exit();
        }

        try {
            // Find Student
            $stmt = $pdo->prepare("
                SELECT a.*, c.name as course_name, cnt.name as center_name, cnt.code as center_code
                FROM admissions a
                LEFT JOIN courses c ON a.course_id = c.id
                LEFT JOIN centers cnt ON a.center_id = cnt.id
                WHERE a.roll_number = ? OR a.mobile = ?
                LIMIT 1
            ");
            $stmt->execute([$search, $search]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$student) {
                echo json_encode(['success' => false, 'message' => 'Student not found.']);
                exit();
            }

            // Get Allocations with Details
            $stmt = $pdo->prepare("
                SELECT fa.id as allocation_id, fg.name as group_name, ft.name as type_name, fgi.amount as total_amount, fgi.fee_type_id,
                (SELECT SUM(amount_paid) FROM fee_collections WHERE admission_id = a.id AND fee_allocation_id = fa.id AND fee_type_id = ft.id) as paid_amount
                FROM fee_allocations fa
                JOIN fee_groups fg ON fa.fee_group_id = fg.id
                JOIN fee_group_items fgi ON fg.id = fgi.fee_group_id
                JOIN fee_types ft ON fgi.fee_type_id = ft.id
                JOIN admissions a ON fa.admission_id = a.id
                WHERE a.id = ?
            ");
            $stmt->execute([$student['id']]);
            $ledger = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get Recent Transactions
            $stmt = $pdo->prepare("SELECT * FROM fee_collections WHERE admission_id = ? ORDER BY payment_date DESC LIMIT 5");
            $stmt->execute([$student['id']]);
            $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true,
                'student' => $student,
                'ledger' => $ledger,
                'transactions' => $transactions
            ]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'collect_payment':
        $admission_id = (int)($_POST['admission_id'] ?? 0);
        $allocation_id = (int)($_POST['allocation_id'] ?? 0);
        $fee_type_id = (int)($_POST['fee_type_id'] ?? 0);
        $amount = (float)($_POST['amount'] ?? 0);
        $mode = $_POST['payment_mode'] ?? 'cash';
        $txn_id = trim($_POST['transaction_id'] ?? '');
        $date = $_POST['payment_date'] ?? date('Y-m-d');

        if (!$admission_id || $amount <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid payment data.']);
            exit();
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO fee_collections (admission_id, fee_allocation_id, fee_type_id, amount_paid, payment_mode, transaction_id, payment_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$admission_id, $allocation_id, $fee_type_id, $amount, $mode, $txn_id, $date]);
            echo json_encode(['success' => true, 'message' => 'Payment collected successfully.']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action.']);
}
