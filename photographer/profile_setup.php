<?php
/**
 * Provider Profile Setup
 * customer/profile_setup.php
 */
require_once __DIR__ . '/../settings/core.php';

// Require login and photographer/vendor role
requireLogin();
$user_role = isset($_SESSION['user_role']) ? intval($_SESSION['user_role']) : 4;
if ($user_role == 4) { // Regular customer
    header('Location: ' . SITE_URL . '/customer/dashboard.php');
    exit;
}

$pageTitle = 'Complete Your Profile - GlamPhotobooth Accra';
$cssPath = SITE_URL . '/css/style.css';
$dashboardCss = SITE_URL . '/css/dashboard.css';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($cssPath); ?>">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($dashboardCss); ?>">
    <style>
        .profile-setup-container {
            max-width: 600px;
            margin: 0 auto;
            padding: var(--spacing-xxl) var(--spacing-xl);
        }

        .profile-setup-header {
            text-align: center;
            margin-bottom: var(--spacing-xxl);
        }

        .profile-setup-header h1 {
            color: var(--primary);
            font-family: var(--font-serif);
            font-size: 2rem;
            margin-bottom: var(--spacing-sm);
        }

        .profile-setup-header p {
            color: var(--text-secondary);
            font-size: 1rem;
        }

        .profile-card {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: var(--spacing-xl);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
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

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            font-size: 0.95rem;
            font-family: var(--font-sans);
            transition: var(--transition);
            background: var(--white);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 120px;
            font-family: var(--font-sans);
        }

        .form-group input:focus,
        .form-group textarea:focus {
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

        .form-hint {
            display: block;
            color: var(--text-secondary);
            font-size: 0.85rem;
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

        .btn-cancel {
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

        .btn-cancel:hover {
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
            .profile-setup-container {
                padding: var(--spacing-lg);
            }

            .profile-setup-header h1 {
                font-size: 1.5rem;
            }

            .profile-card {
                padding: var(--spacing-lg);
            }

            .form-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="profile-setup-container">
        <div class="profile-setup-header">
            <h1>Complete Your Profile</h1>
            <p>Help clients find you by completing your professional profile</p>
        </div>

        <div class="profile-card">
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

            <!-- Profile Form -->
            <form id="profileForm">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

                <div class="form-group">
                    <label for="businessName">Business Name</label>
                    <input type="text" id="businessName" name="business_name" placeholder="Your studio or business name" required>
                    <span class="form-error" id="businessNameError"></span>
                    <span class="form-hint">3-255 characters</span>
                </div>

                <div class="form-group">
                    <label for="description">About Your Business</label>
                    <textarea id="description" name="description" placeholder="Tell clients about your services, experience, and what makes you special..." required></textarea>
                    <span class="form-error" id="descriptionError"></span>
                    <span class="form-hint">10-5000 characters</span>
                </div>

                <div class="form-group">
                    <label for="hourlyRate">Hourly Rate (₵)</label>
                    <input type="number" id="hourlyRate" name="hourly_rate" placeholder="e.g., 150.00" step="0.01" min="0.01" required>
                    <span class="form-error" id="hourlyRateError"></span>
                    <span class="form-hint">Your base hourly rate for services</span>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="window.history.back()">Skip for Now</button>
                    <button type="submit" class="btn-submit" id="submitBtn">Complete Profile</button>
                </div>
            </form>
        </div>
    </div>

    <script src="<?php echo SITE_URL; ?>/js/profile.js"></script>
</body>
</html>
