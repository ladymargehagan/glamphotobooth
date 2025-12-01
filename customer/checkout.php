<?php
/**
 * Checkout Page
 * customer/checkout.php
 */
require_once __DIR__ . '/../settings/core.php';

requireLogin();

$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$cart_class = new cart_class();
$cart_items = $cart_class->get_cart($user_id);
$subtotal = $cart_class->get_cart_subtotal($user_id);

if (!$cart_items || count($cart_items) == 0) {
    header('Location: ' . SITE_URL . '/customer/cart.php');
    exit;
}

$pageTitle = 'Checkout - GlamPhotobooth Accra';
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
        .checkout-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: var(--spacing-xxl) var(--spacing-xl);
        }

        .checkout-header {
            text-align: center;
            margin-bottom: var(--spacing-xxl);
        }

        .checkout-header h1 {
            color: var(--primary);
            font-family: var(--font-serif);
            font-size: 2rem;
            margin-bottom: var(--spacing-sm);
        }

        .checkout-layout {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: var(--spacing-xl);
        }

        .checkout-form {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: var(--spacing-lg);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .form-section {
            margin-bottom: var(--spacing-xl);
            padding-bottom: var(--spacing-xl);
            border-bottom: 1px solid var(--border-color);
        }

        .form-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .form-section h2 {
            color: var(--primary);
            font-size: 1.25rem;
            margin-bottom: var(--spacing-lg);
            font-weight: 600;
        }

        .form-group {
            margin-bottom: var(--spacing-md);
        }

        .form-group label {
            display: block;
            margin-bottom: var(--spacing-xs);
            color: var(--text-primary);
            font-weight: 500;
            font-size: 0.9rem;
        }

        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="tel"] {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            font-size: 0.95rem;
            transition: var(--transition);
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(226, 196, 146, 0.1);
        }

        .payment-methods {
            display: flex;
            flex-direction: column;
            gap: var(--spacing-md);
        }

        .payment-option {
            display: flex;
            align-items: center;
            padding: var(--spacing-md);
            border: 2px solid var(--border-color);
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: var(--transition);
        }

        .payment-option:hover {
            border-color: var(--primary);
            background: rgba(226, 196, 146, 0.05);
        }

        .payment-option input[type="radio"] {
            margin-right: var(--spacing-md);
            cursor: pointer;
        }

        .payment-option input[type="radio"]:checked + label {
            color: var(--primary);
            font-weight: 600;
        }

        .payment-option label {
            margin: 0;
            cursor: pointer;
            flex: 1;
        }

        .order-summary {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: var(--spacing-lg);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            height: fit-content;
        }

        .summary-title {
            font-weight: 600;
            color: var(--primary);
            margin-bottom: var(--spacing-lg);
            padding-bottom: var(--spacing-lg);
            border-bottom: 1px solid var(--border-color);
            font-size: 0.95rem;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: var(--spacing-md);
            font-size: 0.9rem;
        }

        .summary-item-name {
            color: var(--text-primary);
            max-width: 180px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .summary-item-price {
            color: var(--primary);
            font-weight: 600;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: var(--spacing-md);
            font-size: 0.9rem;
        }

        .summary-row.total {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary);
            padding-top: var(--spacing-md);
            border-top: 1px solid var(--border-color);
            margin-bottom: var(--spacing-lg);
        }

        .btn-checkout {
            width: 100%;
            padding: 1rem;
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.95rem;
        }

        .btn-checkout:hover {
            background: #0d1a3a;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 33, 82, 0.2);
        }

        .btn-checkout:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }

        .message {
            padding: var(--spacing-md);
            border-radius: var(--border-radius);
            margin-bottom: var(--spacing-lg);
            display: none;
            align-items: center;
            gap: var(--spacing-sm);
            font-weight: 500;
        }

        .message.show {
            display: flex;
        }

        .message.error {
            background: rgba(211, 47, 47, 0.1);
            color: #c62828;
            border: 1px solid rgba(211, 47, 47, 0.3);
        }

        @media (max-width: 768px) {
            .checkout-container {
                padding: var(--spacing-lg);
            }

            .checkout-header h1 {
                font-size: 1.5rem;
            }

            .checkout-layout {
                grid-template-columns: 1fr;
            }
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

    <div class="checkout-container">
        <div class="checkout-header">
            <h1>Checkout</h1>
        </div>

        <!-- Error Message -->
        <div id="errorMessage" class="message error">
            <span id="errorText"></span>
        </div>

        <div class="checkout-layout">
            <!-- Checkout Form -->
            <div class="checkout-form">
                <form id="checkoutForm">
                    <!-- Delivery Information -->
                    <div class="form-section">
                        <h2>Delivery Information</h2>
                        <div class="form-group">
                            <label for="firstName">First Name</label>
                            <input type="text" id="firstName" name="firstName" required>
                        </div>
                        <div class="form-group">
                            <label for="lastName">Last Name</label>
                            <input type="text" id="lastName" name="lastName" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" required>
                        </div>
                        <div class="form-group">
                            <label for="address">Delivery Address</label>
                            <input type="text" id="address" name="address" required>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="form-section">
                        <h2>Payment Method</h2>
                        <div class="payment-methods">
                            <div class="payment-option">
                                <input type="radio" id="paystack-card" name="paymentMethod" value="paystack" data-channel="card" checked>
                                <label for="paystack-card">Card Payment (Debit/Credit Card)</label>
                            </div>
                            <div class="payment-option">
                                <input type="radio" id="paystack-bank" name="paymentMethod" value="paystack" data-channel="bank">
                                <label for="paystack-bank">Bank Transfer (Direct Bank Account)</label>
                            </div>
                            <div class="payment-option">
                                <input type="radio" id="paystack-mobile" name="paymentMethod" value="paystack" data-channel="mobile_money">
                                <label for="paystack-mobile">Mobile Money (MTN, Vodafone, AirtelTigo)</label>
                            </div>
                        </div>
                        <p style="font-size: 0.85rem; color: var(--text-secondary); margin-top: var(--spacing-md);">
                            All payments are securely processed through Paystack. Choose your preferred payment method above.
                        </p>
                    </div>

                    <!-- CSRF Token -->
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                </form>
            </div>

            <!-- Order Summary -->
            <div class="order-summary">
                <div class="summary-title">Order Summary</div>
                <?php foreach ($cart_items as $item): ?>
                    <div class="summary-item">
                        <span class="summary-item-name"><?php echo htmlspecialchars($item['title']); ?></span>
                        <span class="summary-item-price">₵<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                    </div>
                <?php endforeach; ?>
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>₵<?php echo number_format($subtotal, 2); ?></span>
                </div>
                <div class="summary-row">
                    <span>Shipping</span>
                    <span>₵0.00</span>
                </div>
                <div class="summary-row">
                    <span>Tax</span>
                    <span>₵0.00</span>
                </div>
                <div class="summary-row total">
                    <span>Total</span>
                    <span>₵<?php echo number_format($subtotal, 2); ?></span>
                </div>
                <button class="btn-checkout" onclick="proceedToPayment()">Proceed to Payment</button>
            </div>
        </div>
    </div>

    <?php require_once __DIR__ . '/../views/footer.php'; ?>

    <script src="<?php echo SITE_URL; ?>/js/checkout.js"></script>
</body>
</html>
