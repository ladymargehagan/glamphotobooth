<?php
session_start();

// If already logged in, redirect
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] === 'provider') {
        header('Location: views/provider/dashboard.php');
    } else {
        header('Location: index.php');
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Provider Application - Glam PhotoBooth Accra</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/global.css">
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body>
    <div class="auth-wrapper">
        <!-- Left Side - Branding -->
        <div class="auth-branding">
            <div class="auth-branding-content">
                <h1 class="brand-logo"><span>✦</span> Glam PhotoBooth <span>Accra</span></h1>
                <h2>Become a Service Provider</h2>
                <p>Join Ghana's premier photography services marketplace and grow your business with us.</p>

                <div class="provider-benefits" style="background: rgba(255, 255, 255, 0.1); border-left-color: var(--gold-primary);">
                    <h4 style="color: var(--gold-primary);">Why Join Our Platform?</h4>
                    <ul>
                        <li style="color: rgba(255, 255, 255, 0.9);">Access to thousands of potential clients</li>
                        <li style="color: rgba(255, 255, 255, 0.9);">Secure payment processing</li>
                        <li style="color: rgba(255, 255, 255, 0.9);">Flexible pricing and packages</li>
                        <li style="color: rgba(255, 255, 255, 0.9);">Professional portfolio showcase</li>
                        <li style="color: rgba(255, 255, 255, 0.9);">Built-in booking management</li>
                        <li style="color: rgba(255, 255, 255, 0.9);">Marketing and promotional support</li>
                    </ul>
                </div>

                <div style="background: rgba(212, 175, 120, 0.2); padding: 1rem; border-radius: var(--radius-md); margin-top: 1.5rem;">
                    <p style="margin: 0; font-size: 0.875rem; color: rgba(255, 255, 255, 0.9);">
                        <strong style="color: var(--gold-primary);">Note:</strong> All provider applications are reviewed by our team within 48 hours. We'll contact you via email once your application is approved.
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Side - Application Form -->
        <div class="auth-form-container">
            <div class="auth-form-wrapper" style="max-width: 600px;">
                <div class="auth-header">
                    <h2>Provider Application</h2>
                    <p>Complete the form below to apply as a service provider</p>
                </div>

                <div id="authMessage" class="auth-message" style="display: none;"></div>

                <form id="providerSignupForm" class="auth-form" action="controllers/auth_controller.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="provider_signup">

                    <!-- Personal Information -->
                    <h4 style="color: var(--navy-dark); margin-bottom: 1rem; font-size: 1.125rem;">Personal Information</h4>

                    <div class="form-group">
                        <label for="fullName" class="form-label">Full Name</label>
                        <input type="text" id="fullName" name="full_name" class="form-control" placeholder="John Doe" required>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" id="email" name="email" class="form-control" placeholder="you@example.com" required>
                        </div>

                        <div class="form-group">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" id="phone" name="phone" class="form-control" placeholder="+233 24 123 4567" required>
                        </div>
                    </div>

                    <!-- Business Information -->
                    <h4 style="color: var(--navy-dark); margin: 2rem 0 1rem; font-size: 1.125rem;">Business Information</h4>

                    <div class="form-group">
                        <label for="businessName" class="form-label">Business Name</label>
                        <input type="text" id="businessName" name="business_name" class="form-control" placeholder="Your Photography Business" required>
                    </div>

                    <div class="form-group">
                        <label for="serviceType" class="form-label">Service Type</label>
                        <select id="serviceType" name="service_type" class="form-control" required>
                            <option value="">Select service type</option>
                            <option value="photography">Professional Photography</option>
                            <option value="photobooth">Photobooth Rental</option>
                            <option value="both">Both Photography & Photobooth</option>
                            <option value="videography">Videography</option>
                            <option value="other">Other Services</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="experience" class="form-label">Years of Experience</label>
                        <select id="experience" name="years_experience" class="form-control" required>
                            <option value="">Select experience level</option>
                            <option value="0-1">Less than 1 year</option>
                            <option value="1-3">1-3 years</option>
                            <option value="3-5">3-5 years</option>
                            <option value="5-10">5-10 years</option>
                            <option value="10+">10+ years</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="description" class="form-label">Business Description</label>
                        <textarea id="description" name="business_description" class="form-control" rows="4" placeholder="Tell us about your business, services, and what makes you unique..." required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="location" class="form-label">Service Location</label>
                        <input type="text" id="location" name="location" class="form-control" placeholder="Accra, Ghana" required>
                    </div>

                    <!-- Portfolio -->
                    <h4 style="color: var(--navy-dark); margin: 2rem 0 1rem; font-size: 1.125rem;">Portfolio & Credentials</h4>

                    <div class="form-group">
                        <label for="website" class="form-label">Website URL (Optional)</label>
                        <input type="url" id="website" name="website" class="form-control" placeholder="https://yourwebsite.com">
                    </div>

                    <div class="form-group">
                        <label for="instagram" class="form-label">Instagram Handle (Optional)</label>
                        <input type="text" id="instagram" name="instagram" class="form-control" placeholder="@yourhandle">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Portfolio Images (Upload 5-10 images)</label>
                        <div class="file-upload-area" id="portfolioUpload">
                            <div class="file-upload-icon">📷</div>
                            <p style="margin: 0 0 0.5rem; font-weight: 600;">Click to upload or drag and drop</p>
                            <p style="margin: 0; font-size: 0.875rem; color: var(--medium-gray);">PNG, JPG up to 5MB each</p>
                            <input type="file" name="portfolio_images[]" id="portfolioFiles" accept="image/*" multiple style="display: none;">
                        </div>
                        <div id="selectedFiles" style="margin-top: 1rem; font-size: 0.875rem; color: var(--medium-gray);"></div>
                    </div>

                    <!-- Account Security -->
                    <h4 style="color: var(--navy-dark); margin: 2rem 0 1rem; font-size: 1.125rem;">Account Security</h4>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Minimum 8 characters" required minlength="8">
                    </div>

                    <div class="form-group">
                        <label for="confirmPassword" class="form-label">Confirm Password</label>
                        <input type="password" id="confirmPassword" name="confirm_password" class="form-control" placeholder="Re-enter password" required>
                    </div>

                    <!-- Terms Agreement -->
                    <div class="form-group">
                        <label style="display: flex; align-items: flex-start; gap: 0.5rem; font-weight: normal; text-transform: none;">
                            <input type="checkbox" name="terms" required style="width: auto; margin-top: 0.25rem;">
                            <span style="font-size: 0.875rem;">
                                I agree to the <a href="terms.php" style="color: var(--gold-primary);">Service Provider Terms</a>,
                                <a href="privacy.php" style="color: var(--gold-primary);">Privacy Policy</a>, and understand that my application will be reviewed before approval.
                            </span>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">Submit Application</button>
                </form>

                <div class="auth-footer">
                    <p>Already have a provider account? <a href="login.php">Sign in here</a></p>
                    <p>Looking to book services? <a href="register.php">Sign up as a customer</a></p>
                </div>

                <div class="auth-back-home">
                    <a href="index.php">← Back to Home</a>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/auth.js"></script>
</body>
</html>
