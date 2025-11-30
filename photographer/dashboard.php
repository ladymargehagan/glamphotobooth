<?php
/**
 * Photographer Dashboard
 * photographer/dashboard.php
 */
require_once __DIR__ . '/../settings/core.php';

// Check if logged in
requireLogin();

// Check if user is photographer (role 2)
if ($_SESSION['user_role'] != 2) {
    header('Location: ' . SITE_URL . '/index.php');
    exit;
}

// Check if provider profile exists
$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$provider_class = new provider_class();
$provider = $provider_class->get_provider_by_customer($user_id);
$profileComplete = $provider ? true : false;

// Get booking statistics and recent bookings
$booking_stats = [
    'pending' => 0,
    'confirmed' => 0,
    'completed' => 0,
    'total' => 0
];
$recent_bookings = [];

if ($provider) {
    $booking_class = new booking_class();
    $provider_bookings = $booking_class->get_provider_bookings($provider['provider_id']);
    $booking_stats = $booking_class->get_provider_stats($provider['provider_id']);

    // Ensure provider_bookings is an array
    if (!is_array($provider_bookings)) {
        $provider_bookings = [];
    }

    // Get recent bookings (limit to 3)
    $recent_bookings = array_slice($provider_bookings, 0, 3);
}

$pageTitle = 'Photographer Dashboard - GlamPhotobooth Accra';
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
                    <h1 class="dashboard-title">Your Studio</h1>
                    <p class="dashboard-subtitle">Manage bookings, services, and grow your photography business</p>
                </div>
            </div>

            <!-- Stats Row -->
            <div class="stats-row">
                <div class="stat-card">
                    <div class="stat-label">Pending Requests</div>
                    <div class="stat-value"><?php echo intval($booking_stats['pending'] ?? 0); ?></div>
                    <div class="stat-change">Review incoming bookings</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Confirmed Bookings</div>
                    <div class="stat-value"><?php echo intval($booking_stats['confirmed'] ?? 0); ?></div>
                    <div class="stat-change">Upcoming sessions</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Completed Bookings</div>
                    <div class="stat-value"><?php echo intval($booking_stats['completed'] ?? 0); ?></div>
                    <div class="stat-change">Build your reputation</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Total Bookings</div>
                    <div class="stat-value"><?php echo intval($booking_stats['total'] ?? 0); ?></div>
                    <div class="stat-change">All time bookings</div>
                </div>
            </div>

            <!-- Recent Bookings Header Info -->
            <div style="margin-bottom: var(--spacing-lg);">
                <p style="color: var(--text-secondary); font-size: 0.95rem;">
                    View and manage your recent booking requests below. Click "View Details" to see full booking information and customer reviews.
                </p>
            </div>

            <!-- Recent Bookings Section -->
            <div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--spacing-lg);">
                    <h2 style="color: var(--primary); margin: 0;">Recent Booking Requests</h2>
                </div>

                <?php if ($profileComplete && $recent_bookings && count($recent_bookings) > 0): ?>
                    <div style="display: grid; gap: var(--spacing-lg);">
                        <?php foreach ($recent_bookings as $booking): ?>
                            <div style="background: var(--white); border-radius: var(--border-radius); padding: var(--spacing-lg); box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06); border-left: 4px solid var(--primary);">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: var(--spacing-md);">
                                    <div>
                                        <h3 style="color: var(--primary); font-weight: 600; margin: 0 0 var(--spacing-xs) 0;">
                                            <?php echo htmlspecialchars($booking['customer_name'] ?? 'Customer'); ?>
                                        </h3>
                                        <p style="color: var(--text-secondary); margin: 0; font-size: 0.9rem;">
                                            <?php echo htmlspecialchars($booking['email'] ?? 'No email'); ?>
                                        </p>
                                    </div>
                                    <span style="display: inline-block; padding: 0.4rem 0.8rem; border-radius: 4px; font-size: 0.8rem; font-weight: 600; text-transform: capitalize;
                                        <?php
                                        if ($booking['status'] === 'pending') {
                                            echo 'background: rgba(255, 152, 0, 0.15); color: #f57f17;';
                                        } elseif ($booking['status'] === 'confirmed') {
                                            echo 'background: rgba(76, 175, 80, 0.15); color: #2e7d32;';
                                        } elseif ($booking['status'] === 'completed') {
                                            echo 'background: rgba(33, 150, 243, 0.15); color: #0d47a1;';
                                        } else {
                                            echo 'background: rgba(244, 67, 54, 0.15); color: #b71c1c;';
                                        }
                                        ?>
                                    ">
                                        <?php echo htmlspecialchars($booking['status']); ?>
                                    </span>
                                </div>

                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: var(--spacing-md); margin-bottom: var(--spacing-md);">
                                    <div>
                                        <p style="color: var(--text-secondary); font-size: 0.85rem; margin: 0 0 4px 0; font-weight: 500;">Date</p>
                                        <p style="color: var(--text-primary); font-weight: 600; margin: 0;">
                                            <?php echo date('M d, Y', strtotime($booking['booking_date'])); ?>
                                        </p>
                                    </div>
                                    <div>
                                        <p style="color: var(--text-secondary); font-size: 0.85rem; margin: 0 0 4px 0; font-weight: 500;">Time</p>
                                        <p style="color: var(--text-primary); font-weight: 600; margin: 0;">
                                            <?php echo date('g:i A', strtotime($booking['booking_time'])); ?>
                                        </p>
                                    </div>
                                    <div>
                                        <p style="color: var(--text-secondary); font-size: 0.85rem; margin: 0 0 4px 0; font-weight: 500;">Phone</p>
                                        <p style="color: var(--text-primary); font-weight: 600; margin: 0;">
                                            <?php echo htmlspecialchars($booking['contact'] ?? 'N/A'); ?>
                                        </p>
                                    </div>
                                </div>

                                <div style="background: rgba(226, 196, 146, 0.05); padding: var(--spacing-md); border-radius: var(--border-radius); margin-bottom: var(--spacing-md);">
                                    <p style="color: var(--text-secondary); font-size: 0.9rem; line-height: 1.5; margin: 0;">
                                        <strong>Service:</strong> <?php echo htmlspecialchars(substr($booking['service_description'], 0, 100)); ?><?php echo strlen($booking['service_description']) > 100 ? '...' : ''; ?>
                                    </p>
                                </div>

                                <div style="display: flex; gap: var(--spacing-sm);">
                                    <a href="<?php echo SITE_URL; ?>/photographer/booking_details.php?booking_id=<?php echo $booking['booking_id']; ?>" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.85rem; text-decoration: none;">View Details</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <svg class="empty-state-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M6 9h12M6 9a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V9z"></path>
                        </svg>
                        <h3 class="empty-state-title">No Booking Requests Yet</h3>
                        <p class="empty-state-text">
                            <?php if (!$profileComplete): ?>
                                Complete your business profile and add services to start receiving booking requests from clients
                            <?php else: ?>
                                You don't have any booking requests yet. Your clients will see your profile once you add services!
                            <?php endif; ?>
                        </p>
                        <a href="<?php echo SITE_URL; ?>/photographer/edit_profile.php" class="btn btn-primary">Complete Your Profile</a>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script src="<?php echo SITE_URL; ?>/js/sweetalert.js"></script>
</body>
</html>
