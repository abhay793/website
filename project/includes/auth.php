<?php
/**
 * auth.php
 * Authentication middleware. Include this at the top of any page that
 * requires a logged-in client. Handles session validation, idle timeout,
 * and session fixation protection.
 *
 * Usage: require_once __DIR__ . '/auth.php';
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

/**
 * Guard a page: redirect to login if the client is not authenticated,
 * or if their session has expired due to inactivity.
 */
function requireClientLogin(): void
{
    // Not logged in at all.
    if (empty($_SESSION['client_id']) || empty($_SESSION['authenticated'])) {
        redirectTo('/client/login.php?reason=auth');
    }

    // Enforce idle session timeout.
    if (!empty($_SESSION['last_activity']) &&
        (time() - $_SESSION['last_activity']) > SESSION_TIMEOUT_SECONDS) {
        session_unset();
        session_destroy();
        redirectTo('/client/login.php?reason=timeout');
    }

    $_SESSION['last_activity'] = time();
}

/**
 * Regenerate the session ID to prevent session fixation attacks.
 * Call this immediately after a successful login.
 */
function regenerateSession(): void
{
    session_regenerate_id(true);
}
