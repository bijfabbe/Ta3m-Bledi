<?php
class UserModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function hasAdmin(): bool
    {
        $count = $this->pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
        return (int)$count > 0;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([strtolower(trim($email))]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function create(string $name, string $email, string $role, string $password): void
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (name, email, role, password_hash) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([trim($name), strtolower(trim($email)), $role, $hash]);
    }
}
