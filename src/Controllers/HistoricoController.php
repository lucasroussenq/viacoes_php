<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\HistoricoService;

final class HistoricoController
{
    private HistoricoService $service;

    public function __construct(?HistoricoService $service = null)
    {
        $this->service = $service ?? new HistoricoService();
    }

    public function index(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Filtra a aba atual vinda da URL (?tab=viacoes ou ?tab=usuarios)
        // No seu HistoricoController.php
        $tabAtual = $_GET['tab'] ?? ''; // Deixe vazio por padrão em vez de 'viacoes'

        if (!in_array($tabAtual, ['viacoes', 'usuarios', ''], true)) {
            $tabAtual = '';
        }

        // Normaliza os filtros vindos dos campos de input da barra de pesquisa
        $filtroUsuario = trim((string)($_GET['usuario'] ?? $_GET['alterado_por'] ?? ''));
        $filtroAlvo    = trim((string)($_GET['alvo'] ?? $_GET['item_afetado'] ?? ''));

        $filters = [
            'tab'     => $tabAtual,
            'usuario' => $filtroUsuario,
            'alvo'    => $filtroAlvo
        ];

        // Solicita os registros unificados ao Service
        $historico = $this->service->getHistory($filters);

        // Define a variável de título exigida pelo layout do front
        $title = "Histórico Geral";

        // Inclui a sua view original sem alterar uma única linha dela
        require __DIR__ . '/../views/historico/historico.php';
    }
}