<?php
/**
 * Admin Class
 * classes/admin_class.php
 */

class admin_class extends db_connection {

    /**
     * Add new admin
     */
    public function add_admin($name, $email, $password) {
        if (!$this->db_connect()) {
            return false;
        }

        $password_hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $email = strtolower(trim($email));
        $name = trim($name);

        // Escape SQL inputs
        $name = mysqli_real_escape_string($this->db, $name);
        $email = mysqli_real_escape_string($this->db, $email);
        // Password hash should NOT be escaped - it's already a safe 60-character string from password_hash()

        $sql = "INSERT INTO pb_admin (name, email, password, role, is_active, created_at)
                VALUES ('$name', '$email', '$password_hash', 'admin', 1, NOW())";

        if ($this->db_write_query($sql)) {
            return $this->last_insert_id();
        }
        return false;
    }

    /**
     * Get admin by email
     */
    public function get_admin_by_email($email) {
        if (!$this->db_connect()) {
            return false;
        }

        $email = strtolower(trim($email));
        $email = mysqli_real_escape_string($this->db, $email);
        $sql = "SELECT admin_id, name, email, password, role, is_active FROM pb_admin WHERE email = '$email' AND is_active = 1 LIMIT 1";
        return $this->db_fetch_one($sql);
    }

    /**
     * Get admin by ID
     */
    public function get_admin_by_id($admin_id) {
        if (!$this->db_connect()) {
            return false;
        }

        $admin_id = intval($admin_id);
        $sql = "SELECT admin_id, name, email, role, is_active, last_login, created_at FROM pb_admin WHERE admin_id = $admin_id LIMIT 1";
        return $this->db_fetch_one($sql);
    }

    /**
     * Check if email exists
     */
    public function admin_email_exists($email) {
        if (!$this->db_connect()) {
            return false;
        }

        $email = strtolower(trim($email));
        $email = mysqli_real_escape_string($this->db, $email);
        $sql = "SELECT admin_id FROM pb_admin WHERE email = '$email' LIMIT 1";
        $result = $this->db_fetch_one($sql);
        return $result ? true : false;
    }

    /**
     * Verify password
     */
    public function verify_password($plain_password, $hash) {
        return password_verify($plain_password, $hash);
    }

    /**
     * Update last login
     */
    public function update_last_login($admin_id) {
        if (!$this->db_connect()) {
            return false;
        }

        $admin_id = intval($admin_id);
        $sql = "UPDATE pb_admin SET last_login = NOW() WHERE admin_id = $admin_id";
        return $this->db_write_query($sql);
    }
}
?>
