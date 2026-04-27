<?php

declare(strict_types=1);

namespace App\Models;

/** Representa uma marca de cafe carregada do banco. */
final class CoffeeBrands
{
    /** Construtor com os campos ja normalizados. */
    public function __construct(
        public int $id,
        public string $brand,
        public ?string $description,
        public bool $isImported,
        public string $createdAt,
        public ?string $updatedAt,
    ) {
    }

    /** @param array<string, mixed> $row Mapeia uma linha do PDO para CoffeeBrands. */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            brand: (string) $row['brand'],
            description: $row['description'] !== null ? (string) $row['description'] : null,
            isImported: ((int) $row['is_imported']) === 1,
            createdAt: (string) $row['created_at'],
            updatedAt: $row['updated_at'] !== null ? (string) $row['updated_at'] : null,
        );
    }
}
