<?php
/**
 * Earnings/Revenue Page
 * customer/earnings.php
 */
require_once __DIR__ . '/../settings/core.php';

// Require login
requireLogin();

// Check if user is photographer (role 2) or vendor (role 3)
$user_role = isset($_SESSION['user_role']) ? intval($_SESSION['user_role']) : 4;
if ($user_role != 2 && $user_role != 3) {
    header('Location: ' . SITE_URL . '/index.php');
    exit;
}

$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

// Get provider details
$provider_class = new provider_class();
$provider = $provider_class->get_provider_by_customer($user_id);

if (!$provider) {
    header('Location: ' . SITE_URL . '/customer/profile_setup.php');
    exit;
}

// Get completed bookings for earnings calculation
$booking_class = new booking_class();
$all_bookings = $booking_class->get_provider_bookings($provider['provider_id']);

// Calculate earnings
$total_earnings = 0;
$completed_bookings = [];
$earnings_by_month = [];

if ($all_bookings) {
    foreach ($all_bookings as $booking) {
        if ($booking['status'] === 'completed') {
            $total_earnings += floatval($booking['total_price']);
            $completed_bookings[] = $booking;

            // Group by month
            $month = date('Y-m', strtotime($booking['booking_date']));
            if (!isset($earnings_by_month[$month])) {
                $earnings_by_month[$month] = 0;
            }
            $earnings_by_month[$month] += floatval($booking['total_price']);
        }
    }
}

// Sort by month descending
krsort($earnings_by_month);

// Sort completed bookings by date descending
usort($completed_bookings, function($a, $b) {
    return strtotime($b['booking_date']) - strtotime($a['booking_date']);
});

$pageTitle = 'Earnings - PhotoMarket';
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
</head>
<body>
    <div class="dashboard-layout">
        <!-- Sidebar -->
        <aside class="dashboard-sidebar">
            <div class="sidebar-header">
                <div class="sidebar-brand">PhotoMarket</div>
                <div class="sidebar-user">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></div>
            </div>

            <nav>
                <ul class="sidebar-nav">
                    <li class="sidebar-nav-item">
                        <a href="<?php echo SITE_URL; ?>/<?php echo $user_role === 2 ? 'photographer' : 'vendor'; ?>/dashboard.php" class="sidebar-nav-link">
                            <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                <polyline points="9 22 9 12 15 12 15 22"></polyline>
                            </svg>
                            Dashboard
                        </a>
                    </li>
                </ul>

                <div class="sidebar-section">
                    <div class="sidebar-section-title">Business</div>
                    <ul class="sidebar-nav">
                        <li class="sidebar-nav-item">
                            <a href="<?php echo SITE_URL; ?>/customer/earnings.php" class="sidebar-nav-link active">
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
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="dashboard-content">
            <div class="dashboard-header">
                <div>
                    <h1 class="dashboard-title">Earnings</h1>
                    <p class="dashboard-subtitle">Track your income from completed bookings</p>
                </div>
            </div>

            <!-- Earnings Stats -->
            <div class="stats-row">
                <div class="stat-card">
                    <div class="stat-label">Total Earnings</div>
                    <div class="stat-value">₵<?php echo number_format($total_earnings, 2); ?></div>
                    <div class="stat-change"><?php echo count($completed_bookings); ?> completed bookings</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Completed Bookings</div>
                    <div class="stat-value"><?php echo count($completed_bookings); ?></div>
                    <div class="stat-change">From completed work</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Average per Booking</div>
                    <div class="stat-value">₵<?php echo count($completed_bookings) > 0 ? number_format($total_earnings / count($completed_bookings), 2) : '0.00'; ?></div>
                    <div class="stat-change">Across all bookings</div>
                </div>
            </div>

            <!-- Earnings by Month -->
            <?php if (!empty($earnings_by_month)): ?>
            <div style="background: var(--white); border-radius: var(--border-radius); padding: var(--spacing-lg); box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06); margin-bottom: var(--spacing-lg);">
                <h2 style="color: var(--primary); margin-top: 0; margin-bottom: var(--spacing-lg);">Earnings by Month</h2>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--spacing-lg);">
                    <?php foreach ($earnings_by_month as $month => $amount): ?>
                    <div style="padding: var(--spacing-lg); background: rgba(226, 196, 146, 0.05); border-radius: var(--border-radius); border: 1px solid rgba(226, 196, 146, 0.2);">
                        <p style="color: var(--text-secondary); font-size: 0.9rem; margin: 0 0 var(--spacing-sm) 0;"><?php echo date('F Y', strtotime($month . '-01')); ?></p>
                        <p style="color: var(--primary); font-size: 1.5rem; font-weight: 700; margin: 0;">₵<?php echo number_format($amount, 2); ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Completed Bookings Table -->
            <div>
                <h2 style="color: var(--primary); margin-bottom: var(--spacing-lg);">Completed Bookings</h2>
                <?php if (!empty($completed_bookings)): ?>
                <div style="background: var(--white); border-radius: var(--border-radius); overflow: hidden; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: rgba(226, 196, 146, 0.05);">
                                <th style="padding: var(--spacing-md); text-align: left; color: var(--primary); font-weight: 600; border-bottom: 2px solid rgba(226, 196, 146, 0.2);">Booking Date</th>
                                <th style="padding: var(--spacing-md); text-align: left; color: var(--primary); font-weight: 600; border-bottom: 2px solid rgba(226, 196, 146, 0.2);">Customer</th>
                                <th style="padding: var(--spacing-md); text-align: left; color: var(--primary); font-weight: 600; border-bottom: 2px solid rgba(226, 196, 146, 0.2);">Service</th>
                                <th style="padding: var(--spacing-md); text-align: right; color: var(--primary); font-weight: 600; border-bottom: 2px solid rgba(226, 196, 146, 0.2);">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($completed_bookings as $booking): ?>
                            <tr style="border-bottom: 1px solid rgba(226, 196, 146, 0.1);">
                                <td style="padding: var(--spacing-md); color: var(--text-primary);">
                                    <?php echo date('M d, Y', strtotime($booking['booking_date'])); ?>
                                </td>
                                <td style="padding: var(--spacing-md); color: var(--text-primary);">
                                    <?php echo htmlspecialchars($booking['customer_name'] ?? 'Customer'); ?>
                                </td>
                                <td style="padding: var(--spacing-md); color: var(--text-secondary); font-size: 0.9rem;">
                                    <?php echo htmlspecialchars(substr($booking['service_description'], 0, 50)); ?><?php echo strlen($booking['service_description']) > 50 ? '...' : ''; ?>
                                </td>
                                <td style="padding: var(--spacing-md); text-align: right; color: var(--primary); font-weight: 600;">
                                    ₵<?php echo number_format($booking['total_price'], 2); ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <svg class="empty-state-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <line x1="12" y1="1" x2="12" y2="23"></line>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                    </svg>
                    <h3 class="empty-state-title">No Earnings Yet</h3>
                    <p class="empty-state-text">You don't have any completed bookings yet. Once customers book your services and you complete the work, your earnings will appear here.</p>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
