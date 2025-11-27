<?php
/**
 * Register Page
 * auth/register.php
 */
require_once __DIR__ . '/../settings/core.php';

if (isLoggedIn()) {
    header('Location: ' . SITE_URL . '/index.php');
    exit;
}

$pageTitle = 'Register - PhotoMarket';
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
    <!-- SweetAlert2 Library -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        window.siteUrl = '<?php echo SITE_URL; ?>';
    </script>
</head>
<body>

<main class="auth-main">
    <section class="auth-container">
        <!-- Left Panel - Hero -->
        <div class="auth-panel auth-panel-left">
            <div class="auth-panel-content">
                <h1>Join PhotoMarket</h1>
                <p>Connect with Ghana's most talented photographers and equipment vendors. Create your account to get started.</p>

                <div class="auth-benefits">
                    <div class="benefit-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"></path>
                        </svg>
                        <span>Create your portfolio</span>
                    </div>
                    <div class="benefit-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="8.5" cy="7" r="4"></circle>
                            <path d="M20 8v6M23 11h-6"></path>
                        </svg>
                        <span>Connect with professionals</span>
                    </div>
                    <div class="benefit-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"></path>
                        </svg>
                        <span>Secure transactions</span>
                    </div>
                </div>

                <div class="auth-login-link">
                    <p>Already have an account? <a href="<?php echo SITE_URL; ?>/auth/login.php">Login here</a></p>
                </div>
            </div>
        </div>

        <!-- Right Panel - Form -->
        <div class="auth-panel auth-panel-right">
            <div class="auth-form-wrapper">
                <h2>Create Account</h2>
                <p class="form-subtitle">What type of account do you want?</p>

                <form id="registerForm" class="auth-form">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

                    <!-- Role Selection -->
                    <div class="form-group role-selection">
                        <label>Account Type</label>
                        <div class="role-options">
                            <label class="role-option">
                                <input type="radio" name="role" value="4" checked>
                                <span class="role-label">
                                    <span class="role-title">Customer</span>
                                    <span class="role-desc">Book services & rent equipment</span>
                                </span>
                            </label>
                            <label class="role-option">
                                <input type="radio" name="role" value="2">
                                <span class="role-label">
                                    <span class="role-title">Photographer</span>
                                    <span class="role-desc">Offer your services</span>
                                </span>
                            </label>
                            <label class="role-option">
                                <input type="radio" name="role" value="3">
                                <span class="role-label">
                                    <span class="role-title">Vendor</span>
                                    <span class="role-desc">Rent/sell equipment</span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Name -->
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" placeholder="Your full name" required>
                        <span class="form-error" id="nameError"></span>
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="you@example.com" required>
                        <span class="form-error" id="emailError"></span>
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="At least 6 characters" required>
                        <span class="form-error" id="passwordError"></span>
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-group">
                        <label for="confirmPassword">Confirm Password</label>
                        <input type="password" id="confirmPassword" name="confirm_password" placeholder="Confirm your password" required>
                        <span class="form-error" id="confirmError"></span>
                    </div>

                    <!-- Phone Number (required for all) -->
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" placeholder="+233 XX XXX XXXX" required>
                        <span class="form-error" id="phoneError"></span>
                    </div>

                    <!-- City/Location (required for all) -->
                    <div class="form-group">
                        <label for="city">City/Location</label>
                        <input type="text" id="city" name="city" placeholder="Your city" required>
                        <span class="form-error" id="cityError"></span>
                    </div>

                    <!-- Business Name (for photographer/vendor) -->
                    <div class="form-group" id="businessNameGroup" style="display: none;">
                        <label for="businessName">Business Name</label>
                        <input type="text" id="businessName" name="business_name" placeholder="Your business name">
                        <span class="form-error" id="businessNameError"></span>
                    </div>

                    <!-- Description (for photographer/vendor) -->
                    <div class="form-group" id="descriptionGroup" style="display: none;">
                        <label for="description">Business Description</label>
                        <textarea id="description" name="description" placeholder="Tell us about your business" rows="3"></textarea>
                        <span class="form-error" id="descriptionError"></span>
                    </div>

                    <!-- Hourly Rate (for photographer/vendor) -->
                    <div class="form-group" id="rateGroup" style="display: none;">
                        <label for="hourlyRate">Hourly Rate (₵)</label>
                        <input type="number" id="hourlyRate" name="hourly_rate" placeholder="e.g., 500" min="0" step="0.01">
                        <span class="form-error" id="rateError"></span>
                    </div>

                    <!-- Service Type (for photographer/vendor) -->
                    <div class="form-group" id="serviceTypeGroup" style="display: none;">
                        <label for="serviceType">Service Type</label>
                        <select id="serviceType" name="service_type">
                            <option value="">Select service type</option>
                            <option value="photography">Photography</option>
                            <option value="equipment_rental">Equipment Rental</option>
                            <option value="photobooth">Photobooth Services</option>
                            <option value="prints">Photo Prints</option>
                            <option value="other">Other</option>
                        </select>
                        <span class="form-error" id="serviceTypeError"></span>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="btn btn-lg btn-primary btn-block" id="submitBtn">
                        Create Account
                    </button>

                    <p class="form-footer">By registering, you agree to our Terms of Service and Privacy Policy</p>
                </form>

                <!-- Success Message -->
                <div id="successMessage" class="auth-message success-message hidden">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <span>Account created successfully! Redirecting...</span>
                </div>

                <!-- Error Message -->
                <div id="errorMessage" class="auth-message error-message hidden">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <span id="errorText"></span>
                </div>
            </div>
        </div>
    </section>
