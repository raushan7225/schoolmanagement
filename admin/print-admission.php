<?php
// admin/print-admission.php
require_once('../common/config.php');

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized");
}

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("
    SELECT a.*, c.name as course_name, f.center_name, f.center_code,
           st.name as state_name, dt.name as district_name, ct.name as city_name
    FROM admissions a
    LEFT JOIN courses c ON a.course_id = c.id
    LEFT JOIN franchises f ON a.center_id = f.id
    LEFT JOIN states st ON a.state_id = st.id
    LEFT JOIN districts dt ON a.district_id = dt.id
    LEFT JOIN cities ct ON a.city_id = ct.id
    WHERE a.id = ?
");
$stmt->execute([$id]);
$s = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$s) {
    die("Student not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Print Admission - <?php echo htmlspecialchars($s['full_name']); ?></title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; line-height: 1.6; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 24px; text-transform: uppercase; }
        .header p { margin: 5px 0 0; font-size: 14px; }
        .details-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .details-table th, .details-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .details-table th { background-color: #f4f4f4; width: 30%; }
        .photo-container { position: absolute; top: 120px; right: 20px; width: 120px; height: 150px; border: 1px solid #ccc; text-align: center; line-height: 150px; }
        .footer { margin-top: 50px; display: flex; justify-content: space-between; }
        .signature-box { width: 200px; border-top: 1px solid #000; text-align: center; padding-top: 5px; }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print" style="margin-bottom: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px;">Print this page</button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size: 16px;">Close</button>
    </div>

    <div class="header">
        <h1>Admission Form Copy</h1>
        <p>Registration No: NEB/REG/<?php echo str_pad($s['id'], 6, '0', STR_PAD_LEFT); ?></p>
    </div>

    <?php if ($s['photo']): ?>
        <img src="<?php echo BASE_URL . 'media/students/' . $s['photo']; ?>" class="photo-container" style="object-fit: cover;">
    <?php else: ?>
        <div class="photo-container">Photo Here</div>
    <?php endif; ?>

    <table class="details-table" style="width: calc(100% - 150px);">
        <tr>
            <th>Student Name</th>
            <td><?php echo strtoupper(htmlspecialchars($s['full_name'])); ?></td>
        </tr>
        <tr>
            <th>Father's Name</th>
            <td><?php echo htmlspecialchars($s['father_name']); ?></td>
        </tr>
        <tr>
            <th>Mother's Name</th>
            <td><?php echo htmlspecialchars($s['mother_name']); ?></td>
        </tr>
        <tr>
            <th>Date of Birth</th>
            <td><?php echo date('d M Y', strtotime($s['dob'])); ?></td>
        </tr>
        <tr>
            <th>Gender</th>
            <td><?php echo ucfirst($s['gender']); ?></td>
        </tr>
        <tr>
            <th>Course</th>
            <td><?php echo htmlspecialchars($s['course_name']); ?></td>
        </tr>
        <tr>
            <th>Center Name</th>
            <td><?php echo htmlspecialchars($s['center_name']) . ' (' . htmlspecialchars($s['center_code']) . ')'; ?></td>
        </tr>
        <tr>
            <th>Mobile</th>
            <td><?php echo htmlspecialchars($s['mobile']); ?></td>
        </tr>
        <tr>
            <th>Address</th>
            <td>
                <?php 
                echo htmlspecialchars($s['address']) . ', ' . 
                     htmlspecialchars($s['city_name']) . ', ' . 
                     htmlspecialchars($s['district_name']) . ', ' . 
                     htmlspecialchars($s['state_name']) . ' - ' . 
                     htmlspecialchars($s['pincode']); 
                ?>
            </td>
        </tr>
        <tr>
            <th>Admission Date</th>
            <td><?php echo date('d M Y', strtotime($s['admission_date'])); ?></td>
        </tr>
    </table>

    <div class="footer">
        <div class="signature-box">Student's Signature</div>
        <div class="signature-box">Authorized Signatory</div>
    </div>
</body>
</html>
