<?php

namespace App\Services;

use App\Models\Historico;
use PDO;

final class HistoricoService
{
    private PDO $pdo; //declara a propriedade $pdo

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? \getPdo();
    }

    public function criar(
        int $usuarioId,
        int $viacaoId,
        string $acao,
        array $dados
    ): void {
        $stmt = $this->pdo->prepare("
            INSERT INTO historico_viacoes (
                usuario_id,
                viacao_id,
                acao,
                dados
            )
            VALUES (
                :usuario_id,
                :viacao_id,
                :acao,
                :dados
            )
        ");

        $stmt->execute([
            'usuario_id' => $usuarioId,
            'viacao_id' => $viacaoId,
            'acao' => $acao,
            'dados' => json_encode($dados),
        ]);
    }

    /**
     * @return Historico[]
     */
    public function listar(): array
    {
        $usuarioId = $_SESSION['user_id'] ?? null;

        $stmt = $this->pdo->prepare("
            SELECT *
            FROM historico_viacoes
            WHERE usuario_id = :usuario_id
            ORDER BY id DESC
        ");
        $stmt->execute(['usuario_id' => $usuarioId]);

        $rows = $stmt->fetchAll();

        return array_map(
            fn(array $row) => Historico::fromRow($row),
            $rows
        );
    }
}