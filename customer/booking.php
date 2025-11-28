<?php
/**
 * Booking Page
 * customer/booking.php
 */

require_once __DIR__ . '/../settings/core.php';
requireLogin();

$provider_id = isset($_GET['provider_id']) ? intval($_GET['provider_id']) : 0;
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

if ($provider_id <= 0 || $product_id <= 0) {
    header('Location: ' . SITE_URL . '/shop.php');
    exit;
}

// Get provider info
if (!class_exists('provider_class')) {
    require_once __DIR__ . '/../classes/provider_class.php';
}
$provider = new provider_class();
$provider_info = $provider->get_provider_by_id($provider_id);

if (!$provider_info) {
    header('Location: ' . SITE_URL . '/shop.php');
    exit;
}

// Get product info
if (!class_exists('product_class')) {
    require_once __DIR__ . '/../classes/product_class.php';
}
$product = new product_class();
$product_info = $product->get_product_by_id($product_id);

if (!$product_info) {
    header('Location: ' . SITE_URL . '/shop.php');
    exit;
}

$pageTitle = 'Book ' . htmlspecialchars($provider_info['business_name']);
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

    <script>
        window.siteUrl = '<?php echo SITE_URL; ?>';
    </script>

    <style>
        .booking-page {
            max-width: 800px;
            margin: 0 auto;
            padding: var(--spacing-xxl) var(--spacing-xl);
        }

        .booking-card {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: var(--spacing-xl);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .booking-title {
            color: var(--primary);
            font-family: var(--font-serif);
            font-size: 1.8rem;
            margin-bottom: var(--spacing-md);
            text-align: center;
        }

        .provider-summary {
            background: var(--light-gray);
            padding: var(--spacing-md);
            border-radius: var(--border-radius);
            margin-bottom: var(--spacing-lg);
            text-align: center;
        }

        .provider-name {
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--primary);
        }

        .product-name {
            color: var(--text-secondary);
            font-size: 0.95rem;
            margin-top: var(--spacing-xs);
        }

        .form-group {
            margin-bottom: var(--spacing-lg);
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: var(--spacing-xs);
            color: var(--primary);
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            font-family: inherit;
            font-size: 0.95rem;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(226, 196, 146, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .submit-btn {
            width: 100%;
            padding: 1rem;
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: var(--transition);
        }

        .submit-btn:hover {
            background: #0d1a3a;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 33, 82, 0.2);
        }

        .submit-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }

        .price-info {
            text-align: center;
            padding: var(--spacing-md);
            background: rgba(226, 196, 146, 0.1);
            border-radius: var(--border-radius);
            margin-bottom: var(--spacing-lg);
        }

        .price-label {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .price-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
        }

        @media (max-width: 768px) {
            .booking-page {
                padding: var(--spacing-lg);
            }

            .booking-card {
                padding: var(--spacing-lg);
            }

            .booking-title {
                font-size: 1.4rem;
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
                        <h3 class="font-serif text-primary m-0">PhotoMarket</h3>
                    </a>
                </div>

                <nav class="navbar-menu">
                    <ul class="navbar-items">
                        <li><a href="<?php echo SITE_URL; ?>/index.php" class="nav-link">Home</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/shop.php" class="nav-link">Shop</a></li>
                    </ul>
                </nav>

                <div class="navbar-actions flex gap-sm">
                    <a href="<?php echo SITE_URL; ?>/customer/cart.php" class="btn btn-sm btn-outline">Cart</a>
                    <a href="<?php echo getDashboardUrl(); ?>" class="btn btn-sm btn-outline">Dashboard</a>
                    <a href="<?php echo SITE_URL; ?>/actions/logout.php" class="btn btn-sm btn-primary">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <div class="booking-page">
        <div class="booking-card">
            <h1 class="booking-title">Request a Booking</h1>

            <div class="provider-summary">
                <div class="provider-name"><?php echo htmlspecialchars($provider_info['business_name']); ?></div>
                <div class="product-name"><?php echo htmlspecialchars($product_info['title']); ?></div>
            </div>

            <div class="price-info">
                <div class="price-label">Service Price</div>
                <div class="price-value">₵<?php echo number_format($product_info['price'], 2); ?></div>
            </div>

            <form id="booking_form">
                <div class="form-group">
                    <label for="booking_date">Select Date *</label>
                    <input type="date" id="booking_date" name="booking_date" required>
                </div>

                <div class="form-group">
                    <label for="booking_time">Select Time *</label>
                    <select id="booking_time" name="booking_time" required>
                        <option value="">Select a date first</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="service_description">Service Description *</label>
                    <textarea id="service_description" name="service_description" placeholder="Describe what you need (minimum 10 characters)" required></textarea>
                </div>

                <div class="form-group">
                    <label for="notes">Additional Notes</label>
                    <textarea id="notes" name="notes" placeholder="Any special requests or details..."></textarea>
                </div>

                <!-- Hidden fields -->
                <input type="hidden" id="provider_id" name="provider_id" value="<?php echo $provider_id; ?>">
                <input type="hidden" id="product_id" name="product_id" value="<?php echo $product_id; ?>">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

                <button type="submit" class="submit-btn" id="submit_btn">Make Booking</button>
            </form>
        </div>
    </div>

    <?php require_once __DIR__ . '/../views/footer.php'; ?>

    <script src="<?php echo SITE_URL; ?>/js/sweetalert.js"></script>
    <script src="<?php echo SITE_URL; ?>/js/booking.js"></script>
</body>
</html>
