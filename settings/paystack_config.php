<?php
/**
 * Paystack Configuration
 * settings/paystack_config.php
 */

// Paystack API Keys - Replace with your actual keys
define('PAYSTACK_PUBLIC_KEY', 'pk_test_cd389d93978a547260b8e8362def282f0b015eb6');
define('PAYSTACK_SECRET_KEY', 'sk_test_1e8943d18e45b8c3148212a55d433ecd0bf892cf'); // You'll provide this

// Paystack API URLs
define('PAYSTACK_INITIALIZE_URL', 'https://api.paystack.co/transaction/initialize');
define('PAYSTACK_VERIFY_URL', 'https://api.paystack.co/transaction/verify/');

// Callback URLs
define('PAYSTACK_CALLBACK_URL', SITE_URL . '/customer/payment_callback.php');
?>
