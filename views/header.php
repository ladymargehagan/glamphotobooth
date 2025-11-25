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
                        <li><a href="<?php echo SITE_URL; ?>/shop.php" class="nav-link">Shop</a></li>
                        <li><a href="/services" class="nav-link">Services</a></li>
                        <li><a href="/equipment" class="nav-link">Equipment</a></li>
                        <li><a href="/gallery" class="nav-link">Gallery</a></li>
                        <li><a href="/about" class="nav-link">About</a></li>
                    </ul>
                </nav>

                <div class="navbar-actions flex gap-sm">
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
</style>
