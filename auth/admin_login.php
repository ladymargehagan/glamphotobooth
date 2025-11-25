<?php
/**
 * Admin Login
 * auth/admin_login.php
 */
require_once __DIR__ . '/../settings/core.php';

// Redirect if already logged in as admin
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
    header('Location: ' . SITE_URL . '/admin/dashboard.php');
    exit;
}

$pageTitle = 'Admin Login - PhotoMarket';
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
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: stretch;
        }

        .auth-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: var(--spacing-xxl);
        }

        .auth-panel-left {
            background: linear-gradient(135deg, var(--primary) 0%, #1a3a52 100%);
            color: var(--white);
        }

        .auth-panel-left h2 {
            font-family: var(--font-serif);
            font-size: 2.5rem;
            margin-bottom: var(--spacing-md);
            font-weight: 700;
        }

        .auth-panel-left p {
            font-size: 1rem;
            line-height: 1.6;
            color: rgba(255, 255, 255, 0.9);
        }

        .auth-panel-right {
            background: var(--white);
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .auth-form-wrapper {
            max-width: 400px;
            margin: 0 auto;
            width: 100%;
        }

        .auth-form-title {
            color: var(--primary);
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: var(--spacing-sm);
            font-family: var(--font-serif);
        }

        .auth-form-subtitle {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-bottom: var(--spacing-lg);
        }

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

        .form-actions {
            display: flex;
            gap: var(--spacing-md);
            margin-top: var(--spacing-xl);
        }

        .btn-submit {
            flex: 1;
            padding: 0.875rem 1.5rem;
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.95rem;
        }

        .btn-submit:hover {
            background: #0d1a3a;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 33, 82, 0.2);
        }

        .btn-submit:disabled {
            background: var(--text-secondary);
            cursor: not-allowed;
            transform: none;
        }

        .btn-back {
            flex: 1;
            padding: 0.875rem 1.5rem;
            background: var(--light-gray);
            color: var(--text-primary);
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-align: center;
            text-decoration: none;
            font-size: 0.95rem;
        }

        .btn-back:hover {
            background: #e8e6e1;
        }

        .message {
            padding: var(--spacing-md);
            border-radius: var(--border-radius);
            margin-bottom: var(--spacing-lg);
            display: none;
            align-items: center;
            gap: var(--spacing-sm);
            font-weight: 500;
        }

        .message.show {
            display: flex;
        }

        .message.success {
            background: rgba(76, 175, 80, 0.1);
            color: #2e7d32;
            border: 1px solid rgba(76, 175, 80, 0.3);
        }

        .message.error {
            background: rgba(211, 47, 47, 0.1);
            color: #c62828;
            border: 1px solid rgba(211, 47, 47, 0.3);
        }

        .message svg {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
        }

        @media (max-width: 768px) {
            .auth-container {
                flex-direction: column;
            }

            .auth-panel-left {
                padding: var(--spacing-xl);
                text-align: center;
            }

            .auth-panel-left h2 {
                font-size: 2rem;
            }

            .auth-panel-right {
                padding: var(--spacing-xl);
            }

            .auth-form-wrapper {
                max-width: 100%;
            }

            .form-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <!-- Left Panel -->
        <div class="auth-panel auth-panel-left">
            <h2>Admin Portal</h2>
            <p>Manage and oversee the PhotoMarket platform. Access administrative controls, monitor operations, and ensure smooth platform operations.</p>
        </div>

        <!-- Right Panel -->
        <div class="auth-panel auth-panel-right">
            <div class="auth-form-wrapper">
                <h3 class="auth-form-title">Admin Login</h3>
                <p class="auth-form-subtitle">Enter your credentials to access the admin dashboard</p>

                <!-- Messages -->
                <div id="successMessage" class="message success">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <span id="successText"></span>
                </div>
                <div id="errorMessage" class="message error">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <span id="errorText"></span>
                </div>

                <!-- Login Form -->
                <form id="adminLoginForm">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="admin@photomarket.com" required>
                        <span class="form-error" id="emailError"></span>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                        <span class="form-error" id="passwordError"></span>
                    </div>

                    <div class="form-actions">
                        <a href="<?php echo SITE_URL; ?>/index.php" class="btn-back">Back to Home</a>
                        <button type="submit" class="btn-submit" id="submitBtn">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('adminLoginForm');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const submitBtn = document.getElementById('submitBtn');

            form.addEventListener('submit', handleSubmit);

            function handleSubmit(e) {
                e.preventDefault();

                // Clear previous errors
                document.getElementById('emailError').textContent = '';
                document.getElementById('passwordError').textContent = '';
                document.getElementById('successMessage').classList.remove('show');
                document.getElementById('errorMessage').classList.remove('show');

                const email = emailInput.value.trim();
                const password = passwordInput.value;

                // Validation
                if (!email) {
                    document.getElementById('emailError').textContent = 'Email is required';
                    return;
                }

                if (!password) {
                    document.getElementById('passwordError').textContent = 'Password is required';
                    return;
                }

                // Disable button
                submitBtn.disabled = true;
                const originalText = submitBtn.textContent;
                submitBtn.textContent = 'Logging in...';

                const formData = new FormData();
                formData.append('csrf_token', document.querySelector('input[name="csrf_token"]').value);
                formData.append('email', email);
                formData.append('password', password);

                fetch('../actions/admin_login_action.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('successText').textContent = data.message;
                        document.getElementById('successMessage').classList.add('show');
                        setTimeout(() => {
                            window.location.href = '<?php echo SITE_URL; ?>/admin/dashboard.php';
                        }, 1500);
                    } else {
                        document.getElementById('errorText').textContent = data.message || 'Login failed';
                        document.getElementById('errorMessage').classList.add('show');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('errorText').textContent = 'Network error. Please try again.';
                    document.getElementById('errorMessage').classList.add('show');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                });
            }
        });
    </script>
</body>
</html>
