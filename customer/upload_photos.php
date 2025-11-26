<?php
/**
 * Upload Photos Page
 * customer/upload_photos.php
 * Upload photos to gallery for completed bookings
 */
require_once __DIR__ . '/../settings/core.php';

requireLogin();

$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$booking_id = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : 0;

if ($booking_id <= 0) {
    header('Location: ' . SITE_URL . '/customer/manage_bookings.php');
    exit;
}

// Get booking details
$booking_class = new booking_class();
$booking = $booking_class->get_booking_by_id($booking_id);

if (!$booking) {
    header('Location: ' . SITE_URL . '/customer/manage_bookings.php');
    exit;
}

// Check if user is the provider
$provider_class = new provider_class();
$provider = $provider_class->get_provider_by_customer_id($user_id);

if (!$provider || intval($provider['provider_id']) !== intval($booking['provider_id'])) {
    header('Location: ' . SITE_URL . '/customer/manage_bookings.php');
    exit;
}

// Check if booking is completed
if ($booking['status'] !== 'completed') {
    header('Location: ' . SITE_URL . '/customer/manage_bookings.php');
    exit;
}

// Get or create gallery
$gallery_class = new gallery_class();
$gallery = $gallery_class->get_gallery_by_booking($booking_id);

if (!$gallery) {
    // Create gallery
    $gallery_id = $gallery_class->create_gallery($booking_id, $provider['provider_id']);
    if (!$gallery_id) {
        header('Location: ' . SITE_URL . '/customer/manage_bookings.php');
        exit;
    }
    $gallery = $gallery_class->get_gallery_by_id($gallery_id);
}

// Get photos already uploaded
$photos = $gallery_class->get_gallery_photos($gallery['gallery_id']);

