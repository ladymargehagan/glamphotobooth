<?php
/**
 * Payment Complete Handler
 * customer/payment_complete.php
 * Paystack redirects here after payment with reference parameter
 */

require_once __DIR__ . '/../settings/core.php';

requireLogin();

$reference = isset($_GET['reference']) ? trim($_GET['reference']) : '';
$customer_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

$pageTitle = 'Payment Processing - GlamPhotobooth Accra';
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
    <!-- Global variables for scripts -->
    <script>
        window.isLoggedIn = <?php echo isLoggedIn() ? 'true' : 'false'; ?>;
        window.loginUrl = '<?php echo SITE_URL; ?>/auth/login.php';
        window.siteUrl = '<?php echo SITE_URL; ?>';
    </script>
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

        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid rgba(226, 196, 146, 0.3);
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin: 0 auto var(--spacing-lg);
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .message-box {
            margin-top: var(--spacing-xl);
            padding: var(--spacing-lg);
            border-radius: var(--border-radius);
            font-weight: 500;
        }

        .message-box.success {
            background: rgba(76, 175, 80, 0.1);
            color: #2e7d32;
            border: 1px solid rgba(76, 175, 80, 0.3);
        }

        .message-box.error {
            background: rgba(211, 47, 47, 0.1);
            color: #c62828;
            border: 1px solid rgba(211, 47, 47, 0.3);
        }

        .btn-primary {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            margin-top: var(--spacing-lg);
            transition: background 0.3s;
        }

        .btn-primary:hover {
            background: #0d1a3a;
        }

        .hidden {
            display: none;
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
            <h1>Processing Payment</h1>

            <div id="spinner" class="spinner"></div>
            <p id="processingText">Verifying your payment with Paystack...</p>

            <div id="messageBox" class="message-box hidden"></div>

            <button id="continueBtn" class="btn-primary hidden" onclick="continueShopping()">
                Continue Shopping
            </button>
            <a id="orderLink" class="btn-primary hidden" href="">
                View Order Details
            </a>
        </div>
    </div>

    <?php require_once __DIR__ . '/../views/footer.php'; ?>

    <script>
        window.siteUrl = '<?php echo SITE_URL; ?>';
        const reference = '<?php echo htmlspecialchars($reference); ?>';

        document.addEventListener('DOMContentLoaded', function() {
            if (!reference) {
                showError('No payment reference found');
                return;
            }

            // Verify payment with our AJAX endpoint
            verifyPayment(reference);
        });

        function verifyPayment(paymentReference) {
            const formData = new FormData();
            formData.append('reference', paymentReference);

            fetch(window.siteUrl + '/actions/verify_payment_action.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccess('Payment successful! Your order has been confirmed.', data.order_id);
                } else {
                    showError(data.message || 'Payment verification failed');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('Network error while verifying payment. Please contact support.');
            });
        }

        function showSuccess(message, orderId) {
            document.getElementById('spinner').classList.add('hidden');
            document.getElementById('processingText').classList.add('hidden');

            const msgBox = document.getElementById('messageBox');
            msgBox.textContent = '✓ ' + message;
            msgBox.classList.remove('hidden');
            msgBox.classList.add('success');

            document.getElementById('continueBtn').classList.remove('hidden');

            const orderLink = document.getElementById('orderLink');
            orderLink.href = window.siteUrl + '/customer/order_confirmation.php?order_id=' + orderId;
            orderLink.textContent = 'View Order Details';
            orderLink.classList.remove('hidden');
        }

        function showError(message) {
            document.getElementById('spinner').classList.add('hidden');
            document.getElementById('processingText').classList.add('hidden');

            const msgBox = document.getElementById('messageBox');
            msgBox.textContent = '✗ ' + message;
            msgBox.classList.remove('hidden');
            msgBox.classList.add('error');

            document.getElementById('continueBtn').classList.remove('hidden');
            document.getElementById('continueBtn').textContent = 'Back to Shop';
        }

        function continueShopping() {
            window.location.href = window.siteUrl + '/shop.php';
        }
    </script>
</body>
</html>
