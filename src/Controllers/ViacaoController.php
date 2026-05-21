<?php
//O controller mais completo do projeto.
// Gerencia o ciclo de vida das viações: listar, criar, editar, deletar.
// Também registra histórico de auditoria em cada operação e bloqueia acesso de usuários não logados.
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
        $this->verificarLogin();
        $this->viacaoService    = $ViacaoService ?? new ViacaoService();
        $this->historicoService = new HistoricoService();
    }

    /** Bloqueia acesso de usuários não autenticados. */
    private function verificarLogin(): void
    {
        if (empty($_SESSION['user_id'])) {
            View::redirect('/login');
            exit;
        }
    }
    // Centralizar aqui garante que store(), update() e destroy() sempre
    // registrem os mesmos campos, se amanhã adicionar um campo novo na tabela,
    // basta incluí-lo aqui e todos os registros de histórico ficam consistentes.
    /** @return array{nome: string, url: string, cidade: string, status: bool, logo: string|null} */
    private function snapshot(Viacao $v): array
    {
        return [
            'nome'   => $v->nome,
            'url'    => $v->url,
            'cidade' => $v->cidade,
            'status' => $v->status,   // bool: true = ativo, false = inativo
            'logo'   => $v->logo,     // string com nome do arquivo ou null
        ];
    }

    /** Lista viações e renderiza a tela principal. */
    public function index(): void
    {
        $nome   = trim((string) ($_GET['nome']   ?? ''));
        $cidade = trim((string) ($_GET['cidade'] ?? ''));
        $url    = trim((string) ($_GET['url']    ?? ''));
        $status = $_GET['status'] ?? '';

        $viacoes = $this->viacaoService->listar(
            $nome   !== '' ? $nome   : null,
            $cidade !== '' ? $cidade : null,
            $url    !== '' ? $url    : null,
            $status !== '' ? $status : null,
        );

        $temFiltro = $nome !== '' || $cidade !== '' || $url !== '' || $status !== '';

        View::render('viacoes/index', [
            'title'     => 'Viações',
            'viacoes'   => $viacoes,
            'temFiltro' => $temFiltro,
            'filtros'   => compact('nome', 'cidade', 'url', 'status'),
        ]);
    }

    /** Exibe o histórico de todas as alterações. */
    public function historico(): void
    {
        $filtroUsuario = isset($_GET['usuario']) ? trim((string) $_GET['usuario']) : '';
        $filtroViacao  = isset($_GET['viacao'])  ? trim((string) $_GET['viacao'])  : '';
        $filtroAcao    = isset($_GET['acao'])     ? trim((string) $_GET['acao'])    : '';

        $historico = $this->historicoService->listar(
            $filtroUsuario !== '' ? $filtroUsuario : null,
            $filtroViacao  !== '' ? $filtroViacao  : null,
            $filtroAcao    !== '' ? $filtroAcao    : null,
        );

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
            'old'    => ['nome' => '', 'url' => '', 'cidade' => '', 'logo' => '', 'status' => true],
        ]);
    }

    /** Processa o POST de criação. */
    public function store(): void
    {
        $nomeViacao = trim((string) ($_POST['nome']   ?? ''));
        $url        = trim((string) ($_POST['url']    ?? ''));
        $cidade     = trim((string) ($_POST['cidade'] ?? ''));
        $status     = (isset($_POST['status']) && $_POST['status'] === '1') ? 1 : 0;

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

        // Cria a viação e recebe o ID gerado
        $id = $this->viacaoService->create($nomeViacao, $url, $cidade, $status, $file);

        // Busca o registro recém-criado para montar o snapshot real
        // (inclui logo gerada pelo service, status normalizado etc.)
        $viaCriada = $this->viacaoService->find($id);

        $this->historicoService->criar(
            usuarioId: (int) $_SESSION['user_id'],
            viacaoId:  $id,
            acao:      'criar',
            dados: [
                // Criação não tem estado anterior — só o estado final (depois)
                'antes'  => null,
                'depois' => $viaCriada !== null ? $this->snapshot($viaCriada) : null,
            ]
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

        // Captura o snapshot ANTES de qualquer alteração.
        // É fundamental fazer isso aqui — após o update() os dados já estarão
        // sobrescritos no banco e não teríamos mais acesso ao estado anterior.
        $snapshotAntes = $this->snapshot($viacaoEditar);

        $nomeViacao = trim((string) ($_POST['nome']   ?? ''));
        $url        = trim((string) ($_POST['url']    ?? ''));
        $cidade     = trim((string) ($_POST['cidade'] ?? ''));
        $status     = isset($_POST['status']) && $_POST['status'] === '1';

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

        // Busca o registro atualizado para o snapshot "depois"
        // (o service pode ter processado a logo — precisamos do nome real gravado)
        $viacaoAtualizada = $this->viacaoService->find($id);

        $this->historicoService->criar(
            usuarioId: (int) $_SESSION['user_id'],
            viacaoId:  $id,
            acao:      'editar',
            dados: [
                // Auditoria completa: o avaliador/admin pode ver exatamente o que mudou
                'antes'  => $snapshotAntes,
                'depois' => $viacaoAtualizada !== null ? $this->snapshot($viacaoAtualizada) : null,
            ]
        );

        View::flash('success', 'Viação atualizada com sucesso.');
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
            usuarioId: (int) $_SESSION['user_id'],
            viacaoId:  $id,
            acao:      'deletar',
            dados: [
                // Deleção registra tudo que existia — não há "depois"
                'antes'  => $this->snapshot($viacao),
                'depois' => null,
            ]
        );

        // Deleta APÓS registrar o histórico — se deletar antes, perdemos os dados
        $this->viacaoService->delete($id);

        View::flash('success', 'Viação removida com sucesso.');
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