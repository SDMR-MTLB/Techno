<?php
// core/upload.php
require_once __DIR__ . '/../config/app.php';

function uploadImage($file, $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'], $maxSize = 2097152) {
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        error_log("Upload error code: " . $file['error']);
        return false;
    }


    // Validate file size
    if ($file['size'] > $maxSize) {
        error_log("File too large: " . $file['size']);
        return false;
    }

    // Validate MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    if (!in_array($mime, $allowedTypes)) {
        error_log("Invalid MIME type: " . $mime);
        return false;
    }

    // Generate safe filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newFilename = uniqid() . '_' . time() . '.' . $extension;
    $targetPath = UPLOAD_DIR . $newFilename;

    // Ensure target directory exists
    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
        error_log("Created upload directory: " . UPLOAD_DIR);
    }

    // Move file
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return $newFilename;
    } else {
        error_log("Failed to move uploaded file to " . $targetPath);
        return false;
    }
}