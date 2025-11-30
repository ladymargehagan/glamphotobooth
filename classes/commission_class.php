<?php
/**
 * Commission Class
 * classes/commission_class.php
 * Handles commission calculations and tracking
 */

class commission_class extends db_connection {

    const COMMISSION_RATE = 5.00; // 5% platform commission

    /**
     * Calculate commission for a transaction
     */
    private function calculate_commission($gross_amount) {
        $gross = floatval($gross_amount);
        $commission_rate = self::COMMISSION_RATE;
        $commission_amount = ($gross * $commission_rate) / 100;
        $provider_earnings = $gross - $commission_amount;
        
        return [
            'gross_amount' => $gross,
            'commission_rate' => $commission_rate,
            'commission_amount' => round($commission_amount, 2),
            'provider_earnings' => round($provider_earnings, 2)
        ];
    }

    /**
     * Create commission record for an order
     */
    public function create_order_commission($order_id, $provider_id, $gross_amount) {
        if (!$this->db_connect()) {
            return false;
        }

        // Check if commission already exists for this order and provider
        $check_sql = "SELECT commission_id FROM pb_commissions 
                     WHERE transaction_type = 'order' 
                     AND transaction_id = " . intval($order_id) . "
                     AND provider_id = " . intval($provider_id);
        $existing = $this->db_fetch_one($check_sql);
        
        if ($existing) {
            return $existing['commission_id']; // Already exists
        }

        $calc = $this->calculate_commission($gross_amount);
        
        $order_id = intval($order_id);
        $provider_id = intval($provider_id);
        $gross = floatval($calc['gross_amount']);
        $rate = floatval($calc['commission_rate']);
        $commission = floatval($calc['commission_amount']);
        $earnings = floatval($calc['provider_earnings']);

        $sql = "INSERT INTO pb_commissions 
                (transaction_type, transaction_id, provider_id, gross_amount, commission_rate, commission_amount, provider_earnings, order_id)
                VALUES ('order', $order_id, $provider_id, $gross, $rate, $commission, $earnings, $order_id)";

        if ($this->db_write_query($sql)) {
            return $this->last_insert_id();
        }
        return false;
    }

    /**
     * Create commission record for a booking
     */
    public function create_booking_commission($booking_id, $provider_id, $gross_amount) {
        if (!$this->db_connect()) {
            return false;
        }

        // Check if commission already exists for this booking
        $check_sql = "SELECT commission_id FROM pb_commissions 
                     WHERE transaction_type = 'booking' 
                     AND transaction_id = " . intval($booking_id) . "
                     AND provider_id = " . intval($provider_id);
        $existing = $this->db_fetch_one($check_sql);
        
        if ($existing) {
            return $existing['commission_id']; // Already exists
        }

        $calc = $this->calculate_commission($gross_amount);
        
        $booking_id = intval($booking_id);
        $provider_id = intval($provider_id);
        $gross = floatval($calc['gross_amount']);
        $rate = floatval($calc['commission_rate']);
        $commission = floatval($calc['commission_amount']);
        $earnings = floatval($calc['provider_earnings']);

        $sql = "INSERT INTO pb_commissions 
                (transaction_type, transaction_id, provider_id, gross_amount, commission_rate, commission_amount, provider_earnings, booking_id)
                VALUES ('booking', $booking_id, $provider_id, $gross, $rate, $commission, $earnings, $booking_id)";

        if ($this->db_write_query($sql)) {
            return $this->last_insert_id();
        }
        return false;
    }

    /**
     * Get total platform commission
     */
    public function get_total_platform_commission() {
        if (!$this->db_connect()) {
            return 0;
        }

        $sql = "SELECT SUM(commission_amount) as total FROM pb_commissions";
        $result = $this->db_fetch_one($sql);
        
        return $result ? floatval($result['total']) : 0;
    }

    /**
     * Get provider total earnings (from commissions)
     */
    public function get_provider_total_earnings($provider_id) {
        if (!$this->db_connect()) {
            return 0;
        }

        $provider_id = intval($provider_id);
        $sql = "SELECT SUM(provider_earnings) as total FROM pb_commissions WHERE provider_id = $provider_id";
        $result = $this->db_fetch_one($sql);
        
        return $result ? floatval($result['total']) : 0;
    }

    /**
     * Get provider available earnings (not yet requested)
     */
    public function get_provider_available_earnings($provider_id) {
        if (!$this->db_connect()) {
            return 0;
        }

        $provider_id = intval($provider_id);
        
        // Total earnings
        $total_earnings = $this->get_provider_total_earnings($provider_id);
        
        // Total requested (pending, approved, or paid)
        $sql = "SELECT SUM(requested_amount) as total_requested 
                FROM pb_payment_requests 
                WHERE provider_id = $provider_id 
                AND status IN ('pending', 'approved', 'paid')";
        $result = $this->db_fetch_one($sql);
        $total_requested = $result ? floatval($result['total_requested']) : 0;
        
        $available = $total_earnings - $total_requested;
        return max(0, $available); // Can't be negative
    }

    /**
     * Get commission stats for admin dashboard
     */
    public function get_commission_stats() {
        if (!$this->db_connect()) {
            return false;
        }

        $stats = [];
        
        // Total commission
        $stats['total_commission'] = $this->get_total_platform_commission();
        
        // Commission this month
        $sql = "SELECT SUM(commission_amount) as total 
                FROM pb_commissions 
                WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) 
                AND YEAR(created_at) = YEAR(CURRENT_DATE())";
        $result = $this->db_fetch_one($sql);
        $stats['commission_this_month'] = $result ? floatval($result['total']) : 0;
        
        // Commission by month (last 6 months)
        $sql = "SELECT 
                    DATE_FORMAT(created_at, '%Y-%m') as month,
                    SUM(commission_amount) as total
                FROM pb_commissions
                WHERE created_at >= DATE_SUB(CURRENT_DATE(), INTERVAL 6 MONTH)
                GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                ORDER BY month DESC";
        $result = $this->db_fetch_all($sql);
        $stats['commission_by_month'] = $result ? $result : [];
        
        return $stats;
    }
}
?>

