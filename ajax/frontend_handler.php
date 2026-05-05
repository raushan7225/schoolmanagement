<?php
/**
 * ajax/frontend_handler.php
 * Handles Frontend CMS CRUD (Banners, Notices, Events, etc.)
 */
require_once(__DIR__ . '/../common/config.php');
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Helper for image uploads
function uploadMedia($file, $prefix) {
    if ($file['error'] !== UPLOAD_ERR_OK) return "";
    $dir = "../media/frontend/";
    if (!is_dir($dir)) mkdir($dir, 0777, true);
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $name = $prefix . "_" . time() . "_" . rand(100,999) . "." . $ext;
    if (move_uploaded_file($file['tmp_name'], $dir . $name)) return $name;
    return "";
}

switch ($action) {

    case 'save_banner':
        $id = (int)($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $subtitle = trim($_POST['subtitle'] ?? '');
        $link = trim($_POST['link'] ?? '');
        $status = (int)($_POST['status'] ?? 1);

        $image = "";
        if (isset($_FILES['image'])) $image = uploadMedia($_FILES['image'], 'BANNER');
        $laptop_image = "";
        if (isset($_FILES['laptop_image'])) $laptop_image = uploadMedia($_FILES['laptop_image'], 'LAP_BANNER');
        $mobile_image = "";
        if (isset($_FILES['mobile_image'])) $mobile_image = uploadMedia($_FILES['mobile_image'], 'MOB_BANNER');
        $tablet_image = "";
        if (isset($_FILES['tablet_image'])) $tablet_image = uploadMedia($_FILES['tablet_image'], 'TAB_BANNER');

        try {
            if ($id === 0) {
                if (!$image) throw new Exception("Desktop banner image is required.");
                $stmt = $pdo->prepare("INSERT INTO frontend_banners (title, subtitle, image, laptop_image, mobile_image, tablet_image, link, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$title, $subtitle, $image, $laptop_image, $mobile_image, $tablet_image, $link, $status]);
            } else {
                $params = [$title, $subtitle, $link, $status];
                $sql = "UPDATE frontend_banners SET title=?, subtitle=?, link=?, status=?";
                if ($image) { $sql .= ", image=?"; $params[] = $image; }
                if ($laptop_image) { $sql .= ", laptop_image=?"; $params[] = $laptop_image; }
                if ($mobile_image) { $sql .= ", mobile_image=?"; $params[] = $mobile_image; }
                if ($tablet_image) { $sql .= ", tablet_image=?"; $params[] = $tablet_image; }
                $sql .= " WHERE id=?"; $params[] = $id;
                $pdo->prepare($sql)->execute($params);
            }
            echo json_encode(['success' => true, 'message' => 'Banner saved.']);
        } catch (Exception $e) { echo json_encode(['success' => false, 'message' => $e->getMessage()]); }
        break;

    case 'delete_banner':
        $id = (int)($_POST['id'] ?? 0);
        $pdo->prepare("DELETE FROM frontend_banners WHERE id = ?")->execute([$id]);
        echo json_encode(['success' => true]);
        break;

    case 'save_notice':
        $id = (int)($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $date = $_POST['notice_date'] ?? date('Y-m-d');
        $scrolling = (int)($_POST['is_scrolling'] ?? 0);
        $status = (int)($_POST['status'] ?? 1);

        $file = "";
        if (isset($_FILES['file'])) $file = uploadMedia($_FILES['file'], 'NOTICE');

        try {
            if ($id === 0) {
                $stmt = $pdo->prepare("INSERT INTO frontend_notices (title, content, file_path, notice_date, status) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$title, $content, $file, $date, $status]);
            } else {
                $params = [$title, $content, $date, $status];
                $sql = "UPDATE frontend_notices SET title=?, content=?, notice_date=?, status=?";
                if ($file) { $sql .= ", file_path=?"; $params[] = $file; }
                $sql .= " WHERE id=?"; $params[] = $id;
                $pdo->prepare($sql)->execute($params);
            }
            echo json_encode(['success' => true, 'message' => 'Notice saved.']);
        } catch (Exception $e) { echo json_encode(['success' => false, 'message' => $e->getMessage()]); }
        break;

    case 'delete_notice':
        $id = (int)($_POST['id'] ?? 0);
        $pdo->prepare("DELETE FROM frontend_notices WHERE id = ?")->execute([$id]);
        echo json_encode(['success' => true]);
        break;

    case 'save_event':
        $id = (int)($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $date = $_POST['event_date'] ?? date('Y-m-d');
        $location = trim($_POST['location'] ?? '');
        $desc = trim($_POST['description'] ?? '');
        $status = (int)($_POST['status'] ?? 1);

        $image = "";
        if (isset($_FILES['image'])) $image = uploadMedia($_FILES['image'], 'EVENT');

        try {
            if ($id === 0) {
                $stmt = $pdo->prepare("INSERT INTO frontend_events (title, event_date, location, image, description, status) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$title, $date, $location, $image, $desc, $status]);
            } else {
                $params = [$title, $date, $location, $desc, $status];
                $sql = "UPDATE frontend_events SET title=?, event_date=?, location=?, description=?, status=?";
                if ($image) { $sql .= ", image=?"; $params[] = $image; }
                $sql .= " WHERE id=?"; $params[] = $id;
                $pdo->prepare($sql)->execute($params);
            }
            echo json_encode(['success' => true, 'message' => 'Event saved.']);
        } catch (Exception $e) { echo json_encode(['success' => false, 'message' => $e->getMessage()]); }
        break;

    case 'delete_event':
        $id = (int)($_POST['id'] ?? 0);
        $pdo->prepare("DELETE FROM frontend_events WHERE id = ?")->execute([$id]);
        echo json_encode(['success' => true]);
        break;

    case 'save_testimonial':
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $desig = trim($_POST['designation'] ?? '');
        $quote = trim($_POST['quote'] ?? '');
        $rating = (int)($_POST['rating'] ?? 5);
        $status = (int)($_POST['status'] ?? 1);

        $photo = "";
        if (isset($_FILES['photo'])) $photo = uploadMedia($_FILES['photo'], 'TESTI');

        try {
            if ($id === 0) {
                $stmt = $pdo->prepare("INSERT INTO frontend_testimonials (name, designation, quote, photo, status) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$name, $desig, $quote, $photo, $status]);
            } else {
                $params = [$name, $desig, $quote, $status];
                $sql = "UPDATE frontend_testimonials SET name=?, designation=?, quote=?, status=?";
                if ($photo) { $sql .= ", photo=?"; $params[] = $photo; }
                $sql .= " WHERE id=?"; $params[] = $id;
                $pdo->prepare($sql)->execute($params);
            }
            echo json_encode(['success' => true, 'message' => 'Testimonial saved.']);
        } catch (Exception $e) { echo json_encode(['success' => false, 'message' => $e->getMessage()]); }
        break;

    case 'delete_testimonial':
        $id = (int)($_POST['id'] ?? 0);
        $pdo->prepare("DELETE FROM frontend_testimonials WHERE id = ?")->execute([$id]);
        echo json_encode(['success' => true]);
        break;

    case 'save_achievement':
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['student_name'] ?? '');
        $title = trim($_POST['title'] ?? '');
        $desc = trim($_POST['description'] ?? '');
        $status = (int)($_POST['status'] ?? 1);

        $photo = "";
        if (isset($_FILES['photo'])) $photo = uploadMedia($_FILES['photo'], 'ACHIVE');

        try {
            if ($id === 0) {
                $stmt = $pdo->prepare("INSERT INTO frontend_achievements (student_name, title, description, photo, status) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$name, $title, $desc, $photo, $status]);
            } else {
                $params = [$name, $title, $desc, $status];
                $sql = "UPDATE frontend_achievements SET student_name=?, title=?, description=?, status=?";
                if ($photo) { $sql .= ", photo=?"; $params[] = $photo; }
                $sql .= " WHERE id=?"; $params[] = $id;
                $pdo->prepare($sql)->execute($params);
            }
            echo json_encode(['success' => true, 'message' => 'Achievement saved.']);
        } catch (Exception $e) { echo json_encode(['success' => false, 'message' => $e->getMessage()]); }
        break;

    case 'delete_achievement':
        $id = (int)($_POST['id'] ?? 0);
        $pdo->prepare("DELETE FROM frontend_achievements WHERE id = ?")->execute([$id]);
        echo json_encode(['success' => true]);
        break;

    // ══ GROUP 1: PAGES, SECTIONS, MENUS ════════════════════════
    case 'save_page':
        $id = (int)($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $content = $_POST['content'] ?? '';
        $m_title = trim($_POST['meta_title'] ?? '');
        $m_desc = trim($_POST['meta_description'] ?? '');
        $status = (int)($_POST['status'] ?? 1);

        if (!$title || !$slug) {
            echo json_encode(['success' => false, 'message' => 'Title and Slug are required.']);
            exit();
        }

        try {
            if ($id === 0) {
                $stmt = $pdo->prepare("INSERT INTO frontend_pages (title, slug, content, meta_title, meta_description, status) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$title, $slug, $content, $m_title, $m_desc, $status]);
            } else {
                $pdo->prepare("UPDATE frontend_pages SET title=?, slug=?, content=?, meta_title=?, meta_description=?, status=? WHERE id=?")
                    ->execute([$title, $slug, $content, $m_title, $m_desc, $status, $id]);
            }
            echo json_encode(['success' => true, 'message' => 'Page saved successfully.']);
        } catch (Exception $e) { echo json_encode(['success' => false, 'message' => $e->getMessage()]); }
        break;

    case 'delete_page':
        $id = (int)($_POST['id'] ?? 0);
        $pdo->prepare("DELETE FROM frontend_pages WHERE id = ?")->execute([$id]);
        echo json_encode(['success' => true]);
        break;

    case 'save_section':
        $id = (int)($_POST['id'] ?? 0);
        $key = $_POST['section_key'] ?? '';
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $status = (int)($_POST['status'] ?? 1);

        $image = "";
        if (isset($_FILES['image'])) $image = uploadMedia($_FILES['image'], 'SECTION');

        try {
            if ($id === 0) {
                $stmt = $pdo->prepare("INSERT INTO frontend_sections (section_key, title, content, image, status) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$key, $title, $content, $image, $status]);
            } else {
                $params = [$title, $content, $status];
                $sql = "UPDATE frontend_sections SET title=?, content=?, status=?";
                if ($image) { $sql .= ", image=?"; $params[] = $image; }
                $sql .= " WHERE id=?"; $params[] = $id;
                $pdo->prepare($sql)->execute($params);
            }
            echo json_encode(['success' => true, 'message' => 'Section updated.']);
        } catch (Exception $e) { echo json_encode(['success' => false, 'message' => $e->getMessage()]); }
        break;

    case 'save_menu':
        $id = (int)($_POST['id'] ?? 0);
        $parent = (int)($_POST['parent_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $link = trim($_POST['link'] ?? '');
        $sort = (int)($_POST['sort_order'] ?? 0);
        $status = (int)($_POST['status'] ?? 1);

        try {
            if ($id === 0) {
                $stmt = $pdo->prepare("INSERT INTO frontend_menus (parent_id, title, link, sort_order, status) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$parent, $title, $link, $sort, $status]);
            } else {
                $pdo->prepare("UPDATE frontend_menus SET parent_id=?, title=?, link=?, sort_order=?, status=? WHERE id=?")
                    ->execute([$parent, $title, $link, $sort, $status, $id]);
            }
            echo json_encode(['success' => true, 'message' => 'Menu saved.']);
        } catch (Exception $e) { echo json_encode(['success' => false, 'message' => $e->getMessage()]); }
        break;

    case 'delete_menu':
        $id = (int)($_POST['id'] ?? 0);
        $pdo->prepare("DELETE FROM frontend_menus WHERE id = ? OR parent_id = ?")->execute([$id, $id]);
        echo json_encode(['success' => true]);
        break;

    // ══ GROUP 2: GALLERY & MEDIA ════════════════════════════
    case 'save_gallery_category':
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        if (!$name) { echo json_encode(['success' => false, 'message' => 'Name required']); exit(); }
        
        try {
            if ($id === 0) {
                $pdo->prepare("INSERT INTO frontend_gallery_categories (name) VALUES (?)")->execute([$name]);
            } else {
                $pdo->prepare("UPDATE frontend_gallery_categories SET name=? WHERE id=?")->execute([$name, $id]);
            }
            echo json_encode(['success' => true, 'message' => 'Category saved.']);
        } catch (Exception $e) { echo json_encode(['success' => false, 'message' => $e->getMessage()]); }
        break;

    case 'save_gallery':
        $id = (int)($_POST['id'] ?? 0);
        $cat = (int)($_POST['category_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $type = $_POST['type'] ?? 'image';
        
        $file = "";
        if (isset($_FILES['media_file'])) $file = uploadMedia($_FILES['media_file'], 'GALLERY');

        try {
            if ($id === 0) {
                if (!$file) throw new Exception("Media file is required.");
                $stmt = $pdo->prepare("INSERT INTO frontend_gallery (category_id, title, media_file, type) VALUES (?, ?, ?, ?)");
                $stmt->execute([$cat, $title, $file, $type]);
            } else {
                $params = [$cat, $title, $type];
                $sql = "UPDATE frontend_gallery SET category_id=?, title=?, type=?";
                if ($file) { $sql .= ", media_file=?"; $params[] = $file; }
                $sql .= " WHERE id=?"; $params[] = $id;
                $pdo->prepare($sql)->execute($params);
            }
            echo json_encode(['success' => true, 'message' => 'Gallery item saved.']);
        } catch (Exception $e) { echo json_encode(['success' => false, 'message' => $e->getMessage()]); }
        break;

    case 'delete_gallery':
        $id = (int)($_POST['id'] ?? 0);
        $pdo->prepare("DELETE FROM frontend_gallery WHERE id = ?")->execute([$id]);
        echo json_encode(['success' => true]);
        break;

    case 'save_affiliation':
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $link = trim($_POST['link'] ?? '');
        $status = (int)($_POST['status'] ?? 1);
        
        $logo = "";
        if (isset($_FILES['logo'])) $logo = uploadMedia($_FILES['logo'], 'AFFILIATE');

        try {
            if ($id === 0) {
                if (!$logo) throw new Exception("Logo is required.");
                $stmt = $pdo->prepare("INSERT INTO frontend_affiliations (name, logo, link, status) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $logo, $link, $status]);
            } else {
                $params = [$name, $link, $status];
                $sql = "UPDATE frontend_affiliations SET name=?, link=?, status=?";
                if ($logo) { $sql .= ", logo=?"; $params[] = $logo; }
                $sql .= " WHERE id=?"; $params[] = $id;
                $pdo->prepare($sql)->execute($params);
            }
            echo json_encode(['success' => true, 'message' => 'Affiliation saved.']);
        } catch (Exception $e) { echo json_encode(['success' => false, 'message' => $e->getMessage()]); }
        break;

    case 'delete_affiliation':
        $id = (int)($_POST['id'] ?? 0);
        $pdo->prepare("DELETE FROM frontend_affiliations WHERE id = ?")->execute([$id]);
        echo json_encode(['success' => true]);
        break;

    case 'save_certificate':
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['center_name'] ?? '');
        $sort = (int)($_POST['sort_order'] ?? 1);
        $status = (int)($_POST['status'] ?? 1);
        
        $image = "";
        if (isset($_FILES['image'])) $image = uploadMedia($_FILES['image'], 'CERT');

        try {
            if ($id === 0) {
                if (!$image) throw new Exception("Certificate image is required.");
                $stmt = $pdo->prepare("INSERT INTO frontend_certificates (center_name, image, sort_order, status) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $image, $sort, $status]);
            } else {
                $params = [$name, $sort, $status];
                $sql = "UPDATE frontend_certificates SET center_name=?, sort_order=?, status=?";
                if ($image) { $sql .= ", image=?"; $params[] = $image; }
                $sql .= " WHERE id=?"; $params[] = $id;
                $pdo->prepare($sql)->execute($params);
            }
            echo json_encode(['success' => true, 'message' => 'Certificate saved.']);
        } catch (Exception $e) { echo json_encode(['success' => false, 'message' => $e->getMessage()]); }
        break;

    case 'delete_certificate':
        $id = (int)($_POST['id'] ?? 0);
        $pdo->prepare("DELETE FROM frontend_certificates WHERE id = ?")->execute([$id]);
        echo json_encode(['success' => true]);
        break;

    case 'save_recognition':
        $id = (int)($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $sort = (int)($_POST['sort_order'] ?? 0);
        $status = (int)($_POST['status'] ?? 1);
        
        $file = "";
        if (isset($_FILES['file'])) $file = uploadMedia($_FILES['file'], 'RECOG');

        try {
            if ($id === 0) {
                if (!$file) throw new Exception("Recognition document is required.");
                $stmt = $pdo->prepare("INSERT INTO frontend_recognitions (title, file_path, sort_order, status) VALUES (?, ?, ?, ?)");
                $stmt->execute([$title, $file, $sort, $status]);
            } else {
                $params = [$title, $sort, $status];
                $sql = "UPDATE frontend_recognitions SET title=?, sort_order=?, status=?";
                if ($file) { $sql .= ", file_path=?"; $params[] = $file; }
                $sql .= " WHERE id=?"; $params[] = $id;
                $pdo->prepare($sql)->execute($params);
            }
            echo json_encode(['success' => true, 'message' => 'Recognition saved.']);
        } catch (Exception $e) { echo json_encode(['success' => false, 'message' => $e->getMessage()]); }
        break;

    case 'delete_recognition':
        $id = (int)($_POST['id'] ?? 0);
        $pdo->prepare("DELETE FROM frontend_recognitions WHERE id = ?")->execute([$id]);
        echo json_encode(['success' => true]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action.']);
}
