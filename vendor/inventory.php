<?php
/**
 * Vendor Inventory Page
 * vendor/inventory.php
 */
require_once __DIR__ . '/../settings/core.php';

// Check if logged in
requireLogin();

// Check if user is vendor (role 3)
if ($_SESSION['user_role'] != 3) {
    header('Location: ' . SITE_URL . '/index.php');
    exit;
}

// Check if provider profile exists
$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$provider_class = new provider_class();
$provider = $provider_class->get_provider_by_customer($user_id);

if (!$provider) {
    header('Location: ' . SITE_URL . '/customer/profile_setup.php');
    exit;
}

// Get all products for this vendor
$product_class = new product_class();
$products = $product_class->get_products_by_provider($provider['provider_id']);
if (!$products) {
    $products = [];
}

// Get category names for display
$category_class = new category_class();
$categories = $category_class->get_all_categories();
$categoryMap = [];
if ($categories) {
    foreach ($categories as $cat) {
        $categoryMap[$cat['cat_id']] = $cat['cat_name'];
    }
}

$pageTitle = 'Inventory - PhotoMarket';
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
        .inventory-grid {
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

        .product-status {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-bottom: var(--spacing-md);
        }

        .status-active {
            background: rgba(76, 175, 80, 0.15);
            color: #2e7d32;
        }

        .status-inactive {
            background: rgba(158, 158, 158, 0.15);
            color: #616161;
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

        .btn-primary {
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

        .btn-primary:hover {
            background: #0d1a3a;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="dashboard-layout">
        <!-- Sidebar -->
        <?php require_once __DIR__ . '/../views/dashboard_sidebar.php'; ?>

        <!-- Main Content -->
        <main class="dashboard-content">
            <div class="dashboard-header">
                <div>
                    <h1 class="dashboard-title">Inventory</h1>
                    <p class="dashboard-subtitle">View and manage your product inventory</p>
                </div>
                <div class="dashboard-actions">
                    <a href="<?php echo SITE_URL; ?>/customer/add_product.php" class="btn btn-primary">Add Product</a>
                </div>
            </div>

            <div class="dashboard-card">
                <?php if ($products && count($products) > 0): ?>
                    <div class="inventory-grid">
                        <?php foreach ($products as $product): ?>
                            <div class="product-card">
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
                                        <?php if (isset($categoryMap[$product['cat_id']])): ?>
                                            <span class="product-category"><?php echo htmlspecialchars($categoryMap[$product['cat_id']]); ?></span>
                                        <?php endif; ?>
                                        <span class="product-type"><?php echo ucfirst(htmlspecialchars($product['product_type'])); ?></span>
                                    </div>
                                    <div class="product-description"><?php echo htmlspecialchars(substr($product['description'] ?? '', 0, 100)); ?><?php echo strlen($product['description'] ?? '') > 100 ? '...' : ''; ?></div>
                                    <div class="product-price">₵<?php echo number_format($product['price'], 2); ?></div>
                                    <div>
                                        <span class="product-status status-<?php echo $product['is_active'] ? 'active' : 'inactive'; ?>">
                                            <?php echo $product['is_active'] ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </div>
                                    <div class="product-actions">
                                        <a href="<?php echo SITE_URL; ?>/customer/edit_product.php?id=<?php echo htmlspecialchars($product['product_id']); ?>" class="product-action-btn btn-edit">Edit</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <svg class="empty-state-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <circle cx="9" cy="21" r="1"></circle>
                            <circle cx="20" cy="21" r="1"></circle>
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                        </svg>
                        <h3 class="empty-state-title">No Products in Inventory</h3>
                        <p class="empty-state-text">You don't have any products in your inventory yet. Add your first product to get started.</p>
                        <a href="<?php echo SITE_URL; ?>/customer/add_product.php" class="btn-primary">Add Your First Product</a>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>

