<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Services\ViacaoService;

/** Controla o fluxo HTTP de marcas e delega persistencia ao ViacaoService. */
final class ViacaoController
{
    /** Service usado para consultar e alterar marcas. */
    private ViacaoService $ViacaoService;

    /** @param ViacaoService|null $ViacaoService Permite injecao em testes. */
    public function __construct(?ViacaoService $ViacaoService = null)
    {
        $this->ViacaoService = $ViacaoService ?? new ViacaoService();
    }

    /** Lista marcas e renderiza a tela principal. */
    public function index(): void
    {
        $nomes = $this->ViacaoService->all();

        View::render('nomes/index', [
            'title' => 'Marcas de café',
            'nomes' => $nomes,
        ]);
    }

    /** Exibe o formulario de criacao. */
    public function create(): void
    {
        View::render('nomes/create', [
            'title' => 'Criar marca',
            'errors' => [],
            'old' => [
                'nome' => '',
                'description' => '',
                'is_imported' => false,
            ],
        ]);
    }

    /** Processa o POST de criacao com PRG (Post Redirect Get). */
    public function store(): void
    {
        $nomeViacao = trim((string) ($_POST['nome'] ?? ''));
        $description = trim((string) ($_POST['description'] ?? ''));
        $isImported = isset($_POST['is_imported']) && (string) $_POST['is_imported'] === '1';

        $errors = $this->validate($nomeViacao);

        if ($errors !== []) {
            View::render('nomes/create', [
                'title' => 'Criar marca',
                'errors' => $errors,
                'old' => [
                    'nome' => $nomeViacao,
                    'description' => $description,
                    'is_imported' => $isImported,
                ],
            ]);
            return;
        }

        $id = $this->ViacaoService->create(
            $nomeViacao,
            $description !== '' ? $description : null,
            $isImported
        );

        View::flash('success', 'Marca criada com sucesso (#' . $id . ').');
        View::redirect('/nomes');
    }

    /** Exibe o formulario de edicao de uma marca. */
    public function edit(int $id): void
    {
        $nome = $this->ViacaoService->find($id);

        if ($nome === null) {
            http_response_code(404);
            echo 'Marca não encontrada.';
            return;
        }

        View::render('nomes/edit', [
            'title' => 'Editar marca',
            'marca' => $nome,
            'errors' => [],
            'old' => [
                'nome' => $nome->nome,
                'description' => $nome->description ?? '',
                'is_imported' => $nome->isImported,
            ],
        ]);
    }

    /** Processa o POST de atualizacao. */
    public function update(int $id): void
    {
        $nome = $this->ViacaoService->find($id);

        if ($nome === null) {
            http_response_code(404);
            echo 'Marca não encontrada.';
            return;
        }

        $nomeViacao = trim((string) ($_POST['nome'] ?? ''));
        $description = trim((string) ($_POST['description'] ?? ''));
        $isImported = isset($_POST['is_imported']) && (string) $_POST['is_imported'] === '1';

        $errors = $this->validate($nomeViacao);

        if ($errors !== []) {
            View::render('nomes/edit', [
                'title' => 'Editar marca',
                'marca' => $nome,
                'errors' => $errors,
                'old' => [
                    'nome' => $nomeViacao,
                    'description' => $description,
                    'is_imported' => $isImported,
                ],
            ]);
            return;
        }

        $this->ViacaoService->update(
            $id,
            $nomeViacao,
            $description !== '' ? $description : null,
            $isImported
        );

        View::flash('success', 'Marca atualizada com sucesso.');
        View::redirect('/nomes');
    }

    /** Remove uma marca via POST para evitar delete por GET. */
    public function destroy(int $id): void
    {
        $nome = $this->ViacaoService->find($id);

        if ($nome === null) {
            http_response_code(404);
            echo 'Marca não encontrada.';
            return;
        }

        $this->ViacaoService->delete($id);

        View::flash('success', 'Marca removida com sucesso.');
        View::redirect('/nomes');
    }

    /** @return list<string> Retorna erros de validacao do nome da marca. */
    private function validate(string $nomeViacao): array
    {
        $errors = [];

        if ($nomeViacao === '') {
            $errors[] = 'O nome da marca é obrigatório.';
        }

        if (strlen($nomeViacao) > 255) {
            $errors[] = 'O nome da marca deve ter no máximo 255 caracteres.';
        }

        return $errors;
    }
}
