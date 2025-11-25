<?php
/**
 * Provider Controller
 * controllers/provider_controller.php
 */

class provider_controller {

    /**
     * Add provider profile
     */
    public function add_provider_ctr($customer_id, $business_name, $description, $hourly_rate) {
        // Validation
        if (empty($business_name) || empty($description)) {
            return ['success' => false, 'message' => 'Business name and description are required'];
        }

        if (strlen($business_name) < 3 || strlen($business_name) > 255) {
            return ['success' => false, 'message' => 'Business name must be between 3 and 255 characters'];
        }

        if (strlen($description) < 10 || strlen($description) > 5000) {
            return ['success' => false, 'message' => 'Description must be between 10 and 5000 characters'];
        }

        if ($hourly_rate <= 0 || $hourly_rate > 99999.99) {
            return ['success' => false, 'message' => 'Hourly rate must be a valid positive number'];
        }

        $provider_class = new provider_class();

        // Check if provider already exists
        if ($provider_class->provider_exists($customer_id)) {
            return ['success' => false, 'message' => 'Provider profile already exists'];
        }

        $provider_id = $provider_class->add_provider($customer_id, $business_name, $description, $hourly_rate);

        if ($provider_id) {
            return ['success' => true, 'message' => 'Provider profile created successfully', 'provider_id' => $provider_id];
        }

        return ['success' => false, 'message' => 'Failed to create provider profile'];
    }

    /**
     * Update provider profile
     */
    public function update_provider_ctr($provider_id, $business_name, $description, $hourly_rate) {
        // Validation
        if (empty($business_name) || empty($description)) {
            return ['success' => false, 'message' => 'Business name and description are required'];
        }

        if (strlen($business_name) < 3 || strlen($business_name) > 255) {
            return ['success' => false, 'message' => 'Business name must be between 3 and 255 characters'];
        }

        if (strlen($description) < 10 || strlen($description) > 5000) {
            return ['success' => false, 'message' => 'Description must be between 10 and 5000 characters'];
        }

        if ($hourly_rate <= 0 || $hourly_rate > 99999.99) {
            return ['success' => false, 'message' => 'Hourly rate must be a valid positive number'];
        }

        $provider_class = new provider_class();
        $provider = $provider_class->get_provider_by_id($provider_id);

        if (!$provider) {
            return ['success' => false, 'message' => 'Provider not found'];
        }

        if ($provider_class->update_provider($provider_id, $business_name, $description, $hourly_rate)) {
            return ['success' => true, 'message' => 'Provider profile updated successfully'];
        }

        return ['success' => false, 'message' => 'Failed to update provider profile'];
    }

    /**
     * Get provider profile
     */
    public function get_provider_ctr($customer_id) {
        $provider_class = new provider_class();
        $provider = $provider_class->get_provider_by_customer($customer_id);

        if (!$provider) {
            return ['success' => false, 'data' => null];
        }

        return ['success' => true, 'data' => $provider];
    }
}
?>
