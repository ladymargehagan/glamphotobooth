<?php
/**
 * Photographer Client Galleries
 * photographer/galleries.php
 * Manage photo galleries for completed bookings
 */
require_once __DIR__ . '/../settings/core.php';

requireLogin();

// Check if user is photographer (role 2)
if ($_SESSION['user_role'] != 2) {
    header('Location: ' . SITE_URL . '/index.php');
    exit;
}

$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$provider_class = new provider_class();
$provider = $provider_class->get_provider_by_customer($user_id);

if (!$provider) {
    header('Location: ' . SITE_URL . '/photographer/profile_setup.php');
    exit;
}

// Get all galleries for this provider
$gallery_class = new gallery_class();
$galleries = $gallery_class->get_provider_galleries($provider['provider_id']);

if (!is_array($galleries)) {
    $galleries = [];
}

$pageTitle = 'Client Galleries - GlamPhotobooth Accra';
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

        .gallery-info {
            flex: 1;
        }

        .gallery-info h3 {
            color: var(--primary);
            font-weight: 600;
            margin: 0 0 var(--spacing-xs) 0;
        }

        .gallery-info p {
            color: var(--text-secondary);
            margin: 0 0 4px 0;
            font-size: 0.9rem;
        }

        .gallery-actions {
            display: flex;
            gap: var(--spacing-sm);
            margin-top: var(--spacing-md);
        }

        .btn-action {
            padding: 0.6rem 1.2rem;
            border: none;
            border-radius: var(--border-radius);
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: var(--transition);
            font-size: 0.9rem;
        }

        .btn-view {
            background: var(--primary);
            color: var(--white);
        }

        .btn-view:hover {
            background: #0d1a3a;
        }

        .btn-upload {
            background: rgba(76, 175, 80, 0.15);
            color: #2e7d32;
        }

        .btn-upload:hover {
            background: rgba(76, 175, 80, 0.3);
        }

        .gallery-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: var(--spacing-md);
            margin-top: var(--spacing-md);
            padding-top: var(--spacing-md);
            border-top: 1px solid var(--border-color);
        }

        .stat {
            text-align: center;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary);
        }

        .stat-label {
            color: var(--text-secondary);
            font-size: 0.85rem;
        }

        .access-code {
            background: rgba(226, 196, 146, 0.1);
            padding: var(--spacing-sm) var(--spacing-md);
            border-radius: var(--border-radius);
            font-family: monospace;
            font-size: 0.85rem;
            color: var(--text-primary);
            word-break: break-all;
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
                    <h1 class="dashboard-title">Client Galleries</h1>
                    <p class="dashboard-subtitle">Manage photo galleries for completed bookings</p>
                </div>
            </div>

            <!-- Galleries List -->
            <?php if (count($galleries) > 0): ?>
                <div>
                    <?php foreach ($galleries as $gallery): ?>
                        <div class="gallery-card">
                            <div class="gallery-header">
                                <div class="gallery-info">
                                    <h3><?php echo htmlspecialchars($gallery['customer_name'] ?? 'Client'); ?></h3>
                                    <p><strong>Service:</strong> <?php echo htmlspecialchars($gallery['service_description'] ?? 'N/A'); ?></p>
                                    <p><strong>Booking Date:</strong> <?php echo date('M d, Y', strtotime($gallery['booking_date'])); ?></p>
                                    <p><strong>Gallery Link:</strong></p>
                                    <div class="access-code">
                                        <?php echo SITE_URL; ?>/customer/view_gallery.php?id=<?php echo $gallery['gallery_id']; ?>&code=<?php echo htmlspecialchars($gallery['access_code']); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="gallery-stats">
                                <div class="stat">
                                    <div class="stat-value"><?php echo intval($gallery['photo_count'] ?? 0); ?></div>
                                    <div class="stat-label">Photos</div>
                                </div>
                                <div class="stat">
                                    <div class="stat-value"><?php echo date('M d', strtotime($gallery['created_at'])); ?></div>
                                    <div class="stat-label">Created</div>
                                </div>
                            </div>

                            <div class="gallery-actions">
                                <a href="<?php echo SITE_URL; ?>/customer/view_gallery.php?id=<?php echo $gallery['gallery_id']; ?>&code=<?php echo htmlspecialchars($gallery['access_code']); ?>" class="btn-action btn-view" target="_blank">View Gallery</a>
                                <a href="<?php echo SITE_URL; ?>/photographer/upload_photos.php?booking_id=<?php echo $gallery['booking_id']; ?>" class="btn-action btn-upload">Upload/Edit Photos</a>
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
                    <h3 class="empty-state-title">No Galleries Found</h3>
                    <p class="empty-state-text">
                        You don't have any client galleries yet. Complete bookings and upload photos to create galleries.
                    </p>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <script src="<?php echo SITE_URL; ?>/js/sweetalert.js"></script>
</body>
</html>
