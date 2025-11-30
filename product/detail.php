<?php
/**
 * Product Detail Page
 * product/detail.php
 */
require_once __DIR__ . '/../settings/core.php';

if (!class_exists('product_class')) {
    require_once __DIR__ . '/../classes/product_class.php';
}
if (!class_exists('provider_class')) {
    require_once __DIR__ . '/../classes/provider_class.php';
}

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($product_id <= 0) {
    header('Location: ' . SITE_URL . '/shop.php');
    exit;
}

$product_class = new product_class();
$product = $product_class->get_product_by_id($product_id);

if (!$product) {
    header('Location: ' . SITE_URL . '/shop.php');
    exit;
}

// Get provider info
$provider_class = new provider_class();
$provider = $provider_class->get_provider_by_id($product['provider_id']);

$pageTitle = htmlspecialchars($product['title']) . ' - PhotoMarket';
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

    <script>
        window.isLoggedIn = <?php echo isLoggedIn() ? 'true' : 'false'; ?>;
        window.loginUrl = '<?php echo SITE_URL; ?>/auth/login.php';
        window.siteUrl = '<?php echo SITE_URL; ?>';
    </script>
    <script src="<?php echo SITE_URL; ?>/js/cart.js"></script>

    <style>
        .product-detail-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: var(--spacing-xxl) var(--spacing-xl);
        }

        .product-detail-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--spacing-xxl);
            margin-bottom: var(--spacing-xxl);
        }

        .product-image-section {
            background: var(--light-gray);
            border-radius: var(--border-radius);
            padding: var(--spacing-xl);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 400px;
        }

        .product-image-section img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: var(--border-radius);
        }

        .product-image-placeholder {
            font-size: 4rem;
            color: var(--text-secondary);
        }

        .product-info-section {
            display: flex;
            flex-direction: column;
        }

        .product-category-badge {
            display: inline-block;
            background: rgba(226, 196, 146, 0.15);
            color: var(--primary);
            padding: 0.4rem 0.8rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 600;
            width: fit-content;
            margin-bottom: var(--spacing-md);
        }

        .product-type-badge {
            display: inline-block;
            background: rgba(100, 150, 200, 0.15);
            color: #1976d2;
            padding: 0.4rem 0.8rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 600;
            width: fit-content;
            text-transform: capitalize;
            margin-left: var(--spacing-sm);
        }

        .product-title {
            font-family: var(--font-serif);
            font-size: 2rem;
            color: var(--primary);
            margin: var(--spacing-md) 0;
            line-height: 1.2;
        }

        .product-price {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: var(--spacing-lg);
        }

        .product-description {
            color: var(--text-secondary);
            line-height: 1.8;
            margin-bottom: var(--spacing-lg);
            font-size: 1rem;
        }

        .product-provider {
            background: rgba(226, 196, 146, 0.05);
            padding: var(--spacing-lg);
            border-radius: var(--border-radius);
            margin-bottom: var(--spacing-lg);
            border-left: 4px solid var(--primary);
        }

        .provider-name {
            color: var(--primary);
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: var(--spacing-sm);
        }

        .provider-rating {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .product-actions {
            display: flex;
            gap: var(--spacing-md);
            margin-top: auto;
        }

        .btn-action {
            flex: 1;
            padding: 1rem;
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-primary-action {
            background: var(--primary);
            color: var(--white);
        }

        .btn-primary-action:hover {
            background: #0d1a3a;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 33, 82, 0.2);
        }

        .btn-secondary-action {
            background: var(--white);
            color: var(--primary);
            border: 2px solid var(--primary);
        }

        .btn-secondary-action:hover {
            background: rgba(226, 196, 146, 0.1);
        }

        @media (max-width: 768px) {
            .product-detail-layout {
                grid-template-columns: 1fr;
                gap: var(--spacing-lg);
            }

            .product-title {
                font-size: 1.5rem;
            }

            .product-price {
                font-size: 1.5rem;
            }

            .product-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../views/header.php'; ?>

    <div class="product-detail-container">
        <div class="product-detail-layout">
            <!-- Product Image -->
            <div class="product-image-section">
                <?php if (!empty($product['image'])): ?>
                    <img src="<?php echo SITE_URL . '/uploads/products/' . htmlspecialchars($product['image']); ?>"
                         alt="<?php echo htmlspecialchars($product['title']); ?>">
                <?php else: ?>
                    <div class="product-image-placeholder">📸</div>
                <?php endif; ?>
            </div>

            <!-- Product Info -->
            <div class="product-info-section">
                <div style="display: flex; gap: var(--spacing-sm); margin-bottom: var(--spacing-md);">
                    <span class="product-category-badge">Category</span>
                    <span class="product-type-badge"><?php echo ucfirst(htmlspecialchars($product['product_type'])); ?></span>
                </div>

                <h1 class="product-title"><?php echo htmlspecialchars($product['title']); ?></h1>

                <div class="product-price">₵<?php echo number_format($product['price'], 2); ?></div>

                <p class="product-description">
                    <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                </p>

                <?php if ($provider): ?>
                    <div class="product-provider">
                        <div class="provider-name"><?php echo htmlspecialchars($provider['business_name']); ?></div>
                        <div class="provider-rating">
                            <?php if ($provider['rating'] > 0): ?>
                                ⭐ <?php echo number_format($provider['rating'], 1); ?>
                                (<?php echo intval($provider['total_reviews']); ?> reviews)
                            <?php else: ?>
                                New provider
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="product-actions">
                    <?php if ($product['product_type'] === 'service'): ?>
                        <a href="<?php echo SITE_URL; ?>/customer/booking.php?provider_id=<?php echo $provider['provider_id']; ?>"
                           class="btn-action btn-primary-action">
                            Book Now
                        </a>
                    <?php else: ?>
                        <button class="btn-action btn-primary-action"
                                onclick="addToCart(<?php echo intval($product['product_id']); ?>, '<?php echo htmlspecialchars(addslashes($product['title'])); ?>', <?php echo floatval($product['price']); ?>)">
                            Add to Cart
                        </button>
                        <a href="<?php echo SITE_URL; ?>/customer/cart.php" class="btn-action btn-secondary-action">
                            View Cart
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php require_once __DIR__ . '/../views/footer.php'; ?>
</body>
</html>
