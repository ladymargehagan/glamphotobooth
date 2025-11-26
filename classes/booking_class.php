<?php
/**
 * Booking Management Class
 * classes/booking_class.php
 */

class booking_class extends db_connection {

    /**
     * Create a new booking
     */
    public function create_booking($customer_id, $provider_id, $booking_date, $booking_time, $service_description, $notes = '') {
        if (!$this->db_connect()) {
            return false;
        }

        $customer_id = intval($customer_id);
        $provider_id = intval($provider_id);
        $booking_date = $this->db->real_escape_string($booking_date);
        $booking_time = $this->db->real_escape_string($booking_time);
        $service_description = $this->db->real_escape_string($service_description);
        $notes = $this->db->real_escape_string($notes);

        $query = "INSERT INTO pb_bookings (customer_id, provider_id, booking_date, booking_time, service_description, notes, status, created_at)
                  VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("iissss", $customer_id, $provider_id, $booking_date, $booking_time, $service_description, $notes);
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }

    /**
     * Get booking by ID
     */
    public function get_booking_by_id($booking_id) {
        if (!$this->db_connect()) {
            return false;
        }

        $booking_id = intval($booking_id);

        $query = "SELECT b.*, 
                         c.name as customer_name, c.email as customer_email, c.contact as customer_phone,
                         p.business_name, p.provider_id, sp.name as provider_name
                  FROM pb_bookings b
                  LEFT JOIN pb_customer c ON b.customer_id = c.id
                  LEFT JOIN pb_service_providers p ON b.provider_id = p.provider_id
                  LEFT JOIN pb_customer sp ON p.customer_id = sp.id
                  WHERE b.booking_id = ?";

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
     * Get bookings for a customer
     */
    public function get_customer_bookings($customer_id) {
        if (!$this->db_connect()) {
            return false;
        }

        $customer_id = intval($customer_id);

        $query = "SELECT b.*, p.business_name
                  FROM pb_bookings b
                  LEFT JOIN pb_service_providers p ON b.provider_id = p.provider_id
                  WHERE b.customer_id = ?
                  ORDER BY b.booking_date DESC, b.booking_time DESC";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("i", $customer_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return false;
    }

    /**
     * Get bookings for a provider
     */
    public function get_provider_bookings($provider_id) {
        if (!$this->db_connect()) {
            return false;
        }

        $provider_id = intval($provider_id);

        $query = "SELECT b.*, c.name as customer_name, c.email, c.contact
                  FROM pb_bookings b
                  LEFT JOIN pb_customer c ON b.customer_id = c.id
                  WHERE b.provider_id = ?
                  ORDER BY b.booking_date DESC, b.booking_time DESC";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("i", $provider_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return false;
    }

    /**
     * Update booking status
     */
    public function update_booking_status($booking_id, $status, $response_note = '') {
        if (!$this->db_connect()) {
            return false;
        }

        $booking_id = intval($booking_id);
        $status = $this->db->real_escape_string($status);
        $response_note = $this->db->real_escape_string($response_note);

        $valid_statuses = ['pending', 'confirmed', 'completed', 'cancelled', 'rejected'];
        if (!in_array($status, $valid_statuses)) {
            return false;
        }

        if (empty($response_note)) {
            $query = "UPDATE pb_bookings SET status = ?, updated_at = NOW() WHERE booking_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("si", $status, $booking_id);
        } else {
            $query = "UPDATE pb_bookings SET status = ?, response_note = ?, updated_at = NOW() WHERE booking_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("ssi", $status, $response_note, $booking_id);
        }

        return $stmt->execute();
    }

    /**
     * Get available time slots for a date
     */
    public function get_available_slots($provider_id, $booking_date) {
        if (!$this->db_connect()) {
            return false;
        }

        $provider_id = intval($provider_id);
        $booking_date = $this->db->real_escape_string($booking_date);

        // Define working hours
        $time_slots = ['09:00', '10:00', '11:00', '12:00', '14:00', '15:00', '16:00', '17:00', '18:00'];

        // Get booked slots for this date
        $query = "SELECT booking_time FROM pb_bookings 
                  WHERE provider_id = ? AND booking_date = ? AND status IN ('pending', 'confirmed')";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            return $time_slots;
        }

        $stmt->bind_param("is", $provider_id, $booking_date);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $booked_times = [];
            while ($row = $result->fetch_assoc()) {
                $booked_times[] = $row['booking_time'];
            }

            // Filter out booked slots
            return array_values(array_diff($time_slots, $booked_times));
        }

        return $time_slots;
    }

    /**
     * Cancel booking
     */
    public function cancel_booking($booking_id, $cancellation_reason = '') {
        return $this->update_booking_status($booking_id, 'cancelled', $cancellation_reason);
    }

    /**
     * Complete booking
     */
    public function complete_booking($booking_id) {
        return $this->update_booking_status($booking_id, 'completed');
    }

    /**
     * Get booking statistics for provider
     */
    public function get_provider_stats($provider_id) {
        if (!$this->db_connect()) {
            return false;
        }

        $provider_id = intval($provider_id);

        $query = "SELECT 
                    COUNT(*) as total_bookings,
                    SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
                  FROM pb_bookings
                  WHERE provider_id = ?";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("i", $provider_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            return $result->fetch_assoc();
        }
        return false;
    }
}
?>
