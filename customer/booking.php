<?php
/**
 * Booking Page
 * customer/booking.php
 */
require_once __DIR__ . '/../settings/core.php';

requireLogin();

$provider_id = isset($_GET['provider_id']) ? intval($_GET['provider_id']) : 0;

if ($provider_id <= 0) {
    header('Location: ' . SITE_URL . '/shop.php');
    exit;
}

// Get provider details
$provider_class = new provider_class();
$provider = $provider_class->get_provider_by_id($provider_id);

if (!$provider) {
    header('Location: ' . SITE_URL . '/shop.php');
    exit;
}

$pageTitle = 'Book ' . htmlspecialchars($provider['business_name']) . ' - PhotoMarket';
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
        .booking-container {
            max-width: 900px;
            margin: 0 auto;
            padding: var(--spacing-xxl) var(--spacing-xl);
        }

        .booking-header {
            text-align: center;
            margin-bottom: var(--spacing-xxl);
        }

        .booking-header h1 {
            color: var(--primary);
            font-family: var(--font-serif);
            font-size: 2rem;
            margin-bottom: var(--spacing-sm);
        }

        .booking-layout {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: var(--spacing-xl);
        }

        .booking-form {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: var(--spacing-lg);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .form-section {
            margin-bottom: var(--spacing-lg);
            padding-bottom: var(--spacing-lg);
            border-bottom: 1px solid var(--border-color);
        }

        .form-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .form-section h2 {
            color: var(--primary);
            font-size: 1.1rem;
            margin-bottom: var(--spacing-md);
            font-weight: 600;
        }

        .form-group {
            margin-bottom: var(--spacing-md);
        }

        .form-group label {
            display: block;
            margin-bottom: var(--spacing-xs);
            color: var(--text-primary);
            font-weight: 500;
            font-size: 0.9rem;
        }

        .form-group input[type="date"],
        .form-group input[type="time"],
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            font-size: 0.95rem;
            font-family: inherit;
            transition: var(--transition);
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(226, 196, 146, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .time-slots {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: var(--spacing-sm);
            margin-top: var(--spacing-md);
        }

        .time-slot {
            padding: 0.75rem;
            border: 2px solid var(--border-color);
            border-radius: var(--border-radius);
            background: var(--white);
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
            font-weight: 500;
            font-size: 0.9rem;
        }

        .time-slot:hover {
            border-color: var(--primary);
            background: rgba(226, 196, 146, 0.05);
        }

        .time-slot.selected {
            border-color: var(--primary);
            background: var(--primary);
            color: var(--white);
        }

        .time-slot.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background: var(--light-gray);
        }

        .btn-book {
            width: 100%;
            padding: 1rem;
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.95rem;
            margin-top: var(--spacing-lg);
        }

        .btn-book:hover {
            background: #0d1a3a;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 33, 82, 0.2);
        }

        .btn-book:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }

        .provider-info {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: var(--spacing-lg);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            height: fit-content;
        }

        .provider-name {
            color: var(--primary);
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: var(--spacing-sm);
        }

        .provider-detail {
            display: flex;
            justify-content: space-between;
            margin-bottom: var(--spacing-md);
            font-size: 0.9rem;
            padding-bottom: var(--spacing-md);
            border-bottom: 1px solid var(--border-color);
        }

        .provider-detail:last-child {
            border-bottom: none;
        }

        .provider-label {
            color: var(--text-secondary);
        }

        .provider-value {
            color: var(--text-primary);
            font-weight: 500;
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

        .message.error {
            background: rgba(211, 47, 47, 0.1);
            color: #c62828;
            border: 1px solid rgba(211, 47, 47, 0.3);
        }

        .message.success {
            background: rgba(76, 175, 80, 0.1);
            color: #2e7d32;
            border: 1px solid rgba(76, 175, 80, 0.3);
        }

        @media (max-width: 768px) {
            .booking-container {
                padding: var(--spacing-lg);
            }

            .booking-header h1 {
                font-size: 1.5rem;
            }

            .booking-layout {
                grid-template-columns: 1fr;
            }

            .time-slots {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../views/header.php'; ?>

    <div class="booking-container">
        <div class="booking-header">
            <h1>Book a Session</h1>
            <p>with <?php echo htmlspecialchars($provider['business_name']); ?></p>
        </div>

        <div id="errorMessage" class="message error"></div>
        <div id="successMessage" class="message success"></div>

        <div class="booking-layout">
            <!-- Booking Form -->
            <div class="booking-form">
                <form id="bookingForm">
                    <!-- Date & Time Section -->
                    <div class="form-section">
                        <h2>When would you like to book?</h2>
                        
                        <div class="form-group">
                            <label for="bookingDate">Select Date</label>
                            <input type="date" id="bookingDate" name="bookingDate" required>
                        </div>

                        <div class="form-group">
                            <label>Available Time Slots</label>
                            <div class="time-slots" id="timeSlots">
                                <p style="grid-column: 1/-1; text-align: center; color: var(--text-secondary);">Please select a date first</p>
                            </div>
                            <input type="hidden" id="bookingTime" name="bookingTime" value="">
                        </div>
                    </div>

                    <!-- Service Details Section -->
                    <div class="form-section">
                        <h2>Service Details</h2>
                        
                        <div class="form-group">
                            <label for="serviceDescription">What service do you need? *</label>
                            <textarea id="serviceDescription" name="serviceDescription" placeholder="Describe the photography service you need (e.g., Wedding photoshoot, Product photography, etc.)" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="notes">Additional Notes</label>
                            <textarea id="notes" name="notes" placeholder="Any additional information or special requests..."></textarea>
                        </div>
                    </div>

                    <!-- Hidden Fields -->
                    <input type="hidden" id="providerId" name="provider_id" value="<?php echo $provider_id; ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

                    <!-- Submit Button -->
                    <button type="submit" class="btn-book" id="submitBtn">Request Booking</button>
                </form>
            </div>

            <!-- Provider Info Sidebar -->
            <div class="provider-info">
                <div class="provider-name"><?php echo htmlspecialchars($provider['business_name']); ?></div>
                
                <div class="provider-detail">
                    <span class="provider-label">Rating</span>
                    <span class="provider-value">
                        <?php if ($provider['rating'] > 0): ?>
                            ⭐ <?php echo number_format($provider['rating'], 1); ?>
                        <?php else: ?>
                            New Provider
                        <?php endif; ?>
                    </span>
                </div>

                <?php if ($provider['total_reviews'] > 0): ?>
                    <div class="provider-detail">
                        <span class="provider-label">Reviews</span>
                        <span class="provider-value"><?php echo intval($provider['total_reviews']); ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($provider['description'])): ?>
                    <div class="provider-detail">
                        <span class="provider-label">About</span>
                    </div>
                    <p style="font-size: 0.9rem; color: var(--text-secondary); line-height: 1.5;">
                        <?php echo htmlspecialchars(substr($provider['description'], 0, 100)); ?>...
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php require_once __DIR__ . '/../views/footer.php'; ?>

    <script src="<?php echo SITE_URL; ?>/js/booking.js"></script>
</body>
</html>
