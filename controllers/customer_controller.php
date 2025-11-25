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
     * Register customer
     */
    public function register_customer_ctr($name, $email, $password, $confirm_password, $role) {
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

        // Check if email exists
        if ($this->customer->check_email_exists($email)) {
            return ['success' => false, 'message' => 'Email already registered'];
        }

        // Add customer
        $customer_id = $this->customer->add_customer($name, $email, $password, $role);

        if ($customer_id) {
            $_SESSION['user_id'] = $customer_id;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_role'] = $role;

            return ['success' => true, 'message' => 'Registration successful', 'user_id' => $customer_id];
        }

        return ['success' => false, 'message' => 'Registration failed. Please try again'];
    }

    /**
     * Login customer
     */
    public function login_customer_ctr($email, $password) {
        // Validation
        if (empty($email) || empty($password)) {
            return ['success' => false, 'message' => 'Email and password are required'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }

        // Get customer
        $customer = $this->customer->get_customer_by_email($email);

        if (!$customer) {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }

        // Verify password
        if (!$this->customer->verify_password($password, $customer['password'])) {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }

        // Set session
        $_SESSION['user_id'] = $customer['id'];
        $_SESSION['user_name'] = $customer['name'];
        $_SESSION['user_email'] = $customer['email'];
        $_SESSION['user_role'] = $customer['user_role'];

        return ['success' => true, 'message' => 'Login successful', 'user_id' => $customer['id'], 'user_role' => $customer['user_role']];
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
