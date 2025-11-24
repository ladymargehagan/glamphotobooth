<?php
/**
 * Authentication Controller
 * Handles login/logout and session management
 */

require_once __DIR__ . '/../settings/core.php';

class auth_controller
{
    private $customer;

    public function __construct()
    {
        $this->customer = new customer_class();
    }

    /**
     * Login customer
     */
    public function login($email, $password, $rememberMe = false)
    {
        $response = [
            'success' => false,
            'message' => '',
            'errors' => []
        ];

        // Validate input
        if (empty($email) || empty($password)) {
            $response['errors']['general'] = 'Email and password are required';
            return $response;
        }

        if (!isValidEmail($email)) {
            $response['errors']['email'] = 'Invalid email format';
            return $response;
        }

        // Verify credentials
        $customer = $this->customer->verify_login($email, $password);

        if (!$customer) {
            $response['errors']['general'] = 'Invalid email or password';
            return $response;
        }

        // Set session variables
        $_SESSION['user_id'] = $customer['id'];
        $_SESSION['user_name'] = $customer['full_name'];
        $_SESSION['user_email'] = $customer['email'];
        $_SESSION['user_role'] = $customer['role'];

        // Handle remember me
        if ($rememberMe) {
            $token = bin2hex(random_bytes(32));
            $expiry = time() + (30 * 24 * 60 * 60); // 30 days

            // Store token in database (optional - for added security)
            // setcookie('remember_token', $token, $expiry, '/', '', false, true);
        }

        $response['success'] = true;
        $response['message'] = 'Login successful';

        return $response;
    }

    /**
     * Logout customer
     */
    public function logout()
    {
        // Destroy session
        session_destroy();

        // Delete remember me cookie if exists
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/');
        }

        return true;
    }

    /**
     * Register customer
     */
    public function register($fullName, $email, $phone, $password, $confirmPassword)
    {
        $response = [
            'success' => false,
            'message' => '',
            'errors' => []
        ];

        // Validate inputs
        $errors = $this->validateRegistration($fullName, $email, $phone, $password, $confirmPassword);

        if (!empty($errors)) {
            $response['errors'] = $errors;
            return $response;
        }

        // Add customer to database
        $customerId = $this->customer->add_customer($fullName, $email, $phone, $password);

        if (!$customerId) {
            $response['message'] = 'Email already exists or registration failed';
            return $response;
        }

        // Auto-login after registration
        $customer = $this->customer->get_customer_by_id($customerId);

        $_SESSION['user_id'] = $customer['id'];
        $_SESSION['user_name'] = $customer['full_name'];
        $_SESSION['user_email'] = $customer['email'];
        $_SESSION['user_role'] = $customer['role'];

        $response['success'] = true;
        $response['message'] = 'Account created successfully';

        return $response;
    }

    /**
     * Validate registration input
     */
    private function validateRegistration($fullName, $email, $phone, $password, $confirmPassword)
    {
        $errors = [];

        // Full name validation
        if (empty($fullName)) {
            $errors['full_name'] = 'Full name is required';
        } elseif (strlen($fullName) < 2) {
            $errors['full_name'] = 'Full name must be at least 2 characters';
        } elseif (strlen($fullName) > 100) {
            $errors['full_name'] = 'Full name must be less than 100 characters';
        }

        // Email validation
        if (empty($email)) {
            $errors['email'] = 'Email is required';
        } elseif (!isValidEmail($email)) {
            $errors['email'] = 'Invalid email format';
        }

        // Phone validation
        if (empty($phone)) {
            $errors['phone'] = 'Phone number is required';
        } elseif (!preg_match('/^[\d\s\-\+\(\)]+$/', $phone)) {
            $errors['phone'] = 'Invalid phone number format';
        } elseif (strlen($phone) < 7) {
            $errors['phone'] = 'Phone number must be at least 7 characters';
        }

        // Password validation
        if (empty($password)) {
            $errors['password'] = 'Password is required';
        } elseif (strlen($password) < 8) {
            $errors['password'] = 'Password must be at least 8 characters';
        } elseif (!preg_match('/[A-Z]/', $password)) {
            $errors['password'] = 'Password must contain at least one uppercase letter';
        } elseif (!preg_match('/[a-z]/', $password)) {
            $errors['password'] = 'Password must contain at least one lowercase letter';
        } elseif (!preg_match('/[0-9]/', $password)) {
            $errors['password'] = 'Password must contain at least one number';
        }

        // Confirm password validation
        if (empty($confirmPassword)) {
            $errors['confirm_password'] = 'Confirm password is required';
        } elseif ($password !== $confirmPassword) {
            $errors['confirm_password'] = 'Passwords do not match';
        }

        return $errors;
    }

    /**
     * Check if email exists
     */
    public function emailExists($email)
    {
        return (bool) $this->customer->get_customer_by_email($email);
    }
}
?>
