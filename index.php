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
        <div class="hero-background">
            <div class="hero-overlay"></div>
        </div>
        <div class="hero-content container">
            <div class="hero-text">
                <h1 class="hero-title animate-fade-in">
                    Capture Your Moments<br>
                    <span class="font-script text-secondary">with Grace</span>
                </h1>
                <p class="hero-subtitle text-muted animate-slide-up">
                    Connect with professional photographers, rent premium equipment, and bring your creative vision to life. Ghana's premier photography marketplace.
                </p>
                <div class="hero-cta flex gap-md mt-lg animate-slide-up">
                    <a href="#services" class="btn btn-lg btn-primary">Explore Services</a>
                    <a href="#how-it-works" class="btn btn-lg btn-outline">Learn More</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Overview Cards -->
    <section class="section-spacing services-overview">
        <div class="container">
            <div class="text-center mb-xl">
                <h2 class="mb-md">What We Offer</h2>
                <p class="text-muted max-width-600 mx-auto">
                    Everything you need for stunning photography and creative content creation
                </p>
            </div>

            <div class="grid grid-3 gap-lg" id="services">
                <!-- Photography Services Card -->
                <div class="card glass hover-lift">
                    <div class="service-icon mb-md">
                        <svg width="48" height="48" viewBox="0 0 48 48" fill="none">
                            <path d="M24 6C14.1 6 6 14.1 6 24s8.1 18 18 18 18-8.1 18-18S33.9 6 24 6zm0 32c-7.7 0-14-6.3-14-14s6.3-14 14-14 14 6.3 14 14-6.3 14-14 14z" fill="#102152"/>
                            <circle cx="24" cy="24" r="8" fill="#E2C492"/>
                        </svg>
                    </div>
                    <h4 class="mb-md">Photography Services</h4>
                    <p class="text-muted mb-md text-sm">
                        Professional photographers for weddings, portraits, events, and commercial projects. Book by the hour or package.
                    </p>
                    <ul class="service-list text-sm">
                        <li>💒 Wedding Photography</li>
                        <li>🎭 Portrait Sessions</li>
                        <li>🎉 Event Coverage</li>
                        <li>💼 Commercial Work</li>
                    </ul>
                </div>

                <!-- Equipment Rentals Card -->
                <div class="card glass hover-lift">
                    <div class="service-icon mb-md">
                        <svg width="48" height="48" viewBox="0 0 48 48" fill="none">
                            <rect x="8" y="12" width="32" height="24" rx="2" fill="none" stroke="#102152" stroke-width="2"/>
                            <path d="M14 20h20M14 28h20" stroke="#102152" stroke-width="2" stroke-linecap="round"/>
                            <circle cx="24" cy="24" r="4" fill="#E2C492"/>
                        </svg>
                    </div>
                    <h4 class="mb-md">Equipment Rentals</h4>
                    <p class="text-muted mb-md text-sm">
                        Rent professional photobooths, lighting rigs, cameras, and accessories. Daily and weekly rates available.
                    </p>
                    <ul class="service-list text-sm">
                        <li>📷 Photobooth Rentals</li>
                        <li>💡 Lighting Equipment</li>
                        <li>📹 Camera & Lenses</li>
                        <li>🎬 Production Gear</li>
                    </ul>
                </div>

                <!-- Print Services Card -->
                <div class="card glass hover-lift">
                    <div class="service-icon mb-md">
                        <svg width="48" height="48" viewBox="0 0 48 48" fill="none">
                            <rect x="6" y="8" width="36" height="28" rx="2" fill="none" stroke="#102152" stroke-width="2"/>
                            <path d="M10 14h28M10 22h28M10 30h28" stroke="#102152" stroke-width="1.5"/>
                            <rect x="8" y="38" width="32" height="2" fill="#E2C492"/>
                        </svg>
                    </div>
                    <h4 class="mb-md">Print Services</h4>
                    <p class="text-muted mb-md text-sm">
                        Premium prints on canvas, custom frames, photo books, and elegant albums. Premium quality guaranteed.
                    </p>
                    <ul class="service-list text-sm">
                        <li>🖼️ Canvas Prints</li>
                        <li>🎨 Custom Frames</li>
                        <li>📚 Photo Books</li>
                        <li>💎 Premium Albums</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Category Filter Section -->
    <section class="section-spacing bg-light">
        <div class="container">
            <div class="text-center mb-xl">
                <h2 class="mb-md">Browse by Category</h2>
            </div>

            <div class="flex flex-wrap justify-center gap-md" id="category-filters">
                <button class="category-btn active" data-category="all">All Services</button>
                <button class="category-btn" data-category="wedding">💒 Wedding</button>
                <button class="category-btn" data-category="portrait">👤 Portrait</button>
                <button class="category-btn" data-category="event">🎉 Event</button>
                <button class="category-btn" data-category="commercial">💼 Commercial</button>
                <button class="category-btn" data-category="photobooth">📷 Photobooth</button>
                <button class="category-btn" data-category="prints">🖼️ Prints</button>
            </div>

            <div class="grid grid-3 gap-lg mt-xl" id="category-results">
                <!-- Dynamically populated category results -->
                <div class="card hover-zoom">
                    <div class="card-image mb-md">
                        <div class="image-placeholder">Featured Work</div>
                    </div>
                    <h5 class="mb-md">Premium Wedding Photography</h5>
                    <p class="text-muted text-sm mb-md">Elegant and timeless wedding coverage by Ghana's top photographers.</p>
                    <p class="text-secondary text-bold">₵250 - ₵500/hour</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Photographers Carousel -->
    <section class="section-spacing">
        <div class="container">
            <div class="text-center mb-xl">
                <h2 class="mb-md">Featured Photographers</h2>
                <p class="text-muted max-width-600 mx-auto">
                    Meet our talented photographers bringing creative vision to life
                </p>
            </div>

            <div class="photographer-carousel">
                <div class="photographer-card">
                    <div class="photographer-image">
                        <div class="image-placeholder-large">Portfolio</div>
                    </div>
                    <div class="photographer-info p-lg">
                        <h5 class="mb-sm">Ama Mensah</h5>
                        <p class="text-secondary text-sm mb-md">Wedding & Portrait Specialist</p>
                        <div class="photographer-rating mb-md">
                            <span class="stars">⭐⭐⭐⭐⭐</span>
                            <span class="rating-value">(125 reviews)</span>
                        </div>
                        <a href="#" class="btn btn-sm btn-primary">View Portfolio</a>
                    </div>
                </div>

                <div class="photographer-card">
                    <div class="photographer-image">
                        <div class="image-placeholder-large">Portfolio</div>
                    </div>
                    <div class="photographer-info p-lg">
                        <h5 class="mb-sm">Kwesi Osei</h5>
                        <p class="text-secondary text-sm mb-md">Event & Commercial Expert</p>
                        <div class="photographer-rating mb-md">
                            <span class="stars">⭐⭐⭐⭐⭐</span>
                            <span class="rating-value">(89 reviews)</span>
                        </div>
                        <a href="#" class="btn btn-sm btn-primary">View Portfolio</a>
                    </div>
                </div>

                <div class="photographer-card">
                    <div class="photographer-image">
                        <div class="image-placeholder-large">Portfolio</div>
                    </div>
                    <div class="photographer-info p-lg">
                        <h5 class="mb-sm">Abena Boakye</h5>
                        <p class="text-secondary text-sm mb-md">Creative Director & Photographer</p>
                        <div class="photographer-rating mb-md">
                            <span class="stars">⭐⭐⭐⭐⭐</span>
                            <span class="rating-value">(156 reviews)</span>
                        </div>
                        <a href="#" class="btn btn-sm btn-primary">View Portfolio</a>
                    </div>
                </div>

                <div class="photographer-card">
                    <div class="photographer-image">
                        <div class="image-placeholder-large">Portfolio</div>
                    </div>
                    <div class="photographer-info p-lg">
                        <h5 class="mb-sm">Yaw Akufo</h5>
                        <p class="text-secondary text-sm mb-md">Fashion & Lifestyle Photographer</p>
                        <div class="photographer-rating mb-md">
                            <span class="stars">⭐⭐⭐⭐</span>
                            <span class="rating-value">(98 reviews)</span>
                        </div>
                        <a href="#" class="btn btn-sm btn-primary">View Portfolio</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Timeline -->
    <section class="section-spacing bg-light" id="how-it-works">
        <div class="container">
            <div class="text-center mb-xl">
                <h2 class="mb-md">How It Works</h2>
                <p class="text-muted max-width-600 mx-auto">
                    Simple steps to book services, rent equipment, or order prints
                </p>
            </div>

            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-step">1</div>
                    <div class="timeline-content">
                        <h4>Browse & Explore</h4>
                        <p class="text-muted">Discover photographers, equipment, and services tailored to your needs</p>
                    </div>
                </div>

                <div class="timeline-connector"></div>

                <div class="timeline-item">
                    <div class="timeline-step">2</div>
                    <div class="timeline-content">
                        <h4>Select & Customize</h4>
                        <p class="text-muted">Choose your preferred service, dates, and any special requirements</p>
                    </div>
                </div>

                <div class="timeline-connector"></div>

                <div class="timeline-item">
                    <div class="timeline-step">3</div>
                    <div class="timeline-content">
                        <h4>Secure Payment</h4>
                        <p class="text-muted">Complete checkout with our safe and encrypted payment system</p>
                    </div>
                </div>

                <div class="timeline-connector"></div>

                <div class="timeline-item">
                    <div class="timeline-step">4</div>
                    <div class="timeline-content">
                        <h4>Experience Magic</h4>
                        <p class="text-muted">Enjoy professional service from our talented photographers and vendors</p>
                    </div>
                </div>

                <div class="timeline-connector"></div>

                <div class="timeline-item">
                    <div class="timeline-step">5</div>
                    <div class="timeline-content">
                        <h4>Get Your Memories</h4>
                        <p class="text-muted">Access your photos via secure gallery link or receive premium prints</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="section-spacing testimonials">
        <div class="container">
            <div class="text-center mb-xl">
                <h2 class="mb-md">What Our Clients Say</h2>
                <p class="text-muted max-width-600 mx-auto">
                    Real testimonials from satisfied customers across Ghana
                </p>
            </div>

            <div class="grid grid-3 gap-lg">
                <div class="testimonial-card glass">
                    <div class="testimonial-rating mb-md">
                        <span class="stars">⭐⭐⭐⭐⭐</span>
                    </div>
                    <p class="text-muted mb-lg italic">
                        "Ama captured our wedding day perfectly. The professionalism and attention to detail were exceptional. We couldn't have asked for better!"
                    </p>
                    <div class="testimonial-author">
                        <h6 class="text-primary m-0">Akosua & Kofi</h6>
                        <p class="text-muted text-sm m-0">Wedding – Accra</p>
                    </div>
                </div>

                <div class="testimonial-card glass">
                    <div class="testimonial-rating mb-md">
                        <span class="stars">⭐⭐⭐⭐⭐</span>
                    </div>
                    <p class="text-muted mb-lg italic">
                        "PhotoMarket made it so easy to find and book the perfect photobooth for our corporate event. The entire team was professional and timely."
                    </p>
                    <div class="testimonial-author">
                        <h6 class="text-primary m-0">David Mensah</h6>
                        <p class="text-muted text-sm m-0">Corporate Event – Kumasi</p>
                    </div>
                </div>

                <div class="testimonial-card glass">
                    <div class="testimonial-rating mb-md">
                        <span class="stars">⭐⭐⭐⭐⭐</span>
                    </div>
                    <p class="text-muted mb-lg italic">
                        "The print quality of our canvas is stunning. They really captured the essence of our memories beautifully and delivered on time."
                    </p>
                    <div class="testimonial-author">
                        <h6 class="text-primary m-0">Esi Okoro</h6>
                        <p class="text-muted text-sm m-0">Print Services – Takoradi</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Recent Work Gallery -->
    <section class="section-spacing gallery">
        <div class="container">
            <div class="text-center mb-xl">
                <h2 class="mb-md">Recent Work</h2>
                <p class="text-muted max-width-600 mx-auto">
                    Showcase of our photographers' finest moments
                </p>
            </div>

            <div class="grid grid-4 gap-md">
                <div class="gallery-item hover-zoom">
                    <div class="gallery-image">
                        <div class="image-placeholder">Gallery 1</div>
                    </div>
                </div>

                <div class="gallery-item hover-zoom">
                    <div class="gallery-image">
                        <div class="image-placeholder">Gallery 2</div>
                    </div>
                </div>

                <div class="gallery-item hover-zoom">
                    <div class="gallery-image">
                        <div class="image-placeholder">Gallery 3</div>
                    </div>
                </div>

                <div class="gallery-item hover-zoom">
                    <div class="gallery-image">
                        <div class="image-placeholder">Gallery 4</div>
                    </div>
                </div>

                <div class="gallery-item hover-zoom">
                    <div class="gallery-image">
                        <div class="image-placeholder">Gallery 5</div>
                    </div>
                </div>

                <div class="gallery-item hover-zoom">
                    <div class="gallery-image">
                        <div class="image-placeholder">Gallery 6</div>
                    </div>
                </div>

                <div class="gallery-item hover-zoom">
                    <div class="gallery-image">
                        <div class="image-placeholder">Gallery 7</div>
                    </div>
                </div>

                <div class="gallery-item hover-zoom">
                    <div class="gallery-image">
                        <div class="image-placeholder">Gallery 8</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="section-spacing cta-section">
        <div class="container text-center">
            <h2 class="mb-lg">Ready to Create Magic?</h2>
            <p class="text-muted mb-xl max-width-600 mx-auto">
                Join thousands of happy customers who've found their perfect photographer or equipment vendor on PhotoMarket.
            </p>
            <div class="flex gap-md justify-center">
                <a href="/auth/signup" class="btn btn-lg btn-primary">Get Started</a>
                <a href="#services" class="btn btn-lg btn-outline">Explore Services</a>
            </div>
        </div>
    </section>

