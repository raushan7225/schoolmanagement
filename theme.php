<?php
/**
 * theme.php — Unified Dynamic CSS Variable Generator
 * Reads ALL design tokens from site_settings DB and outputs :root CSS.
 * Included in every portal layout (frontend, admin, student, login).
 */
header('Content-Type: text/css; charset=utf-8');
header('Cache-Control: public, max-age=120');

require_once(__DIR__ . '/common/config.php');

// Fetch all settings into key=>value map
$all = $pdo->query("SELECT setting_key, setting_value FROM site_settings WHERE status=1")
           ->fetchAll(PDO::FETCH_KEY_PAIR);

function v($all, $key, $default) {
    return isset($all[$key]) ? $all[$key] : $default;
}

// ── Colors ───────────────────────────────────────────────────────────────
$primary        = v($all, 'theme_primary_color',       '#1b1260');
$primaryHover   = v($all, 'theme_primary_hover_color', '#0f0a3d');
$secondary      = v($all, 'theme_secondary_color',     '#ff6a1a');
$success        = v($all, 'theme_success_color',       '#0b933a');
$bodyBg         = v($all, 'theme_body_bg_color',       '#f7f9fc');
$headingText    = v($all, 'theme_heading_text_color',  '#061c3a');
$bodyText       = v($all, 'theme_body_text_color',     '#444444');
$borderColor    = v($all, 'theme_border_color',        '#e9ecef');
$linkColor      = v($all, 'theme_link_color',          $primary);
$linkHover      = v($all, 'theme_link_hover_color',    $secondary);
$cardBg         = v($all, 'theme_card_bg',             '#ffffff');
$sidebarBg      = v($all, 'theme_sidebar_bg',          '#ffffff');
$headerBg       = v($all, 'theme_header_bg',           '#ffffff');
$footerBg       = v($all, 'theme_footer_bg',           $primary);
$footerText     = v($all, 'theme_footer_text',         '#ffffff');
$inputFocus     = v($all, 'theme_input_focus_color',   $primary);

// ── Typography ────────────────────────────────────────────────────────────
$fontHeading    = v($all, 'theme_font_heading',        'Poppins');
$fontBody       = v($all, 'theme_font_body',           'Inter');
$fontSizeBase   = v($all, 'theme_font_size_base',      '16px');
$fontSizeH1     = v($all, 'theme_font_size_h1',        '2.2rem');
$fontSizeH2     = v($all, 'theme_font_size_h2',        '1.8rem');
$fontWeightH    = v($all, 'theme_font_weight_heading', '700');
$lineHeight     = v($all, 'theme_line_height',         '1.65');
$letterSpacing  = v($all, 'theme_letter_spacing',      '0.3px');

// ── Layout & Spacing ──────────────────────────────────────────────────────
$borderRadius   = v($all, 'theme_border_radius',        '8px');
$borderRadiusLg = v($all, 'theme_border_radius_lg',     '12px');
$borderRadiusPill=v($all, 'theme_border_radius_pill',   '50px');
$cardShadow     = v($all, 'theme_card_shadow',          '0px 5px 30px rgba(0,0,0,0.08)');
$cardShadowHover= v($all, 'theme_card_shadow_hover',    '0px 15px 40px rgba(0,0,0,0.15)');
$sectionPadding = v($all, 'theme_section_padding',      '80px');
$transitionSpeed= v($all, 'theme_transition_speed',     '0.3s');
$sidebarWidth   = v($all, 'theme_admin_sidebar_width',  '300px');
$headerHeight   = v($all, 'theme_admin_header_height',  '70px');

// ── Modal Settings ────────────────────────────────────────────────────────
$modalBlur      = v($all, 'theme_modal_backdrop_blur',  '8px');
$modalHeaderBg  = v($all, 'theme_modal_header_bg',      'linear-gradient(135deg, var(--theme-primary-color) 0%, var(--theme-primary-hover-color) 100%)');
$modalCloseColor= v($all, 'theme_modal_close_color',   '#ffffff');
$modalCloseSize = v($all, 'theme_modal_close_size',    '18px');



// ── Compute RGB values for rgba() usage ───────────────────────────────────
function hexToRgb($hex) {
    $hex = ltrim($hex, '#');
    if (strlen($hex) === 3) {
        $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
    }
    if (strlen($hex) !== 6) return '0, 0, 0';
    return implode(', ', array_map('hexdec', str_split($hex, 2)));
}
$primaryRgb   = hexToRgb($primary);
$secondaryRgb = hexToRgb($secondary);
$footerBgRgb  = hexToRgb($footerBg);

// Sanitize non-color values (prevent CSS injection)
function safeCssValue($val) {
    return htmlspecialchars(strip_tags($val), ENT_QUOTES);
}

