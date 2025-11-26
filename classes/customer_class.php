<?php
/**
 * Customer Class
 * classes/customer_class.php
 */

class customer_class extends db_connection {

    /**
     * Add new customer (register)
     */
    public function add_customer($name, $email, $password, $user_role = 4) {
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
        // Escaping it can corrupt the hash and break password verification
        $user_role = intval($user_role);

        $sql = "INSERT INTO pb_customer (name, email, password, user_role, created_at)
                VALUES ('$name', '$email', '$password_hash', $user_role, NOW())";

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
}
?>
