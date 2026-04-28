<?php

declare(strict_types=1);

namespace App\Models;

/** Representa uma task carregada do banco. */
final class Task
{
    /** Construtor com os campos ja normalizados. */
    public function __construct(
        public int $id,
        public string $title,
        public ?string $cidade,
        public bool $isDone,
        public string $data_criacao,
        public ?string $data_atualizacao,
    ) {
    }

    /** @param array<string, mixed> $row Mapeia uma linha do PDO para Task. */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            title: (string) $row['title'],
            cidade: $row['cidade'] !== null ? (string) $row['cidade'] : null,
            isDone: ((int) $row['is_done']) === 1,
            data_criacao: (string) $row['created_at'],
            data_atualizacao: $row['updated_at'] !== null ? (string) $row['updated_at'] : null,
        );
    }
}
