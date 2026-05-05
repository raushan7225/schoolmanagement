<!DOCTYPE html>
<html lang="en">

<head>
    <title>School Board | Home</title>
    <?php include("common/meta.php"); ?>
    <meta name="keywords" content="School Board">
    <meta name="description" content="">
</head>

<body>

    <?php include("common/header.php"); ?>
    
<?php
// Fetch dynamic banners
$bannerStmt = $pdo->query("SELECT * FROM frontend_banners WHERE status = 1 ORDER BY created_at DESC");
$dynamicBanners = $bannerStmt->fetchAll(PDO::FETCH_ASSOC);
?>

    <!-- 1. Hero Slider Section -->
    <section class="hero-section">
        <div class="owl-carousel hero-slider owl-theme">
            <?php if(empty($dynamicBanners)): ?>
                <!-- Fallback if no banners -->
                <div class="item" style="background-image: url('<?php echo BASE_URL; ?>media/sliders/slider-1.webp');"></div>
            <?php else: foreach($dynamicBanners as $db): ?>
                <div class="item">
                    <picture>
                        <?php if($db['tablet_image']): ?>
                            <source media="(max-width: 1024px)" srcset="<?php echo BASE_URL; ?>media/frontend/<?php echo $db['tablet_image']; ?>">
                        <?php endif; ?>
                        <?php if($db['mobile_image']): ?>
                            <source media="(max-width: 768px)" srcset="<?php echo BASE_URL; ?>media/frontend/<?php echo $db['mobile_image']; ?>">
                        <?php endif; ?>
                        <img src="<?php echo BASE_URL; ?>media/frontend/<?php echo $db['image']; ?>" class="w-100" alt="<?php echo htmlspecialchars($db['title']); ?>">
                    </picture>
                    <?php if($db['title'] || $db['subtitle']): ?>
                        <div class="slider-caption container">
                            <div class="caption-content">
                                <?php if($db['title']): ?> <h1 class="animate__animated animate__fadeInDown"><?php echo htmlspecialchars($db['title']); ?></h1> <?php endif; ?>
                                <?php if($db['subtitle']): ?> <p class="animate__animated animate__fadeInUp"><?php echo htmlspecialchars($db['subtitle']); ?></p> <?php endif; ?>
                                <?php if($db['link']): ?>
                                    <a href="<?php echo $db['link']; ?>" class="btn btn-secondary-theme rounded-pill px-4 animate__animated animate__zoomIn">Discover More</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; endif; ?>
        </div>
    </section>
 
    <!-- Stats Bar -->
    <div class="stats-bar py-5 bg-white shadow-sm" style="position: relative; z-index: 5; margin-top: -30px; border-radius: 20px 20px 0 0; border: 1px solid rgba(27, 18, 96, 0.05);">
        <div class="container">
            <div class="row text-center gy-4">
                <?php for($i=1; $i<=4; $i++): 
                    $s_title = getSec($pdo, "STATS_$i", 'title');
                    $s_content = getSec($pdo, "STATS_$i", 'content');
                ?>
                <div class="col-lg-3 col-6">
                    <div class="p-3">
                        <h2 class="fw-bold text-primary-theme mb-1"><?php echo htmlspecialchars($s_title); ?></h2>
                        <span class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 1px; font-size: 11px;"><?php echo htmlspecialchars($s_content); ?></span>
                    </div>
                </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>

    <!-- 2. Programs and Courses Offered Section -->
    <section class="section-padding bg-primary-theme programs-section text-center">
        <div class="container">
            <h2 class="fw-bold text-white mb-3"><?php echo getSec($pdo, 'PROGRAMS_HEADING', 'title') ?: 'Programs and Courses Offered'; ?></h2>
            <p class="text-white-50 mb-5 text-sm"><?php echo getSec($pdo, 'PROGRAMS_HEADING', 'content') ?: 'Choose a program that meets your goals from Secondary to Skill & Vocational education.'; ?></p>

            <div class="row justify-content-center gy-4">
                <div class="col-lg-4 col-md-6">
                    <div class="card program-card border-0 h-100 p-4 text-start rounded-4">
                        <h5 class="fw-bold text-secondary-theme mb-3"><?php echo getSec($pdo, 'PROG_SECONDARY', 'title') ?: 'Secondary Level'; ?></h5>
                        <p class="text-muted text-sm mb-4"><?php echo getSec($pdo, 'PROG_SECONDARY', 'content') ?: 'Equivalent to the 10th standard, this program builds a solid foundation for further education and career pathways.'; ?></p>
                        <div class="mt-auto">
                            <a href="<?php echo BASE_URL; ?>secondary" class="btn btn-secondary-theme btn-sm rounded-pill px-4">Read More &rarr;</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card program-card border-0 h-100 p-4 text-start rounded-4">
                        <h5 class="fw-bold text-secondary-theme mb-3"><?php echo getSec($pdo, 'PROG_SR_SECONDARY', 'title') ?: 'Sr. Secondary Level'; ?></h5>
                        <p class="text-muted text-sm mb-4"><?php echo getSec($pdo, 'PROG_SR_SECONDARY', 'content') ?: 'Equivalent to the 12th standard, this program opens doors to higher education and professional courses.'; ?></p>
                        <div class="mt-auto">
                            <a href="<?php echo BASE_URL; ?>sr-secondary" class="btn btn-secondary-theme btn-sm rounded-pill px-4">Read More &rarr;</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card program-card border-0 h-100 p-4 text-start rounded-4">
                        <h5 class="fw-bold text-secondary-theme mb-3"><?php echo getSec($pdo, 'PROG_VOCATIONAL', 'title') ?: 'Skills & Vocational Education'; ?></h5>
                        <p class="text-muted text-sm mb-4"><?php echo getSec($pdo, 'PROG_VOCATIONAL', 'content') ?: 'Practical, skill-based programs designed to equip learners with industry-relevant expertise.'; ?></p>
                        <div class="mt-auto">
                            <a href="<?php echo BASE_URL; ?>certification" class="btn btn-secondary-theme btn-sm rounded-pill px-4">Read More &rarr;</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. Image Collage & Notice Board -->
    <section class="section-padding bg-white">
        <div class="container">
            <div class="row gy-5 align-items-stretch justify-content-between">
                <!-- Left Collage Image -->
                <div class="col-lg-4">
                    <div class="h-100 d-flex align-items-center justify-content-center">
                        <?php 
                        $noticeImg = getSec($pdo, 'NOTICE_BOARD_IMAGE', 'image'); 
                        $noticeImgSrc = $noticeImg ? BASE_URL . 'media/frontend/' . $noticeImg : BASE_URL . 'media/banners/admission-image.webp';
                        ?>
                        <img src="<?php echo $noticeImgSrc; ?>"
                            class="img-fluid rounded-4 shadow-sm w-100 h-100 object-fit-cover" alt="Admissions Open">
                    </div>
                </div>

                <!-- Right Notice Board -->
                <div class="col-lg-6 ">
                    <div class="card border-0 shadow rounded-4 h-100 notice-board-card">
                        <div class="card-body p-4 p-md-5">
                            <h4 class="fw-bold mb-4 text-primary-theme">Notice Board</h4>
                            <div class="notice-list">
                                <?php
                                $noticeStmt = $pdo->query("SELECT * FROM frontend_notices WHERE status = 1 ORDER BY notice_date DESC LIMIT 6");
                                $dynamicNotices = $noticeStmt->fetchAll(PDO::FETCH_ASSOC);
                                if(empty($dynamicNotices)):
                                ?>
                                    <div class="notice-item-new"><a href="#">No active notices at the moment.</a></div>
                                <?php else: foreach($dynamicNotices as $dn): ?>
                                    <div class="notice-item-new">
                                        <a href="<?php echo $dn['file_path'] ? BASE_URL . 'media/frontend/' . $dn['file_path'] : '#'; ?>" target="_blank">
                                            <?php echo htmlspecialchars($dn['title']); ?>
                                            <?php if($dn['file_path']): ?><i class="fas fa-paperclip ms-1 text-xs"></i><?php endif; ?>
                                        </a>
                                        <div class="small text-muted" style="font-size: 0.7rem;"><?php echo date('d M, Y', strtotime($dn['notice_date'])); ?></div>
                                    </div>
                                <?php endforeach; endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. Student & Academic Portals -->
    <section class="section-padding bg-light text-center">
        <div class="container">
            <h2 class="fw-bold text-primary-theme mb-2">Student & Academic Portals</h2>
            <p class="text-muted mb-5">Access your student and academic centers portals from here.</p>

            <div class="row justify-content-center gy-4">
                <div class="col-lg-4 col-md-6">
                    <div class="card border-0 shadow-sm h-100 p-4 portal-card rounded-4">
                        <div class="portal-icon mb-4">
                            <div class="icon-circle">
                                <i class="fas fa-user-check text-secondary-theme fs-3"></i>
                            </div>
                        </div>
                        <h5 class="fw-bold text-white mb-4">Student Verification</h5>
                        <div class="mt-auto">
                            <a href="<?php echo BASE_URL; ?>student/registration-verification.php"
                                class="btn btn-secondary-theme rounded-pill px-4 btn-sm">Verify Now</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card border-0 shadow-sm h-100 p-4 portal-card rounded-4">
                        <div class="portal-icon mb-4">
                            <div class="icon-circle">
                                <i class="fas fa-user-graduate text-secondary-theme fs-3"></i>
                            </div>
                        </div>
                        <h5 class="fw-bold text-white mb-4">Student Login</h5>
                        <div class="mt-auto">
                            <a href="<?php echo BASE_URL; ?>login.php?role=student" class="btn btn-secondary-theme rounded-pill px-4 btn-sm">Login</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card border-0 shadow-sm h-100 p-4 portal-card rounded-4">
                        <div class="portal-icon mb-4">
                            <div class="icon-circle">
                                <i class="fas fa-university text-secondary-theme fs-3"></i>
                            </div>
                        </div>
                        <h5 class="fw-bold text-white mb-4">Academic Centre Login</h5>
                        <div class="mt-auto">
                            <a href="<?php echo BASE_URL; ?>login.php?role=franchise" class="btn btn-secondary-theme rounded-pill px-4 btn-sm">Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. Accreditations & Approvals -->
    <section class="section-padding bg-primary-theme text-center pb-10">
        <div class="container">
            <h2 class="fw-bold text-white mb-3"><?php echo getSec($pdo, 'ACCREDITATION_INTRO', 'title') ?: 'Accreditations & <span class="text-secondary-theme">Approvals</span>'; ?></h2>
            <p class="text-white-50 mx-auto mb-5" style="max-width: 800px;"><?php echo getSec($pdo, 'ACCREDITATION_INTRO', 'content') ?: 'National examination Board of Open Schooling and skill education is recognized and approved by various government bodies and institutions.'; ?></p>

            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4 justify-content-center pb-5">
                <?php
                $affStmt = $pdo->query("SELECT * FROM frontend_affiliations WHERE status = 1 ORDER BY created_at ASC");
                $dynamicAffs = $affStmt->fetchAll(PDO::FETCH_ASSOC);
                foreach($dynamicAffs as $da):
                ?>
                <div class="col">
                    <div class="card bg-primary-dark border-0 p-3 h-100 d-flex flex-row align-items-center text-start rounded-3 approval-card">
                        <img src="<?php echo BASE_URL; ?>media/frontend/<?php echo $da['logo']; ?>" class="rounded me-3 flex-shrink-0" alt="<?php echo htmlspecialchars($da['name']); ?>" style="width: 40px; height: 40px; object-fit: contain;">
                        <span class="text-white small fw-semibold lh-sm text-xs"><?php echo htmlspecialchars($da['name']); ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="row gy-4">
                <?php
                $wc_desc = getSec($pdo, 'WHY_CHOOSE_DESC') ?: "For a learning environment that provides enhanced employment qualifications, accessible equal education support.";
                ?>
                <div class="container" style="position: relative; z-index: 10;">
                    <div class="bg-white p-4 p-md-5 rounded-4 shadow mx-auto">
                        <h2 class="fw-bold text-primary-theme mb-3"><?php echo getSec($pdo, 'WHY_CHOOSE_HEADING', 'title') ?: 'Why Choose Board?'; ?></h2>
                        <p class="text-muted mx-auto mb-5 text-sm" style="max-width: 700px;"><?php echo getSec($pdo, 'WHY_CHOOSE_HEADING', 'content') ?: 'For a learning environment that provides enhanced employment qualifications, accessible equal education support.'; ?></p>

                        <div class="row gy-4">
                            <?php
                            $features = ['WHY_CHOOSE_F1', 'WHY_CHOOSE_F2', 'WHY_CHOOSE_F3'];
                            $icons = ['fa-clock', 'fa-book-open', 'fa-map-marked-alt'];
                            foreach($features as $idx => $fkey):
                                $fstmt = $pdo->prepare("SELECT title, content FROM frontend_sections WHERE section_key = ? AND status = 1 LIMIT 1");
                                $fstmt->execute([$fkey]);
                                $f = $fstmt->fetch(PDO::FETCH_ASSOC);
                                
                                $ftitle = $f['title'] ?? ($idx == 0 ? "Flexible Learning" : ($idx == 1 ? "Wide Subject Choice" : "Nationwide Access"));
                                $fcontent = $f['content'] ?? ($idx == 0 ? "Study online, during your own time schedule with access all study materials online 24/7." : ($idx == 1 ? "Select from multiple distinct courses to match your requirements and interests." : "Get access to verified centers and examinations across the country."));
                            ?>
                            <div class="col-md-4">
                                <div class="p-3 bg-light-blue rounded-4 h-100">
                                    <div class="icon-circle-outline mx-auto mb-4 bg-white mt-3 shadow-sm border border-orange-light text-secondary-theme">
                                        <i class="fas <?php echo $icons[$idx]; ?> fs-4"></i>
                                    </div>
                                    <h6 class="fw-bold text-primary-theme mb-2"><?php echo htmlspecialchars($ftitle); ?></h6>
                                    <p class="text-muted text-xs mb-0"><?php echo htmlspecialchars($fcontent); ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- 7. CTA / Contact / Mobile App Section -->
    <section class="py-5 position-relative bg-light">
        <div class="container">
            <div class="card border-0 shadow rounded-4 overflow-hidden my-5 z-2 position-relative">
                <div class="row g-0 align-items-center">
                    <!-- Left Image -->
                    <div class="col-md-5 position-relative">
                        <?php 
                        $enquiryImg = getSec($pdo, 'ENQUIRY_SIDE_IMAGE', 'image'); 
                        $enquiryImgSrc = $enquiryImg ? BASE_URL . 'media/frontend/' . $enquiryImg : BASE_URL . 'media/banners/enquiry-image.webp';
                        ?>
                        <img src="<?php echo $enquiryImgSrc; ?>" class="img-fluid w-100 h-100 object-fit-cover"
                            alt="Students" style="min-height: 100%;">
                        <div class="position-absolute bottom-0 start-0 w-100 p-4"
                            style="background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);">
                            <span class="text-secondary-theme fw-bold text-uppercase" style="font-size: 11px;">Admission
                                Open</span>
                            <h4 class="text-white fw-bold mb-0">Start Your Journey Today</h4>
                        </div>
                    </div>
                    <!-- Right Content -->
                    <div class="col-md-7 p-4 p-md-5 bg-white">
                        <h3 class="fw-bold text-primary-theme mb-3">Have Questions?</h3>
                        <p class="text-muted mb-4 text-sm">Our team is here to help you with the courses, programs details,
                            and recognised solutions. Reach out to us for the support you need.</p>

                        <div class="row g-3 mb-4">
                            <div class="col-sm-6">
                                <div
                                    class="bg-light-blue p-3 rounded-4 d-flex align-items-center border border-white shadow-sm">
                                    <i class="fas fa-phone-alt fs-5 text-secondary-theme me-3"></i>
                                    <div>
                                        <h6 class="mb-0 text-dark fw-bold text-xs">Call Us</h6>
                                        <span class="text-muted text-xs d-block"><?php echo getSetting('site_phone', '+91 9876543210'); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div
                                    class="bg-light-blue p-3 rounded-4 d-flex align-items-center border border-white shadow-sm">
                                    <i class="fas fa-envelope fs-5 text-secondary-theme me-3"></i>
                                    <div>
                                        <h6 class="mb-0 text-dark fw-bold text-xs">Email Us</h6>
                                        <span class="text-muted text-xs d-block"><?php echo getSetting('site_email', 'info@example.com'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <a href="<?php echo BASE_URL; ?>contact-us.php"
                            class="btn btn-secondary-theme rounded-pill px-4 py-2 fw-bold shadow-sm d-inline-block">Download
                            App for Student &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include("common/footer.php"); ?>

    <?php include("common/requirejs.php"); ?>

</body>

</html>