<?php
/**
 * CONTROLLER — AuthController.php
 * Gère la connexion (admin / agriculteur), l'inscription agriculteur, et le 1er Admin.
 */
require_once __DIR__ . '/../Model/UserModel.php';

class AuthController
{
    private UserModel $userModel;

    public function __construct(PDO $pdo)
    {
        $this->userModel = new UserModel($pdo);
    }

    public function showLogin(): void
    {
        $needsAdmin = !$this->userModel->hasAdmin();
        $error = $_SESSION['flash_error'] ?? null;
        $success = $_SESSION['flash_success'] ?? null;
        unset($_SESSION['flash_error'], $_SESSION['flash_success']);

        require __DIR__ . '/../View/BackOffice/layout/login.php';
    }

    public function login(): void
    {
        verifyCsrf();
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = $this->userModel->findByEmail($email);
        if (!$user || !password_verify($password, $user['password_hash'])) {
            $_SESSION['flash_error'] = 'E-mail ou mot de passe incorrect.';
            redirect('login');
        }

        if ($user['role'] !== 'admin' && $user['role'] !== 'farmer') {
            $_SESSION['flash_error'] = 'Accès refusé : réservé aux administrateurs et agriculteurs.';
            redirect('login');
        }

        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
        ];
        redirect('dashboard');
    }

    public function register(): void
    {
        verifyCsrf();
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role = 'user'; // Un visiteur ne peut jamais s'attribuer le rôle farmer/admin.
        $password = $_POST['password'] ?? '';

        if (strlen($name) < 2 || !filter_var($email, FILTER_VALIDATE_EMAIL) || !$this->isStrongPassword($password)) {
            $_SESSION['flash_error'] = 'Mot de passe : 8 caractères minimum, avec au moins une lettre et un chiffre.';
            redirect('login');
        }

        if ($this->userModel->findByEmail($email)) {
            $_SESSION['flash_error'] = 'Un compte existe déjà avec cet e-mail.';
            redirect('login');
        }

        $this->userModel->create($name, $email, $role, $password);

        $_SESSION['flash_success'] = 'Compte utilisateur créé. Il ne donne pas accès au Back Office.';
        redirect('login');

    }

    public function setupAdmin(): void
    {
        verifyCsrf();
        if ($this->userModel->hasAdmin()) {
            $_SESSION['flash_error'] = 'Un administrateur existe déjà.';
            redirect('login');
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (strlen($name) < 2 || !filter_var($email, FILTER_VALIDATE_EMAIL) || !$this->isStrongPassword($password)) {
            $_SESSION['flash_error'] = 'Mot de passe : 8 caractères minimum, avec au moins une lettre et un chiffre.';
            redirect('login');
        }

        $this->userModel->create($name, $email, 'admin', $password);
        session_regenerate_id(true);
        $admin = $this->userModel->findByEmail($email);
        $_SESSION['user'] = [
            'id' => $admin['id'],
            'name' => $admin['name'],
            'email' => $admin['email'],
            'role' => 'admin',
        ];
        redirect('dashboard');
    }

    private function isStrongPassword(string $password): bool
    {
        return strlen($password) >= 8 && strlen($password) <= 64
            && preg_match('/[A-Za-z]/', $password) === 1
            && preg_match('/\d/', $password) === 1;
    }

    public function logout(): void
    {
        require_once __DIR__ . '/Security.php';
        destroyTa3mSession();
        header('Location: index.php?page=login');
        exit;
    }
}
