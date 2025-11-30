<?php
/**
 * Photographer Booking Details
 * photographer/booking_details.php
 */
require_once __DIR__ . '/../settings/core.php';

// Check if logged in
requireLogin();

// Check if user is photographer (role 2)
if ($_SESSION['user_role'] != 2) {
    header('Location: ' . SITE_URL . '/index.php');
    exit;
}

// Get booking ID
$booking_id = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : 0;

if (!$booking_id) {
    header('Location: ' . SITE_URL . '/photographer/dashboard.php');
    exit;
}

// Get provider info
$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$provider_class = new provider_class();
$provider = $provider_class->get_provider_by_customer($user_id);

if (!$provider) {
    header('Location: ' . SITE_URL . '/photographer/dashboard.php');
    exit;
}

// Get booking details
$booking_class = new booking_class();
$booking = $booking_class->get_booking_by_id($booking_id);

// Verify this booking belongs to this photographer
if (!$booking || intval($booking['provider_id']) !== intval($provider['provider_id'])) {
    header('Location: ' . SITE_URL . '/photographer/dashboard.php');
    exit;
}

// Get customer reviews for this photographer
$reviews = [];
if (class_exists('review_class')) {
    try {
        $review_class = new review_class();
        $reviews = $review_class->get_reviews_by_provider($provider['provider_id']);
        if (!is_array($reviews)) {
            $reviews = [];
        }
    } catch (Exception $e) {
        error_log('Error fetching reviews: ' . $e->getMessage());
    }
}

// Check if this specific booking has a review
$booking_review = null;
if (class_exists('review_class')) {
    try {
        $review_class = new review_class();
        $booking_review = $review_class->get_review_by_booking($booking_id);
    } catch (Exception $e) {
        error_log('Error fetching booking review: ' . $e->getMessage());
    }
}

// Check if gallery exists for this booking
$gallery = null;
if (class_exists('gallery_class')) {
    try {
        $gallery_class = new gallery_class();
        $gallery = $gallery_class->get_gallery_by_booking($booking_id);
    } catch (Exception $e) {
        error_log('Error fetching gallery: ' . $e->getMessage());
    }
}

