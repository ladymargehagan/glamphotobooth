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

            <!-- Stats Row -->
            <div class="stats-row">
                <div class="stat-card">
                    <div class="stat-label">Rating</div>
                    <div class="stat-value">—</div>
                    <div class="stat-change">Build your reputation</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Monthly Revenue</div>
                    <div class="stat-value">₵0</div>
                    <div class="stat-change">No revenue yet</div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="dashboard-grid">
                <div class="dashboard-card">
                    <svg class="card-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <line x1="12" y1="1" x2="12" y2="23"></line>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                    </svg>
                    <h3 class="card-title">View Revenue</h3>
                    <p class="card-subtitle">Track earnings and payment history</p>
                    <a href="<?php echo SITE_URL; ?>/customer/earnings.php" class="card-action">View Revenue →</a>
                </div>
            </div>
        </main>
    </div>

    <script src="<?php echo SITE_URL; ?>/js/sweetalert.js"></script>
</body>
</html>
