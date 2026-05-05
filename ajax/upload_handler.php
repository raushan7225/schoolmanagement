<?php
/**
 * ajax/upload_handler.php
 * Centralized file upload handler for Dropzone.js and other dynamic uploads.
 */
include_once("../admin/includes/config.php");

// Set JSON header
header('Content-Type: application/json');

// Check if user is logged in (security)
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. Get Target Directory & Context
    $targetDir = isset($_POST['target']) ? $_POST['target'] : 'general';
    $uploadPath = dirname(__DIR__) . "/uploads/" . $targetDir . "/";
    
    // Create directory if not exists
    if (!file_exists($uploadPath)) {
        mkdir($uploadPath, 0755, true);
    }
    
    // 2. Handle File
    if (!empty($_FILES['file'])) {
        $file = $_FILES['file'];
        $fileName = time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "_", basename($file['name']));
        $targetFile = $uploadPath . $fileName;
        
        // Validation: Allowed MIME types
        $allowedMime = ['image/jpeg', 'image/png', 'image/gif', 'image/x-icon', 'image/vnd.microsoft.icon'];
        
        // Robust MIME detection
        $mimeType = '';
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
        } elseif (function_exists('mime_content_type')) {
            $mimeType = mime_content_type($file['tmp_name']);
        } else {
            $mimeType = $file['type']; // Fallback to browser-provided type
        }
        
        if (!in_array($mimeType, $allowedMime)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid file type (' . $mimeType . '). Only JPG, PNG, GIF, and ICO allowed.']);
            exit;
        }
        
        // Validation: File Size (max 2MB)
        if ($file['size'] > 2 * 1024 * 1024) {
            echo json_encode(['status' => 'error', 'message' => 'File size exceeds 2MB limit.']);
            exit;
        }
        
        // Move File
        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            
            // 3. Database Update Logic (based on context)
            $db_key = isset($_POST['db_key']) ? $_POST['db_key'] : '';
            if (!empty($db_key)) {
                // Update site_settings table if it's a theme asset
                $stmt = $pdo->prepare("UPDATE site_settings SET setting_value = ? WHERE setting_key = ?");
                $stmt->execute(['uploads/' . $targetDir . '/' . $fileName, $db_key]);
            }
            
            echo json_encode([
                'status' => 'success',
                'message' => 'File uploaded successfully!',
                'filePath' => 'uploads/' . $targetDir . '/' . $fileName,
                'fileName' => $fileName
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to move uploaded file.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No file uploaded.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
