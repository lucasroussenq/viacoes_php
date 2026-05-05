<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Services\ViacaoService;

/** Controla o fluxo HTTP de marcas e delega persistencia ao ViacaoService. */
final class HomeController
{
    private ViacaoService $viacoes;

    // Injeção de dependência: aceita um ViacaoService ou cria um padrão
    public function __construct(?ViacaoService $viacoes = null)
    {
        $this->viacoes = $viacoes ?? new ViacaoService();
    }

    public function index(): void
    {
        try {
            $viacoesAtivas = $this->viacoes->ativas();

            View::render('home/index', [
                'viacoesAtivas' => $viacoesAtivas,
                'erroConexao'   => false,

            ]);
            return;  // Early return — sucesso

        } catch (PDOException $e) {
            // Se o banco falhar, renderiza a home sem viações
            // (graceful degradation — a página não quebra)
            View::render('home/index', [
                'viacoesAtivas' => [],
                'erroConexao'   => true,
            ]);
        }
    }
}
