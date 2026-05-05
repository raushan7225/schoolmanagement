<?php
// templates/about-template.php
?>

<section class="section-padding bg-light-theme overflow-hidden">
    <div class="container">
        <!-- 1. Intro Section with Animation -->
        <div class="row align-items-center gy-5 mb-5">
            <div class="col-lg-6 pe-lg-5 animate-left">
                <div class="mb-4">
                    <h6 class="text-secondary-theme fw-bold text-uppercase mb-2 letter-spacing-1"><?php echo getSec($pdo, 'ABOUT_SUBTITLE', 'title') ?: 'Welcome to our Institution'; ?></h6>
                    <h2 class="display-5 fw-bold text-dark mb-4"><?php echo getSec($pdo, 'ABOUT_INTRO', 'title') ?: 'Transforming Lives Through Quality Education'; ?></h2>
                    <div class="theme-separator mb-4"></div>
                </div>
                
                <div class="text-muted lead mb-5 fs-5 lh-lg">
                    <?php echo getSec($pdo, 'ABOUT_INTRO', 'content') ?: 'We are committed to providing accessible, high-quality open schooling and vocational training to learners across the nation.'; ?>
                </div>
                
                <div class="row g-4 mb-5">
                    <?php if($y_title = getSec($pdo, 'ABOUT_STAT_YEARS', 'title')): ?>
                    <div class="col-sm-4 text-center">
                        <div class="stat-card p-3 rounded-4 bg-white shadow-sm hover-lift">
                            <h3 class="fw-bold text-primary-theme mb-0"><?php echo htmlspecialchars($y_title); ?></h3>
                            <p class="text-muted small mb-0"><?php echo getSec($pdo, 'ABOUT_STAT_YEARS', 'content') ?: 'Years of Excellence'; ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if($s_title = getSec($pdo, 'ABOUT_STAT_STUDENTS', 'title')): ?>
                    <div class="col-sm-4 text-center">
                        <div class="stat-card p-3 rounded-4 bg-white shadow-sm hover-lift">
                            <h3 class="fw-bold text-primary-theme mb-0"><?php echo htmlspecialchars($s_title); ?></h3>
                            <p class="text-muted small mb-0"><?php echo getSec($pdo, 'ABOUT_STAT_STUDENTS', 'content') ?: 'Enrolled Students'; ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if($c_title = getSec($pdo, 'ABOUT_STAT_CENTERS', 'title')): ?>
                    <div class="col-sm-4 text-center">
                        <div class="stat-card p-3 rounded-4 bg-white shadow-sm hover-lift">
                            <h3 class="fw-bold text-primary-theme mb-0"><?php echo htmlspecialchars($c_title); ?></h3>
                            <p class="text-muted small mb-0"><?php echo getSec($pdo, 'ABOUT_STAT_CENTERS', 'content') ?: 'Regional Centers'; ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <?php 
                $btnText = getSec($pdo, 'ABOUT_BTN_TEXT', 'title') ?: 'Get In Touch';
                $btnLink = getSec($pdo, 'ABOUT_BTN_LINK', 'content') ?: BASE_URL . 'contact-us';
                ?>
                <a href="<?php echo htmlspecialchars($btnLink); ?>" class="btn btn-primary-theme px-5 py-3 rounded-pill fw-bold shadow">
                    <?php echo htmlspecialchars($btnText); ?> <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
            
            <div class="col-lg-6 animate-right">
                <div class="position-relative">
                    <div class="image-stack">
                        <?php 
                        $aboutImg = getSec($pdo, 'ABOUT_SIDE_IMAGE', 'image'); 
                        $aboutImgSrc = $aboutImg ? BASE_URL . 'media/frontend/' . $aboutImg : 'https://placehold.co/800x1000/0b1c3d/white?text=Our+Campus';
                        ?>
                        <img src="<?php echo $aboutImgSrc; ?>" class="img-fluid rounded-4 shadow-lg w-100" style="object-fit: cover; height: 600px;" alt="About Us">
                        
                        <?php if($b_title = getSec($pdo, 'ABOUT_BADGE_TITLE', 'title')): ?>
                        <div class="floating-badge bg-secondary-theme text-white p-4 rounded-4 shadow-lg animate-pulse">
                            <h4 class="fw-bold mb-1 fs-3"><?php echo htmlspecialchars($b_title); ?></h4>
                            <p class="mb-0 small opacity-90"><?php echo getSec($pdo, 'ABOUT_BADGE_SUB', 'content') ?: 'Accredited Board'; ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 2. Vision & Mission with Premium Cards -->
