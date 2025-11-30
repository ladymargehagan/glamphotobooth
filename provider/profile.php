<?php
/**
 * Public Provider Profile Page
 * provider/profile.php
 */

require_once __DIR__ . '/../settings/core.php';

if (!class_exists('provider_class')) {
    require_once __DIR__ . '/../classes/provider_class.php';
}
if (!class_exists('product_class')) {
    require_once __DIR__ . '/../classes/product_class.php';
}
if (!class_exists('review_class')) {
    require_once __DIR__ . '/../classes/review_class.php';
}

$provider_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($provider_id <= 0) {
    error_log('Provider profile: Invalid or missing provider ID');
    header('Location: ' . SITE_URL . '/shop.php');
    exit;
}

$provider_class = new provider_class();
$product_class = new product_class();
$review_class = new review_class();

try {
    $provider = $provider_class->get_provider_full($provider_id);

    if (!$provider) {
        error_log('Provider profile: Provider not found for ID ' . $provider_id);
        header('Location: ' . SITE_URL . '/shop.php?error=provider_not_found');
        exit;
    }

    // Validate provider has required fields (business name should always exist)
    if (empty($provider['business_name']) || empty($provider['customer_id'])) {
        error_log('Provider profile: Provider has incomplete data for ID ' . $provider_id);
        header('Location: ' . SITE_URL . '/shop.php?error=invalid_provider');
        exit;
    }
} catch (Exception $e) {
    error_log('Provider profile error: ' . $e->getMessage());
    error_log('Stack trace: ' . $e->getTraceAsString());
    header('Location: ' . SITE_URL . '/shop.php');
    exit;
}

// Active products/services for this provider
$products = $product_class->get_products_by_provider($provider_id, true);
if ($products === false) {
    $products = [];
}

// Reviews for this provider
$reviews = $review_class->get_reviews_by_provider($provider_id);
if ($reviews === false) {
    $reviews = [];
}

