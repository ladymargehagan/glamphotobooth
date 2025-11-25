<?php
/**
 * Login Page
 * auth/login.php
 */
require_once __DIR__ . '/../settings/core.php';

if (isLoggedIn()) {
    header('Location: ' . SITE_URL . '/index.php');
    exit;
}

$pageTitle = 'Login - PhotoMarket';
$cssPath = SITE_URL . '/css/style.css';
?>
<?php include '../views/header.php'; ?>

<main class="auth-main">
    <section class="auth-container">
        <!-- Left Panel - Hero -->
        <div class="auth-panel auth-panel-left">
            <div class="auth-panel-content">
                <h1>Welcome Back</h1>
                <p>Login to your PhotoMarket account to access bookings, manage services, and connect with professionals.</p>

                <div class="auth-benefits">
                    <div class="benefit-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"></path>
                        </svg>
                        <span>View your bookings</span>
                    </div>
                    <div class="benefit-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="8.5" cy="7" r="4"></circle>
                            <path d="M20 8v6M23 11h-6"></path>
                        </svg>
                        <span>Manage your profile</span>
                    </div>
                    <div class="benefit-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"></path>
                        </svg>
                        <span>Secure access</span>
                    </div>
                </div>

                <div class="auth-login-link">
                    <p>Don't have an account? <a href="<?php echo SITE_URL; ?>/auth/register.php">Register here</a></p>
                </div>
            </div>
        </div>

        <!-- Right Panel - Form -->
        <div class="auth-panel auth-panel-right">
            <div class="auth-form-wrapper">
                <h2>Login</h2>
                <p class="form-subtitle">Enter your credentials to access your account</p>

                <form id="loginForm" class="auth-form">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

                    <!-- Email -->
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="you@example.com" required>
                        <span class="form-error" id="emailError"></span>
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Your password" required>
                        <span class="form-error" id="passwordError"></span>
                    </div>

                    <!-- Forgot Password Link -->
                    <div class="form-help">
                        <a href="#">Forgot your password?</a>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="btn btn-lg btn-primary btn-block" id="submitBtn">
                        Login
                    </button>
                </form>

                <!-- Success Message -->
                <div id="successMessage" class="auth-message success-message hidden">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <span>Login successful! Redirecting...</span>
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

<?php include '../views/footer.php'; ?>

<script src="<?php echo SITE_URL; ?>/js/login.js"></script>

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

    .form-error {
        display: block;
        color: #d32f2f;
        font-size: 0.8rem;
        margin-top: 4px;
    }

    .form-help {
        display: flex;
        justify-content: flex-end;
        margin-bottom: var(--spacing-lg);
    }

    .form-help a {
        font-size: 0.85rem;
        color: var(--primary);
        font-weight: 500;
    }

    /* Button */
    .btn-block {
        width: 100%;
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
    }
</style>
