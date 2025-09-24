<?php
// Configuration settings for the User Registration & Login System

// Application settings
define('APP_NAME', 'User Registration & Login System');
define('APP_VERSION', '1.0.0');
define('APP_ENVIRONMENT', 'development'); // development, production

// Security settings
define('PASSWORD_MIN_LENGTH', 6);
define('USERNAME_MIN_LENGTH', 3);
define('SESSION_TIMEOUT', 3600); // 1 hour in seconds

// File paths
define('USERS_FILE', __DIR__ . '/data/users.json');
define('LOGS_DIR', __DIR__ . '/logs/');

// Validation settings
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_ATTEMPT_TIMEOUT', 900); // 15 minutes in seconds

// Email settings (for future use)
define('SMTP_HOST', '');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', '');
define('SMTP_PASSWORD', '');
define('FROM_EMAIL', 'noreply@yoursite.com');
define('FROM_NAME', 'User Registration System');

// Error reporting
if (APP_ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Timezone
date_default_timezone_set('UTC');

// Create necessary directories
if (!is_dir(LOGS_DIR)) {
    mkdir(LOGS_DIR, 0755, true);
}

// Custom error handler for logging
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    $logMessage = date('Y-m-d H:i:s') . " - Error: [$errno] $errstr in $errfile on line $errline" . PHP_EOL;
    error_log($logMessage, 3, LOGS_DIR . 'error.log');
    
    if (APP_ENVIRONMENT === 'development') {
        echo "Error: $errstr in $errfile on line $errline";
    }
}

set_error_handler('customErrorHandler');
?>