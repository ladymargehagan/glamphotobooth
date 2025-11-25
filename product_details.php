<?php
/**
 * Product Details Page
 * product_details.php
 */
require_once __DIR__ . '/settings/core.php';

// Get product ID from query parameter
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($product_id <= 0) {
    header('Location: ' . SITE_URL . '/shop.php');
    exit;
}

// Fetch product details via action
$product = null;
$provider = null;
$category = null;

// Direct fetch instead of using action
$product_class = new product_class();
$product = $product_class->get_product_by_id($product_id);

if (!$product) {
    header('Location: ' . SITE_URL . '/shop.php');
    exit;
}

$provider_class = new provider_class();
$provider = $provider_class->get_provider_by_id($product['provider_id']);

$category_class = new category_class();
$category = $category_class->get_category_by_id($product['cat_id']);

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
    <style>
        .product-hero {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--spacing-xl);
            max-width: 1200px;
            margin: 0 auto;
            padding: var(--spacing-xxl) var(--spacing-xl);
            align-items: center;
        }

        .product-hero-image {
            width: 100%;
            height: 500px;
            background: var(--light-gray);
            border-radius: var(--border-radius);
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 5rem;
            color: var(--text-secondary);
        }

        .product-hero-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-info {
            display: flex;
            flex-direction: column;
        }

        .product-breadcrumb {
            font-size: 0.9rem;
            color: var(--text-secondary);
            margin-bottom: var(--spacing-md);
        }

        .product-breadcrumb a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }

        .product-breadcrumb a:hover {
            text-decoration: underline;
        }

        .product-category-badge {
            display: inline-block;
            background: rgba(226, 196, 146, 0.15);
            color: var(--primary);
            padding: 0.3rem 0.8rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: var(--spacing-md);
            width: fit-content;
        }

        .product-title {
            font-family: var(--font-serif);
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: var(--spacing-md);
            line-height: 1.2;
        }

        .product-type {
            display: inline-block;
            background: rgba(16, 33, 82, 0.08);
            color: var(--primary);
            padding: 0.3rem 0.8rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: var(--spacing-lg);
            width: fit-content;
        }

        .product-price {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: var(--spacing-md);
        }

        .product-description {
            color: var(--text-secondary);
            font-size: 1rem;
            line-height: 1.6;
            margin-bottom: var(--spacing-lg);
        }

        .provider-section {
            background: rgba(226, 196, 146, 0.05);
            padding: var(--spacing-lg);
            border-radius: var(--border-radius);
            margin-bottom: var(--spacing-lg);
            border: 1px solid rgba(226, 196, 146, 0.2);
        }

        .provider-title {
            font-weight: 600;
            color: var(--primary);
            margin-bottom: var(--spacing-sm);
            font-size: 0.9rem;
        }

        .provider-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: var(--spacing-xs);
        }

        .provider-business {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-bottom: var(--spacing-md);
        }

        .provider-rating {
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
            color: var(--primary);
            font-weight: 600;
        }

        .action-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--spacing-md);
        }

        .btn-add-to-cart {
            padding: 1rem 1.5rem;
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.95rem;
        }

        .btn-add-to-cart:hover {
            background: #0d1a3a;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 33, 82, 0.2);
        }

        .btn-contact {
            padding: 1rem 1.5rem;
            background: var(--light-gray);
            color: var(--text-primary);
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.95rem;
        }

        .btn-contact:hover {
            background: #e8e6e1;
        }

        .related-products {
            max-width: 1200px;
            margin: 0 auto;
            padding: var(--spacing-xxl) var(--spacing-xl);
        }

        .related-title {
            font-family: var(--font-serif);
            font-size: 1.75rem;
            color: var(--primary);
            margin-bottom: var(--spacing-lg);
        }

        .related-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: var(--spacing-lg);
        }

        @media (max-width: 768px) {
            .product-hero {
                grid-template-columns: 1fr;
                padding: var(--spacing-lg);
            }

            .product-hero-image {
                height: 300px;
                font-size: 3rem;
            }

            .product-title {
                font-size: 1.5rem;
            }

            .product-price {
                font-size: 2rem;
            }

            .action-buttons {
                grid-template-columns: 1fr;
            }

            .related-products {
                padding: var(--spacing-lg);
            }

            .related-title {
                font-size: 1.35rem;
            }
        }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/views/header.php'; ?>

    <!-- Product Hero -->
    <section class="product-hero">
        <div class="product-hero-image">
            <?php if ($product['image']): ?>
                <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>">
            <?php else: ?>
                📸
            <?php endif; ?>
        </div>

        <div class="product-info">
            <div class="product-breadcrumb">
                <a href="<?php echo SITE_URL; ?>/shop.php">Shop</a> /
                <?php if ($category): ?>
                    <a href="<?php echo SITE_URL; ?>/shop.php?category=<?php echo $category['cat_id']; ?>"><?php echo htmlspecialchars($category['cat_name']); ?></a>
                <?php endif; ?>
            </div>

            <?php if ($category): ?>
                <span class="product-category-badge"><?php echo htmlspecialchars($category['cat_name']); ?></span>
            <?php endif; ?>

            <h1 class="product-title"><?php echo htmlspecialchars($product['title']); ?></h1>
            <span class="product-type"><?php echo ucfirst(htmlspecialchars($product['product_type'])); ?></span>

            <div class="product-price">₵<?php echo number_format($product['price'], 2); ?></div>

            <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>

            <!-- Provider Info -->
            <?php if ($provider): ?>
                <div class="provider-section">
                    <div class="provider-title">Offered by</div>
                    <div class="provider-name"><?php echo htmlspecialchars($provider['business_name']); ?></div>
                    <div class="provider-business">Photography Services</div>
                    <?php if ($provider['rating'] > 0): ?>
                        <div class="provider-rating">
                            ⭐ <?php echo number_format($provider['rating'], 1); ?> (<?php echo intval($provider['total_reviews']); ?> reviews)
                        </div>
                    <?php else: ?>
                        <div class="provider-rating">
                            ⭐ New Provider
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <button class="btn-add-to-cart">Add to Cart</button>
                <button class="btn-contact">Contact Provider</button>
            </div>
        </div>
    </section>

    <?php require_once __DIR__ . '/views/footer.php'; ?>

    <script>
        document.querySelector('.btn-add-to-cart').addEventListener('click', function() {
            if (<?php echo isLoggedIn() ? 'true' : 'false'; ?>) {
                alert('Cart functionality coming soon!');
            } else {
                window.location.href = '<?php echo SITE_URL; ?>/auth/login.php';
            }
        });

        document.querySelector('.btn-contact').addEventListener('click', function() {
            alert('Contact functionality coming soon!');
        });
    </script>
</body>
</html>
