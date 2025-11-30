<?php
/**
 * Admin Dashboard Sidebar Component
 * views/admin_sidebar.php
 *
 * Displays admin navigation sidebar
 * Use: <?php require_once __DIR__ . '/../views/admin_sidebar.php'; ?>
 */

$current_page = basename($_SERVER['PHP_SELF']);
?>

<aside class="dashboard-sidebar">
    <div class="sidebar-header">
        <a href="<?php echo SITE_URL; ?>" class="sidebar-logo">
            <h3>GlamPhotobooth Accra</h3>
        </a>
    </div>

    <div class="sidebar-welcome">
        <p>Welcome, <strong><?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Admin'; ?></strong></p>
    </div>

    <div class="sidebar-section">
        <ul class="sidebar-nav">
            <li class="sidebar-nav-item <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                <a href="<?php echo SITE_URL; ?>/admin/dashboard.php" class="sidebar-nav-link">
                    <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                    Dashboard
                </a>
            </li>
            <li class="sidebar-nav-item <?php echo ($current_page == 'category.php') ? 'active' : ''; ?>">
                <a href="<?php echo SITE_URL; ?>/admin/category.php" class="sidebar-nav-link">
                    <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M6 9h12M6 9a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V9z"></path>
                    </svg>
                    Categories
                </a>
            </li>
            <li class="sidebar-nav-item <?php echo ($current_page == 'manage_users.php') ? 'active' : ''; ?>">
                <a href="<?php echo SITE_URL; ?>/admin/manage_users.php" class="sidebar-nav-link">
                    <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    Manage Users
                </a>
            </li>
            <li class="sidebar-nav-item <?php echo ($current_page == 'manage_orders.php') ? 'active' : ''; ?>">
                <a href="<?php echo SITE_URL; ?>/admin/manage_orders.php" class="sidebar-nav-link">
                    <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                    Manage Orders
                </a>
            </li>
            <li class="sidebar-nav-item <?php echo ($current_page == 'payment_requests.php') ? 'active' : ''; ?>">
                <a href="<?php echo SITE_URL; ?>/admin/payment_requests.php" class="sidebar-nav-link">
                    <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                    </svg>
                    Payment Requests
                </a>
            </li>
        </ul>
    </div>

    <div class="sidebar-section">
        <div class="sidebar-section-title">Account</div>
        <ul class="sidebar-nav">
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
</aside>
