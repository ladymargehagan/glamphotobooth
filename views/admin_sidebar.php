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
            <h3>PhotoMarket</h3>
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
        .sidebar-section-title {
            display: none;
        }

        .sidebar-nav-link {
            justify-content: center;
            padding: var(--spacing-md);
        }

        .sidebar-nav-link span {
            display: none;
        }
    }
</style>
