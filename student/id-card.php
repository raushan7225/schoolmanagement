<?php
require_once('../common/config.php');
checkRole('student');

$user_id = $_SESSION['user_id'];

// Fetch student details
$stmt = $pdo->prepare("
    SELECT a.*, c.name as course_name, f.center_name, f.center_code, f.center_address
    FROM admissions a 
    LEFT JOIN courses c ON a.course_id = c.id 
    LEFT JOIN franchises f ON a.center_id = f.id 
    WHERE a.user_id = ? AND a.approval_status = 'approved'
    ORDER BY a.id DESC LIMIT 1
");
$stmt->execute([$user_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    die("<div style='text-align:center; padding:50px; font-family:sans-serif;'>
            <h3>ID Card Not Available</h3>
            <p>Your admission must be 'Approved' to view your ID card.</p>
            <a href='index.php'>Return to Dashboard</a>
         </div>");
}

$photo = $student['photo'] ? "../media/students/".$student['photo'] : "../assets/img/default-avatar.png";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student ID Card - <?php echo $student['roll_number']; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #1b1260;
            --secondary: #ff6a1a;
        }
        body {
            background: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .id-card-container {
            perspective: 1000px;
            width: 500px;
        }
        .id-card {
            width: 100%;
            height: 300px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
            overflow: hidden;
            position: relative;
            display: flex;
            flex-direction: column;
            border: 1px solid #ddd;
        }
        .card-header {
            background: linear-gradient(135deg, var(--primary), #2c2185);
            color: white;
            padding: 15px;
            display: flex;
            align-items: center;
            gap: 15px;
            border-bottom: 4px solid var(--secondary);
        }
        .logo {
            width: 45px;
            height: 45px;
            background: white;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: var(--primary);
            font-size: 20px;
        }
        .header-text h2 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header-text p {
            margin: 2px 0 0;
            font-size: 10px;
            opacity: 0.8;
            letter-spacing: 0.5px;
        }
        .card-body {
            flex: 1;
            display: flex;
            padding: 15px;
            gap: 20px;
            background: url('../assets/img/watermark.png') no-repeat center;
            background-size: 60%;
        }
        .student-photo-box {
            width: 110px;
            height: 130px;
            border: 3px solid var(--primary);
            border-radius: 8px;
            overflow: hidden;
            background: #f8f9fa;
        }
        .student-photo-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .student-info {
            flex: 1;
        }
        .info-row {
            margin-bottom: 8px;
        }
        .info-label {
            font-size: 9px;
            color: #777;
            text-transform: uppercase;
            font-weight: bold;
            display: block;
        }
        .info-value {
            font-size: 13px;
            color: #333;
            font-weight: 700;
        }
        .card-footer {
            background: #f8f9fa;
            padding: 10px 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #eee;
        }
        .qr-code {
            width: 60px;
            height: 60px;
            background: white;
            padding: 2px;
            border: 1px solid #ddd;
        }
        .qr-code img {
            width: 100%;
            height: 100%;
        }
        .signature-box {
            text-align: right;
        }
        .signature-img {
            max-height: 35px;
            display: block;
            margin-bottom: 2px;
        }
        .sig-label {
            font-size: 8px;
            color: #777;
            text-transform: uppercase;
            font-weight: bold;
            border-top: 1px solid #333;
            padding-top: 2px;
            display: inline-block;
        }
        .controls {
            position: fixed;
            bottom: 30px;
            display: flex;
            gap: 15px;
        }
        .btn {
            padding: 12px 25px;
            border-radius: 50px;
            border: none;
            cursor: pointer;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: 0.3s;
            text-decoration: none;
        }
        .btn-primary { background: var(--primary); color: white; box-shadow: 0 10px 20px rgba(27, 18, 96, 0.2); }
        .btn-light { background: white; color: #333; box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
        .btn:hover { transform: translateY(-3px); }

        @media print {
            .controls { display: none; }
            body { background: white; }
            .id-card { box-shadow: none; border: 1px solid #eee; -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body>

    <div class="id-card-container">
        <div class="id-card" id="cardToPrint">
            <div class="card-header">
                <div class="logo">NEB</div>
                <div class="header-text">
                    <h2>NEBOOSASE INDIA</h2>
                    <p>National Educational Board of Open Schooling & Skill Education</p>
                </div>
            </div>
            
            <div class="card-body">
                <div class="student-photo-box">
                    <img src="<?php echo $photo; ?>" alt="Student Photo">
                </div>
                <div class="student-info">
                    <div class="info-row">
                        <span class="info-label">Student Name</span>
                        <span class="info-value"><?php echo $student['full_name']; ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Roll Number</span>
                        <span class="info-value" style="color:var(--secondary);"><?php echo $student['roll_number']; ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Course Name</span>
                        <span class="info-value"><?php echo $student['course_name']; ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Valid Upto</span>
                        <span class="info-value"><?php echo date('M Y', strtotime('+1 year')); ?></span>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <div class="center-info">
                    <span class="info-label">Authorized Center</span>
                    <span class="info-value" style="font-size: 11px;"><?php echo $student['center_name']; ?></span>
                </div>
                <div class="qr-code">
                    <!-- Simple QR Placeholder using Google Charts API -->
                    <img src="https://chart.googleapis.com/chart?chs=100x100&cht=qr&chl=<?php echo urlencode($student['roll_number']); ?>&choe=UTF-8" alt="QR Code">
                </div>
                <div class="signature-box">
                    <?php if($student['signature']): ?>
                        <img src="../media/students/<?php echo $student['signature']; ?>" class="signature-img" alt="Signature">
                    <?php endif; ?>
                    <span class="sig-label">Auth. Signatory</span>
                </div>
            </div>
        </div>
    </div>

    <div class="controls">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i> Print ID Card
        </button>
        <a href="index.php" class="btn btn-light">
            <i class="fas fa-arrow-left"></i> Back to Portal
        </a>
    </div>

</body>
</html>
