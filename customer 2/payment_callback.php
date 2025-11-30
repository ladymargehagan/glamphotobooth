<?php
/**
 * Payment Callback Handler
 * customer/payment_callback.php
 */

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../settings/paystack_config.php';

requireLogin();

$reference = isset($_GET['reference']) ? $_GET['reference'] : '';
$customer_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

if (!$reference || !$customer_id) {
    header('Location: ' . SITE_URL . '/customer/cart.php');
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
$err = curl_error($curl);
curl_close($curl);

$payment_verified = false;
$order_id = null;

if (!$err) {
    $result = json_decode($response, true);
    if (isset($result['status']) && $result['status'] && isset($result['data']['status']) && $result['data']['status'] === 'success') {
        $payment_verified = true;
        $order_id = isset($result['data']['metadata']['order_id']) ? intval($result['data']['metadata']['order_id']) : null;
    }
}

if ($payment_verified && $order_id) {
    // Get order and verify it belongs to the logged-in customer
    $order_class = new order_class();
    $order = $order_class->get_order_by_id($order_id);

    if ($order && $order['customer_id'] == $customer_id) {
        // Mark order as paid
        $order_class->update_payment_status($order_id, 'paid');

        // Get order items and create bookings for service products
        $order_items = $order_class->get_order_items($order_id);

        if ($order_items && is_array($order_items)) {
            $booking_class = new booking_class();
            $product_class = new product_class();
            $provider_class = new provider_class();

            foreach ($order_items as $item) {
                $product_id = intval($item['product_id']);
                $product = $product_class->get_product_by_id($product_id);

                if ($product && $product['product_type'] === 'service') {
                    // Get provider info
                    $provider = $provider_class->get_provider_by_id($product['provider_id']);

                    if ($provider) {
                        // Create booking entry for service products
                        $booking_data = [
                            'customer_id' => $customer_id,
                            'provider_id' => $product['provider_id'],
                            'product_id' => $product_id,
                            'booking_date' => date('Y-m-d'),
                            'booking_time' => date('H:i:s'),
                            'duration_hours' => 1.0,
                            'total_price' => $item['price'],
                            'status' => 'pending'
                        ];

                        $booking_class->create_booking(
                            $booking_data['customer_id'],
                            $booking_data['provider_id'],
                            $booking_data['product_id'],
                            $booking_data['booking_date'],
                            $booking_data['booking_time'],
                            $booking_data['duration_hours'],
                            $booking_data['total_price']
                        );
                    }
                }
            }
        }

        // Clear user's cart
        $cart_class = new cart_class();
        $cart_class->clear_cart($customer_id);

        // Redirect to confirmation page
        header('Location: ' . SITE_URL . '/customer/order_confirmation.php?order_id=' . $order_id);
        exit;
    }
}

// Payment failed or verification failed
header('Location: ' . SITE_URL . '/customer/cart.php?error=payment_failed');
exit;
?>
