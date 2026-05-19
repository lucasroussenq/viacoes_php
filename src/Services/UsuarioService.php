<?php

namespace App\Services;

use PDO;

final class UsuarioService
{
    public function __construct(
        private PDO $pdo
    ) {
    }

    public function create(
        string $nome,
        string $email,
        string $senha
    ): void {
        $hash = password_hash($senha, PASSWORD_DEFAULT);

        $stmt = $this->pdo->prepare("
            INSERT INTO usuarios (
                nome,
                email,
                senha
            )
            VALUES (
                :nome,
                :email,
                :senha
            )
        ");

        $stmt->execute([
            'nome' => $nome,
            'email' => $email,
            'senha' => $hash,
        ]);
    }
}
