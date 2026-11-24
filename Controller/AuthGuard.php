<?php
/** Contrôle d'accès Front Office / Back Office et helpers de sécurité. */
require_once __DIR__ . '/Security.php';

function ta3mFrontOfficeUrl(string $file): string
{
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $marker = '/View/';
    $pos = strpos($script, $marker);
    if ($pos !== false) {
        return substr($script, 0, $pos) . '/View/FrontOffice/' . ltrim($file, '/');
    }
    return '/View/FrontOffice/' . ltrim($file, '/');
}

function requireLogin(): int
{
    if (empty($_SESSION['user_id'])) {
        header('Location: ' . ta3mFrontOfficeUrl('connexion.php'));
        exit;
    }
    return (int) $_SESSION['user_id'];
}

function requireAdmin(): int
{
    $id = requireLogin();
    if (($_SESSION['role'] ?? '') !== 'ADMIN') {
        http_response_code(403);
        exit('Accès interdit : réservé à l’administrateur.');
    }
    return $id;
}

function e($valeur): string
{
    return htmlspecialchars((string)$valeur, ENT_QUOTES, 'UTF-8');
}

function setFlash(string $message, string $type = 'success'): void
{
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

function getFlash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}
