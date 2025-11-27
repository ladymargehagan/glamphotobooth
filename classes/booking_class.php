<?php
/**
 * Booking Class
 * classes/booking_class.php
 */

class booking_class {
    private $db;

    public function __construct() {
        $this->db = new db_connect();
    }

    /**
     * Create a booking
     */
    public function create_booking($customer_id, $provider_id, $product_id, $booking_date, $booking_time, $service_description, $notes = '') {
        $customer_id = intval($customer_id);
        $provider_id = intval($provider_id);
        $product_id = intval($product_id);
        $booking_date = $this->db->real_escape_string($booking_date);
        $booking_time = $this->db->real_escape_string($booking_time);
        $service_description = $this->db->real_escape_string($service_description);
        $notes = $this->db->real_escape_string($notes);

        // Get product price
        $product_query = "SELECT price FROM pb_products WHERE product_id = $product_id";
        $product_result = $this->db->query($product_query);
        $product = $product_result->fetch_assoc();
        $total_price = $product ? floatval($product['price']) : 0;

        $sql = "INSERT INTO pb_bookings (customer_id, provider_id, product_id, booking_date, booking_time, service_description, notes, total_price, status)
                VALUES ($customer_id, $provider_id, $product_id, '$booking_date', '$booking_time', '$service_description', '$notes', $total_price, 'pending')";

        if ($this->db->query($sql)) {
            return $this->db->insert_id;
        }
        return false;
    }

    /**
     * Get booking by ID
     */
    public function get_booking_by_id($booking_id) {
        $booking_id = intval($booking_id);
        $sql = "SELECT * FROM pb_bookings WHERE booking_id = $booking_id";
        $result = $this->db->query($sql);
        return $result->fetch_assoc();
    }

    /**
     * Get customer's bookings
     */
    public function get_customer_bookings($customer_id) {
        $customer_id = intval($customer_id);
        $sql = "SELECT b.*, p.title as product_title, sp.business_name
                FROM pb_bookings b
                LEFT JOIN pb_products p ON b.product_id = p.product_id
                LEFT JOIN pb_service_providers sp ON b.provider_id = sp.provider_id
                WHERE b.customer_id = $customer_id
                ORDER BY b.booking_date DESC";
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get provider's bookings
     */
    public function get_provider_bookings($provider_id) {
        $provider_id = intval($provider_id);
        $sql = "SELECT b.*, c.name as customer_name, c.contact as customer_contact, p.title as product_title
                FROM pb_bookings b
                LEFT JOIN pb_customer c ON b.customer_id = c.id
                LEFT JOIN pb_products p ON b.product_id = p.product_id
                WHERE b.provider_id = $provider_id
                ORDER BY b.booking_date ASC";
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Update booking status
     */
    public function update_booking_status($booking_id, $status, $response_note = '') {
        $booking_id = intval($booking_id);
        $status = $this->db->real_escape_string($status);
        $response_note = $this->db->real_escape_string($response_note);

        $sql = "UPDATE pb_bookings SET status = '$status', response_note = '$response_note' WHERE booking_id = $booking_id";
        return $this->db->query($sql);
    }

    /**
     * Get available time slots for a date
     */
    public function get_available_slots($provider_id, $booking_date) {
        $provider_id = intval($provider_id);
        $booking_date = $this->db->real_escape_string($booking_date);

        // Default time slots (9 AM to 5 PM, hourly)
        $slots = ['09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00'];

        // Get booked times for this date
        $sql = "SELECT booking_time FROM pb_bookings
                WHERE provider_id = $provider_id AND booking_date = '$booking_date' AND status IN ('pending', 'confirmed', 'accepted')";
        $result = $this->db->query($sql);

        $booked_times = [];
        while ($row = $result->fetch_assoc()) {
            $booked_times[] = $row['booking_time'];
        }

        // Filter out booked slots
        $available_slots = array_filter($slots, function($slot) use ($booked_times) {
            return !in_array($slot, $booked_times);
        });

        return array_values($available_slots);
    }
}
?>
