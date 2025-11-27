<?php
/**
 * Manage Bookings Page
 * customer/manage_bookings.php
 * For photographers to view and manage their booking requests
 */
require_once __DIR__ . '/../settings/core.php';

requireLogin();

// Check if user is photographer (role 2)
if ($_SESSION['user_role'] != 2) {
    header('Location: ' . SITE_URL . '/index.php');
    exit;
}

$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$provider_class = new provider_class();
$provider = $provider_class->get_provider_by_customer($user_id);

if (!$provider) {
    header('Location: ' . SITE_URL . '/customer/profile_setup.php');
    exit;
}

// Get filter status
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';

// Get all bookings for this provider
$booking_class = new booking_class();
$all_bookings = $booking_class->get_provider_bookings($provider['provider_id']);

if (!is_array($all_bookings)) {
    $all_bookings = [];
}

// Filter bookings by status
$filtered_bookings = $all_bookings;
if ($status_filter !== 'all') {
    $filtered_bookings = array_filter($all_bookings, function($booking) use ($status_filter) {
        return $booking['status'] === $status_filter;
    });
}

// Get selected booking for details view
$selected_booking_id = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : 0;
$selected_booking = null;
if ($selected_booking_id > 0) {
    foreach ($all_bookings as $booking) {
        if (intval($booking['booking_id']) === $selected_booking_id) {
            $selected_booking = $booking;
            break;
        }
    }
}

