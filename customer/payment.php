<?php
/**
 * Payment Page
 * customer/payment.php
 */
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../settings/paystack_config.php';

requireLogin();

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if ($order_id <= 0) {
    header('Location: ' . SITE_URL . '/customer/cart.php');
    exit;
}

// Verify order belongs to user
$order_class = new order_class();
$order = $order_class->get_order_by_id($order_id);

if (!$order || $order['customer_id'] != $_SESSION['user_id']) {
    header('Location: ' . SITE_URL . '/customer/cart.php');
    exit;
}

$pageTitle = 'Complete Payment - GlamPhotobooth Accra';
$cssPath = SITE_URL . '/css/style.css';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($cssPath); ?>">
    <!-- SweetAlert2 Library -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Global variables for scripts -->
    <script>
        window.isLoggedIn = <?php echo isLoggedIn() ? 'true' : 'false'; ?>;
        window.loginUrl = '<?php echo SITE_URL; ?>/auth/login.php';
        window.siteUrl = '<?php echo SITE_URL; ?>';
    </script>
    <script src="<?php echo SITE_URL; ?>/js/sweetalert.js"></script>
    <script src="<?php echo SITE_URL; ?>/js/cart.js"></script>
    <style>
        .payment-container {
            max-width: 600px;
            margin: 0 auto;
            padding: var(--spacing-xxl) var(--spacing-xl);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: calc(100vh - 200px);
        }

        .payment-card {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: var(--spacing-xxl);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            text-align: center;
            width: 100%;
        }

        .payment-card h1 {
            color: var(--primary);
            font-family: var(--font-serif);
            font-size: 1.75rem;
            margin-bottom: var(--spacing-lg);
        }

        .payment-amount {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: var(--spacing-lg);
        }

        .payment-details {
            background: rgba(226, 196, 146, 0.05);
            padding: var(--spacing-lg);
            border-radius: var(--border-radius);
            margin-bottom: var(--spacing-lg);
            text-align: left;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: var(--spacing-md);
            font-size: 0.95rem;
        }

        .detail-label {
            color: var(--text-secondary);
        }

        .detail-value {
            color: var(--text-primary);
            font-weight: 600;
        }

        .btn-pay {
            width: 100%;
            padding: 1rem 1.5rem;
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            font-size: 1rem;
            margin-bottom: var(--spacing-md);
        }

        .btn-pay:hover {
            background: #0d1a3a;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 33, 82, 0.2);
        }

        .btn-pay:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }

        .loading-spinner {
            display: none;
            width: 40px;
            height: 40px;
            border: 4px solid rgba(226, 196, 146, 0.3);
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin: 0 auto var(--spacing-lg);
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .payment-instruction {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-top: var(--spacing-lg);
        }

        .message {
            padding: var(--spacing-md);
            border-radius: var(--border-radius);
            margin-bottom: var(--spacing-lg);
            display: none;
        }

        .message.show {
            display: block;
        }

        .message.error {
            background: rgba(211, 47, 47, 0.1);
            color: #c62828;
            border: 1px solid rgba(211, 47, 47, 0.3);
        }
    </style>
</head>
<body>
    <!-- Navigation Header -->
    <header class="navbar">
        <div class="container">
            <div class="flex-between">
                <div class="navbar-brand">
                    <a href="/" class="logo">
                        <h3 class="font-serif text-primary m-0">GlamPhotobooth Accra</h3>
                    </a>
                </div>

                <nav class="navbar-menu">
                    <ul class="navbar-items">
                        <li><a href="<?php echo SITE_URL; ?>/index.php" class="nav-link">Home</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/shop.php" class="nav-link">Products & Services</a></li>
                    </ul>
                </nav>

                <div class="navbar-actions flex gap-sm">
                    <?php if (isLoggedIn()): ?>
                        <a href="<?php echo SITE_URL; ?>/customer/cart.php" class="cart-icon" title="Shopping Cart" style="position: relative; display: flex; align-items: center;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 24px; height: 24px;">
                                <circle cx="9" cy="21" r="1"></circle>
                                <circle cx="20" cy="21" r="1"></circle>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                            </svg>
                            <span id="cartBadge" class="cart-badge" style="display: none;"></span>
                        </a>
                        <a href="<?php echo getDashboardUrl(); ?>" class="btn btn-sm btn-outline">Dashboard</a>
                        <a href="<?php echo SITE_URL; ?>/actions/logout.php" class="btn btn-sm btn-primary">Logout</a>
                    <?php else: ?>
                        <a href="<?php echo SITE_URL; ?>/auth/login.php" class="btn btn-sm btn-outline">Login</a>
                        <a href="<?php echo SITE_URL; ?>/auth/register.php" class="btn btn-sm btn-primary">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <div class="payment-container">
        <div class="payment-card">
            <h1>Complete Your Payment</h1>
            
            <div id="errorMessage" class="message error"></div>

            <div class="payment-amount">₵<?php echo number_format($order['total_amount'], 2); ?></div>

            <div class="payment-details">
                <div class="detail-row">
                    <span class="detail-label">Order ID:</span>
                    <span class="detail-value">#<?php echo $order_id; ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Customer:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($order['customer_name']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Email:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($order['email']); ?></span>
                </div>
            </div>

            <div class="loading-spinner" id="loadingSpinner"></div>

            <button class="btn-pay" id="payButton" onclick="initializePayment(<?php echo $order_id; ?>)">
                Pay with Paystack
            </button>

            <p class="payment-instruction">
                You will be redirected to Paystack to complete your payment securely.
            </p>
        </div>
    </div>

    <?php require_once __DIR__ . '/../views/footer.php'; ?>

    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
    <input type="hidden" id="orderAmount" value="<?php echo $order['total_amount']; ?>">
    <input type="hidden" id="customerEmail" value="<?php echo htmlspecialchars($order['email']); ?>">
    <input type="hidden" id="paystackPublicKey" value="<?php echo PAYSTACK_PUBLIC_KEY; ?>">

    <!-- Paystack Inline JS -->
    <script src="https://js.paystack.co/v1/inline.js"></script>

    <script>
        window.siteUrl = '<?php echo SITE_URL; ?>';

        function initializePayment(orderId) {
            const button = document.getElementById('payButton');
            const spinner = document.getElementById('loadingSpinner');
            const errorMsg = document.getElementById('errorMessage');
            const amount = parseFloat(document.getElementById('orderAmount').value);
            const email = document.getElementById('customerEmail').value;
            const publicKey = document.getElementById('paystackPublicKey').value;

            button.disabled = true;
            button.textContent = 'Opening Paystack...';

            // Use Paystack Inline (Popup) instead of redirect
            // Paystack will show all available payment channels
            const handler = PaystackPop.setup({
                key: publicKey,
                email: email,
                amount: amount * 100, // Convert to pesewas/kobo
                currency: 'GHS',
                ref: 'ord_' + orderId + '_' + Math.floor((Math.random() * 1000000000) + 1),
                channels: ['card', 'bank', 'mobile_money'],
                metadata: {
                    order_id: orderId,
                    custom_fields: [
                        {
                            display_name: "Order ID",
                            variable_name: "order_id",
                            value: orderId
                        }
                    ]
                },
                onClose: function() {
                    button.disabled = false;
                    button.textContent = 'Pay with Paystack';
                    showError('Payment cancelled');
                },
                callback: function(response) {
                    button.textContent = 'Verifying payment...';
                    // Verify the payment
                    verifyPayment(response.reference);
                }
            });

            handler.openStandard();
        }

        function verifyPayment(reference) {
            const formData = new FormData();
            formData.append('reference', reference);

            fetch(window.siteUrl + '/actions/verify_payment_action.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Redirect to order confirmation
                    window.location.href = data.redirect || (window.siteUrl + '/customer/order_confirmation.php?order_id=' + data.order_id);
                } else {
                    showError(data.message || 'Payment verification failed');
                    document.getElementById('payButton').disabled = false;
                    document.getElementById('payButton').textContent = 'Pay with Paystack';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('Error verifying payment. Please contact support.');
                document.getElementById('payButton').disabled = false;
                document.getElementById('payButton').textContent = 'Pay with Paystack';
            });
        }

        function showError(message) {
            const errorMsg = document.getElementById('errorMessage');
            errorMsg.textContent = message;
            errorMsg.classList.add('show');
        }
    </script>
</body>
</html>
