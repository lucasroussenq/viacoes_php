<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Services\UsuarioService;

final class UsuarioController
{
    private UsuarioService $service;

    public function __construct()
    {
        $pdo = \getPdo();
        $this->service = new UsuarioService($pdo);
    }

    public function login(): void
    {
        $email = $_POST ['email'] ?? '';
        $senha = $_POST ['senha'] ?? '';

        if ($this->service->login($email, $senha)) {
            header('Location: /viacoes');

        } else {
            header('Location: /viacoes');
        }
        exit;
    }
    public function downgrade(): void
    {
        $this->service->downgradeAcesso();
        header('Location: /viacoes');
        exit;
    }
    public function upgrade(): void
    {
        $this->service->upgradeAcesso();
        header('Location: /viacoes');
        exit;
    }

}