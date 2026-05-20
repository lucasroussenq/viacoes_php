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
        $stmt = $this->pdo->query("SELECT * FROM historico_viacoes");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** @return list<Viacao> Retorna todas as marcas, da mais nova para a mais antiga. */
    public function all(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM viacoes ORDER BY id DESC');
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC); // Corrigido: Adicionado FETCH_ASSOC

        $viacoes = [];
        foreach ($rows as $row) {
            $viacoes[] = Viacao::fromRow($row);
        }

        return $viacoes;
    }

    /**
     * Retorna registros de histórico com filtros opcionais.
     *
     * @param string|null $usuario  Filtra pelo nome do usuário (LIKE)
     * @param string|null $viacao   Filtra pelo nome da viação (LIKE, via JOIN)
     * @param string|null $acao     Filtra pela ação exata (criar, editar, deletar)
     * @return array
     */
    /**
     * Lista viações com filtros opcionais por nome, cidade, url e status.
     * @return list<Viacao>
     */
    public function listar(
        ?string $nome   = null,
        ?string $cidade = null,
        ?string $url    = null,
        ?string $status = null
    ): array {
        $where  = [];
        $params = [];

        if ($nome !== null && $nome !== '') {
            $where[]        = 'nome LIKE :nome';
            $params['nome'] = '%' . $nome . '%';
        }

        if ($cidade !== null && $cidade !== '') {
            $where[]          = 'cidade LIKE :cidade';
            $params['cidade'] = '%' . $cidade . '%';
        }

        if ($url !== null && $url !== '') {
            $where[]       = 'url LIKE :url';
            $params['url'] = '%' . $url . '%';
        }

        if ($status !== null && $status !== '') {
            $where[]          = 'status = :status';
            $params['status'] = (int) $status;
        }

        $whereClause = $where !== [] ? 'WHERE ' . implode(' AND ', $where) : '';

        $stmt = $this->pdo->prepare("SELECT * FROM viacoes {$whereClause} ORDER BY id DESC");
        $stmt->execute($params);

        return array_map(
            fn(array $row) => Viacao::fromRow($row),
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    /** @return list<Viacao> */
    public function ativas(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM viacoes WHERE status = 1 ORDER BY id DESC');
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC); // Corrigido: Adicionado FETCH_ASSOC

        $viacoes = [];
        foreach ($rows as $row) {
            $viacoes[] = Viacao::fromRow($row);
        }

        return $viacoes;
    }

    /** @return Viacao|null Retorna null quando o id nao existe. */
    public function find(int $id): ?Viacao
    {
        $stmt = $this->pdo->prepare('SELECT * FROM viacoes WHERE id = :id');
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
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

    /** Atualiza os campos de uma marca existente. */
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
        $registro  = $stmt->fetch(PDO::FETCH_ASSOC); // Corrigido: Adicionado FETCH_ASSOC
        $logoAtual = $registro ? $registro['logo'] : null;

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
            '/on\w+\s*=/i',
            '/<foreignObject/i',
        ];

        foreach ($padroesBloqueados as $padrao) {
            if (preg_match($padrao, $content)) {
                throw new \RuntimeException('SVG contém conteúdo não permitido.');
            }
        }
    }
}