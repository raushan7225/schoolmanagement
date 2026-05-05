<?php
/**
 * ajax/user_handler.php
 * Handles CRUD and status operations for users table
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
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role_input = $_POST['role'] ?? '';

        if (!$username || !$email || !$password || !$role_input) {
            echo json_encode(['success' => false, 'message' => 'All fields are required.']);
            exit();
        }

        $role = $role_input;
        $role_id = null;

        // Check if this is a custom admin role
        if (strpos($role_input, 'admin_') === 0) {
            $role = 'admin';
            $role_id = (int)str_replace('admin_', '', $role_input);
        }

        // Check if user exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $stmt->execute([$email, $username]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'User with this email or username already exists.']);
            exit();
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, role_id, status) VALUES (?, ?, ?, ?, ?, 1)");
        $stmt->execute([$username, $email, $hashed_password, $role, $role_id]);

        echo json_encode(['success' => true, 'message' => 'User account created successfully!']);
        break;

    case 'toggle_status':
        $id = (int)($_POST['id'] ?? 0);
        $status = (int)($_POST['status'] ?? 0);
        
        if (!$id) { echo json_encode(['success' => false, 'message' => 'Invalid ID.']); exit(); }
        
        $stmt = $pdo->prepare("UPDATE users SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
        
        echo json_encode(['success' => true, 'message' => 'Status updated.']);
        break;

    case 'delete':
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) { echo json_encode(['success' => false, 'message' => 'Invalid ID.']); exit(); }
        
        // Prevent deleting self
        if ($id == $_SESSION['user_id']) {
            echo json_encode(['success' => false, 'message' => 'You cannot delete your own account.']);
            exit();
        }

        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        
        echo json_encode(['success' => true, 'message' => 'User deleted successfully.']);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action.']);
}
