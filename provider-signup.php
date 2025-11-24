<?php
session_start();

// If already logged in, redirect
if (isset($_SESSION['user_id'])) {
    $userType = $_SESSION['user_type'] ?? 'customer';
    if ($userType === 'photographer' || $userType === 'vendor') {
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
    <title>Become a Service Provider - Glam PhotoBooth Accra</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .provider-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 100vh;
        }

        .provider-left {
            background: linear-gradient(135deg, var(--navy-dark) 0%, var(--navy-medium) 100%);
            color: white;
            padding: 4rem 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .provider-left h2 {
            font-family: 'Playfair Display', serif;
            color: white;
            font-size: 2.5rem;
            margin-bottom: 1rem;
            font-weight: 700;
        }

        .provider-left p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.125rem;
            margin-bottom: 2rem;
            line-height: 1.7;
        }

        .benefit {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1.5rem;
            color: rgba(255, 255, 255, 0.9);
        }

        .benefit i {
            color: var(--gold-primary);
            font-size: 1.5rem;
            margin-top: 0.25rem;
            flex-shrink: 0;
        }

        .provider-right {
            padding: 4rem 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background-color: var(--cream);
            max-height: 100vh;
            overflow-y: auto;
        }

        .form-wrapper h2 {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            margin-bottom: 0.5rem;
            font-weight: 700;
        }

        .form-wrapper > p {
            color: var(--medium-gray);
            margin-bottom: 2rem;
        }

        .form-section {
            margin-bottom: 2rem;
        }

        .form-section h4 {
            font-family: 'Playfair Display', serif;
            font-size: 1.125rem;
            margin-bottom: 1rem;
            color: var(--navy-dark);
            font-weight: 700;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .form-row.full {
            grid-template-columns: 1fr;
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

        .note {
            background: rgba(212, 175, 120, 0.1);
            border-left: 4px solid var(--gold-primary);
            padding: 1rem;
            border-radius: 4px;
            font-size: 0.875rem;
            color: var(--dark-gray);
            margin-top: 2rem;
        }

        @media (max-width: 768px) {
            .provider-container {
                grid-template-columns: 1fr;
            }

            .provider-left {
                padding: 2rem;
                display: none;
            }

            .provider-right {
                padding: 2rem;
                max-height: none;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .form-row.full {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <?php include 'views/components/navbar.php'; ?>

    <div class="provider-container">
        <!-- Left Side -->
        <div class="provider-left">
            <h1 style="font-family: 'Playfair Display', serif; font-size: 2.5rem; margin-bottom: 2rem; font-weight: 800;">
                <span style="color: var(--gold-primary);">&</span> Glam PhotoBooth Accra
            </h1>
            <h2>Become a Service Provider</h2>
            <p>Join Ghana's premier photography services marketplace and grow your business with us.</p>

            <div class="benefit">
                <i class="fas fa-users"></i>
                <span>Access to thousands of potential clients</span>
            </div>

            <div class="benefit">
                <i class="fas fa-lock"></i>
                <span>Secure payment processing</span>
            </div>

            <div class="benefit">
                <i class="fas fa-dollar-sign"></i>
                <span>Flexible pricing and packages</span>
            </div>

            <div class="benefit">
                <i class="fas fa-image"></i>
                <span>Professional portfolio showcase</span>
            </div>

            <div class="benefit">
                <i class="fas fa-calendar"></i>
                <span>Built-in booking management</span>
            </div>

            <div class="benefit">
                <i class="fas fa-bullhorn"></i>
                <span>Marketing and promotional support</span>
            </div>

            <div class="note">
                <strong>Note:</strong> All provider applications are reviewed within 48 hours. We'll contact you via email once approved.
            </div>
        </div>

        <!-- Right Side -->
        <div class="provider-right">
            <div class="form-wrapper">
                <h2>Apply as a Service Provider</h2>
                <p>Complete the form below to apply</p>

                <div id="authMessage" class="message" style="display: none;"></div>

                <form id="providerForm">
                    <!-- Personal Information -->
                    <div class="form-section">
                        <h4>Personal Information</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="fullName" class="form-label">Full Name</label>
                                <input type="text" id="fullName" name="full_name" class="form-control" placeholder="Your name" required>
                            </div>
                            <div class="form-group">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" id="email" name="email" class="form-control" placeholder="your@email.com" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" id="phone" name="phone" class="form-control" placeholder="+233 24 123 4567" required>
                            </div>
                            <div class="form-group">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" id="password" name="password" class="form-control" placeholder="Minimum 8 characters" required minlength="8">
                            </div>
                        </div>
                    </div>

                    <!-- Business Information -->
                    <div class="form-section">
                        <h4>Business Information</h4>
                        <div class="form-row full">
                            <div class="form-group">
                                <label for="businessName" class="form-label">Business Name</label>
                                <input type="text" id="businessName" name="business_name" class="form-control" placeholder="Your business name" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="serviceType" class="form-label">Service Type</label>
                                <select id="serviceType" name="service_type" class="form-control" required>
                                    <option value="">Select service type</option>
                                    <option value="photographer">Photographer</option>
                                    <option value="videographer">Videographer</option>
                                    <option value="vendor">Vendor (Booth/Props)</option>
                                    <option value="other">Other Services</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="experience" class="form-label">Years of Experience</label>
                                <input type="number" id="experience" name="experience_years" class="form-control" placeholder="e.g., 5" required>
                            </div>
                        </div>
                        <div class="form-row full">
                            <div class="form-group">
                                <label for="description" class="form-label">Business Description</label>
                                <textarea id="description" name="description" class="form-control" placeholder="Tell us about your business..." rows="4" required></textarea>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; margin-top: 1rem;">Submit Application</button>
                </form>

                <p style="font-size: 0.875rem; color: var(--medium-gray); margin-top: 1.5rem; text-align: center;">
                    Already registered? <a href="login.php" style="color: var(--gold-primary); font-weight: 600;">Sign in here</a>
                </p>

                <a href="index.php" class="back-link">
                    <i class="fas fa-arrow-left"></i> Back to Home
                </a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'views/components/footer.php'; ?>

    <script src="js/provider-signup.js"></script>
</body>
</html>
