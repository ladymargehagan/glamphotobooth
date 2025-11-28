<?php
/**
 * Dashboard Sidebar Component
 * views/dashboard_sidebar.php
 *
 * Displays role-based sidebar navigation
 * Use: <?php require_once __DIR__ . '/../views/dashboard_sidebar.php'; ?>
 */

$user_role = isset($_SESSION['user_role']) ? intval($_SESSION['user_role']) : 0;
$current_page = basename($_SERVER['PHP_SELF']);
?>

<aside class="dashboard-sidebar">
    <div class="sidebar-header">
        <a href="/" class="sidebar-logo">
            <h3>PhotoMarket</h3>
        </a>
    </div>

    <?php if ($user_role == 3): // Vendor ?>
        <div class="sidebar-welcome">
            <p>Welcome, <strong><?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Vendor'; ?></strong></p>
        </div>

        <div class="sidebar-section">
            <ul class="sidebar-nav">
                <li class="sidebar-nav-item <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                    <a href="<?php echo SITE_URL; ?>/vendor/dashboard.php" class="sidebar-nav-link">
                        <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                        Dashboard
                    </a>
                </li>
                <li class="sidebar-nav-item <?php echo ($current_page == 'inventory.php') ? 'active' : ''; ?>">
                    <a href="<?php echo SITE_URL; ?>/vendor/inventory.php" class="sidebar-nav-link">
                        <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <circle cx="9" cy="21" r="1"></circle>
                            <circle cx="20" cy="21" r="1"></circle>
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                        </svg>
                        Inventory
                    </a>
                </li>
                <li class="sidebar-nav-item <?php echo ($current_page == 'orders.php') ? 'active' : ''; ?>">
                    <a href="<?php echo SITE_URL; ?>/vendor/orders.php" class="sidebar-nav-link">
                        <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M6 9h12M6 9a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2M6 9v10a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V9M10 5V3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2"></path>
                        </svg>
                        Orders
                    </a>
                </li>
                <li class="sidebar-nav-item <?php echo (strpos($current_page, 'manage_products') !== false || strpos($current_page, 'add_product') !== false) ? 'active' : ''; ?>">
                    <a href="<?php echo SITE_URL; ?>/customer/manage_products.php" class="sidebar-nav-link">
                        <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M12 5v14M5 12h14"></path>
                        </svg>
                        Manage Products
                    </a>
                </li>
            </ul>
        </div>

        <div class="sidebar-section">
            <div class="sidebar-section-title">Business</div>
            <ul class="sidebar-nav">
                <li class="sidebar-nav-item <?php echo ($current_page == 'edit_profile.php') ? 'active' : ''; ?>">
                    <a href="<?php echo SITE_URL; ?>/customer/edit_profile.php" class="sidebar-nav-link">
                        <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        Business Profile
                    </a>
                </li>
                <li class="sidebar-nav-item <?php echo ($current_page == 'earnings.php') ? 'active' : ''; ?>">
                    <a href="<?php echo SITE_URL; ?>/customer/earnings.php" class="sidebar-nav-link">
                        <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <line x1="12" y1="1" x2="12" y2="23"></line>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                        </svg>
                        Revenue
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="<?php echo SITE_URL; ?>/actions/logout.php" class="sidebar-nav-link">
                        <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                        Logout
                    </a>
                </li>
            </ul>
        </div>

    <?php elseif ($user_role == 2): // Photographer ?>
        <div class="sidebar-welcome">
            <p>Welcome, <strong><?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Photographer'; ?></strong></p>
        </div>

        <div class="sidebar-section">
            <ul class="sidebar-nav">
                <li class="sidebar-nav-item <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                    <a href="<?php echo SITE_URL; ?>/photographer/dashboard.php" class="sidebar-nav-link">
                        <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                        Dashboard
                    </a>
                </li>
                <li class="sidebar-nav-item <?php echo ($current_page == 'manage_bookings.php' || strpos($current_page, 'manage_bookings') !== false) ? 'active' : ''; ?>">
                    <a href="<?php echo SITE_URL; ?>/customer/manage_bookings.php" class="sidebar-nav-link">
                        <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        Bookings
                    </a>
                </li>
                <li class="sidebar-nav-item <?php echo (strpos($current_page, 'manage_products') !== false || strpos($current_page, 'add_product') !== false) ? 'active' : ''; ?>">
                    <a href="<?php echo SITE_URL; ?>/customer/manage_products.php" class="sidebar-nav-link">
                        <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M12 5v14M5 12h14"></path>
                        </svg>
                        My Services
                    </a>
                </li>
                <li class="sidebar-nav-item <?php echo ($current_page == 'galleries.php') ? 'active' : ''; ?>">
                    <a href="<?php echo SITE_URL; ?>/photographer/galleries.php" class="sidebar-nav-link">
                        <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                            <polyline points="21 15 16 10 5 21"></polyline>
                        </svg>
                        Galleries
                    </a>
                </li>
            </ul>
        </div>

        <div class="sidebar-section">
            <div class="sidebar-section-title">Business</div>
            <ul class="sidebar-nav">
                <li class="sidebar-nav-item <?php echo ($current_page == 'edit_profile.php') ? 'active' : ''; ?>">
                    <a href="<?php echo SITE_URL; ?>/customer/edit_profile.php" class="sidebar-nav-link">
                        <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        Edit Profile
                    </a>
                </li>
                <li class="sidebar-nav-item <?php echo ($current_page == 'earnings.php') ? 'active' : ''; ?>">
                    <a href="<?php echo SITE_URL; ?>/customer/earnings.php" class="sidebar-nav-link">
                        <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <line x1="12" y1="1" x2="12" y2="23"></line>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                        </svg>
                        Earnings
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="<?php echo SITE_URL; ?>/actions/logout.php" class="sidebar-nav-link">
                        <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                        Logout
                    </a>
                </li>
            </ul>
        </div>

    <?php elseif ($user_role == 4): // Customer ?>
        <div class="sidebar-welcome">
            <p>Welcome, <strong><?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Customer'; ?></strong></p>
        </div>

        <div class="sidebar-section">
            <ul class="sidebar-nav">
                <li class="sidebar-nav-item <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                    <a href="<?php echo SITE_URL; ?>/customer/dashboard.php" class="sidebar-nav-link">
                        <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                        Dashboard
                    </a>
                </li>
                <li class="sidebar-nav-item <?php echo ($current_page == 'my_bookings.php') ? 'active' : ''; ?>">
                    <a href="<?php echo SITE_URL; ?>/customer/my_bookings.php" class="sidebar-nav-link">
                        <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M6 9h12M6 9a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V9z"></path>
                        </svg>
                        My Bookings
                    </a>
                </li>
                <li class="sidebar-nav-item <?php echo ($current_page == 'my_galleries.php') ? 'active' : ''; ?>">
                    <a href="<?php echo SITE_URL; ?>/customer/my_galleries.php" class="sidebar-nav-link">
                        <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <rect x="3" y="3" width="18" height="18" rx="2"></rect>
                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                            <polyline points="21 15 16 10 5 21"></polyline>
                        </svg>
                        My Galleries
                    </a>
                </li>
                <li class="sidebar-nav-item <?php echo ($current_page == 'orders.php') ? 'active' : ''; ?>">
                    <a href="<?php echo SITE_URL; ?>/customer/orders.php" class="sidebar-nav-link">
                        <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <circle cx="9" cy="21" r="1"></circle>
                            <circle cx="20" cy="21" r="1"></circle>
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                        </svg>
                        My Orders
                    </a>
                </li>
            </ul>
        </div>

        <div class="sidebar-section">
            <div class="sidebar-section-title">Account</div>
            <ul class="sidebar-nav">
                <li class="sidebar-nav-item <?php echo ($current_page == 'my_profile.php') ? 'active' : ''; ?>">
                    <a href="<?php echo SITE_URL; ?>/customer/my_profile.php" class="sidebar-nav-link">
                        <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        My Profile
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="<?php echo SITE_URL; ?>/actions/logout.php" class="sidebar-nav-link">
                        <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                        Logout
                    </a>
                </li>
            </ul>
        </div>
    <?php endif; ?>
