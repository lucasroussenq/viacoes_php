<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Viacao;
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

    public function historico(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM viacao_historico ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** @return list<viacao> Retorna todas as marcas, da mais nova para a mais antiga. */
    public function all(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM viacoes ORDER BY id DESC');
        $rows = $stmt->fetchAll();

        $viacoes = [];
        foreach ($rows as $row) {
            $viacoes[] = Viacao::fromRow($row);
        }

        return $viacoes;
    }

    public function ativas(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM viacoes WHERE status = 1 ORDER BY id DESC ');
        $rows = $stmt->fetchAll();

        $viacoes = [];
        foreach ($rows as $row) {
            $viacoes[] = Viacao::fromRow($row);
        }

        return $viacoes;
    }

    /** @return viacao|null Retorna null quando o id nao existe. */
    public function find(int $id): ?viacao
    {
        $stmt = $this->pdo->prepare('SELECT * FROM viacoes WHERE id = :id');
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch();
        if (!is_array($row)) {
            return null;
        }

        return Viacao::fromRow($row);
    }

    /** Cria uma marca e retorna o id gerado. */
    public function create(
        string $nome,
        string $url,
        ?string $cidade,
        int $status,
        ?array $file = null
    ): int {

        $logo = null;

        if ($file && isset($file['name']) && $file['name'] !== '') {
            $logo = $this->uploadFile($file);
        }

        $stmt = $this->pdo->prepare(
            'INSERT INTO viacoes 
            (nome, url, cidade, status, logo)
            VALUES
            (:nome, :url, :cidade, :status, :logo)'
        );

        $stmt->execute([
            'nome'   => $nome,
            'url'    => $url,
            'cidade' => $cidade,
            'status' => $status ? 1 : 0,
            'logo'   => $logo
        ]);
                return (int)$this->pdo->lastInsertId();
            }

    /** Atualiza os campos de uma marca existente.
     * @param string|null $logo
     * @param string|null $url
     */
    public function update(int $id, string $nome, ?string $cidade, bool $status, ?string $url, ?string $logo): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE viacoes SET nome = :nome, url = :url, cidade = :cidade, logo = :logo, status = :status WHERE id = :id'
        );

        $stmt->execute([
            'id' => $id,
            'nome' => $nome,
            'url' => $url,
            'cidade' => $cidade,
            'logo' => $logo,
            'status' => $status ? 1 : 0,
        ]);
    }

    /** Remove uma marca pelo id. */
    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM viacoes WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }


    private function uploadFile(array $file): string
    {
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);

        $nomeNovo = uniqid() . '.' . $extension;

        $pasta = __DIR__ . '/../public/uploads/';

        if (!is_dir($pasta)) {
            mkdir($pasta, 0777, true);
        }

        $destino = $pasta . $nomeNovo;

        if (!move_uploaded_file($file['tmp_name'], $destino)) {
            throw new \RuntimeException('Falha ao mover o arquivo enviado.');
        }

        // Retorna o caminho público para salvar no banco
        return '/uploads/' . $nomeNovo;
    }
}