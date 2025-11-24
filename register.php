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
    <title>Create Account - Glam PhotoBooth Accra</title>
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
            margin-bottom: 1.5rem;
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
            max-height: 100vh;
            overflow-y: auto;
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

        .password-hint {
            color: var(--medium-gray);
            font-size: 0.875rem;
            margin-top: 0.5rem;
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
                max-height: none;
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
            <h2>Join Our Community</h2>
            <p>Create an account to discover and book professional photography services across Ghana.</p>

            <div class="feature">
                <i class="fas fa-search"></i>
                <span>Discover professional photographers and vendors</span>
            </div>

            <div class="feature">
                <i class="fas fa-calendar-alt"></i>
                <span>Book services with ease</span>
            </div>

            <div class="feature">
                <i class="fas fa-lock"></i>
                <span>Secure transactions and payments</span>
            </div>

            <div class="feature">
                <i class="fas fa-comments"></i>
                <span>Review and rate service providers</span>
            </div>
        </div>

        <!-- Right Side - Registration Form -->
        <div class="auth-right">
            <div class="auth-form-wrapper">
                <h2>Create Account</h2>
                <p>Sign up as a customer to start booking services</p>

                <div id="authMessage" class="message" style="display: none;"></div>

                <form id="registerForm">
                    <div class="form-group">
                        <label for="fullName" class="form-label">Full Name</label>
                        <input type="text" id="fullName" name="full_name" class="form-control" placeholder="Your full name" required>
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="your@email.com" required>
                    </div>

                    <div class="form-group">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="tel" id="phone" name="phone" class="form-control" placeholder="+233 24 123 4567" required>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Minimum 8 characters" required minlength="8">
                        <div class="password-hint">
                            Must contain uppercase, lowercase, and numbers
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="confirmPassword" class="form-label">Confirm Password</label>
                        <input type="password" id="confirmPassword" name="confirm_password" class="form-control" placeholder="Re-enter password" required>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; margin-top: 1rem;">Create Account</button>
                </form>

                <div class="divider">
                    <span>Already registered?</span>
                </div>

                <a href="login.php" class="btn btn-outline btn-lg" style="width: 100%; text-align: center;">Sign In</a>

                <div class="auth-footer">
                    <p>Looking to provide services? <a href="provider-signup.php">Apply as a provider</a></p>
                </div>

                <a href="index.php" class="back-link">
                    <i class="fas fa-arrow-left"></i> Back to Home
                </a>
            </div>
        </div>
    </div>

    <script src="js/register.js"></script>
</body>
</html>
