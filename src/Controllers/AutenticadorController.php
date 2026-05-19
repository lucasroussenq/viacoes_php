<?php

namespace App\Controllers;

use App\Services\AutenticatorService;

final class AutenticadorController
{
    public function __construct(
        private AutenticatorService $autenticatorService
    ) {
    }

    public function login(): void
    {
        require __DIR__ . '/../views/autenticator/login.php';
    }

    public function autenticar(): void
    {
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';

        if ($email === '' || $senha === '') {
            $_SESSION['erro'] = 'Preencha todos os campos';

            header('Location: /login');
            exit;
        }

        $loginValido = $this->autenticatorService->login(
            $email,
            $senha
        );

        if (!$loginValido) {
            $_SESSION['erro'] = 'Email ou senha inválidos';

            header('Location: /login');
            exit;
        }

        header('Location: /dashboard');
        exit;
    }

    public function logout(): void
    {
        $this->autenticatorService->logout();

        header('Location: /login');
        exit;
    }
}