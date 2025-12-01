<?php
/**
 * Payment Requests Page - Vendor
 * vendor/payment_requests.php
 */
require_once __DIR__ . '/../settings/core.php';

requireLogin();

// Check if user is vendor (role 3)
$user_role = isset($_SESSION['user_role']) ? intval($_SESSION['user_role']) : 4;
if ($user_role != 3) {
    header('Location: ' . SITE_URL . '/index.php');
    exit;
}

$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

// Get provider details
$provider_class = new provider_class();
$provider = $provider_class->get_provider_by_customer($user_id);

if (!$provider) {
    header('Location: ' . SITE_URL . '/vendor/profile_setup.php');
    exit;
}

// Get earnings data
require_once __DIR__ . '/../classes/commission_class.php';
require_once __DIR__ . '/../classes/payment_request_class.php';

$commission_class = new commission_class();
$payment_request_class = new payment_request_class();

$provider_id = intval($provider['provider_id']);
$total_earnings = $commission_class->get_provider_total_earnings($provider_id);
$available_earnings = $commission_class->get_provider_available_earnings($provider_id);
$requests = $payment_request_class->get_provider_requests($provider_id);

$pageTitle = 'Payment Requests - GlamPhotobooth Accra';
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="dashboard-layout">
        <!-- Sidebar -->
        <?php require_once __DIR__ . '/../views/dashboard_sidebar.php'; ?>

        <!-- Main Content -->
        <main class="dashboard-content">
            <div class="dashboard-header">
                <div>
                    <h1 class="dashboard-title">Payment Requests</h1>
                    <p class="dashboard-subtitle">Request payouts for your earnings</p>
                </div>
            </div>

            <!-- Earnings Summary -->
            <div class="stats-row">
                <div class="stat-card">
                    <div class="stat-label">Total Earnings</div>
                    <div class="stat-value">₵<?php echo number_format($total_earnings, 2); ?></div>
                    <div class="stat-change">From product sales</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Available Balance</div>
                    <div class="stat-value">₵<?php echo number_format($available_earnings, 2); ?></div>
                    <div class="stat-change">Ready to request</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Requested Amount</div>
                    <div class="stat-value">₵<?php echo number_format($total_earnings - $available_earnings, 2); ?></div>
                    <div class="stat-change">Pending/approved</div>
                </div>
            </div>

            <!-- Request Payment Form -->
            <?php if ($available_earnings > 0): ?>
            <div class="dashboard-card" style="margin-bottom: var(--spacing-lg);">
                <h2 style="color: var(--primary); margin-top: 0; margin-bottom: var(--spacing-lg);">Request Payment</h2>
                <form id="paymentRequestForm">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--spacing-lg); margin-bottom: var(--spacing-lg);">
                        <div>
                            <label style="display: block; margin-bottom: var(--spacing-sm); color: var(--primary); font-weight: 600;">Amount (₵)</label>
                            <input type="number" id="requested_amount" name="requested_amount" step="0.01" min="0.01" max="<?php echo $available_earnings; ?>" required
                                   style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--border-radius);"
                                   placeholder="0.00">
                            <small style="color: var(--text-secondary); font-size: 0.85rem;">Maximum: ₵<?php echo number_format($available_earnings, 2); ?></small>
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: var(--spacing-sm); color: var(--primary); font-weight: 600;">Payment Method</label>
                            <select id="payment_method" name="payment_method" required
                                    style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--border-radius);">
                                <option value="">Select method</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="mobile_money">Mobile Money</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div id="bankDetails" style="display: none; grid-template-columns: 1fr 1fr; gap: var(--spacing-lg); margin-bottom: var(--spacing-lg);">
                        <div>
                            <label style="display: block; margin-bottom: var(--spacing-sm); color: var(--primary); font-weight: 600;">Account Name</label>
                            <input type="text" id="account_name" name="account_name"
                                   style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--border-radius);">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: var(--spacing-sm); color: var(--primary); font-weight: 600;">Account Number</label>
                            <input type="text" id="account_number" name="account_number"
                                   style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--border-radius);">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: var(--spacing-sm); color: var(--primary); font-weight: 600;">Bank Name</label>
                            <input type="text" id="bank_name" name="bank_name"
                                   style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--border-radius);">
                        </div>
                    </div>

                    <div id="mobileMoneyDetails" style="display: none; grid-template-columns: 1fr 1fr; gap: var(--spacing-lg); margin-bottom: var(--spacing-lg);">
                        <div>
                            <label style="display: block; margin-bottom: var(--spacing-sm); color: var(--primary); font-weight: 600;">Mobile Number</label>
                            <input type="text" id="account_number_mobile" name="account_number"
                                   style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--border-radius);">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: var(--spacing-sm); color: var(--primary); font-weight: 600;">Network</label>
                            <select id="mobile_network" name="mobile_network"
                                    style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--border-radius);">
                                <option value="">Select network</option>
                                <option value="MTN">MTN</option>
                                <option value="Vodafone">Vodafone</option>
                                <option value="AirtelTigo">AirtelTigo</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">Submit Payment Request</button>
                </form>
            </div>
            <?php endif; ?>

            <!-- Payment Requests History -->
            <div>
                <h2 style="color: var(--primary); margin-bottom: var(--spacing-lg);">Request History</h2>
                <div class="dashboard-card">
                    <?php if ($requests && count($requests) > 0): ?>
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: rgba(226, 196, 146, 0.05);">
                                <th style="padding: var(--spacing-md); text-align: left; color: var(--primary); font-weight: 600; border-bottom: 2px solid rgba(226, 196, 146, 0.2);">Date</th>
                                <th style="padding: var(--spacing-md); text-align: left; color: var(--primary); font-weight: 600; border-bottom: 2px solid rgba(226, 196, 146, 0.2);">Amount</th>
                                <th style="padding: var(--spacing-md); text-align: left; color: var(--primary); font-weight: 600; border-bottom: 2px solid rgba(226, 196, 146, 0.2);">Method</th>
                                <th style="padding: var(--spacing-md); text-align: left; color: var(--primary); font-weight: 600; border-bottom: 2px solid rgba(226, 196, 146, 0.2);">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($requests as $request): ?>
                            <tr style="border-bottom: 1px solid rgba(226, 196, 146, 0.1);">
                                <td style="padding: var(--spacing-md);"><?php echo date('M d, Y', strtotime($request['requested_at'])); ?></td>
                                <td style="padding: var(--spacing-md); font-weight: 600; color: var(--primary);">₵<?php echo number_format($request['requested_amount'], 2); ?></td>
                                <td style="padding: var(--spacing-md); text-transform: capitalize;"><?php echo str_replace('_', ' ', $request['payment_method']); ?></td>
                                <td style="padding: var(--spacing-md);">
                                    <span class="status-badge status-<?php echo $request['status']; ?>"><?php echo ucfirst($request['status']); ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <p style="text-align: center; color: var(--text-secondary); padding: var(--spacing-xl);">No payment requests yet</p>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <style>
        .status-badge {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: capitalize;
        }
        .status-pending { background: rgba(255, 152, 0, 0.15); color: #f57f17; }
        .status-approved { background: rgba(33, 150, 243, 0.15); color: #0d47a1; }
        .status-paid { background: rgba(76, 175, 80, 0.15); color: #2e7d32; }
        .status-rejected { background: rgba(244, 67, 54, 0.15); color: #b71c1c; }
        .status-cancelled { background: rgba(158, 158, 158, 0.15); color: #424242; }
    </style>

    <script>
        window.siteUrl = '<?php echo SITE_URL; ?>';
        window.csrfToken = '<?php echo generateCSRFToken(); ?>';

        // Wait for DOM to be ready
        document.addEventListener('DOMContentLoaded', function() {
            const paymentMethodSelect = document.getElementById('payment_method');
            const paymentRequestForm = document.getElementById('paymentRequestForm');
            
            // Only add listeners if elements exist (form might not be rendered if no available earnings)
            if (paymentMethodSelect) {
                paymentMethodSelect.addEventListener('change', function() {
                    const method = this.value;
                    const bankDetails = document.getElementById('bankDetails');
                    const mobileMoneyDetails = document.getElementById('mobileMoneyDetails');
                    if (bankDetails) bankDetails.style.display = method === 'bank_transfer' ? 'grid' : 'none';
                    if (mobileMoneyDetails) mobileMoneyDetails.style.display = method === 'mobile_money' ? 'grid' : 'none';
                });
            }

            if (paymentRequestForm) {
                paymentRequestForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const paymentMethod = document.getElementById('payment_method').value;
                    const requestedAmount = document.getElementById('requested_amount').value;

                    // Validate amount
                    if (!requestedAmount || parseFloat(requestedAmount) <= 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Please enter a valid amount',
                            confirmButtonColor: '#102152'
                        });
                        return;
                    }

                    // Get account number based on payment method
                    let accountNumber = '';
                    if (paymentMethod === 'bank_transfer') {
                        accountNumber = document.getElementById('account_number').value;
                    } else if (paymentMethod === 'mobile_money') {
                        accountNumber = document.getElementById('account_number_mobile').value;
                    } else {
                        // For other, use account_number field if it exists
                        const accountField = document.getElementById('account_number');
                        if (accountField) {
                            accountNumber = accountField.value;
                        }
                    }

                    // Validate account number
                    if (!accountNumber || accountNumber.trim() === '') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Account number is required',
                            confirmButtonColor: '#102152'
                        });
                        return;
                    }

                    // Build form data
                    const formData = new FormData();
                    formData.append('requested_amount', requestedAmount);
                    formData.append('payment_method', paymentMethod);
                    formData.append('account_number', accountNumber);
                    formData.append('csrf_token', window.csrfToken);

                    // Add bank transfer specific fields
                    if (paymentMethod === 'bank_transfer') {
                        const accountName = document.getElementById('account_name').value;
                        const bankName = document.getElementById('bank_name').value;
                        if (accountName) formData.append('account_name', accountName);
                        if (bankName) formData.append('bank_name', bankName);
                    }

                    // Add mobile money specific fields
                    if (paymentMethod === 'mobile_money') {
                        const mobileNetwork = document.getElementById('mobile_network').value;
                        if (mobileNetwork) formData.append('mobile_network', mobileNetwork);
                    }

                    Swal.fire({
                        title: 'Submitting...',
                        text: 'Please wait',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    fetch(window.siteUrl + '/actions/create_payment_request_action.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: data.message,
                                confirmButtonColor: '#102152'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message || 'Failed to submit payment request',
                                confirmButtonColor: '#102152'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Network error. Please try again.',
                            confirmButtonColor: '#102152'
                        });
                    });
                });
        });
    </script>
</body>
</html>

