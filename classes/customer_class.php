<?php
/**
 * Customer Class
 * classes/customer_class.php
 */

class customer_class extends db_connection {

    /**
     * Add new customer (register)
     */
    public function add_customer($name, $email, $password, $user_role = 4, $contact = '', $city = '') {
        if (!$this->db_connect()) {
            return false;
        }

        $password_hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $email = strtolower(trim($email));
        $name = trim($name);
        $contact = trim($contact);
        $city = trim($city);

        // Escape SQL inputs
        $name = mysqli_real_escape_string($this->db, $name);
        $email = mysqli_real_escape_string($this->db, $email);
        $contact = mysqli_real_escape_string($this->db, $contact);
        $city = mysqli_real_escape_string($this->db, $city);
        // Password hash should NOT be escaped - it's already a safe 60-character string from password_hash()
        // Escaping it can corrupt the hash and break password verification
        $user_role = intval($user_role);

        $sql = "INSERT INTO pb_customer (name, email, password, contact, city, user_role, created_at)
                VALUES ('$name', '$email', '$password_hash', '$contact', '$city', $user_role, NOW())";

        if ($this->db_write_query($sql)) {
            return $this->last_insert_id();
        }
        return false;
    }

    /**
     * Check if email exists
     */
    public function check_email_exists($email) {
        if (!$this->db_connect()) {
            return false;
        }
        
        $email = strtolower(trim($email));
        $email = mysqli_real_escape_string($this->db, $email);
        $sql = "SELECT id FROM pb_customer WHERE email = '$email' LIMIT 1";
        $result = $this->db_fetch_one($sql);
        return $result ? true : false;
    }

    /**
     * Get customer by email
     */
    public function get_customer_by_email($email) {
        if (!$this->db_connect()) {
            return false;
        }
        
        $email = strtolower(trim($email));
        $email = mysqli_real_escape_string($this->db, $email);
        $sql = "SELECT id, name, email, password, user_role FROM pb_customer WHERE email = '$email' LIMIT 1";
        return $this->db_fetch_one($sql);
    }

    /**
     * Get customer by ID
     */
    public function get_customer_by_id($id) {
        if (!$this->db_connect()) {
            return false;
        }
        
        $id = intval($id);
        $sql = "SELECT id, name, email, country, city, contact, image, user_role, created_at FROM pb_customer WHERE id = $id LIMIT 1";
        return $this->db_fetch_one($sql);
    }

    /**
     * Update customer profile
     */
    public function update_customer($id, $name, $country, $city, $contact) {
        if (!$this->db_connect()) {
            return false;
        }
        
        $name = trim($name);
        $country = trim($country);
        $city = trim($city);
        $contact = trim($contact);
        
        // Escape SQL inputs
        $name = mysqli_real_escape_string($this->db, $name);
        $country = mysqli_real_escape_string($this->db, $country);
        $city = mysqli_real_escape_string($this->db, $city);
        $contact = mysqli_real_escape_string($this->db, $contact);
        $id = intval($id);

        $sql = "UPDATE pb_customer SET name = '$name', country = '$country', city = '$city', contact = '$contact', updated_at = NOW() WHERE id = $id";

        return $this->db_write_query($sql);
    }

    /**
     * Change password
     */
    public function change_password($id, $new_password) {
        if (!$this->db_connect()) {
            return false;
        }
        
        $password_hash = password_hash($new_password, PASSWORD_BCRYPT, ['cost' => 12]);
        // Password hash should NOT be escaped - it's already a safe 60-character string from password_hash()
        // Escaping it can corrupt the hash and break password verification
        $id = intval($id);
        $sql = "UPDATE pb_customer SET password = '$password_hash', updated_at = NOW() WHERE id = $id";
        return $this->db_write_query($sql);
    }

    /**
     * Verify password
     */
    public function verify_password($plain_password, $hash) {
        return password_verify($plain_password, $hash);
    }

    /**
     * Get all customers
     */
    public function get_all_customers() {
        if (!$this->db_connect()) {
            return false;
        }

        $sql = "SELECT id, name, email, country, city, contact, user_role, created_at, updated_at FROM pb_customer ORDER BY created_at DESC";
        return $this->db_fetch_all($sql);
    }

