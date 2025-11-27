<?php
/**
 * Verify Payment Action - AJAX endpoint for payment verification
 * actions/verify_payment_action.php
 * This is called after Paystack payment completes
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../settings/paystack_config.php';

try {
    requireLogin();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        exit;
    }

    $reference = isset($_POST['reference']) ? trim($_POST['reference']) : '';
    $customer_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

    if (!$reference || !$customer_id) {
        echo json_encode(['success' => false, 'message' => 'Missing reference or customer ID']);
        exit;
    }

    // Verify payment with Paystack
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
    curl_close($curl);

    if ($curl_error) {
        error_log('Paystack verification curl error: ' . $curl_error);
        echo json_encode(['success' => false, 'message' => 'Payment verification failed']);
        exit;
    }

    $result = json_decode($response, true);

    // Check if payment was successful
    if (!isset($result['status']) || !$result['status'] || !isset($result['data']['status'])) {
        error_log('Paystack response invalid: ' . json_encode($result));
        echo json_encode(['success' => false, 'message' => 'Payment verification failed']);
        exit;
    }

    if ($result['data']['status'] !== 'success') {
        echo json_encode(['success' => false, 'message' => 'Payment was not successful']);
        exit;
    }

    // Get order ID from metadata
    $order_id = isset($result['data']['metadata']['order_id']) ? intval($result['data']['metadata']['order_id']) : 0;

    if (!$order_id) {
        error_log('No order ID in Paystack metadata');
        echo json_encode(['success' => false, 'message' => 'Order not found']);
        exit;
    }

    // Get order and verify it belongs to current user
    $order_class = new order_class();
    $order = $order_class->get_order_by_id($order_id);

    if (!$order) {
        echo json_encode(['success' => false, 'message' => 'Order not found']);
        exit;
    }

    if ($order['customer_id'] != $customer_id) {
        error_log("Order customer mismatch: order customer={$order['customer_id']}, session customer={$customer_id}");
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    // Update order payment status to 'paid'
    if (!$order_class->update_payment_status($order_id, 'paid')) {
        error_log("Failed to update order {$order_id} payment status");
        echo json_encode(['success' => false, 'message' => 'Failed to update order']);
        exit;
    }

    // Get order items and create bookings for service products
    $order_items = $order_class->get_order_items($order_id);

    if ($order_items && is_array($order_items) && count($order_items) > 0) {
        $booking_class = new booking_class();
        $product_class = new product_class();
        $provider_class = new provider_class();

        foreach ($order_items as $item) {
            $product_id = intval($item['product_id']);
            $quantity = isset($item['quantity']) ? intval($item['quantity']) : 1;
            $product = $product_class->get_product_by_id($product_id);

            if ($product) {
                // For service products, create booking(s) - ONE PER QUANTITY
                if ($product['product_type'] === 'service') {
                    $provider = $provider_class->get_provider_by_id($product['provider_id']);

                    if ($provider) {
                        // Create multiple bookings if quantity > 1
                        for ($i = 0; $i < $quantity; $i++) {
                            $booking_id = $booking_class->create_booking(
                                $customer_id,
                                $product['provider_id'],
                                $product_id,
                                date('Y-m-d', strtotime('+1 day')), // Default to tomorrow
                                '10:00:00',
                                1.0,
                                floatval($item['price']),
                                'Booked via ' . $product['title'] . ($quantity > 1 ? " (#{$i + 1} of {$quantity})" : ''),
                                ''
                            );

                            if ($booking_id) {
                                error_log("Booking created: ID={$booking_id} for order {$order_id}, item quantity {$i + 1}/{$quantity}");
                            } else {
                                error_log("Failed to create booking for order {$order_id}, item quantity {$i + 1}/{$quantity}");
                            }
                        }
                    }
                }
            }
        }
    }

    // Clear user's cart
    $cart_class = new cart_class();
    $cart_class->clear_cart($customer_id);

    // Return success with order ID
    echo json_encode([
        'success' => true,
        'message' => 'Payment successful',
        'order_id' => $order_id,
        'redirect' => SITE_URL . '/customer/order_confirmation.php?order_id=' . $order_id
    ]);
    exit;

} catch (Exception $e) {
    error_log('Verify payment error: ' . $e->getMessage());
    error_log('Stack trace: ' . $e->getTraceAsString());
    echo json_encode(['success' => false, 'message' => 'Error verifying payment']);
    exit;
}
?>
