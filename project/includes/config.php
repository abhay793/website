<?php
/**
 * config.php
 * Central configuration file for database connection and application settings.
 *
 * IMPORTANT: Update the constants below with your live server credentials
 * before deploying to production. Never commit real credentials to a
 * public repository.
 */

//
// Environment / Error Reporting
//

// Set to false in production to avoid leaking stack traces to end users.
define('APP_DEBUG', false);

if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// Log errors to a file instead of displaying them to visitors.
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/../logs/app_error.log');

//
// Database credentials
//

// NOTE: 'db' matches the service name defined in docker-compose.yml
// (was 'localhost' when using XAMPP — Docker containers are separate
// machines talking over a network, so they use the service name instead)
define('DB_HOST', 'db');
define('DB_NAME', 'corporate_site');
define('DB_USER', 'db_username');
define('DB_PASS', 'db_password');
define('DB_CHARSET', 'utf8mb4');

//
// Application constants
//
define('APP_NAME', 'Ocktova');
define('SESSION_TIMEOUT_SECONDS', 1800); // 30 minutes of inactivity

//
// Secure session configuration (must run before session_start())
//
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');

    // Enable the secure flag automatically when served over HTTPS.
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        ini_set('session.cookie_secure', '1');
    }

    session_start();
}

//
// PDO Database Connection (singleton)
//
function getDbConnection(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log('Database connection failed: ' . $e->getMessage());
            http_response_code(500);
            die('A server error occurred. Please try again later.');
        }
    }

    return $pdo;
}
