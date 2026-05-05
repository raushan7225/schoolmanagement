<?php
// admin/general-settings.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");

// Load all settings into an associative array
$settings = $pdo->query("SELECT setting_key, setting_value FROM site_settings WHERE status = 1")->fetchAll(PDO::FETCH_KEY_PAIR);
function s($settings, $key, $default = '') {
    return htmlspecialchars($settings[$key] ?? $default);
}
?>

<div class="pagetitle">
    <h1>General Settings</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">System Settings</li>
            <li class="breadcrumb-item active">General Settings</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <i class="fas fa-gears text-white me-2"></i>
                    <h5 class="card-title text-white mb-0">Platform Configuration Hub</h5>
                </div>
                <div class="card-body pt-3">
                    <div id="settings-alert" class="d-none mb-3"></div>

                    <!-- Nav Tabs -->
                    <ul class="nav nav-tabs nav-tabs-bordered mb-4" id="settingsTab" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active fw-bold" id="branding-tab" data-bs-toggle="tab" data-bs-target="#tab-branding"><i class="fas fa-palette me-2 text-primary"></i>Branding & Colors</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-bold" id="typography-tab" data-bs-toggle="tab" data-bs-target="#tab-typography"><i class="fas fa-font me-2 text-primary"></i>Typography</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-bold" id="layout-tab" data-bs-toggle="tab" data-bs-target="#tab-layout"><i class="fas fa-layer-group me-2 text-primary"></i>Layout & UI</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-bold" id="contact-tab" data-bs-toggle="tab" data-bs-target="#tab-contact"><i class="fas fa-address-book me-2 text-primary"></i>Contact Info</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-bold" id="security-tab" data-bs-toggle="tab" data-bs-target="#tab-security"><i class="fas fa-shield-halved me-2 text-primary"></i>Security & Google</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-bold" id="email-tab" data-bs-toggle="tab" data-bs-target="#tab-email"><i class="fas fa-paper-plane me-2 text-primary"></i>SMTP Email</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-bold" id="social-tab" data-bs-toggle="tab" data-bs-target="#tab-social"><i class="fas fa-share-nodes me-2 text-primary"></i>Social & Footer</button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- ① Branding & Colors -->
                        <div class="tab-pane fade show active" id="tab-branding">
                            <form id="form-colors" data-group="colors">
                                <div class="row g-4">
                                    <div class="col-lg-8">
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold">Primary Color</label>
                                                <div class="input-group">
                                                    <input type="color" class="form-control form-control-color theme-color-picker" name="theme_primary_color" id="pick_primary" value="<?php echo s($settings,'theme_primary_color','#1b1260'); ?>">
                                                    <input type="text" class="form-control color-hex-input" data-target="pick_primary" value="<?php echo s($settings,'theme_primary_color','#1b1260'); ?>" maxlength="7">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold">Primary Hover</label>
                                                <div class="input-group">
                                                    <input type="color" class="form-control form-control-color theme-color-picker" name="theme_primary_hover_color" id="pick_primary_hover" value="<?php echo s($settings,'theme_primary_hover_color','#0f0a3d'); ?>">
                                                    <input type="text" class="form-control color-hex-input" data-target="pick_primary_hover" value="<?php echo s($settings,'theme_primary_hover_color','#0f0a3d'); ?>" maxlength="7">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold">Secondary Color</label>
                                                <div class="input-group">
                                                    <input type="color" class="form-control form-control-color theme-color-picker" name="theme_secondary_color" id="pick_secondary" value="<?php echo s($settings,'theme_secondary_color','#ff6a1a'); ?>">
                                                    <input type="text" class="form-control color-hex-input" data-target="pick_secondary" value="<?php echo s($settings,'theme_secondary_color','#ff6a1a'); ?>" maxlength="7">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold">Success Color</label>
                                                <div class="input-group">
                                                    <input type="color" class="form-control form-control-color theme-color-picker" name="theme_success_color" id="pick_success" value="<?php echo s($settings,'theme_success_color','#0b933a'); ?>">
                                                    <input type="text" class="form-control color-hex-input" data-target="pick_success" value="<?php echo s($settings,'theme_success_color','#0b933a'); ?>" maxlength="7">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold">Page Background</label>
                                                <div class="input-group">
                                                    <input type="color" class="form-control form-control-color theme-color-picker" name="theme_body_bg_color" id="pick_bg" value="<?php echo s($settings,'theme_body_bg_color','#f7f9fc'); ?>">
                                                    <input type="text" class="form-control color-hex-input" data-target="pick_bg" value="<?php echo s($settings,'theme_body_bg_color','#f7f9fc'); ?>" maxlength="7">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold">Heading Text</label>
                                                <div class="input-group">
                                                    <input type="color" class="form-control form-control-color theme-color-picker" name="theme_heading_text_color" id="pick_heading" value="<?php echo s($settings,'theme_heading_text_color','#061c3a'); ?>">
                                                    <input type="text" class="form-control color-hex-input" data-target="pick_heading" value="<?php echo s($settings,'theme_heading_text_color','#061c3a'); ?>" maxlength="7">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold">Body Text Color</label>
                                                <div class="input-group">
                                                    <input type="color" class="form-control form-control-color theme-color-picker" name="theme_body_text_color" id="pick_body_text" value="<?php echo s($settings,'theme_body_text_color','#444444'); ?>">
                                                    <input type="text" class="form-control color-hex-input" data-target="pick_body_text" value="<?php echo s($settings,'theme_body_text_color','#444444'); ?>" maxlength="7">
                                                </div>
                                            </div>
                                            <div class="col-md-8 text-end align-self-end">
                                                <button type="button" id="btn-reset-colors" class="btn btn-outline-secondary btn-sm"><i class="fas fa-undo me-1"></i>Reset Defaults</button>
                                                <button type="submit" class="btn btn-primary btn-sm px-4 ms-2"><i class="fas fa-save me-1"></i>Save Colors</button>
                                            </div>
                                        </div>

                                        <div class="row mt-5 g-4">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Site Logo (Main)</label>
                                                <div class="brand-preview mb-2 p-2 rounded border bg-light text-center">
                                                    <img src="<?php echo $BASE_URL . s($settings,'theme_logo','media/general/logo.jpeg'); ?>" id="logo-preview-img" style="max-height: 50px;">
                                                </div>
                                                <div id="logo-dropzone" class="dropzone p-4 rounded-3 border-dashed small text-center">
                                                    <i class="fas fa-cloud-upload-alt text-primary mb-2"></i>
                                                    <div class="dz-message small">Click or Drop Logo</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Site Favicon</label>
                                                <div class="brand-preview mb-2 p-2 rounded border bg-light text-center">
                                                    <img src="<?php echo $BASE_URL . s($settings,'theme_favicon','media/general/favicon.png'); ?>" id="favicon-preview-img" style="max-height: 40px;">
                                                </div>
                                                <div id="favicon-dropzone" class="dropzone p-4 rounded-3 border-dashed small text-center">
                                                    <i class="fas fa-image text-primary mb-2"></i>
                                                    <div class="dz-message small">Click or Drop Favicon</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="card bg-light border-0 shadow-sm h-100">
                                            <div class="card-body p-4 text-center">
                                                <h6 class="fw-bold mb-4 text-primary"><i class="fas fa-eye me-2"></i>Live Theme Preview</h6>
                                                <div class="preview-mockup rounded-3 shadow border overflow-hidden bg-white mx-auto" style="max-width: 250px; height: 350px;">
                                                    <div class="preview-header p-2 text-white d-flex align-items-center justify-content-between" id="preview-mock-header" style="background:<?php echo s($settings,'theme_primary_color','#1b1260'); ?>;">
                                                        <div style="width:40px; background:rgba(255,255,255,0.2); height:8px;"></div>
                                                        <div class="rounded-circle" style="width:15px; height:15px; background:rgba(255,255,255,0.2);"></div>
                                                    </div>
                                                    <div class="p-3 text-start">
                                                        <div class="mb-2" style="width:60%; height:12px; background:#eee;"></div>
                                                        <div class="row g-2 mb-3">
                                                            <div class="col-6"><div class="rounded p-1" style="background:rgba(0,0,0,0.03); height:40px;"></div></div>
                                                            <div class="col-6"><div class="rounded p-1" style="background:rgba(0,0,0,0.03); height:40px;"></div></div>
                                                        </div>
                                                        <button type="button" id="preview-mock-btn" class="btn w-100 text-white fw-bold mb-3 btn-sm" style="background:<?php echo s($settings,'theme_secondary_color','#ff6a1a'); ?>; border:none;">PRIMARY ACTION</button>
                                                        <div class="badge rounded-pill px-2 py-1" id="preview-mock-badge" style="background:rgba(25, 135, 84, 0.1); color:#198754; border:1px solid #198754; font-size:10px;">Status Badge</div>
                                                    </div>
                                                </div>
                                                <p class="mt-3 small text-muted">Preview updates as you pick colors.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- ② Typography -->
                        <div class="tab-pane fade" id="tab-typography">
                            <form id="form-typography" data-group="typography">
                                <div class="row g-4">
                                    <?php 
                                    $topFonts = [
                                        'Inter', 'Poppins', 'Outfit', 'Plus Jakarta Sans', 'Sora', 
                                        'Montserrat', 'Roboto', 'Open Sans', 'Lato', 'Mulish',
                                        'Raleway', 'Nunito Sans', 'Work Sans', 'Quicksand', 'Kanit',
                                        'Playfair Display', 'Merriweather', 'Ubuntu', 'Heebo', 'Assistant'
                                    ];
                                    sort($topFonts);
                                    $currentHeading = s($settings,'theme_font_heading','Poppins');
                                    $currentBody = s($settings,'theme_font_body','Inter');
                                    ?>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Heading Font</label>
                                        <div class="input-group">
                                            <select name="theme_font_heading" class="form-select font-selector" id="heading_font_select">
                                                <?php foreach($topFonts as $f): ?>
                                                    <option value="<?php echo $f; ?>" <?php echo ($currentHeading == $f) ? 'selected' : ''; ?>><?php echo $f; ?></option>
                                                <?php endforeach; ?>
                                                <option value="custom" <?php echo (!in_array($currentHeading, $topFonts)) ? 'selected' : ''; ?>>Custom...</option>
                                            </select>
                                            <input type="text" id="heading_font_custom" class="form-control d-none" value="<?php echo $currentHeading; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Body Font</label>
                                        <div class="input-group">
                                            <select name="theme_font_body" class="form-select font-selector" id="body_font_select">
                                                <?php foreach($topFonts as $f): ?>
                                                    <option value="<?php echo $f; ?>" <?php echo ($currentBody == $f) ? 'selected' : ''; ?>><?php echo $f; ?></option>
                                                <?php endforeach; ?>
                                                <option value="custom" <?php echo (!in_array($currentBody, $topFonts)) ? 'selected' : ''; ?>>Custom...</option>
                                            </select>
                                            <input type="text" id="body_font_custom" class="form-control d-none" value="<?php echo $currentBody; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Base Font Size</label>
                                        <input type="text" name="theme_font_size_base" class="form-control" value="<?php echo s($settings,'theme_font_size_base','16px'); ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">H1 Size</label>
                                        <input type="text" name="theme_font_size_h1" class="form-control" value="<?php echo s($settings,'theme_font_size_h1','2.2rem'); ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">H2 Size</label>
                                        <input type="text" name="theme_font_size_h2" class="form-control" value="<?php echo s($settings,'theme_font_size_h2','1.8rem'); ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Heading Weight</label>
                                        <select name="theme_font_weight_heading" class="form-select">
                                            <option value="400" <?php echo s($settings,'theme_font_weight_heading')=='400'?'selected':''; ?>>400</option>
                                            <option value="600" <?php echo s($settings,'theme_font_weight_heading')=='600'?'selected':''; ?>>600</option>
                                            <option value="700" <?php echo s($settings,'theme_font_weight_heading')=='700'?'selected':''; ?>>700</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Line Height</label>
                                        <input type="text" name="theme_line_height" class="form-control" value="<?php echo s($settings,'theme_line_height','1.65'); ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Letter Spacing</label>
                                        <input type="text" name="theme_letter_spacing" class="form-control" value="<?php echo s($settings,'theme_letter_spacing','0.3px'); ?>">
                                    </div>
                                    <div class="col-md-6 text-end align-self-end">
                                        <button type="submit" class="btn btn-primary px-5"><i class="fas fa-save me-1"></i>Save Typography</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- ③ Layout & UI -->
                        <div class="tab-pane fade" id="tab-layout">
                            <form id="form-layout" data-group="layout">
                                <div class="row g-4">
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Border Radius (Sm)</label>
                                        <input type="text" name="theme_border_radius" class="form-control" value="<?php echo s($settings,'theme_border_radius','8px'); ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Border Radius (Lg)</label>
                                        <input type="text" name="theme_border_radius_lg" class="form-control" value="<?php echo s($settings,'theme_border_radius_lg','12px'); ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Section Padding</label>
                                        <input type="text" name="theme_section_padding" class="form-control" value="<?php echo s($settings,'theme_section_padding','80px'); ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Sidebar Width</label>
                                        <input type="text" name="theme_admin_sidebar_width" class="form-control" value="<?php echo s($settings,'theme_admin_sidebar_width','300px'); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Card Shadow</label>
                                        <input type="text" name="theme_card_shadow" class="form-control" value="<?php echo s($settings,'theme_card_shadow','0px 5px 30px rgba(0,0,0,0.08)'); ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Header Height</label>
                                        <input type="text" name="theme_admin_header_height" class="form-control" value="<?php echo s($settings,'theme_admin_header_height','70px'); ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Transition Speed</label>
                                        <input type="text" name="theme_transition_speed" class="form-control" value="<?php echo s($settings,'theme_transition_speed','0.3s'); ?>">
                                    </div>

                                    <div class="col-12 mt-4"><h6 class="fw-bold text-primary border-bottom pb-2">Admin Modal Styling</h6></div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Modal Backdrop Blur</label>
                                        <input type="text" name="theme_modal_backdrop_blur" class="form-control" value="<?php echo s($settings,'theme_modal_backdrop_blur','8px'); ?>">
                                    </div>
                                    <div class="col-md-9">
                                        <label class="form-label fw-bold">Modal Header BG (Linear Gradient)</label>
                                        <input type="text" name="theme_modal_header_bg" class="form-control" value="<?php echo s($settings,'theme_modal_header_bg'); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Close Icon (FA)</label>
                                        <input type="text" name="theme_modal_close_icon" class="form-control" value="<?php echo s($settings,'theme_modal_close_icon','fas fa-times'); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Close Color</label>
                                        <input type="text" name="theme_modal_close_color" class="form-control" value="<?php echo s($settings,'theme_modal_close_color','#ffffff'); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Close Size</label>
                                        <input type="text" name="theme_modal_close_size" id="input_modal_close_size" class="form-control modal-preview-trigger" value="<?php echo s($settings,'theme_modal_close_size','18px'); ?>">
                                    </div>
                                    <div class="col-12 mt-4">
                                        <div class="p-4 rounded-4 border bg-light">
                                            <h6 class="fw-bold mb-3"><i class="fas fa-vial me-2"></i>Live Modal Preview</h6>
                                            <div class="modal-preview-box rounded-4 overflow-hidden shadow-sm mx-auto" style="max-width: 400px; border: 1px solid #ddd;">
                                                <div class="p-3 d-flex align-items-center justify-content-between text-white" id="preview_modal_header" style="background: <?php echo s($settings,'theme_modal_header_bg','linear-gradient(135deg, #1b1260 0%, #0f0a3d 100%)'); ?>;">
                                                    <div class="fw-bold"><i class="fas fa-user-plus me-2"></i>Example Modal Title</div>
                                                    <div class="rounded-circle d-flex align-items-center justify-content-center" id="preview_modal_close" style="width:34px; height:34px; background: rgba(255,255,255,0.15); color: <?php echo s($settings,'theme_modal_close_color','#ffffff'); ?>; font-size: <?php echo s($settings,'theme_modal_close_size','18px'); ?>;">
                                                        <i class="fas fa-times" id="preview_modal_icon"></i>
                                                    </div>
                                                </div>
                                                <div class="p-4 bg-white">
                                                    <div class="mb-2 bg-light rounded" style="height:10px; width:40%;"></div>
                                                    <div class="mb-3 bg-light rounded" style="height:40px; width:100%;"></div>
                                                    <div class="text-end">
                                                        <div class="btn btn-secondary btn-sm px-3 disabled">Cancel</div>
                                                        <div class="btn btn-primary btn-sm px-3 disabled">Action</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <p class="text-center small text-muted mt-3 mb-0">This preview reflects your modal styling settings in real-time.</p>
                                        </div>
                                    </div>
                                    <div class="col-12 text-end mt-3">
                                        <button type="submit" class="btn btn-primary px-5"><i class="fas fa-save me-1"></i>Save Layout</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- ④ Contact Info -->
                        <div class="tab-pane fade" id="tab-contact">
                            <form id="form-general" data-group="general">
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="form-label fw-bold">Site Title</label>
                                        <input name="site_title" type="text" class="form-control" value="<?php echo s($settings,'site_title'); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Site Phone</label>
                                        <input name="site_phone" type="text" class="form-control" value="<?php echo s($settings,'site_phone'); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Site Email</label>
                                        <input name="site_email" type="email" class="form-control" value="<?php echo s($settings,'site_email'); ?>">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label fw-bold">Address</label>
                                        <textarea name="site_address" class="form-control" rows="3"><?php echo s($settings,'site_address'); ?></textarea>
                                    </div>
                                    <div class="col-12 text-end">
                                        <button type="submit" class="btn btn-primary px-5"><i class="fas fa-save me-1"></i>Save Contact</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- ⑤ Security -->
                        <div class="tab-pane fade" id="tab-security">
                            <form id="form-recaptcha" data-group="recaptcha">
                                <div class="row g-3">
                                    <div class="col-12"><h6 class="fw-bold mb-3"><i class="fab fa-google text-primary me-2"></i>Google Recaptcha v3</h6></div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Site Key</label>
                                        <input name="recaptcha_site_key" type="text" class="form-control" value="<?php echo s($settings,'recaptcha_site_key'); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Secret Key</label>
                                        <div class="input-group">
                                            <input name="recaptcha_secret_key" type="password" class="form-control" value="<?php echo s($settings,'recaptcha_secret_key'); ?>">
                                            <button class="btn btn-outline-secondary toggle-pass" type="button"><i class="fas fa-eye"></i></button>
                                        </div>
                                    </div>
                                    <div class="col-12 text-end">
                                        <button type="submit" class="btn btn-primary px-5"><i class="fas fa-save me-1"></i>Save Security</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- ⑥ SMTP Email -->
                        <div class="tab-pane fade" id="tab-email">
                            <form id="form-email" data-group="email">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">SMTP Host</label>
                                        <input name="smtp_host" type="text" class="form-control" value="<?php echo s($settings,'smtp_host'); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">SMTP Port</label>
                                        <input name="smtp_port" type="text" class="form-control" value="<?php echo s($settings,'smtp_port','587'); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">SMTP Username</label>
                                        <input name="smtp_user" type="text" class="form-control" value="<?php echo s($settings,'smtp_user'); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">SMTP Password</label>
                                        <div class="input-group">
                                            <input name="smtp_pass" type="password" class="form-control">
                                            <button class="btn btn-outline-secondary toggle-pass" type="button"><i class="fas fa-eye"></i></button>
                                        </div>
                                    </div>
                                    <div class="col-12 text-end">
                                        <button type="submit" class="btn btn-primary px-5"><i class="fas fa-save me-1"></i>Save SMTP</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- ⑦ Social & Footer -->
                        <div class="tab-pane fade" id="tab-social">
                            <form id="form-social" data-group="social">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold"><i class="fab fa-facebook text-primary me-2"></i>Facebook URL</label>
                                        <input name="social_facebook" type="url" class="form-control" value="<?php echo s($settings,'social_facebook'); ?>" placeholder="https://facebook.com/yourpage">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold"><i class="fab fa-twitter text-info me-2"></i>Twitter (X) URL</label>
                                        <input name="social_twitter" type="url" class="form-control" value="<?php echo s($settings,'social_twitter'); ?>" placeholder="https://twitter.com/yourprofile">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold"><i class="fab fa-instagram text-danger me-2"></i>Instagram URL</label>
                                        <input name="social_instagram" type="url" class="form-control" value="<?php echo s($settings,'social_instagram'); ?>" placeholder="https://instagram.com/yourprofile">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold"><i class="fab fa-linkedin text-primary me-2"></i>LinkedIn URL</label>
                                        <input name="social_linkedin" type="url" class="form-control" value="<?php echo s($settings,'social_linkedin'); ?>" placeholder="https://linkedin.com/company/yourpage">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold"><i class="fab fa-youtube text-danger me-2"></i>YouTube URL</label>
                                        <input name="social_youtube" type="url" class="form-control" value="<?php echo s($settings,'social_youtube'); ?>" placeholder="https://youtube.com/channel/yourid">
                                    </div>
                                    <div class="col-md-12 mt-4">
                                        <h6 class="fw-bold text-primary border-bottom pb-2">Footer Configuration</h6>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label fw-bold">Footer Copyright Text</label>
                                        <input name="footer_copyright" type="text" class="form-control" value="<?php echo s($settings,'footer_copyright','© 2024 School Board. All rights reserved.'); ?>">
                                    </div>
                                    <div class="col-12 text-end mt-3">
                                        <button type="submit" class="btn btn-primary px-5"><i class="fas fa-save me-1"></i>Save Social & Footer</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// ── Color Picker ↔ Hex Input Sync & Live Preview ───────────────────────────
