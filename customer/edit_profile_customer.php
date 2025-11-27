<?php
/**
 * Edit Customer Profile Page
 * customer/edit_profile_customer.php
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

$error_message = '';
$success_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    $csrf_token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
    if (!isset($_SESSION['csrf_token']) || $csrf_token !== $_SESSION['csrf_token']) {
        $error_message = 'Security token expired. Please try again.';
    } else {
        // Get form data
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $country = isset($_POST['country']) ? trim($_POST['country']) : '';
        $city = isset($_POST['city']) ? trim($_POST['city']) : '';
        $contact = isset($_POST['contact']) ? trim($_POST['contact']) : '';

        // Validate
        if (empty($name) || strlen($name) < 2) {
            $error_message = 'Please enter a valid name (at least 2 characters).';
        } else {
            // Update customer
            if ($customer_class->update_customer($user_id, $name, $country, $city, $contact)) {
                $success_message = 'Profile updated successfully!';
                // Refresh customer data
                $customer = $customer_class->get_customer_by_id($user_id);
            } else {
                $error_message = 'Failed to update profile. Please try again.';
            }
        }
    }
}

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$pageTitle = 'Edit Profile - PhotoMarket';
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

        .form-group {
            margin-bottom: var(--spacing-lg);
        }

        .form-group:last-child {
            margin-bottom: 0;
        }

        .form-label {
            display: block;
            color: var(--text-secondary);
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: var(--spacing-xs);
        }

        .form-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            font-size: 1rem;
            font-family: inherit;
            transition: var(--transition);
            box-sizing: border-box;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(15, 43, 89, 0.1);
        }

        .message {
            padding: var(--spacing-md);
            border-radius: var(--border-radius);
            margin-bottom: var(--spacing-lg);
            font-weight: 500;
        }

        .message.error {
            background-color: #fee;
            color: #c00;
            border: 1px solid #f99;
        }

        .message.success {
            background-color: #efe;
            color: #060;
            border: 1px solid #9f9;
        }

        .profile-actions {
            display: flex;
            gap: var(--spacing-md);
            margin-top: var(--spacing-xl);
        }

        .btn-save {
            flex: 1;
            padding: 0.75rem 1.5rem;
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            font-size: 1rem;
        }

        .btn-save:hover {
            background: #0d1a3a;
            transform: translateY(-2px);
        }

        .btn-cancel {
            flex: 1;
            padding: 0.75rem 1.5rem;
            background: var(--light-gray);
            color: var(--text-primary);
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            font-size: 1rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-cancel:hover {
            background: #ddd;
            transform: translateY(-2px);
        }

        .read-only-field {
            color: var(--text-secondary);
            font-size: 0.9rem;
            padding: 0.75rem;
            background: var(--light-gray);
            border-radius: var(--border-radius);
            border: 1px solid var(--border-color);
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
        <aside class="dashboard-sidebar">
            <div class="sidebar-header">
                <div class="sidebar-brand">PhotoMarket</div>
                <div class="sidebar-user">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></div>
            </div>

            <nav>
                <ul class="sidebar-nav">
                    <li class="sidebar-nav-item">
                        <a href="<?php echo SITE_URL; ?>/customer/dashboard.php" class="sidebar-nav-link">
                            <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                <polyline points="9 22 9 12 15 12 15 22"></polyline>
                            </svg>
                            Dashboard
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
                        <a href="<?php echo SITE_URL; ?>/customer/my_bookings.php" class="sidebar-nav-link">
                            <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M6 9h12M6 9a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V9z"></path>
                            </svg>
                            My Bookings
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
                        <a href="<?php echo SITE_URL; ?>/customer/orders.php" class="sidebar-nav-link">
                            <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <circle cx="9" cy="21" r="1"></circle>
                                <circle cx="20" cy="21" r="1"></circle>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                            </svg>
                            My Orders
                        </a>
                    </li>
                </ul>

                <div class="sidebar-section">
                    <div class="sidebar-section-title">Account</div>
                    <ul class="sidebar-nav">
                        <li class="sidebar-nav-item">
                            <a href="<?php echo SITE_URL; ?>/customer/my_profile.php" class="sidebar-nav-link active">
                                <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                My Profile
                            </a>
                        </li>
                        <li class="sidebar-nav-item">
                            <a href="<?php echo SITE_URL; ?>/actions/logout.php" class="sidebar-nav-link">
                                <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                    <polyline points="16 17 21 12 16 7"></polyline>
                                    <line x1="21" y1="12" x2="9" y2="12"></line>
                                </svg>
                                Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="dashboard-content">
            <div class="profile-container">
                <div class="profile-header">
                    <h1>Edit Profile</h1>
                    <p style="color: var(--text-secondary);">Update your account information</p>
                </div>

                <?php if (!empty($error_message)): ?>
                    <div class="message error"><?php echo htmlspecialchars($error_message); ?></div>
                <?php endif; ?>

                <?php if (!empty($success_message)): ?>
                    <div class="message success"><?php echo htmlspecialchars($success_message); ?></div>
                <?php endif; ?>

                <div class="profile-card">
                    <form method="POST" action="">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

                        <div class="form-group">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-input" value="<?php echo htmlspecialchars($customer['name']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Email Address</label>
                            <div class="read-only-field"><?php echo htmlspecialchars($customer['email']); ?></div>
                            <p style="font-size: 0.85rem; color: var(--text-secondary); margin-top: var(--spacing-xs);">Email cannot be changed</p>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Country</label>
                            <input type="text" name="country" class="form-input" value="<?php echo htmlspecialchars($customer['country'] ?? ''); ?>" placeholder="e.g., Ghana">
                        </div>

                        <div class="form-group">
                            <label class="form-label">City</label>
                            <input type="text" name="city" class="form-input" value="<?php echo htmlspecialchars($customer['city'] ?? ''); ?>" placeholder="e.g., Accra">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Contact Number</label>
                            <input type="tel" name="contact" class="form-input" value="<?php echo htmlspecialchars($customer['contact'] ?? ''); ?>" placeholder="e.g., +233 XXX XXX XXX">
                        </div>

                        <div class="profile-actions">
                            <button type="submit" class="btn-save">Save Changes</button>
                            <a href="<?php echo SITE_URL; ?>/customer/my_profile.php" class="btn-cancel">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
