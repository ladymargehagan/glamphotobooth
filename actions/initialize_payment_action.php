<?php
/**
 * Initialize Payment Action
 * actions/initialize_payment_action.php
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../settings/paystack_config.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
        $csrf_token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';

        if ($order_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid order ID']);
            exit;
        }

        // Verify CSRF token
        if (!isset($_SESSION['csrf_token']) || $csrf_token !== $_SESSION['csrf_token']) {
            echo json_encode(['success' => false, 'message' => 'Invalid security token']);
            exit;
        }

        // Get order details
        $order_class = new order_class();
        $order = $order_class->get_order_by_id($order_id);

        if (!$order) {
            echo json_encode(['success' => false, 'message' => 'Order not found']);
            exit;
        }

        // Verify order belongs to current user
        if ($order['customer_id'] != $_SESSION['user_id']) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
            exit;
        }

        // Prepare Paystack request
        $amount = intval($order['total_amount'] * 100); // Convert to kobo
        $callback_url = SITE_URL . '/customer/payment_complete.php';

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => PAYSTACK_INITIALIZE_URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode([
                'amount' => $amount,
                'email' => $order['email'],
                'callback_url' => $callback_url,
                'metadata' => [
                    'order_id' => $order_id,
                    'customer_name' => $order['first_name'] . ' ' . $order['last_name']
                ]
            ]),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Bearer " . PAYSTACK_SECRET_KEY
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            error_log('Paystack API error: ' . $err);
            echo json_encode(['success' => false, 'message' => 'Payment initialization failed']);
            exit;
        }

        $result = json_decode($response, true);

        if (isset($result['status']) && $result['status']) {
            // Store payment reference in order
            $order_class->update_payment_reference($order_id, $result['data']['reference']);

            echo json_encode([
                'success' => true,
                'authorization_url' => $result['data']['authorization_url'],
                'reference' => $result['data']['reference']
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => $result['message'] ?? 'Payment initialization failed'
            ]);
        }
        exit;
    } catch (Exception $e) {
        error_log('Initialize payment error: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error initializing payment']);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
?>
