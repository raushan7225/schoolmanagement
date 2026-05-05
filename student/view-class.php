<?php
/**
 * student/view-class.php
 * Video Player Interface for Recorded Online Classes
 */
require_once('../common/config.php');
checkRole('student');

$class_id = (int)($_GET['id'] ?? 0);
if (!$class_id) { header("Location: index.php"); exit(); }

// Fetch class details
$stmt = $pdo->prepare("SELECT cl.*, c.name as course_name FROM online_classes cl JOIN courses c ON cl.course_id = c.id WHERE cl.id = ? AND cl.status = 1");
$stmt->execute([$class_id]);
$class = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$class || $class['class_type'] !== 'recorded') { die("Class not found or is a live session."); }

// Extract YouTube ID if it's a URL
$video_id = $class['video_url'];
if (strpos($video_id, 'youtube.com') !== false || strpos($video_id, 'youtu.be') !== false) {
    preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $video_id, $match);
    $video_id = $match[1] ?? $video_id;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Watch: <?php echo htmlspecialchars($class['title']); ?></title>
    <?php include("../common/meta.php"); ?>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <link id="dynamic-theme-css" rel="stylesheet" href="<?php echo BASE_URL; ?>theme.php">
    <style>
        .video-container { position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .video-container iframe { position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0; }
    </style>
</head>
<body class="bg-light">
    <?php include("../common/header.php"); ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">View Class</li>
                    </ol>
                </nav>

                <div class="video-container mb-4 bg-black">
                    <iframe src="https://www.youtube.com/embed/<?php echo $video_id; ?>?rel=0&modestbranding=1" allowfullscreen></iframe>
                </div>

                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="fw-bold mb-0"><?php echo htmlspecialchars($class['title']); ?></h2>
                        <span class="badge bg-info-light text-info rounded-pill px-3">RECORDED SESSION</span>
                    </div>
                    <p class="text-muted mb-4"><?php echo nl2br(htmlspecialchars($class['description'] ?? 'No description provided.')); ?></p>
                    <hr>
                    <div class="d-flex align-items-center text-muted small">
                        <div class="me-4"><i class="fas fa-graduation-cap me-2 text-primary"></i> <?php echo htmlspecialchars($class['course_name']); ?></div>
                        <div><i class="far fa-calendar-alt me-2 text-primary"></i> Released: <?php echo date('d M Y', strtotime($class['class_date'])); ?></div>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="index.php" class="btn btn-outline-secondary rounded-pill px-4"><i class="fas fa-arrow-left me-2"></i>Back to Portal</a>
                </div>
            </div>
        </div>
    </div>

    <?php include("../common/footer.php"); ?>
</body>
</html>
