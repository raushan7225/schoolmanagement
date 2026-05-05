<?php
/**
 * ajax/office_handler.php
 * Handles Office Accounting CRUD (Voucher Heads, Deposits, Expenses)
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

    case 'save_voucher_head':
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $type = $_POST['type'] ?? 'expense';
        $description = trim($_POST['description'] ?? '');
        $status = (int)($_POST['status'] ?? 1);

        if (!$name) {
            echo json_encode(['success' => false, 'message' => 'Voucher Head Name is required.']);
            exit();
        }

        try {
            if ($id === 0) {
                $stmt = $pdo->prepare("INSERT INTO voucher_heads (name, type, description, status) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $type, $description, $status]);
            } else {
                $pdo->prepare("UPDATE voucher_heads SET name=?, type=?, description=?, status=? WHERE id=?")
                    ->execute([$name, $type, $description, $status, $id]);
            }
            echo json_encode(['success' => true, 'message' => 'Voucher head saved.']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'delete_voucher_head':
        $id = (int)($_POST['id'] ?? 0);
        try {
            // Check if linked to transactions
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM office_transactions WHERE voucher_head_id = ?");
            $stmt->execute([$id]);
            if ($stmt->fetchColumn() > 0) {
                throw new Exception("Cannot delete as it is linked to transactions.");
            }
            $pdo->prepare("DELETE FROM voucher_heads WHERE id = ?")->execute([$id]);
            echo json_encode(['success' => true, 'message' => 'Voucher head deleted.']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'save_transaction':
        $id = (int)($_POST['id'] ?? 0);
        $head_id = (int)($_POST['voucher_head_id'] ?? 0);
        $type = $_POST['type'] ?? 'expense';
        $amount = (float)($_POST['amount'] ?? 0);
        $mode = $_POST['payment_mode'] ?? 'Cash';
        $txn_id = trim($_POST['transaction_id'] ?? '');
        $date = $_POST['date'] ?? date('Y-m-d');
        $desc = trim($_POST['description'] ?? '');

        if (!$head_id || $amount <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid data.']);
            exit();
        }

        try {
            if ($id === 0) {
                $stmt = $pdo->prepare("INSERT INTO office_transactions (voucher_head_id, type, amount, payment_mode, transaction_id, date, description) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$head_id, $type, $amount, $mode, $txn_id, $date, $desc]);
            } else {
                $pdo->prepare("UPDATE office_transactions SET voucher_head_id=?, type=?, amount=?, payment_mode=?, transaction_id=?, date=?, description=? WHERE id=?")
                    ->execute([$head_id, $type, $amount, $mode, $txn_id, $date, $desc, $id]);
            }
            echo json_encode(['success' => true, 'message' => 'Transaction saved.']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'delete_transaction':
        $id = (int)($_POST['id'] ?? 0);
        try {
            $pdo->prepare("DELETE FROM office_transactions WHERE id = ?")->execute([$id]);
            echo json_encode(['success' => true, 'message' => 'Transaction deleted.']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action.']);
}
