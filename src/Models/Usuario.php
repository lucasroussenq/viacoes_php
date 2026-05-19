<?php

declare(strict_types=1);

namespace App\Models;

final class Usuario
{
    public function __construct(
        public int $id,
        public string $nome,
        public string $email,
        public string $senha,
        public ?string $data_criacao,
    ) {
    }

    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            nome: (string) $row['nome'],
            email: (string) $row['email'],
            senha: (string) $row['senha'],

            data_criacao: ($row['data_criacao'] !== null)
                ? (new \DateTime($row['data_criacao']))
                    ->setTimezone(new \DateTimeZone('America/Sao_Paulo'))
                    ->format('d/m/Y H:i:s')
                : null,
        );
    }
}