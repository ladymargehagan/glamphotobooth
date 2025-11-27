<?php
/**
 * Contact Us Page
 * contact.php
 */
require_once __DIR__ . '/settings/core.php';

$pageTitle = 'Contact Us - PhotoMarket';
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
        .contact-container {
            max-width: 900px;
            margin: 0 auto;
            padding: var(--spacing-2xl) var(--spacing-xl);
        }

        .contact-header {
            text-align: center;
            margin-bottom: var(--spacing-2xl);
        }

        .contact-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: var(--spacing-md);
        }

        .contact-header p {
            font-size: 1.1rem;
            color: var(--text-muted);
            max-width: 600px;
            margin: 0 auto;
        }

        .contact-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--spacing-2xl);
            margin-top: var(--spacing-2xl);
        }

        .contact-info {
            background: var(--bg-light);
            padding: var(--spacing-xl);
            border-radius: var(--border-radius);
        }

        .contact-info h2 {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            color: var(--primary);
            margin-bottom: var(--spacing-lg);
        }

        .info-item {
            margin-bottom: var(--spacing-lg);
        }

        .info-item h3 {
            color: var(--primary);
            font-size: 1rem;
            margin-bottom: var(--spacing-sm);
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 1px;
        }

        .info-item p {
            color: var(--text-secondary);
            margin: 0;
            line-height: 1.6;
        }

        .contact-form-wrapper {
            background: var(--bg-light);
            padding: var(--spacing-xl);
            border-radius: var(--border-radius);
        }

        .contact-form-wrapper h2 {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            color: var(--primary);
            margin-bottom: var(--spacing-lg);
        }

        .form-group {
            margin-bottom: var(--spacing-md);
        }

        .form-group label {
            display: block;
            margin-bottom: var(--spacing-sm);
            color: var(--primary);
            font-weight: 600;
            font-size: 0.95rem;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: var(--spacing-md);
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            font-family: inherit;
            font-size: 0.95rem;
            color: var(--text-primary);
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(16, 33, 82, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }

        .submit-btn {
            background: var(--primary);
            color: white;
            padding: var(--spacing-md) var(--spacing-lg);
            border: none;
            border-radius: var(--border-radius);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            width: 100%;
        }

        .submit-btn:hover {
            background: var(--primary-dark, #0a1535);
        }

        @media (max-width: 768px) {
            .contact-content {
                grid-template-columns: 1fr;
            }

            .contact-header h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/views/header.php'; ?>

    <main>
        <div class="contact-container">
            <div class="contact-header">
                <h1>Contact Us</h1>
                <p>Have questions? We'd love to hear from you. Get in touch with our team.</p>
            </div>

            <div class="contact-content">
                <div class="contact-info">
                    <h2>Get In Touch</h2>

                    <div class="info-item">
                        <h3>Email</h3>
                        <p><a href="mailto:support@photomarket.gh" style="color: var(--text-secondary); text-decoration: none;">support@photomarket.gh</a></p>
                    </div>

                    <div class="info-item">
                        <h3>Location</h3>
                        <p>Accra, Ghana</p>
                    </div>

                    <div class="info-item">
                        <h3>Business Hours</h3>
                        <p>Monday - Friday: 9:00 AM - 6:00 PM<br>Saturday: 10:00 AM - 4:00 PM<br>Sunday: Closed</p>
                    </div>

                    <div class="info-item">
                        <h3>Quick Links</h3>
                        <p>
                            <a href="<?php echo SITE_URL; ?>/services.php" style="color: var(--text-secondary); text-decoration: none; display: block; margin-bottom: 5px;">Browse Services</a>
                            <a href="<?php echo SITE_URL; ?>/shop.php" style="color: var(--text-secondary); text-decoration: none; display: block; margin-bottom: 5px;">Shop Products</a>
                            <a href="<?php echo SITE_URL; ?>/about.php" style="color: var(--text-secondary); text-decoration: none;">About PhotoMarket</a>
                        </p>
                    </div>
                </div>

                <div class="contact-form-wrapper">
                    <h2>Send us a Message</h2>
                    <form id="contactForm" method="POST" action="<?php echo SITE_URL; ?>/actions/contact_form_action.php">
                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" id="name" name="name" required placeholder="Your full name">
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" required placeholder="your@email.com">
                        </div>

                        <div class="form-group">
                            <label for="subject">Subject</label>
                            <input type="text" id="subject" name="subject" required placeholder="How can we help?">
                        </div>

                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea id="message" name="message" required placeholder="Tell us more about your inquiry..."></textarea>
                        </div>

                        <button type="submit" class="submit-btn">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <?php require_once __DIR__ . '/views/footer.php'; ?>
</body>
</html>