document.querySelectorAll('.theme-color-picker').forEach(picker => {
    const hexInput = document.querySelector('[data-target="' + picker.id + '"]');
    picker.addEventListener('input', function() {
        if (hexInput) hexInput.value = this.value;
        updateMockPreview();
    });
    if (hexInput) {
        hexInput.addEventListener('input', function() {
            if (/^#[0-9a-fA-F]{6}$/.test(this.value)) {
                picker.value = this.value;
                updateMockPreview();
            }
        });
    }
});

function updateMockPreview() {
    const primary = document.getElementById('pick_primary').value;
    const secondary = document.getElementById('pick_secondary').value;
    const success = document.getElementById('pick_success').value;
    
    document.getElementById('preview-mock-header').style.background = primary;
    document.getElementById('preview-mock-btn').style.background = secondary;
    const badge = document.getElementById('preview-mock-badge');
    badge.style.color = success;
    badge.style.borderColor = success;
}

// ── Modal Preview Sync ──────────────────────────────────────────────────
function updateModalPreview() {
    const headerBg = document.querySelector('[name="theme_modal_header_bg"]').value;
    const closeColor = document.querySelector('[name="theme_modal_close_color"]').value;
    const closeSize = document.querySelector('[name="theme_modal_close_size"]').value;
    
    const header = document.getElementById('preview_modal_header');
    const close = document.getElementById('preview_modal_close');
    
    if(header) header.style.background = headerBg;
    if(close) {
        close.style.color = closeColor;
        close.style.fontSize = closeSize;
    }
}

