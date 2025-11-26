<?php
/**
 * Manage Products
 * customer/manage_products.php
 */
require_once __DIR__ . '/../settings/core.php';

requireLogin();
$user_role = isset($_SESSION['user_role']) ? intval($_SESSION['user_role']) : 4;
if ($user_role == 4) {
    header('Location: ' . SITE_URL . '/customer/dashboard.php');
    exit;
}

$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$provider_class = new provider_class();
$provider = $provider_class->get_provider_by_customer($user_id);

if (!$provider) {
    header('Location: ' . SITE_URL . '/customer/profile_setup.php');
    exit;
}

// Get all products for this provider
$product_class = new product_class();
$products = $product_class->get_products_by_provider($provider['provider_id']);

$pageTitle = 'Manage Products - PhotoMarket';
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
    <style>
        .products-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: var(--spacing-xxl) var(--spacing-xl);
        }

        .products-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: var(--spacing-xl);
            flex-wrap: wrap;
            gap: var(--spacing-lg);
        }

        .products-header h1 {
            color: var(--primary);
            font-family: var(--font-serif);
            font-size: 2rem;
            margin: 0;
        }

        .btn-add-product {
            padding: 0.875rem 1.5rem;
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

        .btn-add-product:hover {
            background: #0d1a3a;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 33, 82, 0.2);
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: var(--spacing-lg);
        }

        .product-card {
            background: var(--white);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
            transition: var(--transition);
        }

        .product-card:hover {
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
            transform: translateY(-4px);
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
            padding: var(--spacing-lg);
        }

        .product-title {
            font-weight: 600;
            color: var(--primary);
            margin-bottom: var(--spacing-xs);
            font-size: 1.1rem;
        }

        .product-category {
            display: inline-block;
            background: rgba(226, 196, 146, 0.15);
            color: var(--primary);
            padding: 0.25rem 0.75rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-bottom: var(--spacing-sm);
        }

        .product-type {
            display: inline-block;
            background: rgba(16, 33, 82, 0.08);
            color: var(--primary);
            padding: 0.25rem 0.75rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-left: var(--spacing-xs);
            margin-bottom: var(--spacing-sm);
        }

        .product-description {
            color: var(--text-secondary);
            font-size: 0.9rem;
            line-height: 1.4;
            margin-bottom: var(--spacing-md);
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-price {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: var(--spacing-md);
        }

        .product-actions {
            display: flex;
            gap: var(--spacing-sm);
        }

        .product-action-btn {
            flex: 1;
            padding: 0.5rem;
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.85rem;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-edit {
            background: rgba(16, 33, 82, 0.1);
            color: var(--primary);
        }

        .btn-edit:hover {
            background: rgba(16, 33, 82, 0.2);
        }

        .btn-delete {
            background: rgba(211, 47, 47, 0.1);
            color: #d32f2f;
        }

        .btn-delete:hover {
            background: rgba(211, 47, 47, 0.2);
        }

        .empty-state {
            grid-column: 1 / -1;
            text-align: center;
            padding: var(--spacing-xxl) var(--spacing-xl);
        }

        .empty-state-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto var(--spacing-lg);
            color: var(--text-secondary);
            stroke-width: 1.5;
        }

        .empty-state-title {
            color: var(--primary);
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: var(--spacing-sm);
        }

        .empty-state-text {
            color: var(--text-secondary);
            margin-bottom: var(--spacing-lg);
        }

        .message {
            margin-bottom: var(--spacing-lg);
            padding: var(--spacing-md);
            border-radius: var(--border-radius);
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
            .products-container {
                padding: var(--spacing-lg);
            }

            .products-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .products-header h1 {
                font-size: 1.5rem;
            }

            .products-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="products-container">
        <div class="products-header">
            <h1>Manage Products</h1>
            <div style="display: flex; gap: var(--spacing-md);">
                <a href="<?php echo SITE_URL; ?>/<?php echo $user_role === 2 ? 'photographer' : 'vendor'; ?>/dashboard.php" class="btn-add-product" style="background: rgba(16, 33, 82, 0.1); color: var(--primary);">← Back to Dashboard</a>
                <a href="<?php echo SITE_URL; ?>/customer/add_product.php" class="btn-add-product">+ Add New Product</a>
            </div>
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

        <!-- Products Grid -->
        <div class="products-grid" id="productsGrid">
            <?php if (!$products || count($products) === 0): ?>
                <div class="empty-state">
                    <svg class="empty-state-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <rect x="3" y="3" width="18" height="18" rx="2"></rect>
                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                        <polyline points="21 15 16 10 5 21"></polyline>
                    </svg>
                    <h3 class="empty-state-title">No Products Yet</h3>
                    <p class="empty-state-text">Start building your catalog by adding your first product or service</p>
                    <a href="<?php echo SITE_URL; ?>/customer/add_product.php" class="btn-add-product">Create Your First Product</a>
                </div>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card" data-product-id="<?php echo htmlspecialchars($product['product_id']); ?>">
                        <div class="product-image">
                            <?php if ($product['image']): ?>
                                <img src="<?php echo SITE_URL . '/uploads/products/' . htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>">
                            <?php else: ?>
                                📸
                            <?php endif; ?>
                        </div>
                        <div class="product-content">
                            <div class="product-title"><?php echo htmlspecialchars($product['title']); ?></div>
                            <div style="margin-bottom: var(--spacing-sm);">
                                <span class="product-category">Category</span>
                                <span class="product-type"><?php echo ucfirst(htmlspecialchars($product['product_type'])); ?></span>
                            </div>
                            <div class="product-description"><?php echo htmlspecialchars(substr($product['description'], 0, 100)); ?>...</div>
                            <div class="product-price">₵<?php echo number_format($product['price'], 2); ?></div>
                            <div class="product-actions">
                                <a href="<?php echo SITE_URL; ?>/customer/edit_product.php?id=<?php echo htmlspecialchars($product['product_id']); ?>" class="product-action-btn btn-edit">Edit</a>
                                <button type="button" class="product-action-btn btn-delete" onclick="deleteProduct(<?php echo htmlspecialchars($product['product_id']); ?>)">Delete</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="<?php echo SITE_URL; ?>/js/product.js"></script>
</body>
</html>
