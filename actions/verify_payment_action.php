<?php
/**
 * Verify Payment Action - AJAX endpoint for payment verification
 * actions/verify_payment_action.php
 */

// Disable error display to ensure clean JSON
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Start output buffering
ob_start();

header('Content-Type: application/json');

try {
    require_once __DIR__ . '/../settings/core.php';
    require_once __DIR__ . '/../settings/paystack_config.php';

    // Clear any buffered output
    ob_clean();

    requireLogin();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        exit;
    }

    $reference = isset($_POST['reference']) ? trim($_POST['reference']) : '';
    $customer_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

    if (!$reference) {
        echo json_encode(['success' => false, 'message' => 'Missing payment reference']);
        exit;
    }

    if (!$customer_id) {
        echo json_encode(['success' => false, 'message' => 'User not logged in']);
        exit;
    }

    // Check Paystack configuration
    if (!defined('PAYSTACK_VERIFY_URL') || !defined('PAYSTACK_SECRET_KEY')) {
        error_log('Paystack configuration missing');
        echo json_encode(['success' => false, 'message' => 'Payment system not configured']);
        exit;
    }

    // Verify payment with Paystack API
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => PAYSTACK_VERIFY_URL . $reference,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json",
            "Authorization: Bearer " . PAYSTACK_SECRET_KEY
        ),
    ));

    $response = curl_exec($curl);
    $curl_error = curl_error($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    if ($curl_error) {
        error_log('Paystack verification curl error: ' . $curl_error);
        echo json_encode(['success' => false, 'message' => 'Network error verifying payment']);
        exit;
    }

    if ($http_code !== 200) {
        error_log("Paystack API returned HTTP $http_code: $response");
        echo json_encode(['success' => false, 'message' => 'Payment verification failed']);
        exit;
    }

    $result = json_decode($response, true);

    // Validate Paystack response
    if (!isset($result['status']) || !$result['status']) {
        error_log('Invalid Paystack response: ' . json_encode($result));
        echo json_encode(['success' => false, 'message' => 'Invalid payment response']);
        exit;
    }

    if (!isset($result['data']['status']) || $result['data']['status'] !== 'success') {
        echo json_encode(['success' => false, 'message' => 'Payment was not successful']);
        exit;
    }

    // Extract order ID from metadata
    $order_id = isset($result['data']['metadata']['order_id']) ? intval($result['data']['metadata']['order_id']) : 0;

    if (!$order_id) {
        error_log('No order ID in Paystack metadata: ' . json_encode($result['data']['metadata']));
        echo json_encode(['success' => false, 'message' => 'Order not found in payment']);
        exit;
    }

    // Verify order belongs to current user
    $order_class = new order_class();
    $order = $order_class->get_order_by_id($order_id);

    if (!$order) {
        error_log("Order not found: $order_id");
        echo json_encode(['success' => false, 'message' => 'Order not found']);
        exit;
    }

    if ($order['customer_id'] != $customer_id) {
        error_log("Order ownership mismatch: order customer={$order['customer_id']}, session customer=$customer_id");
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    // Update order payment status
    error_log("VERIFY PAYMENT: Updating order $order_id status to 'paid'");
    if (!$order_class->update_payment_status($order_id, 'paid')) {
        error_log("VERIFY PAYMENT ERROR: Failed to update payment status for order $order_id");
        echo json_encode(['success' => false, 'message' => 'Failed to update order payment status']);
        exit;
    }
    error_log("VERIFY PAYMENT: Order $order_id status updated to 'paid' successfully");

    // Save payment reference
    $db = new db_connection();
    if (!$db->db_connect()) {
        error_log("VERIFY PAYMENT ERROR: Database connection failed when saving payment reference");
        echo json_encode(['success' => false, 'message' => 'Database connection error']);
        exit;
    }

    $reference_escaped = $db->db->real_escape_string($reference);
    $sql = "UPDATE pb_orders SET payment_reference = '$reference_escaped' WHERE order_id = $order_id";
    error_log("VERIFY PAYMENT: Executing SQL: $sql");

    $update_result = $db->db_write_query($sql);
    if (!$update_result) {
        $mysql_error = $db->db ? $db->db->error : 'Unknown';
        error_log("VERIFY PAYMENT ERROR: Failed to save payment reference for order $order_id (MySQL error: $mysql_error)");
        // Don't exit - continue with booking creation
    } else {
        error_log("VERIFY PAYMENT: Payment reference '$reference' saved for order $order_id");
    }

    // Process order items and create bookings for services
    $order_items = $order_class->get_order_items($order_id);
    $bookings_created = 0;
    $errors = [];

    if ($order_items && is_array($order_items)) {
        $booking_class = new booking_class();
        $product_class = new product_class();

        foreach ($order_items as $item) {
            $product_id = intval($item['product_id']);
            $quantity = isset($item['quantity']) ? intval($item['quantity']) : 1;

            try {
                $product = $product_class->get_product_by_id($product_id);

                if ($product && $product['product_type'] === 'service') {
                    // Create booking(s) - one per quantity
                    for ($i = 0; $i < $quantity; $i++) {
                        $booking_number = $i + 1;
                        $service_description = $product['title'];
                        if ($quantity > 1) {
                            $service_description .= " (#" . $booking_number . " of " . $quantity . ")";
                        }

                        // Call with correct 7 parameters
                        $booking_id = $booking_class->create_booking(
                            $customer_id,
                            $product['provider_id'],
                            $product_id,
                            date('Y-m-d', strtotime('+1 day')),
                            '10:00:00',
                            $service_description,
                            'Paid via Paystack'
                        );

                        if ($booking_id) {
                            // CRITICAL: Update booking status to 'confirmed' since payment is complete
                            $update_sql = "UPDATE pb_bookings SET status = 'confirmed' WHERE booking_id = $booking_id";
                            error_log("VERIFY PAYMENT: Updating booking $booking_id status to 'confirmed'");

                            if ($db->db_write_query($update_sql)) {
                                $bookings_created++;
                                error_log("VERIFY PAYMENT: Booking $booking_id created and confirmed for order $order_id");
                            } else {
                                $mysql_error = $db->db ? $db->db->error : 'Unknown';
                                error_log("VERIFY PAYMENT ERROR: Booking $booking_id created but failed to update status (MySQL error: $mysql_error)");
                                $bookings_created++;
                            }
                        } else {
                            $errors[] = "Failed to create booking for product $product_id";
                            error_log("VERIFY PAYMENT ERROR: Failed to create booking for product $product_id, order $order_id");
                        }
                    }
                }
            } catch (Exception $e) {
                $errors[] = "Error processing product $product_id: " . $e->getMessage();
                error_log("Error creating booking for product $product_id: " . $e->getMessage());
            }
        }
    }

    // Clear cart
    try {
        $cart_class = new cart_class();
        $cart_class->clear_cart($customer_id);
    } catch (Exception $e) {
        error_log("Error clearing cart: " . $e->getMessage());
    }

    // Return success
    echo json_encode([
        'success' => true,
        'message' => 'Payment verified successfully',
        'order_id' => $order_id,
        'bookings_created' => $bookings_created,
        'redirect' => SITE_URL . '/customer/order_confirmation.php?order_id=' . $order_id
    ]);
    exit;

} catch (Exception $e) {
    ob_clean();
    error_log('Payment verification exception: ' . $e->getMessage());
    error_log('Stack trace: ' . $e->getTraceAsString());
    echo json_encode(['success' => false, 'message' => 'System error: ' . $e->getMessage()]);
    exit;
} catch (Error $e) {
    ob_clean();
    error_log('Payment verification fatal error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'System error occurred']);
    exit;
}
?>
