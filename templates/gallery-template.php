<?php
// templates/gallery-template.php
$categories = $pdo->query("SELECT * FROM frontend_gallery_categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Optimized query to handle missing status columns gracefully
try {
    $images = $pdo->query("SELECT g.*, c.name as category_name FROM frontend_gallery g 
                          LEFT JOIN frontend_gallery_categories c ON g.category_id = c.id 
                          WHERE g.type = 'image' 
                          ORDER BY g.id DESC")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Fallback if table doesn't exist or other issues
    $images = [];
}
?>

<!-- GLightbox CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" />

<section class="section-padding bg-light-theme">
    <div class="container">
        <!-- Section Heading -->
        <div class="text-center mb-5">
            <h2 class="fw-bold text-dark mb-2">Our Campus Gallery</h2>
            <div class="theme-separator mx-auto mb-3"></div>
            <p class="text-muted mx-auto" style="max-width: 600px;">Explore the vibrant life and infrastructure at our institution through these moments captured on campus.</p>
        </div>

        <!-- Filter Tabs -->
        <div class="gallery-filter-tabs text-center mb-5">
            <button class="filter-btn active" data-filter="all">All Photos</button>
            <?php foreach($categories as $cat): ?>
                <button class="filter-btn" data-filter="<?php echo $cat['id']; ?>">
                    <?php echo htmlspecialchars($cat['name']); ?>
                </button>
            <?php endforeach; ?>
        </div>

        <!-- Gallery Grid -->
        <div class="row g-4" id="galleryGrid">
            <?php if(!empty($images)): ?>
                <?php foreach($images as $img): ?>
                <div class="col-lg-4 col-md-6 gallery-item" data-category="<?php echo $img['category_id']; ?>">
                    <div class="gallery-card shadow-sm">
                        <a href="<?php echo BASE_URL . 'media/frontend/' . $img['media_file']; ?>" class="glightbox" data-gallery="gallery1" data-title="<?php echo htmlspecialchars($img['title']); ?>" data-description="<?php echo htmlspecialchars($img['category_name']); ?>">
                            <div class="gallery-thumb rounded-4 overflow-hidden position-relative">
                                <img src="<?php echo BASE_URL . 'media/frontend/' . $img['media_file']; ?>" class="img-fluid w-100" style="height: 280px; object-fit: cover;" alt="<?php echo htmlspecialchars($img['title']); ?>">
                                <div class="gallery-overlay">
                                    <div class="overlay-content">
                                        <div class="zoom-icon"><i class="fas fa-expand"></i></div>
                                        <h6 class="text-white mb-1 mt-3"><?php echo htmlspecialchars($img['title']); ?></h6>
                                        <span class="badge bg-white text-primary rounded-pill px-3 py-1"><?php echo htmlspecialchars($img['category_name']); ?></span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-images fa-4x text-muted opacity-25"></i>
                    </div>
                    <h4 class="text-muted">No images found in the gallery.</h4>
                    <p class="text-muted small">Please check back later for updates.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<style>
.bg-light-theme { background-color: #f8faff; }
.theme-separator { width: 60px; height: 4px; background: var(--theme-primary-color, #1b1260); border-radius: 2px; }

.gallery-filter-tabs .filter-btn {
    border: none;
    background: #fff;
    color: #444;
    font-weight: 600;
    padding: 10px 25px;
    border-radius: 50px;
    margin: 5px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.gallery-filter-tabs .filter-btn.active, 
.gallery-filter-tabs .filter-btn:hover {
    background: var(--theme-primary-color, #1b1260) !important;
    color: #ffffff !important;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(var(--theme-primary-rgb, 27, 18, 96), 0.3);
}

.gallery-card {
    background: white;
    border-radius: 1rem;
    padding: 10px;
    transition: all 0.4s ease;
}

.gallery-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
}

.gallery-thumb {
    position: relative;
    cursor: pointer;
}

.gallery-overlay {
    position: absolute;
    top: 0; left: 0; width: 100%; height: 100%;
    background: linear-gradient(to bottom, rgba(var(--theme-primary-rgb), 0) 0%, rgba(var(--theme-primary-rgb), 0.9) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all 0.4s ease;
}


.gallery-card:hover .gallery-overlay {
    opacity: 1;
}

.overlay-content {
    text-align: center;
    transform: translateY(20px);
    transition: all 0.4s ease;
}

.gallery-card:hover .overlay-content {
    transform: translateY(0);
}

.zoom-icon {
    width: 50px;
    height: 50px;
    background: rgba(255,255,255,0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
    margin: 0 auto;
    border: 1px solid rgba(255,255,255,0.3);
    backdrop-filter: blur(5px);
}

.gallery-item {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
</style>

<!-- GLightbox JS -->
<script src="https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lightbox
    const lightbox = GLightbox({
        selector: '.glightbox',
        touchNavigation: true,
        loop: true
    });

    // Filter Logic
    const filterBtns = document.querySelectorAll('.filter-btn');
    const galleryItems = document.querySelectorAll('.gallery-item');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            filterBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const filterValue = btn.getAttribute('data-filter');

            galleryItems.forEach(item => {
                if (filterValue === 'all' || item.getAttribute('data-category') === filterValue) {
                    item.style.display = 'block';
                    setTimeout(() => { 
                        item.style.opacity = '1'; 
                        item.style.transform = 'scale(1)'; 
                    }, 50);
                } else {
                    item.style.opacity = '0';
                    item.style.transform = 'scale(0.9)';
                    setTimeout(() => { 
                        item.style.display = 'none'; 
                    }, 400);
                }
            });
            
            // Reload lightbox to only include visible items if needed, 
            // though usually we want all to be accessible in a set.
        });
    });
});
</script>

