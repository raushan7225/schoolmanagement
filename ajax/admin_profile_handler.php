<?php
/**
 * ajax/admin_profile_handler.php
 * Handles Admin: Update Profile & Change Password
 */
require_once('../common/config.php');
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit();
}

$action = trim($_POST['action'] ?? '');
$user_id = (int)$_SESSION['user_id'];

try {
    if ($action === 'update_profile') {
        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email'] ?? '');

        // Server-side validation
        if (empty($username)) {
            echo json_encode(['success' => false, 'message' => 'Username is required.']);
            exit();
        }
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
            exit();
        }

        // Check uniqueness (excluding self)
        $dup = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $dup->execute([$username, $user_id]);
        if ($dup->fetch()) {
            echo json_encode(['success' => false, 'message' => 'This username is already taken.']);
            exit();
        }

        $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
        $stmt->execute([$username, $email, $user_id]);

        // Update session
        $_SESSION['username'] = $username;

        echo json_encode(['success' => true, 'message' => 'Profile updated successfully!']);

    } elseif ($action === 'change_password') {
        $current_password  = $_POST['current_password'] ?? '';
        $new_password      = $_POST['new_password'] ?? '';
        $confirm_password  = $_POST['confirm_password'] ?? '';

        // Server-side validation
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            echo json_encode(['success' => false, 'message' => 'All password fields are required.']);
            exit();
        }
        if (strlen($new_password) < 8) {
            echo json_encode(['success' => false, 'message' => 'New password must be at least 8 characters.']);
            exit();
        }
        if ($new_password !== $confirm_password) {
            echo json_encode(['success' => false, 'message' => 'New password and confirmation do not match.']);
            exit();
        }

        // Fetch current hashed password
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($current_password, $user['password'])) {
            echo json_encode(['success' => false, 'message' => 'Current password is incorrect.']);
            exit();
        }

        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashed, $user_id]);

        echo json_encode(['success' => true, 'message' => 'Password changed successfully! Please log in again.']);

    } else {
        echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'DB Error: ' . $e->getMessage()]);
}
?>