$pageTitle = htmlspecialchars($provider['business_name']) . ' - Profile';
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

    <!-- Global variables and cart functionality -->
    <script>
        window.isLoggedIn = <?php echo isLoggedIn() ? 'true' : 'false'; ?>;
        window.loginUrl = '<?php echo SITE_URL; ?>/auth/login.php';
        window.siteUrl = '<?php echo SITE_URL; ?>';
    </script>
    <script src="<?php echo SITE_URL; ?>/js/cart.js"></script>

    <style>
        .profile-hero {
            max-width: 1200px;
            margin: 0 auto;
            padding: 3rem var(--spacing-xl) var(--spacing-xxl);
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: var(--spacing-xl);
        }
        .profile-main-title {
            font-family: var(--font-serif);
            font-size: 2.2rem;
            color: var(--primary);
            margin-bottom: var(--spacing-sm);
        }
        .profile-location {
            color: var(--text-secondary);
            font-size: 0.95rem;
            margin-bottom: var(--spacing-md);
        }
        .profile-rating {
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
            margin-bottom: var(--spacing-md);
            color: var(--primary);
            font-weight: 600;
        }
        .profile-description {
            color: var(--text-secondary);
            line-height: 1.6;
            font-size: 0.98rem;
        }
        .profile-sidebar {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: var(--spacing-lg);
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        }
        .profile-sidebar h3 {
            font-size: 1.1rem;
            color: var(--primary);
            margin-bottom: var(--spacing-sm);
        }
        .profile-sidebar-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: var(--spacing-sm);
            font-size: 0.9rem;
        }
        .profile-actions {
            margin-top: var(--spacing-lg);
            display: flex;
            flex-direction: column;
            gap: var(--spacing-sm);
        }
        .btn-primary-wide {
            display: block;
            width: 100%;
            text-align: center;
            padding: 0.8rem 1.2rem;
            border-radius: var(--border-radius);
            background: var(--primary);
            color: var(--white);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            transition: var(--transition);
        }
        .btn-primary-wide:hover {
            background: #0d1a3a;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16,33,82,0.2);
        }
        .products-section {
            max-width: 1200px;
            margin: 0 auto;
            padding: var(--spacing-xxl) var(--spacing-xl);
        }
        .products-section h2 {
            font-family: var(--font-serif);
            font-size: 1.6rem;
            color: var(--primary);
            margin-bottom: var(--spacing-lg);
        }
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: var(--spacing-lg);
        }
        .product-card {
            background: var(--white);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        .product-image {
            width: 100%;
            height: 200px;
            background: var(--light-gray);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            color: var(--text-secondary);
            font-size: 3rem;
        }
        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .product-content {
            padding: var(--spacing-md);
        }
        .product-title {
            font-weight: 600;
            color: var(--primary);
            margin-bottom: var(--spacing-xs);
        }
        .product-type {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: var(--text-secondary);
            margin-bottom: var(--spacing-sm);
        }
        .product-price {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: var(--spacing-sm);
        }
        .product-actions {
            margin-top: var(--spacing-sm);
        }
        .product-actions a {
            font-size: 0.85rem;
        }
        .reviews-section {
            max-width: 1200px;
            margin: 0 auto;
            padding: var(--spacing-xxl) var(--spacing-xl) var(--spacing-xxl);
            border-top: 1px solid rgba(226,196,146,0.3);
        }
        .reviews-section h2 {
            font-family: var(--font-serif);
            font-size: 1.5rem;
            color: var(--primary);
            margin-bottom: var(--spacing-lg);
        }
        .review-item {
            background: var(--white);
            padding: var(--spacing-md);
            border-radius: var(--border-radius);
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            margin-bottom: var(--spacing-md);
        }
        .review-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: var(--spacing-xs);
            font-size: 0.9rem;
        }
        .review-rating {
            color: #ffc107;
            font-size: 0.9rem;
        }
        .review-comment {
            color: var(--text-secondary);
            font-size: 0.9rem;
            line-height: 1.5;
        }
        @media (max-width: 768px) {
            .profile-hero {
                grid-template-columns: 1fr;
                padding: var(--spacing-lg);
            }
            .products-section,
            .reviews-section {
                padding: 0 var(--spacing-lg) var(--spacing-xxl);
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
                        <li><a href="<?php echo SITE_URL; ?>/shop.php" class="nav-link">Products & Services</a></li>
                    </ul>
                </nav>

                <div class="navbar-actions flex gap-sm">
                    <?php if (isLoggedIn() && isset($_SESSION['user_role']) && $_SESSION['user_role'] == 4): ?>
                        <a href="<?php echo SITE_URL; ?>/customer/cart.php" class="cart-icon" title="Shopping Cart" style="position: relative; display: flex; align-items: center;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 24px; height: 24px;">
                                <circle cx="9" cy="21" r="1"></circle>
                                <circle cx="20" cy="21" r="1"></circle>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                            </svg>
                            <span id="cartBadge" class="cart-badge" style="display: none;"></span>
                        </a>
                    <?php endif; ?>
                    <?php if (isLoggedIn()): ?>
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

    <section class="profile-hero">
        <div>
            <h1 class="profile-main-title"><?php echo htmlspecialchars($provider['business_name']); ?></h1>
            <div class="profile-location">
                <?php echo htmlspecialchars($provider['city'] ?? ''); ?>
                <?php if (!empty($provider['country'])): ?>
                    , <?php echo htmlspecialchars($provider['country']); ?>
                <?php endif; ?>
            </div>
            <div class="profile-rating">
                <?php if (!empty($provider['rating']) && $provider['rating'] > 0): ?>
                    ⭐ <?php echo number_format($provider['rating'], 1); ?>
                    <span style="color: var(--text-secondary); font-weight: 400;">
                        (<?php echo intval($provider['total_reviews']); ?> reviews)
                    </span>
                <?php else: ?>
                    ⭐ New provider
                <?php endif; ?>
            </div>
            <p class="profile-description">
                <?php echo nl2br(htmlspecialchars($provider['description'])); ?>
            </p>
        </div>

        <aside class="profile-sidebar">
            <h3>Business Details</h3>
            <div class="profile-sidebar-row">
                <span>Contact</span>
                <span><?php echo htmlspecialchars($provider['email'] ?? ''); ?></span>
            </div>
            <?php if (!empty($provider['hourly_rate'])): ?>
                <div class="profile-sidebar-row">
                    <span>Hourly Rate</span>
                    <span>₵<?php echo number_format($provider['hourly_rate'], 2); ?></span>
                </div>
            <?php endif; ?>
            <div class="profile-actions">
                <a href="<?php echo SITE_URL; ?>/customer/booking.php?provider_id=<?php echo intval($provider['provider_id']); ?>"
                   class="btn-primary-wide">
                    Book This Provider
                </a>
                <a href="<?php echo SITE_URL; ?>/shop.php?type=service" class="btn btn-sm btn-outline" style="text-align:center;">
                    Browse All Services
                </a>
            </div>
        </aside>
    </section>

    <section class="products-section">
        <h2>Services & Products</h2>
        <?php if ($products && count($products) > 0): ?>
            <div class="products-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <?php if (!empty($product['image'])): ?>
                                <img src="<?php echo SITE_URL . '/uploads/products/' . htmlspecialchars($product['image']); ?>"
                                     alt="<?php echo htmlspecialchars($product['title']); ?>">
                            <?php else: ?>
                                📸
                            <?php endif; ?>
                        </div>
                        <div class="product-content">
                            <div class="product-title"><?php echo htmlspecialchars($product['title']); ?></div>
                            <div class="product-type"><?php echo ucfirst(htmlspecialchars($product['product_type'])); ?></div>
                            <div class="product-price">₵<?php echo number_format($product['price'], 2); ?></div>
                            <div class="product-actions">
                                <?php if (isLoggedIn() && isset($_SESSION['user_role']) && $_SESSION['user_role'] == 4): ?>
                                    <button class="btn btn-sm btn-outline" onclick="addToCart(<?php echo intval($product['product_id']); ?>, '<?php echo htmlspecialchars(addslashes($product['title'])); ?>', <?php echo floatval($product['price']); ?>)">
                                        Add to Cart
                                    </button>
                                <?php elseif (!isLoggedIn()): ?>
                                    <a href="<?php echo SITE_URL; ?>/auth/login.php" class="btn btn-sm btn-outline" style="text-align: center; text-decoration: none;">
                                        Login to Buy
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p style="color: var(--text-secondary); font-style: italic;">
                This provider has not listed any services or products yet.
            </p>
        <?php endif; ?>
    </section>

    <section class="reviews-section">
        <h2>Client Reviews</h2>
        <?php if ($reviews && count($reviews) > 0): ?>
            <?php foreach ($reviews as $review): ?>
                <div class="review-item">
                    <div class="review-header">
                        <span><?php echo htmlspecialchars($review['customer_name'] ?? 'Client'); ?></span>
                        <span class="review-rating">
                            <?php echo str_repeat('⭐', intval($review['rating'])); ?>
                        </span>
                    </div>
                    <p class="review-comment">
                        <?php echo nl2br(htmlspecialchars($review['comment'] ?? '')); ?>
                    </p>
                </div>
            <?php endforeach; ?>
        <?php elseif (!empty($provider['total_reviews']) && intval($provider['total_reviews']) > 0): ?>
            <p style="color: var(--text-secondary); font-style: italic;">
                Reviews are loading... Please refresh the page.
            </p>
        <?php else: ?>
            <p style="color: var(--text-secondary); font-style: italic;">
                No reviews yet. Book a service to be the first to leave a review.
            </p>
        <?php endif; ?>
    </section>

    <?php require_once __DIR__ . '/../views/footer.php'; ?>
</body>
</html>


