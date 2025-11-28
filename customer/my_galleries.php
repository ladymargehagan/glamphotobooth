<?php
/**
 * My Galleries Page
 * customer/my_galleries.php
 * Customer view of all their photo galleries from completed bookings
 */
require_once __DIR__ . '/../settings/core.php';

// Include required classes
if (!class_exists('booking_class')) {
    require_once __DIR__ . '/../classes/booking_class.php';
}
if (!class_exists('gallery_class')) {
    require_once __DIR__ . '/../classes/gallery_class.php';
}

requireLogin();

// Check if user is customer (role 4)
if ($_SESSION['user_role'] != 4) {
    header('Location: ' . SITE_URL . '/index.php');
    exit;
}

$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

// Get all completed bookings
$all_bookings = [];
try {
    $booking_class = new booking_class();
    $bookings = $booking_class->get_customer_bookings($user_id);
    if (is_array($bookings)) {
        // Filter only completed bookings
        foreach ($bookings as $booking) {
            if (isset($booking['status']) && $booking['status'] === 'completed') {
                $all_bookings[] = $booking;
            }
        }
    }
} catch (Exception $e) {
    error_log('Error fetching bookings: ' . $e->getMessage());
}

// Get galleries for these bookings
$galleries = [];
try {
    $gallery_class = new gallery_class();

    foreach ($all_bookings as $booking) {
        if (isset($booking['booking_id'])) {
            $gallery = $gallery_class->get_gallery_by_booking(intval($booking['booking_id']));
            if ($gallery) {
                // Add booking info to gallery
                $gallery['booking_date'] = $booking['booking_date'] ?? '';
                $gallery['service_description'] = $booking['service_description'] ?? '';
                $galleries[] = $gallery;
            }
        }
    }
} catch (Exception $e) {
    error_log('Error fetching galleries: ' . $e->getMessage());
}

$pageTitle = 'My Galleries - PhotoMarket';
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

    <style>
        .gallery-card {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: var(--spacing-lg);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            margin-bottom: var(--spacing-lg);
            border-left: 4px solid var(--primary);
        }

        .gallery-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: var(--spacing-md);
        }

        .gallery-info h3 {
            color: var(--primary);
            font-weight: 600;
            margin: 0 0 var(--spacing-xs) 0;
        }

        .gallery-info p {
            color: var(--text-secondary);
            margin: 0;
            font-size: 0.9rem;
        }

        .gallery-stats {
            display: flex;
            gap: var(--spacing-md);
            margin-bottom: var(--spacing-md);
        }

        .stat-item {
            display: flex;
            flex-direction: column;
        }

        .stat-label {
            color: var(--text-secondary);
            font-size: 0.85rem;
            margin-bottom: 4px;
            font-weight: 500;
        }

        .stat-value {
            color: var(--text-primary);
            font-weight: 600;
        }

        .gallery-actions {
            display: flex;
            gap: var(--spacing-sm);
            flex-wrap: wrap;
        }

        .btn-view-gallery {
            background: #1976d2;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .btn-view-gallery:hover {
            background: #0d47a1;
        }

        .empty-state {
            text-align: center;
            padding: var(--spacing-xxl);
            background: var(--white);
            border-radius: var(--border-radius);
        }

        .empty-state-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto var(--spacing-lg);
            color: var(--text-secondary);
        }
    </style>
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
                        <a href="<?php echo SITE_URL; ?>/customer/dashboard.php" class="sidebar-nav-link">
                            <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                <polyline points="9 22 9 12 15 12 15 22"></polyline>
                            </svg>
                            Dashboard
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
                        <a href="<?php echo SITE_URL; ?>/customer/my_bookings.php" class="sidebar-nav-link">
                            <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M6 9h12M6 9a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V9z"></path>
                            </svg>
                            My Bookings
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
                        <a href="<?php echo SITE_URL; ?>/customer/my_galleries.php" class="sidebar-nav-link active">
                            <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <rect x="3" y="3" width="18" height="18" rx="2"></rect>
                                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                <polyline points="21 15 16 10 5 21"></polyline>
                            </svg>
                            My Galleries
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
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

                <div class="sidebar-section">
                    <div class="sidebar-section-title">Account</div>
                    <ul class="sidebar-nav">
                        <li class="sidebar-nav-item">
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
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="dashboard-content">
            <div class="dashboard-header">
                <div>
                    <h1 class="dashboard-title">My Galleries</h1>
                    <p class="dashboard-subtitle">View your photo galleries from completed bookings</p>
                </div>
            </div>

            <?php if (count($galleries) > 0): ?>
                <div>
                    <?php foreach ($galleries as $gallery): ?>
                        <div class="gallery-card">
                            <div class="gallery-header">
                                <div class="gallery-info">
                                    <h3><?php echo htmlspecialchars($gallery['title'] ?: 'Photo Gallery'); ?></h3>
                                    <p><?php echo htmlspecialchars($gallery['business_name'] ?? 'Photographer'); ?></p>
                                </div>
                            </div>

                            <div class="gallery-stats">
                                <div class="stat-item">
                                    <span class="stat-label">Photos</span>
                                    <span class="stat-value"><?php echo intval($gallery['photo_count'] ?? 0); ?> photos</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-label">Date</span>
                                    <span class="stat-value"><?php echo date('M d, Y', strtotime($gallery['booking_date'] ?? 'now')); ?></span>
                                </div>
                            </div>

                            <?php if (!empty($gallery['service_description'])): ?>
                                <div style="background: rgba(226, 196, 146, 0.05); padding: var(--spacing-md); border-radius: var(--border-radius); margin-bottom: var(--spacing-md);">
                                    <p style="color: var(--text-secondary); font-size: 0.9rem; line-height: 1.5; margin: 0;">
                                        <strong>Service:</strong> <?php echo htmlspecialchars($gallery['service_description']); ?>
                                    </p>
                                </div>
                            <?php endif; ?>

                            <div class="gallery-actions">
                                <a href="<?php echo SITE_URL; ?>/customer/view_gallery.php?code=<?php echo htmlspecialchars($gallery['access_code']); ?>" class="btn-view-gallery">
                                    View Gallery
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <svg class="empty-state-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <rect x="3" y="3" width="18" height="18" rx="2"></rect>
                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                        <polyline points="21 15 16 10 5 21"></polyline>
                    </svg>
                    <h3 class="empty-state-title">No Galleries Yet</h3>
                    <p class="empty-state-text">Your photographers will upload photos after completing your bookings. Check back soon!</p>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
