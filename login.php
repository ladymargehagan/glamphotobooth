<?php
session_start();

// If already logged in, redirect to appropriate dashboard
if (isset($_SESSION['user_id'])) {
    $userType = $_SESSION['user_type'] ?? 'customer';
    if ($userType === 'photographer' || $userType === 'vendor') {
        header('Location: views/provider/dashboard.php');
    } else {
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
    <title>Sign In - Glam PhotoBooth Accra</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lavishly+Yours&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .auth-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 100vh;
        }

        .auth-left {
            background: linear-gradient(135deg, var(--navy-dark) 0%, var(--navy-medium) 100%);
            color: white;
            padding: 4rem 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .auth-left h2 {
            color: white;
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .auth-left p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.125rem;
            margin-bottom: 2rem;
            line-height: 1.7;
        }

        .feature {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 2rem;
            color: rgba(255, 255, 255, 0.9);
        }

        .feature i {
            color: var(--gold-primary);
            font-size: 1.5rem;
            margin-top: 0.25rem;
            flex-shrink: 0;
        }

        .auth-right {
            padding: 4rem 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background-color: var(--cream);
        }

        .auth-form-wrapper h2 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .auth-form-wrapper > p {
            color: var(--medium-gray);
            margin-bottom: 2rem;
        }

        .divider {
            position: relative;
            margin: 2rem 0;
            text-align: center;
            color: var(--medium-gray);
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background-color: var(--light-gray);
        }

        .divider span {
            position: relative;
            background-color: var(--cream);
            padding: 0 1rem;
        }

        .auth-footer {
            margin-top: 2rem;
            text-align: center;
            color: var(--medium-gray);
        }

        .auth-footer p {
            margin-bottom: 0.5rem;
        }

        .auth-footer a {
            color: var(--gold-primary);
            font-weight: 600;
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 2rem;
            color: var(--navy-dark);
        }

        .back-link:hover {
            color: var(--gold-primary);
        }

        @media (max-width: 768px) {
            .auth-container {
                grid-template-columns: 1fr;
            }

            .auth-left {
                padding: 2rem;
                display: none;
            }

            .auth-right {
                padding: 2rem;
            }

            .auth-left h2 {
                font-size: 1.875rem;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <!-- Left Side - Branding -->
        <div class="auth-left">
            <h1 style="font-family: 'Lavishly Yours', serif; font-size: 2.5rem; margin-bottom: 2rem;">
                <span style="color: var(--gold-primary);">&</span> Glam PhotoBooth Accra
            </h1>
            <h2>Welcome Back</h2>
            <p>Access your account to book professional photography services, manage your gallery, and more.</p>

            <div class="feature">
                <i class="fas fa-camera"></i>
                <span>Browse professional photographers and vendors</span>
            </div>

            <div class="feature">
                <i class="fas fa-calendar-check"></i>
                <span>Manage your bookings and events</span>
            </div>

            <div class="feature">
                <i class="fas fa-image"></i>
                <span>Access your photo galleries</span>
            </div>

            <div class="feature">
                <i class="fas fa-star"></i>
                <span>Rate and review service providers</span>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="auth-right">
            <div class="auth-form-wrapper">
                <h2>Sign In</h2>
                <p>Enter your credentials to access your account</p>

                <div id="authMessage" class="message" style="display: none;"></div>

                <form id="loginForm">
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="your@email.com" required>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; margin-top: 1rem;">Sign In</button>
                </form>

                <div class="divider">
                    <span>New to Glam PhotoBooth?</span>
                </div>

                <a href="register.php" class="btn btn-outline btn-lg" style="width: 100%; text-align: center;">Create Account</a>

                <div class="auth-footer">
                    <p>Are you a service provider? <a href="provider-signup.php">Apply here</a></p>
                </div>

                <a href="index.php" class="back-link">
                    <i class="fas fa-arrow-left"></i> Back to Home
                </a>
            </div>
        </div>
    </div>

    <script src="js/login.js"></script>
</body>
</html>
