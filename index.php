<?php
/**
 * Landing Page
 * index.php
 */
require_once __DIR__ . '/settings/core.php';

$pageTitle = 'GlamPhotobooth Accra - Premium Photography Services & Equipment';
$cssPath = SITE_URL . '/css/style.css';
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
                    <h3>Equipment & Accessories</h3>
                    <p>Premium photobooths, lighting, cameras, and production gear for professional photographers.</p>
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

    <!-- Gallery Showcase -->
    <section class="section-spacing gallery-section">
        <div class="container">
            <div class="section-header">
                <h2>Our Work</h2>
                <p style="color: var(--text-secondary); font-size: 1.05rem; max-width: 600px; margin: var(--spacing-md) auto 0;">
                    Explore stunning photography and equipment showcases
                </p>
            </div>
            <div class="gallery-grid">
                <div class="gallery-item">
                    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 400'%3E%3Crect fill='%23102152' width='400' height='400'/%3E%3C/svg%3E" alt="Gallery Image 1">
                </div>
                <div class="gallery-item">
                    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 400'%3E%3Crect fill='%230d1838' width='400' height='400'/%3E%3C/svg%3E" alt="Gallery Image 2">
                </div>
                <div class="gallery-item">
                    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 400'%3E%3Crect fill='%23E2C492' width='400' height='400'/%3E%3C/svg%3E" alt="Gallery Image 3">
                </div>
                <div class="gallery-item">
                    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 400'%3E%3Crect fill='%23D9CEC3' width='400' height='400'/%3E%3C/svg%3E" alt="Gallery Image 4">
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
