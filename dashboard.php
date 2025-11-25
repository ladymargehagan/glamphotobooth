<?php
/**
 * Main Dashboard Router
 * Redirects users to their appropriate dashboard based on role
 * dashboard.php
 */
require_once __DIR__ . '/settings/core.php';

// Check if logged in
if (!isLoggedIn()) {
    header('Location: ' . SITE_URL . '/auth/login.php');
    exit;
}

// Get user role from session
$user_role = isset($_SESSION['user_role']) ? intval($_SESSION['user_role']) : 4;

// Redirect based on role
switch ($user_role) {
    case 1: // Admin
        header('Location: ' . SITE_URL . '/customer/dashboard.php');
        break;
    case 2: // Photographer
        header('Location: ' . SITE_URL . '/photographer/dashboard.php');
        break;
    case 3: // Vendor
        header('Location: ' . SITE_URL . '/vendor/dashboard.php');
        break;
    case 4: // Customer
    default:
        header('Location: ' . SITE_URL . '/customer/dashboard.php');
        break;
}
exit;
?>

