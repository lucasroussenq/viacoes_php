<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Services\ViacaoService;

/** Controla o fluxo HTTP de marcas e delega persistencia ao ViacaoService. */
final class ViacaoController
{
    /** Service usado para consultar e alterar marcas. */
    private ViacaoService $viacaoService;

    /** @param ViacaoService|null $viacoes Permite injecao em testes. */
    public function __construct(?ViacaoService $ViacaoService = null)
    {
        $this->viacaoService = $ViacaoService ?? new ViacaoService();
    }

    /** Lista marcas e renderiza a tela principal. */
    public function index(): void
    {
        $viacoes = $this->viacaoService->all();

        View::render('viacoes/index', [
            'title' => 'Viações',
            'viacoes' => $viacoes,
        ]);
    }

    /** Exibe o histórico de edições de viações. */
    public function historico(): void
    {
        $historico = $this->viacaoService->historico();

        View::render('viacoes/historico', [
            'title' => 'Histórico de viações',
            'historico' => $historico,
        ]);
    }



    /** Exibe o formulario de criacao. */
    public function create(): void
    {
        View::render('viacoes/create', [
            'title' => 'Criar marca',
            'errors' => [],
            'old' => [
                'nome' => '',
                'url' => '',
                'cidade' => '',
                'logo' => '',
                'status' => true,
            ],
        ]);
    }

    /** Processa o POST de criacao com PRG (Post Redirect Get). */
    public function store(): void
    {
        $nomeViacao = trim((string) ($_POST['nome'] ?? ''));
        $url        = trim((string) ($_POST['url'] ?? ''));
        $cidade     = trim((string) ($_POST['cidade'] ?? ''));
        $status     = (isset($_POST['status']) && $_POST['status'] === '1') ? 1 : 0;

        $errors = $this->validateName($nomeViacao);

        if ($errors !== []) {
            View::render('viacoes/create', [
                'title'  => 'Criar viacao',
                'errors' => $errors,
                'old'    => [
                    'nome'   => $nomeViacao,
                    'url'    => $url,
                    'cidade' => $cidade,
                    'logo'   => '',
                    'status' => $status,
                ],
            ]);
            return;
        }

        // Passa $_FILES['logo'] direto — o Service cuida do upload
        $file = (!empty($_FILES['logo']['name'])) ? $_FILES['logo'] : null;

        $id = $this->viacaoService->create(
            $nomeViacao,
            $url,
            $cidade,
            $status,
            $file           // ?array — correto agora
        );

        View::flash('success', 'Viação criada com sucesso (#' . $id . ').');
        View::redirect('/viacoes');
    }

    /** Exibe o formulario de edicao de uma marca. */
    public function edit(int $id): void
    {
        $viacao = $this->viacaoService->find($id);

        if ($viacao === null) {
            http_response_code(404);
            echo 'Marca não encontrada.';
            return;
        }

        View::render('viacoes/edit', [
            'title' => 'Editar viacao',
            'viacao' => $viacao,
            'errors' => [],
            'old' => [
                'nome' => $viacao->nome,
                'url' => $viacao->url,
                'cidade' => $viacao->cidade,
                'logo' => $viacao->logo ?? '',
                'status' => $viacao->status,
            ],
        ]);
    }

    /** Processa o POST de atualizacao. */
    public function update(int $id): void
    {
        $viacaoEditar = $this->viacaoService->find($id);

        if ($viacaoEditar === null) {
            http_response_code(404);
            echo 'viação não encontrada.';
            return;
        }

        $nomeViacao = trim((string) ($_POST['nome'] ?? ''));
        $url        = trim((string) ($_POST['url'] ?? ''));
        $cidade     = trim((string) ($_POST['cidade'] ?? ''));
        $file     = trim((string) ($_POST['logo'] ?? ''));
        $status     = isset($_POST['status']) && (string) $_POST['status'] === '1';

        $errors = $this->validateName($nomeViacao);

        if ($errors !== []) {
            View::render('viacoes/edit', [
                'title'  => 'Editar viacao',
                'viacao' => $viacaoEditar,
                'errors' => $errors,
                'old'    => [
                    'nome'   => $nomeViacao,
                    'logo'   => $viacaoEditar->logo ?? '',
                    'status' => $status,
                    'url'    => $url,
                    'cidade' => $cidade,
                ],
            ]);
            return;
        }

        $file = (!empty($_FILES['logo']['name'])) ? $_FILES['logo'] : null;

        $this->viacaoService->update(
            $id,
            $nomeViacao,
            $cidade,
            $status,
            $url,
            $file
        );

        View::flash('success', 'viacao atualizada com sucesso.');
        View::redirect('/viacoes');
    }
    /** Remove uma marca via POST para evitar delete por GET. */
    public function destroy(int $id): void
    {
        $nome = $this->viacaoService->find($id);

        if ($nome === null) {
            http_response_code(404);
            echo 'Marca não encontrada.';
            return;
        }

        $this->viacaoService->delete($id);

        View::flash('success', 'viacao removida com sucesso.');
        View::redirect('/viacoes');
    }

    /** @return list<string> Retorna erros de validacao do nome da marca. */
    private function validateName(string $NomeViacao): array
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
