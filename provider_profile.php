<?php
/**
 * Public Provider Profile Page
 * provider_profile.php
 */
require_once __DIR__ . '/settings/core.php';

$provider_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($provider_id <= 0) {
    header('Location: ' . SITE_URL . '/shop.php');
    exit;
}

// Get provider details
$provider_class = new provider_class();
$provider = $provider_class->get_provider_by_id($provider_id);

if (!$provider) {
    header('Location: ' . SITE_URL . '/shop.php');
    exit;
}

// Get provider's products
$product_class = new product_class();
$products = $product_class->get_products_by_provider($provider_id, true);

// Get provider's reviews and rating
$review_class = new review_class();
$reviews = $review_class->get_reviews_by_provider($provider_id);

$pageTitle = htmlspecialchars($provider['business_name']) . ' - PhotoMarket';
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
        .provider-hero {
            background: linear-gradient(135deg, rgba(16, 33, 82, 0.05) 0%, rgba(226, 196, 146, 0.1) 100%);
            padding: var(--spacing-xxl) var(--spacing-xl);
            margin-bottom: var(--spacing-xxl);
        }

        .provider-header {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 200px 1fr;
            gap: var(--spacing-xl);
            align-items: start;
        }

        .provider-avatar {
            width: 200px;
            height: 200px;
            background: var(--light-gray);
            border-radius: var(--border-radius);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
        }

        .provider-info h1 {
            color: var(--primary);
            font-family: var(--font-serif);
            font-size: 2.5rem;
            margin-bottom: var(--spacing-sm);
        }

        .provider-meta {
            display: flex;
            gap: var(--spacing-lg);
            margin-bottom: var(--spacing-lg);
            flex-wrap: wrap;
        }

        .provider-stat {
            display: flex;
            flex-direction: column;
        }

        .provider-stat-label {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .provider-stat-value {
            color: var(--primary);
            font-weight: 600;
            font-size: 1.25rem;
        }

        .provider-description {
            color: var(--text-secondary);
            line-height: 1.7;
            margin-bottom: var(--spacing-lg);
        }

        .rating {
            color: #ffc107;
            font-size: 1.1rem;
        }

        .section-title {
            color: var(--primary);
            font-family: var(--font-serif);
            font-size: 1.75rem;
            margin-bottom: var(--spacing-lg);
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
            padding: 0 var(--spacing-xl);
        }

        .products-grid {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 var(--spacing-xl);
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: var(--spacing-lg);
            margin-bottom: var(--spacing-xxl);
        }

        .product-card {
            background: var(--white);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            transition: var(--transition);
            text-decoration: none;
            display: flex;
            flex-direction: column;
        }

        .product-card:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            transform: translateY(-4px);
        }

        .product-image {
            width: 100%;
            height: 180px;
            background: var(--light-gray);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            overflow: hidden;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-content {
            padding: var(--spacing-lg);
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .product-title {
            color: var(--primary);
            font-weight: 600;
            margin-bottom: var(--spacing-sm);
            font-size: 1rem;
        }

        .product-type {
            color: var(--text-secondary);
            font-size: 0.85rem;
            margin-bottom: var(--spacing-md);
            text-transform: uppercase;
        }

        .product-price {
            color: var(--primary);
            font-weight: 700;
            font-size: 1.25rem;
            margin-top: auto;
            padding-top: var(--spacing-md);
            border-top: 1px solid var(--border-color);
        }

        .reviews-section {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 var(--spacing-xl) var(--spacing-xxl);
        }

        .review-item {
            background: var(--white);
            padding: var(--spacing-lg);
            border-radius: var(--border-radius);
            margin-bottom: var(--spacing-lg);
            border: 1px solid var(--border-color);
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: var(--spacing-md);
        }

        .reviewer-name {
            color: var(--primary);
            font-weight: 600;
        }

        .review-rating {
            color: #ffc107;
        }

        .review-comment {
            color: var(--text-secondary);
            line-height: 1.6;
        }

        .review-date {
            color: var(--text-secondary);
            font-size: 0.85rem;
            margin-top: var(--spacing-sm);
        }

        .empty-state {
            text-align: center;
            padding: var(--spacing-xxl);
            color: var(--text-secondary);
        }

        @media (max-width: 768px) {
            .provider-header {
                grid-template-columns: 1fr;
            }

            .provider-avatar {
                width: 150px;
                height: 150px;
                margin: 0 auto;
            }

            .provider-info h1 {
                font-size: 1.75rem;
            }

            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            }
        }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/views/header.php'; ?>

    <!-- Provider Hero Section -->
    <div class="provider-hero">
        <div class="provider-header">
            <div class="provider-avatar">📸</div>
            <div class="provider-info">
                <h1><?php echo htmlspecialchars($provider['business_name']); ?></h1>

                <div class="provider-meta">
                    <div class="provider-stat">
                        <span class="provider-stat-label">Rating</span>
                        <span class="provider-stat-value rating">
                            <?php if ($provider['rating'] > 0): ?>
                                ⭐ <?php echo number_format($provider['rating'], 1); ?>
                            <?php else: ?>
                                New Provider
                            <?php endif; ?>
                        </span>
                    </div>
                    <div class="provider-stat">
                        <span class="provider-stat-label">Reviews</span>
                        <span class="provider-stat-value"><?php echo intval($provider['total_reviews'] ?? 0); ?></span>
                    </div>
                    <div class="provider-stat">
                        <span class="provider-stat-label">Services</span>
                        <span class="provider-stat-value"><?php echo count($products ?? []); ?></span>
                    </div>
                </div>

                <?php if (!empty($provider['description'])): ?>
                    <p class="provider-description">
                        <?php echo htmlspecialchars($provider['description']); ?>
                    </p>
                <?php endif; ?>

                <a href="<?php echo SITE_URL; ?>/customer/booking.php?provider_id=<?php echo $provider_id; ?>" class="btn btn-primary">
                    Book a Service
                </a>
            </div>
        </div>
    </div>

    <!-- Services Section -->
    <?php if ($products && count($products) > 0): ?>
        <h2 class="section-title">Services & Products</h2>
        <div class="products-grid">
            <?php foreach ($products as $product): ?>
                <a href="<?php echo SITE_URL; ?>/product_details.php?id=<?php echo $product['product_id']; ?>" class="product-card">
                    <div class="product-image">
                        <?php if ($product['image']): ?>
                            <img src="<?php echo SITE_URL . '/uploads/products/' . htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>">
                        <?php else: ?>
                            📸
                        <?php endif; ?>
                    </div>
                    <div class="product-content">
                        <div class="product-title"><?php echo htmlspecialchars($product['title']); ?></div>
                        <div class="product-type"><?php echo ucfirst(htmlspecialchars($product['product_type'])); ?></div>
                        <div class="product-price">₵<?php echo number_format($product['price'], 2); ?></div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Reviews Section -->
    <?php if ($reviews && count($reviews) > 0): ?>
        <h2 class="section-title">Customer Reviews</h2>
        <div class="reviews-section">
            <?php foreach ($reviews as $review): ?>
                <div class="review-item">
                    <div class="review-header">
                        <div>
                            <div class="reviewer-name"><?php echo htmlspecialchars($review['customer_name'] ?? 'Anonymous'); ?></div>
                            <div class="review-date"><?php echo date('M d, Y', strtotime($review['review_date'])); ?></div>
                        </div>
                        <div class="review-rating">
                            <?php echo str_repeat('⭐', intval($review['rating'])); ?>
                        </div>
                    </div>
                    <p class="review-comment"><?php echo htmlspecialchars($review['comment']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="reviews-section">
            <div class="empty-state">
                <p>No reviews yet. Be the first to review this service!</p>
            </div>
        </div>
    <?php endif; ?>

    <?php require_once __DIR__ . '/views/footer.php'; ?>
</body>
</html>
