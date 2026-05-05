<?php
require_once('../common/config.php');

$type = $_GET['type'] ?? '';

try {
    if ($type === 'countries') {
        $stmt = $pdo->query("SELECT id, name FROM countries WHERE status = 1 ORDER BY name ASC");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
    elseif ($type === 'states') {
        $country_id = $_GET['country_id'] ?? 1; // Default to India if not provided
        $stmt = $pdo->prepare("SELECT id, name FROM states WHERE country_id = ? AND status = 1 ORDER BY name ASC");
        $stmt->execute([$country_id]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    } 
    elseif ($type === 'districts') {
        $state_id = $_GET['state_id'] ?? '';
        $stmt = $pdo->prepare("SELECT id, name FROM districts WHERE state_id = ? AND status = 1 ORDER BY name ASC");
        $stmt->execute([$state_id]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    } 
    elseif ($type === 'cities') {
        $district_id = $_GET['district_id'] ?? '';
        $stmt = $pdo->prepare("SELECT id, name FROM cities WHERE district_id = ? AND status = 1 ORDER BY name ASC");
        $stmt->execute([$district_id]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
    elseif ($type === 'center_states') {
        // First try to get states that have franchises
        $stmt = $pdo->query("SELECT DISTINCT s.name FROM franchises f JOIN states s ON f.state_id = s.id WHERE f.status = 1 ORDER BY s.name ASC");
        $states = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Fallback: If no states found with active franchises, return all states for India (country_id=1)
        if (empty($states)) {
            $stmt = $pdo->query("SELECT name FROM states WHERE country_id = 1 AND status = 1 ORDER BY name ASC");
            $states = $stmt->fetchAll(PDO::FETCH_COLUMN);
        }
        echo json_encode($states);
    }
    elseif ($type === 'center_districts') {
        $state_name = $_GET['state'] ?? '';
        // Try to get districts that have franchises in this state
        $stmt = $pdo->prepare("SELECT DISTINCT d.name FROM franchises f JOIN districts d ON f.district_id = d.id JOIN states s ON f.state_id = s.id WHERE s.name = ? AND f.status = 1 ORDER BY d.name ASC");
        $stmt->execute([$state_name]);
        $districts = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Fallback: If no districts found with franchises, return all districts for this state
        if (empty($districts)) {
            $stmt = $pdo->prepare("SELECT d.name FROM districts d JOIN states s ON d.state_id = s.id WHERE s.name = ? AND d.status = 1 ORDER BY d.name ASC");
            $stmt->execute([$state_name]);
            $districts = $stmt->fetchAll(PDO::FETCH_COLUMN);
        }
        echo json_encode($districts);
    }
    elseif ($type === 'centers') {
        $district_name = $_GET['district_name'] ?? '';
        if ($district_name) {
            $stmt = $pdo->prepare("SELECT f.id, f.center_name as name FROM franchises f JOIN districts d ON f.district_id = d.id WHERE d.name = ? AND f.status = 1 ORDER BY f.center_name ASC");
            $stmt->execute([$district_name]);
        } else {
            $stmt = $pdo->query("SELECT id, center_name as name FROM franchises WHERE status = 1 ORDER BY center_name ASC");
        }
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
