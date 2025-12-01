<?php
/**
 * Fetch Users Action
 * actions/fetch_users_action.php
 * Retrieves all users for admin panel (all roles: admins, photographers, vendors, customers)
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';

requireLogin();

// Check if user is admin
if (!isset($_SESSION['user_role']) || intval($_SESSION['user_role']) !== 1) {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized - Admin access required'
    ]);
    exit;
}

try {
    $customer_class = new customer_class();
    $users = $customer_class->get_all_users();

    if ($users === false) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: Failed to fetch users'
        ]);
        exit;
    }

    // Format users for display
    if ($users && is_array($users)) {
        $roleNames = [1 => 'Admin', 2 => 'Photographer', 3 => 'Vendor', 4 => 'Customer'];

        foreach ($users as &$user) {
            $user['email'] = htmlspecialchars($user['email']);
            $user['name'] = htmlspecialchars($user['name']);
            $user['role_name'] = isset($roleNames[$user['user_role']]) ? $roleNames[$user['user_role']] : 'Unknown';
        }
    }

    echo json_encode([
        'success' => true,
        'users' => $users ?? [],
        'count' => count($users ?? []),
        'message' => 'Users fetched successfully'
    ]);
} catch (Exception $e) {
    error_log('Fetch users error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching users: ' . $e->getMessage()
    ]);
}
exit;
?>