</main>

<script src="<?php echo SITE_URL; ?>/js/sweetalert.js"></script>
<script src="<?php echo SITE_URL; ?>/js/register.js"></script>

<style>
    /* Auth Layout */
    .auth-main {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--white);
        padding: var(--spacing-lg) 0;
    }

    .auth-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        width: 100%;
        max-width: 1000px;
        border-radius: var(--border-radius);
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        min-height: 600px;
    }

    /* Left Panel */
    .auth-panel-left {
        background: linear-gradient(135deg, var(--primary) 0%, #0d1838 100%);
        color: var(--white);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: var(--spacing-xl);
    }

    .auth-panel-content {
        text-align: center;
        max-width: 350px;
    }

    .auth-panel-content h1 {
        color: var(--white);
        font-size: 2rem;
        margin-bottom: var(--spacing-lg);
    }

    .auth-panel-content p {
        color: rgba(255, 255, 255, 0.85);
        margin-bottom: var(--spacing-xl);
        line-height: 1.7;
    }

    .auth-benefits {
        display: flex;
        flex-direction: column;
        gap: var(--spacing-lg);
        margin-bottom: var(--spacing-xl);
    }

    .benefit-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: var(--spacing-sm);
    }

    .benefit-item svg {
        width: 32px;
        height: 32px;
        color: var(--secondary);
    }

    .benefit-item span {
        color: rgba(255, 255, 255, 0.9);
        font-size: 0.9rem;
    }

    .auth-login-link {
        margin-top: var(--spacing-xl);
        padding-top: var(--spacing-xl);
        border-top: 1px solid rgba(255, 255, 255, 0.2);
    }

    .auth-login-link p {
        margin: 0;
        font-size: 0.9rem;
    }

    .auth-login-link a {
        color: var(--secondary);
        font-weight: 600;
    }

    /* Right Panel - Form */
    .auth-panel-right {
        background: var(--white);
        padding: var(--spacing-xl);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .auth-form-wrapper {
        width: 100%;
        max-width: 380px;
    }

    .auth-form-wrapper h2 {
        color: var(--primary);
        font-size: 1.75rem;
        margin-bottom: var(--spacing-sm);
    }

    .form-subtitle {
        color: var(--text-secondary);
        font-size: 0.9rem;
        margin-bottom: var(--spacing-lg);
    }

    /* Form Groups */
    .form-group {
        margin-bottom: var(--spacing-lg);
    }

    .form-group label {
        display: block;
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: var(--spacing-xs);
        color: var(--text-primary);
    }

    .form-group input {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius);
        font-size: 0.95rem;
        font-family: var(--font-sans);
        transition: var(--transition);
        background: var(--white);
    }

    .form-group input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(16, 33, 82, 0.1);
    }

    .form-group textarea,
    .form-group select {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius);
        font-size: 0.95rem;
        font-family: var(--font-sans);
        transition: var(--transition);
        background: var(--white);
    }

    .form-group textarea:focus,
    .form-group select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(16, 33, 82, 0.1);
    }

    .form-group textarea {
        resize: vertical;
        min-height: 80px;
    }

    .form-error {
        display: block;
        color: #d32f2f;
        font-size: 0.8rem;
        margin-top: 4px;
    }

    /* Role Selection */
    .role-selection {
        margin-bottom: var(--spacing-lg);
    }

    .role-options {
        display: flex;
        flex-direction: column;
        gap: var(--spacing-sm);
    }

    .role-option {
        display: flex;
        align-items: center;
        padding: var(--spacing-md);
        border: 2px solid var(--border-color);
        border-radius: var(--border-radius);
        cursor: pointer;
        transition: var(--transition);
        background: var(--white);
    }

    .role-option input {
        width: auto;
        margin-right: var(--spacing-md);
        cursor: pointer;
    }

    .role-option input[type="radio"]:checked {
        accent-color: var(--primary);
    }

    .role-option:has(input:checked) {
        background: rgba(16, 33, 82, 0.05);
        border-color: var(--primary);
    }

    .role-label {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .role-title {
        font-weight: 600;
        color: var(--primary);
    }

    .role-desc {
        font-size: 0.8rem;
        color: var(--text-secondary);
    }

    /* Button */
    .btn-block {
        width: 100%;
        margin-top: var(--spacing-md);
    }

    .form-footer {
        font-size: 0.75rem;
        color: var(--text-secondary);
        text-align: center;
        margin-top: var(--spacing-md);
    }

    /* Messages */
    .auth-message {
        display: none;
        padding: var(--spacing-md);
        border-radius: var(--border-radius);
        margin-top: var(--spacing-lg);
        display: flex;
        align-items: center;
        gap: var(--spacing-sm);
        font-weight: 500;
    }

    .auth-message.hidden {
        display: none;
    }

    .auth-message svg {
        width: 20px;
        height: 20px;
        flex-shrink: 0;
    }

    .success-message {
        background: rgba(76, 175, 80, 0.1);
        color: #2e7d32;
        border: 1px solid rgba(76, 175, 80, 0.3);
    }

    .success-message svg {
        stroke: #2e7d32;
    }

    .error-message {
        background: rgba(211, 47, 47, 0.1);
        color: #c62828;
        border: 1px solid rgba(211, 47, 47, 0.3);
    }

    .error-message svg {
        stroke: #c62828;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .auth-container {
            grid-template-columns: 1fr;
            min-height: auto;
        }

        .auth-panel-left {
            padding: var(--spacing-lg);
            min-height: 300px;
        }

        .auth-panel-content h1 {
            font-size: 1.5rem;
        }

        .auth-panel-right {
            padding: var(--spacing-lg);
        }

        .role-options {
            flex-direction: column;
        }
    }

    @media (max-width: 480px) {
        .auth-container {
            margin: 0 var(--spacing-sm);
        }

        .auth-panel-content h1 {
            font-size: 1.35rem;
        }

        .auth-form-wrapper h2 {
            font-size: 1.5rem;
        }

        .role-option {
            padding: var(--spacing-sm);
        }
    }
</style>
</body>
</html>
