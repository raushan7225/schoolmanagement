<?php
// templates/contact-template.php
// Fetch Page Details for SEO or Title override if needed
$stmt = $pdo->prepare("SELECT * FROM frontend_pages WHERE slug = ? LIMIT 1");
$stmt->execute(['contact-us']);
$pageInfo = $stmt->fetch();
?>

<!-- Info Cards Section -->
<section class="section-padding bg-white animate-up">
    <div class="container">
        <div class="text-center mb-5">
            <h6 class="text-secondary-theme fw-bold text-uppercase mb-2 letter-spacing-1">Get In Touch</h6>
            <h2 class="fw-bold text-dark mb-4"><?php echo $pageInfo['title'] ?? 'Contact Us'; ?></h2>
            <div class="theme-separator mx-auto mb-5"></div>
        </div>

        <?php echo getSec($pdo, 'CONTACT_CARDS'); ?>
    </div>
</section>

<!-- Full Width Map Section -->
<section class="map-section w-100 animate-up" style="height: 450px; overflow: hidden; border-top: 1px solid #eee; border-bottom: 1px solid #eee;">
    <?php echo getSec($pdo, 'CONTACT_MAP'); ?>
</section>

<!-- Inquiry Form Section -->
<section class="section-padding bg-light-blue overflow-hidden">
    <div class="container">
        <?php echo getSec($pdo, 'CONTACT_FORM_HEADER'); ?>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="bg-white p-4 p-md-5 rounded-4 shadow-lg animate-right">
                    <form id="contactForm" class="row g-4" novalidate>
                        <input type="hidden" name="action" value="submit_contact">
                        
                        <!-- Row 1: Name, Email, Phone -->
                        <div class="col-md-4">
                            <div class="form-group premium-group">
                                <label class="small fw-bold mb-2">First Name</label>
                                <div class="input-wrapper">
                                    <input type="text" class="form-control" name="name" placeholder="Full Name" required>
                                    <i class="fas fa-user input-icon"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group premium-group">
                                <label class="small fw-bold mb-2">Email Address</label>
                                <div class="input-wrapper">
                                    <input type="email" class="form-control" name="email" placeholder="Email" required>
                                    <i class="fas fa-envelope input-icon"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group premium-group">
                                <label class="small fw-bold mb-2">Phone No.</label>
                                <div class="input-wrapper">
                                    <input type="tel" class="form-control" name="phone" placeholder="Phone No." required>
                                    <i class="fas fa-phone-alt input-icon"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Row 2: Message -->
                        <div class="col-12 mt-2">
                            <div class="form-group premium-group">
                                <label class="small fw-bold mb-2">Write comments</label>
                                <div class="input-wrapper">
                                    <textarea class="form-control" name="message" rows="4" placeholder="Your Message" required style="min-height: 120px;"></textarea>
                                    <i class="fas fa-pencil-alt input-icon mt-2"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="col-12 text-center mt-5">
                            <button type="submit" class="btn btn-primary-theme px-5 py-3 rounded-pill fw-bold shadow-sm" id="btn-submit">
                                MAKE A REQUEST <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.bg-light-blue { background-color: #f0f7ff; }
.theme-separator { width: 60px; height: 4px; background: var(--theme-secondary-color, #ff6a1a); border-radius: 2px; }
.text-primary-theme { color: var(--theme-primary-color, #1b1260); }
.bg-primary-theme { background: var(--theme-primary-color, #1b1260) !important; }
.bg-secondary-theme { background: var(--theme-secondary-color, #ff6a1a) !important; }

.btn-primary-theme { background: var(--theme-primary-color, #1b1260); color: white; border: none; transition: all 0.3s ease; }
.btn-primary-theme:hover { background: var(--theme-primary-hover-color, #0f0a3d); color: white; transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.15) !important; }

.contact-info-card { background: white; transition: all 0.3s ease; }
.contact-info-card:hover { transform: translateY(-10px); box-shadow: 0 15px 30px rgba(0,0,0,0.08) !important; }
.hover-border-secondary:hover { border-color: var(--theme-secondary-color, #ff6a1a) !important; }
.hover-border-primary:hover { border-color: var(--theme-primary-color, #1b1260) !important; }
.hover-border-info:hover { border-color: #0dcaf0 !important; }

.icon-circle-large { width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
.bg-primary-light { background-color: rgba(var(--theme-primary-rgb, 27, 18, 96), 0.1); }
.bg-secondary-light { background-color: rgba(var(--theme-secondary-rgb, 255, 106, 26), 0.1); }
.bg-info-light { background-color: rgba(13, 202, 240, 0.1); }

.premium-group .input-wrapper { position: relative; }
.premium-group .form-control { 
    background: #fff; 
    border: 1px solid #e0e0e0; 
    padding: 15px 45px 15px 20px; 
    border-radius: 8px; 
    transition: all 0.3s ease;
    font-weight: 500;
}
.premium-group .form-control:focus { 
    border-color: var(--theme-primary-color); 
    box-shadow: 0 0 0 4px rgba(var(--theme-primary-rgb), 0.1); 
}
.premium-group .input-icon { 
    position: absolute; 
    right: 20px; 
    top: 50%; 
    transform: translateY(-50%); 
    color: #adb5bd; 
    font-size: 0.9rem;
    pointer-events: none;
}
.premium-group textarea.form-control { padding-top: 15px; }

/* Animations */
.animate-up { animation: fadeInUp 0.8s ease-out both; }
.animate-right { animation: fadeInRight 0.8s ease-out both; }
@keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
@keyframes fadeInRight { from { opacity: 0; transform: translateX(30px); } to { opacity: 1; transform: translateX(0); } }

.x-small { font-size: 0.75rem; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('contactForm');
    const HANDLER = '<?php echo BASE_URL; ?>ajax/contact_handler.php';
    
    if(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if(!form.checkValidity()) {
                form.classList.add('was-validated');
                return;
            }
            
            const btn = document.getElementById('btn-submit');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>SENDING...';
            
            fetch(HANDLER, {
                method: 'POST',
                body: new FormData(form)
            })
            .then(r => r.json())
            .then(res => {
                if(res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Inquiry Sent!',
                        text: res.message,
                        timer: 3000,
                        showConfirmButton: false
                    });
                    form.reset();
                    form.classList.remove('was-validated');
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            })
            .catch(err => Swal.fire('Error', 'An unexpected error occurred.', 'error'))
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        });
    }
});
</script>
