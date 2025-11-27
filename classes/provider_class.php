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

    /**
     * Add provider with full registration (name, email, password, role, contact, city)
     * Used for direct registration to pb_service_providers table
     * Photographers (role 2) and Vendors (role 3) ONLY
     */
    public function add_provider_full($name, $email, $password, $role, $phone = '', $city = '') {
        if (!$this->db_connect()) {
            return false;
        }

        // Validate inputs
        $name = trim($name);
        $email = trim($email);
        $phone = trim($phone);
        $city = trim($city);
        $role = intval($role);

        // Only allow photographer (2) and vendor (3)
        if ($role !== 2 && $role !== 3) {
            return false;
        }

        // Hash password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Escape inputs
        $name = mysqli_real_escape_string($this->db, $name);
        $email = mysqli_real_escape_string($this->db, $email);
        $password_hash = mysqli_real_escape_string($this->db, $password_hash);
        $phone = mysqli_real_escape_string($this->db, $phone);
        $city = mysqli_real_escape_string($this->db, $city);

        $business_name = ($role === 2) ? $name . " (Photographer)" : $name . " (Vendor)";
        $business_name = mysqli_real_escape_string($this->db, $business_name);

        // Insert into pb_service_providers
        $sql = "INSERT INTO pb_service_providers (email, password, name, contact, city, user_role, business_name, created_at)
                VALUES ('$email', '$password_hash', '$name', '$phone', '$city', $role, '$business_name', NOW())";

        if ($this->db_write_query($sql)) {
            return $this->last_insert_id();
        }
        return false;
    }

    /**
     * Check if email exists in pb_service_providers table
     */
    public function check_provider_email_exists($email) {
        if (!$this->db_connect()) {
            return false;
        }

        $email = mysqli_real_escape_string($this->db, trim($email));
        $sql = "SELECT provider_id FROM pb_service_providers WHERE email = '$email' LIMIT 1";
        $result = $this->db_fetch_one($sql);
        return $result ? true : false;
    }

    /**
     * Get provider by email (for login)
     */
    public function get_provider_by_email($email) {
        if (!$this->db_connect()) {
            return false;
        }

        $email = mysqli_real_escape_string($this->db, trim($email));
        $sql = "SELECT provider_id, password, name, email, user_role, business_name, contact, city, image
                FROM pb_service_providers WHERE email = '$email' LIMIT 1";
        return $this->db_fetch_one($sql);
    }
}
?>
