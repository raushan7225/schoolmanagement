<?php
require_once('common/config.php');

// Unset all session variables
$_SESSION = array();

// Destroy the session cookie if it exists
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Determine redirect destination
$redirect = (isset($_GET['redirect']) && $_GET['redirect'] === 'home') ? 'index.php' : 'login.php';

// Redirect to destination
header("Location: " . $redirect);
exit();
?>
