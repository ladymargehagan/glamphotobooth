<?php
session_start();

// If already logged in, redirect to appropriate dashboard
if (isset($_SESSION['user_id'])) {
    switch ($_SESSION['user_role']) {
        case 'admin':
            header('Location: views/admin/dashboard.php');
            break;
        case 'provider':
            header('Location: views/provider/dashboard.php');
            break;
        default:
            header('Location: views/customer/dashboard.php');
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Glam PhotoBooth Accra</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/global.css">
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body>
    <!-- Auth Container -->
    <div class="auth-wrapper">
        <!-- Left Side - Branding -->
        <div class="auth-branding">
            <div class="auth-branding-content">
                <h1 class="brand-logo"><span>&</span> Glam PhotoBooth <span>Accra</span></h1>
                <h2>Welcome Back!</h2>
                <p>Sign in to access your dashboard and manage your photography services.</p>
                <div class="auth-features">
                    <div class="feature-item">
                        <span class="feature-icon">=¯</span>
                        <span>Book Professional Services</span>
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon"><≠</span>
                        <span>Access Photo Galleries</span>
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon">P</span>
                        <span>Manage Your Bookings</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="auth-form-container">
            <div class="auth-form-wrapper">
                <div class="auth-header">
                    <h2>Sign In</h2>
                    <p>Enter your credentials to access your account</p>
                </div>

                <!-- Error/Success Messages -->
                <div id="authMessage" class="auth-message" style="display: none;"></div>

                <form id="loginForm" class="auth-form" action="controllers/auth_controller.php" method="POST">
                    <input type="hidden" name="action" value="login">

                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="you@example.com" required>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
                    </div>

                    <div class="form-group" style="display: flex; justify-content: space-between; align-items: center;">
                        <label style="display: flex; align-items: center; gap: 0.5rem; font-weight: normal; text-transform: none;">
                            <input type="checkbox" name="remember" style="width: auto;">
                            <span>Remember me</span>
                        </label>
                        <a href="forgot-password.php" style="color: var(--gold-primary); font-size: 0.875rem;">Forgot Password?</a>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">Sign In</button>
                </form>

                <div class="auth-divider">
                    <span>Or continue with</span>
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
                    <p>Don't have an account? <a href="register.php">Sign up as a customer</a></p>
                    <p>Are you a photographer or vendor? <a href="provider-signup.php">Apply as a service provider</a></p>
                </div>

                <div class="auth-back-home">
                    <a href="index.php">ê Back to Home</a>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/auth.js"></script>
</body>
</html>