<section class="section-padding bg-white position-relative">
    <div class="container">
        <div class="row gy-4">
            <div class="col-md-6 animate-up">
                <div class="card border-0 vision-card p-5 h-100 rounded-4 shadow-sm overflow-hidden bg-light-theme">
                    <div class="icon-circle mb-4 bg-secondary-theme shadow">
                        <i class="fas fa-eye text-white fs-3"></i>
                    </div>
                    <h3 class="fw-bold text-dark mb-3"><?php echo getSec($pdo, 'ABOUT_VISION', 'title') ?: 'Our Vision'; ?></h3>
                    <p class="text-muted mb-0 lh-lg"><?php echo getSec($pdo, 'ABOUT_VISION', 'content') ?: 'To empower every learner with the skills and knowledge needed to succeed in a globalized world.'; ?></p>
                </div>
            </div>
            <div class="col-md-6 animate-up" style="animation-delay: 0.2s;">
                <div class="card border-0 mission-card p-5 h-100 rounded-4 shadow-sm overflow-hidden bg-primary-theme text-white">
                    <div class="icon-circle mb-4 bg-white shadow">
                        <i class="fas fa-bullseye text-primary-theme fs-3"></i>
                    </div>
                    <h3 class="fw-bold mb-3"><?php echo getSec($pdo, 'ABOUT_MISSION', 'title') ?: 'Our Mission'; ?></h3>
                    <p class="text-white-50 mb-0 lh-lg"><?php echo getSec($pdo, 'ABOUT_MISSION', 'content') ?: 'We strive to provide flexible, innovative, and accessible education that bridges the gap between formal and non-formal learning.'; ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 3. What Makes Us Different (Features List) -->
<section class="section-padding bg-light-theme">
    <div class="container">
        <div class="text-center mb-5 animate-up">
            <h2 class="fw-bold text-dark mb-2"><?php echo getSec($pdo, 'ABOUT_FEATURES_TITLE', 'title') ?: 'Why Choose Us?'; ?></h2>
            <div class="theme-separator mx-auto mb-3"></div>
            <p class="text-muted mx-auto" style="max-width: 600px;"><?php echo getSec($pdo, 'ABOUT_FEATURES_SUBTITLE', 'content') ?: 'Experience excellence in education with our unique advantages.'; ?></p>
        </div>
        
        <div class="row gy-4 gx-4">
            <?php 
            $icons = [
                1 => 'fas fa-random',
                2 => 'fas fa-briefcase',
                3 => 'fas fa-exchange-alt',
                4 => 'fas fa-rupee-sign',
                5 => 'fas fa-laptop',
                6 => 'fas fa-globe'
            ];
            for($i=1; $i<=6; $i++): 
                $title = getSec($pdo, "ABOUT_FEAT_$i", 'title');
                $desc = getSec($pdo, "ABOUT_FEAT_$i", 'content');
                if(!$title) continue;
            ?>
            <div class="col-lg-4 col-md-6 animate-up" style="animation-delay: <?php echo $i * 0.1; ?>s;">
                <div class="feature-card bg-white p-4 rounded-4 shadow-sm h-100 transition-all border-bottom border-4 border-transparent hover-border-primary">
                    <div class="d-flex align-items-start">
                        <div class="icon-box-premium me-3 bg-primary-light text-primary-theme">
                            <i class="<?php echo $icons[$i]; ?>"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-2"><?php echo htmlspecialchars($title); ?></h5>
                            <p class="text-muted small mb-0"><?php echo htmlspecialchars($desc); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endfor; ?>
        </div>
    </div>
</section>

<style>
.bg-light-theme { background-color: #f8faff; }
.theme-separator { width: 60px; height: 4px; background: var(--theme-secondary-color, #ff6a1a); border-radius: 2px; }
.letter-spacing-1 { letter-spacing: 1px; }

.stat-card { transition: all 0.3s ease; border: 1px solid rgba(0,0,0,0.05); }
.stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important; }

.floating-badge {
    position: absolute;
    bottom: 40px;
    left: -30px;
    z-index: 10;
    min-width: 220px;
}

.icon-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.feature-card:hover { transform: translateY(-10px); }
.hover-border-primary:hover { border-color: var(--theme-primary-color, #1b1260) !important; }

.icon-box-premium {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 1.2rem;
}

/* Entrance Animations */
.animate-up { animation: fadeInUp 0.8s ease-out both; }
.animate-left { animation: fadeInLeft 0.8s ease-out both; }
.animate-right { animation: fadeInRight 0.8s ease-out both; }
.animate-pulse { animation: pulse 2s infinite; }

@keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
@keyframes fadeInLeft { from { opacity: 0; transform: translateX(-30px); } to { opacity: 1; transform: translateX(0); } }
@keyframes fadeInRight { from { opacity: 0; transform: translateX(30px); } to { opacity: 1; transform: translateX(0); } }
@keyframes pulse { 0% { transform: scale(1); } 50% { transform: scale(1.05); } 100% { transform: scale(1); } }

@media (max-width: 991px) {
    .floating-badge { position: relative; left: 0; bottom: 0; margin-top: -30px; margin-left: 20px; width: fit-content; }
    .image-stack img { height: 400px !important; }
}
</style>
