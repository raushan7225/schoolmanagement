<?php
// templates/event-details-template.php
$event_id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM frontend_events WHERE id = ? AND status = 1 LIMIT 1");
$stmt->execute([$event_id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    echo "<div class='container section-padding text-center min-vh-60 d-flex align-items-center justify-content-center flex-column'>
            <i class='fas fa-exclamation-circle fa-4x text-warning mb-4 opacity-25'></i>
            <h2 class='text-muted'>Event not found</h2>
            <p class='text-muted mb-4'>The event you are looking for may have been removed or expired.</p>
            <a href='".BASE_URL."events' class='btn btn-primary-theme rounded-pill px-4'>Back to Events</a>
          </div>";
    return;
}

$date = strtotime($event['event_date']);
?>

<section class="section-padding bg-light-theme min-vh-70">
    <div class="container">
        <div class="row gy-5">
            <!-- Main Content Area -->
            <div class="col-lg-8">
                <div class="bg-white p-4 p-md-5 rounded-4 shadow-sm border">
                    <!-- Feature Image -->
                    <div class="position-relative mb-5">
                        <img src="<?php echo $event['image'] ? BASE_URL . 'media/frontend/' . $event['image'] : 'https://placehold.co/1200x600/0b1c3d/white?text=' . urlencode($event['title']); ?>" 
                             class="img-fluid rounded-4 shadow-lg w-100" style="max-height: 500px; object-fit: cover;" alt="<?php echo htmlspecialchars($event['title']); ?>">
                        <div class="detail-date-badge shadow">
                            <span class="d-block fw-bold fs-3"><?php echo date('d', $date); ?></span>
                            <span class="d-block text-uppercase small fw-bold"><?php echo date('M', $date); ?></span>
                        </div>
                    </div>

                    <h2 class="fw-bold text-dark mb-4"><?php echo htmlspecialchars($event['title']); ?></h2>
                    
                    <div class="d-flex flex-wrap gap-3 mb-5 border-bottom pb-4">
                        <div class="d-flex align-items-center text-muted small">
                            <i class="fas fa-calendar-alt text-primary-theme me-2"></i> <?php echo date('l, F jS, Y', $date); ?>
                        </div>
                        <div class="d-flex align-items-center text-muted small">
                            <i class="fas fa-map-marker-alt text-danger me-2"></i> <?php echo htmlspecialchars($event['location'] ?: 'Main Campus'); ?>
                        </div>
                        <div class="d-flex align-items-center text-muted small">
                            <i class="fas fa-clock text-warning me-2"></i> 10:00 AM Onwards
                        </div>
                    </div>

                    <div class="event-description text-muted lh-lg fs-5">
                        <?php echo $event['description']; ?>
                    </div>
                    
                    <!-- Share Button (Visual only) -->
                    <div class="mt-5 pt-4 border-top">
                        <span class="text-dark fw-bold me-3">Share this event:</span>
                        <a href="#" class="btn btn-sm btn-outline-secondary rounded-circle me-2"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="btn btn-sm btn-outline-secondary rounded-circle me-2"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="btn btn-sm btn-outline-secondary rounded-circle me-2"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
            </div>

            <!-- Sidebar Area -->
            <div class="col-lg-4">
                <aside class="sticky-top" style="top: 120px; z-index: 10;">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
                        <div class="card-header bg-primary-theme text-white py-3 px-4 border-0">
                            <h5 class="mb-0 fw-bold">Event Quick Info</h5>
                        </div>
                        <div class="card-body p-0">
                            <!-- Info Items -->
                            <div class="info-item p-4 border-bottom d-flex align-items-center">
                                <div class="icon-box bg-primary-light text-primary-theme">
                                    <i class="fas fa-calendar-check fs-4"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="fw-bold mb-1">Scheduling</h6>
                                    <p class="text-muted small mb-0"><?php echo date('jS F Y', $date); ?></p>
                                </div>
                            </div>
                            <div class="info-item p-4 border-bottom d-flex align-items-center">
                                <div class="icon-box bg-danger-light text-danger">
                                    <i class="fas fa-location-dot fs-4"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="fw-bold mb-1">Venue Venue</h6>
                                    <p class="text-muted small mb-0"><?php echo htmlspecialchars($event['location'] ?: 'Main Campus, NEB Center'); ?></p>
                                </div>
                            </div>
                            <div class="info-item p-4 d-flex align-items-center">
                                <div class="icon-box bg-info-light text-info">
                                    <i class="fas fa-headset fs-4"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="fw-bold mb-1">Inquiries</h6>
                                    <p class="text-muted small mb-0"><?php echo getSetting('site_email', 'info@example.com'); ?></p>
                                </div>
                            </div>
                        </div>
                        <!-- Action Button -->
                        <a href="<?php echo BASE_URL; ?>contact-us" class="btn btn-primary-theme w-100 py-3 rounded-0 fw-bold transition-all">
                            INQUIRE ABOUT EVENT <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>

                    <!-- Additional Sidebar Widget -->
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                        <div class="card-body p-3">
                             <div class="p-4 bg-light rounded-4 text-center">
                                <i class="fas fa-map-marked-alt fa-3x text-muted mb-3 opacity-25"></i>
                                <h6 class="fw-bold">Virtual Map</h6>
                                <p class="text-muted small mb-0">Location coordinates available for registered attendees.</p>
                             </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</section>

<style>
.bg-light-theme { background-color: #f8faff; }
.min-vh-70 { min-height: 70vh; }
.text-primary-theme { color: var(--theme-primary-color, #1b1260); }
.bg-primary-theme { background: var(--theme-primary-color, #1b1260) !important; }
.btn-primary-theme { background: var(--theme-primary-color, #1b1260); color: white; border: none; }
.btn-primary-theme:hover { background: var(--theme-primary-hover-color, #0f0a3d); color: white; }

.detail-date-badge {
    position: absolute;
    top: 20px;
    left: 20px;
    background: white;
    color: var(--theme-primary-color, #1b1260);
    padding: 12px 18px;
    border-radius: 15px;
    text-align: center;
    min-width: 80px;
}

.icon-box {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.bg-primary-light { background-color: rgba(var(--theme-primary-rgb, 27, 18, 96), 0.1); }
.bg-danger-light { background-color: rgba(220, 53, 69, 0.1); }
.bg-info-light { background-color: rgba(13, 202, 240, 0.1); }

.info-item { transition: all 0.3s ease; }
.info-item:hover { background-color: #fbfcfe; }

.event-description p { margin-bottom: 1.5rem; }
</style>
