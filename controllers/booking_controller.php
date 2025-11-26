<?php
/**
 * Booking Controller
 * controllers/booking_controller.php
 */

class booking_controller {

    /**
     * Create a booking
     */
    public function create_booking_ctr($customer_id, $provider_id, $booking_date, $booking_time, $service_description, $notes = '') {
        $customer_id = intval($customer_id);
        $provider_id = intval($provider_id);

        // Validate inputs
        if ($customer_id <= 0 || $provider_id <= 0) {
            return [
                'success' => false,
                'message' => 'Invalid customer or provider ID'
            ];
        }

        if (empty($booking_date) || empty($booking_time)) {
            return [
                'success' => false,
                'message' => 'Booking date and time are required'
            ];
        }

        // Validate date is in future
        $booking_datetime = strtotime($booking_date . ' ' . $booking_time);
        if ($booking_datetime === false || $booking_datetime <= time()) {
            return [
                'success' => false,
                'message' => 'Booking date and time must be in the future'
            ];
        }

        if (empty($service_description) || strlen($service_description) < 10) {
            return [
                'success' => false,
                'message' => 'Service description is required (minimum 10 characters)'
            ];
        }

        $booking_class = new booking_class();
        $booking_id = $booking_class->create_booking($customer_id, $provider_id, $booking_date, $booking_time, $service_description, $notes);

        if ($booking_id) {
            return [
                'success' => true,
                'message' => 'Booking created successfully',
                'booking_id' => $booking_id
            ];
        }

        return [
            'success' => false,
            'message' => 'Failed to create booking'
        ];
    }

    /**
     * Update booking status
     */
    public function update_booking_status_ctr($booking_id, $status, $response_note = '') {
        $booking_id = intval($booking_id);

        $valid_statuses = ['pending', 'confirmed', 'completed', 'cancelled', 'rejected'];
        if (!in_array($status, $valid_statuses)) {
            return [
                'success' => false,
                'message' => 'Invalid booking status'
            ];
        }

        $booking_class = new booking_class();
        if ($booking_class->update_booking_status($booking_id, $status, $response_note)) {
            return [
                'success' => true,
                'message' => 'Booking status updated successfully'
            ];
        }

        return [
            'success' => false,
            'message' => 'Failed to update booking status'
        ];
    }

    /**
     * Get available time slots
     */
    public function get_available_slots_ctr($provider_id, $booking_date) {
        $provider_id = intval($provider_id);

        if ($provider_id <= 0) {
            return [
                'success' => false,
                'message' => 'Invalid provider ID'
            ];
        }

        if (empty($booking_date)) {
            return [
                'success' => false,
                'message' => 'Booking date is required'
            ];
        }

        $booking_class = new booking_class();
        $slots = $booking_class->get_available_slots($provider_id, $booking_date);

        return [
            'success' => true,
            'slots' => $slots
        ];
    }
}
?>
