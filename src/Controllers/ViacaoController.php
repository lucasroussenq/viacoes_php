<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\Viacao;
use App\Services\ViacaoService;
use App\Services\HistoricoService;

/** Controla o fluxo HTTP de viações e delega persistência ao Service. */
final class ViacaoController
{
    private ViacaoService   $viacaoService;
    private HistoricoService $historicoService;

    public function __construct(?ViacaoService $ViacaoService = null)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->verificarLogin();
        $this->viacaoService    = $ViacaoService ?? new ViacaoService();
        $this->historicoService = new HistoricoService();
    }

    /** Bloqueia acesso de usuários não autenticados. */
    private function verificarLogin(): void
    {
        // Se seu sistema usa 'usuario_id' ou 'user_id', certifique-se de validar o correto.
        // Mantido 'user_id' conforme o seu código original.
        if (empty($_SESSION['user_id']) && empty($_SESSION['usuario_id'])) {
            View::redirect('/login');
            exit;
        }
    }

    /** Recupera o ID do Administrador logado na sessão */
    private function getAdminId(): int
    {
        return (int)($_SESSION['user_id'] ?? $_SESSION['usuario_id'] ?? 1);
    }

    /** * CORREÇÃO DA SESSÃO DE SNAPSHOT: Retorna o status tratado puramente como string ('ativo' ou 'inativo')
     * @return array{nome: string, url: string, cidade: string, status: string, logo: string|null}
     */
    private function snapshot(Viacao $v): array
    {
        $statusString = 'inativo';
        if ($v->status === true || $v->status === '1' || $v->status === 1 || $v->status === 'ativo') {
            $statusString = 'ativo';
        }

        return [
            'nome'   => $v->nome,
            'url'    => $v->url,
            'cidade' => $v->cidade,
            'status' => $statusString,
            'logo'   => $v->logo,
        ];
    }

    /** Lista viações e renderiza a tela principal. */
    public function index(): void
    {
        $nome   = trim((string) ($_GET['nome']   ?? ''));
        $cidade = trim((string) ($_GET['cidade'] ?? ''));
        $url    = trim((string) ($_GET['url']    ?? ''));
        $status = $_GET['status'] ?? '';
        $excluido = $_GET['excluido'] ?? '';

        $viacoes = $this->viacaoService->listar(
            $nome   !== '' ? $nome   : null,
            $cidade !== '' ? $cidade : null,
            $url    !== '' ? $url    : null,
            $status !== '' ? $status : null,
            $excluido !== '' ? $excluido : null
        );

        $temFiltro = $nome !== '' || $cidade !== '' || $url !== '' || $status !== '' || $excluido !== '';

        View::render('viacoes/index', [
            'title'     => 'Viações',
            'viacoes'   => $viacoes,
            'temFiltro' => $temFiltro,
            'filtros'   => compact('nome', 'cidade', 'url', 'status', 'excluido'),
        ]);
    }

    /** Exibe o histórico de todas as alterações. */
    public function historico(): void
    {
        $filtroUsuario = isset($_GET['usuario']) ? trim((string) $_GET['usuario']) : '';
        $filtroViacao  = isset($_GET['viacao'])  ? trim((string) $_GET['viacao'])  : '';
        $filtroAcao    = isset($_GET['acao'])     ? trim((string) $_GET['acao'])    : '';

        $historico = $this->historicoService->getHistory([
            'tab'     => 'viacoes',
            'usuario' => $filtroUsuario,
            'alvo'    => $filtroViacao
        ]);

        View::render('viacoes/historico', [
            'title'         => 'Histórico de viações',
            'historico'     => $historico,
            'filtroUsuario' => $filtroUsuario,
            'filtroViacao'  => $filtroViacao,
            'filtroAcao'    => $filtroAcao,
        ]);
    }

    /** Exibe o formulário de criação. */
    public function create(): void
    {
        View::render('viacoes/create', [
            'title'  => 'Criar viação',
            'errors' => [],
            'old'    => ['nome' => '', 'url' => '', 'cidade' => '', 'logo' => '', 'status' => 'ativo'],
        ]);
    }

    /** Processa o POST de criação. */
    public function store(): void
    {
        $nomeViacao = trim((string) ($_POST['nome']   ?? ''));
        $url        = trim((string) ($_POST['url']    ?? ''));
        $cidade     = trim((string) ($_POST['cidade'] ?? ''));

        $status = (isset($_POST['status']) && ($_POST['status'] === '1' || $_POST['status'] === 'ativo')) ? 'ativo' : 'inativo';

        $errors = $this->validateName($nomeViacao);

        if ($errors !== []) {
            View::render('viacoes/create', [
                'title'  => 'Criar viação',
                'errors' => $errors,
                'old'    => ['nome' => $nomeViacao, 'url' => $url, 'cidade' => $cidade, 'logo' => '', 'status' => $status],
            ]);
            return;
        }

        $file = (!empty($_FILES['logo']['name'])) ? $_FILES['logo'] : null;

        $id = $this->viacaoService->create($nomeViacao, $url, $cidade, $status, $file);
        $viaCriada = $this->viacaoService->find($id);

        // CORREÇÃO DOS PARÂMETROS NOMEADOS: Mapeado estritamente com os nomes da assinatura do HistoricoService
        $this->historicoService->criar(
            usuarioId:    $this->getAdminId(),
            entidadeId:   $id,
            acao:         'CREATE',
            dados: [
                'antes'  => null,
                'depois' => $viaCriada !== null ? $this->snapshot($viaCriada) : null,
            ],
            entidadeTipo: 'viacoes'
        );

        View::flash('success', 'Viação criada com sucesso (#' . $id . ').');
        View::redirect('/viacoes');
    }

    /** Exibe o formulário de edição. */
    public function edit(int $id): void
    {
        $viacao = $this->viacaoService->find($id);

        if ($viacao === null) {
            http_response_code(404);
            echo 'Viação não encontrada.';
            return;
        }

        View::render('viacoes/edit', [
            'title'  => 'Editar viação',
            'viacao' => $viacao,
            'errors' => [],
            'old'    => [
                'nome'   => $viacao->nome,
                'url'    => $viacao->url,
                'cidade' => $viacao->cidade,
                'logo'   => $viacao->logo ?? '',
                'status' => $viacao->status,
            ],
        ]);
    }

    /** Processa o PUT de atualização. */
    public function update(int $id): void
    {
        $viacaoEditar = $this->viacaoService->find($id);

        if ($viacaoEditar === null) {
            http_response_code(404);
            echo 'Viação não encontrada.';
            return;
        }

        $snapshotAntes = [
            'nome'   => $viacaoEditar->nome,
            'url'    => $viacaoEditar->url,
            'cidade' => $viacaoEditar->cidade,
            'status' => ($viacaoEditar->status === true || $viacaoEditar->status === '1' || $viacaoEditar->status === 1 || $viacaoEditar->status === 'ativo') ? 'ativo' : 'inativo',
            'logo'   => $viacaoEditar->logo,
        ];

        $nomeViacao = trim((string) ($_POST['nome']   ?? ''));
        $url        = trim((string) ($_POST['url']    ?? ''));
        $cidade     = trim((string) ($_POST['cidade'] ?? ''));

        $status = (isset($_POST['status']) && ($_POST['status'] === '1' || $_POST['status'] === 'ativo')) ? 'ativo' : 'inativo';

        $errors = $this->validateName($nomeViacao);

        if ($errors !== []) {
            View::render('viacoes/edit', [
                'title'  => 'Editar viação',
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

        $this->viacaoService->update($id, $nomeViacao, $cidade, $status, $url, $file);

        $viacaoAtualizada = $this->viacaoService->find($id);

        $this->historicoService->criar(
            usuarioId:    $this->getAdminId(),
            entidadeId:   $id,
            acao:         'UPDATE',
            dados: [
                'antes'  => $snapshotAntes,
                'depois' => $viacaoAtualizada !== null ? $this->snapshot($viacaoAtualizada) : null,
            ],
            entidadeTipo: 'viacoes'
        );

        View::flash('success', 'Viação actualizada com sucesso.');
        View::redirect('/viacoes');
    }

    /** Remove uma viação. */
    public function destroy(int $id): void
    {
        $viacao = $this->viacaoService->find($id);

        if ($viacao === null) {
            http_response_code(404);
            echo 'Viação não encontrada.';
            return;
        }

        $this->historicoService->criar(
            usuarioId:    $this->getAdminId(),
            entidadeId:   $id,
            acao:         'DELETE',
            dados: [
                'antes'  => $this->snapshot($viacao),
                'depois' => null,
            ],
            entidadeTipo: 'viacoes'
        );

        $this->viacaoService->delete($id);

        View::flash('success', 'Viação removida com sucesso.');
        View::redirect('/viacoes');
    }

    public function restore(int $id): void
    {
        $viacao = $this->viacaoService->find($id);

        if ($viacao === null) {
            http_response_code(404);
            echo 'Viação não encontrada.';
            return;
        }

        $this->historicoService->criar(
            usuarioId:    $this->getAdminId(),
            entidadeId:   $id,
            acao:         'RESTORE',
            dados: [
                'antes'  => null,
                'depois' => $this->snapshot($viacao)
            ],
            entidadeTipo: 'viacoes'
        );

        $this->viacaoService->restore($id);

        View::flash('success', 'Viação restaurada com sucesso.');
        View::redirect('/viacoes');
    }

    /** @return list<string> */
    private function validateName(string $nomeViacao): array
    {
        $errors = [];
        if ($nomeViacao === '') {
            $errors[] = 'O nome da viação é obrigatório.';
        }
        if (strlen($nomeViacao) > 255) {
            $errors[] = 'O nome da viação deve ter no máximo 255 caracteres.';
        }
        return $errors;
    }
}