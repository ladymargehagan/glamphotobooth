<?php
/**
 * Vendor Dashboard
 * vendor/dashboard.php
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
$profileComplete = $provider ? true : false;

$pageTitle = 'Vendor Dashboard - PhotoMarket';
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
    <!-- SweetAlert2 Library -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .profile-banner {
            background: linear-gradient(135deg, rgba(226, 196, 146, 0.1) 0%, rgba(16, 33, 82, 0.05) 100%);
            border: 1px solid rgba(226, 196, 146, 0.3);
            border-radius: var(--border-radius);
            padding: var(--spacing-lg);
            margin-bottom: var(--spacing-lg);
            display: flex;
            align-items: center;
            gap: var(--spacing-lg);
            animation: slideDown 0.3s ease;
        }

        .profile-banner-icon {
            flex-shrink: 0;
            width: 48px;
            height: 48px;
            background: rgba(226, 196, 146, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
        }

        .profile-banner-icon svg {
            width: 24px;
            height: 24px;
            stroke-width: 2;
        }

        .profile-banner-content {
            flex: 1;
        }

        .profile-banner-title {
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 4px;
        }

        .profile-banner-text {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .profile-banner-action {
            flex-shrink: 0;
            padding: 0.5rem 1rem;
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.85rem;
            text-decoration: none;
            display: inline-block;
        }

        .profile-banner-action:hover {
            background: #0d1a3a;
            transform: translateY(-2px);
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
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
                    <h1 class="dashboard-title">Vendor Dashboard</h1>
                    <p class="dashboard-subtitle">Manage your business and track revenue</p>
                </div>
            </div>

            <!-- Profile Completion Banner -->
            <?php if (!$profileComplete): ?>
            <div class="profile-banner">
                <div class="profile-banner-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div class="profile-banner-content">
                    <div class="profile-banner-title">Complete Your Business Profile</div>
                    <div class="profile-banner-text">Set up your business information to start selling products and receiving orders.</div>
                </div>
                <a href="<?php echo SITE_URL; ?>/vendor/edit_profile.php" class="profile-banner-action">Complete Profile</a>
            </div>
            <?php endif; ?>

            <!-- Stats Row -->
            <div class="stats-row">
                <div class="stat-card">
                    <div class="stat-label">Rating</div>
                    <div class="stat-value" id="ratingValue">—</div>
                    <div class="stat-change" id="ratingChange">Loading...</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Monthly Revenue</div>
                    <div class="stat-value" id="revenueValue">₵0</div>
                    <div class="stat-change" id="revenueChange">Loading...</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Total Products</div>
                    <div class="stat-value" id="productsValue">0</div>
                    <div class="stat-change" id="productsChange">Loading...</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Total Orders</div>
                    <div class="stat-value" id="ordersValue">0</div>
                    <div class="stat-change" id="ordersChange">Loading...</div>
                </div>
            </div>
        </main>
    </div>

    <script src="<?php echo SITE_URL; ?>/js/sweetalert.js"></script>
    <script>
        // Fetch and display vendor stats
        document.addEventListener('DOMContentLoaded', function() {
            fetch('<?php echo SITE_URL; ?>/actions/fetch_vendor_stats_action.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const stats = data.stats;

                        // Update Rating
                        document.getElementById('ratingValue').textContent = stats.rating > 0 ? stats.rating.toFixed(1) + '/5' : '—';
                        document.getElementById('ratingChange').textContent = stats.total_reviews > 0 ?
                            stats.total_reviews + ' review' + (stats.total_reviews !== 1 ? 's' : '') :
                            'No reviews yet';

                        // Update Monthly Revenue
                        document.getElementById('revenueValue').textContent = '₵' + stats.monthly_revenue;
                        document.getElementById('revenueChange').textContent = stats.monthly_revenue > 0 ?
                            'This month' :
                            'No revenue this month';

                        // Update Total Products
                        document.getElementById('productsValue').textContent = stats.total_products;
                        document.getElementById('productsChange').textContent = stats.total_products > 0 ?
                            'In your inventory' :
                            'Add products to get started';

                        // Update Total Orders
                        document.getElementById('ordersValue').textContent = stats.total_orders;
                        document.getElementById('ordersChange').textContent = stats.pending_orders > 0 ?
                            stats.pending_orders + ' pending order' + (stats.pending_orders !== 1 ? 's' : '') :
                            'All orders processed';
                    } else if (data.profile_incomplete) {
                        // Profile not complete - stats remain as placeholders
                        document.getElementById('ratingChange').textContent = 'Complete profile first';
                        document.getElementById('revenueChange').textContent = 'Complete profile first';
                        document.getElementById('productsChange').textContent = 'Complete profile first';
                        document.getElementById('ordersChange').textContent = 'Complete profile first';
                    } else {
                        console.error('Error fetching stats:', data.message);
                        document.getElementById('ratingChange').textContent = 'Error loading stats';
                        document.getElementById('revenueChange').textContent = 'Error loading stats';
                        document.getElementById('productsChange').textContent = 'Error loading stats';
                        document.getElementById('ordersChange').textContent = 'Error loading stats';
                    }
                })
                .catch(error => {
                    console.error('Network error:', error);
                    document.getElementById('ratingChange').textContent = 'Failed to load';
                    document.getElementById('revenueChange').textContent = 'Failed to load';
                    document.getElementById('productsChange').textContent = 'Failed to load';
                    document.getElementById('ordersChange').textContent = 'Failed to load';
                });
        });
    </script>
</body>
</html>
