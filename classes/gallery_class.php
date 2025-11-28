<?php
/**
 * Gallery Management Class
 * classes/gallery_class.php
 * Handles gallery creation and photo management for completed bookings
 */

class gallery_class extends db_connection {

    /**
     * Create a new gallery for a booking
     */
    public function create_gallery($booking_id, $provider_id, $title = '', $access_code = '') {
        if (!$this->db_connect()) {
            return false;
        }

        $booking_id = intval($booking_id);
        $provider_id = intval($provider_id);
        $title = $this->db->real_escape_string($title);
        $access_code = $this->db->real_escape_string($access_code);

        // Generate access code if not provided
        if (empty($access_code)) {
            $access_code = substr(bin2hex(random_bytes(16)), 0, 16);
        }

        $query = "INSERT INTO pb_photo_galleries (booking_id, provider_id, title, access_code, created_at)
                  VALUES (?, ?, ?, ?, NOW())";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("iiss", $booking_id, $provider_id, $title, $access_code);
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }

    /**
     * Get gallery by ID
     */
    public function get_gallery_by_id($gallery_id) {
        if (!$this->db_connect()) {
            return false;
        }

        $gallery_id = intval($gallery_id);

        $query = "SELECT g.*,
                         b.booking_id, b.customer_id, b.provider_id, b.booking_date, b.service_description,
                         c.name as customer_name, c.email as customer_email,
                         p.business_name
                  FROM pb_photo_galleries g
                  LEFT JOIN pb_bookings b ON g.booking_id = b.booking_id
                  LEFT JOIN pb_customer c ON b.customer_id = c.id
                  LEFT JOIN pb_service_providers p ON b.provider_id = p.provider_id
                  WHERE g.gallery_id = ?";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("i", $gallery_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            return $result->fetch_assoc();
        }
        return false;
    }

    /**
     * Get gallery by access code
     */
    public function get_gallery_by_access_code($access_code) {
        if (!$this->db_connect()) {
            return false;
        }

        $access_code = $this->db->real_escape_string($access_code);

        $query = "SELECT g.*,
                         b.booking_id, b.customer_id, b.provider_id, b.booking_date, b.service_description,
                         c.name as customer_name, c.email as customer_email,
                         p.business_name
                  FROM pb_photo_galleries g
                  LEFT JOIN pb_bookings b ON g.booking_id = b.booking_id
                  LEFT JOIN pb_customer c ON b.customer_id = c.id
                  LEFT JOIN pb_service_providers p ON b.provider_id = p.provider_id
                  WHERE g.access_code = ?";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("s", $access_code);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            return $result->fetch_assoc();
        }
        return false;
    }

    /**
     * Get galleries for a provider
     */
    public function get_provider_galleries($provider_id) {
        if (!$this->db_connect()) {
            return false;
        }

        $provider_id = intval($provider_id);

        $query = "SELECT g.*,
                         b.booking_id, b.customer_id, b.booking_date, b.service_description,
                         c.name as customer_name,
                         p.business_name,
                         (SELECT COUNT(*) FROM pb_gallery_photos WHERE gallery_id = g.gallery_id) as photo_count
                  FROM pb_photo_galleries g
                  LEFT JOIN pb_bookings b ON g.booking_id = b.booking_id
                  LEFT JOIN pb_customer c ON b.customer_id = c.id
                  LEFT JOIN pb_service_providers p ON b.provider_id = p.provider_id
                  WHERE g.provider_id = ?
                  ORDER BY g.created_at DESC";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("i", $provider_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $galleries = [];
            while ($row = $result->fetch_assoc()) {
                $galleries[] = $row;
            }
            return $galleries;
        }
        return false;
    }

    /**
     * Get gallery photos
     */
    public function get_gallery_photos($gallery_id) {
        if (!$this->db_connect()) {
            return false;
        }

        $gallery_id = intval($gallery_id);

        $query = "SELECT * FROM pb_gallery_photos
                  WHERE gallery_id = ?
                  ORDER BY photo_order ASC, upload_date ASC";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("i", $gallery_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $photos = [];
            while ($row = $result->fetch_assoc()) {
                $photos[] = $row;
            }
            return $photos;
        }
        return false;
    }

    /**
     * Add photo to gallery
     */
    public function add_photo($gallery_id, $file_path, $original_name = '', $photo_order = 0) {
        if (!$this->db_connect()) {
            return false;
        }

        $gallery_id = intval($gallery_id);
        $file_path = $this->db->real_escape_string($file_path);
        $original_name = $this->db->real_escape_string($original_name);
        $photo_order = intval($photo_order);

        $query = "INSERT INTO pb_gallery_photos (gallery_id, file_path, original_name, photo_order, upload_date)
                  VALUES (?, ?, ?, ?, NOW())";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("issi", $gallery_id, $file_path, $original_name, $photo_order);
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }

    /**
     * Delete photo
     */
    public function delete_photo($photo_id) {
        if (!$this->db_connect()) {
            return false;
        }

        $photo_id = intval($photo_id);

        // Get photo details first
        $query = "SELECT * FROM pb_gallery_photos WHERE photo_id = ?";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("i", $photo_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $photo = $result->fetch_assoc();

        if (!$photo) {
            return false;
        }

        // Delete from database
        $delete_query = "DELETE FROM pb_gallery_photos WHERE photo_id = ?";
        $delete_stmt = $this->db->prepare($delete_query);
        if (!$delete_stmt) {
            return false;
        }

        $delete_stmt->bind_param("i", $photo_id);
        return $delete_stmt->execute();
    }

    /**
     * Get photo by ID
     */
    public function get_photo_by_id($photo_id) {
        if (!$this->db_connect()) {
            return false;
        }

        $photo_id = intval($photo_id);

        $query = "SELECT p.*, g.gallery_id, g.provider_id, g.booking_id
                  FROM pb_gallery_photos p
                  LEFT JOIN pb_photo_galleries g ON p.gallery_id = g.gallery_id
                  WHERE p.photo_id = ?";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("i", $photo_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            return $result->fetch_assoc();
        }
        return false;
    }

    /**
     * Get gallery for booking
     */
    public function get_gallery_by_booking($booking_id) {
        if (!$this->db_connect()) {
            return false;
        }

        $booking_id = intval($booking_id);

        $query = "SELECT g.*,
                         (SELECT COUNT(*) FROM pb_gallery_photos WHERE gallery_id = g.gallery_id) as photo_count
                  FROM pb_photo_galleries g
                  WHERE g.booking_id = ?
                  LIMIT 1";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("i", $booking_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            return $result->fetch_assoc();
        }
        return false;
    }

    /**
     * Update gallery title
     */
    public function update_gallery_title($gallery_id, $title) {
        if (!$this->db_connect()) {
            return false;
        }

        $gallery_id = intval($gallery_id);
        $title = $this->db->real_escape_string($title);

        $query = "UPDATE pb_photo_galleries SET title = ? WHERE gallery_id = ?";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("si", $title, $gallery_id);
        return $stmt->execute();
    }

    /**
     * Delete gallery and all photos
     */
    public function delete_gallery($gallery_id) {
        if (!$this->db_connect()) {
            return false;
        }

        $gallery_id = intval($gallery_id);

        // Delete all photos first
        $query = "DELETE FROM pb_gallery_photos WHERE gallery_id = ?";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("i", $gallery_id);
        $stmt->execute();

        // Delete gallery
        $delete_query = "DELETE FROM pb_photo_galleries WHERE gallery_id = ?";
        $delete_stmt = $this->db->prepare($delete_query);
        if (!$delete_stmt) {
            return false;
        }

        $delete_stmt->bind_param("i", $gallery_id);
        return $delete_stmt->execute();
    }
}
?>
