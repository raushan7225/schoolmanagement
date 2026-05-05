<?php
// admin/includes/config.php

// Find the relative path to the root directory
$root_path = dirname(__DIR__, 2);
include_once($root_path . "/common/config.php");

// Admin Specific Constants
if (!defined('ADMIN_BASE_URL')) {
    define('ADMIN_BASE_URL', $BASE_URL . 'admin/');
}

// Global Admin Protection (Exclude login/register/recovery pages)
$current_page = basename($_SERVER['PHP_SELF']);
$excluded_pages = ['login.php', 'register.php', 'forgot-password.php', 'reset-password.php'];

if (!in_array($current_page, $excluded_pages)) {
    checkRole('admin');
}

// Fetch Global Settings for use in templates
$all_settings = $pdo->query("SELECT setting_key, setting_value FROM site_settings WHERE status = 1")->fetchAll(PDO::FETCH_KEY_PAIR);

/**
 * Global helper to get setting value with fallback
 */
if (!function_exists('v')) {
    function v($key, $default = '') {
        global $all_settings;
        return htmlspecialchars($all_settings[$key] ?? $default);
    }
}
?>
