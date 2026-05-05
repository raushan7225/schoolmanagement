<!-- Footer -->
<footer class="text-white pt-5 bg-primary-theme" style="padding-bottom: 20px; border-top: 5px solid #ff6a1a;">
    <div class="container">
        <div class="row gy-4 pb-4">
            <!-- Col 1: About -->
            <div class="col-lg-4 col-md-6 pe-lg-4">
                <img src="<?php echo BASE_URL; ?><?php echo getSetting('theme_logo', 'media/general/logo.jpeg'); ?>" alt="Board Logo" class="mb-3 bg-white px-2 rounded" height="65" onerror="this.onerror=null; this.src='https://placehold.co/250x65/FFF/0d2b52?text=BOARD+LOGO';">
                <p class="text-white-50 mt-2" style="font-size: 14px;">
                    <?php echo getSetting('footer_about_text', 'National examination Board of Open Schooling and skill education creates opportunities for learners to complete their Secondary and Senior Secondary education through flexible open schooling methods and comprehensive vocational programs.'); ?>
                </p>
                <div class="social-links mt-4">
                    <?php
                    $socials = ['facebook', 'twitter', 'instagram', 'youtube', 'linkedin'];
                    foreach ($socials as $s):
                        $url = getSetting('social_' . $s);
                        if ($url): ?>
                            <a href="<?php echo $url; ?>" target="_blank" class="text-white bg-secondary bg-opacity-25 rounded-circle p-2 me-2 d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;"><i class="fab fa-<?php echo $s; ?>"></i></a>
                    <?php endif;
                    endforeach; ?>
                </div>
            </div>

            <!-- Col 2: Quick Links -->
            <div class="col-lg-2 col-md-6">
                <h5 class="text-uppercase fw-bold mb-4 text-secondary-theme">Quick Links</h5>
                <ul class="list-unstyled footer-links">
                    <li class="mb-3"><a href="<?php echo BASE_URL; ?>about-us" class="text-white text-decoration-none">About Us</a></li>
                    <li class="mb-3"><a href="<?php echo BASE_URL; ?>recognition" class="text-white text-decoration-none">Recognition</a></li>
                    <li class="mb-3"><a href="<?php echo BASE_URL; ?>center-list" class="text-white text-decoration-none">Find Authorized Institutes</a></li>
                    <li class="mb-3"><a href="<?php echo BASE_URL; ?>contact-us" class="text-white text-decoration-none">Contact Us</a></li>
                </ul>
            </div>

            <!-- Col 3: Legal Pages -->
            <div class="col-lg-3 col-md-6">
                <h5 class="text-uppercase fw-bold mb-4 text-secondary-theme">Legal Pages</h5>
                <ul class="list-unstyled footer-links">
                    <li class="mb-3"><a href="<?php echo BASE_URL; ?>downloads" class="text-white text-decoration-none">Prospectus & Downloads</a></li>
                    <li class="mb-3"><a href="<?php echo BASE_URL; ?>disclaimer" class="text-white text-decoration-none">Disclaimer</a></li>
                    <li class="mb-3"><a href="<?php echo BASE_URL; ?>privacy-policy" class="text-white text-decoration-none">Privacy Policy</a></li>
                    <li class="mb-3"><a href="<?php echo BASE_URL; ?>terms-and-conditions" class="text-white text-decoration-none">Terms and Conditions</a></li>
                    <li class="mb-3"><a href="<?php echo BASE_URL; ?>return-refund-policy" class="text-white text-decoration-none">Return and Refund Policy</a></li>
                </ul>
            </div>

            <!-- Col 4: Contact -->
            <div class="col-lg-3 col-md-6">
                <h5 class="text-uppercase fw-bold mb-4 text-secondary-theme">Contact Info</h5>
                <ul class="list-unstyled text-white-50" style="font-size: 14px;">
                    <li class="mb-3 d-flex">
                        <i class="fas fa-map-marker-alt text-secondary-theme fs-5 me-3 mt-1"></i>
                        <span><?php echo nl2br(getSetting('site_address', 'Your Address Here')); ?></span>
                    </li>
                    <li class="mb-3 d-flex align-items-center">
                        <i class="fas fa-phone-alt text-secondary-theme fs-5 me-3"></i>
                        <span><?php echo getSetting('site_phone', '+91 00000 00000'); ?></span>
                    </li>
                    <li class="mb-3 d-flex align-items-center">
                        <i class="fas fa-envelope text-secondary-theme fs-5 me-3"></i>
                        <span><?php echo getSetting('site_email', 'info@example.com'); ?></span>
                    </li>
                </ul>
            </div>
        </div>
        <hr class="border-light mt-2 mb-4 opacity-10">
        <div class="row text-center">
            <div class="col-12">
                <p class="text-white mb-0" style="font-size: 14px;"><?php echo getSetting('footer_copyright', '&copy; 2026 School Board. All Rights Reserved.'); ?> | Developed by <a href="https://www.nsprowebtech.com" class="text-white">NS Pro Web Tech</a></p>
            </div>
        </div>
    </div>
</footer>