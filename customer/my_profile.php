<?php
/**
 * Customer Profile Page
 * customer/my_profile.php
 */
require_once __DIR__ . '/../settings/core.php';

requireLogin();

// Only allow customers (role 4)
if ($_SESSION['user_role'] != 4) {
    header('Location: ' . SITE_URL . '/index.php');
    exit;
}

$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

// Get customer info
if (!class_exists('customer_class')) {
    require_once __DIR__ . '/../classes/customer_class.php';
}
$customer_class = new customer_class();
$customer = $customer_class->get_customer_by_id($user_id);

if (!$customer) {
    header('Location: ' . SITE_URL . '/index.php');
    exit;
}

$pageTitle = 'My Profile - GlamPhotobooth Accra';
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
        .profile-container {
            max-width: 800px;
            margin: 0 auto;
            padding: var(--spacing-xxl) var(--spacing-xl);
        }

        .profile-header {
            text-align: center;
            margin-bottom: var(--spacing-xxl);
        }

        .profile-header h1 {
            color: var(--primary);
            font-family: var(--font-serif);
            font-size: 2rem;
            margin-bottom: var(--spacing-sm);
        }

        .profile-card {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: var(--spacing-xl);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            margin-bottom: var(--spacing-lg);
        }

        .profile-field {
            margin-bottom: var(--spacing-lg);
            padding-bottom: var(--spacing-lg);
            border-bottom: 1px solid var(--border-color);
        }

        .profile-field:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .field-label {
            color: var(--text-secondary);
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: var(--spacing-xs);
        }

        .field-value {
            color: var(--text-primary);
            font-size: 1.1rem;
            font-weight: 500;
        }

        .field-value.empty {
            color: var(--text-secondary);
            font-style: italic;
        }

        .profile-actions {
            display: flex;
            gap: var(--spacing-md);
        }

        .btn-edit {
            flex: 1;
            padding: 0.75rem 1.5rem;
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-edit:hover {
            background: #0d1a3a;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .profile-container {
                padding: var(--spacing-lg);
            }

            .profile-header h1 {
                font-size: 1.5rem;
            }

            .profile-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-layout">
        <!-- Sidebar -->
        <?php require_once __DIR__ . '/../views/dashboard_sidebar.php'; ?>

        <!-- Main Content -->
        <main class="dashboard-content">
            <div class="profile-container">
                <div class="profile-header">
                    <h1>My Profile</h1>
                    <p style="color: var(--text-secondary);">View and manage your account information</p>
                </div>

                <div class="profile-card">
                    <div class="profile-field">
                        <div class="field-label">Full Name</div>
                        <div class="field-value"><?php echo htmlspecialchars($customer['name']); ?></div>
                    </div>

                    <div class="profile-field">
                        <div class="field-label">Email Address</div>
                        <div class="field-value"><?php echo htmlspecialchars($customer['email']); ?></div>
                    </div>

                    <div class="profile-field">
                        <div class="field-label">Country</div>
                        <div class="field-value <?php echo empty($customer['country']) ? 'empty' : ''; ?>">
                            <?php echo !empty($customer['country']) ? htmlspecialchars($customer['country']) : 'Not provided'; ?>
                        </div>
                    </div>

                    <div class="profile-field">
                        <div class="field-label">City</div>
                        <div class="field-value <?php echo empty($customer['city']) ? 'empty' : ''; ?>">
                            <?php echo !empty($customer['city']) ? htmlspecialchars($customer['city']) : 'Not provided'; ?>
                        </div>
                    </div>

                    <div class="profile-field">
                        <div class="field-label">Contact Number</div>
                        <div class="field-value <?php echo empty($customer['contact']) ? 'empty' : ''; ?>">
                            <?php echo !empty($customer['contact']) ? htmlspecialchars($customer['contact']) : 'Not provided'; ?>
                        </div>
                    </div>

                    <div class="profile-field">
                        <div class="field-label">Member Since</div>
                        <div class="field-value"><?php echo date('M d, Y', strtotime($customer['created_at'])); ?></div>
                    </div>

                    <div class="profile-actions">
                        <a href="<?php echo SITE_URL; ?>/customer/edit_profile_customer.php" class="btn-edit">Edit Profile</a>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