    /**
     * Get all users (from both pb_customer and pb_service_providers)
     * Returns unified user list with all roles
     */
    public function get_all_users() {
        if (!$this->db_connect()) {
            return false;
        }

        // Get customers
        $customer_sql = "SELECT id, name, email, country as country, city, contact, user_role, created_at, updated_at FROM pb_customer";
        $customers = $this->db_fetch_all($customer_sql);

        // Get service providers (photographers and vendors)
        $provider_sql = "SELECT provider_id as id, name, email, NULL as country, city, contact, user_role, created_at, updated_at FROM pb_service_providers";
        $providers = $this->db_fetch_all($provider_sql);

        // Merge both arrays
        $all_users = array();

        if ($customers && is_array($customers)) {
            $all_users = array_merge($all_users, $customers);
        }

        if ($providers && is_array($providers)) {
            $all_users = array_merge($all_users, $providers);
        }

        // Sort by created_at DESC
        usort($all_users, function($a, $b) {
            return strtotime($b['created_at'] ?? 0) - strtotime($a['created_at'] ?? 0);
        });

        return !empty($all_users) ? $all_users : array();
    }

    /**
     * Get users by role (from both pb_customer and pb_service_providers)
     */
    public function get_users_by_role($role) {
        if (!$this->db_connect()) {
            return false;
        }

        $role = intval($role);
        $all_users = array();

        // Get from pb_customer
        $customer_sql = "SELECT id, name, email, country, city, contact, user_role, created_at, updated_at FROM pb_customer WHERE user_role = $role";
        $customers = $this->db_fetch_all($customer_sql);

        // Get from pb_service_providers (roles 2 and 3)
        if ($role === 2 || $role === 3) {
            $provider_sql = "SELECT provider_id as id, name, email, NULL as country, city, contact, user_role, created_at, updated_at FROM pb_service_providers WHERE user_role = $role";
            $providers = $this->db_fetch_all($provider_sql);
            if ($providers && is_array($providers)) {
                $all_users = $providers;
            }
        }

        // Add customers
        if ($customers && is_array($customers)) {
            $all_users = array_merge($all_users, $customers);
        }

        // Sort by created_at DESC
        if (!empty($all_users)) {
            usort($all_users, function($a, $b) {
                return strtotime($b['created_at'] ?? 0) - strtotime($a['created_at'] ?? 0);
            });
        }

        return !empty($all_users) ? $all_users : array();
    }

    /**
     * Get customers by role
     */
    public function get_customers_by_role($role) {
        if (!$this->db_connect()) {
            return false;
        }

        $role = intval($role);
        $sql = "SELECT id, name, email, country, city, contact, user_role, created_at FROM pb_customer WHERE user_role = $role ORDER BY created_at DESC";
        return $this->db_fetch_all($sql);
    }

    /**
     * Get total customer count
     */
    public function get_total_customers() {
        if (!$this->db_connect()) {
            return 0;
        }

        $sql = "SELECT COUNT(*) as total FROM pb_customer";
        $result = $this->db_fetch_one($sql);
        return $result ? intval($result['total']) : 0;
    }

    /**
     * Delete customer (admin only)
     * Handles cascading deletes for related records
     */
    public function delete_customer($id) {
        if (!$this->db_connect()) {
            return false;
        }

        $id = intval($id);

        try {
            // Start transaction
            $this->db->begin_transaction();

            // Delete from pb_reviews (customer reviews)
            $sql = "DELETE FROM pb_reviews WHERE customer_id = $id";
            if (!$this->db_write_query($sql)) {
                throw new Exception('Failed to delete customer reviews');
            }

            // Delete from pb_cart (shopping cart items)
            $sql = "DELETE FROM pb_cart WHERE customer_id = $id";
            if (!$this->db_write_query($sql)) {
                throw new Exception('Failed to delete customer cart');
            }

            // Delete from pb_bookings (service bookings)
            $sql = "DELETE FROM pb_bookings WHERE customer_id = $id";
            if (!$this->db_write_query($sql)) {
                throw new Exception('Failed to delete customer bookings');
            }

            // Delete from pb_orders (product orders)
            // Note: This has ON DELETE RESTRICT, so we need to delete order items first
            $sql = "SELECT order_id FROM pb_orders WHERE customer_id = $id";
            $orders = $this->db_fetch_all($sql);

            if ($orders && is_array($orders)) {
                foreach ($orders as $order) {
                    $order_id = intval($order['order_id']);
                    $sql = "DELETE FROM pb_order_items WHERE order_id = $order_id";
                    if (!$this->db_write_query($sql)) {
                        throw new Exception('Failed to delete order items');
                    }
                }
            }

            // Now delete orders
            $sql = "DELETE FROM pb_orders WHERE customer_id = $id";
            if (!$this->db_write_query($sql)) {
                throw new Exception('Failed to delete customer orders');
            }

            // Finally delete customer
            $sql = "DELETE FROM pb_customer WHERE id = $id";
            if (!$this->db_write_query($sql)) {
                throw new Exception('Failed to delete customer');
            }

            // Commit transaction
            $this->db->commit();
            return true;

        } catch (Exception $e) {
            // Rollback on error
            $this->db->rollback();
            error_log('Delete customer error: ' . $e->getMessage());
            return false;
        }
    }
}
?>
