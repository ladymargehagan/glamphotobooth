<?php
/**
 * Admin Controller
 * controllers/admin_controller.php
 */

class admin_controller {

    /**
     * Admin login
     */
    public function admin_login_ctr($email, $password) {
        if (empty($email) || empty($password)) {
            return ['success' => false, 'message' => 'Email and password are required'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }

        $admin_class = new admin_class();
        $admin = $admin_class->get_admin_by_email($email);

        if (!$admin) {
            return ['success' => false, 'message' => 'Admin account not found'];
        }

        if (!$admin['is_active']) {
            return ['success' => false, 'message' => 'Admin account is inactive'];
        }

        if (!$admin_class->verify_password($password, $admin['password'])) {
            return ['success' => false, 'message' => 'Invalid password'];
        }

        // Update last login
        $admin_class->update_last_login($admin['admin_id']);

        // Set session
        $_SESSION['admin_id'] = $admin['admin_id'];
        $_SESSION['admin_name'] = $admin['name'];
        $_SESSION['admin_email'] = $admin['email'];
        $_SESSION['user_role'] = 1; // Admin role
        $_SESSION['is_admin'] = true;

        return ['success' => true, 'message' => 'Login successful', 'admin_id' => $admin['admin_id']];
    }

    /**
     * Admin logout
     */
    public function admin_logout_ctr() {
        session_destroy();
        return ['success' => true, 'message' => 'Logout successful'];
    }
}
?>
