<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\CoffeeBrands;
use PDO;

/** Concentra operacoes de CRUD e mapeamento de CoffeeBrands. */
final class CoffeeBrandService
{
    private PDO $pdo;

    /** @param PDO|null $pdo Permite injetar a conexao em testes. */
    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? \getPdo();
    }

    /** @return list<CoffeeBrands> Retorna todas as marcas, da mais nova para a mais antiga. */
    public function all(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM coffee_brands ORDER BY id DESC');
        $rows = $stmt->fetchAll();

        $brands = [];
        foreach ($rows as $row) {
            $brands[] = CoffeeBrands::fromRow($row);
        }

        return $brands;
    }

    /** @return CoffeeBrands|null Retorna null quando o id nao existe. */
    public function find(int $id): ?CoffeeBrands
    {
        $stmt = $this->pdo->prepare('SELECT * FROM coffee_brands WHERE id = :id');
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch();
        if (!is_array($row)) {
            return null;
        }

        return CoffeeBrands::fromRow($row);
    }

    /** Cria uma marca e retorna o id gerado. */
    public function create(string $brand, ?string $description, bool $isImported): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO coffee_brands (brand, description, is_imported) VALUES (:brand, :description, :is_imported)'
        );

        $stmt->execute([
            'brand' => $brand,
            'description' => $description,
            'is_imported' => $isImported ? 1 : 0,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /** Atualiza os campos de uma marca existente. */
    public function update(int $id, string $brand, ?string $description, bool $isImported): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE coffee_brands SET brand = :brand, description = :description, is_imported = :is_imported WHERE id = :id'
        );

        $stmt->execute([
            'id' => $id,
            'brand' => $brand,
            'description' => $description,
            'is_imported' => $isImported ? 1 : 0,
        ]);
    }

    /** Remove uma marca pelo id. */
    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM coffee_brands WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
