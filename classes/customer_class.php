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
        $password_hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $email = strtolower(trim($email));
        $name = htmlspecialchars(trim($name));

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
        $email = strtolower(trim($email));
        $sql = "SELECT id FROM pb_customer WHERE email = '$email' LIMIT 1";
        $result = $this->db_fetch_one($sql);
        return $result ? true : false;
    }

    /**
     * Get customer by email
     */
    public function get_customer_by_email($email) {
        $email = strtolower(trim($email));
        $sql = "SELECT id, name, email, password, user_role FROM pb_customer WHERE email = '$email' LIMIT 1";
        return $this->db_fetch_one($sql);
    }

    /**
     * Get customer by ID
     */
    public function get_customer_by_id($id) {
        $sql = "SELECT id, name, email, country, city, contact, image, user_role, created_at FROM pb_customer WHERE id = $id LIMIT 1";
        return $this->db_fetch_one($sql);
    }

    /**
     * Update customer profile
     */
    public function update_customer($id, $name, $country, $city, $contact) {
        $name = htmlspecialchars(trim($name));
        $country = htmlspecialchars(trim($country));
        $city = htmlspecialchars(trim($city));
        $contact = htmlspecialchars(trim($contact));

        $sql = "UPDATE pb_customer SET name = '$name', country = '$country', city = '$city', contact = '$contact', updated_at = NOW() WHERE id = $id";

        return $this->db_write_query($sql);
    }

    /**
     * Change password
     */
    public function change_password($id, $new_password) {
        $password_hash = password_hash($new_password, PASSWORD_BCRYPT, ['cost' => 12]);
        $sql = "UPDATE pb_customer SET password = '$password_hash', updated_at = NOW() WHERE id = $id";
        return $this->db_write_query($sql);
    }

    /**
     * Verify password
     */
    public function verify_password($plain_password, $hash) {
        return password_verify($plain_password, $hash);
    }
}
?>
