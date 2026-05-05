<?php
require_once('../common/config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Unauthorized");
}

$id = $_GET['id'] ?? null;
if ($id) {
    // Check if roll number already exists
    $check = $pdo->prepare("SELECT roll_number, admission_date FROM admissions WHERE id = ?");
    $check->execute([$id]);
    $row = $check->fetch();

    if ($row) {
        $status_update = "UPDATE admissions SET approval_status = 'approved'";
        $params = [$id];
        
        if (empty($row['roll_number'])) {
            $year = date('Y', strtotime($row['admission_date'] ?: 'now'));
            $roll_no = $year . sprintf("%06d", $id);
            $status_update .= ", roll_number = ?";
            array_unshift($params, $roll_no);
        }
        
        $status_update .= " WHERE id = ?";
        $pdo->prepare($status_update)->execute($params);
    }
}
header("Location: online-admission-list.php");
exit();
?>
