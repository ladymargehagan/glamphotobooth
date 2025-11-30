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
        <?php require_once __DIR__ . '/../views/dashboard_sidebar.php'; ?>

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
