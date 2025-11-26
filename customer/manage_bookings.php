<?php
/**
 * Provider Manage Bookings Page
 * customer/manage_bookings.php
 */
require_once __DIR__ . '/../settings/core.php';

requireLogin();

$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

// Check if user is a photographer/provider (role 2) or vendor (role 3)
$customer_class = new customer_class();
$customer = $customer_class->get_customer_by_id($user_id);

if (!$customer || ($customer['user_role'] != 2 && $customer['user_role'] != 3)) {
    header('Location: ' . SITE_URL . '/index.php');
    exit;
}

// Get provider details
$provider_class = new provider_class();
$provider = $provider_class->get_provider_by_customer($user_id);

if (!$provider) {
    header('Location: ' . SITE_URL . '/customer/dashboard.php');
    exit;
}

// Get provider bookings
$booking_class = new booking_class();
$bookings = $booking_class->get_provider_bookings($provider['provider_id']);
$stats = $booking_class->get_provider_stats($provider['provider_id']);

$pageTitle = 'Manage Bookings - PhotoMarket';
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
        .bookings-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: var(--spacing-xxl) var(--spacing-xl);
        }

        .bookings-header {
            margin-bottom: var(--spacing-xxl);
        }

        .bookings-header h1 {
            color: var(--primary);
            font-family: var(--font-serif);
            font-size: 2rem;
            margin-bottom: var(--spacing-lg);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: var(--spacing-lg);
            margin-bottom: var(--spacing-xxl);
        }

        .stat-card {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: var(--spacing-lg);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            text-align: center;
        }

        .stat-value {
            color: var(--primary);
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: var(--spacing-sm);
        }

        .stat-label {
            color: var(--text-secondary);
            font-size: 0.9rem;
            font-weight: 500;
        }

        .booking-card {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: var(--spacing-lg);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            margin-bottom: var(--spacing-lg);
            border-left: 4px solid var(--primary);
        }

        .booking-card.pending {
            border-left-color: #ff9800;
        }

        .booking-card.confirmed {
            border-left-color: #4caf50;
        }

        .booking-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: var(--spacing-md);
        }

        .customer-info {
            color: var(--primary);
            font-weight: 600;
            font-size: 1.05rem;
        }

        .booking-status {
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

        .status-confirmed {
            background: rgba(76, 175, 80, 0.15);
            color: #2e7d32;
        }

        .booking-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: var(--spacing-md);
            margin-bottom: var(--spacing-md);
        }

        .detail {
            font-size: 0.9rem;
        }

        .detail-label {
            color: var(--text-secondary);
            font-weight: 500;
            margin-bottom: 2px;
        }

        .detail-value {
            color: var(--text-primary);
            font-weight: 600;
        }

        .booking-description {
            background: rgba(226, 196, 146, 0.05);
            padding: var(--spacing-md);
            border-radius: var(--border-radius);
            font-size: 0.9rem;
            line-height: 1.6;
            color: var(--text-secondary);
            margin-bottom: var(--spacing-md);
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
        }

        .btn-confirm {
            background: #4caf50;
            color: var(--white);
        }

        .btn-confirm:hover {
            background: #388e3c;
        }

        .btn-reject {
            background: #f44336;
            color: var(--white);
        }

        .btn-reject:hover {
            background: #d32f2f;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: var(--white);
            padding: var(--spacing-lg);
            border-radius: var(--border-radius);
            max-width: 500px;
            width: 90%;
        }

        .modal-header {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: var(--spacing-lg);
        }

        .modal-body {
            margin-bottom: var(--spacing-lg);
        }

        .form-group {
            margin-bottom: var(--spacing-md);
        }

        .form-group label {
            display: block;
            margin-bottom: var(--spacing-xs);
            color: var(--text-primary);
            font-weight: 500;
        }

        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            font-family: inherit;
            min-height: 80px;
        }

        .modal-footer {
            display: flex;
            gap: var(--spacing-sm);
        }

        .btn-cancel {
            flex: 1;
            padding: 0.75rem;
            background: var(--light-gray);
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
        }

        .btn-submit {
            flex: 1;
            padding: 0.75rem;
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
        }

        .empty-state {
            text-align: center;
            padding: var(--spacing-xxl);
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        @media (max-width: 768px) {
            .bookings-container {
                padding: var(--spacing-lg);
            }

            .bookings-header h1 {
                font-size: 1.5rem;
            }

            .booking-actions {
                flex-direction: column;
            }

            .btn-action {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../views/header.php'; ?>

    <div class="bookings-container">
        <div class="bookings-header">
            <h1>Manage Bookings</h1>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?php echo intval($stats['total_bookings'] ?? 0); ?></div>
                <div class="stat-label">Total Bookings</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo intval($stats['pending'] ?? 0); ?></div>
                <div class="stat-label">Pending</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo intval($stats['confirmed'] ?? 0); ?></div>
                <div class="stat-label">Confirmed</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo intval($stats['completed'] ?? 0); ?></div>
                <div class="stat-label">Completed</div>
            </div>
        </div>

        <!-- Bookings List -->
        <?php if ($bookings && count($bookings) > 0): ?>
            <?php foreach ($bookings as $booking): ?>
                <div class="booking-card <?php echo strtolower($booking['status']); ?>">
                    <div class="booking-header">
                        <div class="customer-info">
                            <?php echo htmlspecialchars($booking['customer_name'] ?? 'Customer'); ?>
                            <div style="font-size: 0.85rem; font-weight: 400; color: var(--text-secondary); margin-top: 2px;">
                                <?php echo htmlspecialchars($booking['email'] ?? ''); ?>
                            </div>
                        </div>
                        <span class="booking-status status-<?php echo strtolower($booking['status']); ?>">
                            <?php echo htmlspecialchars($booking['status']); ?>
                        </span>
                    </div>

                    <div class="booking-details">
                        <div class="detail">
                            <div class="detail-label">Date</div>
                            <div class="detail-value"><?php echo date('M d, Y', strtotime($booking['booking_date'])); ?></div>
                        </div>
                        <div class="detail">
                            <div class="detail-label">Time</div>
                            <div class="detail-value"><?php echo date('g:i A', strtotime($booking['booking_time'])); ?></div>
                        </div>
                        <div class="detail">
                            <div class="detail-label">Phone</div>
                            <div class="detail-value"><?php echo htmlspecialchars($booking['contact'] ?? 'N/A'); ?></div>
                        </div>
                    </div>

                    <div class="booking-description">
                        <strong>Service Requested:</strong><br>
                        <?php echo htmlspecialchars($booking['service_description']); ?>
                        <?php if (!empty($booking['notes'])): ?>
                            <br><br><strong>Notes:</strong><br> <?php echo htmlspecialchars($booking['notes']); ?>
                        <?php endif; ?>
                    </div>

                    <div class="booking-actions">
                        <?php if ($booking['status'] === 'pending'): ?>
                            <button class="btn-action btn-confirm" onclick="openConfirmModal(<?php echo $booking['booking_id']; ?>)">Accept</button>
                            <button class="btn-action btn-reject" onclick="openRejectModal(<?php echo $booking['booking_id']; ?>)">Reject</button>
                        <?php elseif ($booking['status'] === 'confirmed'): ?>
                            <button class="btn-action btn-confirm" onclick="completeBooking(<?php echo $booking['booking_id']; ?>)">Mark Complete</button>
                        <?php elseif ($booking['status'] === 'completed'): ?>
                            <a href="<?php echo SITE_URL; ?>/customer/upload_photos.php?booking_id=<?php echo $booking['booking_id']; ?>" class="btn-action btn-confirm" style="text-decoration: none; display: inline-block;">📸 Upload Photos</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <p style="font-size: 3rem; margin-bottom: var(--spacing-lg);">📭</p>
                <h3 style="color: var(--primary); font-size: 1.25rem; font-weight: 600; margin-bottom: var(--spacing-sm);">No Bookings</h3>
                <p style="color: var(--text-secondary);">You don't have any booking requests yet. Keep adding services to get more bookings!</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Confirm Modal -->
    <div class="modal" id="confirmModal">
        <div class="modal-content">
            <div class="modal-header">Accept Booking</div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="confirmNote">Message (Optional)</label>
                    <textarea id="confirmNote" placeholder="Send a message to the customer..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-cancel" onclick="closeModal('confirmModal')">Cancel</button>
                <button class="btn-submit" onclick="submitConfirm()">Accept</button>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal" id="rejectModal">
        <div class="modal-content">
            <div class="modal-header">Reject Booking</div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="rejectNote">Reason (Optional)</label>
                    <textarea id="rejectNote" placeholder="Explain why you're rejecting this booking..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-cancel" onclick="closeModal('rejectModal')">Cancel</button>
                <button class="btn-submit" onclick="submitReject()">Reject</button>
            </div>
        </div>
    </div>

    <?php require_once __DIR__ . '/../views/footer.php'; ?>

    <script>
        window.siteUrl = '<?php echo SITE_URL; ?>';
        let currentBookingId = null;

        function openConfirmModal(bookingId) {
            currentBookingId = bookingId;
            document.getElementById('confirmModal').classList.add('show');
        }

        function openRejectModal(bookingId) {
            currentBookingId = bookingId;
            document.getElementById('rejectModal').classList.add('show');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('show');
            currentBookingId = null;
        }

        function submitConfirm() {
            const note = document.getElementById('confirmNote').value;
            updateBookingStatus('confirmed', note);
            closeModal('confirmModal');
        }

        function submitReject() {
            const note = document.getElementById('rejectNote').value;
            updateBookingStatus('rejected', note);
            closeModal('rejectModal');
        }

        function completeBooking(bookingId) {
            currentBookingId = bookingId;
            updateBookingStatus('completed', '');
        }

        function updateBookingStatus(status, note) {
            const csrfToken = document.querySelector('input[name="csrf_token"]')?.value;
            const formData = new FormData();
            formData.append('booking_id', currentBookingId);
            formData.append('status', status);
            formData.append('response_note', note);
            formData.append('csrf_token', csrfToken);

            fetch(window.siteUrl + '/actions/update_booking_status_action.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Failed to update booking');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating booking');
            });
        }

        // Close modal when clicking outside
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.remove('show');
                }
            });
        });
    </script>
</body>
</html>
