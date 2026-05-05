<?php
require_once('../common/config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Unauthorized");
}

$id = $_GET['id'] ?? null;
if ($id) {
    // Soft delete: set status to 0
    $stmt = $pdo->prepare("UPDATE admissions SET status = 0 WHERE id = ?");
    $stmt->execute([$id]);
    
    // Also deactivate the user account
    $stmt = $pdo->prepare("UPDATE users u JOIN admissions a ON u.id = a.user_id SET u.status = 0 WHERE a.id = ?");
    $stmt->execute([$id]);
}
header("Location: student-list.php");
exit();
?>
