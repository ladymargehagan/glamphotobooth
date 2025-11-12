<?php
session_start();

// If already logged in, redirect
if (isset($_SESSION['user_id'])) {
    header('Location: views/customer/dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Glam PhotoBooth Accra</title>
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
                <h2>Join Our Community!</h2>
                <p>Create an account to book professional photography services and manage your events.</p>
                <div class="auth-features">
                    <div class="feature-item">
                        <span class="feature-icon">🎯</span>
                        <span>Easy Booking Process</span>
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon">💳</span>
                        <span>Secure Payments</span>
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon">📱</span>
                        <span>Instant Gallery Access</span>
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon">🎁</span>
                        <span>Loyalty Rewards</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Registration Form -->
        <div class="auth-form-container">
            <div class="auth-form-wrapper">
                <div class="auth-header">
                    <h2>Create Account</h2>
                    <p>Sign up as a customer to start booking services</p>
                </div>

                <div id="authMessage" class="auth-message" style="display: none;"></div>

                <form id="registerForm" class="auth-form" action="controllers/auth_controller.php" method="POST">
                    <input type="hidden" name="action" value="register">

                    <div class="form-group">
                        <label for="fullName" class="form-label">Full Name</label>
                        <input type="text" id="fullName" name="full_name" class="form-control" placeholder="John Doe" required>
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="you@example.com" required>
                    </div>

                    <div class="form-group">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="tel" id="phone" name="phone" class="form-control" placeholder="+233 24 123 4567" required>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Minimum 8 characters" required minlength="8">
                        <small style="color: var(--medium-gray); font-size: 0.75rem; margin-top: 0.25rem; display: block;">
                            Must be at least 8 characters with uppercase, lowercase, and numbers
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="confirmPassword" class="form-label">Confirm Password</label>
                        <input type="password" id="confirmPassword" name="confirm_password" class="form-control" placeholder="Re-enter password" required>
                    </div>

                    <div class="form-group">
                        <label style="display: flex; align-items: flex-start; gap: 0.5rem; font-weight: normal; text-transform: none;">
                            <input type="checkbox" name="terms" required style="width: auto; margin-top: 0.25rem;">
                            <span style="font-size: 0.875rem;">
                                I agree to the <a href="terms.php" style="color: var(--gold-primary);">Terms of Service</a> and
                                <a href="privacy.php" style="color: var(--gold-primary);">Privacy Policy</a>
                            </span>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">Create Account</button>
                </form>

                <div class="auth-divider">
                    <span>Or sign up with</span>
                </div>

                <div class="social-auth">
                    <button class="social-btn google-btn">
                        <span>G</span> Google
                    </button>
                    <button class="social-btn facebook-btn">
                        <span>f</span> Facebook
                    </button>
                </div>

                <div class="auth-footer">
                    <p>Already have an account? <a href="login.php">Sign in here</a></p>
                    <p>Looking to provide services? <a href="provider-signup.php">Apply as a provider</a></p>
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
