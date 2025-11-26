<?php
/**
 * View Gallery Page
 * customer/view_gallery.php
 * Public gallery view with lightbox for photos
 */
require_once __DIR__ . '/../settings/core.php';

$gallery_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$access_code = isset($_GET['code']) ? $_GET['code'] : '';

if ($gallery_id <= 0 || empty($access_code)) {
    http_response_code(404);
    $pageTitle = 'Gallery Not Found - PhotoMarket';
} else {
    // Validate access code
    $gallery_class = new gallery_class();
    $gallery = $gallery_class->get_gallery_by_access_code($access_code);

    if (!$gallery || intval($gallery['gallery_id']) !== intval($gallery_id)) {
        http_response_code(403);
        $pageTitle = 'Access Denied - PhotoMarket';
    } else {
        $pageTitle = htmlspecialchars($gallery['title'] ?: 'Gallery') . ' - PhotoMarket';
        $photos = $gallery_class->get_gallery_photos($gallery_id);
    }
}

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
    <!-- Global variables for scripts -->
    <script>
        window.isLoggedIn = <?php echo isLoggedIn() ? 'true' : 'false'; ?>;
        window.loginUrl = '<?php echo SITE_URL; ?>/auth/login.php';
        window.siteUrl = '<?php echo SITE_URL; ?>';
    </script>
    <script src="<?php echo SITE_URL; ?>/js/cart.js"></script>
    <style>
        body {
            background: #f5f5f5;
        }

        .gallery-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: var(--spacing-xxl) var(--spacing-xl);
        }

        .gallery-header {
            text-align: center;
            margin-bottom: var(--spacing-xxl);
        }

        .gallery-header h1 {
            color: var(--primary);
            font-family: var(--font-serif);
            font-size: 2.5rem;
            margin-bottom: var(--spacing-sm);
        }

        .gallery-info {
            display: flex;
            justify-content: center;
            gap: var(--spacing-lg);
            color: var(--text-secondary);
            flex-wrap: wrap;
        }

        .gallery-info-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .photos-masonry {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: var(--spacing-lg);
            margin-top: var(--spacing-xxl);
        }

        .photo-card {
            position: relative;
            aspect-ratio: 1;
            border-radius: var(--border-radius);
            overflow: hidden;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: var(--transition);
        }

        .photo-card:hover {
            transform: scale(1.03);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        }

        .photo-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .photo-card::after {
            content: '🔍';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 2rem;
            opacity: 0;
            transition: opacity 0.3s;
            background: rgba(0, 0, 0, 0.6);
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .photo-card:hover::after {
            opacity: 1;
        }

        /* Lightbox */
        .lightbox {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.95);
            z-index: 1000;
            padding: var(--spacing-lg);
        }

        .lightbox.active {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .lightbox-image {
            max-width: 90%;
            max-height: 85vh;
            object-fit: contain;
            animation: zoomIn 0.3s;
        }

        @keyframes zoomIn {
            from {
                transform: scale(0.8);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .lightbox-controls {
            position: absolute;
            top: var(--spacing-lg);
            right: var(--spacing-lg);
            display: flex;
            gap: var(--spacing-sm);
        }

        .lightbox-btn {
            background: rgba(255, 255, 255, 0.2);
            color: var(--white);
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            cursor: pointer;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.3s;
        }

        .lightbox-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .lightbox-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.2);
            color: var(--white);
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            cursor: pointer;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.3s;
            z-index: 1001;
        }

        .lightbox-nav:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .lightbox-nav.prev {
            left: var(--spacing-lg);
        }

        .lightbox-nav.next {
            right: var(--spacing-lg);
        }

        .lightbox-counter {
            position: absolute;
            bottom: var(--spacing-lg);
            left: 50%;
            transform: translateX(-50%);
            color: var(--white);
            background: rgba(0, 0, 0, 0.5);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
        }

        .empty-state {
            text-align: center;
            padding: var(--spacing-xxl);
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .empty-state-icon {
            font-size: 3rem;
            margin-bottom: var(--spacing-lg);
        }

        .empty-state-title {
            color: var(--primary);
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: var(--spacing-sm);
        }

        .empty-state-text {
            color: var(--text-secondary);
        }

        .error-message {
            text-align: center;
            padding: var(--spacing-xxl);
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .error-icon {
            font-size: 3rem;
            margin-bottom: var(--spacing-lg);
        }

        .error-title {
            color: #c62828;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: var(--spacing-sm);
        }

        .error-text {
            color: var(--text-secondary);
            margin-bottom: var(--spacing-lg);
        }

        @media (max-width: 768px) {
            .gallery-container {
                padding: var(--spacing-lg);
            }

            .gallery-header h1 {
                font-size: 1.5rem;
            }

            .photos-masonry {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
                gap: var(--spacing-md);
            }

            .lightbox-image {
                max-width: 95%;
                max-height: 80vh;
            }

            .lightbox-nav {
                width: 40px;
                height: 40px;
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Header -->
    <header class="navbar">
        <div class="container">
            <div class="flex-between">
                <div class="navbar-brand">
                    <a href="/" class="logo">
                        <h3 class="font-serif text-primary m-0">PhotoMarket</h3>
                    </a>
                </div>

                <nav class="navbar-menu">
                    <ul class="navbar-items">
                        <li><a href="<?php echo SITE_URL; ?>/index.php" class="nav-link">Home</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/shop.php" class="nav-link">Products & Services</a></li>
                    </ul>
                </nav>

                <div class="navbar-actions flex gap-sm">
                    <?php if (isLoggedIn()): ?>
                        <a href="<?php echo SITE_URL; ?>/customer/cart.php" class="cart-icon" title="Shopping Cart" style="position: relative; display: flex; align-items: center;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 24px; height: 24px;">
                                <circle cx="9" cy="21" r="1"></circle>
                                <circle cx="20" cy="21" r="1"></circle>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                            </svg>
                            <span id="cartBadge" class="cart-badge" style="display: none;"></span>
                        </a>
                        <a href="<?php echo getDashboardUrl(); ?>" class="btn btn-sm btn-outline">Dashboard</a>
                        <a href="<?php echo SITE_URL; ?>/actions/logout.php" class="btn btn-sm btn-primary">Logout</a>
                    <?php else: ?>
                        <a href="<?php echo SITE_URL; ?>/auth/login.php" class="btn btn-sm btn-outline">Login</a>
                        <a href="<?php echo SITE_URL; ?>/auth/register.php" class="btn btn-sm btn-primary">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <div class="gallery-container">
        <?php if (isset($gallery) && $gallery): ?>
            <!-- Gallery Header -->
            <div class="gallery-header">
                <h1><?php echo htmlspecialchars($gallery['title'] ?: 'Photo Gallery'); ?></h1>
                <div class="gallery-info">
                    <?php if (!empty($gallery['customer_name'])): ?>
                        <div class="gallery-info-item">
                            <span>👤</span>
                            <span><?php echo htmlspecialchars($gallery['customer_name']); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($gallery['booking_date'])): ?>
                        <div class="gallery-info-item">
                            <span>📅</span>
                            <span><?php echo date('M d, Y', strtotime($gallery['booking_date'])); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($gallery['photo_count'])): ?>
                        <div class="gallery-info-item">
                            <span>📸</span>
                            <span><?php echo intval($gallery['photo_count']); ?> photo<?php echo intval($gallery['photo_count']) !== 1 ? 's' : ''; ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($photos && count($photos) > 0): ?>
                <!-- Photos Grid -->
                <div class="photos-masonry">
                    <?php foreach ($photos as $index => $photo): ?>
                        <div class="photo-card" onclick="openLightbox(<?php echo $index; ?>)">
                            <img src="<?php echo SITE_URL; ?>/uploads/<?php echo htmlspecialchars($photo['file_path']); ?>"
                                 alt="Photo <?php echo $index + 1; ?>"
                                 loading="lazy">
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <!-- Empty State -->
                <div class="empty-state">
                    <div class="empty-state-icon">📸</div>
                    <h3 class="empty-state-title">No Photos Yet</h3>
                    <p class="empty-state-text">The photographer hasn't uploaded any photos for this gallery yet. Check back soon!</p>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <!-- Error State -->
            <div class="error-message">
                <div class="error-icon">🔒</div>
                <h3 class="error-title">Access Denied</h3>
                <p class="error-text">The gallery you're trying to access is not available or the access code is invalid.</p>
                <p style="color: var(--text-secondary); font-size: 0.9rem;">Please check your link and try again.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Lightbox -->
    <div class="lightbox" id="lightbox">
        <div class="lightbox-controls">
            <button class="lightbox-btn" onclick="closeLightbox()" title="Close">✕</button>
        </div>
        <img class="lightbox-image" id="lightboxImage" src="" alt="">
        <button class="lightbox-nav prev" onclick="prevPhoto()" style="display: none;" id="prevBtn">‹</button>
        <button class="lightbox-nav next" onclick="nextPhoto()" style="display: none;" id="nextBtn">›</button>
        <div class="lightbox-counter" id="photoCounter" style="display: none;"></div>
    </div>

    <?php require_once __DIR__ . '/../views/footer.php'; ?>

    <script>
        <?php if (isset($photos) && is_array($photos)): ?>
        const photos = <?php echo json_encode(array_map(function($p) {
            return SITE_URL . '/uploads/' . $p['file_path'];
        }, $photos)); ?>;
        <?php else: ?>
        const photos = [];
        <?php endif; ?>

        let currentIndex = 0;

        function openLightbox(index) {
            if (photos.length === 0) return;
            currentIndex = index;
            showPhoto();
        }

        function closeLightbox() {
            document.getElementById('lightbox').classList.remove('active');
        }

        function nextPhoto() {
            currentIndex = (currentIndex + 1) % photos.length;
            showPhoto();
        }

        function prevPhoto() {
            currentIndex = (currentIndex - 1 + photos.length) % photos.length;
            showPhoto();
        }

        function showPhoto() {
            const lightbox = document.getElementById('lightbox');
            const image = document.getElementById('lightboxImage');
            const counter = document.getElementById('photoCounter');
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');

            image.src = photos[currentIndex];
            counter.textContent = (currentIndex + 1) + ' / ' + photos.length;

            lightbox.classList.add('active');
            prevBtn.style.display = photos.length > 1 ? 'flex' : 'none';
            nextBtn.style.display = photos.length > 1 ? 'flex' : 'none';
            counter.style.display = photos.length > 1 ? 'block' : 'none';
        }

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (!document.getElementById('lightbox').classList.contains('active')) return;
            if (e.key === 'Escape') closeLightbox();
            if (e.key === 'ArrowRight') nextPhoto();
            if (e.key === 'ArrowLeft') prevPhoto();
        });

        // Close on background click
        document.getElementById('lightbox').addEventListener('click', function(e) {
            if (e.target === this) closeLightbox();
        });
    </script>
</body>
</html>
