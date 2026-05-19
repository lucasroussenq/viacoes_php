<?php

declare(strict_types=1);

namespace App\Models;

/** Representa um histórico de ações do usuário. */
final class Historico
{
    public function __construct(
        public int $id,
        public int $usuario_id,
        public int $viacao_id,
        public string $acao,
        public array $dados,
        public ?string $criado_em,
    ) {
    }

    /**
     * @param array<string, mixed> $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            usuario_id: (int) $row['usuario_id'],
            viacao_id: (int) $row['viacao_id'],
            acao: (string) $row['acao'],

            dados: json_decode($row['dados'], true) ?? [],

            criado_em: ($row['criado_em'] !== null)
                ? (new \DateTime($row['criado_em']))
                    ->setTimezone(new \DateTimeZone('America/Sao_Paulo'))
                    ->format('d/m/Y H:i:s')
                : null,
        );
    }
}