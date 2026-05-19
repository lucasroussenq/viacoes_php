<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Historico;
use PDO;

final class HistoricoService
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? \getPdo();
    }

    /**
     * Insere um registro de auditoria.
     *
     * O array $dados deve seguir a estrutura:
     *   ['antes' => snapshot|null, 'depois' => snapshot|null]
     *
     * Onde snapshot é:
     *   ['nome' => string, 'url' => string, 'cidade' => string, 'status' => bool, 'logo' => string|null]
     */
    public function criar(
        int    $usuarioId,
        int    $viacaoId,
        string $acao,
        array  $dados
    ): void {
        $stmt = $this->pdo->prepare("
            INSERT INTO historico_viacoes (usuario_id, viacao_id, acao, dados)
            VALUES (:usuario_id, :viacao_id, :acao, :dados)
        ");

        $stmt->execute([
            'usuario_id' => $usuarioId,
            'viacao_id'  => $viacaoId,
            'acao'       => $acao,
            'dados'      => json_encode($dados, JSON_UNESCAPED_UNICODE),
        ]);
    }

    /**
     * Retorna TODOS os registros de histórico, do mais recente para o mais antigo.
     *
     * Histórico de auditoria é um registro global do sistema — não filtra por
     * usuário logado. Qualquer usuário autenticado pode ver todas as ações
     * (quem criou, editou ou deletou cada viação).
     *
     * Se futuramente precisar restringir por perfil (ex: só admin vê tudo),
     * basta adicionar um parâmetro $apenasDoUsuario = false aqui.
     *
     * @return list<Historico>
     */
    public function listar(): array
    {
        // JOIN com usuarios para trazer o nome de quem fez a ação,
        // sem depender de foreign key — o relacionamento é feito pelo campo indexado usuario_id
        $stmt = $this->pdo->query("
            SELECT
                h.id,
                h.usuario_id,
                h.viacao_id,
                h.acao,
                h.dados,
                h.data_criacao,
                u.nome AS usuario_nome
            FROM historico_viacoes h
            LEFT JOIN usuarios u ON u.id = h.usuario_id
            ORDER BY h.id DESC
        ");

        $rows = $stmt->fetchAll();

        return array_map(
            fn(array $row) => Historico::fromRow($row),
            $rows
        );
    }
}