<?php
/**
 * student/print-admission.php
 * A4 Perfect Printable Admission Form - Fixed Overlaps & Improved Block Header.
 */
require_once('../common/config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    die("Unauthorized access.");
}

$user_id = $_SESSION['user_id'];

// Fetch Admission & Course Data
$stmt = $pdo->prepare("
    SELECT a.*, c.name as course_name, c.duration_months, c.eligibility, f.center_name, f.center_code, 
           gs.name as state_name, gd.name as district_name, gc.name as city_name 
    FROM admissions a 
    LEFT JOIN courses c ON a.course_id = c.id 
    LEFT JOIN franchises f ON a.center_id = f.id 
    LEFT JOIN states gs ON a.state_id = gs.id
    LEFT JOIN districts gd ON a.district_id = gd.id
    LEFT JOIN cities gc ON a.city_id = gc.id
    WHERE a.user_id = ?
    ORDER BY a.id DESC LIMIT 1
");
$stmt->execute([$user_id]);
$data = $stmt->fetch();

if (!$data) {
    die("No admission record found.");
}

$primary_color = getSetting('primary_color', '#ff9800');
$site_name = getSetting('site_name', 'National Examination Board');
$logo = BASE_URL . getSetting('theme_logo', 'media/general/logo.jpeg');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admission_Form_<?php echo str_replace(' ', '_', $data['full_name'] ?? 'Student'); ?></title>
    <style>
        :root {
            --primary: <?php echo $primary_color; ?>;
            --text-dark: #111;
            --text-muted: #444;
            --border-color: #ddd;
        }
        @page {
            size: A4;
            margin: 0;
        }
        * { box-sizing: border-box; -webkit-print-color-adjust: exact; }
        body {
            font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f4f4f4;
            color: var(--text-dark);
        }
        .page-container {
            padding: 10mm 0;
        }
        .page {
            width: 210mm;
            height: 297mm;
            padding: 12mm 18mm;
            margin: 0 auto;
            background: white;
            position: relative;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        /* Decorative Corners */
        .corner-accent {
            position: absolute;
            top: 0;
            left: 0;
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, var(--primary) 50%, transparent 50%);
            z-index: 1;
        }
        .corner-accent-bottom {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 80px;
            height: 80px;
            background: linear-gradient(-45deg, var(--primary) 50%, transparent 50%);
            opacity: 0.15;
        }

        /* --- Header: Block Level Layout --- */
        .header-block {
            text-align: center;
            margin-bottom: 10px;
            position: relative;
            z-index: 10;
            padding-bottom: 10px;
            border-bottom: 3px double var(--primary);
        }
        .logo-main {
            height: 85px;
            width: auto;
            margin-bottom: 15px;
        }
        .board-name {
            margin: 0;
            font-size: 28px;
            color: var(--primary);
            text-transform: uppercase;
            font-weight: 900;
            letter-spacing: 1px;
            line-height: 1.2;
        }
        .tagline {
            margin: 5px 0 0;
            font-size: 13px;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* --- Info Section: Grid to prevent Overlaps --- */
        .top-info-grid {
            display: grid;
            grid-template-columns: 1fr 40mm; /* Space for photo on right */
            gap: 15px;
            margin-bottom: 15px;
        }

        .meta-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
            align-items: center;
        }
        .form-label-box {
            display: inline-block;
            background: var(--primary);
            color: white;
            padding: 6px 20px;
            border-radius: 4px;
            font-weight: 800;
            font-size: 16px;
            text-transform: uppercase;
            width: fit-content;
        }
        .meta-strip {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px 20px;
            font-size: 13px;
            background: #f9f9f9;
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #eee;
            width: 100%;
            text-align: center;
        }
        .meta-strip b { color: var(--primary); margin-right: 5px; }

        .photo-area {
            width: 35mm;
            height: 45mm;
            border: 1px solid var(--border-color);
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .photo-area img { width: 100%; height: 100%; object-fit: cover; }
        .photo-placeholder { font-size: 10px; color: #999; text-align: center; }

        /* --- Section Styling --- */
        .section-header {
            background: #f8f9fa;
            border-bottom: 2px solid var(--primary);
            padding: 8px;
            font-size: 15px;
            font-weight: 800;
            color: var(--text-dark);
            text-transform: uppercase;
            margin: 15px 0 8px 0;
            letter-spacing: 1px;
            text-align: center;
        }

        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px 40px;
            padding: 0 10px;
        }
        .detail-row {
            display: flex;
            align-items: baseline;
            gap: 10px;
            font-size: 13.5px;
            line-height: 1.4;
        }
        .detail-row.full { grid-column: span 2; }
        .lbl { min-width: 140px; font-weight: 700; color: #333; }
        .val {
            flex-grow: 1;
            border-bottom: 1px dotted #bbb;
            padding-bottom: 2px;
            color: #000;
            font-weight: 500;
        }

        .footer-area {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
            padding: 0 30px;
        }
        .sig-block { text-align: center; width: 190px; }
        .sig-line { border-top: 1px solid #000; margin-bottom: 8px; }
        .sig-text { font-size: 12px; font-weight: 700; color: #444; }

        /* --- Controls --- */
        .no-print-controls {
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            z-index: 1000;
        }
        .btn-ui {
            background: var(--primary);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }
        .btn-ui:hover { filter: brightness(1.1); }
        .btn-dark { background: #222; }

        @media print {
            body { background: white; }
            .page-container { padding: 0; }
            .page { margin: 0; box-shadow: none; border: none; }
            .no-print-controls { display: none !important; }
        }
    </style>
</head>
<body>

    <div class="no-print-controls">
        <a href="index.php" class="btn-ui btn-dark">
            <i class="fas fa-home"></i> Dashboard
        </a>
        <button class="btn-ui" onclick="window.print()">
            <i class="fas fa-print"></i> Print / Download PDF
        </button>
    </div>

    <div class="page-container">
        <div class="page">
            <div class="corner-accent"></div>
            <div class="corner-accent-bottom"></div>

            <!-- HEADER BLOCK -->
            <div class="header-block">
                <img src="<?php echo $logo; ?>" alt="Logo" class="logo-main" onerror="this.src='https://placehold.co/100x100?text=LOGO'">
                <h1 class="board-name"><?php echo strtoupper($site_name); ?></h1>
                <p class="tagline">An ISO Certified Educational Board | Established for Excellence</p>
            </div>

            <!-- TOP INFO GRID (Prevents Overlaps) -->
            <div class="top-info-grid">
                <div class="meta-container">
                    <div class="form-label-box">Admission Application Form</div>
                    <div class="meta-strip">
                        <div><b>Enrollment Date:</b> <?php echo date('d M, Y', strtotime($data['admission_date'])); ?></div>
                        <div><b>Session:</b> <?php echo $data['session_name'] ?: '2024-25'; ?></div>
                        <div><b>Roll Number:</b> <?php echo $data['roll_number'] ?: 'PENDING'; ?></div>
                        <div><b>Serial No:</b> #<?php echo str_pad($data['id'], 6, '0', STR_PAD_LEFT); ?></div>
                    </div>
                </div>
                <div class="photo-area">
                    <?php if ($data['photo']): ?>
                        <img src="<?php echo BASE_URL; ?>media/students/<?php echo $data['photo']; ?>" alt="Photo">
                    <?php else: ?>
                        <div class="photo-placeholder">STUDENT<br>PASSPORT PHOTO</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ACADEMIC INFO -->
            <div class="section-header">Academic Details</div>
            <div class="details-grid">
                <div class="detail-row">
                    <span class="lbl">Applied Course</span>: 
                    <span class="val"><?php echo $data['course_name']; ?></span>
                </div>
                <div class="detail-row">
                    <span class="lbl">Course Duration</span>: 
                    <span class="val"><?php echo ($data['duration_months'] ?? 'N/A') . ' Months'; ?></span>
                </div>
                <div class="detail-row">
                    <span class="lbl">Eligibility</span>: 
                    <span class="val"><?php echo $data['eligibility'] ?? 'N/A'; ?></span>
                </div>
                <div class="detail-row">
                    <span class="lbl">Study Center</span>: 
                    <span class="val"><?php echo $data['center_name']; ?></span>
                </div>
            </div>

            <!-- PERSONAL INFO -->
            <div class="section-header">Personal Information</div>
            <div class="details-grid">
                <div class="detail-row full">
                    <span class="lbl">Student Name</span>: 
                    <span class="val" style="font-weight: 800; font-size: 15px;"><?php echo strtoupper($data['full_name']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="lbl">Father's Name</span>: 
                    <span class="val"><?php echo strtoupper($data['father_name']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="lbl">Mother's Name</span>: 
                    <span class="val"><?php echo strtoupper($data['mother_name']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="lbl">Date of Birth</span>: 
                    <span class="val"><?php echo date('d-m-Y', strtotime($data['dob'])); ?></span>
                </div>
                <div class="detail-row">
                    <span class="lbl">Gender</span>: 
                    <span class="val"><?php echo ucfirst($data['gender']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="lbl">Religion</span>: 
                    <span class="val"><?php echo ucfirst($data['religion']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="lbl">Caste / Category</span>: 
                    <span class="val"><?php echo ucfirst($data['caste'] ?: 'General'); ?></span>
                </div>
                <div class="detail-row">
                    <span class="lbl">Qualification</span>: 
                    <span class="val"><?php echo $data['qualification']; ?></span>
                </div>
            </div>

            <!-- ADDRESS INFO -->
            <div class="section-header">Address & Contact</div>
            <div class="details-grid">
                <div class="detail-row full">
                    <span class="lbl">Contact Mobile</span>: 
                    <span class="val" style="font-weight: 700;"><?php echo $data['mobile']; ?></span>
                </div>
                <div class="detail-row full">
                    <span class="lbl">Full Address</span>: 
                    <span class="val"><?php echo $data['address']; ?></span>
                </div>
                <div class="detail-row">
                    <span class="lbl">District & State</span>: 
                    <span class="val"><?php echo $data['district_name'] . ', ' . $data['state_name']; ?></span>
                </div>
                <div class="detail-row">
                    <span class="lbl">Pin Code</span>: 
                    <span class="val"><?php echo $data['pincode']; ?></span>
                </div>
            </div>

            <!-- FOOTER SIGNATURES -->
            <div class="footer-area">
                <div class="sig-block">
                    <div class="sig-line"></div>
                    <div class="sig-text">Student Signature</div>
                </div>
                <div class="sig-block">
                    <div class="sig-line"></div>
                    <div class="sig-text">Authorized Signatory</div>
                </div>
            </div>

            <div style="position: absolute; bottom: 10mm; left: 0; width: 100%; text-align: center; font-size: 9px; color: #777;">
                Computer Generated Form | Printed on <?php echo date('d-m-Y H:i:s'); ?> | System Verification Code: NEB-<?php echo time(); ?>
            </div>
        </div>
    </div>

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</body>
</html>
