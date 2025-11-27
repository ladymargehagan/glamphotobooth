<?php
/**
 * Gallery Controller
 * controllers/gallery_controller.php
 * Handles validation and business logic for gallery operations
 */

class gallery_controller {

    /**
     * Validate and create gallery
     */
    public function create_gallery_ctr($booking_id, $provider_id, $title = '') {
        if (!is_numeric($booking_id) || $booking_id <= 0) {
            return ['success' => false, 'message' => 'Invalid booking ID'];
        }

        if (!is_numeric($provider_id) || $provider_id <= 0) {
            return ['success' => false, 'message' => 'Invalid provider ID'];
        }

        $title = isset($title) ? trim($title) : '';

        try {
            $gallery_class = new gallery_class();
            $gallery_id = $gallery_class->create_gallery($booking_id, $provider_id, $title);

            if ($gallery_id) {
                return ['success' => true, 'gallery_id' => $gallery_id];
            } else {
                return ['success' => false, 'message' => 'Failed to create gallery'];
            }
        } catch (Exception $e) {
            error_log('Gallery creation error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error creating gallery'];
        }
    }

    /**
     * Validate photo upload
     */
    public function validate_photo_upload($file) {
        // Check if file exists
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            return ['success' => false, 'message' => 'No file provided'];
        }

        // Check file size (max 5MB)
        $max_size = 5 * 1024 * 1024;
        if ($file['size'] > $max_size) {
            return ['success' => false, 'message' => 'File too large. Maximum size: 5MB'];
        }

        // Check file type
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime_type, $allowed_types)) {
            return ['success' => false, 'message' => 'Invalid file type. Allowed: JPG, PNG, GIF, WebP'];
        }

        // Verify it's actually an image
        if (!getimagesize($file['tmp_name'])) {
            return ['success' => false, 'message' => 'File is not a valid image'];
        }

        return ['success' => true];
    }

    /**
     * Process and save photo
     */
    public function save_photo($file, $gallery_id, $provider_id, $booking_id) {
        // Validate
        $validation = $this->validate_photo_upload($file);
        if (!$validation['success']) {
            return $validation;
        }

        try {
            // Create directory structure: uploads/u{user_id}/g{gallery_id}/
            $upload_base = UPLOADS_DIR;
            if (!is_dir($upload_base)) {
                if (!mkdir($upload_base, 0755, true)) {
                    error_log('Failed to create base upload directory: ' . $upload_base);
                    return ['success' => false, 'message' => 'Failed to create upload directory'];
                }
            }

            $user_dir = $upload_base . 'u' . $provider_id . '/';
            if (!is_dir($user_dir)) {
                if (!mkdir($user_dir, 0755, true)) {
                    error_log('Failed to create user upload directory: ' . $user_dir);
                    return ['success' => false, 'message' => 'Failed to create user directory'];
                }
            }

            $gallery_dir = $user_dir . 'g' . $gallery_id . '/';
            if (!is_dir($gallery_dir)) {
                if (!mkdir($gallery_dir, 0755, true)) {
                    error_log('Failed to create gallery directory: ' . $gallery_dir);
                    return ['success' => false, 'message' => 'Failed to create gallery directory'];
                }
            }

            // Verify all directories are writable
            if (!is_writable($gallery_dir)) {
                error_log('Gallery directory not writable: ' . $gallery_dir);
                return ['success' => false, 'message' => 'Gallery directory is not writable'];
            }

            // Generate unique filename
            $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $safe_name = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($file['name'], PATHINFO_FILENAME));
            $filename = $safe_name . '_' . time() . '_' . rand(1000, 9999) . '.' . $file_ext;
            $file_path = $gallery_dir . $filename;

            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $file_path)) {
                return ['success' => false, 'message' => 'Failed to save file'];
            }

            // Make file readable
            chmod($file_path, 0644);

            // Save to database
            $gallery_class = new gallery_class();
            $photo_id = $gallery_class->add_photo(
                $gallery_id,
                'u' . $provider_id . '/g' . $gallery_id . '/' . $filename,
                $file['name']
            );

            if ($photo_id) {
                return [
                    'success' => true,
                    'photo_id' => $photo_id,
                    'file_path' => 'u' . $provider_id . '/g' . $gallery_id . '/' . $filename
                ];
            } else {
                // Clean up file if database save fails
                unlink($file_path);
                return ['success' => false, 'message' => 'Failed to save photo record'];
            }
        } catch (Exception $e) {
            error_log('Photo save error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error saving photo'];
        }
    }

    /**
     * Validate gallery access
     */
    public function validate_gallery_access($gallery_id, $access_code = '') {
        if (empty($access_code)) {
            return ['success' => false, 'message' => 'Access code required'];
        }

        try {
            $gallery_class = new gallery_class();
            $gallery = $gallery_class->get_gallery_by_access_code($access_code);

            if (!$gallery || intval($gallery['gallery_id']) !== intval($gallery_id)) {
                return ['success' => false, 'message' => 'Invalid access code'];
            }

            return ['success' => true, 'gallery' => $gallery];
        } catch (Exception $e) {
            error_log('Gallery access validation error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error validating access'];
        }
    }
}
?>
