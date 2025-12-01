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

    // Save payment reference and insert payment record
    $db = new db_connection();
    if (!$db->db_connect()) {
        error_log("VERIFY PAYMENT ERROR: Database connection failed");
        echo json_encode(['success' => false, 'message' => 'Database connection error']);
        exit;
    }

    // Extract payment details from Paystack response
    $payment_channel = isset($result['data']['channel']) ? $db->db->real_escape_string($result['data']['channel']) : '';
    $authorization_code = isset($result['data']['authorization']['authorization_code']) ? $db->db->real_escape_string($result['data']['authorization']['authorization_code']) : '';
    $reference_escaped = $db->db->real_escape_string($reference);

    // Update order payment reference
    $sql = "UPDATE pb_orders SET payment_reference = '$reference_escaped' WHERE order_id = $order_id";
    error_log("VERIFY PAYMENT: Updating order payment reference");

    if (!$db->db_write_query($sql)) {
        $mysql_error = $db->db ? $db->db->error : 'Unknown';
        error_log("VERIFY PAYMENT ERROR: Failed to update order payment reference (MySQL error: $mysql_error)");
    } else {
        error_log("VERIFY PAYMENT: Payment reference updated for order $order_id");
    }

    // Insert into pb_payment table
    $payment_sql = "INSERT INTO pb_payment (order_id, payment_method, transaction_ref, authorization_code, payment_channel)
                    VALUES ($order_id, 'paystack', '$reference_escaped', '$authorization_code', '$payment_channel')";
    error_log("VERIFY PAYMENT: Inserting payment record: $payment_sql");

    if (!$db->db_write_query($payment_sql)) {
        $mysql_error = $db->db ? $db->db->error : 'Unknown';
        error_log("VERIFY PAYMENT ERROR: Failed to insert payment record (MySQL error: $mysql_error)");
        echo json_encode(['success' => false, 'message' => 'Failed to save payment record']);
        exit;
    } else {
        $payment_id = $db->last_insert_id();
        error_log("VERIFY PAYMENT: Payment record created with ID $payment_id for order $order_id");
    }

    // Process order items and create bookings for services
    // Also calculate commissions for vendor products
    $order_items = $order_class->get_order_items($order_id);
    $bookings_created = 0;
    $errors = [];

    // Calculate commissions for vendor products
    require_once __DIR__ . '/../classes/commission_class.php';
    $commission_class = new commission_class();

    if ($order_items && is_array($order_items)) {
        $booking_class = new booking_class();
        $product_class = new product_class();

        // Aggregate items by provider for commission calculation
        $provider_totals = [];

        foreach ($order_items as $item) {
            $product_id = intval($item['product_id']);
            $quantity = isset($item['quantity']) ? intval($item['quantity']) : 1;
            $item_price = floatval($item['price']);
            $item_total = $item_price * $quantity;

            try {
                $product = $product_class->get_product_by_id($product_id);

                if ($product) {
                    // Aggregate vendor product totals by provider
                    if ($product['product_type'] !== 'service' && isset($product['provider_id'])) {
                        $provider_id = intval($product['provider_id']);
                        if (!isset($provider_totals[$provider_id])) {
                            $provider_totals[$provider_id] = 0;
                        }
                        $provider_totals[$provider_id] += $item_total;
                    }

                    // Create or update booking for service products
                    if ($product['product_type'] === 'service') {
                        // Check if there are existing pending bookings for this customer, provider, and product
                        // Look for bookings created recently (within 30 minutes of order)
                        $check_existing_sql = "SELECT b.booking_id
                                               FROM pb_bookings b
                                               JOIN pb_orders o ON o.order_id = $order_id
                                               WHERE b.customer_id = $customer_id
                                               AND b.provider_id = " . intval($product['provider_id']) . "
                                               AND b.product_id = $product_id
                                               AND b.status = 'pending'
                                               AND ABS(TIMESTAMPDIFF(MINUTE, b.created_at, o.order_date)) <= 30
                                               ORDER BY b.created_at DESC
                                               LIMIT $quantity";
                        $existing_bookings = $db->db_fetch_all($check_existing_sql);

                        $existing_count = $existing_bookings ? count($existing_bookings) : 0;

                        error_log("VERIFY PAYMENT: Found $existing_count existing pending bookings for product $product_id, customer $customer_id, order $order_id");

                        // Update existing bookings to 'confirmed'
                        if ($existing_count > 0) {
                            foreach ($existing_bookings as $existing) {
                                $existing_booking_id = intval($existing['booking_id']);
                                $update_sql = "UPDATE pb_bookings SET status = 'confirmed' WHERE booking_id = $existing_booking_id";
                                error_log("VERIFY PAYMENT: Updating existing booking $existing_booking_id status to 'confirmed'");

                                if ($db->db_write_query($update_sql)) {
                                    $bookings_created++;
                                    error_log("VERIFY PAYMENT: Existing booking $existing_booking_id confirmed for order $order_id");

                                    // Create commission for this booking
                                    $commission_result = $commission_class->create_booking_commission($existing_booking_id, intval($product['provider_id']), $item_price);
                                    if ($commission_result) {
                                        error_log("VERIFY PAYMENT: Commission created for booking $existing_booking_id");
                                    }
                                } else {
                                    $mysql_error = $db->db ? $db->db->error : 'Unknown';
                                    error_log("VERIFY PAYMENT ERROR: Failed to update booking $existing_booking_id status (MySQL error: $mysql_error)");
                                }
                            }
                        }

                        // Create new bookings for any remaining quantity
                        $remaining_quantity = $quantity - $existing_count;
                        for ($i = 0; $i < $remaining_quantity; $i++) {
                            $booking_number = $existing_count + $i + 1;
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
                                error_log("VERIFY PAYMENT: Updating new booking $booking_id status to 'confirmed'");

                                if ($db->db_write_query($update_sql)) {
                                    $bookings_created++;
                                    error_log("VERIFY PAYMENT: New booking $booking_id created and confirmed for order $order_id");

                                    // Create commission for this booking
                                    $commission_result = $commission_class->create_booking_commission($booking_id, intval($product['provider_id']), $item_price);
                                    if ($commission_result) {
                                        error_log("VERIFY PAYMENT: Commission created for booking $booking_id");
                                    }
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
                }
            } catch (Exception $e) {
                $errors[] = "Error processing product $product_id: " . $e->getMessage();
                error_log("Error creating booking for product $product_id: " . $e->getMessage());
            }
        }

        // Create commission records for each provider
        foreach ($provider_totals as $provider_id => $total_amount) {
            $commission_result = $commission_class->create_order_commission($order_id, $provider_id, $total_amount);
            if ($commission_result) {
                error_log("VERIFY PAYMENT: Commission created for order $order_id, provider $provider_id, amount $total_amount");
            } else {
                error_log("VERIFY PAYMENT WARNING: Failed to create commission for order $order_id, provider $provider_id");
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