</main>

<?php include 'views/footer.php'; ?>

<style>
    /* Hero Section */
    .hero {
        position: relative;
        min-height: 90vh;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .hero-background {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, var(--primary) 0%, #0d1838 50%, var(--accent) 100%);
        z-index: -1;
    }

    .hero-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 800"><defs><pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M 40 0 L 0 0 0 40" fill="none" stroke="rgba(226,196,146,0.05)" stroke-width="1"/></pattern></defs><rect width="1200" height="800" fill="url(%23grid)"/></svg>');
        z-index: -1;
    }

    .hero-content {
        position: relative;
        z-index: 1;
        text-align: center;
        color: var(--white);
    }

    .hero-title {
        color: var(--white);
        font-size: 4.5rem;
        margin-bottom: var(--spacing-lg);
        line-height: 1.1;
    }

    .hero-subtitle {
        font-size: 1.3rem;
        color: rgba(255, 255, 255, 0.9);
        max-width: 600px;
        margin: 0 auto var(--spacing-lg);
    }

    .hero-cta {
        justify-content: center;
    }

    /* Services Overview */
    .services-overview {
        background: var(--white);
    }

    .service-icon {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(226, 196, 146, 0.1);
        border-radius: 50%;
    }

    .service-list {
        list-style: none;
        padding: 0;
    }

    .service-list li {
        padding: var(--spacing-xs) 0;
        border-bottom: 1px solid var(--border-color);
    }

    .service-list li:last-child {
        border-bottom: none;
    }

    /* Category Filters */
    .category-btn {
        padding: var(--spacing-sm) var(--spacing-lg);
        border: 2px solid var(--border-color);
        background: var(--white);
        border-radius: var(--border-radius);
        cursor: pointer;
        font-weight: 500;
        transition: var(--transition);
    }

    .category-btn:hover,
    .category-btn.active {
        background: var(--primary);
        color: var(--white);
        border-color: var(--primary);
    }

    .justify-center {
        justify-content: center;
    }

    .mx-auto {
        margin-left: auto;
        margin-right: auto;
    }

    .max-width-600 {
        max-width: 600px;
    }

    /* Card Results */
    #category-results .card {
        text-align: center;
    }

    .card-image {
        width: 100%;
        height: 200px;
        margin: -1.5rem -1.5rem 1rem -1.5rem;
        border-radius: var(--border-radius) var(--border-radius) 0 0;
        overflow: hidden;
    }

    .image-placeholder {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, var(--neutral) 0%, var(--secondary) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--accent);
        font-weight: 600;
    }

    /* Photographer Carousel */
    .photographer-carousel {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: var(--spacing-lg);
        overflow-x: auto;
        padding-bottom: var(--spacing-md);
    }

    .photographer-card {
        background: var(--white);
        border-radius: var(--border-radius);
        overflow: hidden;
        box-shadow: var(--shadow-md);
        transition: var(--transition);
    }

    .photographer-card:hover {
        box-shadow: var(--shadow-lg);
        transform: translateY(-8px);
    }

    .photographer-image {
        width: 100%;
        height: 250px;
        overflow: hidden;
        position: relative;
    }

    .image-placeholder-large {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--white);
        font-weight: 600;
    }

    .photographer-info h5 {
        color: var(--primary);
    }

    .photographer-rating {
        font-size: 0.9rem;
    }

    .stars {
        margin-right: var(--spacing-xs);
    }

    .rating-value {
        color: var(--text-secondary);
        font-size: 0.85rem;
    }

    /* Timeline */
    .timeline {
        position: relative;
        max-width: 900px;
        margin: 0 auto;
    }

    .timeline-item {
        display: flex;
        gap: var(--spacing-lg);
        margin-bottom: var(--spacing-xl);
        align-items: flex-start;
    }

    .timeline-step {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary), #0d1838);
        color: var(--white);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.5rem;
        flex-shrink: 0;
        position: relative;
        z-index: 2;
    }

    .timeline-content {
        flex: 1;
        padding-top: var(--spacing-sm);
    }

    .timeline-content h4 {
        color: var(--primary);
        margin-bottom: var(--spacing-sm);
    }

    .timeline-connector {
        position: absolute;
        left: 29px;
        width: 2px;
        height: 80px;
        background: var(--secondary);
        margin-top: -60px;
        z-index: 1;
    }

    .timeline-item:last-child .timeline-connector {
        display: none;
    }

    /* Testimonials */
    .testimonials {
        background: var(--white);
    }

    .testimonial-card {
        text-align: center;
        padding: var(--spacing-lg) !important;
    }

    .testimonial-rating {
        font-size: 1.2rem;
    }

    .italic {
        font-style: italic;
        font-size: 1.1rem;
        line-height: 1.8;
    }

    .testimonial-author {
        margin-top: var(--spacing-md);
    }

    /* Gallery */
    .gallery-item {
        border-radius: var(--border-radius);
        overflow: hidden;
        position: relative;
    }

    .gallery-image {
        width: 100%;
        height: 250px;
        overflow: hidden;
        position: relative;
    }

    .gallery-image .image-placeholder {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, var(--neutral) 0%, var(--accent) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--white);
        font-weight: 600;
    }

    /* CTA Section */
    .cta-section {
        background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
        color: var(--white);
        text-align: center;
    }

    .cta-section h2 {
        color: var(--white);
    }

    .cta-section .text-muted {
        color: rgba(255, 255, 255, 0.8);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .hero-title {
            font-size: 2.5rem;
        }

        .photographer-carousel {
            grid-template-columns: repeat(2, 1fr);
        }

        .timeline-item {
            flex-direction: column;
            margin-bottom: var(--spacing-lg);
        }

        .timeline-step {
            position: relative;
            left: 50%;
            transform: translateX(-50%);
        }

        .timeline-connector {
            display: none;
        }
    }

    @media (max-width: 480px) {
        .hero-title {
            font-size: 1.75rem;
        }

        .photographer-carousel {
            grid-template-columns: 1fr;
        }

        .grid-4 {
            grid-template-columns: repeat(2, 1fr);
        }

        .hero-cta {
            flex-direction: column;
        }
    }
</style>
