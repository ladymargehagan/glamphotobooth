<?php
/**
 * Configuration File Template
 * Copy this file to config.php and update with your settings
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'glam_photobooth_db');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_CHARSET', 'utf8mb4');

// Site Configuration
define('SITE_NAME', 'Glam PhotoBooth Accra');
define('SITE_URL', 'http://localhost/ECommerceFinalProject');
define('SITE_EMAIL', 'info@glamphotobooth.com');

// File Upload Configuration
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

// Session Configuration
define('SESSION_LIFETIME', 3600); // 1 hour
define('REMEMBER_ME_DURATION', 30 * 24 * 3600); // 30 days

// Security
define('HASH_ALGO', PASSWORD_BCRYPT);
define('HASH_COST', 12);

// Payment Gateway (Paystack/Stripe)
define('PAYMENT_GATEWAY', 'paystack'); // or 'stripe'
define('PAYSTACK_SECRET_KEY', 'your_paystack_secret_key');
define('PAYSTACK_PUBLIC_KEY', 'your_paystack_public_key');

// Email Configuration (SMTP)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your_email@gmail.com');
define('SMTP_PASS', 'your_password');
define('SMTP_ENCRYPTION', 'tls');

// Application Settings
define('DEFAULT_TIMEZONE', 'Africa/Accra');
define('CURRENCY', 'GH₵');
define('COMMISSION_RATE', 0.15); // 15% platform commission

// User Roles
define('ROLE_ADMIN', 'admin');
define('ROLE_PROVIDER', 'provider');
define('ROLE_CUSTOMER', 'customer');

// Pagination
define('ITEMS_PER_PAGE', 20);

// Error Reporting
define('DISPLAY_ERRORS', false); // Set to true in development
define('LOG_ERRORS', true);
define('ERROR_LOG_FILE', __DIR__ . '/../logs/error.log');

// API Keys (if needed)
define('GOOGLE_MAPS_API_KEY', 'your_google_maps_key');
define('CLOUDINARY_CLOUD_NAME', 'your_cloudinary_cloud_name');
define('CLOUDINARY_API_KEY', 'your_cloudinary_api_key');
define('CLOUDINARY_API_SECRET', 'your_cloudinary_api_secret');

// Set timezone
date_default_timezone_set(DEFAULT_TIMEZONE);

// Error reporting based on environment
if (DISPLAY_ERRORS) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}
