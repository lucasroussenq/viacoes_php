<?php


namespace App\Services;

use PDO;

class UsuarioService
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function login(string $email, string $senha): bool
    {
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        // Verifica se usuário existe e se a senha (hash) bate
        if ($user && password_verify($senha, $user['senha'])) {
            if (session_status() === PHP_SESSION_NONE) session_start();

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nome'] = $user['nome'];
            $_SESSION['user_nivel'] = (int)$user['nivel']; // O "pulo do gato" está aqui

            return true;
        }
        return false;
    }

    public function downgradeAcesso(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['user_nivel'] = 0; // Altera apenas a sessão, não o banco
    }

    public function upgradeAcesso(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Forçamos o nível 1 na sessão atual
        $_SESSION['user_nivel'] = 1;
        $_SESSION['user_nome'] = 'Admin Temporário';
    }

}