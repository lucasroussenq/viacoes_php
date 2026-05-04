<?php

declare(strict_types=1);

namespace App\Models;

/** Representa uma marca de cafe carregada do banco. */
final class Home
{
    /** Construtor com os campos ja normalizados. */
    public function __construct(
        public int $id,
        public string $nome,
        public string $cidade,
        public ?string $logo,
        public string $url,
        public bool $status,
        public string $data_criacao,
        public ?string $data_atualizacao,
    ) {
    }

    /** @param array<string, mixed> $row Mapeia uma linha do PDO para Viacao. */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            nome: (string) $row['nome'],
            cidade: (string) ($row['cidade'] ?? ''),
            logo: $row['logo'] !== null ? (string) $row['logo'] : null,
            url: (string) $row['url'],
            status: ((int) $row['status']) === 1,
            data_criacao: (string) $row['data_criacao'],
            data_atualizacao: $row['data_atualizacao'] !== null ? (string) $row['data_atualizacao'] : null,
        );
    }
}
