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
    header('Location: ' . SITE_URL . '/customer/profile_setup.php');
    exit;
}

// Get all galleries for this provider
$gallery_class = new gallery_class();
$galleries = $gallery_class->get_provider_galleries($provider['provider_id']);

if (!is_array($galleries)) {
    $galleries = [];
}

$pageTitle = 'Client Galleries - PhotoMarket';
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
        <aside class="dashboard-sidebar">
            <div class="sidebar-header">
                <div class="sidebar-brand">PhotoMarket</div>
                <div class="sidebar-user">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></div>
            </div>

            <nav>
                <ul class="sidebar-nav">
                    <li class="sidebar-nav-item">
                        <a href="<?php echo SITE_URL; ?>/photographer/dashboard.php" class="sidebar-nav-link">
                            <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                <polyline points="9 22 9 12 15 12 15 22"></polyline>
                            </svg>
                            Dashboard
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
                        <a href="<?php echo SITE_URL; ?>/customer/manage_bookings.php" class="sidebar-nav-link">
                            <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M6 9h12M6 9a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V9z"></path>
                            </svg>
                            Booking Requests
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
                        <a href="<?php echo SITE_URL; ?>/customer/manage_products.php" class="sidebar-nav-link">
                            <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <rect x="3" y="3" width="18" height="18" rx="2"></rect>
                                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                <polyline points="21 15 16 10 5 21"></polyline>
                            </svg>
                            My Products
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
                        <a href="<?php echo SITE_URL; ?>/photographer/galleries.php" class="sidebar-nav-link active">
                            <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <rect x="3" y="3" width="18" height="18" rx="2"></rect>
                                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                <polyline points="21 15 16 10 5 21"></polyline>
                            </svg>
                            Client Galleries
                        </a>
                    </li>
                </ul>

                <div class="sidebar-section">
                    <div class="sidebar-section-title">Business</div>
                    <ul class="sidebar-nav">
                        <li class="sidebar-nav-item">
                            <a href="<?php echo SITE_URL; ?>/customer/edit_profile.php" class="sidebar-nav-link">
                                <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                Edit Business Profile
                            </a>
                        </li>
                        <li class="sidebar-nav-item">
                            <a href="<?php echo SITE_URL; ?>/customer/earnings.php" class="sidebar-nav-link">
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
                                <a href="<?php echo SITE_URL; ?>/customer/upload_photos.php?booking_id=<?php echo $gallery['booking_id']; ?>" class="btn-action btn-upload">Upload/Edit Photos</a>
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
