<?php
/**
 * Customer Controller
 * controllers/customer_controller.php
 */

require_once __DIR__ . '/../settings/core.php';

class customer_controller {

    private $customer;

    public function __construct() {
        $this->customer = new customer_class();
        $this->customer->db_connect();
    }

    /**
     * Register customer or provider (photographer/vendor)
     * Role 4 (customers) → pb_customer table
     * Role 2,3 (photographers/vendors) → pb_service_providers table
     */
    public function register_customer_ctr($name, $email, $password, $confirm_password, $role, $phone = '', $city = '', $business_name = '', $description = '', $hourly_rate = 0) {
        // Validation
        if (empty($name) || empty($email) || empty($password) || empty($role)) {
            return ['success' => false, 'message' => 'All fields are required'];
        }

        if (strlen($password) < 6) {
            return ['success' => false, 'message' => 'Password must be at least 6 characters'];
        }

        if ($password !== $confirm_password) {
            return ['success' => false, 'message' => 'Passwords do not match'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }

        // Check valid role (exclude admin)
        $valid_roles = [2, 3, 4]; // photographer, vendor, customer
        if (!in_array($role, $valid_roles)) {
            return ['success' => false, 'message' => 'Invalid role selected'];
        }

        // For photographers (role 2) and vendors (role 3), use provider registration
        if ($role === 2 || $role === 3) {
            return $this->register_provider_ctr($name, $email, $password, $role, $phone, $city, $business_name, $description, $hourly_rate);
        }

        // For customers (role 4), use customer registration
        // Check if email exists in customer table
        if ($this->customer->check_email_exists($email)) {
            return ['success' => false, 'message' => 'Email already registered'];
        }

        // Add customer to pb_customer table
        $customer_id = $this->customer->add_customer($name, $email, $password, $role, $phone, $city);

        if ($customer_id) {
            $_SESSION['user_id'] = $customer_id;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_role'] = $role;

            return ['success' => true, 'message' => 'Registration successful', 'customer_id' => $customer_id];
        }

        return ['success' => false, 'message' => 'Registration failed. Please try again'];
    }

    /**
     * Register provider (photographer or vendor)
     * Saves directly to pb_service_providers table with all business info
     */
    private function register_provider_ctr($name, $email, $password, $role, $phone = '', $city = '', $business_name = '', $description = '', $hourly_rate = 0) {
        $provider_class = new provider_class();
        $provider_class->db_connect();

        // Check if email exists in provider table
        if ($provider_class->check_provider_email_exists($email)) {
            return ['success' => false, 'message' => 'Email already registered'];
        }

        // Add provider directly to pb_service_providers table with all info
        $provider_id = $provider_class->add_provider_full($name, $email, $password, $role, $phone, $city, $business_name, $description, $hourly_rate);

        if ($provider_id) {
            $_SESSION['user_id'] = $provider_id;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_role'] = $role;

            return ['success' => true, 'message' => 'Registration successful', 'customer_id' => $provider_id];
        }

        return ['success' => false, 'message' => 'Registration failed. Please try again'];
    }

    /**
     * Login customer or provider (photographer/vendor)
     * Checks both pb_customer (for customers) and pb_service_providers (for photographers/vendors)
     */
    public function login_customer_ctr($email, $password) {
        // Validation
        if (empty($email) || empty($password)) {
            return ['success' => false, 'message' => 'Email and password are required'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }

        // Try to get user from pb_customer first (for customers - role 4)
        $customer = $this->customer->get_customer_by_email($email);

        if ($customer && isset($customer['password']) && !empty($customer['password'])) {
            // Verify password
            $password_valid = $this->customer->verify_password($password, $customer['password']);

            if ($password_valid) {
                // Set session for customer
                $_SESSION['user_id'] = $customer['id'];
                $_SESSION['user_name'] = $customer['name'];
                $_SESSION['user_email'] = $customer['email'];
                $_SESSION['user_role'] = $customer['user_role'];

                return ['success' => true, 'message' => 'Login successful', 'user_id' => $customer['id'], 'user_role' => $customer['user_role']];
            }
        }

        // If not found in pb_customer or password invalid, try pb_service_providers (for photographers/vendors - role 2,3)
        $provider_class = new provider_class();
        $provider_class->db_connect();
        $provider = $provider_class->get_provider_by_email($email);

        if ($provider && isset($provider['password']) && !empty($provider['password'])) {
            // Verify password
            $password_valid = password_verify($password, $provider['password']);

            if ($password_valid) {
                // Set session for provider
                $_SESSION['user_id'] = $provider['provider_id'];
                $_SESSION['user_name'] = $provider['name'];
                $_SESSION['user_email'] = $provider['email'];
                $_SESSION['user_role'] = $provider['user_role'];

                return ['success' => true, 'message' => 'Login successful', 'user_id' => $provider['provider_id'], 'user_role' => $provider['user_role']];
            }
        }

        // Invalid credentials
        return ['success' => false, 'message' => 'Invalid email or password'];
    }

    /**
     * Logout customer
     */
    public function logout_customer_ctr() {
        session_destroy();
        return ['success' => true, 'message' => 'Logged out successfully'];
    }
}
?>
