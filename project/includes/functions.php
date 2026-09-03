<?php
/**
 * functions.php
 * Reusable helper functions shared across the application:
 * input sanitization, CSRF token handling, validation, and small utilities.
 */

/**
 * Escape a string for safe output in HTML context (XSS protection).
 */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Trim and strip tags from raw user input.
 */
function cleanInput(?string $value): string
{
    return trim(strip_tags($value ?? ''));
}

/**
 * Generate (or reuse) a CSRF token for the current session.
 */
function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Render a hidden CSRF input field for forms.
 */
function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrfToken()) . '">';
}

/**
 * Validate a submitted CSRF token against the session token.
 */
function verifyCsrfToken(?string $submittedToken): bool
{
    if (empty($_SESSION['csrf_token']) || empty($submittedToken)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $submittedToken);
}

/**
 * Basic email format validation.
 */
function isValidEmail(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Redirect helper.
 */
function redirectTo(string $path): void
{
    header('Location: ' . $path);
    exit;
}

/**
 * Determine the currently active navigation page for highlighting purposes.
 */
function currentPage(): string
{
    return basename($_SERVER['PHP_SELF']);
}

/**
 * Log an application-level error message.
 */
function logError(string $message): void
{
    error_log('[' . date('Y-m-d H:i:s') . '] ' . $message);
}
