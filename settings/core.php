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
require_once __DIR__ . '/db_class.php';

// Define user types (enums from pb_users table)
if (!defined('USER_TYPE_CUSTOMER')) {
    define('USER_TYPE_CUSTOMER', 'customer');
    define('USER_TYPE_PHOTOGRAPHER', 'photographer');
    define('USER_TYPE_VENDOR', 'vendor');
}

// Define user status
if (!defined('USER_STATUS_ACTIVE')) {
    define('USER_STATUS_ACTIVE', 'active');
    define('USER_STATUS_SUSPENDED', 'suspended');
    define('USER_STATUS_PENDING', 'pending_approval');
}

// Define site paths
if (!defined('SITE_ROOT')) {
    define('SITE_ROOT', dirname(dirname(__FILE__)));
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    define('SITE_URL', $protocol . '://' . $host . '/~lady.hagan/glamphotobooth');
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
 * Get current user type
 */
function getCurrentUserType()
{
    return isset($_SESSION['user_type']) ? $_SESSION['user_type'] : null;
}

/**
 * Redirect user based on user type
 */
function redirectByUserType($userType)
{
    switch ($userType) {
        case USER_TYPE_PHOTOGRAPHER:
            header('Location: ' . SITE_URL . '/provider/dashboard.php');
            break;
        case USER_TYPE_VENDOR:
            header('Location: ' . SITE_URL . '/vendor/dashboard.php');
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
 * Require specific user type
 */
function requireUserType($userType)
{
    requireLogin();
    if (getCurrentUserType() != $userType) {
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
