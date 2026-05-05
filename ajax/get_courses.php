<?php
require_once('../common/config.php');

$type = $_GET['type'] ?? '';

try {
    if ($type === 'categories') {
        // Get unique categories from ENUM or existing data
        $stmt = $pdo->query("SELECT DISTINCT category FROM courses WHERE status = 1 ORDER BY category ASC");
        echo json_encode($stmt->fetchAll(PDO::FETCH_COLUMN));
    } 
    elseif ($type === 'courses') {
        if (!empty($_GET['category_id'])) {
            $category_id = $_GET['category_id'];
            $stmt = $pdo->prepare("SELECT id, name, duration_months, eligibility FROM courses WHERE category_id = ? AND status = 1 ORDER BY name ASC");
            $stmt->execute([$category_id]);
        } else {
            $category = $_GET['category'] ?? '';
            $stmt = $pdo->prepare("SELECT id, name, duration_months, eligibility FROM courses WHERE category = ? AND status = 1 ORDER BY name ASC");
            $stmt->execute([$category]);
        }
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
