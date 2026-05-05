<div class="container section-padding">
    <!-- Intro Section -->
    <div class="row justify-content-center text-center mb-5">
        <div class="col-lg-8">
            <h2 class="display-5 fw-bold text-dark-theme mb-3"><?php echo getSec($pdo, 'franchise_intro_title', 'title'); ?></h2>
            <p class="lead text-muted"><?php echo getSec($pdo, 'franchise_intro_title', 'content'); ?></p>
            <div class="theme-divider mx-auto"></div>
        </div>
    </div>

    <!-- Benefits Grid -->
    <div class="row g-4 mb-5">
        <?php
        $benefits = ['franchise_benefit_1', 'franchise_benefit_2', 'franchise_benefit_3', 'franchise_benefit_4'];
        $icons = ['fa-award', 'fa-chart-line', 'fa-bullhorn', 'fa-laptop-code'];
        foreach($benefits as $index => $key):
        ?>
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm rounded-4 p-4 text-center hover-lift transition-all">
                <div class="icon-box bg-primary-light text-primary-theme rounded-circle mb-3 mx-auto" style="width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas <?php echo $icons[$index]; ?> fs-3"></i>
                </div>
                <h5 class="fw-bold mb-3"><?php echo getSec($pdo, $key, 'title'); ?></h5>
                <p class="text-muted small mb-0"><?php echo getSec($pdo, $key, 'content'); ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Infrastructure Requirements -->
    <div class="row align-items-center g-5 section-padding">
        <div class="col-lg-6">
            <div class="pe-lg-4">
                <h3 class="fw-bold text-dark-theme mb-4"><?php echo getSec($pdo, 'franchise_infra_title', 'title'); ?></h3>
                <p class="text-muted mb-5"><?php echo getSec($pdo, 'franchise_infra_title', 'content'); ?></p>
                
                <div class="infra-item mb-4 d-flex">
                    <div class="icon-box bg-secondary-theme text-white rounded-3 me-3" style="width: 50px; height: 50px; flex-shrink: 0; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-expand-arrows-alt"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1"><?php echo getSec($pdo, 'franchise_infra_space', 'title'); ?></h6>
                        <p class="text-muted small mb-0"><?php echo getSec($pdo, 'franchise_infra_space', 'content'); ?></p>
                    </div>
                </div>

                <div class="infra-item mb-4 d-flex">
                    <div class="icon-box bg-secondary-theme text-white rounded-3 me-3" style="width: 50px; height: 50px; flex-shrink: 0; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-desktop"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1"><?php echo getSec($pdo, 'franchise_infra_hardware', 'title'); ?></h6>
                        <p class="text-muted small mb-0"><?php echo getSec($pdo, 'franchise_infra_hardware', 'content'); ?></p>
                    </div>
                </div>

                <div class="infra-item d-flex">
                    <div class="icon-box bg-secondary-theme text-white rounded-3 me-3" style="width: 50px; height: 50px; flex-shrink: 0; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1"><?php echo getSec($pdo, 'franchise_infra_hr', 'title'); ?></h6>
                        <p class="text-muted small mb-0"><?php echo getSec($pdo, 'franchise_infra_hr', 'content'); ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="position-relative">
                <img src="<?php echo BASE_URL; ?>media/infra-preview.jpg" class="img-fluid rounded-5 shadow-lg" alt="Infrastructure">
                <div class="position-absolute bottom-0 end-0 bg-primary-theme p-4 rounded-4 text-white shadow-lg m-4 d-none d-md-block">
                    <h4 class="fw-bold mb-0">15+ Years</h4>
                    <p class="small mb-0">Educational Excellence</p>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="bg-dark-theme rounded-5 p-5 text-center text-white shadow-lg" style="background: linear-gradient(45deg, #0b1c3d, #1a2a4a);">
                <h3 class="fw-bold mb-3">Ready to Start Your Own Center?</h3>
                <p class="opacity-75 mb-4 mx-auto" style="max-width: 600px;">Join our growing network of 200+ authorized study centers and be a part of the educational revolution.</p>
                <a href="<?php echo BASE_URL; ?>franchise/franchise-application" class="btn btn-light btn-lg px-5 py-3 rounded-pill fw-bold text-primary-theme shadow hover-lift">
                    APPLY FOR FRANCHISE <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </div>
</div>
