<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Glam PhotoBooth Accra - Photography Services Marketplace</title>
    <meta name="description" content="Ghana's leading marketplace for professional photographers, videographers, and photobooths. Discover and book premium services for your events.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lavishly+Yours&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">

    <style>
        .hero {
            background: linear-gradient(135deg, var(--navy-dark) 0%, var(--navy-medium) 100%);
            color: white;
            padding: 6rem 2rem;
            text-align: center;
            min-height: 500px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .hero h1 {
            color: white;
            font-size: 4rem;
            margin-bottom: 1rem;
            letter-spacing: -0.02em;
        }

        .hero p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.25rem;
            margin-bottom: 2rem;
            max-width: 700px;
        }

        .section {
            padding: 4rem 2rem;
        }

        .section-title {
            font-size: 2.75rem;
            margin-bottom: 0.5rem;
            text-align: center;
        }

        .section-subtitle {
            color: var(--medium-gray);
            font-size: 1.125rem;
            text-align: center;
            max-width: 700px;
            margin: 0 auto 3rem;
        }

        .service-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .service-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 6px rgba(27, 43, 77, 0.1);
            transition: all 300ms ease;
            text-align: center;
            border-top: 4px solid var(--gold-primary);
        }

        .service-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px rgba(27, 43, 77, 0.2);
        }

        .service-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--gold-primary), var(--gold-accent));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: white;
            margin: 0 auto 1.5rem;
        }

        .service-card h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .service-card p {
            color: var(--medium-gray);
            margin-bottom: 1.5rem;
            line-height: 1.8;
        }

        .cta-section {
            background: linear-gradient(135deg, var(--navy-dark) 0%, var(--navy-medium) 100%);
            color: white;
            padding: 4rem 2rem;
            text-align: center;
        }

        .cta-section h2 {
            color: white;
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .cta-section p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.125rem;
            max-width: 700px;
            margin: 0 auto 2rem;
        }

        .btn-group {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .steps-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .step {
            text-align: center;
        }

        .step-number {
            width: 60px;
            height: 60px;
            background: var(--gold-primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1.5rem;
            margin: 0 auto 1rem;
        }

        .step h4 {
            margin-bottom: 0.75rem;
        }

        .step p {
            color: var(--medium-gray);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }

            .hero p {
                font-size: 1rem;
            }

            .section-title {
                font-size: 2rem;
            }

            .cta-section h2 {
                font-size: 2rem;
            }

            .btn-group {
                flex-direction: column;
            }

            .btn-group .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <?php include 'views/components/navbar.php'; ?>

    <!-- Hero Section -->
    <div class="hero">
        <h1>
            <span style="color: var(--gold-primary);">&</span> Glam PhotoBooth Accra
        </h1>
        <p style="font-family: 'Lavishly Yours', serif; font-size: 1.5rem; margin-bottom: 1rem;">Ghana's Premier Photography Marketplace</p>
        <p>Discover and book professional photographers, videographers, and photobooths for your special events.</p>
        <div class="btn-group" style="margin-top: 2rem;">
            <?php if(!isset($_SESSION['user_id'])): ?>
                <a href="register.php" class="btn btn-primary btn-lg">Get Started</a>
                <a href="login.php" class="btn btn-outline btn-lg" style="color: white; border-color: white;">Sign In</a>
            <?php else: ?>
                <a href="packages.php" class="btn btn-primary btn-lg">Browse Services</a>
                <a href="gallery.php" class="btn btn-outline btn-lg" style="color: white; border-color: white;">View Gallery</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Service Types Section -->
    <section class="section bg-white">
        <div class="container">
            <h2 class="section-title">Find the Perfect Service Provider</h2>
            <p class="section-subtitle">Explore our marketplace of talented professionals ready to make your event unforgettable</p>

            <div class="service-grid">
                <!-- Photographers -->
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-camera"></i>
                    </div>
                    <h3>Professional Photographers</h3>
                    <p>Skilled photographers specializing in weddings, corporate events, portraits, and more. Browse their portfolios and book directly.</p>
                    <a href="packages.php?type=photographer" class="btn btn-outline">Explore Photographers</a>
                </div>

                <!-- Photobooths -->
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-image"></i>
                    </div>
                    <h3>Luxury Photobooths</h3>
                    <p>State-of-the-art photobooth experiences with instant prints, custom backdrops, and digital sharing options.</p>
                    <a href="packages.php?type=booth" class="btn btn-outline">Browse Photobooths</a>
                </div>

                <!-- Videographers -->
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-video"></i>
                    </div>
                    <h3>Videography & Drone Services</h3>
                    <p>Professional videographers and drone operators for cinematic coverage of your events. Drone shots, same-day edits, and more.</p>
                    <a href="packages.php?type=videographer" class="btn btn-outline">Find Videographers</a>
                </div>

                <!-- Add-ons & Props -->
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-sparkles"></i>
                    </div>
                    <h3>Premium Add-ons & Props</h3>
                    <p>Enhance your event with professional lighting, custom backdrops, props, albums, and other premium add-on services.</p>
                    <a href="packages.php?type=addons" class="btn btn-outline">View Add-ons</a>
                </div>

                <!-- Videography -->
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-film"></i>
                    </div>
                    <h3>Event Videography</h3>
                    <p>Capture your event in stunning 4K video. Professional editing, color grading, and rapid turnaround times available.</p>
                    <a href="packages.php?type=videography" class="btn btn-outline">Book Videography</a>
                </div>

                <!-- Bundles -->
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-gift"></i>
                    </div>
                    <h3>Complete Packages</h3>
                    <p>Save more with bundled services combining photography, videography, booths, and add-ons at discounted rates.</p>
                    <a href="packages.php?filter=bundles" class="btn btn-outline">View Bundles</a>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="section" style="background-color: var(--cream);">
        <div class="container">
            <h2 class="section-title">How It Works</h2>
            <p class="section-subtitle">Booking professional services has never been easier</p>

            <div class="steps-grid">
                <div class="step">
                    <div class="step-number">1</div>
                    <h4>Browse & Discover</h4>
                    <p>Explore our marketplace of verified photographers, videographers, and vendors. View portfolios and customer reviews.</p>
                </div>

                <div class="step">
                    <div class="step-number">2</div>
                    <h4>Check Availability</h4>
                    <p>Select your event date and time. Instantly see which providers are available for your specific needs.</p>
                </div>

                <div class="step">
                    <div class="step-number">3</div>
                    <h4>Book & Customize</h4>
                    <p>Choose your package, customize with add-ons, and complete your booking with our secure checkout.</p>
                </div>

                <div class="step">
                    <div class="step-number">4</div>
                    <h4>Get Professional Results</h4>
                    <p>Relax while our professionals capture your event. Access your photos and videos in your private gallery.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="section bg-white">
        <div class="container">
            <h2 class="section-title">Why Choose Glam PhotoBooth Accra</h2>

            <div class="service-grid" style="margin-top: 3rem;">
                <div style="text-align: center;">
                    <div style="font-size: 3rem; color: var(--gold-primary); margin-bottom: 1rem;">
                        <i class="fas fa-star"></i>
                    </div>
                    <h4>Vetted Professionals</h4>
                    <p style="color: var(--medium-gray);">All service providers are verified and reviewed by customers to ensure quality and reliability.</p>
                </div>

                <div style="text-align: center;">
                    <div style="font-size: 3rem; color: var(--gold-primary); margin-bottom: 1rem;">
                        <i class="fas fa-lock"></i>
                    </div>
                    <h4>Secure Transactions</h4>
                    <p style="color: var(--medium-gray);">Your payments are safe and protected. Book with confidence knowing your investment is secured.</p>
                </div>

                <div style="text-align: center;">
                    <div style="font-size: 3rem; color: var(--gold-primary); margin-bottom: 1rem;">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h4>24/7 Support</h4>
                    <p style="color: var(--medium-gray);">Our support team is always available to help with any questions or concerns about your booking.</p>
                </div>

                <div style="text-align: center;">
                    <div style="font-size: 3rem; color: var(--gold-primary); margin-bottom: 1rem;">
                        <i class="fas fa-users"></i>
                    </div>
                    <h4>Community Driven</h4>
                    <p style="color: var(--medium-gray);">Join thousands of satisfied customers and professional providers building Ghana's photography marketplace.</p>
                </div>

                <div style="text-align: center;">
                    <div style="font-size: 3rem; color: var(--gold-primary); margin-bottom: 1rem;">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h4>Quick Turnaround</h4>
                    <p style="color: var(--medium-gray);">Fast delivery of edited photos and videos. Same-day highlights and rush options available.</p>
                </div>

                <div style="text-align: center;">
                    <div style="font-size: 3rem; color: var(--gold-primary); margin-bottom: 1rem;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h4>Quality Guaranteed</h4>
                    <p style="color: var(--medium-gray);">Professional equipment, experienced providers, and consistent quality standards across all bookings.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <div class="cta-section">
        <h2>Ready to Plan Your Perfect Event?</h2>
        <p>Join thousands of satisfied customers and book professional photography services today.</p>
        <div class="btn-group">
            <?php if(!isset($_SESSION['user_id'])): ?>
                <a href="register.php" class="btn btn-primary btn-lg">Create Account Free</a>
                <a href="packages.php" class="btn btn-outline btn-lg" style="color: white; border-color: white;">Browse Services</a>
            <?php else: ?>
                <a href="packages.php" class="btn btn-primary btn-lg">Start Booking Now</a>
                <a href="gallery.php" class="btn btn-outline btn-lg" style="color: white; border-color: white;">View Gallery</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'views/components/footer.php'; ?>
</body>
</html>
