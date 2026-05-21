<?php

declare(strict_types=1);
// O metodo estático fromRow() converte o array bruto do PDO nesse objeto,
// fazendo as conversões de tipo necessárias (inteiro, bool, formatação de data).
namespace App\Models;

//Representa uma marca de viação carregada do banco. */
final class Viacao
{
    public function __construct(
        public int $id,
        public string $nome,
        public ?string $logo,
        public string $url,
        public string $cidade,
        public bool $status,
        public ?string $data_criacao,
    ) {
    }

// @param array<string, mixed> $row */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            nome: (string) $row['nome'],
            logo: $row['logo'], // pode ser null direto
            url: (string) $row['url'],
            cidade: (string) $row['cidade'],
            status: (int) $row['status'] === 1,

            data_criacao: $row['data_criacao'] !== null
                ? (new \DateTime($row['data_criacao']))
                    ->setTimezone(new \DateTimeZone('America/Sao_Paulo'))
                    ->format('d/m/Y H:i:s')
                : null,
        );
    }
}