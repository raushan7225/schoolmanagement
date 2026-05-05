<?php
/**
 * ajax/get_franchise_data.php
 * Fetches comprehensive data for the franchise dashboard
 */
require_once('../common/config.php');
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'franchise') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    // 1. Fetch Franchise / Center Data
    $stmt = $pdo->prepare("
        SELECT f.*, s.name as state_name, d.name as district_name, c.name as city_name 
        FROM franchises f 
        LEFT JOIN states s ON f.state_id = s.id 
        LEFT JOIN districts d ON f.district_id = d.id 
        LEFT JOIN cities c ON f.city_id = c.id
        WHERE f.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $franchise = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$franchise) {
        echo json_encode(['status' => 'error', 'message' => 'Franchise record not found.']);
        exit();
    }

    $franchise_id = $franchise['id'];

    // 2. Fetch Wallet Ledger
    $stmt = $pdo->prepare("SELECT * FROM franchise_wallet_ledger WHERE franchise_id = ? ORDER BY created_at DESC LIMIT 10");
    $stmt->execute([$franchise_id]);
    $ledger = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 3. Fetch Students List
    $stmt = $pdo->prepare("
        SELECT a.id, a.full_name, a.created_at, a.approval_status, c.name as course_name 
        FROM admissions a 
        LEFT JOIN courses c ON a.course_id = c.id
        WHERE a.center_id = ? 
        ORDER BY a.created_at DESC
    ");
    $stmt->execute([$franchise_id]);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 4. Fetch Student Leads (from enquiries table)
    $stmt = $pdo->prepare("
        SELECT e.*, c.name as course_name 
        FROM enquiries e 
        LEFT JOIN courses c ON e.course_id = c.id
        WHERE e.center_id = ? 
        ORDER BY e.created_at DESC
    ");
    $stmt->execute([$franchise_id]);
    $leads = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 5. Fetch Document Vault
    $stmt = $pdo->prepare("SELECT * FROM franchise_documents WHERE franchise_id = ? ORDER BY created_at DESC");
    $stmt->execute([$franchise_id]);
    $vault = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 6. Fetch Stats Summary
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total_students,
               SUM(CASE WHEN approval_status = 'approved' THEN 1 ELSE 0 END) as approved_students,
               SUM(CASE WHEN approval_status = 'pending' THEN 1 ELSE 0 END) as pending_students
        FROM admissions 
        WHERE center_id = ?
    ");
    $stmt->execute([$franchise_id]);
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);

    // 7. Fetch Notifications / Notices for Franchise
    $stmt = $pdo->prepare("SELECT * FROM franchise_notices ORDER BY created_at DESC LIMIT 5");
    $stmt->execute();
    $notices = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 8. Fetch Student Attendance Logs
    $stmt = $pdo->prepare("
        SELECT a.*, adm.full_name as student_name, adm.roll_number as reg_no 
        FROM qr_attendance a 
        JOIN admissions adm ON a.student_id = adm.id 
        WHERE a.franchise_id = ? 
        ORDER BY a.check_in_time DESC 
        LIMIT 50
    ");
    $stmt->execute([$franchise_id]);
    $attendance = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 9. Ensure Support Tickets table exists and fetch
    $pdo->exec("CREATE TABLE IF NOT EXISTS support_tickets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        franchise_id INT NOT NULL,
        subject VARCHAR(255) NOT NULL,
        department VARCHAR(100),
        message TEXT NOT NULL,
        status ENUM('open', 'in-progress', 'resolved', 'closed') DEFAULT 'open',
        priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    
    $stmt = $pdo->prepare("SELECT * FROM support_tickets WHERE franchise_id = ? ORDER BY created_at DESC");
    $stmt->execute([$franchise_id]);
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 10. Fetch Pending Wallet Requests
    $stmt = $pdo->prepare("SELECT * FROM franchise_wallet_requests WHERE franchise_id = ? AND status = 'pending' ORDER BY created_at DESC");
    $stmt->execute([$franchise_id]);
    $wallet_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 11. Fetch Courses and Sessions for Admission Tab
    $courses_list = $pdo->query("SELECT id, name, code FROM courses WHERE status = 'active' ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
    $sessions_list = $pdo->query("SELECT id, session_label as session_name, status as is_active FROM admission_sessions ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'success',
        'franchise' => $franchise,
        'ledger' => $ledger ?: [],
        'students' => $students ?: [],
        'leads' => $leads ?: [],
        'vault' => $vault ?: [],
        'stats' => $stats,
        'notices' => $notices ?: [],
        'attendance' => $attendance ?: [],
        'tickets' => $tickets ?: [],
        'wallet_requests' => $wallet_requests ?: [],
        'courses' => $courses_list,
        'sessions' => $sessions_list
    ]);

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>