$pageTitle = 'Manage Bookings - PhotoMarket';
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
    <!-- SweetAlert2 Library -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .filter-tabs {
            display: flex;
            gap: var(--spacing-sm);
            margin-bottom: var(--spacing-lg);
            flex-wrap: wrap;
        }

        .filter-tab {
            padding: 0.5rem 1rem;
            background: var(--white);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            text-decoration: none;
            color: var(--text-primary);
            font-weight: 500;
            transition: var(--transition);
        }

        .filter-tab:hover {
            background: var(--light-gray);
        }

        .filter-tab.active {
            background: var(--primary);
            color: var(--white);
            border-color: var(--primary);
        }

        .booking-card {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: var(--spacing-lg);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            margin-bottom: var(--spacing-lg);
            border-left: 4px solid var(--primary);
        }

        .booking-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: var(--spacing-md);
        }

        .booking-customer {
            flex: 1;
        }

        .booking-customer h3 {
            color: var(--primary);
            font-weight: 600;
            margin: 0 0 var(--spacing-xs) 0;
        }

        .booking-customer p {
            color: var(--text-secondary);
            margin: 0;
            font-size: 0.9rem;
        }

        .status-badge {
            display: inline-block;
            padding: 0.4rem 0.8rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: capitalize;
        }

        .status-pending {
            background: rgba(255, 152, 0, 0.15);
            color: #f57f17;
        }

        .status-confirmed, .status-accepted {
            background: rgba(76, 175, 80, 0.15);
            color: #2e7d32;
        }

        .status-completed {
            background: rgba(33, 150, 243, 0.15);
            color: #0d47a1;
        }

        .status-rejected, .status-cancelled {
            background: rgba(244, 67, 54, 0.15);
            color: #b71c1c;
        }

        .booking-details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: var(--spacing-md);
            margin-bottom: var(--spacing-md);
        }

        .detail-item {
            display: flex;
            flex-direction: column;
        }

        .detail-label {
            color: var(--text-secondary);
            font-size: 0.85rem;
            margin-bottom: 4px;
            font-weight: 500;
        }

        .detail-value {
            color: var(--text-primary);
            font-weight: 600;
        }

        .booking-description {
            background: rgba(226, 196, 146, 0.05);
            padding: var(--spacing-md);
            border-radius: var(--border-radius);
            margin-bottom: var(--spacing-md);
        }

        .booking-description p {
            color: var(--text-secondary);
            font-size: 0.9rem;
            line-height: 1.5;
            margin: 0;
        }

        .booking-actions {
            display: flex;
            gap: var(--spacing-sm);
            flex-wrap: wrap;
        }

        .btn-action {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.85rem;
            text-decoration: none;
            display: inline-block;
        }

        .btn-accept {
            background: #2e7d32;
            color: var(--white);
        }

        .btn-accept:hover {
            background: #1b5e20;
        }

        .btn-reject {
            background: #c62828;
            color: var(--white);
        }

        .btn-reject:hover {
            background: #b71c1c;
        }

        .btn-complete {
            background: #1976d2;
            color: var(--white);
        }

        .btn-complete:hover {
            background: #0d47a1;
        }

        .btn-view {
            background: var(--primary);
            color: var(--white);
        }

        .btn-view:hover {
            background: #0d1a3a;
        }

        .empty-state {
            text-align: center;
            padding: var(--spacing-xxl);
            background: var(--white);
            border-radius: var(--border-radius);
        }

        .empty-state-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto var(--spacing-lg);
            color: var(--text-secondary);
        }

        .message {
            padding: var(--spacing-md);
            border-radius: var(--border-radius);
            margin-bottom: var(--spacing-lg);
            display: none;
        }

        .message.show {
            display: block;
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
                        <a href="<?php echo SITE_URL; ?>/photographer/dashboard.php" class="sidebar-nav-link">
                            <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                <polyline points="9 22 9 12 15 12 15 22"></polyline>
                            </svg>
                            Dashboard
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
                        <a href="<?php echo SITE_URL; ?>/customer/manage_bookings.php" class="sidebar-nav-link active">
                            <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M6 9h12M6 9a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V9z"></path>
                            </svg>
                            Booking Requests
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
                        <a href="<?php echo SITE_URL; ?>/customer/manage_products.php" class="sidebar-nav-link">
                            <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <rect x="3" y="3" width="18" height="18" rx="2"></rect>
                                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                <polyline points="21 15 16 10 5 21"></polyline>
                            </svg>
                            My Products
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
                        <a href="<?php echo SITE_URL; ?>/customer/manage_bookings.php?status=completed" class="sidebar-nav-link">
                            <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <rect x="3" y="3" width="18" height="18" rx="2"></rect>
                                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                <polyline points="21 15 16 10 5 21"></polyline>
                            </svg>
                            Client Galleries
                        </a>
                    </li>
                </ul>

                <div class="sidebar-section">
                    <div class="sidebar-section-title">Business</div>
                    <ul class="sidebar-nav">
                        <li class="sidebar-nav-item">
                            <a href="<?php echo SITE_URL; ?>/customer/edit_profile.php" class="sidebar-nav-link">
                                <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                Edit Business Profile
                            </a>
                        </li>
                        <li class="sidebar-nav-item">
                            <a href="<?php echo SITE_URL; ?>/customer/earnings.php" class="sidebar-nav-link">
                                <svg class="sidebar-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <line x1="12" y1="1" x2="12" y2="23"></line>
                                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                </svg>
                                Earnings
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
            <div class="dashboard-header">
                <div>
                    <h1 class="dashboard-title">Manage Bookings</h1>
                    <p class="dashboard-subtitle">Review and respond to client booking requests</p>
                </div>
            </div>

            <div id="messageBox" class="message"></div>

            <!-- Filter Tabs -->
            <div class="filter-tabs">
                <a href="?status=all" class="filter-tab <?php echo $status_filter === 'all' ? 'active' : ''; ?>">All</a>
                <a href="?status=pending" class="filter-tab <?php echo $status_filter === 'pending' ? 'active' : ''; ?>">Pending</a>
                <a href="?status=confirmed" class="filter-tab <?php echo $status_filter === 'confirmed' ? 'active' : ''; ?>">Confirmed</a>
                <a href="?status=completed" class="filter-tab <?php echo $status_filter === 'completed' ? 'active' : ''; ?>">Completed</a>
            </div>

            <!-- Bookings List -->
            <?php if (count($filtered_bookings) > 0): ?>
                <div>
                    <?php foreach ($filtered_bookings as $booking): ?>
                        <div class="booking-card">
                            <div class="booking-header">
                                <div class="booking-customer">
                                    <h3><?php echo htmlspecialchars($booking['customer_name'] ?? 'Customer'); ?></h3>
                                    <p><?php echo htmlspecialchars($booking['email'] ?? 'No email'); ?></p>
                                </div>
                                <span class="status-badge status-<?php echo htmlspecialchars($booking['status']); ?>">
                                    <?php echo htmlspecialchars($booking['status']); ?>
                                </span>
                            </div>

                            <div class="booking-details-grid">
                                <div class="detail-item">
                                    <span class="detail-label">Date</span>
                                    <span class="detail-value"><?php echo date('M d, Y', strtotime($booking['booking_date'])); ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Time</span>
                                    <span class="detail-value"><?php echo date('g:i A', strtotime($booking['booking_time'])); ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Phone</span>
                                    <span class="detail-value"><?php echo htmlspecialchars($booking['contact'] ?? 'N/A'); ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Service</span>
                                    <span class="detail-value"><?php echo htmlspecialchars($booking['product_title'] ?? 'N/A'); ?></span>
                                </div>
                            </div>

                            <?php if (!empty($booking['service_description'])): ?>
                                <div class="booking-description">
                                    <p><strong>Service Description:</strong> <?php echo htmlspecialchars($booking['service_description']); ?></p>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($booking['notes'])): ?>
                                <div class="booking-description">
                                    <p><strong>Notes:</strong> <?php echo htmlspecialchars($booking['notes']); ?></p>
                                </div>
                            <?php endif; ?>

                            <div class="booking-actions">
                                <?php if ($booking['status'] === 'pending'): ?>
                                    <button class="btn-action btn-accept" onclick="updateBookingStatus(<?php echo $booking['booking_id']; ?>, 'confirmed')">Accept</button>
                                    <button class="btn-action btn-reject" onclick="updateBookingStatus(<?php echo $booking['booking_id']; ?>, 'rejected')">Reject</button>
                                <?php elseif ($booking['status'] === 'confirmed' || $booking['status'] === 'accepted'): ?>
                                    <button class="btn-action btn-complete" onclick="updateBookingStatus(<?php echo $booking['booking_id']; ?>, 'completed')">Mark as Completed</button>
                                <?php endif; ?>
                                
                                <?php if ($booking['status'] === 'completed'): ?>
                                    <a href="<?php echo SITE_URL; ?>/customer/upload_photos.php?booking_id=<?php echo $booking['booking_id']; ?>" class="btn-action btn-view">Upload Photos</a>
                                <?php endif; ?>
                                
                                <a href="?booking_id=<?php echo $booking['booking_id']; ?>&status=<?php echo $status_filter; ?>" class="btn-action btn-view">View Details</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <svg class="empty-state-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M6 9h12M6 9a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V9z"></path>
                    </svg>
                    <h3 class="empty-state-title">No Bookings Found</h3>
                    <p class="empty-state-text">
                        <?php if ($status_filter !== 'all'): ?>
                            You don't have any <?php echo htmlspecialchars($status_filter); ?> bookings.
                        <?php else: ?>
                            You don't have any bookings yet. Your clients will see your profile once you add services!
                        <?php endif; ?>
                    </p>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

    <script src="<?php echo SITE_URL; ?>/js/sweetalert.js"></script>
    <script>
        window.siteUrl = '<?php echo SITE_URL; ?>';

        function updateBookingStatus(bookingId, status) {
            showConfirmAlert(
                'Confirm Action',
                'Are you sure you want to ' + status + ' this booking?',
                function() {
                    processBookingUpdate(bookingId, status);
                }
            );
        }

        function processBookingUpdate(bookingId, status) {

            const csrfToken = document.querySelector('input[name="csrf_token"]').value;
            const formData = new FormData();
            formData.append('booking_id', bookingId);
            formData.append('status', status);
            formData.append('csrf_token', csrfToken);

            fetch(window.siteUrl + '/actions/update_booking_status_action.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage(data.message || 'Booking status updated successfully', 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showMessage(data.message || 'Failed to update booking status', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('Network error. Please try again.', 'error');
            });
        }

        function showMessage(message, type) {
            const msgBox = document.getElementById('messageBox');
            msgBox.textContent = message;
            msgBox.className = 'message show ' + type;
            setTimeout(() => {
                msgBox.classList.remove('show');
            }, 5000);
        }

        // Show message from URL parameter
        <?php if (isset($_GET['message'])): ?>
            showMessage('<?php echo htmlspecialchars($_GET['message']); ?>', 'success');
        <?php endif; ?>
    </script>
</body>
</html>

