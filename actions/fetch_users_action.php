<?php
/**
 * Fetch Users Action
 * actions/fetch_users_action.php
 * Retrieves all users for admin panel
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';

requireLogin();

// Check if user is admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 1) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $customer_class = new customer_class();
    $users = $customer_class->get_all_customers();

    if ($users) {
        // Format users for display
        foreach ($users as &$user) {
            $user['email'] = htmlspecialchars($user['email']);
            $user['name'] = htmlspecialchars($user['name']);
        }
    }

    echo json_encode(['success' => true, 'users' => $users ?? []]);
} catch (Exception $e) {
    error_log('Fetch users error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error fetching users: ' . $e->getMessage()]);
}
exit;
?>
