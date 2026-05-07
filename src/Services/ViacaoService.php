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
        $stmt = $this->pdo->query("SELECT * FROM viacoes_historico ");

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

//aqui esta meu tratamento de uploads
    private function uploadFile(array $file): string
    {
        // Verifica erro no upload, caso tenha mostra mensagem
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('Erro no upload do arquivo.');
        }

        // Limite de tamanho (2MB)
        $maxSize = 2 * 1024 * 1024;

        if ($file['size'] > $maxSize) {
            throw new \RuntimeException('Arquivo muito grande.');
        }

        // Extensões permitidas
        $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'webp'];

        $extension = strtolower(
            pathinfo($file['name'], PATHINFO_EXTENSION)
        );

        if (!in_array($extension, $extensoesPermitidas, true)) {
            throw new \RuntimeException('Extensão não permitida.');
        }

        // Valida MIME type REAL do arquivo
        $finfo = new \finfo(FILEINFO_MIME_TYPE);

        $mime = $finfo->file($file['tmp_name']);

        $mimesPermitidos = [
            'image/jpeg',
            'image/png',
            'image/webp'
        ];

        if (!in_array($mime, $mimesPermitidos, true)) {
            throw new \RuntimeException('Tipo de arquivo inválido.');
        }

        //isso vai validar para ver se a imagem é real:
        if (getimagesize($file['tmp_name']) === false) {
            throw new \RuntimeException('Arquivo não é uma imagem válida.');
        }

        // Gera nome seguro
        $nomeNovo = bin2hex(random_bytes(16)) . '.' . $extension;

        // Pasta de upload
        $pasta = __DIR__ . '/../public/uploads/';

        // Cria pasta com permissão mais segura
        if (!is_dir($pasta)) {
            mkdir($pasta, 0755, true);
        }

        $destino = $pasta . $nomeNovo;

        // Move arquivo
        if (!move_uploaded_file($file['tmp_name'], $destino)) {
            throw new \RuntimeException('Falha ao mover o arquivo.');
        }

        return $nomeNovo;
    }
}