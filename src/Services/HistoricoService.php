<?php
//Grava e recupera o histórico de quem fez o quê e quando nas viações.
// Os dados são salvos como JSON no banco, incluindo o estado "antes" e "depois" de cada alteração.
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

    //Insere um registro de auditoria.

   /* O array $dados deve seguir a estrutura:
       ['antes' => snapshot|null, 'depois' => snapshot|null]
     Onde snapshot é:
       ['nome' => string, 'url' => string, 'cidade' => string, 'status' => bool, 'logo' => string|null] */

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
     * Retorna registros de histórico com filtros opcionais.
     *
     * @param string|null $usuario  Filtra pelo nome do usuário (LIKE)
     * @param string|null $viacao   Filtra pelo nome da viação (LIKE, via JOIN)
     * @param string|null $acao     Filtra pela ação exata (criar, editar, deletar)
     * @return list<Historico>
     */
    public function listar(?string $usuario = null, ?string $viacao = null, ?string $acao = null): array
    {
        $where  = [];
        $params = [];
//where pesquisa por aproximação exata, com registros exatamente iguais
        if ($usuario !== null && trim($usuario) !== '') {
            $where[]           = 'u.nome LIKE :usuario';
            $params['usuario'] = '%' . trim($usuario) . '%';
        }

        if ($viacao !== null && trim($viacao) !== '') {
            $where[]          = 'v.nome LIKE :viacao';
            $params['viacao'] = '%' . trim($viacao) . '%';
        }

        if ($acao !== null && trim($acao) !== '') {
            $where[]        = 'h.acao = :acao';
            $params['acao'] = trim($acao);
        }

        $whereClause = $where !== [] ? 'WHERE ' . implode(' AND ', $where) : '';

        $sql = "
            SELECT
                h.id,
                h.usuario_id,
                h.viacao_id,
                h.acao,
                h.dados,
                h.data_criacao,
                u.nome AS usuario_nome,
                v.nome AS viacao_nome
            FROM historico_viacoes h
            LEFT JOIN usuarios u ON u.id = h.usuario_id
            LEFT JOIN viacoes  v ON v.id = h.viacao_id
            {$whereClause}
            ORDER BY h.id DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        $rows = $stmt->fetchAll();

        return array_map(
            fn(array $row) => Historico::fromRow($row),
            $rows
        );
    }
}