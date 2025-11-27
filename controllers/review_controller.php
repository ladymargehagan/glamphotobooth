<?php
/**
 * Review Controller
 * controllers/review_controller.php
 * Validates review input before database operations
 */

class review_controller {

    /**
     * Validate and add review
     */
    public function add_review_ctr($customer_id, $provider_id, $booking_id, $rating, $comment) {
        // Validate inputs
        if (!is_numeric($customer_id) || $customer_id <= 0) {
            return ['success' => false, 'message' => 'Invalid customer ID'];
        }

        if (!is_numeric($provider_id) || $provider_id <= 0) {
            return ['success' => false, 'message' => 'Invalid provider ID'];
        }

        if (!is_numeric($booking_id) || $booking_id <= 0) {
            return ['success' => false, 'message' => 'Invalid booking ID'];
        }

        // Validate rating (1-5 stars)
        $rating = intval($rating);
        if ($rating < 1 || $rating > 5) {
            return ['success' => false, 'message' => 'Rating must be between 1 and 5'];
        }

        // Validate comment
        $comment = trim($comment ?? '');
        if (strlen($comment) > 500) {
            return ['success' => false, 'message' => 'Comment cannot exceed 500 characters'];
        }

        // Check if booking is completed
        $booking_class = new booking_class();
        $booking = $booking_class->get_booking_by_id($booking_id);

        if (!$booking) {
            return ['success' => false, 'message' => 'Booking not found'];
        }

        if ($booking['status'] !== 'completed') {
            return ['success' => false, 'message' => 'Can only review completed bookings'];
        }

        // Verify customer is the one who made booking
        if (intval($booking['customer_id']) !== intval($customer_id)) {
            return ['success' => false, 'message' => 'You can only review your own bookings'];
        }

        // Check if already reviewed (customer can only review each provider once)
        $review_class = new review_class();
        $existing_review = $review_class->get_review_by_customer_provider($customer_id, $provider_id);

        if ($existing_review) {
            return ['success' => false, 'message' => 'You have already reviewed this provider'];
        }

        // Add review
        $review_id = $review_class->add_review($customer_id, $provider_id, $booking_id, $rating, $comment);

        if ($review_id) {
            return ['success' => true, 'message' => 'Review added successfully', 'review_id' => $review_id];
        } else {
            return ['success' => false, 'message' => 'Failed to add review'];
        }
    }

    /**
     * Get provider reviews with pagination
     */
    public function get_provider_reviews_ctr($provider_id, $limit = 10, $offset = 0) {
        if (!is_numeric($provider_id) || $provider_id <= 0) {
            return ['success' => false, 'message' => 'Invalid provider ID'];
        }

        $limit = max(1, min(intval($limit), 100)); // 1-100 reviews per request
        $offset = max(0, intval($offset));

        $review_class = new review_class();
        $reviews = $review_class->get_reviews_by_provider($provider_id);

        if (!$reviews) {
            return ['success' => true, 'reviews' => [], 'total' => 0];
        }

        // Apply pagination
        $total = count($reviews);
        $reviews = array_slice($reviews, $offset, $limit);

        return [
            'success' => true,
            'reviews' => $reviews,
            'total' => $total,
            'limit' => $limit,
            'offset' => $offset
        ];
    }

    /**
     * Get provider rating summary
     */
    public function get_provider_rating_ctr($provider_id) {
        if (!is_numeric($provider_id) || $provider_id <= 0) {
            return ['success' => false, 'message' => 'Invalid provider ID'];
        }

        $review_class = new review_class();
        $avg_rating = $review_class->calculate_average_rating($provider_id);
        $total_reviews = $review_class->get_total_reviews($provider_id);

        return [
            'success' => true,
            'average_rating' => $avg_rating,
            'total_reviews' => $total_reviews,
            'rating_display' => $this->format_rating_display($avg_rating, $total_reviews)
        ];
    }

    /**
     * Format rating for display
     */
    private function format_rating_display($average, $total) {
        if ($total === 0) {
            return 'No ratings yet';
        }
        return $average . '/5 (' . $total . ' review' . ($total !== 1 ? 's' : '') . ')';
    }

    /**
     * Delete review (for review owner or admin)
     */
    public function delete_review_ctr($review_id, $user_id, $is_admin = false) {
        if (!is_numeric($review_id) || $review_id <= 0) {
            return ['success' => false, 'message' => 'Invalid review ID'];
        }

        if (!is_numeric($user_id) || $user_id <= 0) {
            return ['success' => false, 'message' => 'Invalid user ID'];
        }

        $review_class = new review_class();
        $review = $review_class->get_review_by_id($review_id);

        if (!$review) {
            return ['success' => false, 'message' => 'Review not found'];
        }

        // Check authorization (owner or admin)
        if (!$is_admin && intval($review['customer_id']) !== intval($user_id)) {
            return ['success' => false, 'message' => 'You can only delete your own reviews'];
        }

        if ($review_class->delete_review($review_id)) {
            return ['success' => true, 'message' => 'Review deleted successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to delete review'];
        }
    }
}
?>
