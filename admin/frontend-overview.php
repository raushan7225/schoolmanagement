<?php
// admin/frontend-overview.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Fetch stats
$totalPages = $pdo->query("SELECT COUNT(*) FROM frontend_pages")->fetchColumn();
$activePages = $pdo->query("SELECT COUNT(*) FROM frontend_pages WHERE status = 1")->fetchColumn();
$totalMenus = $pdo->query("SELECT COUNT(*) FROM frontend_menus")->fetchColumn();
$totalBanners = $pdo->query("SELECT COUNT(*) FROM frontend_banners")->fetchColumn();
$totalEvents = $pdo->query("SELECT COUNT(*) FROM frontend_events")->fetchColumn();
?>

<div class="pagetitle">
    <h1>Frontend CMS Dashboard</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item active">Frontend Overview</li>
        </ol>
    </nav>
</div>

<section class="section dashboard">
    <div class="row">

        <!-- Stats Cards -->
        <div class="col-xxl-3 col-md-6">
            <div class="card info-card sales-card">
                <div class="card-body">
                    <h5 class="card-title">Pages <span>| Total</span></h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-file-earmark-richtext"></i>
                        </div>
                        <div class="ps-3">
                            <h6><?php echo $totalPages; ?></h6>
                            <span class="text-success small pt-1 fw-bold"><?php echo $activePages; ?></span> <span class="text-muted small pt-2 ps-1">Published</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-md-6">
            <div class="card info-card revenue-card">
                <div class="card-body">
                    <h5 class="card-title">Menu Items <span>| Nav</span></h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-list-task"></i>
                        </div>
                        <div class="ps-3">
                            <h6><?php echo $totalMenus; ?></h6>
                            <span class="text-muted small pt-2 ps-1">Active Links</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-md-6">
            <div class="card info-card customers-card">
                <div class="card-body">
                    <h5 class="card-title">Banners <span>| Sliders</span></h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-images"></i>
                        </div>
                        <div class="ps-3">
                            <h6><?php echo $totalBanners; ?></h6>
                            <span class="text-muted small pt-2 ps-1">Hero Sections</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-md-6">
            <div class="card info-card sales-card">
                <div class="card-body">
                    <h5 class="card-title">Events <span>| Active</span></h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center" style="color: #ff771d; background: #ffecdf;">
                            <i class="bi bi-calendar-event"></i>
                        </div>
                        <div class="ps-3">
                            <h6><?php echo $totalEvents; ?></h6>
                            <span class="text-muted small pt-2 ps-1">Public Events</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Management Links -->
        <div class="col-12">
            <div class="card">
                <div class="card-body pt-3">
                    <h5 class="card-title">Management Modules</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <a href="frontend-page-manage.php" class="d-flex align-items-center p-3 border rounded-3 text-decoration-none hover-light">
                                <i class="bi bi-file-plus fs-3 text-primary me-3"></i>
                                <div>
                                    <h6 class="mb-0 fw-bold">Page Manager</h6>
                                    <small class="text-muted">Create and edit custom pages</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="frontend-menu.php" class="d-flex align-items-center p-3 border rounded-3 text-decoration-none hover-light">
                                <i class="bi bi-menu-button-wide fs-3 text-success me-3"></i>
                                <div>
                                    <h6 class="mb-0 fw-bold">Menu Builder</h6>
                                    <small class="text-muted">Manage navigation dropdowns</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="frontend-banners.php" class="d-flex align-items-center p-3 border rounded-3 text-decoration-none hover-light">
                                <i class="bi bi-layout-wtf fs-3 text-warning me-3"></i>
                                <div>
                                    <h6 class="mb-0 fw-bold">Banner Manager</h6>
                                    <small class="text-muted">Upload homepage sliders</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="frontend-notices.php" class="d-flex align-items-center p-3 border rounded-3 text-decoration-none hover-light">
                                <i class="bi bi-megaphone fs-3 text-info me-3"></i>
                                <div>
                                    <h6 class="mb-0 fw-bold">Notice Board</h6>
                                    <small class="text-muted">Scrolling and popup notices</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="frontend-events.php" class="d-flex align-items-center p-3 border rounded-3 text-decoration-none hover-light">
                                <i class="bi bi-calendar2-range fs-3 text-danger me-3"></i>
                                <div>
                                    <h6 class="mb-0 fw-bold">Events & News</h6>
                                    <small class="text-muted">Keep students updated</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="frontend-gallery.php" class="d-flex align-items-center p-3 border rounded-3 text-decoration-none hover-light">
                                <i class="bi bi-camera fs-3 text-secondary me-3"></i>
                                <div>
                                    <h6 class="mb-0 fw-bold">Photo Gallery</h6>
                                    <small class="text-muted">Categorized media albums</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="frontend-recognitions.php" class="d-flex align-items-center p-3 border rounded-3 text-decoration-none hover-light">
                                <i class="bi bi-award fs-3 text-primary me-3"></i>
                                <div>
                                    <h6 class="mb-0 fw-bold">Recognitions</h6>
                                    <small class="text-muted">Manage Board Approvals</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="frontend-page-section.php" class="d-flex align-items-center p-3 border rounded-3 text-decoration-none hover-light">
                                <i class="bi bi-grid-3x3-gap fs-3 text-info me-3"></i>
                                <div>
                                    <h6 class="mb-0 fw-bold">Page Sections</h6>
                                    <small class="text-muted">Manage About Us blocks</small>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<style>
.hover-light:hover { background-color: #f8f9fa; }
.card-icon i { font-size: 32px; line-height: 0; }
</style>

<?php include(__DIR__ . "/includes/footer.php"); ?>
