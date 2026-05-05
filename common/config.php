<?php
require_once(__DIR__ . "/session.php");
// Define base URL dynamically to work on both local and live servers automatically
if (!defined('BASE_URL')) {
    $doc_root = str_replace('\\', '/', rtrim($_SERVER['DOCUMENT_ROOT'], '/'));
    $proj_root = str_replace('\\', '/', dirname(dirname(__FILE__)));
    $base_path = str_ireplace($doc_root, '', $proj_root) . '/';
    define('BASE_URL', $base_path);
    $BASE_URL = BASE_URL;
}

// Include Database Connection
require_once(__DIR__ . "/db.php");

// Fetch Global Site Settings
$SITE_SETTINGS = [];
try {
    $setStmt = $pdo->query("SELECT setting_key, setting_value FROM site_settings WHERE status = 1");
    $SITE_SETTINGS = $setStmt->fetchAll(PDO::FETCH_KEY_PAIR);
} catch (PDOException $e) { /* silent fail if table not exists yet */ }

function getSetting($key, $default = '') {
    global $SITE_SETTINGS;
    return $SITE_SETTINGS[$key] ?? $default;
}

// Helper to get section content
function getSec($pdo, $key, $field='content') {
    $stmt = $pdo->prepare("SELECT $field FROM frontend_sections WHERE section_key = ? AND status = 1 LIMIT 1");
    $stmt->execute([$key]);
    return $stmt->fetchColumn() ?: '';
}

?>

