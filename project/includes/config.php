<?php
/**
 * config.php
 * Central configuration file for database connection and application settings.
 *
 * IMPORTANT: This file uses environment variables for configuration.
 * Set these in your hosting platform (Render, cPanel, etc.) or in a .env file for local development.
 * Never commit real credentials to a public repository.
 */

//
// Load environment variables from .env file if it exists (for local development)
//
$envPath = __DIR__ . '/../../.env';
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) continue;
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            // Remove quotes if present
            $value = trim($value, '"\'');
            if (!array_key_exists($key, $_SERVER) && !array_key_exists($key, $_ENV)) {
                putenv("$key=$value");
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        }
    }
}

//
// Environment / Error Reporting
//

// Set to false in production to avoid leaking stack traces to end users.
// Can be overridden via APP_DEBUG environment variable
define('APP_DEBUG', filter_var($_ENV['APP_DEBUG'] ?? $_SERVER['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN));

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
// All values can be overridden via environment variables
// Render automatically provides these when using a managed database
//
define('DB_HOST', $_ENV['DB_HOST'] ?? $_SERVER['DB_HOST'] ?? 'localhost');
define('DB_PORT', $_ENV['DB_PORT'] ?? $_SERVER['DB_PORT'] ?? '3306');
define('DB_NAME', $_ENV['DB_NAME'] ?? $_SERVER['DB_NAME'] ?? 'corporate_site');
define('DB_USER', $_ENV['DB_USER'] ?? $_SERVER['DB_USER'] ?? 'db_username');
define('DB_PASS', $_ENV['DB_PASS'] ?? $_SERVER['DB_PASS'] ?? 'db_password');
define('DB_CHARSET', $_ENV['DB_CHARSET'] ?? $_SERVER['DB_CHARSET'] ?? 'utf8mb4');

//
// Application constants
//
define('APP_NAME', $_ENV['APP_NAME'] ?? $_SERVER['APP_NAME'] ?? 'Ocktova');
define('SESSION_TIMEOUT_SECONDS', (int)($_ENV['SESSION_TIMEOUT_SECONDS'] ?? $_SERVER['SESSION_TIMEOUT_SECONDS'] ?? 1800)); // 30 minutes of inactivity

//
// Secure session configuration (must run before session_start())
//
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');

    // Enable the secure flag automatically when served over HTTPS.
    // Render terminates SSL at the load balancer, so check for X-Forwarded-Proto
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
    
    if ($isHttps) {
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
        $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            // Enable persistent connections for better performance on Render
            PDO::ATTR_PERSISTENT         => true,
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
