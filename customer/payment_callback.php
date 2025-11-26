<?php
/**
 * Payment Callback Handler
 * customer/payment_callback.php
 */

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../settings/paystack_config.php';

requireLogin();

$reference = isset($_GET['reference']) ? $_GET['reference'] : '';

if (!$reference) {
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
    if (isset($result['status']) && $result['status'] && $result['data']['status'] === 'success') {
        $payment_verified = true;
        $order_id = $result['data']['metadata']['order_id'];
    }
}

if ($payment_verified && $order_id) {
    // Update order status
    $order_class = new order_class();
    $order = $order_class->get_order_by_id($order_id);

    if ($order && $order['customer_id'] == $_SESSION['user_id']) {
        // Mark order as paid
        $order_class->update_payment_status($order_id, 'paid');

        // Clear user's cart
        $cart_class = new cart_class();
        $cart_class->clear_cart($_SESSION['user_id']);

        // Redirect to confirmation page
        header('Location: ' . SITE_URL . '/customer/order_confirmation.php?order_id=' . $order_id);
        exit;
    }
}

// Payment failed or verification failed
header('Location: ' . SITE_URL . '/customer/cart.php?error=payment_failed');
exit;
?>
