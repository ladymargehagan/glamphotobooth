<?php
/**
 * Shopping Cart Page
 * customer/cart.php
 */
require_once __DIR__ . '/../settings/core.php';

requireLogin();

$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$cart_class = new cart_class();
$cart_items = $cart_class->get_cart($user_id);
$subtotal = $cart_class->get_cart_subtotal($user_id);
$cart_count = $cart_class->get_cart_count($user_id);

$pageTitle = 'Shopping Cart - GlamPhotobooth Accra';
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
        .cart-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: var(--spacing-xxl) var(--spacing-xl);
        }

        .cart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: var(--spacing-xxl);
            flex-wrap: wrap;
            gap: var(--spacing-lg);
        }

        .cart-header h1 {
            color: var(--primary);
            font-family: var(--font-serif);
            font-size: 2rem;
            margin: 0;
        }

        .cart-layout {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: var(--spacing-xl);
        }

        .cart-items {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: var(--spacing-lg);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .cart-item {
            display: grid;
            grid-template-columns: 120px 1fr auto;
            gap: var(--spacing-lg);
            padding: var(--spacing-lg);
            border-bottom: 1px solid var(--border-color);
            align-items: center;
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .cart-item-image {
            width: 120px;
            height: 120px;
            background: var(--light-gray);
            border-radius: var(--border-radius);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            font-size: 2rem;
            color: var(--text-secondary);
        }

        .cart-item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .cart-item-details {
            display: flex;
            flex-direction: column;
            gap: var(--spacing-sm);
        }

        .cart-item-title {
            font-weight: 600;
            color: var(--primary);
            font-size: 1rem;
        }

        .cart-item-type {
            font-size: 0.8rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .cart-item-price {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary);
        }

        .cart-item-actions {
            display: flex;
            flex-direction: column;
            gap: var(--spacing-sm);
            align-items: flex-end;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            overflow: hidden;
        }

        .quantity-control button {
            width: 32px;
            height: 32px;
            border: none;
            background: var(--light-gray);
            cursor: pointer;
            transition: var(--transition);
            font-weight: 600;
        }

        .quantity-control button:hover {
            background: var(--primary);
            color: var(--white);
        }

        .quantity-control input {
            width: 50px;
            border: none;
            text-align: center;
            font-weight: 600;
            background: var(--white);
        }

        .remove-btn {
            padding: 0.5rem 1rem;
            background: #d32f2f;
            color: var(--white);
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.8rem;
        }

        .remove-btn:hover {
            background: #c62828;
        }

        .cart-empty {
            text-align: center;
            padding: var(--spacing-xxl);
        }

        .cart-empty-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto var(--spacing-lg);
            color: var(--text-secondary);
            stroke-width: 1.5;
        }

        .cart-empty-title {
            color: var(--primary);
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: var(--spacing-sm);
        }

        .cart-empty-text {
            color: var(--text-secondary);
            margin-bottom: var(--spacing-lg);
        }

        .btn-continue-shopping {
            padding: 0.75rem 1.5rem;
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-block;
        }

        .btn-continue-shopping:hover {
            background: #0d1a3a;
            transform: translateY(-2px);
        }

        .cart-summary {
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

        .message.success {
            background: rgba(76, 175, 80, 0.1);
            color: #2e7d32;
            border: 1px solid rgba(76, 175, 80, 0.3);
        }

        .message.error {
            background: rgba(211, 47, 47, 0.1);
            color: #c62828;
            border: 1px solid rgba(211, 47, 47, 0.3);
        }

        .message svg {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
        }

        @media (max-width: 768px) {
            .cart-container {
                padding: var(--spacing-lg);
            }

            .cart-header h1 {
                font-size: 1.5rem;
            }

            .cart-layout {
                grid-template-columns: 1fr;
            }

            .cart-item {
                grid-template-columns: 100px 1fr auto;
                gap: var(--spacing-md);
            }

            .cart-item-image {
                width: 100px;
                height: 100px;
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

    <div class="cart-container">
        <div class="cart-header">
            <h1>Shopping Cart</h1>
            <a href="<?php echo SITE_URL; ?>/shop.php" class="btn-continue-shopping">Continue Shopping</a>
        </div>

        <!-- Messages -->
        <div id="successMessage" class="message success">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
            <span id="successText"></span>
        </div>
        <div id="errorMessage" class="message error">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            <span id="errorText"></span>
        </div>

        <?php if ($cart_items && count($cart_items) > 0): ?>
            <div class="cart-layout">
                <!-- Cart Items -->
                <div class="cart-items">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="cart-item" data-product-id="<?php echo $item['product_id']; ?>">
                            <div class="cart-item-image">
                                <?php if ($item['image']): ?>
                                    <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                                <?php else: ?>
                                    📦
                                <?php endif; ?>
                            </div>

                            <div class="cart-item-details">
                                <div class="cart-item-title"><?php echo htmlspecialchars($item['title']); ?></div>
                                <div class="cart-item-type"><?php echo ucfirst($item['product_type']); ?></div>
                                <div class="cart-item-price">₵<?php echo number_format($item['price'], 2); ?></div>
                            </div>

                            <div class="cart-item-actions">
                                <div class="quantity-control">
                                    <button onclick="updateQuantity(<?php echo $item['product_id']; ?>, -1)">−</button>
                                    <input type="number" value="<?php echo intval($item['quantity']); ?>" readonly>
                                    <button onclick="updateQuantity(<?php echo $item['product_id']; ?>, 1)">+</button>
                                </div>
                                <button class="remove-btn" onclick="removeFromCart(<?php echo $item['product_id']; ?>)">Remove</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Cart Summary -->
                <div class="cart-summary">
                    <div class="summary-title">Order Summary</div>
                    <div class="summary-row">
                        <span>Subtotal (<?php echo $cart_count; ?> items)</span>
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
                    <a href="<?php echo SITE_URL; ?>/customer/checkout.php" class="btn-checkout" style="text-decoration: none; text-align: center; display: block;">Proceed to Checkout</a>
                </div>
            </div>
        <?php else: ?>
            <div class="cart-items">
                <div class="cart-empty">
                    <svg class="cart-empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                    <h3 class="cart-empty-title">Your cart is empty</h3>
                    <p class="cart-empty-text">Start adding items to your cart by browsing our shop</p>
                    <a href="<?php echo SITE_URL; ?>/shop.php" class="btn-continue-shopping">Continue Shopping</a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php require_once __DIR__ . '/../views/footer.php'; ?>

    <script>
        // Store CSRF token for use in cart operations
        const csrfToken = '<?php echo htmlspecialchars(generateCSRFToken()); ?>';

        function updateQuantity(productId, change) {
            const item = document.querySelector(`[data-product-id="${productId}"]`);
            if (!item) return;

            const input = item.querySelector('input[type="number"]');
            const newQuantity = Math.max(0, parseInt(input.value) + change);

            updateCart(productId, newQuantity);
        }

        function removeFromCart(productId) {
            showConfirmAlert('Remove Item', 'Are you sure you want to remove this item from your cart?', function() {
                updateCart(productId, 0);
            });
        }

        function updateCart(productId, quantity) {
            const formData = new FormData();
            formData.append('csrf_token', csrfToken);
            formData.append('product_id', productId);
            formData.append('quantity', quantity);

            fetch('<?php echo SITE_URL; ?>/actions/update_cart_action.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update cart badge
                    if (window.updateCartBadgeCount) {
                        window.updateCartBadgeCount(data.cart_count || 0);
                    }

                    // If quantity is 0, remove the item from DOM
                    if (quantity === 0) {
                        const item = document.querySelector(`[data-product-id="${productId}"]`);
                        if (item) {
                            item.style.opacity = '0.5';
                            setTimeout(() => {
                                item.remove();
                                // Check if cart is now empty
                                if (document.querySelectorAll('.cart-item').length === 0) {
                                    location.reload();
                                } else {
                                    updateCartTotals();
                                }
                            }, 300);
                        }
                    } else {
                        // Update the quantity in the DOM without reload
                        const item = document.querySelector(`[data-product-id="${productId}"]`);
                        if (item) {
                            const input = item.querySelector('input[type="number"]');
                            if (input) {
                                input.value = quantity;
                            }
                        }
                        updateCartTotals();
                    }
                } else {
                    showError(data.message || 'Failed to update cart');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('Network error. Please try again.');
            });
        }

        function updateCartTotals() {
            let subtotal = 0;
            let itemCount = 0;

            // Get all cart items
            const items = document.querySelectorAll('.cart-item');
            items.forEach(item => {
                const priceText = item.querySelector('.cart-item-price').textContent;
                const price = parseFloat(priceText.replace('₵', '').replace(',', ''));
                const quantity = parseInt(item.querySelector('input[type="number"]').value);

                subtotal += price * quantity;
                itemCount += quantity;
            });

            // Update summary
            const summaryRows = document.querySelectorAll('.summary-row');
            summaryRows.forEach(row => {
                const label = row.querySelector('span:first-child').textContent.trim();

                if (label.startsWith('Subtotal')) {
                    row.querySelector('span:last-child').textContent = '₵' + subtotal.toFixed(2);
                } else if (label === 'Total') {
                    row.querySelector('span:last-child').textContent = '₵' + subtotal.toFixed(2);
                }
            });
        }

        function showError(message) {
            const msg = document.getElementById('errorMessage');
            document.getElementById('errorText').textContent = message;
            msg.classList.add('show');
        }
    </script>
</body>
</html>
