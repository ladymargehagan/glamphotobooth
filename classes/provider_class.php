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
     * Get provider by customer ID (for legacy compatibility - returns provider with user fields)
     */
    public function get_provider_by_customer($customer_id) {
        if (!$this->db_connect()) {
            return false;
        }

        $customer_id = intval($customer_id);
        $sql = "SELECT provider_id, email, name, contact, city, image, user_role,
                       business_name, description, hourly_rate,
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
        $sql = "SELECT provider_id, email, name, contact, city, image, user_role,
                       business_name, description, hourly_rate,
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
     * Get provider by ID with all info
     * Returns all provider info from pb_service_providers table
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

        $sql = "SELECT provider_id, email, password, name, contact, city, image, user_role,
                       business_name, description, hourly_rate, portfolio_images,
                       rating, total_reviews, created_at, updated_at
                FROM pb_service_providers
                WHERE provider_id = $provider_id LIMIT 1";

        $result = $this->db_fetch_one($sql);
        if (!$result) {
            error_log('Provider get_provider_full: No provider found for id ' . $provider_id);
        }
        return $result;
    }

    /**
     * Add provider with full registration (name, email, password, role, contact, city, business_name, description, hourly_rate)
     * Used for direct registration to pb_service_providers table
     * Photographers (role 2) and Vendors (role 3) ONLY
     */
    public function add_provider_full($name, $email, $password, $role, $phone = '', $city = '', $business_name = '', $description = '', $hourly_rate = 0) {
        if (!$this->db_connect()) {
            return false;
        }

        // Validate inputs
        $name = trim($name);
        $email = trim($email);
        $phone = trim($phone);
        $city = trim($city);
        $business_name = trim($business_name);
        $description = trim($description);
        $role = intval($role);
        $hourly_rate = floatval($hourly_rate);

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
        $description = mysqli_real_escape_string($this->db, $description);

        // Use provided business_name or generate default
        if (empty($business_name)) {
            $business_name = ($role === 2) ? $name . " (Photographer)" : $name . " (Vendor)";
        }
        $business_name = mysqli_real_escape_string($this->db, $business_name);

        // Insert into pb_service_providers with all fields
        $sql = "INSERT INTO pb_service_providers (email, password, name, contact, city, user_role, business_name, description, hourly_rate, created_at)
                VALUES ('$email', '$password_hash', '$name', '$phone', '$city', $role, '$business_name', '$description', $hourly_rate, NOW())";

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

    /**
     * Delete provider (admin only)
     * Handles cascading deletes for related records
     */
    public function delete_provider($provider_id) {
        if (!$this->db_connect()) {
            return false;
        }

        $provider_id = intval($provider_id);

        try {
            // Start transaction
            $this->db->begin_transaction();

            // Delete from pb_reviews (provider reviews)
            $sql = "DELETE FROM pb_reviews WHERE provider_id = $provider_id";
            if (!$this->db_write_query($sql)) {
                throw new Exception('Failed to delete provider reviews');
            }

            // Delete from pb_bookings (service bookings)
            $sql = "DELETE FROM pb_bookings WHERE provider_id = $provider_id";
            if (!$this->db_write_query($sql)) {
                throw new Exception('Failed to delete provider bookings');
            }

            // Delete from pb_photo_galleries (photo galleries)
            $sql = "DELETE FROM pb_photo_galleries WHERE provider_id = $provider_id";
            if (!$this->db_write_query($sql)) {
                throw new Exception('Failed to delete provider galleries');
            }

            // Delete from pb_products (vendor products)
            // Note: This has ON DELETE CASCADE, but we delete items first
            $sql = "SELECT product_id FROM pb_products WHERE provider_id = $provider_id";
            $products = $this->db_fetch_all($sql);

            if ($products && is_array($products)) {
                foreach ($products as $product) {
                    $product_id = intval($product['product_id']);
                    $sql = "DELETE FROM pb_order_items WHERE product_id = $product_id";
                    if (!$this->db_write_query($sql)) {
                        throw new Exception('Failed to delete product order items');
                    }
                }
            }

            // Now delete products
            $sql = "DELETE FROM pb_products WHERE provider_id = $provider_id";
            if (!$this->db_write_query($sql)) {
                throw new Exception('Failed to delete provider products');
            }

            // Delete from pb_payment_requests
            $sql = "DELETE FROM pb_payment_requests WHERE provider_id = $provider_id";
            if (!$this->db_write_query($sql)) {
                throw new Exception('Failed to delete payment requests');
            }

            // Finally delete provider
            $sql = "DELETE FROM pb_service_providers WHERE provider_id = $provider_id";
            if (!$this->db_write_query($sql)) {
                throw new Exception('Failed to delete provider');
            }

            // Commit transaction
            $this->db->commit();
            return true;

        } catch (Exception $e) {
            // Rollback on error
            $this->db->rollback();
            error_log('Delete provider error: ' . $e->getMessage());
            return false;
        }
    }
}
?>
