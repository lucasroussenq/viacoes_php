<?php

declare(strict_types=1);

namespace App\Controllers;

use PDO;

class HistoricoController
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? \getPdo();
    }

    public function index(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $tabAtual = $_GET['tab'] ?? 'viacoes';
        if (!in_array($tabAtual, ['viacoes', 'usuarios'], true)) {
            $tabAtual = 'viacoes';
        }

        $filtroUsuario = trim((string)($_GET['usuario'] ?? ''));
        $filtroAlvo    = trim((string)($_GET['alvo'] ?? ''));

        $sql = "
            SELECT 
                h.id,
                h.entidade_id,
                h.entidade_tipo,
                h.campo_alterado AS acao,
                h.valor_antigo,
                h.valor_novo,
                h.alterado_por AS usuario_id,
                h.data_alteracao,
                u.nome AS usuario_nome
            FROM viacoes.historico_alteracoes h
            LEFT JOIN viacoes.usuarios u ON u.id = h.alterado_por
            WHERE h.entidade_tipo = :entidade_tipo
        ";

        $params = ['entidade_tipo' => $tabAtual];

        if ($filtroUsuario !== '') {
            $sql .= " AND u.nome LIKE :filtroUsuario";
            $params['filtroUsuario'] = '%' . $filtroUsuario . '%';
        }

        if ($filtroAlvo !== '') {
            $sql .= " AND h.valor_novo LIKE :filtroAlvo";
            $params['filtroAlvo'] = '%' . $filtroAlvo . '%';
        }

        $sql .= " ORDER BY h.id DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $historico = [];
        foreach ($rows as $row) {
            $antesRaw  = json_decode((string)$row['valor_antigo'], true);
            $depoisRaw = json_decode((string)$row['valor_novo'], true);

            // Padroniza as ações estritamente para o inglês em caixa alta
            $acaoOriginal = strtolower(trim($row['acao']));
            $acaoIngles = 'UPDATE';
            if (in_array($acaoOriginal, ['criar', 'create'], true)) {
                $acaoIngles = 'CREATE';
            } elseif (in_array($acaoOriginal, ['deletar', 'delete'], true)) {
                $acaoIngles = 'DELETE';
            }

            // Normalização cirúrgica dos 3 formatos de histórico salvos no banco
            $antes  = null;
            $depois = null;

            if ($antesRaw === null && is_array($depoisRaw) && (isset($depoisRaw['antes']) || isset($depoisRaw['depois']))) {
                // Caso formatado antigo envelopado em valor_novo
                $antes  = $depoisRaw['antes'] ?? null;
                $depois = $depoisRaw['depois'] ?? null;
            } else {
                // Caso padrão e linear corrigido
                $antes  = $antesRaw;
                $depois = $depoisRaw;
            }

            // Descobre o Nome Histórico e mata o vazamento de escopo entre linhas
            $nomeHistorico = null;

            if (is_array($antes) && !empty($antes['nome'])) {
                $nomeHistorico = $antes['nome'];
            } elseif (is_array($depois) && !empty($depois['nome'])) {
                $nomeHistorico = $depois['nome'];
            } elseif (is_array($depoisRaw) && !empty($depoisRaw['nome'])) {
                $nomeHistorico = $depoisRaw['nome'];
            }

            $historico[] = (object) [
                'id'             => $row['id'],
                'usuario_id'     => $row['usuario_id'],
                'usuario_nome'   => $row['usuario_nome'] ?? 'Sistema/Admin',
                'entidade_id'    => $row['entidade_id'],
                'entidade_tipo'  => $row['entidade_tipo'],
                'acao'           => $acaoIngles,
                'dados'          => [
                    'antes'  => $antes,
                    'depois' => $depois
                ],
                'nome_historico' => $nomeHistorico ? (string)$nomeHistorico : null,
                'data_alteracao' => $row['data_alteracao']
            ];
        }

        $title = "Histórico Geral";
        require __DIR__ . '/../views/historico/historico.php';
    }
}