<?php
/**
 * Landing Page
 * index.php
 */
require_once __DIR__ . '/settings/core.php';

$pageTitle = 'PhotoMarket - Premium Photography Services & Equipment';
$cssPath = '/css/style.css';
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
            <a href="#services" class="btn btn-lg btn-primary mt-lg">Explore Services</a>
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
                <h2>Featured Photographers</h2>
            </div>

            <div class="photographer-grid">
                <div class="photographer-card">
                    <div class="photographer-image"></div>
                    <div class="photographer-info">
                        <h4>Ama Mensah</h4>
                        <p class="photographer-specialty">Wedding & Portrait</p>
                        <div class="photographer-rating">
                            <span class="rating-stars">★★★★★</span>
                            <span class="rating-count">(125)</span>
                        </div>
                        <a href="#" class="btn btn-sm btn-primary">View Portfolio</a>
                    </div>
                </div>

                <div class="photographer-card">
                    <div class="photographer-image"></div>
                    <div class="photographer-info">
                        <h4>Kwesi Osei</h4>
                        <p class="photographer-specialty">Event & Commercial</p>
                        <div class="photographer-rating">
                            <span class="rating-stars">★★★★★</span>
                            <span class="rating-count">(89)</span>
                        </div>
                        <a href="#" class="btn btn-sm btn-primary">View Portfolio</a>
                    </div>
                </div>

                <div class="photographer-card">
                    <div class="photographer-image"></div>
                    <div class="photographer-info">
                        <h4>Abena Boakye</h4>
                        <p class="photographer-specialty">Creative Director</p>
                        <div class="photographer-rating">
                            <span class="rating-stars">★★★★★</span>
                            <span class="rating-count">(156)</span>
                        </div>
                        <a href="#" class="btn btn-sm btn-primary">View Portfolio</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="section-spacing cta-section" id="services">
        <div class="container text-center">
            <h2>Ready to Create Something Beautiful?</h2>
            <p>Join thousands of clients who've found their perfect photographer or equipment partner.</p>
            <a href="/auth/signup" class="btn btn-lg btn-primary">Get Started</a>
        </div>
    </section>

</main>

<?php include 'views/footer.php'; ?>
