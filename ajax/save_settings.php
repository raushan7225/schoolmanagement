<?php
/**
 * ajax/save_settings.php
 * Handles saving of General Settings (colors, basic info, email, etc.)
 */
require_once('../common/config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

$group = $_POST['group'] ?? 'general';
$allowed_groups = ['general', 'colors', 'email', 'recaptcha', 'typography', 'layout', 'social', 'payment_razorpay', 'payment_payu', 'payment_paypal', 'payment_qr'];

if (!in_array($group, $allowed_groups)) {
    echo json_encode(['success' => false, 'message' => 'Invalid group']);
    exit();
}

// Whitelist of allowed keys per group
$allowed_keys = [
    'general'   => ['site_title','site_phone','site_email','site_address'],
    'colors'    => ['theme_primary_color','theme_primary_hover_color','theme_secondary_color','theme_success_color','theme_body_bg_color','theme_heading_text_color','theme_body_text_color', 'theme_border_color', 'theme_input_focus_color', 'theme_link_color', 'theme_link_hover_color', 'theme_card_bg', 'theme_sidebar_bg', 'theme_header_bg', 'theme_footer_bg', 'theme_footer_text'],
    'email'     => ['smtp_host','smtp_user','smtp_pass','smtp_port'],
    'recaptcha' => ['recaptcha_site_key','recaptcha_secret_key'],
    'typography' => ['theme_font_heading', 'theme_font_body', 'theme_font_size_base', 'theme_font_size_h1', 'theme_font_size_h2', 'theme_font_weight_heading', 'theme_line_height', 'theme_letter_spacing'],
    'layout'    => ['theme_border_radius', 'theme_border_radius_lg', 'theme_border_radius_pill', 'theme_card_shadow', 'theme_card_shadow_hover', 'theme_section_padding', 'theme_transition_speed', 'theme_admin_sidebar_width', 'theme_admin_header_height', 'theme_modal_backdrop_blur', 'theme_modal_header_bg', 'theme_modal_close_icon', 'theme_modal_close_color', 'theme_modal_close_size'],
    'social'    => ['social_facebook', 'social_twitter', 'social_instagram', 'social_linkedin', 'social_youtube', 'footer_copyright'],
    'payment_razorpay' => ['pg_razorpay_status', 'pg_razorpay_currency', 'pg_razorpay_key', 'pg_razorpay_secret'],
    'payment_payu'     => ['pg_payu_status', 'pg_payu_currency', 'pg_payu_key', 'pg_payu_secret'],
    'payment_paypal'   => ['pg_paypal_status', 'pg_paypal_currency', 'pg_paypal_key', 'pg_paypal_secret'],
    'payment_qr'       => ['payment_qr_name', 'payment_qr_upi'],
];

$keys = $allowed_keys[$group];

try {
    $stmt = $pdo->prepare("INSERT INTO site_settings (setting_key, setting_value, setting_group) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
    
    foreach ($keys as $key) {
        if (isset($_POST[$key])) {
            $value = trim($_POST[$key]);
            
            // Server-Side Validation: Empty Check for mandatory payment fields
            if (str_starts_with($group, 'payment_') && $group !== 'payment_qr') {
                if (str_ends_with($key, '_key') || str_ends_with($key, '_secret')) {
                    if (empty($value) && $_POST[str_replace(['_key', '_secret'], '_status', $key)] === '1') {
                        echo json_encode(['success' => false, 'message' => "API Key and Secret are required when the gateway is enabled."]);
                        exit();
                    }
                }
            }
            if ($group === 'payment_qr' && empty($value)) {
                echo json_encode(['success' => false, 'message' => "All fields are required."]);
                exit();
            }

            // Validate hex colors
            if ($group === 'colors' && !preg_match('/^#[0-9a-fA-F]{6}$/', $value)) {
                continue; // skip invalid color
            }
            $stmt->execute([$key, $value, $group]);
        }
    }

    echo json_encode(['success' => true, 'message' => 'Settings saved successfully!']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'DB Error: ' . $e->getMessage()]);
}
?>