$pageTitle = 'Upload Photos - PhotoMarket';
$cssPath = SITE_URL . '/css/style.css';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($cssPath); ?>">
    <style>
        .upload-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: var(--spacing-xxl) var(--spacing-xl);
        }

        .upload-header {
            margin-bottom: var(--spacing-xxl);
        }

        .upload-header h1 {
            color: var(--primary);
            font-family: var(--font-serif);
            font-size: 2rem;
            margin-bottom: var(--spacing-sm);
        }

        .booking-info {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: var(--spacing-lg);
            margin-bottom: var(--spacing-lg);
            border-left: 4px solid var(--primary);
        }

        .booking-info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: var(--spacing-md);
            padding-bottom: var(--spacing-md);
            border-bottom: 1px solid var(--border-color);
        }

        .booking-info-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .booking-info-label {
            color: var(--text-secondary);
            font-weight: 500;
        }

        .booking-info-value {
            color: var(--text-primary);
            font-weight: 600;
        }

        .upload-section {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: var(--spacing-lg);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            margin-bottom: var(--spacing-lg);
        }

        .upload-section h2 {
            color: var(--primary);
            font-size: 1.1rem;
            margin-bottom: var(--spacing-lg);
            font-weight: 600;
        }

        .drop-zone {
            border: 2px dashed var(--primary);
            border-radius: var(--border-radius);
            padding: var(--spacing-xxl);
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
            background: rgba(226, 196, 146, 0.05);
        }

        .drop-zone:hover {
            background: rgba(226, 196, 146, 0.1);
            border-color: #0d1a3a;
        }

        .drop-zone.dragover {
            background: rgba(226, 196, 146, 0.2);
            border-color: #0d1a3a;
        }

        .drop-zone-icon {
            font-size: 3rem;
            margin-bottom: var(--spacing-md);
        }

        .drop-zone-text {
            color: var(--primary);
            font-weight: 600;
            margin-bottom: var(--spacing-sm);
        }

        .drop-zone-subtext {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        #photoInput {
            display: none;
        }

        .upload-progress {
            margin-top: var(--spacing-lg);
        }

        .progress-item {
            background: var(--light-gray);
            border-radius: var(--border-radius);
            padding: var(--spacing-md);
            margin-bottom: var(--spacing-md);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .progress-bar {
            flex: 1;
            height: 6px;
            background: #e0e0e0;
            border-radius: 3px;
            overflow: hidden;
            margin: 0 var(--spacing-md);
        }

        .progress-bar-fill {
            height: 100%;
            background: var(--primary);
            width: 0%;
            transition: width 0.3s;
        }

        .photos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: var(--spacing-lg);
            margin-top: var(--spacing-lg);
        }

        .photo-item {
            position: relative;
            aspect-ratio: 1;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .photo-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .photo-item-delete {
            position: absolute;
            top: var(--spacing-sm);
            right: var(--spacing-sm);
            background: rgba(244, 67, 54, 0.9);
            color: var(--white);
            border: none;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            opacity: 0;
            transition: var(--transition);
        }

        .photo-item:hover .photo-item-delete {
            opacity: 1;
        }

        .btn-upload {
            width: 100%;
            padding: 1rem;
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.95rem;
            margin-top: var(--spacing-lg);
        }

        .btn-upload:hover {
            background: #0d1a3a;
            transform: translateY(-2px);
        }

        .btn-upload:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }

        .message {
            padding: var(--spacing-md);
            border-radius: var(--border-radius);
            margin-bottom: var(--spacing-lg);
            display: none;
        }

        .message.show {
            display: block;
        }

        .message.error {
            background: rgba(211, 47, 47, 0.1);
            color: #c62828;
            border: 1px solid rgba(211, 47, 47, 0.3);
        }

        .message.success {
            background: rgba(76, 175, 80, 0.1);
            color: #2e7d32;
            border: 1px solid rgba(76, 175, 80, 0.3);
        }

        .gallery-link {
            margin-top: var(--spacing-lg);
            text-align: center;
        }

        .btn-view-gallery {
            padding: 0.75rem 1.5rem;
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-block;
        }

        .btn-view-gallery:hover {
            background: #0d1a3a;
            transform: translateY(-2px);
        }

        .btn-back {
            display: inline-block;
            padding: 0.5rem 1rem;
            color: var(--primary);
            text-decoration: none;
            margin-bottom: var(--spacing-lg);
            font-weight: 500;
        }

        .btn-back:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .upload-container {
                padding: var(--spacing-lg);
            }

            .upload-header h1 {
                font-size: 1.5rem;
            }

            .photos-grid {
                grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            }
        }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../views/header.php'; ?>

    <div class="upload-container">
        <a href="<?php echo SITE_URL; ?>/customer/manage_bookings.php" class="btn-back">← Back to Bookings</a>

        <div class="upload-header">
            <h1>Upload Photos</h1>
            <p style="color: var(--text-secondary);">Share your photos from this booking with your customer</p>
        </div>

        <!-- Booking Information -->
        <div class="booking-info">
            <div class="booking-info-row">
                <span class="booking-info-label">Customer</span>
                <span class="booking-info-value"><?php echo htmlspecialchars($booking['customer_name'] ?? 'Customer'); ?></span>
            </div>
            <div class="booking-info-row">
                <span class="booking-info-label">Booking Date</span>
                <span class="booking-info-value"><?php echo date('M d, Y', strtotime($booking['booking_date'])); ?></span>
            </div>
            <div class="booking-info-row">
                <span class="booking-info-label">Service</span>
                <span class="booking-info-value"><?php echo htmlspecialchars(substr($booking['service_description'], 0, 50)); ?></span>
            </div>
            <div class="booking-info-row">
                <span class="booking-info-label">Access Code</span>
                <span class="booking-info-value" style="font-family: monospace;"><?php echo htmlspecialchars($gallery['access_code']); ?></span>
            </div>
        </div>

        <!-- Upload Section -->
        <div class="upload-section">
            <h2>📸 Add Photos</h2>

            <div id="errorMessage" class="message error"></div>
            <div id="successMessage" class="message success"></div>

            <div class="drop-zone" id="dropZone">
                <div class="drop-zone-icon">📁</div>
                <div class="drop-zone-text">Drag photos here or click to select</div>
                <div class="drop-zone-subtext">Supported: JPG, PNG, GIF, WebP (Max 5MB each)</div>
            </div>

            <input type="file" id="photoInput" accept="image/*" multiple>

            <button class="btn-upload" id="uploadBtn" disabled>Upload Selected Photos</button>

            <div id="uploadProgress" class="upload-progress"></div>
        </div>

        <!-- Uploaded Photos -->
        <?php if ($photos && count($photos) > 0): ?>
            <div class="upload-section">
                <h2>✓ Uploaded Photos (<?php echo count($photos); ?>)</h2>
                <div class="photos-grid">
                    <?php foreach ($photos as $photo): ?>
                        <div class="photo-item">
                            <img src="<?php echo SITE_URL; ?>/uploads/<?php echo htmlspecialchars($photo['file_path']); ?>" alt="Photo">
                            <button class="photo-item-delete" onclick="deletePhoto(<?php echo $photo['photo_id']; ?>)" title="Delete photo">×</button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="gallery-link">
                <p style="color: var(--text-secondary); margin-bottom: var(--spacing-md);">Share this link with your customer to view the gallery:</p>
                <input type="text" readonly value="<?php echo SITE_URL; ?>/customer/view_gallery.php?id=<?php echo $gallery['gallery_id']; ?>&code=<?php echo $gallery['access_code']; ?>"
                       style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--border-radius); font-size: 0.85rem; font-family: monospace; margin-bottom: var(--spacing-md);">
                <br>
                <a href="<?php echo SITE_URL; ?>/customer/view_gallery.php?id=<?php echo $gallery['gallery_id']; ?>&code=<?php echo $gallery['access_code']; ?>"
                   class="btn-view-gallery" target="_blank">View Gallery</a>
            </div>
        <?php endif; ?>
    </div>

    <?php require_once __DIR__ . '/../views/footer.php'; ?>

    <script src="<?php echo SITE_URL; ?>/js/gallery.js"></script>
    <script>
        window.siteUrl = '<?php echo SITE_URL; ?>';
        window.galleryId = <?php echo $gallery['gallery_id']; ?>;
        window.csrfToken = '<?php echo generateCSRFToken(); ?>';
    </script>
</body>
</html>