document.querySelectorAll('.modal-preview-trigger, [name="theme_modal_header_bg"], [name="theme_modal_close_color"]').forEach(el => {
    el.addEventListener('input', updateModalPreview);
});
updateModalPreview();

// ── Reset Defaults ──────────────────────────────────────────────────────────
document.getElementById('btn-reset-colors')?.addEventListener('click', function() {
    const defaults = {
        'pick_primary': '#1b1260', 'pick_primary_hover': '#0f0a3d', 'pick_secondary': '#ff6a1a',
        'pick_success': '#0b933a', 'pick_bg': '#f7f9fc', 'pick_heading': '#061c3a', 'pick_body_text': '#444444'
    };
    Object.entries(defaults).forEach(([id, val]) => {
        document.getElementById(id).value = val;
        document.querySelector('[data-target="' + id + '"]').value = val;
    });
    updateMockPreview();
});

// ── Font Selector ─────────────────────────────────────────────────────────
document.querySelectorAll('.font-selector').forEach(select => {
    const customInput = document.getElementById(select.id.replace('_select', '_custom'));
    select.addEventListener('change', function() {
        if (this.value === 'custom') customInput.classList.remove('d-none');
        else customInput.classList.add('d-none');
    });
    if (select.value === 'custom') customInput.classList.remove('d-none');
});