// ── Compute Google Fonts Import ──────────────────────────────────────────
$fontsToImport = [];
if (!empty($fontHeading)) $fontsToImport[] = str_replace(' ', '+', $fontHeading) . ':wght@400;500;600;700;800;900';
if (!empty($fontBody) && $fontBody !== $fontHeading) $fontsToImport[] = str_replace(' ', '+', $fontBody) . ':wght@300;400;500;600;700';

$fontImportCss = "";
if (!empty($fontsToImport)) {
    $fontImportCss = "@import url('https://fonts.googleapis.com/css2?family=" . implode('&family=', $fontsToImport) . "&display=swap');\n\n";
}

echo "/* ================================================================
   DYNAMIC THEME.PHP — All Design Tokens from Admin General Settings
   Generated: " . date('Y-m-d H:i:s') . "
   ================================================================ */

" . $fontImportCss . "

:root {

    /* ── Brand Colors ── */
    --theme-primary-color:        " . safeCssValue($primary) . ";
    --theme-primary-hover-color:  " . safeCssValue($primaryHover) . ";
    --theme-primary-dark-color:   " . safeCssValue($primaryHover) . ";
    --theme-secondary-color:      " . safeCssValue($secondary) . ";
    --theme-secondary-hover-color: color-mix(in srgb, " . safeCssValue($secondary) . " 70%, white);
    --theme-success-color:        " . safeCssValue($success) . ";
    --theme-blue-color:           #2196F3;

    /* ── Text & Background ── */
    --theme-body-bg-color:        " . safeCssValue($bodyBg) . ";
    --theme-body-text-color:      " . safeCssValue($bodyText) . ";
    --theme-heading-text-color:   " . safeCssValue($headingText) . ";
    --theme-white-color:          #ffffff;
    --theme-black-color:          #000000;

    /* ── Advanced Colors ── */
    --theme-border-color:         " . safeCssValue($borderColor) . ";
    --theme-link-color:           " . safeCssValue($linkColor) . ";
    --theme-link-hover-color:     " . safeCssValue($linkHover) . ";
    --theme-card-bg:              " . safeCssValue($cardBg) . ";
    --theme-input-focus-color:    " . safeCssValue($inputFocus) . ";
    --theme-sidebar-bg:           " . safeCssValue($sidebarBg) . ";
    --theme-header-bg:            " . safeCssValue($headerBg) . ";
    --theme-footer-bg:            " . safeCssValue($footerBg) . ";
    --theme-footer-text:          " . safeCssValue($footerText) . ";

    /* ── Gray Scale ── */
    --theme-notice-bg-color:       #f4f8fc;
    --theme-notice-bg-hover-color: #eaf2fb;
    --theme-light-blue-color:      #f8fbff;
    --theme-gray-100-color: #f8f9fa;
    --theme-gray-200-color: #f1f1f1;
    --theme-gray-300-color: #eee;
    --theme-gray-400-color: #ccc;
    --theme-gray-800-color: #333;

    /* ── RGB Utilities for rgba() ── */
    --theme-primary-rgb:   " . $primaryRgb . ";
    --theme-secondary-rgb: " . $secondaryRgb . ";
    --theme-footer-bg-rgb: " . $footerBgRgb . ";
    --theme-white-rgb:     255, 255, 255;
    --theme-black-rgb:     0, 0, 0;

    /* ── Typography ── */
    --theme-font-heading:        '" . safeCssValue($fontHeading) . "', sans-serif;
    --theme-font-body:           '" . safeCssValue($fontBody) . "', sans-serif;
    --theme-font-size-base:      " . safeCssValue($fontSizeBase) . ";
    --theme-font-size-h1:        " . safeCssValue($fontSizeH1) . ";
    --theme-font-size-h2:        " . safeCssValue($fontSizeH2) . ";
    --theme-font-weight-heading: " . safeCssValue($fontWeightH) . ";
    --theme-line-height:         " . safeCssValue($lineHeight) . ";
    --theme-letter-spacing:      " . safeCssValue($letterSpacing) . ";

    /* ── Layout & Spacing ── */
    --theme-border-radius:       " . safeCssValue($borderRadius) . ";
    --theme-border-radius-lg:    " . safeCssValue($borderRadiusLg) . ";
    --theme-border-radius-pill:  " . safeCssValue($borderRadiusPill) . ";
    --theme-card-shadow:         " . safeCssValue($cardShadow) . ";
    --theme-card-shadow-hover:   " . safeCssValue($cardShadowHover) . ";
    --theme-section-padding:     " . safeCssValue($sectionPadding) . ";
    --theme-transition-speed:    " . safeCssValue($transitionSpeed) . ";

    /* ── Admin Dashboard Specific ── */
    --theme-sidebar-width:       " . safeCssValue($sidebarWidth) . ";
    --theme-header-height:       " . safeCssValue($headerHeight) . ";
    --theme-modal-backdrop-blur: " . safeCssValue($modalBlur) . ";
    --theme-modal-header-bg:     " . $modalHeaderBg . ";
    --theme-modal-close-color:   " . safeCssValue($modalCloseColor) . ";
    --theme-modal-close-size:    " . safeCssValue($modalCloseSize) . ";
    --theme-modal-close-icon:    '" . safeCssValue(v($all, 'theme_modal_close_icon', 'fas fa-times')) . "';
}

