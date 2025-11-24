<?php
/**
 * Core Configuration and Auto-Loader
 * settings/core.php
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database credentials
require_once __DIR__ . '/db_cred.php';

// Include database class
require_once __DIR__ . '/../db/db_class.php';

// Define user roles
if (!defined('ROLE_CUSTOMER')) {
    define('ROLE_CUSTOMER', 1);
    define('ROLE_ADMIN', 2);
    define('ROLE_PROVIDER', 3);
}

// Define user status
if (!defined('STATUS_ACTIVE')) {
    define('STATUS_ACTIVE', 'active');
    define('STATUS_INACTIVE', 'inactive');
    define('STATUS_SUSPENDED', 'suspended');
}

// Define site paths
if (!defined('SITE_ROOT')) {
    define('SITE_ROOT', dirname(dirname(__FILE__)));
    define('SITE_URL', 'http://localhost/glamphotobooth');
    define('UPLOADS_DIR', SITE_ROOT . '/uploads/');
}

/**
 * Auto-load classes from classes folder
 */
spl_autoload_register(function ($class) {
    $file = SITE_ROOT . '/classes/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

/**
 * Check if user is logged in
 */
function isLoggedIn()
{
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get current user ID
 */
function getCurrentUserId()
{
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

/**
 * Get current user role
 */
function getCurrentUserRole()
{
    return isset($_SESSION['user_role']) ? $_SESSION['user_role'] : null;
}

/**
 * Redirect user based on role
 */
function redirectByRole($role)
{
    switch ($role) {
        case ROLE_ADMIN:
            header('Location: ' . SITE_URL . '/admin/dashboard.php');
            break;
        case ROLE_PROVIDER:
            header('Location: ' . SITE_URL . '/provider/dashboard.php');
            break;
        default:
            header('Location: ' . SITE_URL . '/customer/dashboard.php');
    }
    exit();
}

/**
 * Require login middleware
 */
function requireLogin()
{
    if (!isLoggedIn()) {
        header('Location: ' . SITE_URL . '/login/login.php');
        exit();
    }
}

/**
 * Require specific role
 */
function requireRole($role)
{
    requireLogin();
    if (getCurrentUserRole() != $role) {
        header('Location: ' . SITE_URL . '/index.php');
        exit();
    }
}

/**
 * Sanitize input
 */
function sanitize($input)
{
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Hash password
 */
function hashPassword($password)
{
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

/**
 * Verify password
 */
function verifyPassword($password, $hash)
{
    return password_verify($password, $hash);
}

/**
 * Validate email
 */
function isValidEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Generate CSRF token
 */
function generateCSRFToken()
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token)
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>
