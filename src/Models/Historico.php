<?php
//Representa um registro de auditoria.
//O campo dados chega do banco como string JSON e o fromRow() já o decodifica para array PHP com as chaves antes e depois.
declare(strict_types=1);

namespace App\Models;

/** Representa um registro da tabela historico_viacoes. */
final class Historico
{
    public function __construct(
        public int     $id,
        public int     $usuario_id,
        public int     $viacao_id,
        public string  $acao,
        public array   $dados,       // array decodificado com chaves 'antes' e 'depois'
        public ?string $criado_em,
        public ?string $usuario_nome, // vem do JOIN com usuarios — pode ser null se usuário foi deletado
    ) {
    }

    /** @param array<string, mixed> $row */
    public static function fromRow(array $row): self
    {
        return new self(
            id:           (int) $row['id'],
            usuario_id:   (int) $row['usuario_id'],
            viacao_id:    (int) $row['viacao_id'],
            acao:         (string) $row['acao'],
            dados:        json_decode($row['dados'], true) ?? [],
            criado_em:    isset($row['data_criacao']) && $row['data_criacao'] !== null
                ? (new \DateTime($row['data_criacao']))
                    ->setTimezone(new \DateTimeZone('America/Sao_Paulo'))
                    ->format('d/m/Y H:i:s')
                : null,
            usuario_nome: isset($row['usuario_nome']) ? (string) $row['usuario_nome'] : null,
        );
    }
}