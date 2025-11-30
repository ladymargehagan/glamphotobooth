<?php
/**
 * Admin - Payment Requests Management
 * admin/payment_requests.php
 */
require_once __DIR__ . '/../settings/core.php';

requireAdmin();

$pageTitle = 'Payment Requests - GlamPhotobooth Accra Admin';
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
        <?php require_once __DIR__ . '/../views/admin_sidebar.php'; ?>

        <!-- Main Content -->
        <main class="dashboard-content">
            <div class="dashboard-header">
                <div>
                    <h1 class="dashboard-title">Payment Requests</h1>
                    <p class="dashboard-subtitle">Manage vendor and photographer payout requests</p>
                </div>
            </div>

            <!-- Filter -->
            <div class="dashboard-card" style="margin-bottom: var(--spacing-lg);">
                <div class="search-filter">
                    <select id="statusFilter" class="status-filter">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="paid">Paid</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
            </div>

            <!-- Payment Requests Table -->
            <div class="dashboard-card">
                <table class="orders-table" id="requestsTable">
                    <thead>
                        <tr>
                            <th>Request ID</th>
                            <th>Provider</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Payment Method</th>
                            <th>Account Details</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="9" style="text-align: center; padding: 20px;">Loading requests...</td></tr>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Request Details Modal -->
    <div id="requestModal" class="modal">
        <div class="modal-overlay" onclick="closeRequestModal()"></div>
        <div class="modal-content">
            <div class="modal-header">
                <h2>Payment Request Details</h2>
                <button type="button" class="modal-close" onclick="closeRequestModal()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <div class="modal-body" id="requestDetailsContent">
                <p style="text-align: center; color: var(--text-secondary);">Loading...</p>
            </div>
        </div>
    </div>

    <style>
        .search-filter {
            display: flex;
            gap: var(--spacing-md);
            align-items: center;
        }
        .status-filter {
            padding: 0.75rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            font-family: var(--font-sans);
            font-size: 0.95rem;
        }
        .orders-table {
            width: 100%;
            border-collapse: collapse;
        }
        .orders-table th {
            background: rgba(226, 196, 146, 0.05);
            padding: var(--spacing-md);
            text-align: left;
            font-weight: 600;
            color: var(--primary);
            border-bottom: 2px solid rgba(226, 196, 146, 0.2);
            font-size: 0.9rem;
        }
        .orders-table td {
            padding: var(--spacing-md);
            border-bottom: 1px solid rgba(226, 196, 146, 0.1);
            color: var(--text-primary);
            font-size: 0.9rem;
        }
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
        .action-btn {
            padding: 0.4rem 0.9rem;
            margin: 0 0.15rem;
            border: none;
            border-radius: var(--border-radius);
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }
        .btn-approve {
            background: #2196f3;
            color: white;
        }
        .btn-pay {
            background: #4caf50;
            color: white;
        }
        .btn-reject {
            background: #f44336;
            color: white;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        .modal.active {
            display: flex;
        }
        .modal-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: -1;
        }
        .modal-content {
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 90%;
            max-height: 85vh;
            overflow-y: auto;
            position: relative;
            z-index: 1001;
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: var(--spacing-lg);
            border-bottom: 1px solid var(--border-color);
        }
        .modal-close {
            background: none;
            border: none;
            cursor: pointer;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary);
        }
        .modal-body {
            padding: var(--spacing-lg);
        }
    </style>

    <script>
        window.siteUrl = '<?php echo SITE_URL; ?>';
        window.csrfToken = '<?php echo generateCSRFToken(); ?>';

        document.addEventListener('DOMContentLoaded', function() {
            loadRequests();
            document.getElementById('statusFilter').addEventListener('change', loadRequests);
        });

        function loadRequests() {
            const status = document.getElementById('statusFilter').value;
            const url = window.siteUrl + '/actions/fetch_payment_requests_action.php' + (status ? '?status=' + status : '');
            
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayRequests(data.requests);
                    } else {
                        console.error('Failed to load requests:', data.message);
                    }
                })
                .catch(error => console.error('Error loading requests:', error));
        }

        function displayRequests(requests) {
            const tbody = document.querySelector('#requestsTable tbody');
            if (!requests || requests.length === 0) {
                tbody.innerHTML = '<tr><td colspan="9" style="text-align: center; padding: 20px;">No payment requests found</td></tr>';
                return;
            }

            tbody.innerHTML = requests.map(req => {
                const providerName = (req.business_name || req.provider_name || 'Provider #' + req.provider_id).replace(/"/g, '&quot;').replace(/'/g, '&#39;');
                const providerType = req.user_role == 2 ? 'Photographer' : 'Vendor';
                const accountInfo = (req.payment_method === 'bank_transfer'
                    ? (req.account_number ? req.account_number + (req.bank_name ? ' (' + req.bank_name + ')' : '') : 'N/A')
                    : (req.payment_method === 'mobile_money'
                        ? (req.account_number ? req.account_number + (req.mobile_network ? ' (' + req.mobile_network + ')' : '') : 'N/A')
                        : req.account_number || 'N/A')).replace(/"/g, '&quot;').replace(/'/g, '&#39;');

                let actions = '';
                if (req.status === 'pending') {
                    actions = `
                        <button class="action-btn btn-approve" onclick="updateRequest(${req.request_id}, 'approved')">Approve</button>
                        <button class="action-btn btn-reject" onclick="updateRequest(${req.request_id}, 'rejected')">Reject</button>
                    `;
                } else if (req.status === 'approved') {
                    actions = `<button class="action-btn btn-pay" onclick="markAsPaid(${req.request_id})">Mark as Paid</button>`;
                }

                return `
                    <tr>
                        <td>#${req.request_id}</td>
                        <td>${providerName}</td>
                        <td>${providerType}</td>
                        <td style="font-weight: 600; color: var(--primary);">₵${parseFloat(req.requested_amount).toFixed(2)}</td>
                        <td style="text-transform: capitalize;">${req.payment_method.replace('_', ' ')}</td>
                        <td style="font-size: 0.85rem;">${accountInfo}</td>
                        <td><span class="status-badge status-${req.status}">${req.status}</span></td>
                        <td>${new Date(req.requested_at).toLocaleDateString()}</td>
                        <td>${actions}</td>
                    </tr>
                `;
            }).join('');
        }

        function updateRequest(requestId, status) {
            const action = status === 'approved' ? 'approve' : 'reject';
            Swal.fire({
                title: `${action.charAt(0).toUpperCase() + action.slice(1)} Request?`,
                text: `Are you sure you want to ${action} this payment request?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: status === 'approved' ? '#2196f3' : '#f44336',
                cancelButtonColor: '#6c757d',
                confirmButtonText: `Yes, ${action}`,
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('request_id', requestId);
                    formData.append('status', status);
                    formData.append('csrf_token', window.csrfToken);

                    fetch(window.siteUrl + '/actions/update_payment_request_action.php', {
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
                            }).then(() => loadRequests());
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message || 'Failed to update request',
                                confirmButtonColor: '#102152'
                            });
                        }
                    });
                }
            });
        }

        function markAsPaid(requestId) {
            Swal.fire({
                title: 'Mark as Paid?',
                html: `
                    <input id="paymentRef" class="swal2-input" placeholder="Payment Reference (Optional)">
                    <textarea id="adminNotes" class="swal2-textarea" placeholder="Notes (Optional)"></textarea>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4caf50',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Mark as Paid',
                cancelButtonText: 'Cancel',
                preConfirm: () => {
                    return {
                        payment_reference: document.getElementById('paymentRef').value,
                        admin_notes: document.getElementById('adminNotes').value
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('request_id', requestId);
                    formData.append('status', 'paid');
                    formData.append('payment_reference', result.value.payment_reference);
                    formData.append('admin_notes', result.value.admin_notes);
                    formData.append('csrf_token', window.csrfToken);

                    fetch(window.siteUrl + '/actions/update_payment_request_action.php', {
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
                            }).then(() => loadRequests());
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message || 'Failed to update request',
                                confirmButtonColor: '#102152'
                            });
                        }
                    });
                }
            });
        }

        function closeRequestModal() {
            document.getElementById('requestModal').classList.remove('active');
        }
    </script>
</body>
</html>

