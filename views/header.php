<?php
/**
 * Header Navigation Component
 * views/header.php
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Photography Marketplace'; ?></title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Montserrat:wght@300;400;500;600;700&family=Allura&display=swap" rel="stylesheet">

    <!-- Stylesheet -->
    <link rel="stylesheet" href="<?php echo isset($cssPath) ? htmlspecialchars($cssPath) : SITE_URL . '/css/style.css'; ?>">

    <!-- Global variables for cart script -->
    <script>
        window.isLoggedIn = <?php echo isLoggedIn() ? 'true' : 'false'; ?>;
        window.loginUrl = '<?php echo SITE_URL; ?>/auth/login.php';
        window.siteUrl = '<?php echo SITE_URL; ?>';
    </script>
    <script src="<?php echo SITE_URL; ?>/js/cart.js"></script>
</head>
<body>
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
                        <?php
                        // Only show Marketplace to customers (role 4) and guests
                        $user_role = isLoggedIn() ? (isset($_SESSION['user_role']) ? intval($_SESSION['user_role']) : 4) : 4;
                        if ($user_role == 4):
                        ?>
                            <li><a href="<?php echo SITE_URL; ?>/shop.php" class="nav-link">Marketplace</a></li>
                        <?php endif; ?>
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
</body>
</html>

<style>
.navbar {
    background: var(--white);
    border-bottom: 1px solid var(--border-color);
    padding: var(--spacing-md) 0;
    position: sticky;
    top: 0;
    z-index: 100;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.navbar-brand a {
    display: flex;
    align-items: center;
}

.navbar-menu {
    flex: 1;
    display: flex;
    justify-content: center;
}

.navbar-items {
    list-style: none;
    display: flex;
    gap: var(--spacing-lg);
    align-items: center;
}

.nav-link {
    font-weight: 500;
    font-size: 0.95rem;
    position: relative;
}

.nav-link::after {
    content: '';
    position: absolute;
    bottom: -4px;
    left: 0;
    width: 0;
    height: 2px;
    background: var(--secondary);
    transition: var(--transition);
}

.nav-link:hover::after {
    width: 100%;
}

.navbar-actions {
    align-items: center;
}

@media (max-width: 768px) {
    .navbar-items {
        flex-direction: column;
        gap: var(--spacing-md);
    }
}

.cart-badge {
    position: absolute;
    top: -8px;
    right: -8px;
    background: var(--primary);
    color: white;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    font-size: 0.75rem;
    font-weight: 700;
    line-height: 24px;
    text-align: center;
    display: none !important;
}

.cart-badge[style*="display: block"] {
    display: block !important;
}
</style>
