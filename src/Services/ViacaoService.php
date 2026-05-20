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
        $stmt = $this->pdo->query("SELECT * FROM historico_viacoes ");

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

    /**
     * Busca viações filtrando por nome, cidade ou url.
     * Se $termo for vazio ou null, retorna todas (equivalente a all()).
     *
     * @return list<Viacao>
     */
    public function search(?string $termo): array
    {
        if ($termo === null || trim($termo) === '') {
            return $this->all();
        }

        $like = '%' . trim($termo) . '%';

        $stmt = $this->pdo->prepare(
            'SELECT * FROM viacoes
             WHERE nome   LIKE :t1
                OR cidade LIKE :t2
                OR url    LIKE :t3
             ORDER BY id DESC'
        );
        $stmt->execute(['t1' => $like, 't2' => $like, 't3' => $like]);

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
        ?string $nome,
        string $url,
        ?string $cidade,
        int $status,
        ?array $file = null
    ): int {

        $logo = null;

        if ($file && isset($file['name']) && $file['name'] !== '') {
            $logo = $this->validateFile($file);
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
    public function update(
        int $id,
        string $nome,
        ?string $cidade,
        bool $status,
        ?string $url,
        ?array $file = null
    ): void {

        $stmt = $this->pdo->prepare('SELECT logo FROM viacoes WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $registro  = $stmt->fetch();
        $logoAtual = $registro['logo'];

        $logo = $logoAtual;
        if ($file && isset($file['name']) && $file['name'] !== '') {
            $logo = $this->validateFile($file);
        }

        $stmt = $this->pdo->prepare(
            'UPDATE viacoes SET nome = :nome, url = :url, cidade = :cidade, logo = :logo, status = :status WHERE id = :id'
        );

        $stmt->execute([
            'id'     => $id,
            'nome'   => $nome,
            'url'    => $url,
            'cidade' => $cidade,
            'logo'   => $logo,
            'status' => $status ? 1 : 0,
        ]);
    }

    /** Remove uma marca pelo id. */
    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM viacoes WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    /**
     * Método privado para tratar o upload de arquivos.
     * Centralizado para garantir que as regras de segurança (MIME type, tamanho)
     * sejam as mesmas em todo o app.
     */
    private function validateFile(array $file): string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('Erro no upload do arquivo.');
        }

        $maxSize = 2 * 1024 * 1024;

        if ($file['size'] > $maxSize) {
            throw new \RuntimeException('Arquivo muito grande.');
        }

        $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'webp', 'svg'];

        $extension = strtolower(
            pathinfo($file['name'], PATHINFO_EXTENSION)
        );

        if (!in_array($extension, $extensoesPermitidas, true)) {
            throw new \RuntimeException('Extensão não permitida.');
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file['tmp_name']);

        $mimesPermitidos = [
            'image/jpeg',
            'image/png',
            'image/webp',
            'image/svg+xml',
        ];

        if (!in_array($mime, $mimesPermitidos, true)) {
            throw new \RuntimeException('Tipo de arquivo inválido.');
        }

        // ✅ SVG é XML/texto — getimagesize() não funciona, validação separada
        if ($mime === 'image/svg+xml') {
            $this->validateSvg($file['tmp_name']);
        } else {
            if (getimagesize($file['tmp_name']) === false) {
                throw new \RuntimeException('Arquivo não é uma imagem válida.');
            }
        }

        $nomeNovo = bin2hex(random_bytes(16)) . '.' . $extension;
        $pasta    = __DIR__ . '/../public/uploads/';

        if (!is_dir($pasta)) {
            mkdir($pasta, 0755, true);
        }

        $destino = $pasta . $nomeNovo;

        if (!move_uploaded_file($file['tmp_name'], $destino)) {
            throw new \RuntimeException('Falha ao mover o arquivo.');
        }

        return $nomeNovo;
    }

    /**
     * Valida SVG: confirma XML bem-formado e bloqueia scripts embutidos.
     * SVG é um vetor de XSS — nunca confie só na extensão ou MIME.
     */
    private function validateSvg(string $tmpPath): void
    {
        $content = file_get_contents($tmpPath);

        if ($content === false) {
            throw new \RuntimeException('Não foi possível ler o arquivo SVG.');
        }

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($content);
        libxml_clear_errors();

        if ($xml === false) {
            throw new \RuntimeException('SVG inválido ou malformado.');
        }

        $padroesBloqueados = [
            '/<script/i',
            '/javascript:/i',
            '/on\w+\s*=/i',   // onclick=, onload=, onerror= etc.
            '/<foreignObject/i',
        ];

        foreach ($padroesBloqueados as $padrao) {
            if (preg_match($padrao, $content)) {
                throw new \RuntimeException('SVG contém conteúdo não permitido.');
            }
        }
    }
}