$pageTitle = 'Booking Details - GlamPhotobooth Accra';
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
        <?php require_once __DIR__ . '/../views/dashboard_sidebar.php'; ?>

        <main class="dashboard-content">
            <div class="dashboard-header">
                <div>
                    <h1 class="dashboard-title">Booking Details</h1>
                    <p class="dashboard-subtitle">Booking #<?php echo $booking_id; ?></p>
                </div>
                <div class="dashboard-actions">
                    <a href="<?php echo SITE_URL; ?>/photographer/dashboard.php" class="btn btn-outline">← Back to Dashboard</a>
                </div>
            </div>

            <div style="display: grid; gap: var(--spacing-lg);">
                <!-- Booking Information -->
                <div class="dashboard-card">
                    <h2 style="color: var(--primary); margin: 0 0 var(--spacing-lg) 0;">Booking Information</h2>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--spacing-lg);">
                        <div>
                            <p style="color: var(--text-secondary); font-size: 0.85rem; margin: 0 0 4px 0;">Status</p>
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

                        <div>
                            <p style="color: var(--text-secondary); font-size: 0.85rem; margin: 0 0 4px 0;">Customer Name</p>
                            <p style="color: var(--text-primary); font-weight: 600; margin: 0;">
                                <?php echo htmlspecialchars($booking['customer_name'] ?? 'N/A'); ?>
                            </p>
                        </div>

                        <div>
                            <p style="color: var(--text-secondary); font-size: 0.85rem; margin: 0 0 4px 0;">Email</p>
                            <p style="color: var(--text-primary); font-weight: 600; margin: 0;">
                                <?php echo htmlspecialchars($booking['email'] ?? 'N/A'); ?>
                            </p>
                        </div>

                        <div>
                            <p style="color: var(--text-secondary); font-size: 0.85rem; margin: 0 0 4px 0;">Phone</p>
                            <p style="color: var(--text-primary); font-weight: 600; margin: 0;">
                                <?php echo htmlspecialchars($booking['contact'] ?? 'N/A'); ?>
                            </p>
                        </div>

                        <div>
                            <p style="color: var(--text-secondary); font-size: 0.85rem; margin: 0 0 4px 0;">Booking Date</p>
                            <p style="color: var(--text-primary); font-weight: 600; margin: 0;">
                                <?php echo date('M d, Y', strtotime($booking['booking_date'])); ?>
                            </p>
                        </div>

                        <div>
                            <p style="color: var(--text-secondary); font-size: 0.85rem; margin: 0 0 4px 0;">Booking Time</p>
                            <p style="color: var(--text-primary); font-weight: 600; margin: 0;">
                                <?php echo date('g:i A', strtotime($booking['booking_time'])); ?>
                            </p>
                        </div>
                    </div>

                    <div style="margin-top: var(--spacing-lg); padding: var(--spacing-md); background: rgba(226, 196, 146, 0.05); border-radius: var(--border-radius);">
                        <p style="color: var(--text-secondary); font-size: 0.85rem; margin: 0 0 4px 0; font-weight: 600;">Service Description</p>
                        <p style="color: var(--text-primary); margin: 0; line-height: 1.6;">
                            <?php echo nl2br(htmlspecialchars($booking['service_description'] ?? 'N/A')); ?>
                        </p>
                    </div>

                    <div style="margin-top: var(--spacing-lg); display: flex; gap: var(--spacing-sm); flex-wrap: wrap;">
                        <?php if ($booking['status'] === 'confirmed' || $booking['status'] === 'completed'): ?>
                            <a href="<?php echo SITE_URL; ?>/photographer/upload_photos.php?booking_id=<?php echo $booking_id; ?>" class="btn btn-primary">
                                <?php echo $gallery ? 'Add More Photos' : 'Upload Photos'; ?>
                            </a>
                        <?php endif; ?>

                        <?php if ($gallery): ?>
                            <a href="<?php echo SITE_URL; ?>/photographer/galleries.php?gallery_id=<?php echo $gallery['gallery_id']; ?>" class="btn btn-outline">
                                View Gallery (<?php echo isset($gallery['photo_count']) ? $gallery['photo_count'] : 0; ?> photos)
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Review for this booking -->
                <?php if ($booking_review): ?>
                <div class="dashboard-card">
                    <h2 style="color: var(--primary); margin: 0 0 var(--spacing-lg) 0;">Customer Review</h2>

                    <div style="padding: var(--spacing-md); background: rgba(226, 196, 146, 0.05); border-radius: var(--border-radius);">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: var(--spacing-sm);">
                            <div>
                                <p style="font-weight: 600; color: var(--primary); margin: 0;">
                                    <?php echo htmlspecialchars($booking['customer_name'] ?? 'Customer'); ?>
                                </p>
                                <p style="font-size: 0.85rem; color: var(--text-secondary); margin: 4px 0 0 0;">
                                    <?php echo date('M d, Y', strtotime($booking_review['review_date'] ?? $booking_review['created_at'] ?? 'now')); ?>
                                </p>
                            </div>
                            <div style="color: #ffc107; font-size: 1.1rem;">
                                <?php
                                $rating = intval($booking_review['rating'] ?? 5);
                                echo str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);
                                ?>
                            </div>
                        </div>
                        <p style="color: var(--text-primary); line-height: 1.6; margin: var(--spacing-sm) 0 0 0;">
                            <?php echo nl2br(htmlspecialchars($booking_review['comment'] ?? '')); ?>
                        </p>
                    </div>
                </div>
                <?php endif; ?>

                <!-- All Reviews -->
                <?php if (!empty($reviews)): ?>
                <div class="dashboard-card">
                    <h2 style="color: var(--primary); margin: 0 0 var(--spacing-lg) 0;">All Customer Reviews (<?php echo count($reviews); ?>)</h2>

                    <div style="display: grid; gap: var(--spacing-md);">
                        <?php foreach ($reviews as $review): ?>
                        <div style="padding: var(--spacing-md); background: rgba(226, 196, 146, 0.05); border-radius: var(--border-radius);">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: var(--spacing-sm);">
                                <div>
                                    <p style="font-weight: 600; color: var(--primary); margin: 0;">
                                        <?php echo htmlspecialchars($review['customer_name'] ?? 'Customer'); ?>
                                    </p>
                                    <p style="font-size: 0.85rem; color: var(--text-secondary); margin: 4px 0 0 0;">
                                        <?php echo date('M d, Y', strtotime($review['review_date'] ?? $review['created_at'] ?? 'now')); ?>
                                    </p>
                                </div>
                                <div style="color: #ffc107; font-size: 1.1rem;">
                                    <?php
                                    $rating = intval($review['rating'] ?? 5);
                                    echo str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);
                                    ?>
                                </div>
                            </div>
                            <p style="color: var(--text-primary); line-height: 1.6; margin: var(--spacing-sm) 0 0 0;">
                                <?php echo nl2br(htmlspecialchars($review['comment'] ?? '')); ?>
                            </p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
