<?php
/**
 * Booking Class
 * classes/booking_class.php
 */

class booking_class extends db_connection {

    /**
     * Create a booking
     */
    public function create_booking($customer_id, $provider_id, $product_id, $booking_date, $booking_time, $service_description, $notes = '') {
        if (!$this->db_connect()) {
            return false;
        }

        $customer_id = intval($customer_id);
        $provider_id = intval($provider_id);
        $product_id = intval($product_id);
        $booking_date = mysqli_real_escape_string($this->db, $booking_date);
        $booking_time = mysqli_real_escape_string($this->db, $booking_time);
        $service_description = mysqli_real_escape_string($this->db, $service_description);
        $notes = mysqli_real_escape_string($this->db, $notes);

        // Get customer contact info
        $sql_customer = "SELECT contact FROM pb_customer WHERE id = $customer_id";
        $customer = $this->db_fetch_one($sql_customer);
        $contact = $customer && isset($customer['contact']) ? mysqli_real_escape_string($this->db, $customer['contact']) : null;

        // Get product price
        $sql_price = "SELECT price FROM pb_products WHERE product_id = $product_id";
        $result = $this->db_fetch_one($sql_price);
        $total_price = $result ? floatval($result['price']) : 0;

        // Default duration is 1 hour
        $duration_hours = 1.00;

        // Include contact field in insert
        $contact_value = $contact ? "'$contact'" : "NULL";
        $sql = "INSERT INTO pb_bookings (customer_id, provider_id, product_id, booking_date, booking_time, service_description, notes, contact, duration_hours, total_price, status)
                VALUES ($customer_id, $provider_id, $product_id, '$booking_date', '$booking_time', '$service_description', '$notes', $contact_value, $duration_hours, $total_price, 'pending')";

        if ($this->db_write_query($sql)) {
            return $this->last_insert_id();
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
        $sql = "SELECT b.*, c.name as customer_name, c.email, c.contact, p.title as product_title
                FROM pb_bookings b
                LEFT JOIN pb_customer c ON b.customer_id = c.id
                LEFT JOIN pb_products p ON b.product_id = p.product_id
                WHERE b.booking_id = $booking_id";
        return $this->db_fetch_one($sql);
    }

    /**
     * Get customer's bookings
     */
    public function get_customer_bookings($customer_id) {
        if (!$this->db_connect()) {
            return false;
        }

        $customer_id = intval($customer_id);
        $sql = "SELECT b.*, p.title as product_title, sp.business_name
                FROM pb_bookings b
                LEFT JOIN pb_products p ON b.product_id = p.product_id
                LEFT JOIN pb_service_providers sp ON b.provider_id = sp.provider_id
                WHERE b.customer_id = $customer_id
                ORDER BY b.booking_date DESC";
        return $this->db_fetch_all($sql);
    }

    /**
     * Get provider's bookings
     */
    public function get_provider_bookings($provider_id) {
        if (!$this->db_connect()) {
            return false;
        }

        $provider_id = intval($provider_id);
        $sql = "SELECT b.*, c.name as customer_name, c.email, c.contact, p.title as product_title
                FROM pb_bookings b
                LEFT JOIN pb_customer c ON b.customer_id = c.id
                LEFT JOIN pb_products p ON b.product_id = p.product_id
                WHERE b.provider_id = $provider_id
                ORDER BY b.booking_date DESC";
        return $this->db_fetch_all($sql);
    }

    /**
     * Get provider booking statistics
     */
    public function get_provider_stats($provider_id) {
        if (!$this->db_connect()) {
            return [
                'pending' => 0,
                'confirmed' => 0,
                'completed' => 0,
                'total' => 0
            ];
        }

        $provider_id = intval($provider_id);
        
        // Get counts by status
        $sql = "SELECT 
                    COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending,
                    COUNT(CASE WHEN status = 'confirmed' OR status = 'accepted' THEN 1 END) as confirmed,
                    COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed,
                    COUNT(*) as total
                FROM pb_bookings 
                WHERE provider_id = $provider_id";
        
        $result = $this->db_fetch_one($sql);
        
        if ($result) {
            return [
                'pending' => intval($result['pending'] ?? 0),
                'confirmed' => intval($result['confirmed'] ?? 0),
                'completed' => intval($result['completed'] ?? 0),
                'total' => intval($result['total'] ?? 0)
            ];
        }
        
        return [
            'pending' => 0,
            'confirmed' => 0,
            'completed' => 0,
            'total' => 0
        ];
    }

    /**
     * Update booking status
     */
    public function update_booking_status($booking_id, $status, $response_note = '') {
        if (!$this->db_connect()) {
            return false;
        }

        $booking_id = intval($booking_id);
        $status = mysqli_real_escape_string($this->db, $status);
        $response_note = mysqli_real_escape_string($this->db, $response_note);

        $sql = "UPDATE pb_bookings SET status = '$status', response_note = '$response_note' WHERE booking_id = $booking_id";
        return $this->db_write_query($sql);
    }

    /**
     * Get available time slots for a date
     */
    public function get_available_slots($provider_id, $booking_date) {
        if (!$this->db_connect()) {
            return [];
        }

        $provider_id = intval($provider_id);
        $booking_date = mysqli_real_escape_string($this->db, $booking_date);

        // Default time slots (9 AM to 5 PM, hourly)
        $slots = ['09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00'];

        // Get booked times for this date
        $sql = "SELECT booking_time FROM pb_bookings
                WHERE provider_id = $provider_id AND booking_date = '$booking_date' AND status IN ('pending', 'confirmed', 'accepted')";
        $results = $this->db_fetch_all($sql);

        $booked_times = [];
        if ($results) {
            foreach ($results as $row) {
                $booked_times[] = $row['booking_time'];
            }
        }

        // Filter out booked slots
        $available_slots = array_filter($slots, function($slot) use ($booked_times) {
            return !in_array($slot, $booked_times);
        });

        return array_values($available_slots);
    }

    /**
     * Get all bookings (for admin dashboard)
     */
    public function get_all_bookings() {
        if (!$this->db_connect()) {
            return false;
        }

        $sql = "SELECT b.*, c.name as customer_name, sp.business_name as provider_name
                FROM pb_bookings b
                LEFT JOIN pb_customer c ON b.customer_id = c.id
                LEFT JOIN pb_service_providers sp ON b.provider_id = sp.provider_id
                ORDER BY b.created_at DESC";
        return $this->db_fetch_all($sql);
    }
}
?>
