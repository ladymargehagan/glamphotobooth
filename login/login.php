<?php
session_start();

// If already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: ../customer/dashboard.php');
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
    <link href="https://fonts.googleapis.com/css2?family=Lavishly+Yours&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/auth.css">
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
                        <span class="feature-icon"><i class="fas fa-camera"></i></span>
                        <span>Book Professional Services</span>
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon"><i class="fas fa-images"></i></span>
                        <span>Access Photo Galleries</span>
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon"><i class="fas fa-calendar-check"></i></span>
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

                <form id="loginForm" class="auth-form">
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

                <div class="auth-footer">
                    <p>Don't have an account? <a href="register.php">Sign up as a customer</a></p>
                    <p>Are you a photographer or vendor? <a href="../provider-signup.php">Apply as a service provider</a></p>
                </div>

                <div class="auth-back-home">
                    <a href="../index.php"><i class="fas fa-arrow-left"></i> Back to Home</a>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/login.js"></script>
</body>
</html>
