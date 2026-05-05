<?php
// common/session.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is logged in, otherwise redirect to login page
 */
function checkLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: " . BASE_URL . "login.php");
        exit();
    }
}

/**
 * Check if user has a specific role
 */
function checkRole($role) {
    checkLogin();
    if ($_SESSION['role'] !== $role) {
        header("Location: " . BASE_URL . "login.php?error=unauthorized");
        exit();
    }
}
?>
