<?php
/**
 * Customer Class
 * Handles customer-related database operations
 */

class customer_class extends db_connection
{
    /**
     * Add a new customer
     */
    public function add_customer($fullName, $email, $phone, $password)
    {
        if (!$this->db_connect()) {
            return false;
        }

        // Check if email already exists
        if ($this->get_customer_by_email($email)) {
            return false;
        }

        // Hash password
        $hashedPassword = hashPassword($password);

        // Insert customer
        $query = "INSERT INTO users (full_name, email, phone, password, role, status, created_at)
                  VALUES (
                      '" . mysqli_real_escape_string($this->db, sanitize($fullName)) . "',
                      '" . mysqli_real_escape_string($this->db, sanitize($email)) . "',
                      '" . mysqli_real_escape_string($this->db, sanitize($phone)) . "',
                      '" . $hashedPassword . "',
                      " . ROLE_CUSTOMER . ",
                      '" . STATUS_ACTIVE . "',
                      NOW()
                  )";

        if ($this->db_write_query($query)) {
            return $this->last_insert_id();
        }

        return false;
    }

    /**
     * Get customer by email
     */
    public function get_customer_by_email($email)
    {
        if (!$this->db_connect()) {
            return false;
        }

        $query = "SELECT id, full_name, email, phone, password, role, status, created_at
                  FROM users
                  WHERE email = '" . mysqli_real_escape_string($this->db, sanitize($email)) . "'
                  AND role = " . ROLE_CUSTOMER;

        return $this->db_fetch_one($query);
    }

    /**
     * Get customer by ID
     */
    public function get_customer_by_id($customerId)
    {
        if (!$this->db_connect()) {
            return false;
        }

        $query = "SELECT id, full_name, email, phone, role, status, created_at
                  FROM users
                  WHERE id = " . intval($customerId) . "
                  AND role = " . ROLE_CUSTOMER;

        return $this->db_fetch_one($query);
    }

    /**
     * Verify login credentials
     */
    public function verify_login($email, $password)
    {
        $customer = $this->get_customer_by_email($email);

        if (!$customer) {
            return false;
        }

        if ($customer['status'] !== STATUS_ACTIVE) {
            return false;
        }

        if (!verifyPassword($password, $customer['password'])) {
            return false;
        }

        return $customer;
    }

    /**
     * Edit customer profile
     */
    public function edit_customer($customerId, $fullName, $phone)
    {
        if (!$this->db_connect()) {
            return false;
        }

        $query = "UPDATE users
                  SET full_name = '" . mysqli_real_escape_string($this->db, sanitize($fullName)) . "',
                      phone = '" . mysqli_real_escape_string($this->db, sanitize($phone)) . "',
                      updated_at = NOW()
                  WHERE id = " . intval($customerId) . "
                  AND role = " . ROLE_CUSTOMER;

        return $this->db_write_query($query);
    }

    /**
     * Update customer password
     */
    public function update_password($customerId, $newPassword)
    {
        if (!$this->db_connect()) {
            return false;
        }

        $hashedPassword = hashPassword($newPassword);

        $query = "UPDATE users
                  SET password = '" . $hashedPassword . "',
                      updated_at = NOW()
                  WHERE id = " . intval($customerId) . "
                  AND role = " . ROLE_CUSTOMER;

        return $this->db_write_query($query);
    }

    /**
     * Delete customer account
     */
    public function delete_customer($customerId)
    {
        if (!$this->db_connect()) {
            return false;
        }

        $query = "DELETE FROM users
                  WHERE id = " . intval($customerId) . "
                  AND role = " . ROLE_CUSTOMER;

        return $this->db_write_query($query);
    }

    /**
     * Get all customers (admin function)
     */
    public function get_all_customers($limit = 20, $offset = 0)
    {
        if (!$this->db_connect()) {
            return false;
        }

        $query = "SELECT id, full_name, email, phone, status, created_at
                  FROM users
                  WHERE role = " . ROLE_CUSTOMER . "
                  ORDER BY created_at DESC
                  LIMIT " . intval($limit) . " OFFSET " . intval($offset);

        return $this->db_fetch_all($query);
    }

    /**
     * Count total customers
     */
    public function count_customers()
    {
        if (!$this->db_connect()) {
            return 0;
        }

        $query = "SELECT COUNT(*) as total FROM users WHERE role = " . ROLE_CUSTOMER;
        $result = $this->db_fetch_one($query);

        return $result ? $result['total'] : 0;
    }
}
?>
