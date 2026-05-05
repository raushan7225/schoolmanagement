<!-- Top Info Bar -->
<div class="top-bar py-2 d-lg-block text-white bg-primary-theme">
    <div class="container-fluid px-lg-5">
        <div class="row align-items-center">
            <div class="col-md-6 d-none d-sm-block text-start">
                <span class="me-3"><i class="fas fa-phone-alt text-secondary-theme me-2"></i><?php echo getSetting('site_phone', '+91 00000 00000'); ?> || <?php echo getSetting('site_email', 'info@example.com'); ?></span>
            </div>
            <div class="col-md-6 text-center text-sm-end">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span class="text-white me-3 small"><i class="fas fa-user-circle me-1"></i> Hi, <?php echo $_SESSION['username']; ?></span>
                    <a href="<?php echo BASE_URL . $_SESSION['role']; ?>/index.php" class="topbar-btn topbar-btn-orange me-2">Dashboard</a>
                    <a href="<?php echo BASE_URL; ?>logout.php" class="topbar-btn btn-danger text-white"><i class="fas fa-sign-out-alt"></i> Logout</a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>login.php?role=student" class="topbar-btn topbar-btn-orange me-2">Student Login</a>
                    <a href="<?php echo BASE_URL; ?>login.php?role=franchise" class="topbar-btn topbar-btn-orange me-2">Franchise Login</a>
                    <a href="<?php echo BASE_URL; ?>login.php?role=admin" class="topbar-btn topbar-btn-orange"><i class="fas fa-user-shield"></i></a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Main Navigation -->
<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top">
    <div class="container-fluid px-lg-5">
        <!-- Logo -->
        <a class="navbar-brand py-0" href="<?php echo BASE_URL; ?>">
            <img src="<?php echo BASE_URL . getSetting('theme_logo', 'media/general/logo.jpeg'); ?>" alt="Logo" height="90px" onerror="this.onerror=null; this.src='https://placehold.co/250x75/FFF/1b1260?text=LOGO';">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav ms-auto mb-0 fw-semibold neb-nav-pill">
                <?php
                // Fetch dynamic menus
                $menuStmt = $pdo->query("SELECT * FROM frontend_menus WHERE status = 1 ORDER BY sort_order ASC");
                $allMenus = $menuStmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Group by parent
                $menuTree = [];
                foreach($allMenus as $m) {
                    $m_id = (int)$m['id'];
                    $p_id = (int)$m['parent_id'];
                    if($p_id == 0) $menuTree[$m_id] = ['info' => $m, 'children' => []];
                }
                foreach($allMenus as $m) {
                    $p_id = (int)$m['parent_id'];
                    if($p_id != 0 && isset($menuTree[$p_id])) $menuTree[$p_id]['children'][] = $m;
                }

                foreach($menuTree as $m_id => $m_item):
                    $m = $m_item['info'];
                    $children = $m_item['children'];
                    $hasChildren = !empty($children);
                    
                    // Active logic for slugs
                    $current_slug = $_GET['slug'] ?? '';
                    if(empty($current_slug) && basename($_SERVER['PHP_SELF']) == 'index.php') {
                        $active = ($m['link'] == 'index.php' || $m['link'] == '' || $m['link'] == '/') ? 'active' : '';
                    } else {
                        $active = ($current_slug == $m['link']) ? 'active' : '';
                    }
                ?>
                    <li class="nav-item <?php echo $hasChildren ? 'dropdown' : ''; ?>">
                        <a class="nav-link <?php echo $active; ?> px-3 <?php echo $hasChildren ? 'dropdown-toggle' : ''; ?>" 
                           href="<?php echo $hasChildren ? '#' : (str_contains($m['link'], 'http') ? $m['link'] : ($m['link'] == 'index.php' ? BASE_URL : BASE_URL . $m['link'])); ?>" 
                           <?php echo $hasChildren ? 'id="drop-'.$m_id.'" role="button" data-bs-toggle="dropdown" aria-expanded="false"' : ''; ?>>
                            <?php echo htmlspecialchars($m['title']); ?>
                            <?php if($hasChildren): ?><i class="fas fa-angle-down ms-1" style="font-size:10px;"></i><?php endif; ?>
                        </a>
                        <?php if($hasChildren): ?>
                            <ul class="dropdown-menu border-0 shadow" aria-labelledby="drop-<?php echo $m_id; ?>">
                                <?php foreach($children as $child): ?>
                                    <li><a class="dropdown-item py-1" href="<?php echo (str_contains($child['link'], 'http') ? $child['link'] : BASE_URL . $child['link']); ?>"><?php echo htmlspecialchars($child['title']); ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Announcement Bar -->
<div class="announcement-bar">
    <div class="announcement-label">Announcement :</div>
    <div class="announcement-marquee">
        <marquee behavior="scroll" direction="left" scrollamount="6">
            <?php
            $tickerNotices = $pdo->query("SELECT title FROM frontend_notices WHERE status = 1 ORDER BY notice_date DESC LIMIT 5")->fetchAll(PDO::FETCH_COLUMN);
            if(empty($tickerNotices)) {
                echo "Admissions Open for Session 2026-27. | Welcome to National Examination Board.";
            } else {
                echo implode(" &nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp; ", array_map('htmlspecialchars', $tickerNotices));
            }
            ?>
        </marquee>
    </div>
</div>
