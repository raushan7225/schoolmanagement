<?php
require_once('common/config.php');

$slug = $_GET['slug'] ?? '';
if (!$slug) {
    header("Location: " . BASE_URL);
    exit;
}

// Fetch Page Data
$stmt = $pdo->prepare("SELECT * FROM frontend_pages WHERE slug = ? AND status = 1 LIMIT 1");
$stmt->execute([$slug]);
$page = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$page) {
    // Check if a special template exists for this slug even if DB record is missing
    $possible_templates = ["templates/" . $slug . "-template.php", "templates/" . str_replace('-us', '', $slug) . "-template.php"];
    $found_template = false;
    foreach($possible_templates as $t_path) {
        if(file_exists($t_path)) {
            $found_template = true;
            break;
        }
    }

    if ($found_template) {
        $page = [
            'title' => ucwords(str_replace('-', ' ', $slug)),
            'content' => '',
            'meta_title' => ucwords(str_replace('-', ' ', $slug)),
            'meta_description' => 'Welcome to ' . ucwords(str_replace('-', ' ', $slug)) . ' page.'
        ];
    } else if (file_exists($slug . ".php")) {
        include($slug . ".php");
        exit;
    } else {
        header("HTTP/1.1 404 Not Found");
        if (file_exists("404.php")) {
            include("404.php");
        } else {
            echo "<h1>404 Not Found</h1><p>The page you are looking for does not exist.</p>";
        }
        exit;
    }
}

// Template Mapping
$template = 'default';
$special_templates = ['about-us', 'recognition', 'contact-us', 'franchise-info', 'center-list', 'events', 'event-details', 'gallery'];

if (in_array($slug, $special_templates)) {
    $template = $slug;
    // Check if we need to use the base version (e.g. contact-template instead of contact-us-template)
    if (!file_exists("templates/" . $template . "-template.php")) {
        $base = str_replace('-us', '', $template);
        if (file_exists("templates/" . $base . "-template.php")) {
            $template = $base;
        }
    }
} else if (file_exists("templates/" . $slug . "-template.php")) {
    $template = $slug;
}

$page_title = $page['title'];
$page_content = $page['content'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title><?php echo htmlspecialchars($page['meta_title'] ?: $page_title); ?> | <?php echo getSetting('site_title', 'NEB'); ?></title>
    <?php include("common/meta.php"); ?>
    <meta name="description" content="<?php echo htmlspecialchars($page['meta_description']); ?>">
</head>

<body>
    <?php include("common/header.php"); ?>

    <!-- Dynamic Page Header -->
    <?php
    $bannerImg = getSec($pdo, 'INNER_PAGE_BANNER', 'image');
    $bannerSrc = $bannerImg ? BASE_URL . 'media/frontend/' . $bannerImg : BASE_URL . 'media/banners/about-us.png';
    ?>
    <div class="page-header text-center" style="background: linear-gradient(rgba(13, 43, 82, 0.85), rgba(13, 43, 82, 0.85)), url('<?php echo $bannerSrc; ?>') center center; padding: 70px 0; background-size: cover;">
        <div class="container">
            <h1 class="display-4 fw-bold text-white mb-3"><?php echo htmlspecialchars($page_title); ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center mb-0">
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>" class="text-white text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active text-white" aria-current="page"><?php echo htmlspecialchars($page_title); ?></li>
                </ol>
            </nav>
        </div>
    </div>

    <?php if ($template == 'contact-us' || $template == 'contact'): ?>
        <!-- Special Template: Contact Us -->
        <?php include('templates/contact-template.php'); ?>
    <?php elseif ($template == 'about-us' || $template == 'about'): ?>
        <!-- Special Template: About Us -->
        <?php include('templates/about-template.php'); ?>
    <?php elseif ($template == 'recognition'): ?>
        <!-- Special Template: Recognition -->
        <?php include('templates/recognition-template.php'); ?>
    <?php elseif ($template == 'franchise-info' || $template == 'franchise'): ?>
        <!-- Special Template: Franchise Info -->
        <?php include('templates/franchise-info-template.php'); ?>
    <?php elseif ($template == 'center-list' || $template == 'center'): ?>
        <!-- Special Template: Center List -->
        <?php include('templates/center-list-template.php'); ?>
    <?php elseif ($template == 'events'): ?>
        <!-- Special Template: Events -->
        <?php include('templates/events-template.php'); ?>
    <?php elseif ($template == 'event-details'): ?>
        <!-- Special Template: Event Details -->
        <?php include('templates/event-details-template.php'); ?>
    <?php elseif ($template == 'gallery'): ?>
        <!-- Special Template: Gallery -->
        <?php include('templates/gallery-template.php'); ?>
    <?php else: ?>
        <!-- Default CMS Template -->
        <section class="section-padding bg-white min-vh-50">
            <div class="container">
                <div class="cms-content shadow-sm p-4 p-md-5 rounded-4 border bg-white">
                    <?php echo $page_content; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php include("common/footer.php"); ?>
    <?php include("common/requirejs.php"); ?>
</body>

</html>