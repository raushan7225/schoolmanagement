<?php
// templates/events-template.php
$stmt = $pdo->query("SELECT * FROM frontend_events WHERE status = 1 ORDER BY event_date DESC");
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="section-padding bg-light-theme min-vh-60">
    <div class="container">
        <!-- Section Heading -->
        <div class="text-center mb-5">
            <h2 class="fw-bold text-dark mb-2">Upcoming & Past Events</h2>
            <div class="theme-separator mx-auto mb-3"></div>
            <p class="text-muted mx-auto" style="max-width: 600px;">Stay updated with the latest happenings, workshops, and celebrations at our institution.</p>
        </div>

        <div class="row gy-4 gx-4">
            <?php if(!empty($events)): ?>
                <?php foreach($events as $e): 
                    $date = strtotime($e['event_date']);
                    $day = date('d', $date);
                    $month = date('M', $date);
                    $year = date('Y', $date);
                ?>
                <!-- Event Card -->
                <div class="col-lg-4 col-md-6">
                    <div class="card event-card h-100 border-0 rounded-4 overflow-hidden shadow-sm hover-lift transition-all bg-white">
                        <div class="position-relative">
                            <img src="<?php echo $e['image'] ? BASE_URL . 'media/frontend/' . $e['image'] : 'https://placehold.co/600x400/0b1c3d/white?text=' . urlencode($e['title']); ?>" 
                                 class="card-img-top" style="height: 230px; object-fit: cover;" alt="<?php echo htmlspecialchars($e['title']); ?>">
                            
                            <!-- Date Badge -->
                            <div class="event-date-badge shadow-sm">
                                <span class="d-block fw-bold fs-4"><?php echo $day; ?></span>
                                <span class="d-block text-uppercase small fw-bold"><?php echo $month; ?></span>
                                <span class="d-block x-small opacity-75"><?php echo $year; ?></span>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-primary-light text-primary-theme px-3 py-1 rounded-pill small me-2">
                                    <i class="fas fa-tag me-1 x-small"></i> Event
                                </span>
                                <small class="text-muted"><i class="fas fa-map-marker-alt me-1"></i> <?php echo htmlspecialchars($e['location'] ?: 'Main Campus'); ?></small>
                            </div>
                            <h5 class="card-title fw-bold text-dark mb-3 mt-2 line-clamp-2" style="height: 3rem; overflow: hidden;"><?php echo htmlspecialchars($e['title']); ?></h5>
                            <p class="card-text text-muted small mb-4 line-clamp-3">
                                <?php echo substr(strip_tags($e['description']), 0, 120); ?>...
                            </p>
                            <div class="border-top pt-3 d-flex justify-content-between align-items-center">
                                <a href="<?php echo BASE_URL; ?>event-details/<?php echo $e['id']; ?>" class="btn btn-link text-primary-theme p-0 fw-bold text-decoration-none">
                                    READ MORE <i class="fas fa-arrow-right ms-1 small"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-calendar-times fa-4x text-muted opacity-25"></i>
                    </div>
                    <h4 class="text-muted">No upcoming events scheduled.</h4>
                    <p class="text-muted small">Please check back later for announcements.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<style>
.bg-light-theme { background-color: #f8faff; }
.theme-separator { width: 60px; height: 4px; background: var(--theme-primary-color, #1b1260); border-radius: 2px; }
.min-vh-60 { min-height: 60vh; }

.event-card {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
.event-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important;
}

.event-date-badge {
    position: absolute;
    bottom: -25px;
    right: 25px;
    background: var(--theme-primary-color, #1b1260);
    color: white;
    padding: 10px 15px;
    border-radius: 12px;
    text-align: center;
    min-width: 70px;
    z-index: 2;
    border: 3px solid #fff;
}

.bg-primary-light { background-color: rgba(var(--theme-primary-rgb, 27, 18, 96), 0.1); }
.text-primary-theme { color: var(--theme-primary-color, #1b1260); }

.x-small { font-size: 0.7rem; }
.line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.line-clamp-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }

@media (max-width: 768px) {
    .event-date-badge { right: 15px; padding: 8px 12px; min-width: 60px; }
}
</style>
