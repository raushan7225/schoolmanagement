<?php require_once __DIR__ . '/config.php'; ?>
<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=7">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
     <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title><?php echo getSetting('site_title', 'National Examination Board'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="<?php echo BASE_URL . getSetting('theme_favicon', 'media/general/favicon.png'); ?>">
    <!-- Custom Web Fonts (Google Fonts) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <!-- Dynamic Theme Colors from General Settings -->
    <link id="dynamic-theme-css" rel="stylesheet" href="<?php echo BASE_URL; ?>theme.php">