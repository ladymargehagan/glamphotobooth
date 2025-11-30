<?php
/**
 * Admin - Manage Users Page
 * admin/manage_users.php
 */
require_once __DIR__ . '/../settings/core.php';

requireAdmin();

$pageTitle = 'Manage Users - PhotoMarket Admin';
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
</head>
<body>
    <div class="dashboard-layout">
        <!-- Sidebar -->
        <?php require_once __DIR__ . '/../views/admin_sidebar.php'; ?>

        <!-- Main Content -->
        <main class="dashboard-content">
            <div class="dashboard-header">
                <div>
                    <h1 class="dashboard-title">User Management</h1>
                    <p class="dashboard-subtitle">Manage all platform users</p>
                </div>
            </div>

            <!-- Search and Filter -->
            <div class="dashboard-card" style="margin-bottom: var(--spacing-lg);">
                <div class="search-filter">
                    <input type="text" id="searchInput" class="search-input" placeholder="Search users by name or email...">
                    <select id="roleFilter" class="role-filter">
                        <option value="">All Roles</option>
                        <option value="1">Admin</option>
                        <option value="2">Photographer</option>
                        <option value="3">Vendor</option>
                        <option value="4">Customer</option>
                    </select>
                </div>
            </div>

            <!-- Users Table -->
            <div class="dashboard-card">
                <table class="users-table" id="usersTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="7" style="text-align: center; padding: 20px;">Loading users...</td></tr>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <style>
        .search-filter {
            display: flex;
            gap: var(--spacing-md);
            flex-wrap: wrap;
        }

        .search-input,
        .role-filter {
            padding: 0.75rem 1rem;
            border: 1px solid rgba(226, 196, 146, 0.3);
            border-radius: var(--border-radius);
            font-family: inherit;
            font-size: 0.95rem;
        }

        .search-input {
            flex: 1;
            min-width: 200px;
        }

        .search-input:focus,
        .role-filter:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(92, 154, 173, 0.1);
        }

        .users-table {
            width: 100%;
            border-collapse: collapse;
        }

        .users-table th {
            background: rgba(226, 196, 146, 0.05);
            padding: var(--spacing-md);
            text-align: left;
            font-weight: 600;
            color: var(--primary);
            border-bottom: 2px solid rgba(226, 196, 146, 0.2);
        }

        .users-table td {
            padding: var(--spacing-md);
            border-bottom: 1px solid rgba(226, 196, 146, 0.1);
        }

        .users-table tr:last-child td {
            border-bottom: none;
        }

        .role-badge {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: capitalize;
        }

        .role-admin {
            background: rgba(244, 67, 54, 0.15);
            color: #b71c1c;
        }

        .role-photographer {
            background: rgba(33, 150, 243, 0.15);
            color: #0d47a1;
        }

        .role-vendor {
            background: rgba(76, 175, 80, 0.15);
            color: #2e7d32;
        }

        .role-customer {
            background: rgba(255, 152, 0, 0.15);
            color: #f57f17;
        }

        .status-badge {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-active {
            background: rgba(76, 175, 80, 0.15);
            color: #2e7d32;
        }

        .status-inactive {
            background: rgba(189, 189, 189, 0.15);
            color: #424242;
        }

        .action-btn {
            padding: 0.4rem 0.8rem;
            margin: 0 2px;
            border: none;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-view {
            background: var(--primary);
            color: white;
        }

        .btn-view:hover {
            background: #0d1a3a;
        }

        .btn-delete {
            background: #f44336;
            color: white;
        }

        .btn-delete:hover {
            background: #d32f2f;
        }

        @media (max-width: 768px) {
            .search-filter {
                flex-direction: column;
            }

            .search-input,
            .role-filter {
                width: 100%;
            }

            .users-table {
                font-size: 0.9rem;
            }

            .users-table th,
            .users-table td {
                padding: var(--spacing-sm);
            }

            .action-btn {
                padding: 0.3rem 0.6rem;
                font-size: 0.75rem;
            }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?php echo SITE_URL; ?>/js/admin.js"></script>
    <script>
        window.siteUrl = '<?php echo SITE_URL; ?>';
        window.csrfToken = '<?php echo generateCSRFToken(); ?>';

        document.addEventListener('DOMContentLoaded', function() {
            loadUsers();
            setupFilters();
        });

        function setupFilters() {
            document.getElementById('searchInput').addEventListener('input', filterUsers);
            document.getElementById('roleFilter').addEventListener('change', filterUsers);
        }

        function loadUsers() {
            fetch(window.siteUrl + '/actions/fetch_users_action.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayUsers(data.users);
                    } else {
                        console.error('Failed to load users:', data.message);
                    }
                })
                .catch(error => console.error('Error loading users:', error));
        }

        function displayUsers(users) {
            const tbody = document.querySelector('#usersTable tbody');
            if (!users || users.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 20px;">No users found</td></tr>';
                return;
            }

            const roleNames = {
                1: 'Admin',
                2: 'Photographer',
                3: 'Vendor',
                4: 'Customer'
            };

            tbody.innerHTML = users.map(user => `
                <tr>
                    <td>#${user.id}</td>
                    <td>${user.name}</td>
                    <td>${user.email}</td>
                    <td><span class="role-badge role-${Object.keys(roleNames).find(k => roleNames[k].toLowerCase() === getRoleName(user.user_role).toLowerCase())}">${getRoleName(user.user_role)}</span></td>
                    <td><span class="status-badge status-active">Active</span></td>
                    <td>${new Date(user.created_at).toLocaleDateString()}</td>
                    <td>
                        <button class="action-btn btn-view" onclick="viewUser(${user.id})">View</button>
                        <button class="action-btn btn-delete" onclick="deleteUser(${user.id}, '${user.name}')">Delete</button>
                    </td>
                </tr>
            `).join('');
        }

        function getRoleName(roleId) {
            const roles = {
                1: 'Admin',
                2: 'Photographer',
                3: 'Vendor',
                4: 'Customer'
            };
            return roles[roleId] || 'Unknown';
        }

        function filterUsers() {
            const searchText = document.getElementById('searchInput').value.toLowerCase();
            const roleFilter = document.getElementById('roleFilter').value;
            const rows = document.querySelectorAll('#usersTable tbody tr');

            rows.forEach(row => {
                const name = row.cells[1]?.textContent.toLowerCase() || '';
                const email = row.cells[2]?.textContent.toLowerCase() || '';
                const role = row.cells[3]?.textContent.toLowerCase() || '';

                const matchesSearch = name.includes(searchText) || email.includes(searchText);
                const matchesRole = roleFilter === '' || role.includes(roleFilter);

                row.style.display = (matchesSearch && matchesRole) ? '' : 'none';
            });
        }

        function viewUser(userId) {
            alert('View user #' + userId + ' - Feature coming soon');
        }

        function deleteUser(userId, userName) {
            Swal.fire({
                title: 'Delete User?',
                html: `Are you sure you want to delete <strong>${userName}</strong>? This action is permanent and will also delete all associated records (orders, bookings, reviews, etc.).`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f44336',
                cancelButtonColor: '#757575',
                confirmButtonText: 'Yes, Delete User',
                cancelButtonText: 'Cancel',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    performDeleteUser(userId, userName);
                }
            });
        }

        function performDeleteUser(userId, userName) {
            const formData = new FormData();
            formData.append('user_id', userId);
            formData.append('csrf_token', window.csrfToken);

            fetch(window.siteUrl + '/actions/delete_user_action.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Deleted!',
                        text: `User "${userName}" has been deleted successfully.`,
                        icon: 'success',
                        confirmButtonColor: '#5c9aad'
                    }).then(() => {
                        loadUsers();
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message || 'Failed to delete user',
                        icon: 'error',
                        confirmButtonColor: '#5c9aad'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'An error occurred while deleting the user',
                    icon: 'error',
                    confirmButtonColor: '#5c9aad'
                });
            });
        }
    </script>
</body>
</html>
