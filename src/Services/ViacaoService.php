<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\viacao;
use PDO;

/** Concentra operacoes de CRUD e mapeamento de viacao. */
final class ViacaoService
{
    private PDO $pdo;

    /** @param PDO|null $pdo Permite injetar a conexao em testes. */
    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? \getPdo();
    }

    /** @return list<viacao> Retorna todas as marcas, da mais nova para a mais antiga. */
    public function all(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM coffee_viacoes ORDER BY id DESC');
        $rows = $stmt->fetchAll();

        $viacoes = [];
        foreach ($rows as $row) {
            $viacoes[] = viacao::fromRow($row);
        }

        return $viacoes;
    }

    /** @return viacao|null Retorna null quando o id nao existe. */
    public function find(int $id): ?viacao
    {
        $stmt = $this->pdo->prepare('SELECT * FROM coffee_viacoes WHERE id = :id');
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch();
        if (!is_array($row)) {
            return null;
        }

        return viacao::fromRow($row);
    }

    /** Cria uma marca e retorna o id gerado. */
    public function create(string $nome, ?string $cidade, bool $status): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO coffee_viacoes (nome, cidade, Status) VALUES (:nome, :cidade, :Status)'
        );

        $stmt->execute([
            'nome' => $nome,
            'cidade' => $cidade,
            'Status' => $status ? 1 : 0,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /** Atualiza os campos de uma marca existente. */
    public function update(int $id, string $nome, ?string $cidade, bool $status): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE coffee_viacoes SET nome = :nome, cidade = :cidade, Status = :Status WHERE id = :id'
        );

        $stmt->execute([
            'id' => $id,
            'nome' => $nome,
            'cidade' => $cidade,
            'Status' => $status ? 1 : 0,
        ]);
    }

    /** Remove uma marca pelo id. */
    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM coffee_viacoes WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
