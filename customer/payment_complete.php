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

$pageTitle = 'Payment Processing - PhotoMarket';
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
    <?php require_once __DIR__ . '/../views/header.php'; ?>

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