</aside>

<style>
    .dashboard-sidebar {
        width: 250px;
        background: var(--white);
        border-right: 1px solid var(--border-color);
        padding: var(--spacing-lg) 0;
        height: 100vh;
        position: sticky;
        top: 0;
        overflow-y: auto;
        box-shadow: 2px 0 8px rgba(0, 0, 0, 0.05);
    }

    .sidebar-header {
        padding: var(--spacing-lg) var(--spacing-lg);
        border-bottom: 1px solid var(--border-color);
        margin-bottom: var(--spacing-lg);
    }

    .sidebar-logo {
        text-decoration: none;
        color: inherit;
    }

    .sidebar-logo h3 {
        color: var(--primary);
        margin: 0;
        font-size: 1.3rem;
        font-family: var(--font-serif);
    }

    .sidebar-welcome {
        padding: var(--spacing-md) var(--spacing-lg);
        background: rgba(226, 196, 146, 0.1);
        margin: 0 var(--spacing-lg) var(--spacing-lg) var(--spacing-lg);
        border-radius: var(--border-radius);
    }

    .sidebar-welcome p {
        margin: 0;
        font-size: 0.9rem;
        color: var(--text-secondary);
    }

    .sidebar-welcome strong {
        color: var(--primary);
    }

    .sidebar-section {
        margin-bottom: var(--spacing-lg);
        padding: 0 var(--spacing-lg);
    }

    .sidebar-section-title {
        font-weight: 600;
        color: var(--primary);
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: var(--spacing-md);
        padding: 0 var(--spacing-sm);
    }

    .sidebar-nav {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .sidebar-nav-item {
        margin-bottom: var(--spacing-sm);
    }

    .sidebar-nav-link {
        display: flex;
        align-items: center;
        gap: var(--spacing-sm);
        padding: var(--spacing-md) var(--spacing-sm);
        color: var(--text-secondary);
        text-decoration: none;
        border-radius: var(--border-radius);
        transition: all 0.3s ease;
        font-weight: 500;
        font-size: 0.95rem;
    }

    .sidebar-nav-link:hover {
        background: rgba(16, 33, 82, 0.05);
        color: var(--primary);
    }

    .sidebar-nav-item.active .sidebar-nav-link {
        background: rgba(226, 196, 146, 0.15);
        color: var(--primary);
        border-left: 3px solid var(--secondary);
        padding-left: calc(var(--spacing-sm) - 3px);
    }

    .sidebar-nav-icon {
        width: 20px;
        height: 20px;
        flex-shrink: 0;
        stroke-width: 2;
    }

    @media (max-width: 768px) {
        .dashboard-sidebar {
            width: 70px;
            padding: var(--spacing-md) 0;
        }

        .sidebar-header,
        .sidebar-welcome,
        .sidebar-section-title,
        .sidebar-nav-link span {
            display: none;
        }

        .sidebar-nav-link {
            justify-content: center;
            padding: var(--spacing-md);
        }
    }
</style>
