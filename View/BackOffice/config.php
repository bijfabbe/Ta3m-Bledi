<?php

require_once __DIR__ . '/../../Controller/Security.php';

// 1. Paramètres de connexion à la base MySQL (XAMPP / Laragon / WAMP par défaut)
$dbHost = getenv('DB_HOST') ?: '127.0.0.1';
$dbName = getenv('DB_NAME') ?: 'ta3m_bledi';
$dbUser = getenv('DB_USER') ?: 'root';
$dbPass = getenv('DB_PASS') ?: '';

try {
    $pdo = new PDO(
        "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4",
        $dbUser,
        $dbPass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die('Erreur de connexion à la base de données : ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}

// 2. Fonctions utilitaires simples pour les vues et contrôleurs

// Protège les textes affichés en HTML contre les failles XSS
function e(?string $text): string
{
    return htmlspecialchars((string)$text, ENT_QUOTES, 'UTF-8');
}

// Redirige vers une page MVC
function redirect(string $page): void
{
    header("Location: index.php?page=$page");
    exit;
}

// Vérifie si l'utilisateur connecté est Administrateur
function isAdmin(): bool
{
    return isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';
}

// Vérifie que l'utilisateur est connecté en Admin ou Agriculteur
function requireBackendAuth(): void
{
    $role = $_SESSION['user']['role'] ?? '';
    if ($role !== 'admin' && $role !== 'farmer') {
        redirect('login');
    }
}
