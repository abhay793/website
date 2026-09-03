<?php
/**
 * logout.php
 * Fully terminates the client session: clears session data, destroys the
 * session, and expires the session cookie client-side.
 */
require_once __DIR__ . '/../includes/config.php';

$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

session_destroy();

header('Location: /client/login.php');
exit;
