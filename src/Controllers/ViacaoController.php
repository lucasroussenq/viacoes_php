<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Services\ViacaoService;

/** Controla o fluxo HTTP de marcas e delega persistencia ao ViacaoService. */
final class ViacaoController
{
    /** Service usado para consultar e alterar marcas. */
    private ViacaoService $viacao;

    /** @param ViacaoService|null $viacoes Permite injecao em testes. */
    public function __construct(?ViacaoService $ViacaoService = null)
    {
        $this->ViacaoService = $ViacaoService ?? new ViacaoService();
    }

    /** Lista marcas e renderiza a tela principal. */
    public function index(): void
    {
        $Viacoes = $this->ViacaoService->all();

        View::render('Viacoes/index', [
            'title' => 'Marcas de café',
            'Viacoes' => $Viacoes,
        ]);
    }

    /** Exibe o formulario de criacao. */
    public function create(): void
    {
        View::render('Viacoes/create', [
            'title' => 'Criar marca',
            'errors' => [],
            'old' => [
                'nome' => '',
                'logo' => '',
                'Status' => false,
            ],
        ]);
    }

    /** Processa o POST de criacao com PRG (Post Redirect Get). */
    public function loja(): void
    {
        $NomeViacao = trim((string) ($_POST['nome'] ?? ''));
        $url = trim((string) ($_POST['url'] ?? ''));
        $cidade = trim((string) ($_POST['cidade'] ?? ''));
        $logo = trim((string) ($_POST['logo'] ?? ''));
        $status = isset($_POST['Status']) && (string) $_POST['Status'] === '1';

        $errors = $this->validate($NomeViacao);

        if ($errors !== []) {
            View::render('Viacoes/create', [
                'title' => 'Criar viacao',
                'errors' => $errors,
                'old' => [
                    'nome' => $NomeViacao,
                    'url' => $url,
                    'cidade' => $cidade,
                    'logo' => $logo,
                    'status' => $status,
                ],
            ]);
            return;
        }

        $id = $this->ViacaoService->create(
            $NomeViacao,
            $logo !== '' ? $logo : null,
            $status
        );

        View::flash('success', 'viação criada com sucesso (#' . $id . ').');
        View::redirect('/Viacoes');
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

        View::render('Viacoes/edit', [
            'title' => 'Editar viacao',
            'viacao' => $nome,
            'errors' => [],
            'old' => [
                'nome' => $nome->nome,
                'logo' => $nome->logo ?? '',
                'Status' => $nome->status,
            ],
        ]);
    }

    /** Processa o POST de atualizacao. */
    public function update(int $id): void
    {
        $nome = $this->ViacaoService->find($id);

        if ($nome === null) {
            http_response_code(404);
            echo 'viação não encontrada.';
            return;
        }

        $NomeViacao = trim((string) ($_POST['nome'] ?? ''));
        $logo = trim((string) ($_POST['logo'] ?? ''));
        $status = isset($_POST['Status']) && (string) $_POST['Status'] === '1';

        $errors = $this->validate($NomeViacao);

        if ($errors !== []) {
            View::render('Viacoes/edit', [
                'title' => 'Editar marca',
                'marca' => $nome,
                'errors' => $errors,
                'old' => [
                    'nome' => $NomeViacao,
                    'logo' => $logo,
                    'Status' => $status,
                ],
            ]);
            return;
        }

        $this->ViacaoService->update(
            $id,
            $NomeViacao,
            $logo !== '' ? $logo : null,
            $status
        );

        View::flash('success', 'viacao atualizada com sucesso.');
        View::redirect('/Viacoes');
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

        View::flash('success', 'viacao removida com sucesso.');
        View::redirect('/Viacoes');
    }

    /** @return list<string> Retorna erros de validacao do nome da marca. */
    private function validate(string $NomeViacao): array
    {
        $errors = [];

        if ($NomeViacao === '') {
            $errors[] = 'O nome da viação é obrigatório.';
        }

        if (strlen($NomeViacao) > 255) {
            $errors[] = 'O nome da viação deve ter no máximo 255 caracteres.';
        }

        return $errors;
    }
}
