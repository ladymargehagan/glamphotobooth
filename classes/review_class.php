<?php
/**
 * Review Management Class
 * classes/review_class.php
 * Handles review and rating operations
 */

class review_class extends db_connection {

    /**
     * Add review for provider
     */
    public function add_review($customer_id, $provider_id, $booking_id, $rating, $comment = '') {
        if (!$this->db_connect()) {
            return false;
        }

        $customer_id = intval($customer_id);
        $provider_id = intval($provider_id);
        $rating = intval($rating);
        $comment = $this->db->real_escape_string($comment);

        // Check if already reviewed (customer can only review each provider once)
        $check_query = "SELECT review_id FROM pb_reviews WHERE customer_id = ? AND provider_id = ?";
        $check_stmt = $this->db->prepare($check_query);
        if ($check_stmt) {
            $check_stmt->bind_param("ii", $customer_id, $provider_id);
            $check_stmt->execute();
            $result = $check_stmt->get_result();
            if ($result->num_rows > 0) {
                return false; // Already reviewed
            }
        }

        $query = "INSERT INTO pb_reviews (customer_id, provider_id, rating, comment)
                  VALUES (?, ?, ?, ?)";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("iiis", $customer_id, $provider_id, $rating, $comment);
        if ($stmt->execute()) {
            // Update provider rating
            $this->update_provider_rating($provider_id);
            return $this->db->insert_id;
        }
        return false;
    }

    /**
     * Get reviews by provider
     */
    public function get_reviews_by_provider($provider_id) {
        if (!$this->db_connect()) {
            return false;
        }

        $provider_id = intval($provider_id);

        $query = "SELECT r.*,
                         c.name as customer_name, c.image as customer_image,
                         b.booking_date, b.service_description
                  FROM pb_reviews r
                  LEFT JOIN pb_customer c ON r.customer_id = c.id
                  LEFT JOIN pb_bookings b ON r.booking_id = b.booking_id
                  WHERE r.provider_id = ?
                  ORDER BY r.review_date DESC";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("i", $provider_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $reviews = [];
            while ($row = $result->fetch_assoc()) {
                $reviews[] = $row;
            }
            return $reviews;
        }
        return false;
    }

    /**
     * Get review by ID
     */
    public function get_review_by_id($review_id) {
        if (!$this->db_connect()) {
            return false;
        }

        $review_id = intval($review_id);

        $query = "SELECT r.*,
                         c.name as customer_name, c.image as customer_image,
                         p.business_name,
                         b.booking_date
                  FROM pb_reviews r
                  LEFT JOIN pb_customer c ON r.customer_id = c.id
                  LEFT JOIN pb_service_providers p ON r.provider_id = p.provider_id
                  LEFT JOIN pb_bookings b ON r.booking_id = b.booking_id
                  WHERE r.review_id = ?";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("i", $review_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            return $result->fetch_assoc();
        }
        return false;
    }

    /**
     * Calculate average rating for provider
     */
    public function calculate_average_rating($provider_id) {
        if (!$this->db_connect()) {
            return 0;
        }

        $provider_id = intval($provider_id);

        $query = "SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews
                  FROM pb_reviews
                  WHERE provider_id = ?";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            return 0;
        }

        $stmt->bind_param("i", $provider_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            return $row['avg_rating'] ? round($row['avg_rating'], 2) : 0;
        }
        return 0;
    }

    /**
     * Get total reviews count for provider
     */
    public function get_total_reviews($provider_id) {
        if (!$this->db_connect()) {
            return 0;
        }

        $provider_id = intval($provider_id);

        $query = "SELECT COUNT(*) as total FROM pb_reviews WHERE provider_id = ?";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            return 0;
        }

        $stmt->bind_param("i", $provider_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            return intval($row['total']);
        }
        return 0;
    }

    /**
     * Update provider rating in pb_service_providers
     */
    public function update_provider_rating($provider_id) {
        if (!$this->db_connect()) {
            return false;
        }

        $provider_id = intval($provider_id);

        // Get average rating and count
        $avg_rating = $this->calculate_average_rating($provider_id);
        $total_reviews = $this->get_total_reviews($provider_id);

        // Update provider table
        $query = "UPDATE pb_service_providers SET rating = ?, total_reviews = ? WHERE provider_id = ?";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("dii", $avg_rating, $total_reviews, $provider_id);
        return $stmt->execute();
    }

    /**
     * Get reviews by booking (for checking if already reviewed)
     */
    public function get_review_by_booking($booking_id) {
        // Since booking_id doesn't exist in the actual database schema,
        // this method is kept for backward compatibility but will return false
        return false;
    }

    /**
     * Get review by customer and provider (check if already reviewed)
     */
    public function get_review_by_customer_provider($customer_id, $provider_id) {
        if (!$this->db_connect()) {
            return false;
        }

        $customer_id = intval($customer_id);
        $provider_id = intval($provider_id);

        $query = "SELECT * FROM pb_reviews WHERE customer_id = ? AND provider_id = ? LIMIT 1";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("ii", $customer_id, $provider_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            return $result->fetch_assoc();
        }
        return false;
    }

    /**
     * Delete review
     */
    public function delete_review($review_id) {
        if (!$this->db_connect()) {
            return false;
        }

        $review_id = intval($review_id);

        // Get review details first
        $review = $this->get_review_by_id($review_id);
        if (!$review) {
            return false;
        }

        // Delete review
        $query = "DELETE FROM pb_reviews WHERE review_id = ?";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("i", $review_id);
        if ($stmt->execute()) {
            // Update provider rating
            $this->update_provider_rating($review['provider_id']);
            return true;
        }
        return false;
    }

    /**
     * Get reviews for product (from associated bookings)
     */
    public function get_reviews_for_product($product_id) {
        if (!$this->db_connect()) {
            return false;
        }

        $product_id = intval($product_id);

        $query = "SELECT r.*,
                         c.name as customer_name, c.image as customer_image
                  FROM pb_reviews r
                  LEFT JOIN pb_customer c ON r.customer_id = c.id
                  INNER JOIN pb_bookings b ON r.booking_id = b.booking_id
                  WHERE b.product_id = ?
                  ORDER BY r.review_date DESC";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("i", $product_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $reviews = [];
            while ($row = $result->fetch_assoc()) {
                $reviews[] = $row;
            }
            return $reviews;
        }
        return false;
    }

    /**
     * Get all reviews for admin dashboard
     */
    public function get_all_reviews() {
        if (!$this->db_connect()) {
            return false;
        }

        $query = "SELECT r.* FROM pb_reviews r ORDER BY r.review_date DESC";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            return false;
        }

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return false;
    }
}
?>
