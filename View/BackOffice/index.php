<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../../Controller/AuthController.php';
require_once __DIR__ . '/../../Controller/VarietyController.php';
require_once __DIR__ . '/../../Controller/TestimonialController.php';

$page = $_GET['page'] ?? (isset($_SESSION['user']) ? 'dashboard' : 'login');
$action = $_GET['action'] ?? '';

// Le Back Office est inaccessible aux comptes simples (role=user).
if ($page !== 'login' && !in_array($_SESSION['user']['role'] ?? '', ['admin', 'farmer'], true)) {
    redirect('login');
}

$auth = new AuthController($pdo);
$variety = new VarietyController($pdo);
$testimonial = new TestimonialController($pdo);

// Routage simple des pages et actions
if ($page === 'login') {
    if ($action === 'login') $auth->login();
    elseif ($action === 'register') {
        $_SESSION['flash_error'] = 'La création des comptes clients se fait depuis le Front Office.';
        redirect('login');
    }
    elseif ($action === 'setupAdmin') $auth->setupAdmin();
    else $auth->showLogin();
} elseif ($page === 'logout') {
    $auth->logout();
} elseif ($page === 'varieties') {
    if ($action === 'create') $variety->create();
    elseif ($action === 'update') $variety->update();
    elseif ($action === 'delete') $variety->delete();
    else $variety->list();
} elseif ($page === 'testimonials') {
    $testimonial->list();
} else {
    $variety->dashboard();
}
