<?php

namespace App\Services;

use PDO;

final class AutenticatorService
{
    public function __construct(
        private PDO $pdo
    ) {
    }

    public function login(string $email, string $senha): bool
    {
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($senha, $user['senha'])) {
            return false;
        }

        $_SESSION['user_id'] = $user['id']; // ← este é o id que será usado no histórico

        return true;
    }

    public function logout(): void
    {
        session_destroy();
    }
}