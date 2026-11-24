<?php
/**
 * Sécurité commune Ta3m Bledi : sessions, expiration d'inactivité et CSRF.
 */

declare(strict_types=1);

const TA3M_SESSION_IDLE_TIMEOUT = 1200; // 20 minutes

function startTa3mSecureSession(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    // Session commune : la connexion Front Office peut rediriger un ADMIN vers le Back Office.
    // Les rôles et guards assurent la séparation des autorisations.
    session_name('TA3M_BLEDI_SESSION');

    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $https,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_start();

    $now = time();
    $lastActivity = (int)($_SESSION['ta3m_last_activity'] ?? 0);
    if ($lastActivity > 0 && ($now - $lastActivity) > TA3M_SESSION_IDLE_TIMEOUT) {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', [
                'expires' => time() - 42000,
                'path' => $params['path'] ?: '/',
                'domain' => $params['domain'],
                'secure' => $params['secure'],
                'httponly' => $params['httponly'],
                'samesite' => $params['samesite'] ?? 'Lax',
            ]);
        }
        session_regenerate_id(true);
        $_SESSION['session_expired'] = true;
    }

    $_SESSION['ta3m_last_activity'] = $now;
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' .
        htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') . '">';
}

function verifyCsrf(): void
{
    $sent = $_POST['csrf_token'] ?? '';
    $known = $_SESSION['csrf_token'] ?? '';
    if (!is_string($sent) || !is_string($known) || $known === '' || !hash_equals($known, $sent)) {
        http_response_code(403);
        exit('Jeton de sécurité invalide ou expiré. Rechargez la page puis réessayez.');
    }
}

function destroyTa3mSession(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        startTa3mSecureSession();
    }
    $_SESSION = [];
    $params = session_get_cookie_params();
    setcookie(session_name(), '', [
        'expires' => time() - 42000,
        'path' => $params['path'] ?: '/',
        'domain' => $params['domain'],
        'secure' => $params['secure'],
        'httponly' => $params['httponly'],
        'samesite' => $params['samesite'] ?? 'Lax',
    ]);
    session_destroy();
}

startTa3mSecureSession();
// Toutes les requêtes POST des pages qui chargent ce fichier doivent présenter un CSRF valide.
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (!empty($_SESSION['session_expired'])) {
        $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
        if (strpos($script, '/View/BackOffice/') !== false) {
            header('Location: index.php?page=login');
        } else {
            $marker = '/View/';
            $pos = strpos($script, $marker);
            $base = $pos === false ? '' : substr($script, 0, $pos);
            header('Location: ' . $base . '/View/FrontOffice/connexion.php');
        }
        exit;
    }
    verifyCsrf();
}
