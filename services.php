<?php
/**
 * Services Page - Photography Services Only
 * services.php
 */
require_once __DIR__ . '/settings/core.php';

// Check if user is photographer or vendor - they should use their own dashboards
if (isLoggedIn() && ($_SESSION['user_role'] == 2 || $_SESSION['user_role'] == 3)) {
    header('Location: ' . SITE_URL . '/index.php');
    exit;
}

// Ensure category class is loaded
if (!class_exists('category_class')) {
    require_once __DIR__ . '/classes/category_class.php';
}

$pageTitle = 'Photography Services - GlamPhotobooth Accra';
$cssPath = SITE_URL . '/css/style.css';

// Get all categories for filter
$category_class = new category_class();
$categories = $category_class->get_all_categories();
if ($categories === false) {
    $categories = [];
}
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
        .shop-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: var(--spacing-xxl) var(--spacing-xl);
        }

        .shop-header {
            text-align: center;
            margin-bottom: var(--spacing-xxl);
        }

        .shop-header h1 {
            color: var(--primary);
            font-family: var(--font-serif);
            font-size: 2.5rem;
            margin-bottom: var(--spacing-sm);
        }

        .shop-header p {
            color: var(--text-secondary);
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }

        .shop-layout {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: var(--spacing-xl);
        }

        .shop-sidebar {
            background: var(--white);
            padding: var(--spacing-lg);
            border-radius: var(--border-radius);
            height: fit-content;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .filter-group {
            margin-bottom: var(--spacing-lg);
            padding-bottom: var(--spacing-lg);
            border-bottom: 1px solid var(--border-color);
        }

        .filter-group:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .filter-title {
            font-weight: 600;
            color: var(--primary);
            margin-bottom: var(--spacing-sm);
            font-size: 0.95rem;
        }

        .filter-option {
            display: flex;
            align-items: center;
            margin-bottom: var(--spacing-sm);
            cursor: pointer;
        }

        .filter-option input[type="radio"],
        .filter-option input[type="checkbox"] {
            margin-right: var(--spacing-sm);
            cursor: pointer;
        }

        .filter-option label {
            cursor: pointer;
            color: var(--text-primary);
            font-size: 0.9rem;
            margin: 0;
        }

        .filter-option input:checked + label {
            color: var(--primary);
            font-weight: 600;
        }

        .shop-main {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: var(--spacing-lg);
        }

        .product-card {
            background: var(--white);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            color: inherit;
        }

        .product-card:hover {
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.12);
        }

        .product-card-link {
            flex: 1;
            display: flex;
            flex-direction: column;
            text-decoration: none;
            color: inherit;
            cursor: pointer;
            transition: var(--transition);
        }

        .product-card-link:hover {
            transform: translateY(-4px);
        }

        .product-card-btn-add-to-cart {
            padding: 0.75rem 1rem;
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: 0;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.9rem;
        }

        .product-card-btn-add-to-cart:hover {
            background: #0d1a3a;
        }

        .product-card-btn-add-to-cart:active {
            transform: scale(0.98);
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

        .product-info {
            padding: var(--spacing-md);
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .product-category {
            display: inline-block;
            background: rgba(226, 196, 146, 0.15);
            color: var(--primary);
            padding: 0.2rem 0.6rem;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 600;
            margin-bottom: var(--spacing-xs);
            width: fit-content;
        }

        .product-title {
            font-weight: 600;
            color: var(--primary);
            margin-bottom: var(--spacing-xs);
            font-size: 1rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-type {
            font-size: 0.75rem;
            color: var(--text-secondary);
            margin-bottom: var(--spacing-sm);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .product-description {
            font-size: 0.85rem;
            color: var(--text-secondary);
            flex: 1;
            margin-bottom: var(--spacing-md);
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.4;
        }

        .product-price {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary);
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
        }

        /* Provider Profile Modal */
        .provider-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            overflow-y: auto;
        }

        .provider-modal.active {
            display: flex;
        }

        .provider-modal-content {
            background: var(--white);
            border-radius: var(--border-radius);
            width: 90%;
            max-width: 700px;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
        }

        .provider-modal-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--text-secondary);
            z-index: 10;
        }

        .provider-modal-close:hover {
            color: var(--text-primary);
        }

        .provider-profile-header {
            padding: var(--spacing-xxl) var(--spacing-lg);
            border-bottom: 1px solid var(--border-color);
        }

        .provider-profile-cover {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: var(--light-gray);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            margin-bottom: var(--spacing-md);
            font-size: 2.5rem;
        }

        .provider-profile-cover img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .provider-profile-name {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: var(--spacing-xs);
        }

        .provider-rating {
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
            margin-bottom: var(--spacing-md);
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .provider-rating-stars {
            color: #ffc107;
        }

        .provider-description {
            color: var(--text-secondary);
            line-height: 1.6;
            margin-bottom: var(--spacing-md);
        }

        .provider-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: var(--spacing-md);
            margin-top: var(--spacing-md);
        }

        .provider-info-item {
            padding: var(--spacing-sm);
            background: var(--light-gray);
            border-radius: var(--border-radius);
        }

        .provider-info-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: var(--text-secondary);
            margin-bottom: 0.25rem;
        }

        .provider-info-value {
            font-weight: 600;
            color: var(--primary);
            font-size: 1rem;
        }

        .provider-reviews-section {
            padding: var(--spacing-lg);
            border-top: 1px solid var(--border-color);
        }

        .provider-reviews-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: var(--spacing-md);
        }

        .review-item {
            padding: var(--spacing-md);
            background: var(--light-gray);
            border-radius: var(--border-radius);
            margin-bottom: var(--spacing-md);
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: var(--spacing-sm);
        }

        .review-author {
            font-weight: 600;
            color: var(--primary);
        }

        .review-rating {
            color: #ffc107;
        }

        .review-date {
            font-size: 0.85rem;
            color: var(--text-secondary);
        }

        .review-comment {
            color: var(--text-secondary);
            line-height: 1.5;
        }

        .no-reviews {
            color: var(--text-secondary);
            font-style: italic;
        }

        .provider-modal-loading {
            text-align: center;
            padding: var(--spacing-xxl) var(--spacing-lg);
        }

        .provider-modal-error {
            color: #d32f2f;
            background: rgba(211, 47, 47, 0.1);
            padding: var(--spacing-lg);
            border-radius: var(--border-radius);
            margin: var(--spacing-lg);
        }

        @media (max-width: 768px) {
            .shop-container {
                padding: var(--spacing-lg);
            }

            .shop-header h1 {
                font-size: 1.75rem;
            }

            .shop-layout {
                grid-template-columns: 1fr;
            }

            .shop-sidebar {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                height: auto;
            }

            .shop-main {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: var(--spacing-md);
            }

            .provider-modal-content {
                width: 95%;
                max-height: 95vh;
            }
        }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/views/header.php'; ?>

    <div class="shop-container">
        <div class="shop-header">
            <h1>Photography Services</h1>
            <p>Discover professional photography services from talented photographers in Ghana</p>
        </div>

        <div class="shop-layout">
            <!-- Sidebar Filters -->
            <aside class="shop-sidebar">
                <!-- Category Filter -->
                <div class="filter-group">
                    <div class="filter-title">Categories</div>
                    <div class="filter-option">
                        <input type="radio" id="cat-all" name="category" value="0" checked>
                        <label for="cat-all">All Categories</label>
                    </div>
                    <?php if ($categories): ?>
                        <?php foreach ($categories as $category): ?>
                            <div class="filter-option">
                                <input type="radio" id="cat-<?php echo $category['cat_id']; ?>" name="category" value="<?php echo $category['cat_id']; ?>">
                                <label for="cat-<?php echo $category['cat_id']; ?>"><?php echo htmlspecialchars($category['cat_name']); ?></label>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Product Type Filter - Hidden, only services shown -->
                <input type="hidden" id="type-service" name="product_type" value="service">
            </aside>

            <!-- Products Grid -->
            <main class="shop-main" id="productsGrid">
                <div class="empty-state">
                    <svg class="empty-state-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    <h3 class="empty-state-title">Loading products...</h3>
                </div>
            </main>
        </div>
    </div>

    <?php require_once __DIR__ . '/views/footer.php'; ?>

    <!-- Provider Profile Modal -->
    <div id="providerModal" class="provider-modal">
        <div class="provider-modal-content">
            <button class="provider-modal-close" onclick="closeProviderModal()">&times;</button>
            <div id="modalBody">
                <div class="provider-modal-loading">
                    <p>Loading provider information...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden CSRF token for cart operations -->
    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

    <script src="<?php echo SITE_URL; ?>/js/shop.js"></script>
</body>
</html>
