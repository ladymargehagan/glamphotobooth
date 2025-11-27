<?php
/**
 * Landing Page
 * index.php
 */
require_once __DIR__ . '/settings/core.php';

// Load provider and product classes for featured photographers
if (!class_exists('provider_class')) {
    require_once __DIR__ . '/classes/provider_class.php';
}
if (!class_exists('product_class')) {
    require_once __DIR__ . '/classes/product_class.php';
}

$pageTitle = 'PhotoMarket - Premium Photography Services & Equipment';
$cssPath = SITE_URL . '/css/style.css';

// Fetch top-rated photographers/vendors to feature on the homepage
$featuredProviders = [];
try {
    $db = new db_connection();
    if ($db->db_connect()) {
        // Providers with the best ratings and most reviews (with customer validation)
        $sql = "SELECT sp.provider_id, sp.business_name, sp.description, sp.rating, sp.total_reviews,
                       c.name, c.city, c.country
                FROM pb_service_providers sp
                INNER JOIN pb_customer c ON sp.customer_id = c.id
                WHERE sp.rating IS NOT NULL AND c.id IS NOT NULL
                ORDER BY sp.rating DESC, sp.total_reviews DESC, sp.created_at DESC
                LIMIT 3";
        $featuredProviders = $db->db_fetch_all($sql);
        if (!$featuredProviders) {
            $featuredProviders = [];
        }
    }
} catch (Exception $e) {
    // Fail silently – homepage should still render even if this query fails
    error_log('Featured providers query error: ' . $e->getMessage());
    $featuredProviders = [];
}
?>
<?php include 'views/header.php'; ?>

<main>
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content container">
            <h1 class="hero-title">
                Capture Your Moments
                <span class="hero-accent">with Grace</span>
            </h1>
            <p class="hero-subtitle">
                Ghana's premier photography marketplace connecting you with creative professionals.
            </p>
            <a href="<?php echo SITE_URL; ?>/auth/register.php" class="btn btn-lg btn-primary mt-lg">Get Started</a>
        </div>
    </section>

    <!-- What We Offer -->
    <section class="section-spacing services-section">
        <div class="container">
            <div class="section-header">
                <h2>What We Offer</h2>
            </div>

            <div class="grid grid-3 gap-lg">
                <div class="service-card">
                    <div class="service-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                            <circle cx="12" cy="13" r="4"></circle>
                        </svg>
                    </div>
                    <h3>Photography Services</h3>
                    <p>Professional photographers for weddings, portraits, events, and commercial projects.</p>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"></path>
                            <path d="M12 5v7l5 3"></path>
                        </svg>
                    </div>
                    <h3>Equipment Rentals</h3>
                    <p>Premium photobooths, lighting, cameras, and production gear for daily or weekly rental.</p>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <rect x="3" y="3" width="18" height="18" rx="2"></rect>
                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                            <polyline points="21 15 16 10 5 21"></polyline>
                        </svg>
                    </div>
                    <h3>Print Services</h3>
                    <p>Canvas prints, frames, albums, and custom photo books of exceptional quality.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission & Vision -->
    <section class="section-spacing mission-section">
        <div class="container">
            <div class="mission-grid">
                <div class="mission-item">
                    <h3>Our Mission</h3>
                    <p>To connect Ghana's most talented creative professionals with clients who value excellence, making premium photography services and equipment accessible to everyone.</p>
                </div>
                <div class="mission-divider"></div>
                <div class="mission-item">
                    <h3>Our Vision</h3>
                    <p>To be the trusted marketplace where every moment is captured beautifully and every creative project exceeds expectations.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Photographers -->
    <section class="section-spacing photographers-section">
        <div class="container">
            <div class="section-header">
                <h2>Featured Photographers/Vendors</h2>
            </div>

            <div class="photographer-grid">
                <?php if ($featuredProviders && count($featuredProviders) > 0): ?>
                    <?php foreach ($featuredProviders as $provider): ?>
                        <div class="photographer-card">
                            <div class="photographer-image"></div>
                            <div class="photographer-info">
                                <h4><?php echo htmlspecialchars($provider['business_name']); ?></h4>
                                <p class="photographer-specialty">
                                    <?php echo htmlspecialchars($provider['city'] ?? ''); ?>
                                    <?php if (!empty($provider['country'])): ?>
                                        , <?php echo htmlspecialchars($provider['country']); ?>
                                    <?php endif; ?>
                                </p>
                                <div class="photographer-rating">
                                    <?php if (!empty($provider['rating']) && $provider['rating'] > 0): ?>
                                        <span class="rating-stars">⭐ <?php echo number_format($provider['rating'], 1); ?></span>
                                        <span class="rating-count">(<?php echo intval($provider['total_reviews']); ?> reviews)</span>
                                    <?php else: ?>
                                        <span class="rating-stars">New provider</span>
                                    <?php endif; ?>
                                </div>
                                <a href="<?php echo SITE_URL; ?>/provider/profile.php?id=<?php echo intval($provider['provider_id']); ?>"
                                   class="btn btn-sm btn-primary">
                                    View Profile
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: var(--text-secondary); text-align: center;">
                        Featured photographers and vendors will appear here once providers start receiving reviews.
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="section-spacing cta-section" id="services">
        <div class="container text-center">
            <h2>Ready to Create Something Beautiful?</h2>
            <p>Join thousands of clients who've found their perfect photographer or equipment partner.</p>
            <a href="<?php echo SITE_URL; ?>/auth/register.php" class="btn btn-lg btn-primary">Get Started</a>
        </div>
    </section>

</main>

<?php include 'views/footer.php'; ?>
