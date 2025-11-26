<?php
/**
 * Public Customer Profile Page
 * customer/profile.php
 */

require_once __DIR__ . '/../settings/core.php';

if (!class_exists('customer_class')) {
    require_once __DIR__ . '/../classes/customer_class.php';
}

$customer_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($customer_id <= 0) {
    header('Location: ' . SITE_URL . '/shop.php');
    exit;
}

$customer_class = new customer_class();
$customer = $customer_class->get_customer_by_id($customer_id);

if (!$customer) {
    header('Location: ' . SITE_URL . '/shop.php');
    exit;
}

$pageTitle = htmlspecialchars($customer['name']) . ' - Customer Profile';
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
            padding: var(--spacing-xxl) var(--spacing-xl);
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

        .profile-contact {
            color: var(--text-secondary);
            font-size: 0.95rem;
            margin-bottom: var(--spacing-lg);
        }

        .profile-sidebar {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: var(--spacing-lg);
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
            height: fit-content;
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
            border: none;
            cursor: pointer;
        }

        .btn-primary-wide:hover {
            background: #0d1a3a;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16,33,82,0.2);
        }

        .customer-info-section {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 var(--spacing-xl) var(--spacing-xxl);
            border-top: 1px solid rgba(226,196,146,0.3);
        }

        .customer-info-section h2 {
            font-family: var(--font-serif);
            font-size: 1.5rem;
            color: var(--primary);
            margin-bottom: var(--spacing-lg);
        }

        .info-box {
            background: var(--white);
            padding: var(--spacing-lg);
            border-radius: var(--border-radius);
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            margin-bottom: var(--spacing-lg);
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: var(--spacing-sm) 0;
            border-bottom: 1px solid var(--border-color);
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: var(--primary);
        }

        .info-value {
            color: var(--text-secondary);
        }

        @media (max-width: 768px) {
            .profile-hero {
                grid-template-columns: 1fr;
                padding: var(--spacing-lg);
            }

            .customer-info-section {
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

    <section class="profile-hero">
        <div>
            <h1 class="profile-main-title"><?php echo htmlspecialchars($customer['name']); ?></h1>
            <div class="profile-location">
                <?php if (!empty($customer['city']) || !empty($customer['country'])): ?>
                    📍
                    <?php if (!empty($customer['city'])): ?>
                        <?php echo htmlspecialchars($customer['city']); ?>
                    <?php endif; ?>
                    <?php if (!empty($customer['country']) && !empty($customer['city'])): ?>
                        ,
                    <?php endif; ?>
                    <?php if (!empty($customer['country'])): ?>
                        <?php echo htmlspecialchars($customer['country']); ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <div class="profile-contact">
                📧 <?php echo htmlspecialchars($customer['email']); ?>
                <?php if (!empty($customer['contact'])): ?>
                    <br>📱 <?php echo htmlspecialchars($customer['contact']); ?>
                <?php endif; ?>
            </div>
            <p style="color: var(--text-secondary); font-size: 0.95rem;">
                Member since <?php echo date('F Y', strtotime($customer['created_at'])); ?>
            </p>
        </div>

        <aside class="profile-sidebar">
            <h3>About This Customer</h3>
            <div class="profile-sidebar-row">
                <span>Member Since</span>
                <span><?php echo date('M Y', strtotime($customer['created_at'])); ?></span>
            </div>
            <?php if (!empty($customer['city'])): ?>
                <div class="profile-sidebar-row">
                    <span>Location</span>
                    <span><?php echo htmlspecialchars($customer['city']); ?></span>
                </div>
            <?php endif; ?>
            <div class="profile-actions">
                <a href="<?php echo SITE_URL; ?>/shop.php" class="btn-primary-wide">
                    Browse Services & Products
                </a>
            </div>
        </aside>
    </section>

    <section class="customer-info-section">
        <h2>Customer Information</h2>
        <div class="info-box">
            <div class="info-row">
                <span class="info-label">Name</span>
                <span class="info-value"><?php echo htmlspecialchars($customer['name']); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Email</span>
                <span class="info-value"><?php echo htmlspecialchars($customer['email']); ?></span>
            </div>
            <?php if (!empty($customer['contact'])): ?>
                <div class="info-row">
                    <span class="info-label">Phone</span>
                    <span class="info-value"><?php echo htmlspecialchars($customer['contact']); ?></span>
                </div>
            <?php endif; ?>
            <?php if (!empty($customer['city'])): ?>
                <div class="info-row">
                    <span class="info-label">City</span>
                    <span class="info-value"><?php echo htmlspecialchars($customer['city']); ?></span>
                </div>
            <?php endif; ?>
            <?php if (!empty($customer['country'])): ?>
                <div class="info-row">
                    <span class="info-label">Country</span>
                    <span class="info-value"><?php echo htmlspecialchars($customer['country']); ?></span>
                </div>
            <?php endif; ?>
            <div class="info-row">
                <span class="info-label">Member Since</span>
                <span class="info-value"><?php echo date('F d, Y', strtotime($customer['created_at'])); ?></span>
            </div>
        </div>
    </section>

    <?php require_once __DIR__ . '/../views/footer.php'; ?>
</body>
</html>
