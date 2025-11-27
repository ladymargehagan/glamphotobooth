<?php
/**
 * About Us Page
 * about.php
 */
require_once __DIR__ . '/settings/core.php';

$pageTitle = 'About Us - PhotoMarket';
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
        .about-container {
            max-width: 900px;
            margin: 0 auto;
            padding: var(--spacing-2xl) var(--spacing-xl);
        }

        .about-header {
            text-align: center;
            margin-bottom: var(--spacing-2xl);
        }

        .about-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: var(--spacing-md);
        }

        .about-header p {
            font-size: 1.1rem;
            color: var(--text-muted);
            max-width: 600px;
            margin: 0 auto;
        }

        .about-section {
            margin-bottom: var(--spacing-2xl);
            line-height: 1.8;
        }

        .about-section h2 {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            color: var(--primary);
            margin-bottom: var(--spacing-md);
            margin-top: var(--spacing-xl);
        }

        .about-section p {
            color: var(--text-secondary);
            margin-bottom: var(--spacing-md);
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: var(--spacing-lg);
            margin-top: var(--spacing-lg);
        }

        .feature-card {
            background: var(--bg-light);
            padding: var(--spacing-lg);
            border-radius: var(--border-radius);
            border-left: 4px solid var(--secondary);
        }

        .feature-card h3 {
            color: var(--primary);
            margin-bottom: var(--spacing-sm);
        }

        .feature-card p {
            font-size: 0.95rem;
            color: var(--text-secondary);
            margin: 0;
        }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/views/header.php'; ?>

    <main>
        <div class="about-container">
            <div class="about-header">
                <h1>About PhotoMarket</h1>
                <p>Your premier marketplace for professional photography services and equipment</p>
            </div>

            <section class="about-section">
                <h2>Our Mission</h2>
                <p>PhotoMarket is dedicated to connecting Ghana's creative professionals with clients seeking exceptional photography services. We believe in empowering photographers, videographers, and creative professionals by providing them with a platform to showcase their talent and grow their business.</p>
                <p>Our mission is to make professional photography services accessible, transparent, and affordable for everyone while supporting the thriving creative community in Ghana.</p>
            </section>

            <section class="about-section">
                <h2>What We Offer</h2>
                <div class="features-grid">
                    <div class="feature-card">
                        <h3>Photography Services</h3>
                        <p>Browse a curated selection of professional photographers offering wedding, portrait, event, and commercial photography services.</p>
                    </div>
                    <div class="feature-card">
                        <h3>Equipment & Products</h3>
                        <p>Explore a wide range of photography equipment, accessories, and products from trusted sellers and vendors.</p>
                    </div>
                    <div class="feature-card">
                        <h3>Professional Network</h3>
                        <p>Connect with talented photographers and creative professionals in Ghana's vibrant creative community.</p>
                    </div>
                    <div class="feature-card">
                        <h3>Easy Booking</h3>
                        <p>Seamless booking process with clear pricing, transparent communication, and secure payment options.</p>
                    </div>
                    <div class="feature-card">
                        <h3>Quality Assurance</h3>
                        <p>All professionals on our platform are verified, with ratings and reviews from previous clients.</p>
                    </div>
                    <div class="feature-card">
                        <h3>Support</h3>
                        <p>Dedicated support team ready to help with bookings, questions, and technical assistance.</p>
                    </div>
                </div>
            </section>

            <section class="about-section">
                <h2>Why Choose PhotoMarket?</h2>
                <p><strong>Verified Professionals:</strong> All photographers and sellers on our platform are verified and reviewed by customers.</p>
                <p><strong>Transparent Pricing:</strong> No hidden fees. See exactly what you're paying for before you book.</p>
                <p><strong>Secure Payments:</strong> Your payments are secure and protected through our trusted payment gateway.</p>
                <p><strong>Community Focused:</strong> We're proud to support Ghana's creative professionals and help them succeed.</p>
                <p><strong>Responsive Support:</strong> Our team is here to support you every step of the way.</p>
            </section>

            <section class="about-section">
                <h2>Get Started</h2>
                <p>Whether you're looking for professional photography services or high-quality photography equipment, PhotoMarket makes it easy to find exactly what you need.</p>
                <p>
                    <a href="<?php echo SITE_URL; ?>/services.php" class="btn btn-primary">Browse Services</a>
                    <a href="<?php echo SITE_URL; ?>/shop.php" class="btn btn-secondary" style="margin-left: var(--spacing-md);">Shop Products</a>
                </p>
            </section>
        </div>
    </main>

    <?php require_once __DIR__ . '/views/footer.php'; ?>
</body>
</html>
