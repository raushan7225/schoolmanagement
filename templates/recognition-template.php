<section class="section-padding bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="mb-5 text-center">
                    <h2 class="fw-bold text-primary-theme"><?php echo getSec($pdo, 'RECOGNITION_TITLE', 'title') ?: 'Our Accreditations'; ?></h2>
                    <p class="text-muted"><?php echo getSec($pdo, 'RECOGNITION_DESC', 'content') ?: 'National Examination Board is recognized by various government and nodal bodies.'; ?></p>
                </div>

                <!-- Recognition List -->
                <div class="recognition-wrapper">
                    <?php
                    $stmt = $pdo->query("SELECT * FROM frontend_recognitions WHERE status = 1 ORDER BY sort_order ASC, title ASC");
                    $recognitions = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if(empty($recognitions)):
                    ?>
                        <div class="alert alert-info text-center">No recognition documents available at the moment.</div>
                    <?php else: ?>
                        <?php foreach($recognitions as $r): 
                            $is_pdf = str_ends_with(strtolower($r['file_path']), '.pdf');
                        ?>
                        <div class="recognition-item">
                            <div class="recognition-icon"><i class="fas fa-reply"></i></div>
                            <p class="recognition-text mb-0">
                                <?php echo htmlspecialchars($r['title']); ?> 
                                <a href="<?php echo BASE_URL; ?>media/frontend/<?php echo $r['file_path']; ?>" target="_blank" class="recognition-link fw-bold text-secondary-theme text-decoration-none ms-2">– Click Here</a>
                            </p>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="alert alert-info mt-5 shadow-sm rounded-4 border-0 p-4" style="background-color: #eaf4fd;">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-info-circle fs-3 text-primary-theme me-3"></i>
                        <div>
                            <h6 class="fw-bold mb-1 text-primary-theme"><?php echo getSec($pdo, 'RECOGNITION_INFO_TITLE', 'title') ?: 'Verify Certificates Online'; ?></h6>
                            <p class="mb-0 small text-muted"><?php echo getSec($pdo, 'RECOGNITION_INFO_DESC', 'content') ?: 'You can verify our registration status by visiting the official portals of the respective departments using our registration numbers.'; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Accreditation Logos -->
        <div class="mt-10 pt-5 border-top text-center" style="margin-top: 60px;">
            <h5 class="text-muted text-uppercase small fw-bold mb-5" style="letter-spacing: 2px;">Other Affiliations</h5>
            <div class="d-flex flex-wrap justify-content-center gap-5 opacity-75">
                <?php
                $affStmt = $pdo->query("SELECT * FROM frontend_affiliations WHERE status = 1 LIMIT 8");
                $affs = $affStmt->fetchAll(PDO::FETCH_ASSOC);
                foreach($affs as $aff):
                ?>
                    <img src="<?php echo BASE_URL; ?>media/frontend/<?php echo $aff['logo']; ?>" alt="<?php echo $aff['name']; ?>" style="height: 50px; filter: grayscale(100%);" class="hover-grayscale-0 transition-all">
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<style>
.recognition-wrapper {
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 15px;
}
.recognition-item {
    display: flex;
    align-items: center;
    background-color: #fff;
    padding: 15px 25px;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.03);
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
}
.recognition-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    border-left-color: var(--theme-secondary);
}
.recognition-icon {
    width: 35px;
    height: 35px;
    background-color: rgba(26, 26, 84, 0.1); /* Primary theme light */
    color: var(--theme-primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 20px;
    flex-shrink: 0;
    transform: scaleX(-1) rotate(180deg); /* Flip the reply icon to point right like in the ref */
}
.recognition-text {
    font-size: 1.05rem;
    color: #444;
}
.recognition-link:hover {
    text-decoration: underline !important;
}
.hover-grayscale-0:hover {
    filter: grayscale(0%) !important;
}
</style>
