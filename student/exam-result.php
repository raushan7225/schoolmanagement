<?php
/**
 * student/exam-result.php
 * Displays student performance after CBT submission
 */
require_once('../common/config.php');
checkRole('student');

$result_id = (int)($_GET['id'] ?? 0);
if (!$result_id) { header("Location: index.php"); exit(); }

// Fetch result details
$stmt = $pdo->prepare("
    SELECT r.*, e.title as exam_title, e.pass_percentage, c.course_name 
    FROM online_exam_results r 
    JOIN online_exams e ON r.exam_id = e.id 
    JOIN courses c ON e.course_id = c.id 
    WHERE r.id = ?
");
$stmt->execute([$result_id]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$result) { die("Result record not found."); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Exam Result: <?php echo htmlspecialchars($result['exam_title']); ?></title>
    <?php include("../common/meta.php"); ?>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <link id="dynamic-theme-css" rel="stylesheet" href="<?php echo BASE_URL; ?>theme.php">
    <style>
        .result-card { max-width: 600px; margin: 80px auto; }
        .score-circle { width: 150px; height: 150px; border-radius: 50%; border: 10px solid #eee; margin: 0 auto; display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: bold; }
        .score-circle.pass { border-color: #198754; color: #198754; }
        .score-circle.fail { border-color: #dc3545; color: #dc3545; }
    </style>
</head>
<body class="bg-light">
    <?php include("../common/header.php"); ?>

    <div class="container">
        <div class="card border-0 shadow rounded-4 p-5 text-center result-card">
            <h5 class="text-muted text-uppercase small ls-1 mb-2">Performance Summary</h5>
            <h2 class="fw-bold mb-4"><?php echo htmlspecialchars($result['exam_title']); ?></h2>
            
            <div class="score-circle mb-4 <?php echo $result['result_status']; ?>">
                <?php echo round($result['percentage']); ?>%
            </div>

            <h3 class="fw-bold <?php echo $result['result_status'] == 'pass' ? 'text-success' : 'text-danger'; ?> mb-4">
                RESULT: <?php echo strtoupper($result['result_status']); ?>
            </h3>

            <div class="row g-3 mb-5">
                <div class="col-6">
                    <div class="p-3 border rounded-3 bg-light">
                        <small class="text-muted d-block">Correct Answers</small>
                        <strong class="fs-5"><?php echo $result['correct_answers']; ?> / <?php echo $result['total_questions']; ?></strong>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-3 border rounded-3 bg-light">
                        <small class="text-muted d-block">Marks Obtained</small>
                        <strong class="fs-5"><?php echo $result['obtained_marks']; ?></strong>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2">
                <a href="index.php" class="btn btn-primary-theme btn-lg rounded-pill">Back to Dashboard</a>
                <button onclick="window.print()" class="btn btn-outline-secondary rounded-pill">Print Scorecard</button>
            </div>
        </div>
    </div>

    <?php include("../common/footer.php"); ?>
</body>
</html>
