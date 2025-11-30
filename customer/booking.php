<?php
/**
 * Booking Page
 * customer/booking.php
 */
require_once __DIR__ . '/../settings/core.php';

requireLogin();

// Check if user is customer (role 4)
if ($_SESSION['user_role'] != 4) {
    header('Location: ' . SITE_URL . '/index.php');
    exit;
}

$provider_id = isset($_GET['provider_id']) ? intval($_GET['provider_id']) : 0;
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

if (!$provider_id || !$product_id) {
    header('Location: ' . SITE_URL . '/shop.php');
    exit;
}

// Get provider and product details
$provider_class = new provider_class();
$provider = $provider_class->get_provider_by_id($provider_id);

if (!$provider) {
    header('Location: ' . SITE_URL . '/shop.php');
    exit;
}

$product_class = new product_class();
$product = $product_class->get_product_by_id($product_id);

if (!$product || $product['product_type'] !== 'service') {
    header('Location: ' . SITE_URL . '/shop.php');
    exit;
}

$pageTitle = 'Book Service - GlamPhotobooth Accra';
$cssPath = SITE_URL . '/css/style.css';
$dashboardCss = SITE_URL . '/css/dashboard.css';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($cssPath); ?>">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($dashboardCss); ?>">
    <!-- SweetAlert2 Library -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        window.siteUrl = '<?php echo SITE_URL; ?>';
    </script>
    <style>
        .booking-container {
            max-width: 700px;
            margin: 0 auto;
            padding: var(--spacing-xxl) var(--spacing-xl);
        }

        .booking-header {
            text-align: center;
            margin-bottom: var(--spacing-xxl);
        }

        .booking-header h1 {
            color: var(--primary);
            font-family: var(--font-serif);
            font-size: 2rem;
            margin-bottom: var(--spacing-sm);
        }

        .booking-header p {
            color: var(--text-secondary);
            font-size: 1rem;
        }

        .service-info {
            background: rgba(226, 196, 146, 0.05);
            padding: var(--spacing-lg);
            border-radius: var(--border-radius);
            margin-bottom: var(--spacing-xxl);
        }

        .service-info h3 {
            color: var(--primary);
            margin: 0 0 var(--spacing-sm) 0;
        }

        .service-info .provider-name {
            color: var(--text-secondary);
            margin: 0 0 var(--spacing-md) 0;
        }

        .service-info .price {
            color: var(--primary);
            font-size: 1.5rem;
            font-weight: 600;
        }

        .booking-card {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: var(--spacing-xl);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        }

        .form-group {
            margin-bottom: var(--spacing-lg);
        }

        .form-group label {
            display: block;
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: var(--spacing-xs);
            color: var(--text-primary);
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            font-size: 0.95rem;
            font-family: var(--font-sans);
            transition: var(--transition);
            background: var(--white);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(16, 33, 82, 0.1);
        }

        .form-hint {
            display: block;
            color: var(--text-secondary);
            font-size: 0.85rem;
            margin-top: 4px;
        }

        .form-actions {
            display: flex;
            gap: var(--spacing-md);
            margin-top: var(--spacing-xl);
        }

        .btn-submit {
            flex: 1;
            padding: 0.875rem 1.5rem;
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.95rem;
        }

        .btn-submit:hover {
            background: #0d1a3a;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 33, 82, 0.2);
        }

        .btn-submit:disabled {
            background: var(--text-secondary);
            cursor: not-allowed;
            transform: none;
        }

        .btn-cancel {
            flex: 1;
            padding: 0.875rem 1.5rem;
            background: var(--light-gray);
            color: var(--text-primary);
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-align: center;
            text-decoration: none;
            font-size: 0.95rem;
        }

        .btn-cancel:hover {
            background: #e8e6e1;
        }
    </style>
</head>
<body>
    <div class="dashboard-layout">
        <?php require_once __DIR__ . '/../views/dashboard_sidebar.php'; ?>

        <main class="dashboard-content">
            <div class="booking-container">
                <div class="booking-header">
                    <h1>Book Photography Service</h1>
                    <p>Fill in the details to book your photography session</p>
                </div>

                <div class="service-info">
                    <h3><?php echo htmlspecialchars($product['title']); ?></h3>
                    <p class="provider-name">By <?php echo htmlspecialchars($provider['business_name']); ?></p>
                    <p class="price">₵<?php echo number_format($product['price'], 2); ?></p>
                </div>

                <div class="booking-card">
                    <form id="booking_form">
                        <input type="hidden" id="provider_id" name="provider_id" value="<?php echo $provider_id; ?>">
                        <input type="hidden" id="product_id" name="product_id" value="<?php echo $product_id; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

                        <div class="form-group">
                            <label for="booking_date">Booking Date</label>
                            <input type="date" id="booking_date" name="booking_date" required>
                            <span class="form-hint">Select your preferred date</span>
                        </div>

                        <div class="form-group">
                            <label for="booking_time">Booking Time</label>
                            <select id="booking_time" name="booking_time" required>
                                <option value="">Select a date first</option>
                            </select>
                            <span class="form-hint">Available time slots will appear after selecting a date</span>
                        </div>

                        <div class="form-group">
                            <label for="service_description">Service Details</label>
                            <textarea id="service_description" name="service_description" placeholder="Describe what you need for this photography session..." required></textarea>
                            <span class="form-hint">Provide details about your requirements, location, number of people, etc.</span>
                        </div>

                        <div class="form-group">
                            <label for="notes">Additional Notes (Optional)</label>
                            <textarea id="notes" name="notes" placeholder="Any special requests or additional information..."></textarea>
                        </div>

                        <div class="form-actions">
                            <a href="<?php echo SITE_URL; ?>/shop.php" class="btn-cancel">Cancel</a>
                            <button type="submit" class="btn-submit" id="submit_btn">Book Now</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script src="<?php echo SITE_URL; ?>/js/sweetalert.js"></script>
    <script src="<?php echo SITE_URL; ?>/js/booking.js"></script>
</body>
</html>
