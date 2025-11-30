<?php
/**
 * Payment Request Class
 * classes/payment_request_class.php
 * Handles payment request operations
 */

class payment_request_class extends db_connection {

    /**
     * Create a payment request
     */
    public function create_payment_request($provider_id, $user_role, $requested_amount, $payment_method, $payment_details) {
        if (!$this->db_connect()) {
            return ['success' => false, 'message' => 'Database connection failed'];
        }

        // Validate amount - ensure commission_class is loaded
        if (!class_exists('commission_class')) {
            require_once __DIR__ . '/commission_class.php';
        }
        $commission_class = new commission_class();
        $available = $commission_class->get_provider_available_earnings($provider_id);
        
        if ($available === false) {
            return ['success' => false, 'message' => 'Failed to calculate available earnings'];
        }
        
        if ($requested_amount > $available) {
            return ['success' => false, 'message' => 'Requested amount exceeds available earnings'];
        }

        if ($requested_amount <= 0) {
            return ['success' => false, 'message' => 'Requested amount must be greater than zero'];
        }

        $provider_id = intval($provider_id);
        $user_role = intval($user_role);
        $requested_amount = round(floatval($requested_amount), 2);
        $available = round(floatval($available), 2);
        $payment_method = $this->db->real_escape_string($payment_method);
        
        // Ensure payment_method is valid (remove paypal if it exists in enum)
        if ($payment_method === 'paypal') {
            $payment_method = 'other';
        }
        
        // Parse payment details
        $account_name = isset($payment_details['account_name']) ? trim($payment_details['account_name']) : '';
        $account_number = isset($payment_details['account_number']) ? trim($payment_details['account_number']) : '';
        $bank_name = isset($payment_details['bank_name']) ? trim($payment_details['bank_name']) : '';
        $mobile_network = isset($payment_details['mobile_network']) ? trim($payment_details['mobile_network']) : '';

        // Escape for SQL
        $account_name_escaped = $account_name ? $this->db->real_escape_string($account_name) : '';
        $account_number_escaped = $account_number ? $this->db->real_escape_string($account_number) : '';
        $bank_name_escaped = $bank_name ? $this->db->real_escape_string($bank_name) : '';
        $mobile_network_escaped = $mobile_network ? $this->db->real_escape_string($mobile_network) : '';

        $account_name_sql = $account_name_escaped ? "'$account_name_escaped'" : "NULL";
        $account_number_sql = $account_number_escaped ? "'$account_number_escaped'" : "NULL";
        $bank_name_sql = $bank_name_escaped ? "'$bank_name_escaped'" : "NULL";
        $mobile_network_sql = $mobile_network_escaped ? "'$mobile_network_escaped'" : "NULL";

        // Build payment_details JSON (store as JSON string)
        $payment_details_json = json_encode($payment_details);
        if ($payment_details_json === false) {
            $payment_details_json = '{}';
        }
        $payment_details_escaped = $this->db->real_escape_string($payment_details_json);
        $payment_details_sql = "'$payment_details_escaped'";
        
        $sql = "INSERT INTO pb_payment_requests 
                (provider_id, user_role, requested_amount, available_earnings, payment_method, payment_details, account_name, account_number, bank_name, mobile_network, status)
                VALUES ($provider_id, $user_role, $requested_amount, $available, '$payment_method', $payment_details_sql, $account_name_sql, $account_number_sql, $bank_name_sql, $mobile_network_sql, 'pending')";

        error_log('Payment request SQL: ' . $sql);
        
        if ($this->db_write_query($sql)) {
            $request_id = $this->last_insert_id();
            return ['success' => true, 'request_id' => $request_id];
        }
        
        $error = $this->db ? $this->db->error : 'Unknown database error';
        error_log('Payment request SQL error: ' . $error . ' | SQL: ' . $sql);
        return ['success' => false, 'message' => 'Failed to create payment request. Error: ' . $error];
    }

    /**
     * Get payment requests by provider
     */
    public function get_provider_requests($provider_id) {
        if (!$this->db_connect()) {
            return false;
        }

        $provider_id = intval($provider_id);
        $sql = "SELECT * FROM pb_payment_requests 
                WHERE provider_id = $provider_id 
                ORDER BY requested_at DESC";
        
        return $this->db_fetch_all($sql);
    }

    /**
     * Get all payment requests (for admin)
     */
    public function get_all_requests($status = null) {
        if (!$this->db_connect()) {
            return false;
        }

        $sql = "SELECT pr.*, 
                       sp.business_name, sp.email,
                       c.name as provider_name
                FROM pb_payment_requests pr
                LEFT JOIN pb_service_providers sp ON pr.provider_id = sp.provider_id
                LEFT JOIN pb_customer c ON pr.provider_id = c.id
                WHERE 1=1";
        
        if ($status) {
            $status = $this->db->real_escape_string($status);
            $sql .= " AND pr.status = '$status'";
        }
        
        $sql .= " ORDER BY pr.requested_at DESC";
        
        return $this->db_fetch_all($sql);
    }

    /**
     * Get payment request by ID
     */
    public function get_request_by_id($request_id) {
        if (!$this->db_connect()) {
            return false;
        }

        $request_id = intval($request_id);
        $sql = "SELECT pr.*, 
                       sp.business_name, sp.email,
                       c.name as provider_name
                FROM pb_payment_requests pr
                LEFT JOIN pb_service_providers sp ON pr.provider_id = sp.provider_id
                LEFT JOIN pb_customer c ON pr.provider_id = c.id
                WHERE pr.request_id = $request_id";
        
        return $this->db_fetch_one($sql);
    }

    /**
     * Update payment request status (admin only)
     */
    public function update_request_status($request_id, $status, $admin_id, $admin_notes = null, $payment_reference = null) {
        if (!$this->db_connect()) {
            return false;
        }

        $request_id = intval($request_id);
        $admin_id = intval($admin_id);
        $status = $this->db->real_escape_string($status);
        
        $allowed_statuses = ['pending', 'approved', 'paid', 'rejected', 'cancelled'];
        if (!in_array($status, $allowed_statuses)) {
            return false;
        }

        $notes_sql = $admin_notes ? "'" . $this->db->real_escape_string($admin_notes) . "'" : "NULL";
        $ref_sql = $payment_reference ? "'" . $this->db->real_escape_string($payment_reference) . "'" : "NULL";
        
        $processed_at = ($status == 'paid') ? ", processed_at = CURRENT_TIMESTAMP" : "";

        $sql = "UPDATE pb_payment_requests 
                SET status = '$status', 
                    processed_by = $admin_id,
                    admin_notes = $notes_sql,
                    payment_reference = $ref_sql
                    $processed_at
                WHERE request_id = $request_id";

        return $this->db_write_query($sql);
    }

    /**
     * Get pending requests count
     */
    public function get_pending_count() {
        if (!$this->db_connect()) {
            return 0;
        }

        $sql = "SELECT COUNT(*) as count FROM pb_payment_requests WHERE status = 'pending'";
        $result = $this->db_fetch_one($sql);
        
        return $result ? intval($result['count']) : 0;
    }
}
?>

