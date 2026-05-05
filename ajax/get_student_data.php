<?php
/**
 * ajax/get_student_data.php
 * Fetches comprehensive profile data for the student dashboard
 */
require_once('../common/config.php');
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    // 1. Fetch Admission & Course Data
    $stmt = $pdo->prepare("
        SELECT a.*, c.name as course_name, f.center_name, f.center_code, gc.name as city_name 
        FROM admissions a 
        LEFT JOIN courses c ON a.course_id = c.id 
        LEFT JOIN franchises f ON a.center_id = f.id 
        LEFT JOIN cities gc ON a.city_id = gc.id
        WHERE a.user_id = ?
        ORDER BY a.id DESC LIMIT 1
    ");
    $stmt->execute([$user_id]);
    $admission = $stmt->fetch(PDO::FETCH_ASSOC);

    $enquiries = [];
    $exams = [];
    $classes = [];
    $attendance = [];
    $marks = [];
    $issued_docs = [];

    if ($admission) {
        $course_id = $admission['course_id'];
        $student_id = $admission['id'];

        // 2. Fetch Online Exams for this Course
        $exStmt = $pdo->prepare("
            SELECT e.* FROM online_exams e 
            WHERE e.course_id = ? AND e.status = 1 
            ORDER BY e.start_datetime ASC
        ");
        $exStmt->execute([$course_id]);
        $exams = $exStmt->fetchAll(PDO::FETCH_ASSOC);

        // 3. Fetch Online Classes for this Course
        $clStmt = $pdo->prepare("
            SELECT cl.* FROM online_classes cl 
            WHERE cl.course_id = ? AND cl.status = 1 
            ORDER BY cl.class_date DESC
        ");
        $clStmt->execute([$course_id]);
        $classes = $clStmt->fetchAll(PDO::FETCH_ASSOC);

        // 4. Fetch Attendance History
        $atStmt = $pdo->prepare("
            SELECT a.*, f.center_name 
            FROM qr_attendance a 
            LEFT JOIN franchises f ON a.franchise_id = f.id
            WHERE a.student_id = ? 
            ORDER BY a.check_in_time DESC 
            LIMIT 50
        ");
        $atStmt->execute([$student_id]);
        $attendance = $atStmt->fetchAll(PDO::FETCH_ASSOC);

        // 4b. Fetch Marks
        $mkStmt = $pdo->prepare("SELECT * FROM marks WHERE admission_id = ? AND status = 1");
        $mkStmt->execute([$student_id]);
        $marks = $mkStmt->fetchAll(PDO::FETCH_ASSOC);

        // 4c. Fetch Issued Documents (Admit Card, ID Card, Certificate)
        $docStmt = $pdo->prepare("SELECT * FROM issued_documents WHERE admission_id = ? AND status = 1");
        $docStmt->execute([$student_id]);
        $issued_docs = $docStmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 5. Fetch Enquiry History (Include those matching by User ID, Email, or Mobile)
    $userEmail = $admission['email'] ?? '';
    $userMobile = $admission['mobile'] ?? '';
    
    // Also check the users table if admission info is sparse
    if (!$userEmail || !$userMobile) {
        $uStmt = $pdo->prepare("SELECT email, username FROM users WHERE id = ?");
        $uStmt->execute([$user_id]);
        $uData = $uStmt->fetch();
        if (!$userEmail) $userEmail = $uData['email'] ?? '';
        // Often username is mobile in this system
        if (!$userMobile && is_numeric($uData['username'])) $userMobile = $uData['username'];
    }

    $enqStmt = $pdo->prepare("
        SELECT e.*, c.name as course_name 
        FROM enquiries e 
        LEFT JOIN courses c ON e.course_id = c.id 
        WHERE e.user_id = ? 
        OR (e.email != '' AND e.email = ?) 
        OR (e.mobile != '' AND e.mobile = ?)
        ORDER BY e.created_at DESC
    ");
    $enqStmt->execute([$user_id, $userEmail, $userMobile]);
    $enquiries = $enqStmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'success', 
        'admission' => $admission ?: null,
        'enquiries' => $enquiries ?: [],
        'exams' => $exams,
        'classes' => $classes,
        'attendance' => $attendance,
        'marks' => $marks,
        'issued_docs' => $issued_docs
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
