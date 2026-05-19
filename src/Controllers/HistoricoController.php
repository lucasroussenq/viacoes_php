<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\HistoricoService;

final class HistoricoController
{
    public function __construct(
        private HistoricoService $service
    ) {
    }

    public function index(): void
    {
        $historicos = $this->service->listar();

        require __DIR__ . '/../views/viacoes/historico.php';
    }
}
