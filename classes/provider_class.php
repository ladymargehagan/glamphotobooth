<?php
/**
 * Provider Class
 * classes/provider_class.php
 */

class provider_class extends db_connection {

    /**
     * Add provider profile
     */
    public function add_provider($customer_id, $business_name, $description, $hourly_rate) {
        if (!$this->db_connect()) {
            return false;
        }

        $customer_id = intval($customer_id);
        $business_name = trim($business_name);
        $description = trim($description);
        $hourly_rate = floatval($hourly_rate);

        // Escape inputs
        $business_name = mysqli_real_escape_string($this->db, $business_name);
        $description = mysqli_real_escape_string($this->db, $description);

        $sql = "INSERT INTO pb_service_providers (provider_id, business_name, description, hourly_rate)
                VALUES ($customer_id, '$business_name', '$description', $hourly_rate)";

        if ($this->db_write_query($sql)) {
            return $customer_id;
        }
        return false;
    }

    /**
     * Get provider by customer ID (via foreign key relationship)
     */
    public function get_provider_by_customer($customer_id) {
        if (!$this->db_connect()) {
            return false;
        }

        $customer_id = intval($customer_id);
        $sql = "SELECT provider_id, business_name, description, hourly_rate,
                       rating, total_reviews, created_at, updated_at
                FROM pb_service_providers WHERE provider_id = $customer_id LIMIT 1";
        return $this->db_fetch_one($sql);
    }

    /**
     * Get provider by ID
     */
    public function get_provider_by_id($provider_id) {
        if (!$this->db_connect()) {
            return false;
        }

        $provider_id = intval($provider_id);
        $sql = "SELECT provider_id, business_name, description, hourly_rate,
                       rating, total_reviews, created_at, updated_at
                FROM pb_service_providers WHERE provider_id = $provider_id LIMIT 1";
        return $this->db_fetch_one($sql);
    }

    /**
     * Check if provider exists for customer
     */
    public function provider_exists($customer_id) {
        if (!$this->db_connect()) {
            return false;
        }

        $customer_id = intval($customer_id);
        $sql = "SELECT provider_id FROM pb_service_providers WHERE provider_id = $customer_id LIMIT 1";
        $result = $this->db_fetch_one($sql);
        return $result ? true : false;
    }

    /**
     * Update provider profile
     */
    public function update_provider($provider_id, $business_name, $description, $hourly_rate) {
        if (!$this->db_connect()) {
            return false;
        }

        $provider_id = intval($provider_id);
        $business_name = trim($business_name);
        $description = trim($description);
        $hourly_rate = floatval($hourly_rate);

        // Escape inputs
        $business_name = mysqli_real_escape_string($this->db, $business_name);
        $description = mysqli_real_escape_string($this->db, $description);

        $sql = "UPDATE pb_service_providers
                SET business_name = '$business_name',
                    description = '$description',
                    hourly_rate = $hourly_rate,
                    updated_at = NOW()
                WHERE provider_id = $provider_id";

        return $this->db_write_query($sql);
    }

    /**
     * Get provider by ID with customer info
     */
    public function get_provider_full($provider_id) {
        if (!$this->db_connect()) {
            error_log('Provider get_provider_full: Database connection failed');
            return false;
        }

        $provider_id = intval($provider_id);
        if ($provider_id <= 0) {
            error_log('Provider get_provider_full: Invalid provider_id');
            return false;
        }

        $sql = "SELECT sp.provider_id, sp.business_name, sp.description,
                       sp.hourly_rate, sp.rating, sp.total_reviews, sp.created_at, sp.updated_at,
                       c.id, c.name, c.email, c.country, c.city, c.contact, c.image
                FROM pb_service_providers sp
                LEFT JOIN pb_customer c ON sp.provider_id = c.id
                WHERE sp.provider_id = $provider_id LIMIT 1";

        $result = $this->db_fetch_one($sql);
        if (!$result) {
            error_log('Provider get_provider_full: No provider found for id ' . $provider_id);
        }
        return $result;
    }
}
?>