/* ── Global Modal Close Icon Injection ── */
.modal-header .btn-close::before,
.btn-custom-close::before {
    font-family: 'Font Awesome 6 Free' !important;
    font-weight: 900 !important;
    content: '\f00d'; /* Fallback to X */
    color: var(--theme-modal-close-color) !important;
    font-size: var(--theme-modal-close-size) !important;
}


    /* ── Admin CSS Aliases (admin.css compatibility) ── */
    --primary-color:    " . safeCssValue($primary) . ";
    --primary-dark:     " . safeCssValue($primaryHover) . ";
    --secondary-color:  " . safeCssValue($secondary) . ";
    --success-color:    " . safeCssValue($success) . ";
    --bg-light:         " . safeCssValue($bodyBg) . ";
    --sidebar-width:    " . safeCssValue($sidebarWidth) . ";
    --header-height:    " . safeCssValue($headerHeight) . ";
    --card-shadow:      " . safeCssValue($cardShadow) . ";
    --transition-smooth: all " . safeCssValue($transitionSpeed) . " cubic-bezier(0.4, 0, 0.2, 1);

    /* ── Student Portal Aliases ── */
    --student-primary:   " . safeCssValue($primary) . ";
    --student-secondary: " . safeCssValue($secondary) . ";
    --student-success:   " . safeCssValue($success) . ";
    --student-bg-light:  " . safeCssValue($bodyBg) . ";
    --student-card-shadow: " . safeCssValue($cardShadow) . ";

    /* ── Login Page Aliases ── */
    --login-primary:  " . safeCssValue($primary) . ";
    --login-secondary:" . safeCssValue($secondary) . ";
    --login-primary-light: " . safeCssValue($bodyBg) . ";
    --login-text-dark: " . safeCssValue($headingText) . ";
}

/* ── Apply dynamic fonts to HTML elements ── */
body {
    font-family: var(--theme-font-body);
    font-size: var(--theme-font-size-base);
    line-height: var(--theme-line-height);
    color: var(--theme-body-text-color);
    background-color: var(--theme-body-bg-color);
}

h1, h2, h3, h4, h5, h6 {
    font-family: var(--theme-font-heading);
    font-weight: var(--theme-font-weight-heading);
    color: var(--theme-heading-text-color);
    letter-spacing: var(--theme-letter-spacing);
}

h1 { font-size: var(--theme-font-size-h1); }
h2 { font-size: var(--theme-font-size-h2); }

a {
    color: var(--theme-link-color);
    transition: color var(--theme-transition-speed) ease;
}
a:hover { color: var(--theme-link-hover-color); }

/* ── Dynamic Layout Elements ── */
.card { border-radius: var(--theme-border-radius) !important; overflow: hidden; }
.card-lg { border-radius: var(--theme-border-radius-lg) !important; overflow: hidden; }

/* ── Dynamic Card & Modal Headers ── */
.card-header, .modal-header {
    background: var(--theme-modal-header-bg) !important;
    border-bottom: none !important;
    padding: 15px 25px !important;
}
.card-header, 
.card-header h1, .card-header h2, .card-header h3, .card-header h4, .card-header h5, .card-header h6,
.card-header .card-title, .card-header i, .card-header span, .card-header p,
.modal-header,
.modal-header .modal-title, .modal-header i, .modal-header h1, .modal-header h2, .modal-header h3, .modal-header h4, .modal-header h5, .modal-header h6 {
    color: #ffffff !important;
}

/* Ensure danger headers stay red (standardization for delete modals) */
.modal-header.bg-danger {
    background: #dc3545 !important;
}

/* ── Dynamic Section Padding (frontend) ── */
.section-padding { padding: var(--theme-section-padding) 0; }

/* ── Dynamic Form Focus ── */
.form-control:focus,
.form-select:focus {
    border-color: var(--theme-input-focus-color);
    box-shadow: 0 0 0 0.25rem rgba(var(--theme-primary-rgb), 0.12);
}

/* ── Dynamic Sidebar & Header Background ── */
.sidebar { background-color: var(--theme-sidebar-bg) !important; }
.header  { background-color: var(--theme-header-bg) !important; }

/* ── Dynamic Admin Sidebar Width ── */
.sidebar { width: var(--theme-sidebar-width) !important; }
@media (min-width: 1200px) {
    #main, #footer {
        margin-left: var(--theme-sidebar-width);
        width: calc(100% - var(--theme-sidebar-width));
    }
    .toggle-sidebar #main, .toggle-sidebar #footer {
        margin-left: 0;
        width: 100%;
    }
    .toggle-sidebar .sidebar {
        left: calc(-1 * var(--theme-sidebar-width));
    }
}
";
?>
