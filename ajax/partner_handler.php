<?php
/**
 * ajax/partner_handler.php
 * Handles Partner CRUD and Wallet Adjustments
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

    case 'get':
        $id = (int)($_GET['id'] ?? 0);
        $stmt = $pdo->prepare("SELECT * FROM partners WHERE id = ?");
        $stmt->execute([$id]);
        $partner = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($partner) {
            echo json_encode(['success' => true, 'data' => $partner]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Partner not found.']);
        }
        break;

    case 'get_txn':
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $pdo->prepare("
            SELECT l.*, p.full_name as partner_name 
            FROM partner_wallet_ledger l 
            JOIN partners p ON l.partner_id = p.id 
            WHERE l.id = ?
        ");
        $stmt->execute([$id]);
        $txn = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($txn) {
            echo json_encode(['success' => true, 'data' => $txn]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Transaction not found.']);
        }
        break;

    case 'add':
    case 'edit':
        $id = (int)($_POST['id'] ?? 0);
        $full_name = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $status = (int)($_POST['status'] ?? 1);

        if (!$full_name || !$email || !$phone) {
            echo json_encode(['success' => false, 'message' => 'Required fields missing.']);
            exit();
        }

        // Image Handling
        $image_name = '';
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
            $ext = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
            $image_name = 'partner_' . time() . '.' . $ext;
            $upload_path = '../media/partners/' . $image_name;
            
            if (!is_dir('../media/partners/')) {
                mkdir('../media/partners/', 0777, true);
            }
            
            move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_path);
        }

        try {
            $pdo->beginTransaction();

            if ($action === 'add') {
                // Check if user exists
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$email]);
                if ($stmt->fetch()) {
                    throw new Exception("User with this email already exists.");
                }

                // 1. Create User
                $hashed_password = password_hash($password ?: '12345678', PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, status) VALUES (?, ?, ?, 'partner', ?)");
                $stmt->execute([$full_name, $email, $hashed_password, $status]);
                $user_id = $pdo->lastInsertId();

                // 2. Create Partner
                $stmt = $pdo->prepare("INSERT INTO partners (user_id, full_name, email, phone, profile_image, status) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$user_id, $full_name, $email, $phone, $image_name, $status]);
            } else {
                // Edit
                $stmt = $pdo->prepare("SELECT user_id FROM partners WHERE id = ?");
                $stmt->execute([$id]);
                $user_id = $stmt->fetchColumn();

                // Update User
                if ($password) {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, password = ?, status = ? WHERE id = ?");
                    $stmt->execute([$full_name, $email, $hashed_password, $status, $user_id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, status = ? WHERE id = ?");
                    $stmt->execute([$full_name, $email, $status, $user_id]);
                }

                // Update Partner
                if ($image_name) {
                    $stmt = $pdo->prepare("UPDATE partners SET full_name = ?, email = ?, phone = ?, profile_image = ?, status = ? WHERE id = ?");
                    $stmt->execute([$full_name, $email, $phone, $image_name, $status, $id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE partners SET full_name = ?, email = ?, phone = ?, status = ? WHERE id = ?");
                    $stmt->execute([$full_name, $email, $phone, $status, $id]);
                }
            }

            $pdo->commit();
            echo json_encode(['success' => true, 'message' => 'Partner account ' . ($action === 'add' ? 'created' : 'updated') . ' successfully!']);
        } catch (Exception $e) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'adjust_wallet':
        $id = (int)($_POST['partner_id'] ?? 0);
        $amount = (float)($_POST['amount'] ?? 0);
        $type = $_POST['type'] ?? '';
        $desc = trim($_POST['description'] ?? 'Manual adjustment');

        if (!$id || $amount <= 0 || !in_array($type, ['credit', 'debit'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid adjustment data.']);
            exit();
        }

        try {
            $pdo->beginTransaction();

            // 1. Log Ledger
            $stmt = $pdo->prepare("INSERT INTO partner_wallet_ledger (partner_id, amount, type, description) VALUES (?, ?, ?, ?)");
            $stmt->execute([$id, $amount, $type, $desc]);

            // 2. Update Balance
            $sign = ($type === 'credit') ? '+' : '-';
            $stmt = $pdo->prepare("UPDATE partners SET wallet_balance = wallet_balance $sign ? WHERE id = ?");
            $stmt->execute([$amount, $id]);

            $pdo->commit();
            echo json_encode(['success' => true, 'message' => 'Wallet adjusted successfully!']);
        } catch (Exception $e) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'delete':
        $id = (int)($_POST['id'] ?? 0);
        
        try {
            $pdo->beginTransaction();
            
            // Get user_id first
            $stmt = $pdo->prepare("SELECT user_id FROM partners WHERE id = ?");
            $stmt->execute([$id]);
            $user_id = $stmt->fetchColumn();
            
            // Soft delete partner
            $stmt = $pdo->prepare("UPDATE partners SET status = -1 WHERE id = ?");
            $stmt->execute([$id]);
            
            // Soft delete user
            if ($user_id) {
                $stmt = $pdo->prepare("UPDATE users SET status = -1 WHERE id = ?");
                $stmt->execute([$user_id]);
            }
            
            $pdo->commit();
            echo json_encode(['success' => true, 'message' => 'Partner and associated user account deleted.']);
        } catch (Exception $e) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action.']);
}
