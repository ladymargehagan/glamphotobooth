<?php
/**
 * Dynamic Dashboard Sidebar Component
 * views/dashboard_sidebar.php
 *
 * Displays role-based sidebar navigation
 * Use: <?php require_once __DIR__ . '/../views/dashboard_sidebar.php'; ?>
 */

$user_role = isset($_SESSION['user_role']) ? intval($_SESSION['user_role']) : 0;
$user_name = isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'User';
$current_page = basename($_SERVER['PHP_SELF']);

// Define role configurations
$role_config = [
    1 => [ // Admin
        'name' => 'Admin',
        'base_url' => '/admin',
        'sections' => [] // Admin uses its own sidebar
    ],
    2 => [ // Photographer
        'name' => 'Photographer',
        'base_url' => '/photographer',
        'sections' => [
            'main' => [
                'title' => null,
                'items' => [
                    [
                        'label' => 'Dashboard',
                        'url' => '/photographer/dashboard.php',
                        'icon' => '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline>',
                        'active_pages' => ['dashboard.php']
                    ],
                    [
                        'label' => 'Bookings',
                        'url' => '/photographer/manage_bookings.php',
                        'icon' => '<rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line>',
                        'active_pages' => ['manage_bookings.php', 'upload_photos.php'],
                        'active_keywords' => ['booking']
                    ],
                    [
                        'label' => 'Galleries',
                        'url' => '/photographer/galleries.php',
                        'icon' => '<rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline>',
                        'active_pages' => ['galleries.php']
                    ]
                ]
            ],
            'business' => [
                'title' => 'Business',
                'items' => [
                    [
                        'label' => 'Business Profile',
                        'url' => '/photographer/edit_profile.php',
                        'icon' => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle>',
                        'active_pages' => ['edit_profile.php', 'profile_setup.php'],
                        'active_keywords' => ['profile']
                    ],
                    [
                        'label' => 'Earnings',
                        'url' => '/photographer/earnings.php',
                        'icon' => '<line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>',
                        'active_pages' => ['earnings.php']
                    ],
                    [
                        'label' => 'Logout',
                        'url' => '/actions/logout.php',
                        'icon' => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line>',
                        'active_pages' => []
                    ]
                ]
            ]
        ]
    ],
    3 => [ // Vendor
        'name' => 'Vendor',
        'base_url' => '/vendor',
        'sections' => [
            'main' => [
                'title' => null,
                'items' => [
                    [
                        'label' => 'Dashboard',
                        'url' => '/vendor/dashboard.php',
                        'icon' => '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline>',
                        'active_pages' => ['dashboard.php']
                    ],
                    [
                        'label' => 'Inventory',
                        'url' => '/vendor/inventory.php',
                        'icon' => '<circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>',
                        'active_pages' => ['inventory.php']
                    ],
                    [
                        'label' => 'Orders',
                        'url' => '/vendor/orders.php',
                        'icon' => '<path d="M6 9h12M6 9a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2M6 9v10a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V9M10 5V3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2"></path>',
                        'active_pages' => ['orders.php']
                    ],
                    [
                        'label' => 'My Products',
                        'url' => '/vendor/manage_products.php',
                        'icon' => '<path d="M12 5v14M5 12h14"></path>',
                        'active_pages' => ['manage_products.php', 'add_product.php', 'edit_product.php'],
                        'active_keywords' => ['product']
                    ]
                ]
            ],
            'business' => [
                'title' => 'Business',
                'items' => [
                    [
                        'label' => 'Business Profile',
                        'url' => '/vendor/edit_profile.php',
                        'icon' => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle>',
                        'active_pages' => ['edit_profile.php', 'profile_setup.php'],
                        'active_keywords' => ['profile']
                    ],
                    [
                        'label' => 'Revenue',
                        'url' => '/vendor/earnings.php',
                        'icon' => '<line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>',
                        'active_pages' => ['earnings.php']
                    ],
                    [
                        'label' => 'Logout',
                        'url' => '/actions/logout.php',
                        'icon' => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line>',
                        'active_pages' => []
                    ]
                ]
            ]
        ]
    ],
    4 => [ // Customer
        'name' => 'Customer',
        'base_url' => '/customer',
        'sections' => [
            'main' => [
                'title' => null,
                'items' => [
                    [
                        'label' => 'Dashboard',
                        'url' => '/customer/dashboard.php',
                        'icon' => '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline>',
                        'active_pages' => ['dashboard.php']
                    ],
                    [
                        'label' => 'My Bookings',
                        'url' => '/customer/my_bookings.php',
                        'icon' => '<path d="M6 9h12M6 9a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V9z"></path>',
                        'active_pages' => ['my_bookings.php']
                    ],
                    [
                        'label' => 'My Galleries',
                        'url' => '/customer/my_galleries.php',
                        'icon' => '<rect x="3" y="3" width="18" height="18" rx="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline>',
                        'active_pages' => ['my_galleries.php', 'view_gallery.php']
                    ],
                    [
                        'label' => 'My Orders',
                        'url' => '/customer/orders.php',
                        'icon' => '<circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>',
                        'active_pages' => ['orders.php', 'order_confirmation.php']
                    ]
                ]
            ],
            'account' => [
                'title' => 'Account',
                'items' => [
                    [
                        'label' => 'My Profile',
                        'url' => '/customer/my_profile.php',
                        'icon' => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle>',
                        'active_pages' => ['my_profile.php', 'edit_profile_customer.php']
                    ],
                    [
                        'label' => 'Logout',
                        'url' => '/actions/logout.php',
                        'icon' => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line>',
                        'active_pages' => []
                    ]
                ]
            ]
        ]
    ]
];

// Helper function to check if link is active
function isLinkActive($item, $current_page) {
    // Check exact page match
    if (in_array($current_page, $item['active_pages'])) {
        return true;
    }

    // Check keyword match
    if (isset($item['active_keywords'])) {
        foreach ($item['active_keywords'] as $keyword) {
            if (strpos($current_page, $keyword) !== false) {
                return true;
            }
        }
    }

    return false;
}

// Get current role config
$config = isset($role_config[$user_role]) ? $role_config[$user_role] : null;

if (!$config) {
    return; // No sidebar for invalid roles
}
?>

<aside class="dashboard-sidebar">
    <div class="sidebar-header">
        <a href="<?php echo SITE_URL; ?>" class="sidebar-logo">
            <h3>PhotoMarket</h3>
        </a>
    </div>

    <div class="sidebar-welcome">
        <p>Welcome, <strong><?php echo $user_name; ?></strong></p>
    </div>

    <?php foreach ($config['sections'] as $section): ?>
        <div class="sidebar-section">
            <?php if ($section['title']): ?>
                <div class="sidebar-section-title"><?php echo $section['title']; ?></div>
            <?php endif; ?>

            <ul class="sidebar-nav">
                <?php foreach ($section['items'] as $item): ?>
                    <?php $is_active = isLinkActive($item, $current_page); ?>
                    <li class="sidebar-nav-item <?php echo $is_active ? 'active' : ''; ?>">
                        <a href="<?php echo SITE_URL . $item['url']; ?>" class="sidebar-nav-link">
                            <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <?php echo $item['icon']; ?>
                            </svg>
                            <span><?php echo $item['label']; ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endforeach; ?>
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

        .sidebar-header h3,
        .sidebar-welcome,
        .sidebar-section-title,
        .sidebar-nav-link span {
            display: none;
        }

        .sidebar-header {
            padding: var(--spacing-md);
        }

        .sidebar-nav-link {
            justify-content: center;
            padding: var(--spacing-md);
        }
    }
</style>