// ── AJAX Save ──────────────────────────────────────────────────────────────
document.querySelectorAll('form[data-group]').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const data = new FormData(this);
        data.append('group', this.dataset.group);
        const alertEl = document.getElementById('settings-alert');
        
        // Typography custom fix
        if (this.dataset.group === 'typography') {
            if (document.getElementById('heading_font_select').value === 'custom') 
                data.set('theme_font_heading', document.getElementById('heading_font_custom').value);
            if (document.getElementById('body_font_select').value === 'custom') 
                data.set('theme_font_body', document.getElementById('body_font_custom').value);
        }

        fetch('<?php echo $BASE_URL; ?>ajax/save_settings.php', { method: 'POST', body: data })
        .then(r => r.json())
        .then(res => {
            alertEl.className = 'mb-3 alert ' + (res.success ? 'alert-success' : 'alert-danger');
            alertEl.textContent = res.message;
            alertEl.classList.remove('d-none');
            alertEl.scrollIntoView({behavior:'smooth', block:'nearest'});
            if (res.success && ['colors', 'typography', 'layout'].includes(this.dataset.group)) {
                setTimeout(() => location.reload(), 1000);
            }
        });
    });
});

// ── Password Toggle ─────────────────────────────────────────────────────────
document.querySelectorAll('.toggle-pass').forEach(btn => {
    btn.addEventListener('click', function() {
        const input = this.previousElementSibling;
        const type = input.type === 'password' ? 'text' : 'password';
        input.type = type;
        this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
    });
});

// ── Dropzone (Mock Init) ───────────────────────────────────────────────────
Dropzone.autoDiscover = false;
const commonDz = { url: '<?php echo $BASE_URL; ?>ajax/upload_handler.php', acceptedFiles: 'image/*', success: function(f, r) { location.reload(); } };
new Dropzone("#logo-dropzone", { ...commonDz, params: { target: 'branding', db_key: 'theme_logo' } });
new Dropzone("#favicon-dropzone", { ...commonDz, params: { target: 'branding', db_key: 'theme_favicon' } });
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>
